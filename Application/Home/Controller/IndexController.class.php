<?php
namespace Home\Controller;

use Think\Controller;

/**
 * 前端首页控制器
 */
class IndexController extends Controller
{
    /**
     * 首页
     */
    public function index()
    {
        $this->show('<div style="width: 1000px; height: 900px; margin: 0 auto; text-align: center;"><span style="font-size: 80px; color: #4f8ad0; line-height: 900px;">Thank you for your use!</span></div>','utf-8');
    }
}