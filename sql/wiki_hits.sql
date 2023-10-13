CREATE TABLE IF NOT EXISTS `wiki_hits` (
  `url` varbinary(255) NOT NULL,
  `wiki` varbinary(24) NOT NULL,
  `datum` int(10) UNSIGNED NOT NULL
) /*$wgDBTableOptions*/;

ALTER TABLE `wiki_hits`
  ADD KEY `wiki` (`wiki`,`datum`);
