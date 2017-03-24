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
            // 检测验证码
            if (APP_DEBUG == false){
                if(!check_verify($verify)){
                    $this->error('验证码输入错误！',U('Login/login'),true);
                }
            }

            // 验证用户并执行登录
            $user = $this->users_db->login($username,$password);
            if ($user['id'] > 0) {
                /* 记录登录SESSION和COOKIES */
                $auth = array(
                    'uid'             => $user['id'],
                    'username'        => $user['nickname'],
                    'last_login_time' => $user['last_login_time'],
                    'type'            => $user['type'],
                );

                session('user_auth', $auth);
                session('user_auth_sign', data_auth_sign($auth));

                // 登录成功，跳转页面
                $this->success('登录成功！', U('Index/index'));
            } else {
                $this->error(getErrorMsg($user),U('Login/login',true));
            }
        } else {
            if(is_login()){
                $this->redirect('Index/index');
            }else{
                /* 读取数据库中的配置 */
                $config = S('DB_CONFIG_DATA');
                if(!$config){
                    $config = D('Config')->lists();
                    S('DB_CONFIG_DATA',$config);
                }
                C($config); //添加配置

                $this->assign('meta_title',C('WEB_SITE_NAME'));
                $this->display();
            }
        }
    }

    /**
     * 退出登录
     */
    public function logout(){
        if(is_login()){
            $this->users_db->logout();
            session('[destroy]');
        }
        $this->redirect('login');
    }

    /**
     * 验证码切换
     */
    public function verify()
    {
        verify();
    }
}