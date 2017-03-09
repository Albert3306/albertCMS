<?php
namespace Admin\Controller;
use \Think\Controller;

/**
 * 后台登录控制器
 */
class LoginController extends Controller
{
    private $users_db;

    /**
     * 控制器初始化
     */
    public function _initialize()
    {
        $this->users_db = D('Common/Users');
    }

    /**
     * 后台登录
     */
    public function login($username = null, $password = null, $verify = null)
    {
        if (IS_POST) {
            /* 检测验证码 TODO: */
            if (APP_DEBUG == false){
                if(!check_verify($verify)){
                    $this->error('验证码输入错误！',U('Login/login'),true);
                }
            }

            // 执行用户登录
            $uid = $this->users_db->login($username,$password);
        } else {
            $this->display();
        }
    }

    /**
     * 验证码切换
     */
    public function verify()
    {
        verify();
    }

    /**
     * 获取错误信息
     * @param  integer $code   错误代号
     * @param  string  $defaul 默认成功后的信息
     */
    public function getErrorMsg($code,$defaul = '操作成功！')
    {
        switch ($code) {
            case 1:
                $msg = $defaul;
                break;

            case 0:
                $msg = '未知错误，请稍后再试！';
                break;
            
            case -1:
                $msg = '用户不存在或被禁用！';
                break;
            
            case -2:
                $msg = '密码错误，请重新输入！';
                break;
        }

        return $msg;
    }
}