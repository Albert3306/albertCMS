<?php

/**
 * 系统公共库文件
 * 主要定义系统公共函数库
 */

require_once(APP_PATH . '/Common/Common/user.php');
require_once(APP_PATH . '/Common/Common/limit.php');

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

/**
 * 检测验证码
 * @param  integer $id 验证码ID
 * @return boolean     检测结果
 */
function check_verify($code, $id = 1)
{
    $verify = new \Think\Verify();
    return $verify->check($code, $id);
}

/**
 * 系统密码加密方法
 * @param  string $str 需要加密的字符串
 * @param  string $key 加密密匙
 * @return string      加密后的字符串
 */
function get_password_md5($str, $key = 'Albert')
{
    return '' === $str ? '' : md5(sha1($str) . $key);
}

/**
 * 记录行为日志，并执行该行为的规则
 * @param string $action 行为标识
 * @param string $model 触发行为的模型名
 * @param int $record_id 触发行为的记录id
 * @param int $user_id 执行行为的用户id
 * @return boolean
 * @author huajie <banhuajie@163.com>
 */
function action_log($action = null, $model = null, $record_id = null, $user_id = null)
{
    // 参数检查
    if (empty($action) || empty($model) || empty($record_id)) {
        return L('FUNCTION_PARAMETERS_CANT_BE_EMPTY');
    }
    if (empty($user_id)) {
        $user_id = is_admin_login();
    }

    // 查询行为,判断是否执行
    $action_info = M('Action')->getByName($action);
    if ($action_info['status'] != 1) {
        return L('FUNCTION_THE_ACT_IS_DISABLED_OR_DELETED');
    }

    // 插入行为日志
    $data['action_id'] = $action_info['id'];
    $data['user_id'] = $user_id;
    $data['action_ip'] = ip2long(get_client_ip());
    $data['model'] = $model;
    $data['record_id'] = $record_id;
    $data['create_time'] = NOW_TIME;

    // 解析日志规则,生成日志备注
    if (!empty($action_info['log'])) {
        if (preg_match_all('/\[(\S+?)\]/', $action_info['log'], $match)) {
            $log['user'] = $user_id;
            $log['record'] = $record_id;
            $log['model'] = $model;
            $log['time'] = NOW_TIME;
            $log['data'] = array('user' => $user_id, 'model' => $model, 'record' => $record_id, 'time' => NOW_TIME);
            foreach ($match[1] as $value) {
                $param = explode('|', $value);
                if (isset($param[1])) {
                    $replace[] = call_user_func($param[1], $log[$param[0]]);
                } else {
                    $replace[] = $log[$param[0]];
                }
            }
            $data['remark'] = str_replace($match[0], $replace, $action_info['log']);
        } else {
            $data['remark'] = $action_info['log'];
        }
    } else {
        // 未定义日志规则，记录操作url
        $data['remark'] = '操作url：' . $_SERVER['REQUEST_URI'];
    }


    $log_id = M('ActionLog')->add($data);
}
