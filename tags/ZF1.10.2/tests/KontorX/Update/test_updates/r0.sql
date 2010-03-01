CREATE TABLE IF NOT EXISTS `mail` (
  `email` varchar(255) NOT NULL COMMENT 'E-mail',
  `name` varchar(255) default NULL COMMENT 'Nazwa odbiorcy',
  PRIMARY KEY  USING BTREE (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
