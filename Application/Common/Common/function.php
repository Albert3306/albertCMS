<?php

/**
 * 系统公共库文件
 * 主要定义系统公共函数库
 */

require_once(APP_PATH . '/Common/Common/user.php');
require_once(APP_PATH . '/Common/Common/limit.php');
require_once(APP_PATH . '/Common/Common/error.php');

/**
 * 检测后端用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 */
function is_login()
{
    $user = session('user_auth');
    if (empty($user)) {
        return  0;
    } else {
        return session('user_auth_sign') == data_auth_sign($user) ? $user['uid'] : 0;
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
 */
function action_log($action = null, $model = null, $record_id = null, $user_id = null)
{
    // 参数检查
    if (empty($action) || empty($model) || empty($record_id)) {
        return '参数不能为空';
    }
    if (empty($user_id)) {
        $user_id = is_login();
    }

    // 查询行为,判断是否执行
    $action_info = M('Action')->getByName($action);
    if ($action_info['status'] != 1) {
        return '该行为被禁用或删除！';
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

/**
 * 检测当前用户是否为管理员
 * @return boolean true-管理员，false-非管理员
 */
function is_administrator($uid = null)
{
    $uid = is_null($uid) ? is_login() : $uid;
    $admin_uids = explode(',', C('USER_ADMINISTRATOR'));//调整验证机制，支持多管理员，用,分隔
    return $uid && (in_array(intval($uid), $admin_uids));//调整验证机制，支持多管理员，用,分隔
}

/**
 * 获取管理员 ID
 * @return array 所有的管理员 ID
 */
function get_administrator()
{
    $admin_uids = explode(',', C('USER_ADMINISTRATOR')); //调整验证机制，支持多管理员，用,分隔
    return $admin_uids;
}

/**
 * 根据用户ID获取用户昵称
 * @param  integer $uid 用户ID
 * @return string       用户昵称
 */
function get_nickname($uid = null)
{
    $nickname = D('Users')->getNickname($uid);
    return $nickname;
}

/**
 * 时间戳格式化
 * @param int $time
 * @return string 完整的时间显示
 */
function time_format($time = NULL, $format = 'Y-m-d H:i')
{
    $time = $time === NULL ? NOW_TIME : intval($time);
    return date($format, $time);
}

/**
 * 获取数组中某一列的值
 */
if (!function_exists('array_column')) {
    function array_column(array $input, $columnKey, $indexKey = null)
    {
        $result = array();
        if (null === $indexKey) {
            if (null === $columnKey) {
                $result = array_values($input);
            } else {
                foreach ($input as $row) {
                    $result[] = $row[$columnKey];
                }
            }
        } else {
            if (null === $columnKey) {
                foreach ($input as $row) {
                    $result[$row[$indexKey]] = $row;
                }
            } else {
                foreach ($input as $row) {
                    $result[$row[$indexKey]] = $row[$columnKey];
                }
            }
        }
        return $result;
    }
}

/**
 * 对查询结果集进行排序
 * @access public
 * @param array $list 查询结果
 * @param string $field 排序的字段名
 * @param array $sortby 排序类型
 * asc正向排序 desc逆向排序 nat自然排序
 * @return array
 */
function list_sort_by($list, $field, $sortby = 'asc')
{
    if (is_array($list)) {
        $refer = $resultSet = array();
        foreach ($list as $i => $data)
            $refer[$i] = &$data[$field];
        switch ($sortby) {
            case 'asc': // 正向排序
                asort($refer);
                break;
            case 'desc': // 逆向排序
                arsort($refer);
                break;
            case 'nat': // 自然排序
                natcasesort($refer);
                break;
        }
        foreach ($refer as $key => $val)
            $resultSet[] = &$list[$key];
        return $resultSet;
    }
    return false;
}

/**
 * 把返回的数据集转换成Tree
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 */
function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0)
{
    // 创建Tree
    $tree = array();
    if (is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            } else {
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }

    return $tree;
}

/**
 * 将list_to_tree的树还原成列表
 * @param  array $tree 原来的树
 * @param  string $child 孩子节点的键
 * @param  string $order 排序显示的键，一般是主键 升序排列
 * @param  array $list 过渡用的中间数组，
 * @return array        返回排过序的列表数组
 */
function tree_to_list($tree, $child = '_child', $order = 'id', &$list = array())
{
    if (is_array($tree)) {
        $refer = array();
        foreach ($tree as $key => $value) {
            $reffer = $value;
            if (isset($reffer[$child])) {
                unset($reffer[$child]);
                tree_to_list($value[$child], $child, $order, $list);
            }
            $list[] = $reffer;
        }
        $list = list_sort_by($list, $order, $sortby = 'asc');
    }
    return $list;
}

/**
 * 获取登录用户 ID
 */
function get_uid()
{
    return is_login();
}

/**
 * 剥去字符串中的 HTML、XML 以及 PHP 的标签
 * @param  string $str            需要剥去标签的字符串
 * @param  string $allowable_tags 规定允许的标签
 */
function real_strip_tags($str, $allowable_tags = "")
{
    return strip_tags($str, $allowable_tags);
}

/**
 * t函数用于过滤标签，输出没有html的干净的文本
 * @param string text 文本内容
 * @return string 处理后内容
 */
function op_t($text, $addslanshes = false)
{
    $text = nl2br($text);
    $text = real_strip_tags($text);
    if ($addslanshes)
        $text = addslashes($text);
    $text = trim($text);
    return $text;
}

/**
 * select返回的数组进行整数映射转换
 * @param array $map 映射关系二维数组
 * @return array
 */
function int_to_string(&$data, $map = array('status' => array(1 => '正常', -1 => '删除', 0 => '禁用', 2 => '未审核', 3 => '草稿')))
{
    if ($data === false || $data === null) {
        return $data;
    }
    $data = (array)$data;
    foreach ($data as $key => $row) {
        foreach ($map as $col => $pair) {
            if (isset($row[$col]) && isset($pair[$row[$col]])) {
                $data[$key][$col . '_text'] = $pair[$row[$col]];
            }
        }
    }
    return $data;
}
