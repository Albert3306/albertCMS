<?php
/**
 * 行为限制扩展
 */
class ActionLimit
{
    var $item = array();
    var $state = true;
    var $url;
    var $info = '';
    var $punish = array(
        array('warning','警告并禁止'),
        array('logout_account', '强制退出登陆'),
        array('ban_account', '封停账户'),
        array('ban_ip', '封IP'),
    );

    function __construct()
    {
        $this->url = '';
        $this->info = '';
        $this->state = true;
    }

    function addCheckItem($action = null, $model = null, $record_id = null, $user_id = null, $ip = false)
    {
        $this->item[] = array('action' => $action, 'model' => $model, 'record_id' => $record_id, 'user_id' => $user_id, 'action_ip' => $ip);
        return $this;
    }

    function check()
    {
        $items = $this->item;
        foreach ($items as &$item) {
            $this->checkOne($item);
        }
        unset($item);
    }

    function checkOne($item)
    {
        $item['action_ip'] = $item['action_ip'] ? get_client_ip(1) : null;
        foreach ($item as $k => $v) {
            if (empty($v)) {
                unset($item[$k]);
            }
        }
        unset($k, $v);
        $time = time();
        $map['action_list'] = array(array('like', '%[' . $item['action'] . ']%'), '', 'or');
        $map['status'] = 1;
        $limitList = D('ActionLimit')->getList($map);
        !empty($item['action']) && $item['action_id'] = M('action')->where(array('name' => $item['action']))->cache(true,60)->getField('id');
        unset($item['action']);
        foreach ($limitList as &$val) {
            $ago = get_time_ago($val['time_unit'], $val['time_number'], $time);
            $item['create_time'] = array('egt', $ago);
            $log = M('action_log')->where($item)->count();
            if (count($log) >= $val['frequency']) {
                $punishes = explode(',', $val['punish']);
                foreach ($punishes as $punish) {
                    //执行惩罚
                    if (method_exists($this, $punish)) {
                        $this->$punish($item,$val);
                    }
                }
                unset($punish);
            }
        }
        unset($val);
    }
}

/**
 * 校验行为限制
 * @param  string   $action    行为名称
 * @param  string   $model     触发行为的模型
 * @param  integer  $record_id 被触发行为的记录 ID
 * @param  integer  $user_id   执行用户 ID
 * @param  boolean $ip         执行操作的 IP
 */
function check_action_limit($action = null, $model = null, $record_id = null, $user_id = null, $ip = false)
{
    $obj = new ActionLimit();

    $item = array('action' => $action, 'model' => $model, 'record_id' => $record_id, 'user_id' => $user_id, 'action_ip' => $ip);
    if(empty($record_id)){
        unset($item['record_id']);
    }

    $obj->checkOne($item);
    $return = array();
    if (!$obj->state) {
        $return['state'] = $obj->state;
        $return['info'] = $obj->info;
        $return['url'] = $obj->url;
    }else{
        $return['state'] = true;
    }

    return $return;
}

/**
 * 获取之前的时间
 * @param  string  $type 时间单位
 * @param  integer $some 时间次数
 * @param  integer $time 计算的时间戳
 */
function get_time_ago($type = 'second', $some = 1, $time = null)
{
    $time = empty($time) ? time() : $time;
    switch ($type) {
        case 'second':
            $result = $time - $some;
            break;
        case 'minute':
            $result = $time - $some * 60;
            break;
        case 'hour':
            $result = $time - $some * 60 * 60;
            break;
        case 'day':
            $result = strtotime('-' . $some . ' day', $time);
            break;
        case 'week':
            $result = strtotime('-' . ($some * 7) . ' day', $time);
            break;
        case 'month':
            $result = strtotime('-' . $some . ' month', $time);
            break;
        case 'year':
            $result = strtotime('-' . $some . ' year', $time);
            break;
        default:
            $result = $time - $some;
    }
    return $result;
}
