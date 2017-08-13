-- Adminer 4.3.1 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `Transactions`;
CREATE TABLE `Transactions` (
  `transaction_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `uid` varchar(36) NOT NULL,
  `wallet_type` varchar(80) NOT NULL,
  `target` varchar(80) NOT NULL,
  `amount` float NOT NULL,
  `last_total` float DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`transaction_id`),
  KEY `uid` (`uid`),
  KEY `uid_wallet_type_target` (`uid`,`wallet_type`,`target`),
  KEY `wallet_type_target` (`wallet_type`,`target`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- 2017-08-13 18:16:49
