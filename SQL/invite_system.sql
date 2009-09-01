-- ----------------------------
-- Table structure for point_system
-- ----------------------------
DROP TABLE IF EXISTS `point_system`;

CREATE TABLE `point_system` (
  `entry` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'Entry number',
  `accountid` bigint(20) NOT NULL,
  `points` bigint(20) NOT NULL DEFAULT '0',
  UNIQUE KEY `entry` (`entry`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for point_system_invites
-- ----------------------------
DROP TABLE IF EXISTS `point_system_invites`;

CREATE TABLE `point_system_invites` (
  `entry` int(11) NOT NULL AUTO_INCREMENT,
  `PlayersAccount` char(50) DEFAULT NULL,
  `InvitedBy` char(50) DEFAULT NULL,
  `InviterAccount` char(50) DEFAULT NULL,
  `Treated` int(1) NOT NULL DEFAULT '0',
  `Rewarded` int(1) NOT NULL DEFAULT '0',
  UNIQUE KEY `entry` (`entry`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for point_system_requests
-- ----------------------------
DROP TABLE IF EXISTS `point_system_requests`;

CREATE TABLE `point_system_requests` (
  `entry` bigint(20) NOT NULL AUTO_INCREMENT,
  `username` char(120) NOT NULL DEFAULT '',
  `request` char(120) NOT NULL DEFAULT '',
  `date` datetime DEFAULT '0000-00-00 00:00:00',
  `code` char(120) NOT NULL DEFAULT 'none',
  `treated` char(3) NOT NULL DEFAULT 'No',
  UNIQUE KEY `entry` (`entry`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;