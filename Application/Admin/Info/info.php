<?php

return array(
    'name'          => 'Admin', // 模块名
    'alias'         => '用户中心', // 别名
    'version'       => '1.0.0', // 版本号
    'is_com'        => 0, // 是否商业模块,1是，0，否
    'show_nav'      => 1, // 是否显示在导航栏内？  1是，0否
    'summary'       => '用户中心模块，系统核心模块', // 模块描述
    'developer'     => 'Albert', // 开发者
    'website'       => 'https://www.github.com/Albert3306/AlbertCMS', // 开发者网站
    'entry'         => 'Home/index/index', // 前台入口，可用U函数
    'admin_entry'   => 'Admin/User/index', // 后台入口
    'icon'          => 'user', // 模块图标
    'sort'          => 0, // 排序规则，越小越在上
    'can_uninstall' => 0 // 是否可以卸载 1：是 0：否
);