<?php

/**
 * 根据 type 或用户名来判断注册使用的是用户名、邮箱或者手机
 * @param $username
 * @param $email
 * @param $mobile
 * @param int $type
 * @return bool
 */
function check_username(&$username, &$email, &$mobile, &$type = 0)
{
    if ($type) {
        switch ($type) {
            case 2:
                $email = $username;
                $username = '';
                $mobile = '';
                $type = 2;
                break;
            case 3:
                $mobile = $username;
                $username = '';
                $email = '';
                $type = 3;
                break;
            default :
                $mobile = '';
                $email = '';
                $type = 1;
                break;
        }
    } else {
        $check_email = preg_match("/[a-z0-9_\-\.]+@([a-z0-9_\-]+?\.)+[a-z]{2,3}/i", $username, $match_email);
        $check_mobile = preg_match("/^(1[0-9])[0-9]{9}$/", $username, $match_mobile);
        if ($check_email) {
            $email = $username;
            $username = '';
            $mobile = '';
            $type = 2;
        } elseif ($check_mobile) {
            $mobile = $username;
            $username = '';
            $email = '';
            $type = 3;
        } else {
            $mobile = '';
            $email = '';
            $type = 1;
        }
    }
    return true;
}

/**
 * 验证注册格式是否开启
 * @param $type
 * @return bool
 */
function check_reg_type($type){
    $t[1] = $t['username'] ='username';
    $t[2] = $t['email'] ='email';
    $t[3] = $t['mobile'] ='mobile';

    $switch = modC('REG_SWITCH','','USERCONFIG');
    if($switch){
        $switch = explode(',',$switch);
        if(in_array($t[$type],$switch)){
           return true;
        }
    }
    return false;
}

/**获取后台设置
 * @param        $key 获取配置
 * @param string $default 默认值
 * @param string $module 模块名，不设置用当前模块名
 * @return string
 */
function modC($key, $default = '', $module = '')
{
    $mod = $module ? $module : MODULE_NAME;
    if (MODULE_NAME == "Install" && $key == "NOW_THEME") {
        return $default;
    }
    $tag = 'conf_' . strtoupper($mod) . '_' . strtoupper($key);
    $result = S($tag);
    if ($result === false) {
        $config = M('Config')->field('value')->where(array('name' => '_' . strtoupper($mod) . '_' . strtoupper($key)))->find();
        if (!$config) {
            $result = $default;
        } else {
            $result = $config['value'];
        }
        S($tag, $result);
    }
    return $result;
}
