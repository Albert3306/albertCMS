<?php

namespace Admin\Model;
use Think\Model;

/**
 * 菜单模型
 */
class MenuModel extends Model {
	protected $_validate = array(
		array('url','require','url必须填写'), //默认情况下用正则进行验证
	);

	/**
	 * 获取树的根到子节点的路径
	 * @param  integer $id 菜单 ID
	 * @return array       返回数据数组
	 */
	public function getPath($id){
		$path = array();
		$nav = $this->where("id={$id}")->field('id,pid,title')->find();
		$path[] = $nav;
		if($nav['pid'] >1){
			$path = array_merge($this->getPath($nav['pid']),$path);
		}
		return $path;
	}
}