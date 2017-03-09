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
                    $this->error(L('LOGIN_VERIFY_ERROR'),U('Login/login'),true);
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
                $msg = L('LOGIN_UNKNOWN_ERROR');
                break;
            
            case -1:
                $msg = L('USERS_DO_NOT_EXIST_OR_ARE_DISABLED');
                break;
            
            case -2:
                $msg = L('LOGIN_PASSWORD_ERROR');
                break;
        }

        return $msg;
    }
}