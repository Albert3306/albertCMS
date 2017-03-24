<?php

namespace Admin\Controller;

use Admin\Model\AuthRuleModel;
use Admin\Model\AuthGroupModel;

/**
 * 权限管理控制器
 * Class AuthManageController
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
}
