<?php

namespace Admin\Controller;
use Admin\Builder\AdminListBuilder;

/**
 * 行为控制器
 * @author:albert https://github.com/Albert3306
 */
class ActionController extends AdminController {
    /**
     * 行为日志列表
     */
    public function actionLog()
    {
        //获取列表数据
        $aUid=I('get.uid',0,'intval');
        if($aUid) $map['user_id'] = $aUid;
        $map['status']    =   array('gt', -1);
        $list   =   $this->lists('ActionLog', $map);
        int_to_string($list);

        $this->assign('_list', $list);
        $this->display();
    }

    /**
     * 删除日志
     * @param mixed $ids
     */
    public function remove($ids = 0){
        empty($ids) && $this->error('参数错误！');
        if(is_array($ids)){
            $map['id'] = array('in', $ids);
        }elseif (is_numeric($ids)){
            $map['id'] = $ids;
        }
        $res = M('ActionLog')->where($map)->delete();
        if($res !== false){
            $this->success('删除成功！');
        }else {
            $this->error('删除失败！');
        }
    }

    /**
     * 清空日志
     */
    public function clear(){
        $res = M('ActionLog')->where('1=1')->delete();
        if($res !== false){
            $this->success('日志清空成功！');
        }else {
            $this->error('日志清空失败！');
        }
    }
}
