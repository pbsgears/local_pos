/*
Navicat MySQL Data Transfer

Source Server         : LocalServer
Source Server Version : 50622
Source Host           : 192.168.52.5:3306
Source Database       : central_db_local

Target Server Type    : MYSQL
Target Server Version : 50622
File Encoding         : 65001

Date: 2020-07-14 09:26:02
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for srp_erp_company
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_company`;
CREATE TABLE `srp_erp_company` (
  `company_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_link_id` int(11) DEFAULT NULL COMMENT 'if it is a school then school master id ',
  `branch_link_id` int(11) DEFAULT NULL COMMENT 'if it is school then branch id',
  `company_code` varchar(5) NOT NULL,
  `company_name` varchar(100) NOT NULL,
  `company_start_date` date NOT NULL,
  `company_url` varchar(100) DEFAULT NULL,
  `company_logo` varchar(255) DEFAULT NULL,
  `company_secondary_logo` varchar(255) DEFAULT NULL,
  `company_default_currencyID` int(11) DEFAULT NULL,
  `company_default_currency` varchar(45) DEFAULT NULL,
  `company_default_decimal` tinyint(1) DEFAULT '2',
  `company_reporting_currencyID` int(11) DEFAULT NULL,
  `company_reporting_currency` varchar(45) DEFAULT NULL,
  `company_reporting_decimal` tinyint(1) DEFAULT '2',
  `company_email` varchar(50) DEFAULT NULL,
  `company_phone` varchar(15) DEFAULT NULL,
  `companyPrintName` varchar(300) DEFAULT NULL,
  `companyPrintAddress` varchar(500) DEFAULT NULL,
  `companyPrintTelephone` varchar(500) DEFAULT NULL,
  `companyPrintOther` varchar(500) DEFAULT NULL,
  `companyPrintTagline` varchar(500) DEFAULT NULL,
  `company_address1` varchar(150) DEFAULT NULL,
  `company_address2` varchar(150) DEFAULT NULL,
  `company_city` varchar(100) DEFAULT NULL,
  `company_province` varchar(100) DEFAULT NULL,
  `company_postalcode` int(5) DEFAULT NULL,
  `countryID` int(11) DEFAULT NULL,
  `company_country` varchar(100) DEFAULT NULL,
  `legalName` varchar(45) DEFAULT NULL,
  `textIdentificationNo` varchar(45) DEFAULT NULL,
  `textYear` varchar(45) DEFAULT NULL,
  `industryID` int(11) DEFAULT NULL,
  `industry` varchar(45) DEFAULT NULL,
  `default_segment` varchar(100) DEFAULT NULL,
  `companyFinanceYearID` int(11) DEFAULT NULL,
  `companyFinanceYear` varchar(100) DEFAULT NULL,
  `FYBegin` date DEFAULT NULL,
  `FYEnd` date DEFAULT NULL,
  `companyFinancePeriodID` int(11) DEFAULT NULL,
  `FYPeriodDateFrom` date DEFAULT NULL,
  `FYPeriodDateTo` date DEFAULT NULL,
  `defaultTimezoneID` int(11) DEFAULT NULL,
  `confirmedYN` int(1) DEFAULT '0',
  `host` longtext,
  `op_host` longtext,
  `db_username` longtext,
  `db_password` longtext,
  `db_name` longtext,
  `attachmentFolderName` varchar(255) DEFAULT NULL,
  `attachmentHost` varchar(255) DEFAULT NULL,
  `subscriptionNo` varchar(100) DEFAULT NULL,
  `registeredDate` date DEFAULT NULL COMMENT 'Date of company registation to the system',
  `subscriptionStartDate` date DEFAULT NULL,
  `subscriptionCurrency` int(11) DEFAULT NULL,
  `subscriptionAmount` double DEFAULT NULL,
  `implementationAmount` double DEFAULT NULL,
  `paymentEnabled` int(1) DEFAULT '0',
  `isInitialSubscriptionConfirmed` int(1) DEFAULT '0',
  `isSubscriptionEnabled` int(1) DEFAULT '1',
  `isSubscriptionDisabled` int(1) DEFAULT '0',
  `adminType` int(1) DEFAULT '1',
  `createdUserGroup` varchar(11) DEFAULT NULL,
  `createdPCID` varchar(45) DEFAULT NULL,
  `createdUserID` varchar(45) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`company_id`) USING BTREE,
  UNIQUE KEY `company_id` (`company_id`) USING BTREE,
  KEY `company_name` (`company_name`) USING BTREE,
  KEY `default_segment` (`default_segment`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='recreated by shafri only for localhost implementation';

-- ----------------------------
-- Records of srp_erp_company
-- ----------------------------
INSERT INTO `srp_erp_company` VALUES ('1', null, null, '', '', '0000-00-00', null, null, null, null, null, '2', null, null, '2', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0', 'f8b5e3d46d48070ee6085c8340b70f742bc017a9ae6a17049a261ba600cd32d289984ac3c188bfd55f0a99f8dcd4bd272d6a6da4c7269e2aaff5bd26d7a78322WUCNKJ1QQ7oG4OukgndNOUuHZg2Wh07VMapbjOaSJaI=', null, 'dbfeb59dbdec2254b332d21204204d82cca9b1331fd0be84f3b34dc246c0c102a1009106e16a1f0b9ec10a7e36fd37db7994a3136f4ddaf534903cc684bbc7d5xjeX07LbOBlVZXpR58etHX/NxlLZ0yi3bCdGDt/hs7w=', '28331774853d5bb48af1c7b4e6dabd7bd2ca0aaa2f230795c3c78e1c0ee76aa0ee16246eef0775a7053b9a1e0c869fcba21bdfa83ef955cefc99561c2553e9ddDlGuE7Jh1lEPodxdoIOBDMECvAsz3ULNA4CVqVXCrGc=', '58488a5a585d26204a7e4bf3652048b9cb36a892bf9d865a6a67bb69176259d0862fe359914094fbd526e9e3a4e9cddae0289f04e9b69c57a77ced3624933219uuUav0ezjcEPnJTK1+iUgs7clyrGrqaxaSuOGwUPAe8=', null, null, null, null, null, null, null, null, '0', '0', '1', '0', '1', null, null, null, null, null, null, null, null, null, null);

-- ----------------------------
-- Table structure for srp_erp_countrymaster
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_countrymaster`;
CREATE TABLE `srp_erp_countrymaster` (
  `countryID` int(11) NOT NULL AUTO_INCREMENT,
  `countryShortCode` varchar(2) NOT NULL DEFAULT '',
  `CountryDes` varchar(100) NOT NULL DEFAULT '',
  `Nationality` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`countryID`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of srp_erp_countrymaster
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_currencymaster
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_currencymaster`;
CREATE TABLE `srp_erp_currencymaster` (
  `currencyID` int(11) NOT NULL AUTO_INCREMENT,
  `CurrencyName` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `CurrencyCode` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `DecimalPlaces` int(11) DEFAULT NULL,
  `ExchangeRate` double DEFAULT '0',
  `isLocal` int(11) DEFAULT NULL,
  `DateModified` datetime DEFAULT NULL,
  `ModifiedBy` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `createdUserGroup` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `createdPcID` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `createdUserID` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `modifiedPc` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `modifiedUser` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `timeStamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`currencyID`) USING BTREE,
  UNIQUE KEY `currencyID` (`currencyID`) USING BTREE,
  UNIQUE KEY `CurrencyCode` (`CurrencyCode`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of srp_erp_currencymaster
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_sessions
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_sessions`;
CREATE TABLE `srp_erp_sessions` (
  `id` varchar(40) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `data` blob NOT NULL,
  KEY `ci_sessions_timestamp` (`timestamp`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of srp_erp_sessions
-- ----------------------------

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `EidNo` int(11) NOT NULL AUTO_INCREMENT,
  `Username` varchar(255) DEFAULT NULL,
  `Password` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `empID` int(11) DEFAULT NULL,
  `companyID` int(11) DEFAULT NULL,
  `isSystemAdmin` tinyint(4) DEFAULT NULL,
  `randNum` longtext,
  `login_token` varchar(500) DEFAULT NULL,
  `groupID` int(11) DEFAULT NULL,
  `isGroupUser` int(1) DEFAULT '0',
  PRIMARY KEY (`EidNo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user
-- ----------------------------
