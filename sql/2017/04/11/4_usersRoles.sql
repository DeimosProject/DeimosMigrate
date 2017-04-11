SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

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