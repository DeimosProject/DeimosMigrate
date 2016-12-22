SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `images`;
CREATE TABLE `images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `imageUrl` varchar(256) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`),
  CONSTRAINT `images_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `images` (`id`, `userId`, `imageUrl`) VALUES
(1,	1,	'http://asdsadsadsadsaasdsadsadsadsa.ss/asd.jpg'),
(2,	2,	'http://asdsad.cc/sadsadsasa.ss/asd.jpg'),
(3,	3,	'http://asdsad.cc/sadsadsasa.ss/a.s/d.jpg');