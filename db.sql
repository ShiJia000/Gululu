-- CREATE DATABASE

CREATE DATABASE `nextdoor`;

USE `nextdoor`;

-- user table
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(45) NOT NULL,
  `lastname` varchar(45) NOT NULL,
  `user_pwd` char(32) NOT NULL,
  `state` varchar(15) NOT NULL,
  `city` varchar(45) NOT NULL,
  `zipcode` int(5) NOT NULL,
  `address` text NOT NULL,
  `phone_num` bigint NOT NULL,
  `photo` text NOT NULL,
  `self_intro` text,
  `family_intro` text,
  `profile_timestamp` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `email` text NOT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- neighbor table
DROP TABLE IF EXISTS `neighbor`;
CREATE TABLE `neighbor` (
	`uid` int(11) NOT NULL,
	`neighbor_uid` int(11) NOT NULL,
	`is_valid` tinyint NOT NULL DEFAULT 0 COMMENT '0 means not valid, 1 means valid',
	PRIMARY KEY (`uid`, `neighbor_uid`),
	FOREIGN KEY (`uid`) REFERENCES `user` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (`neighbor_uid`) REFERENCES `user` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

DROP TABLE IF EXISTS `friend`;
CREATE TABLE `friend` (
	`uid` int(11) NOT NULL,
	`friend_uid` int(11) NOT NULL,
	`is_valid` tinyint NOT NULL DEFAULT 0 COMMENT '0 means not valid, 1 means valid',
	PRIMARY KEY (`uid`, `friend_uid`),
	FOREIGN KEY (`uid`) REFERENCES `user` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (`friend_uid`) REFERENCES `user` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- type table
DROP TABLE IF EXISTS `type`;
CREATE TABLE `type` (
  `tid` int(11) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(20) NOT NULL,
  PRIMARY KEY (`tid`)
)ENGINE=InnoDB;

-- message table
DROP TABLE IF EXISTS `message`;
CREATE TABLE `message` (
	`mid` int(11) NOT NULL AUTO_INCREMENT,
	`title` varchar(200) NOT NULL,
	`uid` int(11) NOT NULL,
	`text_body` text NOT NULL,
	`timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`lantitude` decimal(10, 8) COMMENT 'can be null',
	`longitude` decimal(11, 8) COMMENT 'can be null',
	`tid` int(11) NOT NULL,
	PRIMARY KEY (`mid`),
	FOREIGN KEY (`uid`) REFERENCES `user` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (`tid`) REFERENCES `type` (`tid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- receive_msg table
DROP TABLE IF EXISTS `receive_msg`;
CREATE TABLE `receive_msg` (
	`mid` int(11) NOT NULL,
	`uid` int(11) NOT NULL,
	`is_read` tinyint NOT NULL DEFAULT 0,
	`read_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`mid`, `uid`),
	FOREIGN KEY (`uid`) REFERENCES `user` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (`mid`) REFERENCES `message` (`mid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- reply table
DROP TABLE IF EXISTS `reply`;
CREATE TABLE `reply` (
	`rid` int(11) NOT NULL AUTO_INCREMENT,
	`mid` int(11) NOT NULL,
	`uid` int(11) NOT NULL,
	`reply_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`content` text NOT NULL,
	PRIMARY KEY (`rid`),
	FOREIGN KEY (`uid`) REFERENCES `user` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (`mid`) REFERENCES `message` (`mid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- receive_reply table
DROP TABLE IF EXISTS `receive_reply`;
CREATE TABLE `receive_reply` (
	`rid` int(11) NOT NULL,
	`uid` int(11) NOT NULL,
	`is_read` tinyint NOT NULL DEFAULT 0,
	`read_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`rid`, `uid`),
	FOREIGN KEY (`uid`) REFERENCES `user` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (`rid`) REFERENCES `reply` (`rid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- hood table
DROP TABLE IF EXISTS `hood`;
CREATE TABLE `hood` (
  `hid` int(11) NOT NULL AUTO_INCREMENT,
  `hname` varchar(50) NOT NULL,
  PRIMARY KEY (`hid`)
)ENGINE=InnoDB;

-- block table
DROP TABLE IF EXISTS `block`;
CREATE TABLE `block` (
  `bid` int(11) NOT NULL AUTO_INCREMENT,
  `hid` int(11) NOT NULL,
  `bname` varchar(50) NOT NULL,
  PRIMARY KEY (`bid`),
  FOREIGN KEY (`hid`) REFERENCES `hood` (`hid`)
)ENGINE=InnoDB;

-- join_block
DROP TABLE IF EXISTS `join_block`;
CREATE TABLE `join_block` (
  `joinid` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `bid` int(11) NOT NULL,
  `is_approved` int(1) NOT NULL,
  `approve_num` varchar(10) NOT NULL,
  PRIMARY KEY (`joinid`),
  FOREIGN KEY (`uid`) REFERENCES `user` (`uid`),
  FOREIGN KEY (`bid`) REFERENCES `block` (`bid`)
)ENGINE=InnoDB;

-- agree_join
DROP TABLE IF EXISTS `agree_join`;
CREATE TABLE `agree_join` (
  `uid` int(11) NOT NULL,
  `joinid` int(11) NOT NULL,
  `is_agree` int(1) NOT NULL,
  PRIMARY KEY (`uid`,`joinid`),
  FOREIGN KEY (`uid`) REFERENCES `user` (`uid`),
  FOREIGN KEY (`joinid`) REFERENCES `join_block` (`joinid`)
)ENGINE=InnoDB;

-- msg_setting
DROP TABLE IF EXISTS `msg_setting`;
CREATE TABLE `msg_setting` (
  `uid` int(11) NOT NULL,
  `tid` int(11) NOT NULL,
  PRIMARY KEY (`uid`,`tid`),
  FOREIGN KEY (`uid`) REFERENCES `user` (`uid`),
  FOREIGN KEY (`tid`) REFERENCES `type` (`tid`)
)ENGINE=InnoDB;

-- access
DROP TABLE IF EXISTS `access`;
CREATE TABLE `access` (
  `uid` int(11) NOT NULL,
  `access_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`uid`,`access_timestamp`),
  FOREIGN KEY (`uid`) REFERENCES `user` (`uid`)
)ENGINE=InnoDB;

INSERT INTO USER (`firstname`, `lastname`, `user_pwd`, `state`, `city`, `zipcode`, `address`, `phone_num`, `photo`, `self_intro`, `family_intro`, `profile_timestamp`, `email`)
VALUES ('Jia', 'Shi', 'psw', 'New York', 'Brooklyn', 11202,'111 Street', 9491118218,'jia.jpg', null, null, '2017-07-23 13:10:11', 'js11182@nyu.edu');

