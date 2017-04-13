<?php

namespace Admin\Controller;

/**
 * 菜单控制器
 * @author:albert https://github.com/Albert3306
 */
class MenuController extends AdminController {
    private $menu_db;
    public function _initialize()
    {
        parent::_initialize();

        $this->menu_db = D('Menu');
    }

    /**
     * 菜单首页
     */
    public function index()
    {
        $pid  = I('get.pid',0);
        if ($pid) { // 获取父菜单信息
            $data = $this->menu_db->where("id={$pid}")->field(true)->find();
            $this->assign('data',$data);
        }

        $title      = trim(I('get.title'));
        $type       = C('CONFIG_GROUP_LIST');
        $all_menu   = $this->menu_db->getField('id,title');
        $map['pid'] = $pid;
        if ($title)
            $map['title'] = array('like',"%{$title}%");
        $list       =   $this->menu_db->where($map)->field(true)->order('sort asc,id asc')->select();
        int_to_string($list,array('hide'=>array(1=>'是',0=>'不是'),'is_dev'=>array(1=>'是',0=>'不是')));
        if ($list) {
            foreach ($list as &$key) {
                if ($key['pid']) {
                    $key['up_title'] = $all_menu[$key['pid']];
                }
            }
            $this->assign('list',$list);
        }
        // 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);

        $this->display();
    }

    /**
     * 新增菜单
     */
    public function add()
    {
        if (IS_POST) {
            $data = $this->menu_db->create();
            if ($data) {
                $id = $this->menu_db->add();
                if ($id) {
                    //记录行为
                    action_log('update_menu', 'Menu', $id, UID);
                    $this->success('新增成功', Cookie('__forward__'));
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($this->menu_db->getError());
            }
        } else {
            $this->assign('info',array('pid'=>I('pid')));
            $menus = $this->menu_db->field(true)->select();
            $menus = D('Common/Tree')->toFormatTree($menus);
            $menus = array_merge(array(0=>array('id'=>0,'title_show'=>'顶级菜单')), $menus);
            $this->assign('Modules',D('Module')->getAll());
            $this->assign('Menus', $menus);
            $this->display('edit');
        }
    }

    /**
     * 编辑配置
     */
    public function edit($id = 0)
    {
        if (IS_POST) {
            $data = $this->menu_db->create();
            if ($data) {
                if ($this->menu_db->save()!== false) {
                    //记录行为
                    action_log('update_menu', 'Menu', $data['id'], UID);
                    $this->success('编辑成功', Cookie('__forward__'));
                } else {
                    $this->error('编辑失败');
                }
            } else {
                $this->error($this->menu_db->getError());
            }
        } else {
            $info = array();
            /* 获取数据 */
            $info = $this->menu_db->field(true)->find($id);
            $menus = $this->menu_db->field(true)->select();
            $menus = D('Common/Tree')->toFormatTree($menus);

            $menus = array_merge(array(0=>array('id'=>0,'title_show'=>'顶级菜单')), $menus);
            $this->assign('Menus', $menus);
            $this->assign('Modules',D('Module')->getAll());
            if (false === $info) {
                $this->error('获取后台菜单信息错误');
            }
            $this->assign('info', $info);
            $this->display();
        }
    }

    /**
     * 删除后台菜单
     */
    public function del()
    {
        $id = array_unique((array)I('id',0));

        if (empty($id)) {
            $this->error('请选择要操作的数据！');
        }

        $map = array('id' => array('in', $id) );
        if ($this->menu_db->where($map)->delete()) {
            //记录行为
            action_log('update_menu', 'Menu', $id, UID);
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    public function toogleHide($id,$value = 1)
    {
        $this->editRow('Menu', array('hide'=>$value), array('id'=>$id));
    }

    public function toogleDev($id,$value = 1)
    {
        $this->editRow('Menu', array('is_dev'=>$value), array('id'=>$id));
    }

    /**
     * 导入文件
     * @param  array   $tree 数组树
     * @param  integer $pid  父级 ID
     */
    public function importFile($tree = null, $pid=0)
    {
        if ($tree == null) {
            $file = APP_PATH."Admin/Conf/Menu.php";
            $tree = require_once($file);
        }
        foreach ($tree as $value) {
            $add_pid = $this->menu_db->add(
                array(
                    'title'=>$value['title'],
                    'url'=>$value['url'],
                    'pid'=>$pid,
                    'hide'=>isset($value['hide'])? (int)$value['hide'] : 0,
                    'tip'=>isset($value['tip'])? $value['tip'] : '',
                    'group'=>$value['group'],
                )
            );
            if ($value['operator']) {
                $this->import($value['operator'], $add_pid);
            }
        }
    }

    /**
     * 导入菜单
     */
    public function import()
    {
        if (IS_POST) {
            $tree = I('post.tree');
            $lists = explode(PHP_EOL, $tree);
            if ($lists == array()) {
                $this->error('请按格式填写批量导入的菜单，至少一个菜单');
            } else {
                $pid = I('post.pid');
                foreach ($lists as $key => $value) {
                    $record = explode('|', $value);
                    if (count($record) == 2) {
                        $this->menu_db->add(array(
                            'title'=>$record[0],
                            'url'=>$record[1],
                            'pid'=>$pid,
                            'sort'=>0,
                            'hide'=>0,
                            'tip'=>'',
                            'is_dev'=>0,
                            'group'=>'',
                        ));
                    }
                }
                $this->success('导入成功',U('index?pid='.$pid));
            }
        } else {
            $pid = (int)I('get.pid');
            $this->assign('pid', $pid);
            $data = $this->menu_db->where("id={$pid}")->field(true)->find();
            $this->assign('data', $data);
            $this->display();
        }
    }

    /**
     * 菜单排序
     */
    public function sort()
    {
        if (IS_GET) {
            $ids = I('get.ids');
            $pid = I('get.pid');

            //获取排序的数据
            $map['hide']=0;
            if (!empty($ids)) {
                $map['id'] = array('in',$ids);
            } else {
                if ($pid !== '') {
                    $map['pid'] = $pid;
                }
            }
            $list = $this->menu_db->where($map)->field('id,title')->order('sort asc,id asc')->select();

            $this->assign('list', $list);
            $this->display();
        } elseif (IS_POST){
            $ids = I('post.ids');
            $ids = explode(',', $ids);
            foreach ($ids as $key=>$value){
                $res = $this->menu_db->where(array('id'=>$value))->setField('sort', $key+1);
            }
            if($res !== false){
                $this->success('排序成功');
            }else{
                $this->eorror('排序失败');
            }
        }else{
            $this->error('非法请求');
        }
    }
}
