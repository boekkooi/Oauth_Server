/*
Navicat MySQL Data Transfer

Source Server         : Jobability
Source Server Version : 50141
Source Host           : ja:3306
Source Database       : oauth_test

Target Server Type    : MYSQL
Target Server Version : 50141
File Encoding         : 65001

Date: 2010-10-11 09:23:19
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for `consumer`
-- ----------------------------
DROP TABLE IF EXISTS `consumer`;
CREATE TABLE `consumer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(255) CHARACTER SET utf8 NOT NULL,
  `secret` varchar(255) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`),
  UNIQUE KEY `secret` (`secret`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of consumer
-- ----------------------------

-- ----------------------------
-- Table structure for `consumer_access`
-- ----------------------------
DROP TABLE IF EXISTS `consumer_access`;
CREATE TABLE `consumer_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `consumer_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`consumer_id`),
  KEY `consumer_id` (`consumer_id`),
  CONSTRAINT `consumer_access_ibfk_1` FOREIGN KEY (`consumer_id`) REFERENCES `consumer` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of consumer_access
-- ----------------------------

-- ----------------------------
-- Table structure for `consumer_access_token`
-- ----------------------------
DROP TABLE IF EXISTS `consumer_access_token`;
CREATE TABLE `consumer_access_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `consumer_access_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `secret` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `consumer_access_token_ibfk_1` (`consumer_access_id`),
  CONSTRAINT `token_access_fk` FOREIGN KEY (`token`) REFERENCES `token` (`token`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `consumer_access_token_ibfk_1` FOREIGN KEY (`consumer_access_id`) REFERENCES `consumer_access` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of consumer_access_token
-- ----------------------------

-- ----------------------------
-- Table structure for `consumer_temporary`
-- ----------------------------
DROP TABLE IF EXISTS `consumer_temporary`;
CREATE TABLE `consumer_temporary` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `consumerId` int(11) NOT NULL,
  `token` varchar(255) CHARACTER SET utf8 NOT NULL,
  `secret` varchar(255) CHARACTER SET utf8 NOT NULL,
  `verifyCode` varchar(255) DEFAULT NULL,
  `callbackUri` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  UNIQUE KEY `verifyCode` (`verifyCode`),
  KEY `consumerId` (`consumerId`),
  CONSTRAINT `token_temp_fk` FOREIGN KEY (`token`) REFERENCES `token` (`token`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `consumer_temporary_ibfk_1` FOREIGN KEY (`consumerId`) REFERENCES `consumer` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of consumer_temporary
-- ----------------------------

-- ----------------------------
-- Table structure for `token`
-- ----------------------------
DROP TABLE IF EXISTS `token`;
CREATE TABLE `token` (
  `token` varchar(255) NOT NULL COMMENT 'The main token table all tokens that are in use are here',
  PRIMARY KEY (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of token
-- ----------------------------

-- ----------------------------
-- Table structure for `token_request`
-- ----------------------------
DROP TABLE IF EXISTS `token_request`;
CREATE TABLE `token_request` (
  `token` varchar(255) NOT NULL,
  `nonce` varchar(50) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`token`,`nonce`,`timestamp`),
  CONSTRAINT `token_fk` FOREIGN KEY (`token`) REFERENCES `token` (`token`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of token_request
-- ----------------------------
