SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for `oauth_consumer`
-- ----------------------------
DROP TABLE IF EXISTS `oauth_consumer`;
CREATE TABLE `oauth_consumer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `secret` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `oauth_consumer_key_secret` (`key`,`secret`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `oauth_consumer_access`
-- ----------------------------
DROP TABLE IF EXISTS `oauth_consumer_access`;
CREATE TABLE `oauth_consumer_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `consumer_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `oauth_consumer_access_user` (`user_id`,`consumer_id`),
  KEY `oauth_consumer_access_user_id_idx` (`user_id`),
  KEY `oauth_consumer_access_consumer_id_idx` (`consumer_id`),
  CONSTRAINT `oauth_consumer_access_ibfk_2` FOREIGN KEY (`consumer_id`) REFERENCES `oauth_consumer` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `oauth_consumer_access_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `oauth_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `oauth_consumer_access_token`
-- ----------------------------
DROP TABLE IF EXISTS `oauth_consumer_access_token`;
CREATE TABLE `oauth_consumer_access_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `consumer_access_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `secret` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `oauth_consumer_access_token_token_uniq` (`token`),
  KEY `oauth_consumer_access_token_consumer_access_id_idx` (`consumer_access_id`),
  CONSTRAINT `oauth_consumer_access_token_ibfk_1` FOREIGN KEY (`consumer_access_id`) REFERENCES `oauth_consumer_access` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `oauth_consumer_access_token_ibfk_2` FOREIGN KEY (`token`) REFERENCES `oauth_token` (`token`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `oauth_consumer_temporary`
-- ----------------------------
DROP TABLE IF EXISTS `oauth_consumer_temporary`;
CREATE TABLE `oauth_consumer_temporary` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `consumer_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `secret` varchar(255) NOT NULL,
  `verifyCode` varchar(255) NOT NULL,
  `callbackUri` varchar(255) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `oauth_consumer_temporary_token_uniq` (`token`),
  KEY `oauth_consumer_temporary_user_id_idx` (`user_id`),
  KEY `oauth_consumer_temporary_consumer_id_idx` (`consumer_id`),
  CONSTRAINT `oauth_consumer_temporary_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `oauth_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `oauth_consumer_temporary_ibfk_2` FOREIGN KEY (`consumer_id`) REFERENCES `oauth_consumer` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `oauth_consumer_temporary_ibfk_3` FOREIGN KEY (`token`) REFERENCES `oauth_token` (`token`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `oauth_token`
-- ----------------------------
DROP TABLE IF EXISTS `oauth_token`;
CREATE TABLE `oauth_token` (
  `token` varchar(255) NOT NULL,
  PRIMARY KEY (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `oauth_token_request`
-- ----------------------------
DROP TABLE IF EXISTS `oauth_token_request`;
CREATE TABLE `oauth_token_request` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(255) NOT NULL,
  `nonce` varchar(50) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `oauth_token_request_tnt` (`token`,`nonce`,`timestamp`),
  KEY `oauth_token_request_token_idx` (`token`),
  CONSTRAINT `oauth_token_request_ibfk_1` FOREIGN KEY (`token`) REFERENCES `oauth_token` (`token`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for `oauth_user`
-- ----------------------------
DROP TABLE IF EXISTS `oauth_user`;
CREATE TABLE `oauth_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(200) NOT NULL,
  `password` varchar(200) NOT NULL,
  `role_id` varchar(200) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `oauth_user_email` (`email`),
  KEY `oauth_user_role_id_idx` (`role_id`),
  CONSTRAINT `oauth_user_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `acl_role` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;