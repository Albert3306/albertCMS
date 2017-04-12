<?php

namespace Common\Model;

use Think\Model;

/**
 * 行为日志模型
 * @author albert https://github.com/Albert3306
 */
class ActionLimitModel extends Model
{
    protected $tableName = 'action_limit';
    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),

    );

    /**
     * 新增日志
     * @param array $data 新增数据
     */
    public function addActionLimit($data)
    {
        $res = $this->add($data);
        return $res;
    }

    /**
     * 根据条件获取指定日志
     * @param  array  $where 查询条件
     */
    public function getActionLimit($where)
    {
        $limit = $this->where($where)->find();
        return $limit;
    }

    /**
     * 根据条件获取日志列表
     * @param  array  $where 查询条件
     */
    public function getList($where)
    {
        $list = $this->where($where)->select();
        return $list;
    }

    /**
     * 编辑日志
     * @param  array  $data 需要修改的数据
     */
    public function editActionLimit($data)
    {
        $res = $this->save($data);
        return $res;
    }
}
