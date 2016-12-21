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
    public function login()
    {
        if (IS_POST) {
            # code...
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