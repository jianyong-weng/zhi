/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50714
Source Host           : localhost:3306
Source Database       : zhi

Target Server Type    : MYSQL
Target Server Version : 50714
File Encoding         : 65001

Date: 2017-12-07 22:13:37
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `zhi_admin`
-- ----------------------------
DROP TABLE IF EXISTS `zhi_admin`;
CREATE TABLE `zhi_admin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID，主键',
  `password` varchar(32) NOT NULL DEFAULT '' COMMENT '用户密码',
  `encrypt` varchar(6) NOT NULL DEFAULT '' COMMENT '盐值',
  `lastloginip` int(10) NOT NULL DEFAULT '0' COMMENT '最近登陆IP地址',
  `lastlogintime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '最近登陆时间',
  `email` varchar(40) NOT NULL DEFAULT '' COMMENT '邮箱地址',
  `mobile` varchar(11) NOT NULL DEFAULT '' COMMENT '用户手机号',
  `realname` varchar(50) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `openid` varchar(100) NOT NULL DEFAULT '' COMMENT '微信openID',
  `weibo` varchar(100) NOT NULL DEFAULT '' COMMENT '微博账号',
  `qq` varchar(100) NOT NULL DEFAULT '' COMMENT 'QQ关联账号',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否有效(2:无效,1:有效)',
  `updatetime` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '最近修改时间',
  PRIMARY KEY (`id`),
  KEY `mobile` (`mobile`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='后台管理人员表';

-- ----------------------------
-- Records of zhi_admin
-- ----------------------------
INSERT INTO `zhi_admin` VALUES ('1', '67848355efc1a9896a2e7b969fc49e2d', '123456', '0', '2017-12-07 21:30:34', '396342220@qq.com', '18259106776', 'zhi', '', '', '', '1', '2017-12-07 21:30:34');

-- ----------------------------
-- Table structure for `zhi_admin_group`
-- ----------------------------
DROP TABLE IF EXISTS `zhi_admin_group`;
CREATE TABLE `zhi_admin_group` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` text,
  `rules` varchar(500) NOT NULL DEFAULT '' COMMENT '用户组拥有的规则id，多个规则 , 隔开',
  `listorder` smallint(5) unsigned NOT NULL DEFAULT '0',
  `updatetime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `listorder` (`listorder`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of zhi_admin_group
-- ----------------------------

-- ----------------------------
-- Table structure for `zhi_admin_group_access`
-- ----------------------------
DROP TABLE IF EXISTS `zhi_admin_group_access`;
CREATE TABLE `zhi_admin_group_access` (
  `uid` int(10) unsigned NOT NULL COMMENT '用户id',
  `group_id` mediumint(8) unsigned NOT NULL COMMENT '用户组id',
  UNIQUE KEY `uid_group_id` (`uid`,`group_id`),
  KEY `uid` (`uid`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of zhi_admin_group_access
-- ----------------------------

-- ----------------------------
-- Table structure for `zhi_admin_log`
-- ----------------------------
DROP TABLE IF EXISTS `zhi_admin_log`;
CREATE TABLE `zhi_admin_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '操作记录ID',
  `m` varchar(15) NOT NULL,
  `c` varchar(20) NOT NULL,
  `a` varchar(20) NOT NULL,
  `loginip` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '登录IP地址',
  `querystring` varchar(255) NOT NULL,
  `userid` int(11) unsigned NOT NULL DEFAULT '0',
  `realname` varchar(50) NOT NULL,
  `ip` int(10) NOT NULL,
  `time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '操作时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of zhi_admin_log
-- ----------------------------

-- ----------------------------
-- Table structure for `zhi_mail_log`
-- ----------------------------
DROP TABLE IF EXISTS `zhi_mail_log`;
CREATE TABLE `zhi_mail_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(50) NOT NULL DEFAULT '' COMMENT '邮件地址',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '邮件主题',
  `content` text NOT NULL COMMENT '邮件内容',
  `key` char(32) NOT NULL DEFAULT '' COMMENT '邮件验证码',
  `time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '发送时间',
  `updatetime` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '邮件类型：1忘记密码',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态:1未验证，2已验证',
  PRIMARY KEY (`id`),
  KEY `key` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='邮件发送记录';

-- ----------------------------
-- Records of zhi_mail_log
-- ----------------------------
INSERT INTO `zhi_mail_log` VALUES ('1', '396342220@qq.com', '找回密码', '找回密码<a href=\'http://zhi.cn/admin/login/reset?key=39ad9c62d795925dc2b106f2e4066cd5\'>请点击此链接</a>,本链接1小时内点击有效。', '39ad9c62d795925dc2b106f2e4066cd5', '2017-12-07 21:59:14', null, '1', '1');
