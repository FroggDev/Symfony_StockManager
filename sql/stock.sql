CREATE TABLE IF NOT EXISTS stock
(
  id BIGINT AUTO_INCREMENT
    PRIMARY KEY
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

/*!40000 ALTER TABLE `stock` DISABLE KEYS */;
INSERT INTO stock (id) VALUES (1);
/*!40000 ALTER TABLE `stock` ENABLE KEYS */;