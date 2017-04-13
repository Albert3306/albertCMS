<?php

namespace Admin\Controller;

use Admin\Model\AuthRuleModel;
use Admin\Model\AuthGroupModel;

/**
 * 权限管理控制器
 * Class AuthManageController
 * @author:albert https://github.com/Albert3306
 */
class AuthManageController extends AdminController
{
    /**
     * 权限管理首页
     */
    public function index()
    {
        $list = $this->lists('AuthGroup', array('module' => 'admin'), 'id asc');
        $list = int_to_string($list);
        $this->assign('_list', $list);
        $this->assign('_use_tip', true);
        $this->display();
    }

    /**
     * 创建管理员用户组
     * @author 朱亚杰 <zhuyajie@topthink.net>
     */
    public function createGroup()
    {
        if (empty($this->auth_group)) {
            $this->assign('auth_group', array('title' => null, 'id' => null, 'description' => null, 'rules' => null,));//排除notice信息
        }
        $this->display('editgroup');
    }

    /**
     * 编辑管理员用户组
     */
    public function editGroup()
    {
        $auth_group = M('AuthGroup')->where(array('module' => 'admin', 'type' => AuthGroupModel::TYPE_ADMIN))
            ->find((int)$_GET['id']);
        $this->assign('auth_group', $auth_group);
        $this->display();
    }

    /**
     * 管理员用户组数据写入/更新
     */
    public function writeGroup()
    {
        if (isset($_POST['rules'])) {
            sort($_POST['rules']);
            $_POST['rules'] = implode(',', array_unique($_POST['rules']));
        }

        $group_db        = D('AuthGroup');
        $_POST['module'] = 'admin';
        $_POST['type']   = $group_db::TYPE_ADMIN;
        $data = $group_db->create();
        if ($data) {
            $oldGroup = $group_db->find($_POST['id']);
            $data['rules'] = $this->getMergedRules($oldGroup['rules'], explode(',', $_POST['rules']), 'eq');
            if (empty($data['id'])) {
                $r = $group_db->add($data);
            } else {
                $r = $group_db->save($data);
            }
            if ($r === false) {
                $this->error('操作失败' . $group_db->getError());
            } else {
                $this->success('操作成功!');
            }
        } else {
            $this->error('操作失败' . $group_db->getError());
        }
    }

    /**
     * 访问授权页面
     */
    public function access()
    {
        $this->updateRules();
        $auth_group = M('AuthGroup')->where(array('status' => array('egt', '0'), 'module' => 'admin', 'type' => AuthGroupModel::TYPE_ADMIN))->getfield('id,id,title,rules');
        $node_list = $this->returnNodes();
        $map = array('module' => 'admin', 'type' => AuthRuleModel::RULE_MAIN, 'status' => 1);
        $main_rules = M('AuthRule')->where($map)->getField('name,id');
        $map = array('module' => 'admin', 'type' => AuthRuleModel::RULE_URL, 'status' => 1);
        $child_rules = M('AuthRule')->where($map)->getField('name,id');

        $this->assign('main_rules', $main_rules);
        $this->assign('auth_rules', $child_rules);
        $this->assign('node_list', $node_list);
        $this->assign('auth_group', $auth_group);
        $this->assign('this_group', $auth_group[(int)$_GET['group_id']]);
        $this->display();
    }
    /**
     * 后台节点配置的url作为规则存入auth_rule
     * 执行新节点的插入,已有节点的更新,无效规则的删除三项任务
     */
    public function updateRules()
    {
        // 需要新增的节点必然位于$nodes
        $nodes = $this->returnNodes(false);

        $AuthRule = M('AuthRule');
        $map = array('module' => 'admin', 'type' => array('in', '1,2')); // status全部取出,以进行更新
        // 需要更新和删除的节点必然位于$rules
        $rules = $AuthRule->where($map)->order('name')->select();

        //构建insert数据
        $data = array(); // 保存需要插入和更新的新节点
        foreach ($nodes as $value) {
            $temp['name'] = $value['url'];
            $temp['title'] = $value['title'];
            $temp['module'] = 'admin';
            if ($value['pid'] > 0) {
                $temp['type'] = AuthRuleModel::RULE_URL;
            } else {
                $temp['type'] = AuthRuleModel::RULE_MAIN;
            }
            $temp['status'] = 1;
            $data[strtolower($temp['name'] . $temp['module'] . $temp['type'])] = $temp;//去除重复项
        }

        $update = array(); // 保存需要更新的节点
        $ids = array(); // 保存需要删除的节点的id
        foreach ($rules as $index => $rule) {
            $key = strtolower($rule['name'] . $rule['module'] . $rule['type']);
            if (isset($data[$key])) { // 如果数据库中的规则与配置的节点匹配,说明是需要更新的节点
                $data[$key]['id'] = $rule['id']; // 为需要更新的节点补充id值
                $update[] = $data[$key];
                unset($data[$key]);
                unset($rules[$index]);
                unset($rule['condition']);
                $diff[$rule['id']] = $rule;
            } elseif ($rule['status'] == 1) {
                $ids[] = $rule['id'];
            }
        }
        if (count($update)) {
            foreach ($update as $k => $row) {
                if ($row != $diff[$row['id']]) {
                    $AuthRule->where(array('id' => $row['id']))->save($row);
                }
            }
        }
        if (count($ids)) {
            $AuthRule->where(array('id' => array('IN', implode(',', $ids))))->save(array('status' => -1));
            //删除规则是否需要从每个用户组的访问授权表中移除该规则?
        }
        if (count($data)) {
            $AuthRule->addAll(array_values($data));
        }
        if ($AuthRule->getDbError()) {
            trace('[' . __METHOD__ . ']:' . $AuthRule->getDbError());
            return false;
        } else {
            return true;
        }
    }

