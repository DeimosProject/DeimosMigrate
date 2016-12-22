SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id`     INT(11)      NOT NULL AUTO_INCREMENT,
  `name`   VARCHAR(63)  NOT NULL,
  `rules`  VARCHAR(255) NOT NULL,
  `active` TINYINT(1)   NOT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

INSERT INTO `roles` (`id`, `name`, `rules`, `active`) VALUES
  (1, 'admin', '7efdbe5add78a9d00d00e3eec952cd655a755274', 1),
  (2, 'user', '0731a2dabaa92ad365cf9fdcf3db98f78161871c', 1),
  (3, 'musers', '3c1bef9d2e6e4eafc993c3de0ca658b6f48e8297', 1);

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id`     INT(11)    NOT NULL AUTO_INCREMENT,
  `lname`  VARCHAR(127)        DEFAULT NULL,
  `fname`  VARCHAR(127)        DEFAULT NULL,
  `active` TINYINT(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

INSERT INTO `users` (`id`, `lname`, `fname`, `active`) VALUES
  (1, 'admin', NULL, 1),
  (2, 'user', NULL, 1),
  (3, 'muser', NULL, 1);

DROP TABLE IF EXISTS `usersRoles`;
CREATE TABLE `usersRoles` (
  `id`     INT(11) NOT NULL AUTO_INCREMENT,
  `roleId` INT(11) NOT NULL,
  `userId` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `roleId` (`roleId`),
  KEY `userId` (`userId`),
  CONSTRAINT `usersRoles_ibfk_1` FOREIGN KEY (`roleId`) REFERENCES `roles` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `usersRoles_ibfk_2` FOREIGN KEY (`userId`) REFERENCES `users` (`id`)
    ON DELETE CASCADE
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

INSERT INTO `usersRoles` (`id`, `roleId`, `userId`) VALUES
  (6, 1, 1),
  (7, 2, 1),
  (8, 2, 2),
  (9, 3, 1),
  (10, 3, 3);