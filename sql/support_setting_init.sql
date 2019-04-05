/*
Navicat MySQL Data Transfer

Source Server         : phpStudy
Source Server Version : 50553
Source Host           : 127.0.0.1:3306
Source Database       : sudav3_apartment

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2019-04-05 09:36:24
*/

SET FOREIGN_KEY_CHECKS=0;

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
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dx_setting_roles
-- ----------------------------
INSERT INTO `dx_setting_roles` VALUES ('1', '系统管理员', '[\"user\",\"visitor\",\"role\"]', '0');

-- ----------------------------
-- Table structure for dx_setting_session
-- ----------------------------
DROP TABLE IF EXISTS `dx_setting_session`;
CREATE TABLE `dx_setting_session` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `grantee` bigint(20) DEFAULT NULL COMMENT '会话所有者',
  `group` varchar(32) DEFAULT NULL COMMENT '会话分组',
  `token` varchar(32) DEFAULT NULL COMMENT '验证令牌',
  `expire` int(11) DEFAULT NULL COMMENT '过期时间',
  `ip` varchar(32) DEFAULT NULL COMMENT '会话创建IP',
  `time` int(11) DEFAULT NULL COMMENT '会话创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

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
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `mobile` (`mobile`)
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
