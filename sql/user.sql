CREATE TABLE IF NOT EXISTS `user`
(
  id               INT AUTO_INCREMENT PRIMARY KEY,
  first_name       VARCHAR(100) NOT NULL,
  last_name        VARCHAR(100) NOT NULL,
  email            VARCHAR(255) NOT NULL,
  token            VARCHAR(100) NULL,
  token_validity   DATETIME     NULL,
  password         VARCHAR(64)  NOT NULL,
  date_inscription DATETIME     NOT NULL,
  roles            LONGTEXT     NOT NULL
  COMMENT '(DC2Type:array)',
  last_connexion   DATETIME     NULL,
  status           INT          NOT NULL,
  date_closed      DATETIME     NULL,
  stock_id         BIGINT       NULL,
  CONSTRAINT UNIQ_8D93D649E7927C74
  UNIQUE (email),
  CONSTRAINT UNIQ_8D93D649DCD6110
  UNIQUE (stock_id),
  CONSTRAINT FK_8D93D649DCD6110
  FOREIGN KEY (stock_id) REFERENCES stock (id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO user (id, first_name, last_name, email, token, token_validity, password, date_inscription, roles, last_connexion, status, date_closed, stock_id) VALUES (1, 'Frogg', 'Frogg', 'admin@frogg.fr', null, null, '$2y$13$SEVdTLMKUxiNi5Y3X8tHL.b/WKBu/lYyq9Tp9YaGj893qMRAjvdLK', '2018-04-20 17:49:18', 'a:1:{i:1;s:10:"ROLE_ADMIN";}', '2018-04-21 14:38:21', 1, null, 1);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;