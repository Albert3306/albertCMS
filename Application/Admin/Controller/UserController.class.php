<?php

namespace Admin\Controller;

use Admin\Builder\AdminConfigBuilder;
use Admin\Builder\AdminListBuilder;
use Admin\Builder\AdminSortBuilder;

/**
 * 用户控制器
 * @author albert https://github.com/Albert3306
 */
class UserController extends AdminController
{
    /**
     * 用户管理首页
     */
    public function index()
    {
        $nickname = I('nickname', '', 'text');
        $map['status'] = array('egt', 0);
        if (is_numeric($nickname)) {
            $map['uid|nickname'] = array(intval($nickname), array('like', '%' . $nickname . '%'), '_multi' => true);
        } else {
            if ($nickname !== '') {
                $map['nickname'] = array('like', '%' . (string)$nickname . '%');
            }
        }
        $list = $this->lists('Users', $map);
        int_to_string($list);

        $this->assign('_list', $list);
        $this->display();
    }

    /**
     * 重置用户密码
     */
    public function initPass()
    {
        $uids = I('id');
        !is_array($uids) && $uids = explode(',', $uids);
        if (!count($uids)) {
            $this->error('请选择要重置的用户！');
        }
        $users_db = D('Common/Users');
        $data = $users_db->create(array('password' => '123456'));
        $res  = $users_db->where(array('id' => array('in', $uids)))->save(array('password' => $data['password']));
        if ($res) {
            $this->success('密码重置成功！');
        } else {
            $this->error('密码重置失败！可能密码重置前就是“123456”。');
        }
    }

    /**
     * 修改昵称初始化
     */
    public function updateNickname()
    {
        if (IS_POST) {
            //获取参数
            $nickname = I('post.nickname');
            $password = I('post.password');
            empty($nickname) && $this->error('请输入昵称');
            empty($password) && $this->error('请输入密码');

            //密码验证
            $user_db = D('Common/Users');
            $user = $user_db->login(UID, $password, 4);
            ($uid == -2) && $this->error('密码不正确');

            $data = $user_db->create(array('nickname' => $nickname));
            if (!$data) {
                $this->error(getErrorMsg($user_db->getError()));
            }

            $res = $user_db->where(array('id' => $user['id']))->save($data);

            if ($res) {
                $info = session('user_auth');
                $info['username'] = $data['nickname'];
                session('user_auth', $info);
                session('user_auth_sign', data_auth_sign($info));
                $this->success('修改昵称成功！');
            } else {
                $this->error('修改昵称失败！');
            }
        } else {
            $this->display();
        }
    }

    /**
     * 修改密码初始化
     */
    public function updatePassword()
    {
        if (IS_POST) {
            //获取参数
            $password = I('post.old');
            empty($password) && $this->error('请输入原密码');
            $data['password'] = I('post.password');
            empty($data['password']) && $this->error('请输入新密码');
            $repassword = I('post.repassword');
            empty($repassword) && $this->error('请输入确认密码');

            if ($data['password'] !== $repassword) {
                $this->error('您输入的新密码与确认密码不一致');
            }

            $user_db = D('Common/Users');
            $res = $user_db->updateInfo(UID, $password, $data);
            if ($res !== false) {
                $this->success('修改密码成功！');
            } else {
                $this->error(getErrorMsg($res['info']));
            }
        } else {
            $this->display();
        }
    }
}
