<?php
/**
 * 安装程序配置文件
 */

define('INSTALL_APP_PATH', realpath('./') . '/');

return array(
    'ORIGINAL_TABLE_PREFIX' => 'albert_', //默认表前缀

    /* 模板相关配置 */
    'TMPL_PARSE_STRING' => array(
        '__STATIC__' => __ROOT__ . '/Public/static',
        '__ADDONS__' => __ROOT__ . '/Public/' . MODULE_NAME . '/Addons',
        '__IMG__' => __ROOT__ . '/Public/' . MODULE_NAME . '/images',
        '__CSS__' => __ROOT__ . '/Public/' . MODULE_NAME . '/css',
        '__JS__' => __ROOT__ . '/Public/' . MODULE_NAME . '/js',
        '__ZUI__'=>__ROOT__.'/Public/zui',
        '__NAME__'=>'Albert',
        '__VERSION__'=>'1.0',
    ),
    /* URL配置 */
    'URL_MODEL'      => 3, //URL模式
    'DEFAULT_THEME'  =>  'default',  // 默认模板主题名称
    'SESSION_PREFIX' => 'albert', //session前缀
    'COOKIE_PREFIX'  => 'albert_', // Cookie前缀 避免冲突
);