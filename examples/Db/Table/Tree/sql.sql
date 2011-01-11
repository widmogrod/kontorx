CREATE TABLE  `shop_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `level` varchar(255) NOT NULL COMMENT 'Zagnieżdzenie',
  `name` varchar(100) NOT NULL COMMENT 'Nazwa',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
