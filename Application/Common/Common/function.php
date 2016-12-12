<?php
const THINK_ADDON_PATH = './Addons/';

/**
 * 系统公共库文件
 * 主要定义系统公共函数库
 */

/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 */
function is_login()
{

    if(is_api_login()){
        return is_api_login();
    }

    $user = session('user_auth');
    if (empty($user)) {
        return  0;
    } else {
        return session('user_auth_sign') == data_auth_sign($user) ? $user['uid'] : 0;
    }
}

function is_api_login()
{
    if(function_exists('I_POST')){
        $user =R('Api/Base/isLogin');
        if (empty($user)) {
            return 0;
        } else {
            return  $user;
        }
    }else{
        return 0;
    }
}

/**
 * 生成系统AUTH_KEY
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function build_auth_key()
{
    $chars = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $chars = str_shuffle($chars);
    return substr($chars, 0, 40);
}