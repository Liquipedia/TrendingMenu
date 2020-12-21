CREATE TABLE IF NOT EXISTS `wiki_list` (
	`wiki` varbinary(100) NOT NULL,
	`slug` varbinary(100) NOT NULL,
	`type` varbinary(100) NOT NULL
) /*$wgDBTableOptions*/;

INSERT INTO `wiki_list` (`wiki`, `slug`, `type`) VALUES
('Dota 2', 'dota2', 'mainWiki'),
('Counter-Strike', 'counterstrike', 'mainWiki'),
('PUBG', 'pubg', 'mainWiki'),
('StarCraft II', 'starcraft2', 'mainWiki'),
('Rocket League', 'rocketleague', 'mainWiki'),
('VALORANT', 'valorant', 'mainWiki'),
('Overwatch', 'overwatch', 'mainWiki'),
('Rainbow Six', 'rainbowsix', 'mainWiki'),
('Apex Legends', 'apexlegends', 'mainWiki'),
('League of Legends', 'leagueoflegends', 'mainWiki'),
('Warcraft III', 'warcraft', 'mainWiki'),
('Brood War', 'starcraft', 'mainWiki'),
('Smash', 'smash', 'mainWiki'),
('Hearthstone', 'hearthstone', 'mainWiki'),
('Heroes', 'heroes', 'mainWiki'),
('Artifact', 'artifact', 'mainWiki'),
('Age of Empires', 'ageofempires', 'alphaWiki'),
('Arena of Valor', 'arenaofvalor', 'alphaWiki'),
('Fighting Games', 'fighters', 'alphaWiki'),
('Arena FPS', 'arenafps', 'alphaWiki'),
('Clash Royale', 'clashroyale', 'alphaWiki'),
('Fortnite', 'fortnite', 'alphaWiki'),
('Call of Duty', 'callofduty', 'alphaWiki'),
('Team Fortress', 'teamfortress', 'alphaWiki'),
('Free Fire', 'freefire', 'alphaWiki'),
('World of Warcraft', 'worldofwarcraft', 'alphaWiki'),
('FIFA', 'fifa', 'alphaWiki'),
('Paladins', 'paladins', 'alphaWiki'),
('TrackMania', 'trackmania', 'preAlphaWiki'),
('Battlerite', 'battlerite', 'preAlphaWiki'),
('CrossFire', 'crossfire', 'preAlphaWiki'),
('Pokémon', 'pokemon', 'preAlphaWiki'),
('Battalion 1944', 'battalion', 'preAlphaWiki'),
('Critical Ops', 'criticalops', 'preAlphaWiki'),
('Magic: The Gathering', 'magic', 'preAlphaWiki'),
('Auto Chess', 'autochess', 'preAlphaWiki'),
('Sim Racing', 'simracing', 'preAlphaWiki'),
('Dota Underlords', 'underlords', 'preAlphaWiki'),
('Teamfight Tactics', 'teamfighttactics', 'preAlphaWiki'),
('Brawl Stars', 'brawlstars', 'preAlphaWiki'),
('Runeterra', 'runeterra', 'preAlphaWiki'),
('Wild Rift', 'wildrift', 'preAlphaWiki');
