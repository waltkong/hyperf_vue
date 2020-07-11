/*
Navicat MySQL Data Transfer

Source Server         : kongweitao虚拟机hyperf1
Source Server Version : 50728
Source Host           : 192.168.88.208:3306
Source Database       : hyperf1

Target Server Type    : MYSQL
Target Server Version : 50728
File Encoding         : 65001

Date: 2020-06-22 20:37:53
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `system_config`
-- ----------------------------
DROP TABLE IF EXISTS `system_config`;
CREATE TABLE `system_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(255) DEFAULT NULL COMMENT '分组',
  `name` varchar(255) DEFAULT NULL COMMENT '键名',
  `value` text COMMENT '值',
  `type` varchar(255) DEFAULT 'string' COMMENT '类型： [json] , [semicoma] , [string] , [stop] ,[url] ',
  `is_global` tinyint(3) DEFAULT NULL COMMENT '是否全局（忽略company_id） 1是0否',
  `company_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of system_config
-- ----------------------------

-- ----------------------------
-- Table structure for `system_log`
-- ----------------------------
DROP TABLE IF EXISTS `system_log`;
CREATE TABLE `system_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) DEFAULT NULL,
  `param` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `ip` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of system_log
-- ----------------------------

-- ----------------------------
-- Table structure for `system_menu`
-- ----------------------------
DROP TABLE IF EXISTS `system_menu`;
CREATE TABLE `system_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT '0' COMMENT '父id',
  `level` tinyint(3) DEFAULT NULL COMMENT '层级，parent_id=0时level=1，然后依次递增',
  `name` varchar(50) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `need_auth` tinyint(3) DEFAULT '0' COMMENT '1需要权限验证 0 不需要',
  `is_menu` tinyint(3) DEFAULT '1' COMMENT '是菜单 1是0否',
  `is_only_super_admin` tinyint(3) DEFAULT '0' COMMENT '仅仅超级管理员可操作 1必须 0不必要',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of system_menu
-- ----------------------------
INSERT INTO `system_menu` VALUES ('1', '1', '0', '1', '用户', null, '0', '1', '0', '2020-06-18 19:27:03', '2020-06-18 19:27:06', null);

-- ----------------------------
-- Table structure for `user_company`
-- ----------------------------
DROP TABLE IF EXISTS `user_company`;
CREATE TABLE `user_company` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL COMMENT '名称',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `phone` varchar(20) DEFAULT NULL COMMENT '电话',
  `contact_user` varchar(20) DEFAULT NULL COMMENT '联系人',
  `admin_status` tinyint(3) DEFAULT '2' COMMENT '超管 1 普通2',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user_company
-- ----------------------------
INSERT INTO `user_company` VALUES ('1', '超管', '平台管理', '13450290122', '小马', '1', '2020-06-17 19:20:34', '2020-06-17 19:20:34', null);

-- ----------------------------
-- Table structure for `user_role`
-- ----------------------------
DROP TABLE IF EXISTS `user_role`;
CREATE TABLE `user_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL COMMENT '角色名',
  `remark` varchar(255) DEFAULT NULL COMMENT '角色备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user_role
-- ----------------------------

-- ----------------------------
-- Table structure for `user_role_menu`
-- ----------------------------
DROP TABLE IF EXISTS `user_role_menu`;
CREATE TABLE `user_role_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) DEFAULT NULL,
  `menu_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user_role_menu
-- ----------------------------

-- ----------------------------
-- Table structure for `user_role_user`
-- ----------------------------
DROP TABLE IF EXISTS `user_role_user`;
CREATE TABLE `user_role_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) DEFAULT NULL COMMENT '角色id',
  `user_id` int(11) DEFAULT NULL COMMENT '用户id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user_role_user
-- ----------------------------

-- ----------------------------
-- Table structure for `user_user`
-- ----------------------------
DROP TABLE IF EXISTS `user_user`;
CREATE TABLE `user_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nickname` varchar(50) DEFAULT NULL COMMENT '昵称',
  `mobile` varchar(20) DEFAULT NULL COMMENT '手机号',
  `password` varchar(255) DEFAULT NULL COMMENT '密码',
  `salt` varchar(20) DEFAULT NULL COMMENT '密码加盐',
  `avatar` varchar(255) DEFAULT NULL COMMENT '头像',
  `company_id` int(11) DEFAULT NULL COMMENT '公司',
  `login_status` tinyint(3) DEFAULT '0' COMMENT '登录状态【1登录 0否，做用户挤下线功能】',
  `admin_status` tinyint(3) DEFAULT '2' COMMENT '用户权限状态 1超级用户 2普通用户',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user_user
-- ----------------------------
INSERT INTO `user_user` VALUES ('2', 'xiaoma', '13450290122', '6299973477962dbf6e2a924d7a9bccf4', '814765', '814765', '1', '1', '1', '2020-06-17 19:20:34', '2020-06-18 14:12:09', null);
