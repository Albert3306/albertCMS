<?php
namespace Install\Controller;
use Think\Controller;
use Think\Storage;

class IndexController extends Controller{
    //安装首页
    public function index()
    {
        if(is_file( '/Conf/user.php')){
            // 已经安装过了 执行更新程序
            //session('update',true);
            $msg = '请删除install.lock文件后再运行安装程序!';
        }else{
            $msg = '已经成功安装，请不要重复安装!';
        }
        if(Storage::has('Conf/install.lock')){
            $this->error($msg);
        }
        $this->display();
    }

    //安装完成
    public function complete()
    {
        $step = session('step');

        if(!$step){
            $this->redirect('index');
        } elseif($step != 3) {
            $this->redirect("Install/step{$step}");
        }

        // 写入安装锁定文件
        Storage::put('./Conf/install.lock', 'lock');
        if(!session('update')){
            //创建配置文件
            $this->assign('info',session('config_file'));
        }
        session('step', null);
        session('error', null);
        session('update',null);
        $this->display();
    }
}