<?php

namespace Admin\Controller;

use Admin\Builder\AdminConfigBuilder;
use Admin\Builder\AdminListBuilder;

/**
 * 模块控制器
 * @author:albert https://github.com/Albert3306
 */
class ModuleController extends AdminController
{
    protected $moduleModel;

    function _initialize()
    {
        $this->moduleModel = D('Common/Module');
        parent::_initialize();
    }

    /**
     * 模块列表
     */
    public function lists()
    {
        $aType = I('type', 'all', 'op_t');
        $this->assign('type', $aType);

        /* 刷新模块列表时清空缓存 */
        $aRefresh = I('get.refresh', 0, 'intval');
        if ($aRefresh == 1) {
            S('admin_modules', null);
            $this->moduleModel->reload();
            S('admin_modules', null);
        }

        /* 刷新模块列表时清空缓存 end */
        $modules = S('admin_modules');
        if ($modules === false) {
            $modules = $this->moduleModel->getAll();
            S('admin_modules', $modules);
        }

        foreach ($modules as $key => $m) {
            switch ($aType) {
                case 'all':
                    break;
                case 'installed':
                    if ($m['can_uninstall'] && $m['is_setup']) {
                    } else unset($modules[$key]);
                    break;
                case 'uninstalled':
                    if ($m['can_uninstall'] && $m['is_setup'] == 0) {
                    } else unset($modules[$key]);
                    break;
                case 'core':
                    if ($m['can_uninstall'] == 0) {
                    } else unset($modules[$key]);
                    break;
            }
        }

        unset($m);
        $this->assign('modules', $modules);
        $this->display();
    }
}