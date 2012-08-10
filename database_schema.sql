# Dump of table album
# ------------------------------------------------------------

CREATE TABLE `album` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `flickr_id` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table album_collection
# ------------------------------------------------------------

CREATE TABLE `album_collection` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `album_id` varchar(255) DEFAULT NULL,
  `collection_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table collection
# ------------------------------------------------------------

CREATE TABLE `collection` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `flickr_id` varchar(255) DEFAULT NULL,
  `title` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table photo
# ------------------------------------------------------------

CREATE TABLE `photo` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `flickr_id` varchar(256) NOT NULL DEFAULT '',
  `title` varchar(512) DEFAULT NULL,
  `thumbnail` varchar(1024) DEFAULT NULL,
  `large` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table photo_album
# ------------------------------------------------------------

CREATE TABLE `photo_album` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `album_id` varchar(255) DEFAULT NULL,
  `set_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