    /**
     * 获得合并后的权限
     * @param  array  $oldRules 旧的权限
     * @param  array  $rules    需更新的权限
     * @param  string $isAdmin  是否是后端权限
     * @return array
     */
    private function getMergedRules($oldRules, $rules, $isAdmin = 'neq')
    {
        $map = array('module' => array($isAdmin, 'admin'), 'status' => 1);
        $otherRules = D('AuthRule')->where($map)->field('id')->select();
        $oldRulesArray = explode(',', $oldRules);
        $otherRulesArray = getSubByKey($otherRules, 'id');

        //1.删除全部非Admin模块下的权限，排除老的权限的影响
        //2.合并新的规则
        foreach ($otherRulesArray as $key => $v) {
            if (in_array($v, $oldRulesArray)) {
                $key_search = array_search($v, $oldRulesArray);
                if ($key_search !== false)
                    array_splice($oldRulesArray, $key_search, 1);
            }
        }

        return str_replace(',,', ',', implode(',', array_unique(array_merge($oldRulesArray, $rules))));
    }

    /**
     * 状态修改
     */
    public function changeStatus($method = null)
    {
        if (empty($_REQUEST['id'])) {
            $this->error('请选择要操作的数据！');
        }
        switch (strtolower($method)) {
            case 'forbidgroup':
                $this->forbid('AuthGroup');
                break;
            case 'resumegroup':
                $this->resume('AuthGroup');
                break;
            case 'deletegroup':
                $this->delete('AuthGroup');
                break;
            default:
                $this->error($method . '参数非法');
        }
    }

    /**
     * 用户组授权用户列表
     */
    public function user($group_id)
    {
        if (empty($group_id)) {
            $this->error('参数错误！');
        }

        $auth_group = M('AuthGroup')->where(array('status' => array('egt', '0'), 'module' => 'admin', 'type' => AuthGroupModel::TYPE_ADMIN))
            ->getfield('id,id,title,rules');
        $prefix = C('DB_PREFIX');
        $l_table = $prefix . (AuthGroupModel::USERS);
        $r_table = $prefix . (AuthGroupModel::AUTH_GROUP_ACCESS);
        $model = M()->table($l_table . ' u')->join($r_table . ' a ON u.id=a.uid');
        $_REQUEST = array();
        $list = $this->lists($model, array('a.group_id' => $group_id, 'u.status' => array('egt', 0)), 'u.id asc', null, 'u.id,u.nickname,u.last_login_time,u.last_login_ip,u.status');
        int_to_string($list);
        $this->assign('_list', $list);
        $this->assign('auth_group', $auth_group);
        $this->assign('this_group', $auth_group[(int)$_GET['group_id']]);
        $this->display();
    }

    /**
     * 将用户添加到用户组的编辑页面
     */
    public function group()
    {
        $uid = I('uid');
        $auth_groups = D('AuthGroup')->getGroups();
        $user_groups = AuthGroupModel::getUserGroup($uid);
        $ids = array();
        foreach ($user_groups as $value) {
            $ids[] = $value['group_id'];
        }
        $nickname = D('Users')->getNickName($uid);
        $this->assign('nickname', $nickname);
        $this->assign('auth_groups', $auth_groups);
        $this->assign('user_groups', implode(',', $ids));
        $this->display();
    }

    /**
     * 将用户添加到用户组,入参uid,group_id
     */
    public function addToGroup()
    {
        $uid = I('uid');
        $gid = I('group_id');
        if (empty($uid)) {
            $this->error('参数有误！');
        }
        $AuthGroup = D('AuthGroup');
        if (is_numeric($uid)) {
            if (is_administrator($uid)) {
                $this->error('该用户为超级管理员！');
            }
            if (!M('Member')->where(array('uid' => $uid))->find()) {
                $this->error('管理员用户不存在！');
            }
        }

        if ($gid && !$AuthGroup->checkGroupId($gid)) {
            $this->error($AuthGroup->error);
        }
        if ($AuthGroup->addToGroup($uid, $gid)) {
            $this->success('操作成功！');
        } else {
            $this->error($AuthGroup->getError());
        }
    }

    /**
     * 将用户从用户组中移除  入参:uid,group_id
     */
    public function removeFromGroup()
    {
        $uid = I('uid');
        $gid = I('group_id');
        if ($uid == UID) {
            $this->error('不允许解除自身授权！');
        }
        if (empty($uid) || empty($gid)) {
            $this->error('参数有误！');
        }
        $AuthGroup = D('AuthGroup');
        if (!$AuthGroup->find($gid)) {
            $this->error('用户组不存在！');
        }
        if ($AuthGroup->removeFromGroup($uid, $gid)) {
            $this->success('操作成功！');
        } else {
            $this->error('操作失败！');
        }
    }
}
