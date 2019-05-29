/*
Navicat MySQL Data Transfer

Source Server         : local-phpstudy
Source Server Version : 50553
Source Host           : 127.0.0.1:3306
Source Database       : suda_apartment_install

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2019-05-29 11:31:10
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for dx_apartment
-- ----------------------------
DROP TABLE IF EXISTS `dx_apartment`;
CREATE TABLE `dx_apartment` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user` bigint(20) DEFAULT NULL COMMENT '选择人',
  `build` int(11) DEFAULT NULL COMMENT '楼宇',
  `floor` int(11) DEFAULT NULL COMMENT '楼层',
  `room` int(11) DEFAULT NULL COMMENT '房间号',
  `bed` int(11) DEFAULT NULL COMMENT '床位号',
  `sex` char(1) DEFAULT NULL COMMENT '性别',
  `major` varchar(255) DEFAULT NULL COMMENT '专业',
  `time` int(11) DEFAULT NULL COMMENT '时间',
  `ip` varchar(255) DEFAULT NULL COMMENT '选择IP',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user` (`user`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2749 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dx_apartment
-- ----------------------------

-- ----------------------------
-- Table structure for dx_open_client_user
-- ----------------------------
DROP TABLE IF EXISTS `dx_open_client_user`;
CREATE TABLE `dx_open_client_user` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL COMMENT '用户名',
  `headimg` varchar(512) DEFAULT NULL COMMENT '头像',
  `user` varchar(255) DEFAULT NULL COMMENT '用户ID',
  `access_token` varchar(255) DEFAULT NULL COMMENT '访问密钥',
  `refresh_token` varchar(255) DEFAULT NULL COMMENT '刷新密钥',
  `expires_in` int(11) DEFAULT NULL COMMENT '授权过期时间',
  `signup_ip` varchar(32) DEFAULT NULL COMMENT '注册IP',
  `signup_time` int(11) DEFAULT NULL COMMENT '注册时间',
  `status` tinyint(1) DEFAULT '1' COMMENT '用户状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dx_open_client_user
-- ----------------------------

-- ----------------------------
-- Table structure for dx_open_user
-- ----------------------------
DROP TABLE IF EXISTS `dx_open_user`;
CREATE TABLE `dx_open_user` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) DEFAULT NULL COMMENT '用户名',
  `email` varchar(128) DEFAULT NULL COMMENT '邮箱',
  `mobile` varchar(128) DEFAULT NULL COMMENT '手机号',
  `password` varchar(255) DEFAULT NULL COMMENT '密码',
  `headimg` varchar(512) DEFAULT NULL COMMENT '头像',
  `mobile_checked` tinyint(4) DEFAULT '0' COMMENT '短信验证',
  `mobile_send` int(11) DEFAULT '0' COMMENT '上次发送短信时间',
  `email_checked` tinyint(4) DEFAULT '0' COMMENT '邮箱验证',
  `code` varchar(128) DEFAULT NULL COMMENT '验证码',
  `code_type` int(10) DEFAULT '0' COMMENT '验证类型',
  `code_expires` int(11) DEFAULT NULL COMMENT '验证时间',
  `signup_ip` varchar(32) DEFAULT NULL COMMENT '注册IP',
  `signup_time` int(11) DEFAULT NULL COMMENT '注册时间',
  `status` tinyint(1) DEFAULT '1' COMMENT '用户状态',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE,
  UNIQUE KEY `email` (`email`) USING BTREE,
  UNIQUE KEY `mobile` (`mobile`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of dx_open_user
-- ----------------------------

-- ----------------------------
-- Table structure for dx_setting_grant
-- ----------------------------
DROP TABLE IF EXISTS `dx_setting_grant`;
CREATE TABLE `dx_setting_grant` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `grant` bigint(20) unsigned DEFAULT NULL COMMENT '授权权限',
  `investor` bigint(20) DEFAULT NULL COMMENT '授权者',
  `grantee` bigint(20) DEFAULT NULL COMMENT '授予者',
  `time` int(11) DEFAULT NULL COMMENT '授予时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dx_setting_grant
-- ----------------------------
INSERT INTO `dx_setting_grant` VALUES ('1', '1', '1', '1', '1554410095');

-- ----------------------------
-- Table structure for dx_setting_roles
-- ----------------------------
DROP TABLE IF EXISTS `dx_setting_roles`;
CREATE TABLE `dx_setting_roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL COMMENT '角色名',
  `permission` text COMMENT '权限控制对象',
  `sort` int(11) DEFAULT '0' COMMENT '排序索引',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dx_setting_roles
-- ----------------------------
INSERT INTO `dx_setting_roles` VALUES ('1', '系统管理员', '[\"setting:user\",\"setting:visitor\",\"setting:role\",\"open-user\",\"open-client\"]', '0');

-- ----------------------------
-- Table structure for dx_setting_user
-- ----------------------------
DROP TABLE IF EXISTS `dx_setting_user`;
CREATE TABLE `dx_setting_user` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL COMMENT '用户名',
  `email` varchar(255) DEFAULT NULL COMMENT '邮箱',
  `mobile` varchar(255) DEFAULT NULL COMMENT '手机号',
  `password` varchar(255) DEFAULT NULL COMMENT '密码',
  `headimg` varchar(512) DEFAULT NULL COMMENT '头像',
  `create_ip` varchar(32) DEFAULT NULL COMMENT '创建IP',
  `create_by` bigint(20) DEFAULT '0' COMMENT '创建的用户',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `status` tinyint(1) DEFAULT '1' COMMENT '用户状态',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE,
  UNIQUE KEY `email` (`email`) USING BTREE,
  UNIQUE KEY `mobile` (`mobile`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dx_setting_user
-- ----------------------------
INSERT INTO `dx_setting_user` VALUES ('1', 'dxkite', null, null, '$2y$10$HcWNu9WXITWxv9UwlsmT5uvKKN6j4R/DC/nxftA6s2YK.UkYJYhwy', null, '0.0.0.0', null, '1554011893', '1');

-- ----------------------------
-- Table structure for dx_setting_view_history
-- ----------------------------
DROP TABLE IF EXISTS `dx_setting_view_history`;
CREATE TABLE `dx_setting_view_history` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `session` varchar(32) DEFAULT NULL COMMENT '访问会话',
  `user` varchar(20) DEFAULT NULL COMMENT '用户ID',
  `hash` varchar(32) DEFAULT NULL COMMENT '访问地址',
  `ip` varchar(32) DEFAULT NULL COMMENT '访问IP',
  `url` varchar(512) DEFAULT NULL COMMENT '访问地址',
  `time` int(11) DEFAULT NULL COMMENT '访问时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dx_setting_view_history
-- ----------------------------

-- ----------------------------
-- Table structure for dx_student
-- ----------------------------
DROP TABLE IF EXISTS `dx_student`;
CREATE TABLE `dx_student` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user` bigint(20) DEFAULT NULL COMMENT '用户ID',
  `number` int(8) DEFAULT NULL COMMENT '学号',
  `name` varchar(255) DEFAULT NULL COMMENT '姓名',
  `exam_number` varchar(255) DEFAULT NULL COMMENT '考生号',
  `idcard` varchar(18) DEFAULT NULL COMMENT '身份证号',
  `sex` char(1) DEFAULT NULL COMMENT '性别',
  `major` varchar(255) DEFAULT NULL COMMENT '录取专业',
  `class` varchar(255) DEFAULT NULL COMMENT '录取班级',
  `arrearage` int(11) DEFAULT '0' COMMENT '欠费金额',
  `selected` tinyint(1) DEFAULT '0' COMMENT '是否已经选择',
  `export` tinyint(1) DEFAULT '0' COMMENT '导出标记',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2894 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dx_student
-- ----------------------------

-- ----------------------------
-- Table structure for dx_support_session
-- ----------------------------
DROP TABLE IF EXISTS `dx_support_session`;
CREATE TABLE `dx_support_session` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `grantee` bigint(20) DEFAULT NULL COMMENT '会话所有者',
  `group` varchar(32) DEFAULT NULL COMMENT '会话分组',
  `token` varchar(32) DEFAULT NULL COMMENT '验证令牌',
  `expire` int(11) DEFAULT NULL COMMENT '过期时间',
  `ip` varchar(32) DEFAULT NULL COMMENT '会话创建IP',
  `time` int(11) DEFAULT NULL COMMENT '会话创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dx_support_session
-- ----------------------------
