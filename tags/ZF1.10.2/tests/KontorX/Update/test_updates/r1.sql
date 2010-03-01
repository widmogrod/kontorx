CREATE TABLE IF NOT EXISTS `mail_template` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `sender` varchar(255) NOT NULL,
  `sender_name` varchar(255) default NULL,
  `content` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
