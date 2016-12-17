<?php
namespace Admin\Controller;

/**
 * 后端首页控制器
 */
class IndexController extends AdminController
{
    /**
     * 后端首页
     */
    public function index()
    {
        // 判断是否登录
        if (UID) {
            # code...
        } else {
            // 还没登录 跳转到登录页面
            $this->redirect('Login/login');
        }
    }
}