<?php

namespace Admin\Model;

use Think\Model;

/**
 * 用户组模型类
 */
class AuthGroupModel extends Model {
    const TYPE_ADMIN        = 1;                   // 管理员用户组类型标识
    const USERS             = 'users';
    const AUTH_GROUP_ACCESS = 'auth_group_access'; // 关系表表名
    const AUTH_GROUP        = 'auth_group';        // 用户组表名

    protected $_validate = array(
        array('title','require', '必须设置用户组标题', Model::MUST_VALIDATE ,'regex',Model::MODEL_INSERT),
        array('description','0,80', '描述最多80字符', Model::VALUE_VALIDATE , 'length'  ,Model::MODEL_BOTH ),
    );

    /**
     * 将用户从用户组中移除
     * @param int|string|array $gid   用户组id
     * @param int|string|array $cid   分类id
     */
    public function removeFromGroup($uid,$gid){
        return M(self::AUTH_GROUP_ACCESS)->where( array( 'uid'=>$uid,'group_id'=>$gid) )->delete();
    }
}
