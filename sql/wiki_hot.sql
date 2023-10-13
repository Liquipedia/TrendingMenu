CREATE TABLE IF NOT EXISTS `wiki_hot` (
  `wiki` varbinary(16) NOT NULL,
  `page` varbinary(128) NOT NULL,
  `title` varbinary(128) NOT NULL,
  `hits` int(11) NOT NULL
) /*$wgDBTableOptions*/;

ALTER TABLE `wiki_hot`
  ADD KEY `wiki` (`wiki`);
