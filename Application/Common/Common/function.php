<?php
const THINK_ADDON_PATH = './Addons/';

/**
 * 系统公共库文件
 * 主要定义系统公共函数库
 */

/**
 * 检测后端用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 */
function is_admin_login()
{
    $user = session('admin_user_auth');
    if (empty($user)) {
        return  0;
    } else {
        return session('admin_user_auth_sign') == data_auth_sign($user) ? $user['uid'] : 0;
    }
}

/**
 * 检测前端用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 */
function is_home_login()
{
    $user = session('home_user_auth');
    if (empty($user)) {
        return  0;
    } else {
        return session('home_user_auth_sign') == data_auth_sign($user) ? $user['uid'] : 0;
    }
}

/**
 * 生成系统AUTH_KEY
 */
function build_auth_key()
{
    $chars = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $chars = str_shuffle($chars);
    return substr($chars, 0, 40);
}

/**
 * 数据签名认证
 * @param  array $data 被认证的数据
 * @return string       签名
 */
function data_auth_sign($data)
{
    //数据类型检测
    if (!is_array($data)) {
        $data = (array)$data;
    }
    ksort($data); //排序
    $code = http_build_query($data); //url编码并生成query字符串
    $sign = sha1($code); //生成签名
    return $sign;
}

/**
 * 获取验证码
 * @param  integer $id 需要生成验证码的标识
 * @return string      返回生成后的验证码
 */
function verify($id = 1)
{
    $type = C('VERIFY_TYPE');
    $verify = new \Think\Verify();
    switch ($type) {
        case 1 :
            $verify->useZh = true;
            break;
        case 2 :
            $verify->codeSet = 'abcdefhijkmnpqrstuvwxyzABCDEFGHJKLMNPQRTUVWXY';
            break;
        case 3 :
            $verify->codeSet = '0123456789';
            break;
        case 4 :
            break;
        default:

    }
    $verify->entry($id);
}

function check_verify_open($open)
{
    $config = C('VERIFY_OPEN');

    if ($config) {
        $config = explode(',', $config);
        if (in_array($open, $config)) {
            return true;
        }
    }
    return false;
}