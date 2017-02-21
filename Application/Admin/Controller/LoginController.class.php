<?php
namespace Admin\Controller;
use \Think\Controller;

/**
 * 后端登录控制器
 */
class LoginController extends Controller
{
    /**
     * 后端登录
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
}