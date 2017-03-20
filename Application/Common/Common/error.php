<?php

/**
 * 获取错误信息
 * @param  integer $code   错误代号
 * @param  string  $defaul 默认成功后的信息
 */
function getErrorMsg($code,$defaul = '操作成功！')
{
    switch ($code) {
        case 1:
            $msg = $defaul;
            break;
        case 0:
            $msg = '系统繁忙，请稍后再试！';
            break;
        case -1:
            $msg = '用户名长度必须在'.modC('USERNAME_MIN_LENGTH',2,'USERCONFIG').'-'.modC('USERNAME_MAX_LENGTH',32,'USERCONFIG').'个字符之间！';
            break;
        case -2:
            $msg = '用户名被禁止注册！';
            break;
        case -3:
            $msg = '用户名被占用！';
            break;
        case -4:
            $msg = '密码长度必须在6-30个字符之间！';
            break;
        case -5:
            $msg = '邮箱格式不正确！';
            break;
        case -6:
            $msg = '邮箱长度必须在4-32个字符之间！';
            break;
        case -7:
            $msg = '邮箱被禁止注册！';
            break;
        case -8:
            $msg = '邮箱被占用！';
            break;
        case -9:
            $msg = '手机格式不正确！';
            break;
        case -10:
            $msg = '手机被禁止注册！';
            break;
        case -11:
            $msg = '手机号被占用！';
            break;
        case -12:
            $msg = '用户名必须以中文或字母开始，只能包含拼音数字，字母，汉字！';
            break;
        case -20:
            $msg = '参数错误，请重新输入！';
            break;
        case -30:
            $msg = '昵称已被占用';
        case -31:
            $msg = '昵称禁止注册';
            break;
        case -33:
            $msg = '昵称长度必须在'.modC('NICKNAME_MIN_LENGTH',2,'USERCONFIG').'-'.modC('NICKNAME_MAX_LENGTH',32,'USERCONFIG').'个字符之间！';
            break;
        case -32:
            $msg = '昵称不合法';
            break;
        case -41:
            $msg = '旧密码不正确';
            break;
        case -42:
            $msg = '密码错误，请重新输入！';
            break;
        default:
            $msg = '未知错误';
    }

    return $msg;
}