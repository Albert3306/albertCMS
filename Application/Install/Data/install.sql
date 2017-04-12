/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50711
Source Host           : localhost:3306
Source Database       : albert

Target Server Type    : MYSQL
Target Server Version : 50711
File Encoding         : 65001

Date: 2017-03-15 15:49:46
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for albert_action
-- ----------------------------
DROP TABLE IF EXISTS `albert_action`;
CREATE TABLE `albert_action` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` char(30) NOT NULL DEFAULT '' COMMENT '行为唯一标识',
  `title` char(80) NOT NULL DEFAULT '' COMMENT '行为说明',
  `remark` char(140) NOT NULL DEFAULT '' COMMENT '行为描述',
  `rule` text NOT NULL COMMENT '行为规则',
  `log` text NOT NULL COMMENT '日志规则',
  `type` tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT '类型',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '状态',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  `module` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='系统行为表';

-- ----------------------------
-- Records of albert_action
-- ----------------------------
INSERT INTO `albert_action` VALUES ('1', 'reg', '用户注册', '用户注册', '', '', '1', '1', '1426070545', '');
INSERT INTO `albert_action` VALUES ('2', 'input_password', '输入密码', '记录输入密码的次数。', '', '', '1', '1', '1426122119', '');
INSERT INTO `albert_action` VALUES ('3', 'user_login', '用户登录', 'a:1:{i:0;a:5:{s:5:\"table\";s:6:\"users\";s:5:\"field\";s:1:\"1\";s:4:\"rule\";s:2:\"10\";s:5:\"cycle\";s:2:\"24\";s:3:\"max\";s:1:\"1\";}}', '[user|get_nickname]在[time|time_format]登录了账号', '', '1', '1', '1426122119', '');
INSERT INTO `albert_action` VALUES ('4', 'update_config', '更新配置', '新增或修改或删除配置', '', '', '1', '1', '1383294988', '');
INSERT INTO `albert_action` VALUES ('5', 'update_channel', '更新导航', '新增或修改或删除导航', '', '', '1', '1', '1383296301', '');
INSERT INTO `albert_action` VALUES ('6', 'update_menu', '更新菜单', '新增或修改或删除菜单', '', '', '1', '1', '1383296392', '');

-- ----------------------------
-- Table structure for albert_action_limit
-- ----------------------------
DROP TABLE IF EXISTS `albert_action_limit`;
CREATE TABLE `albert_action_limit` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `title` varchar(100) NOT NULL COMMENT '行为',
  `name` varchar(50) NOT NULL COMMENT '行为名称',
  `frequency` int(11) NOT NULL COMMENT '频率',
  `time_number` int(11) NOT NULL COMMENT '时间次数',
  `time_unit` varchar(50) NOT NULL COMMENT '时间单位',
  `punish` text NOT NULL COMMENT '惩罚',
  `is_message` tinyint(4) NOT NULL COMMENT '是否提示消息',
  `message_content` text NOT NULL COMMENT '消息内容',
  `action_list` text NOT NULL COMMENT '行为列表',
  `status` tinyint(4) NOT NULL COMMENT '状态',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `module` varchar(20) NOT NULL COMMENT '模块',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='行为限制表';

-- ----------------------------
-- Records of albert_action_limit
-- ----------------------------
INSERT INTO `albert_action_limit` VALUES ('1', 'reg', '注册限制', '1', '1', 'minute', 'warning', '0', '', '[reg]', '1', '0', '');
INSERT INTO `albert_action_limit` VALUES ('2', 'input_password', '输密码', '0', '1', 'minute', 'warning', '0', '', '[input_password]', '1', '0', '');

-- ----------------------------
-- Table structure for albert_action_log
-- ----------------------------
DROP TABLE IF EXISTS `albert_action_log`;
CREATE TABLE `albert_action_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `action_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '行为ID',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '执行用户ID',
  `action_ip` bigint(20) NOT NULL COMMENT '执行行为者IP',
  `model` varchar(50) NOT NULL DEFAULT '' COMMENT '触发行为的表',
  `record_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '触发行为的数据ID',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '日志备注',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '执行行为的时间',
  PRIMARY KEY (`id`),
  KEY `action_ip_ix` (`action_ip`),
  KEY `action_id_ix` (`action_id`),
  KEY `user_id_ix` (`user_id`)
) ENGINE=MyISAM CHARSET=utf8 ROW_FORMAT=FIXED COMMENT='行为日志表';

