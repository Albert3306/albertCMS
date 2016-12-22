<?php
namespace Home\Controller;

/**
 * 前端首页控制器
 */
class IndexController extends HomeController
{
    private $ip;
    private $port;
    public function _initialize()
    {
        parent::_initialize();

        set_time_limit(0); //确保在连接客户端时不会超时
        $this->ip   = '127.0.0.1';
        $this->port = 1935;
    }

    /**
     * 首页
     */
    public function index()
    {
        echo 111;
    }
}