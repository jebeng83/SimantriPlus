CREATE TABLE `wa_outbox` (
  `nomor` bigint(20) NOT NULL AUTO_INCREMENT,
  `nowa` varchar(50) NOT NULL DEFAULT '',
  `pesan` text CHARACTER SET utf8 NOT NULL,
  `tanggal_jam` datetime DEFAULT NULL,
  `status` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT 'ANTRIAN',
  `source` varchar(50) DEFAULT NULL,
  `sender` varchar(10) NOT NULL DEFAULT 'NODEJS' COMMENT 'ANY, NODEJS, QISCUS',
  `success` varchar(1) DEFAULT NULL,
  `response` text,
  `request` text,
  `type` varchar(10) NOT NULL DEFAULT 'TEXT' COMMENT 'TEXT, IMAGE, VIDEO',
  `file` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`nomor`),
  KEY `NOWA` (`nowa`),
  KEY `STATUS` (`status`),
  KEY `SENDER` (`sender`)
) ENGINE=InnoDB AUTO_INCREMENT=146676 DEFAULT CHARSET=latin1