-- ----------------------------
-- Records of albert_action_log
-- ----------------------------

-- ----------------------------
-- Table structure for albert_auth_group
-- ----------------------------
DROP TABLE IF EXISTS `albert_auth_group`;
CREATE TABLE `albert_auth_group` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `module` varchar(20) NOT NULL COMMENT '用户组所属模块',
  `type` tinyint(4) NOT NULL COMMENT '组类型',
  `title` char(20) NOT NULL DEFAULT '' COMMENT '用户组中文名称',
  `description` varchar(80) DEFAULT '' COMMENT '描述信息',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '用户组状态 1：正常 0：禁用 -1：删除',
  `rules` text NOT NULL COMMENT '用户组拥有的规则ID，多个规则 , 隔开',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='用户组表';

-- ----------------------------
-- Records of albert_auth_group
-- ----------------------------
INSERT INTO `albert_auth_group` VALUES ('1', 'admin', '1', '普通用户', '', '1', '1');

-- ----------------------------
-- Table structure for albert_auth_group_access
-- ----------------------------
DROP TABLE IF EXISTS `albert_auth_group_access`;
CREATE TABLE `albert_auth_group_access` (
  `uid` int(10) unsigned NOT NULL COMMENT '用户 ID',
  `group_id` mediumint(8) unsigned NOT NULL COMMENT '用户组ID',
  UNIQUE KEY `uid_group_id` (`uid`,`group_id`),
  KEY `uid` (`uid`),
  KEY `group_id` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of albert_auth_group_access
-- ----------------------------
INSERT INTO `albert_auth_group_access` VALUES ('1', '1');

-- ----------------------------
-- Table structure for albert_auth_rule
-- ----------------------------
DROP TABLE IF EXISTS `albert_auth_rule`;
CREATE TABLE `albert_auth_rule` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `module` varchar(20) NOT NULL COMMENT '规则所属MODULE',
  `type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '1：URL 2：主菜单',
  `name` char(80) NOT NULL DEFAULT '' COMMENT '规则唯一英文标识',
  `title` char(20) NOT NULL DEFAULT '' COMMENT '规则中文描述',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否有效 0：无效 1：有效',
  `condition` varchar(300) NOT NULL DEFAULT '' COMMENT '规则附加条件',
  PRIMARY KEY (`id`),
  KEY `module` (`module`,`status`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户组权限校验表';

-- ----------------------------
-- Records of albert_auth_rule
-- ----------------------------

-- ----------------------------
-- Table structure for albert_avatar
-- ----------------------------
DROP TABLE IF EXISTS `albert_avatar`;
CREATE TABLE `albert_avatar` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `path` varchar(200) NOT NULL COMMENT '头像存储路径',
  `driver` varchar(50) NOT NULL COMMENT '驱动',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `status` int(11) NOT NULL COMMENT '状态',
  `is_temp` int(11) NOT NULL COMMENT '是否是临时的',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='上传头像表';

-- ----------------------------
-- Records of albert_avatar
-- ----------------------------

-- ----------------------------
-- Table structure for albert_config
-- ----------------------------
DROP TABLE IF EXISTS `albert_config`;
CREATE TABLE `albert_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '配置名称',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '配置类型',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '配置说明',
  `group` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '配置分组',
  `extra` varchar(255) NOT NULL DEFAULT '' COMMENT '配置值',
  `remark` varchar(255) DEFAULT NULL COMMENT '配置说明',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  `value` text COMMENT '配置值',
  `sort` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_name` (`name`),
  KEY `type` (`type`),
  KEY `group` (`group`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COMMENT='系统配置表';

-- ----------------------------
-- Records of albert_config
-- ----------------------------
INSERT INTO `albert_config` VALUES ('1', '_USERCONFIG_USERNAME_MIN_LENGTH', '0', '', '0', '', null, '1484128566', '1484128566', '1', '2', '0');
INSERT INTO `albert_config` VALUES ('2', '_USERCONFIG_USERNAME_MAX_LENGTH', '0', '', '0', '', null, '1484128566', '1484128566', '1', '32', '0');
INSERT INTO `albert_config` VALUES ('3', 'USER_NAME_BAOLIU', '1', '保留用户名和昵称', '3', '', '禁止注册用户名和昵称，包含这些即无法注册,用\" , \"号隔开，用户只能是英文，下划线_，数字等', '1388845937', '1388845937', '1', '管理员,测试,admin,垃圾', '0');
INSERT INTO `albert_config` VALUES ('4', 'WEB_SITE_CLOSE', '4', '关闭站点', '1', '0：关闭\r\n1：开启', '站点关闭后其他用户不能访问，管理员可以正常访问', '1378898976', '1378898976', '1', '1', '11');
INSERT INTO `albert_config` VALUES ('5', 'ADMIN_ALLOW_IP', '2', '后台允许访问IP', '4', '', '多个用逗号分隔，如果不配置表示不限制IP访问', '1387165454', '1387165454', '1', null, '27');
INSERT INTO `albert_config` VALUES ('6', 'ALLOW_VISIT', '3', '不受限控制器方法', '0', '', null, '1386644047', '1386644047', '1', '0:article/draftbox\r\n1:article/mydocument\r\n2:Category/tree\r\n3:Index/verify\r\n4:file/upload\r\n5:file/download\r\n6:user/updatePassword\r\n7:user/updateNickname\r\n8:file/uploadpicture', '2');
INSERT INTO `albert_config` VALUES ('7', 'DENY_VISIT', '3', '超管专限控制器方法', '0', '', '仅超级管理员可访问的控制器方法', '1386644141', '1386644141', '1', '0:Addons/addhook\r\n1:Addons/edithook\r\n2:Addons/delhook\r\n3:Addons/updateHook\r\n4:Admin/getMenus\r\n5:Admin/recordList\r\n6:AuthManager/updateRules\r\n7:AuthManager/tree', '3');
INSERT INTO `albert_config` VALUES ('8', 'DEVELOP_MODE', '4', '开启开发者模式', '4', '0：关闭\r\n1：开启', '是否开启开发者模式', '1383105995', '1383105995', '1', '1', '26');
INSERT INTO `albert_config` VALUES ('9', 'WEB_SITE_NAME', '2', '站点名称', '4', '', '用于后端 title 展示', '1383105995', '1383105995', '1', 'Albert', '0');
INSERT INTO `albert_config` VALUES ('10', 'COUNT_DAY', '0', '后台首页统计用户增长天数', '0', '', '默认统计最近半个月的用户数增长情况', '1420791945', '1420791945', '1', '7', '0');
INSERT INTO `albert_config` VALUES ('11', 'CONFIG_GROUP_LIST', '3', '配置分组', '4', '', '配置分组', '1379228036', '1379228036', '1', '1:基本\r\n2:内容\r\n3:用户\r\n4:系统\r\n5:邮件', '15');
INSERT INTO `albert_config` VALUES ('12', 'LIST_ROWS', '0', '后台每页记录数', '2', '', '后台数据每页显示记录数', '1379503896', '1379503896', '1', '10', '24');

-- ----------------------------
-- Table structure for albert_file
-- ----------------------------
DROP TABLE IF EXISTS `albert_file`;
CREATE TABLE `albert_file` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '原始文件名',
  `savename` varchar(100) NOT NULL DEFAULT '' COMMENT '保存名称',
  `savepath` varchar(255) NOT NULL DEFAULT '' COMMENT '文件保存路径',
  `ext` char(5) NOT NULL DEFAULT '' COMMENT '文件后缀',
  `mime` char(40) NOT NULL DEFAULT '' COMMENT '文件 MIME 类型',
  `size` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小',
  `md5` char(32) NOT NULL DEFAULT '' COMMENT '文件MD5',
  `sha1` char(40) NOT NULL DEFAULT '' COMMENT '文件SHA1编码',
  `location` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '文件保存位置',
  `create_time` int(10) unsigned NOT NULL COMMENT '上传时间',
  `driver` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_md5` (`md5`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文件表';

-- ----------------------------
-- Records of albert_file
-- ----------------------------

-- ----------------------------
-- Table structure for albert_menu
-- ----------------------------
DROP TABLE IF EXISTS `albert_menu`;
CREATE TABLE `albert_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '标题',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级分类ID',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序（同级有效）',
  `url` char(255) NOT NULL DEFAULT '' COMMENT '链接地址',
  `hide` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否隐藏',
  `tip` varchar(255) NOT NULL DEFAULT '' COMMENT '提示',
  `group` varchar(50) DEFAULT '' COMMENT '分组',
  `is_dev` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否仅开发者模式可见',
  `icon` varchar(20) NOT NULL COMMENT '导航图标',
  `module` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM AUTO_INCREMENT=43 DEFAULT CHARSET=utf8 COMMENT='菜单表';

-- ----------------------------
-- Records of albert_menu
-- ----------------------------
INSERT INTO `albert_menu` VALUES ('1', '首页', '0', '1', 'Index/index', '0', '', '', '0', 'home', 'admin');
INSERT INTO `albert_menu` VALUES ('2', '用户', '0', '2', 'User/index', '0', '', '', '0', 'user', 'admin');
INSERT INTO `albert_menu` VALUES ('3', '运营', '0', '3', 'Operation/index', '0', '', '', '0', 'laptop', 'admin');
INSERT INTO `albert_menu` VALUES ('4', '安全', '0', '4', 'ActionLimit/limitList', '0', '', '', '0', 'shield', 'admin');
INSERT INTO `albert_menu` VALUES ('5', '系统', '0', '5', 'Config/group', '0', '', '', '0', 'windows', 'admin');
INSERT INTO `albert_menu` VALUES ('6', '扩展', '0', '6', 'Module/lists', '0', '', '', '0', 'cloud', 'admin');
INSERT INTO `albert_menu` VALUES ('7', '后台菜单管理', '2', '6', 'Menu/index', '0', '', '权限管理', '0', '', 'Admin');
INSERT INTO `albert_menu` VALUES ('8', '新增', '7', '0', 'Menu/add', '0', '', '系统设置', '0', '', 'Admin');
INSERT INTO `albert_menu` VALUES ('9', '编辑', '7', '1', 'Menu/edit', '0', '', '系统设置', '0', '', 'Admin');
INSERT INTO `albert_menu` VALUES ('10', '导入', '7', '2', 'Menu/import', '0', '', '系统设置', '0', '', 'Admin');
INSERT INTO `albert_menu` VALUES ('11', '排序', '7', '3', 'Menu/sort', '1', '', '系统设置', '0', '', 'Admin');
INSERT INTO `albert_menu` VALUES ('12', '删除菜单', '7', '4', 'Menu/del', '1', '', '系统设置', '0', '', 'Admin');
INSERT INTO `albert_menu` VALUES ('13', '设置开发者模式可见', '7', '5', 'Menu/toogleDev', '1', '', ' 系统设置', '0', '', 'Admin');
INSERT INTO `albert_menu` VALUES ('14', '设置显示隐藏', '7', '6', 'Menu/toogleHide', '1', '', ' 系统设置', '0', '', 'Admin');
INSERT INTO `albert_menu` VALUES ('15', '模块管理', '6', '5', 'module/lists', '0', '', '本地', '0', '', '');
INSERT INTO `albert_menu` VALUES ('16', '编辑模块', '15', '0', 'Module/edit', '1', '', '模块管理', '0', '', '');
INSERT INTO `albert_menu` VALUES ('17', '用户信息', '2', '2', 'User/index', '0', '', '用户管理', '0', '', 'Admin');
INSERT INTO `albert_menu` VALUES ('18', '重置用户密码', '17', '0', 'User/initpass', '1', '', '', '0', '', 'Admin');
INSERT INTO `albert_menu` VALUES ('19', '修改密码', '2', '3', 'User/updatePassword', '1', '', '', '0', '', 'Admin');
INSERT INTO `albert_menu` VALUES ('20', '修改昵称', '2', '4', 'User/updateNickname', '1', '', '', '0', '', 'Admin');
INSERT INTO `albert_menu` VALUES ('21', '用户组管理', '2', '5', 'AuthManage/index', '0', '', '权限管理', '0', '', 'Admin');
INSERT INTO `albert_menu` VALUES ('22', '删除', '21', '0', 'AuthManage/changeStatus?method=deleteGroup', '0', '', '', '0', '', 'Admin');
INSERT INTO `albert_menu` VALUES ('23', '禁用', '21', '1', 'AuthManage/changeStatus?method=forbidGroup', '0', '', '', '0', '', 'Admin');
INSERT INTO `albert_menu` VALUES ('24', '恢复', '21', '2', 'AuthManage/changeStatus?method=resumeGroup', '0', '', '', '0', '', 'Admin');
INSERT INTO `albert_menu` VALUES ('25', '新增', '21', '3', 'AuthManage/createGroup', '0', '', '', '0', '', 'Admin');
INSERT INTO `albert_menu` VALUES ('26', '编辑', '21', '4', 'AuthManage/editGroup', '0', '', '', '0', '', 'Admin');
INSERT INTO `albert_menu` VALUES ('27', '保存用户组', '21', '5', 'AuthManage/writeGroup', '0', '', '', '0', '', 'Admin');
INSERT INTO `albert_menu` VALUES ('28', '授权', '21', '6', 'AuthManage/group', '0', '', '', '0', '', 'Admin');
INSERT INTO `albert_menu` VALUES ('29', '访问授权', '21', '7', 'AuthManage/access', '0', '', '', '0', '', 'Admin');
INSERT INTO `albert_menu` VALUES ('30', '成员授权', '21', '8', 'AuthManage/user', '0', '', '', '0', '', 'Admin');
INSERT INTO `albert_menu` VALUES ('31', '解除授权', '21', '9', 'AuthManage/removeFromGroup', '0', '', '', '0', '', 'Admin');
INSERT INTO `albert_menu` VALUES ('32', '保存成员授权', '21', '10', 'AuthManage/addToGroup', '0', '', '', '0', '', 'Admin');
INSERT INTO `albert_menu` VALUES ('33', '分类授权', '21', '11', 'AuthManage/category', '0', '', '', '0', '', 'Admin');
INSERT INTO `albert_menu` VALUES ('34', '保存分类授权', '21', '12', 'AuthManage/addToCategory', '0', '', '', '0', '', 'Admin');
INSERT INTO `albert_menu` VALUES ('35', '模型授权', '21', '13', 'AuthManage/modelauth', '0', '', '', '0', '', 'Admin');
INSERT INTO `albert_menu` VALUES ('36', '保存模型授权', '21', '14', 'AuthManage/addToModel', '0', '', '', '0', '', 'Admin');
INSERT INTO `albert_menu` VALUES ('37', '新增权限节点', '21', '15', 'AuthManage/addNode', '1', '', '', '1', '', 'Admin');
INSERT INTO `albert_menu` VALUES ('38', '前台权限管理', '21', '16', 'AuthManage/accessUser', '1', '', '权限管理', '0', '', 'Admin');
INSERT INTO `albert_menu` VALUES ('39', '删除权限节点', '21', '17', 'AuthManage/deleteNode', '1', '', '', '0', '', 'Admin');
INSERT INTO `albert_menu` VALUES ('40', '行为日志', '4', '0', 'Action/actionlog', '0', '', '行为管理', '0', '', '');
INSERT INTO `albert_menu` VALUES ('41', '删除日志', '40', '0', 'Action/remove', '1', '', '', '0', '', '');
INSERT INTO `albert_menu` VALUES ('42', '清空日志', '40', '1', 'Action/clear', '1', '', '', '0', '', '');

-- ----------------------------
-- Table structure for albert_module
-- ----------------------------
DROP TABLE IF EXISTS `albert_module`;
CREATE TABLE `albert_module` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(30) NOT NULL COMMENT '模块名',
  `alias` varchar(30) NOT NULL COMMENT '中文名',
  `version` varchar(20) NOT NULL COMMENT '版本号',
  `is_com` tinyint(4) NOT NULL COMMENT '是否商业版',
  `show_nav` tinyint(4) NOT NULL COMMENT '是否显示在导航栏中',
  `summary` varchar(200) NOT NULL COMMENT '简介',
  `developer` varchar(50) NOT NULL COMMENT '开发者',
  `website` varchar(200) NOT NULL COMMENT '网址',
  `entry` varchar(50) NOT NULL COMMENT '前台入口',
  `is_setup` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否已安装',
  `sort` int(11) NOT NULL COMMENT '模块排序',
  `icon` varchar(20) NOT NULL,
  `can_uninstall` tinyint(4) NOT NULL,
  `admin_entry` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `name_2` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='模块管理表';

-- ----------------------------
-- Records of albert_module
-- ----------------------------

-- ----------------------------
-- Table structure for albert_picture
-- ----------------------------
DROP TABLE IF EXISTS `albert_picture`;
CREATE TABLE `albert_picture` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `type` varchar(50) NOT NULL,
  `path` varchar(255) NOT NULL DEFAULT '' COMMENT '路径',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '图片链接',
  `md5` char(32) NOT NULL DEFAULT '' COMMENT '文件 MD5',
  `sha1` char(40) NOT NULL DEFAULT '' COMMENT '文件 SHA1 编码',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '状态',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM CHARSET=utf8 COMMENT='图片表';

-- ----------------------------
-- Records of albert_picture
-- ----------------------------

-- ----------------------------
-- Table structure for albert_seo_rule
-- ----------------------------
DROP TABLE IF EXISTS `albert_seo_rule`;
CREATE TABLE `albert_seo_rule` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `title` text NOT NULL COMMENT '标题',
  `app` varchar(40) NOT NULL COMMENT '模块名',
  `controller` varchar(40) NOT NULL COMMENT '控制器名',
  `action` varchar(40) NOT NULL COMMENT '方法名',
  `status` tinyint(4) NOT NULL COMMENT '状态',
  `seo_keywords` text NOT NULL COMMENT 'SEO关键词',
  `seo_description` text NOT NULL COMMENT 'SEO描述',
  `seo_title` text NOT NULL COMMENT 'SEO标题',
  `sort` int(11) NOT NULL COMMENT '排序',
  `summary` varchar(500) NOT NULL COMMENT 'SEO变量介绍',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='SEO表';

-- ----------------------------
-- Records of albert_seo_rule
-- ----------------------------

-- ----------------------------
-- Table structure for albert_users
-- ----------------------------
DROP TABLE IF EXISTS `albert_users`;
CREATE TABLE `albert_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `username` char(32) NOT NULL COMMENT '用户名',
  `nickname` char(32) NOT NULL DEFAULT '' COMMENT '昵称',
  `signature` text COMMENT '签名',
  `password` char(32) NOT NULL COMMENT '密码',
  `email` char(32) NOT NULL COMMENT '用户邮箱',
  `mobile` char(15) NOT NULL COMMENT '用户手机',
  `login` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '登录次数',
  `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
  `reg_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '注册IP',
  `last_login_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `last_login_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '最后登录IP',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) DEFAULT '0' COMMENT '用户状态',
  `type` tinyint(4) NOT NULL COMMENT '1：用户名注册 2：邮箱注册 3：手机注册',
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `username` (`username`),
  KEY `nickname` (`nickname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户表';

-- ----------------------------
-- Records of albert_users
-- ----------------------------
