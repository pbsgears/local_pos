/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : pos_localdb2

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2018-11-30 12:34:08
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for srp_employeesdetails
-- ----------------------------
DROP TABLE IF EXISTS `srp_employeesdetails`;
CREATE TABLE `srp_employeesdetails` (
  `EIdNo` int(11) NOT NULL AUTO_INCREMENT,
  `serialNo` int(11) DEFAULT NULL,
  `ECode` varchar(50) DEFAULT NULL,
  `EmpSecondaryCode` varchar(30) DEFAULT NULL,
  `EmpTitleId` int(50) DEFAULT NULL,
  `manPowerNo` varchar(50) DEFAULT NULL,
  `ssoNo` varchar(20) DEFAULT NULL,
  `EmpDesignationId` varchar(50) DEFAULT NULL,
  `Ename1` varchar(200) DEFAULT NULL,
  `Ename2` varchar(200) DEFAULT NULL,
  `Ename3` varchar(200) DEFAULT NULL,
  `Ename4` varchar(200) DEFAULT NULL,
  `initial` varchar(255) DEFAULT NULL,
  `EmpShortCode` varchar(200) DEFAULT NULL,
  `Enameother1` varchar(50) DEFAULT NULL,
  `Enameother2` varchar(50) DEFAULT NULL,
  `Enameother3` varchar(50) DEFAULT NULL,
  `Enameother4` varchar(50) DEFAULT NULL,
  `empSignature` varchar(255) DEFAULT NULL,
  `EmpImage` varchar(255) DEFAULT NULL,
  `Gender` varchar(1) DEFAULT NULL,
  `EpAddress1` varchar(50) DEFAULT NULL,
  `EpAddress2` varchar(50) DEFAULT NULL,
  `EpAddress3` varchar(50) DEFAULT NULL,
  `EpAddress4` varchar(50) DEFAULT NULL,
  `ZipCode` varchar(50) DEFAULT NULL,
  `EpTelephone` varchar(50) DEFAULT NULL,
  `EpFax` varchar(50) DEFAULT NULL,
  `EcAddress1` varchar(255) DEFAULT NULL,
  `EcAddress2` varchar(255) DEFAULT NULL,
  `EcAddress3` varchar(255) DEFAULT NULL,
  `EcAddress4` varchar(255) DEFAULT NULL,
  `EcPOBox` varchar(50) DEFAULT NULL,
  `EcPC` varchar(50) DEFAULT NULL,
  `EcArea` varchar(50) DEFAULT NULL,
  `EcTel` varchar(50) DEFAULT NULL,
  `EcFax` varchar(50) DEFAULT NULL,
  `EcMobile` varchar(50) DEFAULT NULL,
  `EEmail` varchar(50) DEFAULT NULL,
  `personalEmail` varchar(50) DEFAULT NULL,
  `EDOB` date DEFAULT NULL,
  `EDOJ` date DEFAULT NULL,
  `NIC` varchar(255) DEFAULT NULL COMMENT 'National Idinticatd No',
  `EPassportNO` varchar(50) DEFAULT NULL,
  `EPassportExpiryDate` date DEFAULT NULL,
  `EVisaExpiryDate` date DEFAULT NULL,
  `Nid` int(11) DEFAULT NULL,
  `Rid` int(11) DEFAULT NULL,
  `AirportDestination` varchar(50) DEFAULT NULL,
  `SchMasterId` int(11) DEFAULT '0',
  `branchID` int(11) DEFAULT NULL,
  `UserName` varchar(255) DEFAULT NULL,
  `Password` varchar(255) DEFAULT NULL,
  `isDeleted` int(1) DEFAULT '0',
  `HouseID` int(1) DEFAULT '0',
  `HouseCatID` int(2) DEFAULT '0',
  `HPID` int(11) DEFAULT '0',
  `isPayrollEmployee` int(1) DEFAULT '1' COMMENT '1 yes 0 no',
  `payCurrencyID` int(11) DEFAULT '0',
  `payCurrency` varchar(5) DEFAULT NULL,
  `isLeft` int(1) DEFAULT '0',
  `DateLeft` date DEFAULT NULL,
  `LeftComment` varchar(45) DEFAULT NULL,
  `BloodGroup` int(11) DEFAULT NULL,
  `DateAssumed` date DEFAULT NULL,
  `probationPeriod` date DEFAULT NULL,
  `isDischarged` int(1) DEFAULT '0' COMMENT 'IF yes -1 else 0',
  `dischargedByEmpID` int(11) DEFAULT NULL,
  `EmployeeConType` int(11) DEFAULT NULL,
  `dischargedDate` date DEFAULT NULL,
  `lastWorkingDate` date DEFAULT NULL,
  `dischargedComment` varchar(255) DEFAULT NULL,
  `finalSettlementDoneYN` int(1) DEFAULT '0' COMMENT '0- no 1- yes',
  `MaritialStatus` int(11) DEFAULT NULL,
  `Nationality` int(11) DEFAULT NULL,
  `isLoginAttempt` int(1) DEFAULT '0',
  `isChangePassword` int(1) DEFAULT '1',
  `CreatedUserName` varchar(255) DEFAULT NULL,
  `CreatedDate` datetime DEFAULT NULL,
  `CreatedPC` varchar(255) DEFAULT NULL,
  `ModifiedUserName` varchar(255) DEFAULT NULL,
  `Timestamp` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `ModifiedPC` varchar(255) DEFAULT NULL,
  `isActive` int(1) DEFAULT '1',
  `NoOfLoginAttempt` int(1) DEFAULT '0',
  `languageID` int(1) DEFAULT NULL,
  `locationID` int(11) DEFAULT NULL,
  `segmentID` int(11) DEFAULT NULL,
  `Erp_companyID` int(11) DEFAULT NULL,
  `floorID` int(11) DEFAULT NULL,
  `empMachineID` int(11) DEFAULT NULL COMMENT 'Payroll Machine Employee Auto ID',
  `leaveGroupID` int(11) DEFAULT NULL,
  `isCheckin` int(11) DEFAULT NULL,
  `token` varchar(400) DEFAULT NULL,
  `overTimeGroup` int(11) DEFAULT NULL,
  `gratuityID` int(11) DEFAULT '0' COMMENT 'FK srp_erp_pay_gratuitymaster.gratuityID',
  `isSystemAdmin` int(1) DEFAULT '0',
  `isHRAdmin` int(1) DEFAULT NULL,
  `contractStartDate` date DEFAULT NULL,
  `contractEndDate` date DEFAULT NULL,
  `contractRefNo` varchar(255) DEFAULT NULL,
  `empConfirmDate` date DEFAULT NULL,
  `empConfirmedYN` tinyint(1) DEFAULT NULL,
  `rejoinDate` date DEFAULT NULL,
  `previousEmpID` int(11) DEFAULT NULL,
  `gradeID` int(11) DEFAULT NULL,
  `pos_userGroupMasterID` tinyint(4) DEFAULT NULL,
  `pos_barCode` varchar(255) DEFAULT NULL,
  `isLocalPosSyncEnable` int(1) DEFAULT '0',
  PRIMARY KEY (`EIdNo`),
  UNIQUE KEY `EIdNo` (`EIdNo`) USING BTREE,
  UNIQUE KEY `UserName_UNIQUE` (`UserName`),
  KEY `payCurrencyID` (`payCurrencyID`) USING BTREE,
  KEY `isDischarged` (`isDischarged`) USING BTREE,
  KEY `isActive` (`isActive`) USING BTREE,
  KEY `segmentID` (`segmentID`) USING BTREE,
  KEY `floorID` (`floorID`) USING BTREE,
  KEY `Erp_companyID` (`Erp_companyID`) USING BTREE,
  KEY `isSystemAdmin` (`isSystemAdmin`) USING BTREE,
  KEY `isPayrollEmployee` (`isPayrollEmployee`) USING BTREE,
  KEY `ssoNo` (`ssoNo`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of srp_employeesdetails
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_bankledger
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_bankledger`;
CREATE TABLE `srp_erp_bankledger` (
  `bankLedgerAutoID` int(11) NOT NULL AUTO_INCREMENT,
  `wareHouseAutoID` int(11) NOT NULL DEFAULT '0',
  `documentDate` date DEFAULT NULL,
  `transactionType` int(1) DEFAULT NULL COMMENT '1 -  Receipt 2 - Payment if JV debit 1 credit 2',
  `partyType` varchar(3) DEFAULT NULL COMMENT 'CUS - or SUP or Emp',
  `partyAutoID` int(11) DEFAULT NULL COMMENT 'auto id of customer of supplier from master table',
  `partyCode` varchar(100) DEFAULT NULL COMMENT 'code of supplier or customer ',
  `partyName` varchar(200) DEFAULT NULL,
  `partyCurrencyID` int(11) DEFAULT NULL,
  `partyCurrency` varchar(45) DEFAULT NULL COMMENT 'currency of supplier or customer ',
  `partyCurrencyExchangeRate` double DEFAULT '0',
  `partyCurrencyDecimalPlaces` double DEFAULT '2',
  `partyCurrencyAmount` double DEFAULT '0',
  `transactionCurrencyID` int(11) DEFAULT NULL,
  `transactionCurrency` varchar(45) DEFAULT NULL COMMENT 'Document transaction currency',
  `transactionExchangeRate` double DEFAULT '0' COMMENT 'Always 1',
  `transactionAmount` double DEFAULT '0' COMMENT 'Amount of transaction in document',
  `transactionCurrencyDecimalPlaces` int(1) DEFAULT '2' COMMENT 'Decimal places of transaction currency ',
  `bankCurrencyID` int(11) DEFAULT NULL,
  `bankCurrency` varchar(45) DEFAULT NULL COMMENT 'Document transaction currency',
  `bankCurrencyExchangeRate` double DEFAULT '0' COMMENT 'Always 1',
  `bankCurrencyAmount` double DEFAULT '0' COMMENT 'Amount of transaction in document',
  `bankCurrencyDecimalPlaces` int(1) DEFAULT '2' COMMENT 'Decimal places of transaction currency ',
  `modeofPayment` int(1) DEFAULT NULL COMMENT '1 - Cash 2 - cheque',
  `chequeNo` varchar(45) DEFAULT NULL,
  `chequeDate` date DEFAULT NULL,
  `isThirdPartyCheque` tinyint(1) DEFAULT '0' COMMENT '0- No 1-Yes',
  `thirdPartyName` varchar(200) DEFAULT NULL COMMENT 'If its a third party cheque names should be maintained',
  `thirdPartyInfo` varchar(255) DEFAULT NULL COMMENT 'addtional details of third party ',
  `remainIn` int(11) DEFAULT '3' COMMENT 'if post dated cheque notification should show before days',
  `memo` varchar(255) DEFAULT NULL,
  `bankName` varchar(255) DEFAULT NULL,
  `bankGLAutoID` int(11) DEFAULT NULL,
  `bankSystemAccountCode` varchar(45) DEFAULT NULL,
  `bankGLSecondaryCode` varchar(45) DEFAULT NULL,
  `documentMasterAutoID` int(11) DEFAULT NULL,
  `documentType` varchar(45) DEFAULT NULL COMMENT 'RV or PV or JV',
  `documentSystemCode` varchar(100) DEFAULT NULL,
  `clearedYN` int(1) DEFAULT '0' COMMENT 'update as if selected in bankrec',
  `clearedDate` datetime DEFAULT NULL,
  `clearedAmount` double DEFAULT '0',
  `clearedBy` varchar(45) DEFAULT NULL,
  `bankRecMonthID` int(11) DEFAULT NULL,
  `createdPCID` varchar(45) DEFAULT NULL,
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) DEFAULT NULL,
  `segmentID` int(11) DEFAULT NULL,
  `segmentCode` varchar(45) DEFAULT NULL,
  `createdUserID` varchar(45) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `timeStamp` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `is_sync` int(1) DEFAULT '0',
  `id_store` int(255) NOT NULL DEFAULT '0',
  PRIMARY KEY (`bankLedgerAutoID`,`id_store`),
  KEY `bankLedgerAutoID` (`bankLedgerAutoID`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='Table Use : All the transactions related to banks will be recorded in this tbale\r\nCreated By : Mohamed Hisham on 27.09.2016\r\nDiscussed with : Mohamed Rishad,Mohamed Reyaas, Shahmy Mohamed\r\nReviewed By : ';

-- ----------------------------
-- Records of srp_erp_bankledger
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_chartofaccounts
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_chartofaccounts`;
CREATE TABLE `srp_erp_chartofaccounts` (
  `GLAutoID` int(11) NOT NULL AUTO_INCREMENT,
  `systemAccountCode` varchar(45) NOT NULL COMMENT 'code generated by system',
  `GLSecondaryCode` varchar(45) NOT NULL,
  `GLDescription` varchar(255) DEFAULT NULL,
  `masterAccountYN` tinyint(1) DEFAULT '0' COMMENT '0- No 1-Yes',
  `masterAutoID` int(11) DEFAULT NULL COMMENT 'if MasterAccountYN-0 then should record the autoID of masteraccount',
  `masterAccount` varchar(45) DEFAULT NULL COMMENT 'Secondary code of master account',
  `masterAccountDescription` varchar(255) DEFAULT NULL COMMENT 'description master account ',
  `masterCategory` varchar(2) NOT NULL COMMENT 'BS or PL',
  `levelNo` int(2) DEFAULT NULL,
  `accountCategoryTypeID` int(11) DEFAULT NULL COMMENT 'autoid from srp_erp_accountcategorytypes table',
  `CategoryTypeDescription` varchar(200) DEFAULT NULL COMMENT 'description from srp_erp_accountcategorytypes table',
  `subCategory` varchar(3) DEFAULT NULL COMMENT 'BSA or BSL or BSE or PLI or PLE',
  `controllAccountYN` tinyint(1) DEFAULT '0' COMMENT '0 - No 1- Yes',
  `isActive` tinyint(1) DEFAULT '0' COMMENT '0 - No 1- Yes',
  `accountDefaultType` int(2) DEFAULT '0' COMMENT '1 - System Change Over',
  `isAuto` tinyint(1) DEFAULT '0',
  `isCard` tinyint(1) DEFAULT '0' COMMENT '0- No 1- Yes (should be 1 if isBank-1 and it is a credit card account)',
  `isBank` tinyint(1) DEFAULT '0' COMMENT 'if its bank -1 else 0',
  `isCash` int(1) DEFAULT '0' COMMENT 'if cash type bank both isbank and iscash should be 1',
  `replicatedCoaID` int(11) DEFAULT NULL,
  `bankName` varchar(150) DEFAULT NULL,
  `bankBranch` varchar(150) DEFAULT NULL,
  `bankShortCode` varchar(10) DEFAULT NULL,
  `bankSwiftCode` varchar(150) DEFAULT '0',
  `bankCheckNumber` varchar(50) DEFAULT '',
  `authourizedSignatureLevel` int(1) DEFAULT '0',
  `bankAccountNumber` varchar(45) DEFAULT '0',
  `bankCurrencyID` int(11) DEFAULT NULL,
  `bankCurrencyCode` varchar(45) DEFAULT NULL,
  `bankCurrencyDecimalPlaces` double DEFAULT '2',
  `confirmedYN` tinyint(1) DEFAULT '0',
  `confirmedDate` datetime DEFAULT NULL,
  `confirmedbyEmpID` varchar(45) DEFAULT NULL,
  `confirmedbyName` varchar(200) DEFAULT NULL,
  `approvedYN` tinyint(1) DEFAULT '0',
  `approvedDate` datetime DEFAULT NULL,
  `approvedbyEmpID` varchar(45) DEFAULT NULL,
  `approvedbyEmpName` varchar(200) DEFAULT NULL,
  `approvedComment` varchar(255) DEFAULT NULL,
  `companyID` int(5) DEFAULT NULL,
  `companyCode` varchar(45) DEFAULT NULL,
  `createdPCID` varchar(150) DEFAULT NULL,
  `createdUserGroup` varchar(50) DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `createdUserID` varchar(150) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `modifiedPCID` varchar(150) DEFAULT NULL,
  `modifiedUserID` varchar(150) DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`GLAutoID`),
  UNIQUE KEY `GLAutoID` (`GLAutoID`) USING BTREE,
  KEY `systemAccountCode` (`systemAccountCode`) USING BTREE,
  KEY `GLSecondaryCode` (`GLSecondaryCode`) USING BTREE,
  KEY `isActive` (`isActive`) USING BTREE,
  KEY `controllAccountYN` (`controllAccountYN`) USING BTREE,
  KEY `GLDescription` (`GLDescription`) USING BTREE,
  KEY `masterAccountYN` (`masterAccountYN`) USING BTREE,
  KEY `masterAutoID` (`masterAutoID`) USING BTREE,
  KEY `levelNo` (`levelNo`) USING BTREE,
  KEY `accountCategoryTypeID` (`accountCategoryTypeID`) USING BTREE,
  KEY `subCategory` (`subCategory`) USING BTREE,
  KEY `isCard` (`isCard`) USING BTREE,
  KEY `isBank` (`isBank`) USING BTREE,
  KEY `isCash` (`isCash`) USING BTREE,
  KEY `bankName` (`bankName`) USING BTREE,
  KEY `bankBranch` (`bankBranch`) USING BTREE,
  KEY `bankAccountNumber` (`bankAccountNumber`) USING BTREE,
  KEY `bankCurrencyID` (`bankCurrencyID`) USING BTREE,
  KEY `bankCurrencyDecimalPlaces` (`bankCurrencyDecimalPlaces`) USING BTREE,
  KEY `companyID` (`companyID`) USING BTREE,
  KEY `companyCode` (`companyCode`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table Use : TO create financial ledgers and maintain control accounts ,master accounts  and other accounts\r\nCreated By : Mohamed Hisham \r\nDiscussed with : Mohamed Rishad,Mohamed Reyaas, Nusky Rauf\r\nReviewed By : ';

-- ----------------------------
-- Records of srp_erp_chartofaccounts
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_company
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_company`;
CREATE TABLE `srp_erp_company` (
  `company_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_link_id` int(11) DEFAULT NULL COMMENT 'if it is a school then school master id ',
  `branch_link_id` int(11) DEFAULT NULL COMMENT 'if it is school then branch id',
  `productID` int(1) DEFAULT '1' COMMENT '1 - Spur  2- Gears Standard',
  `company_code` varchar(5) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `company_start_date` date NOT NULL,
  `company_url` varchar(100) DEFAULT NULL,
  `company_logo` varchar(255) DEFAULT NULL,
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
  `companyPrintTagline` text,
  `company_address1` varchar(150) DEFAULT NULL,
  `company_address2` varchar(150) DEFAULT NULL,
  `company_city` varchar(100) DEFAULT NULL,
  `company_province` varchar(100) DEFAULT NULL,
  `company_postalcode` varchar(20) DEFAULT '',
  `countryID` int(11) DEFAULT NULL,
  `company_country` varchar(100) DEFAULT NULL,
  `legalName` varchar(45) DEFAULT NULL,
  `isVatEligible` int(1) DEFAULT '1',
  `vatIdNo` varchar(30) DEFAULT NULL,
  `textIdentificationNo` varchar(45) DEFAULT NULL,
  `textYear` varchar(45) DEFAULT NULL,
  `industryID` int(11) DEFAULT NULL,
  `industry` varchar(45) DEFAULT NULL,
  `mfqIndustryID` int(11) DEFAULT NULL,
  `default_segment` varchar(100) DEFAULT NULL,
  `supportURL` text,
  `noOfUsers` int(11) DEFAULT '0',
  `companyFinanceYearID` int(11) DEFAULT NULL,
  `companyFinanceYear` varchar(100) DEFAULT NULL,
  `FYBegin` date DEFAULT NULL,
  `FYEnd` date DEFAULT NULL,
  `companyFinancePeriodID` int(11) DEFAULT NULL,
  `FYPeriodDateFrom` date DEFAULT NULL,
  `FYPeriodDateTo` date DEFAULT NULL,
  `pos_isFinanceEnables` int(1) DEFAULT '0',
  `isBuyBackEnabled` int(1) DEFAULT '0',
  `companyType` int(1) DEFAULT '1' COMMENT '1- FIN 2 - PVT',
  `pvtCompanyID` int(11) DEFAULT NULL,
  `defaultTimezoneID` int(11) DEFAULT NULL,
  `confirmedYN` int(1) DEFAULT '0',
  `localposaccesstoken` varchar(500) DEFAULT '' COMMENT 'Local POS system pull request validation',
  `createdUserGroup` varchar(50) DEFAULT NULL,
  `createdPCID` varchar(45) DEFAULT NULL,
  `createdUserID` varchar(45) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`company_id`),
  UNIQUE KEY `company_id` (`company_id`) USING BTREE,
  KEY `company_name` (`company_name`) USING BTREE,
  KEY `default_segment` (`default_segment`) USING BTREE,
  KEY `company_default_currencyID` (`company_default_currencyID`) USING BTREE,
  KEY `company_default_decimal` (`company_default_decimal`) USING BTREE,
  KEY `company_default_currency` (`company_default_currency`) USING BTREE,
  KEY `company_reporting_currencyID` (`company_reporting_currencyID`) USING BTREE,
  KEY `company_reporting_currency` (`company_reporting_currency`) USING BTREE,
  KEY `company_reporting_decimal` (`company_reporting_decimal`) USING BTREE,
  KEY `countryID` (`countryID`) USING BTREE,
  KEY `companyFinanceYearID` (`companyFinanceYearID`) USING BTREE,
  KEY `companyFinancePeriodID` (`companyFinancePeriodID`) USING BTREE,
  KEY `productID` (`productID`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table Use : company Master Table\r\nCreated By : Nusky Rauf\r\nDiscussed with : Mohamed Rishad,Mohamed Reyaas,Mohamed Hisham\r\nReviewed By : ';

-- ----------------------------
-- Records of srp_erp_company
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_companycontrolaccounts
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_companycontrolaccounts`;
CREATE TABLE `srp_erp_companycontrolaccounts` (
  `controlAccountsAutoID` int(11) NOT NULL AUTO_INCREMENT,
  `controlAccountType` varchar(45) DEFAULT NULL,
  `controlAccountDescription` varchar(255) DEFAULT NULL,
  `GLAutoID` int(11) DEFAULT NULL,
  `systemAccountCode` varchar(45) DEFAULT NULL,
  `GLSecondaryCode` varchar(45) DEFAULT NULL,
  `GLDescription` varchar(255) DEFAULT NULL,
  `companyID` int(3) NOT NULL,
  `companyCode` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`controlAccountsAutoID`),
  UNIQUE KEY `controlAccountsAutoID` (`controlAccountsAutoID`) USING BTREE,
  KEY `controlAccountType` (`controlAccountType`) USING BTREE,
  KEY `GLAutoID` (`GLAutoID`) USING BTREE,
  KEY `companyID` (`companyID`) USING BTREE,
  KEY `companyCode` (`companyCode`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table Use : To store default controll accounts of all the companies\r\nCreated By : Nusky Rauf\r\nDiscussed with : Mohamed Rishad,Mohamed Reyaas, Mohamed Hisham\r\nReviewed By : ';

-- ----------------------------
-- Records of srp_erp_companycontrolaccounts
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_companycurrencyassign
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_companycurrencyassign`;
CREATE TABLE `srp_erp_companycurrencyassign` (
  `currencyassignAutoID` int(11) NOT NULL AUTO_INCREMENT,
  `currencyID` int(11) DEFAULT NULL,
  `CurrencyName` varchar(100) DEFAULT NULL,
  `CurrencyCode` varchar(45) DEFAULT NULL,
  `DecimalPlaces` int(11) DEFAULT NULL,
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`currencyassignAutoID`),
  UNIQUE KEY `currencyassignAutoID` (`currencyassignAutoID`) USING BTREE,
  KEY `currencyID` (`currencyID`) USING BTREE,
  KEY `CurrencyCode` (`CurrencyCode`) USING BTREE,
  KEY `DecimalPlaces` (`DecimalPlaces`) USING BTREE,
  KEY `companyID` (`companyID`) USING BTREE,
  KEY `companyCode` (`companyCode`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of srp_erp_companycurrencyassign
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_companycurrencyconversion
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_companycurrencyconversion`;
CREATE TABLE `srp_erp_companycurrencyconversion` (
  `currencyConversionAutoID` int(11) NOT NULL AUTO_INCREMENT,
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) DEFAULT NULL,
  `mastercurrencyassignAutoID` int(11) DEFAULT NULL,
  `masterCurrencyID` int(11) DEFAULT NULL,
  `masterCurrencyCode` varchar(45) DEFAULT NULL,
  `subcurrencyassignAutoID` int(11) DEFAULT NULL,
  `subCurrencyID` int(11) DEFAULT NULL,
  `subCurrencyCode` varchar(45) DEFAULT NULL,
  `conversion` double DEFAULT '0',
  `timestamp` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`currencyConversionAutoID`),
  UNIQUE KEY `currencyConversionAutoID` (`currencyConversionAutoID`) USING BTREE,
  KEY `companyID` (`companyID`) USING BTREE,
  KEY `companyCode` (`companyCode`) USING BTREE,
  KEY `masterCurrencyID` (`masterCurrencyID`) USING BTREE,
  KEY `subCurrencyID` (`subCurrencyID`) USING BTREE,
  KEY `masterCurrencyCode` (`masterCurrencyCode`) USING BTREE,
  KEY `subCurrencyCode` (`subCurrencyCode`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of srp_erp_companycurrencyconversion
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_companyfinanceperiod
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_companyfinanceperiod`;
CREATE TABLE `srp_erp_companyfinanceperiod` (
  `companyFinancePeriodID` int(11) NOT NULL AUTO_INCREMENT,
  `companyFinanceYearID` int(11) DEFAULT NULL,
  `dateFrom` date DEFAULT NULL,
  `dateTo` date DEFAULT NULL,
  `isActive` int(1) DEFAULT '0',
  `isCurrent` int(1) DEFAULT '0',
  `isClosed` int(1) DEFAULT '0',
  `closedByEmpID` varchar(100) DEFAULT NULL,
  `closedByEmpName` varchar(300) DEFAULT NULL,
  `closedDate` datetime DEFAULT NULL,
  `comments` varchar(200) DEFAULT NULL,
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) DEFAULT NULL,
  `createdUserGroup` int(11) DEFAULT NULL,
  `createdPCID` varchar(45) DEFAULT NULL,
  `createdUserID` varchar(45) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`companyFinancePeriodID`),
  UNIQUE KEY `companyFinancePeriodID` (`companyFinancePeriodID`) USING BTREE,
  KEY `companyFinanceYearID` (`companyFinanceYearID`) USING BTREE,
  KEY `isActive` (`isActive`) USING BTREE,
  KEY `isCurrent` (`isCurrent`) USING BTREE,
  KEY `isClosed` (`isClosed`) USING BTREE,
  KEY `companyID` (`companyID`) USING BTREE,
  KEY `companyCode` (`companyCode`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table Use : Financial Periods for created financial years will be maintained in this table\r\nCreated By : Nusky Rauf\r\nDiscussed with : Mohamed Rishad,Mohamed Reyaas, Mohamed Hisham\r\nReviewed By : ';

-- ----------------------------
-- Records of srp_erp_companyfinanceperiod
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_companyfinanceyear
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_companyfinanceyear`;
CREATE TABLE `srp_erp_companyfinanceyear` (
  `companyFinanceYearID` int(11) NOT NULL AUTO_INCREMENT,
  `beginingDate` date DEFAULT NULL,
  `endingDate` date DEFAULT NULL,
  `isActive` int(1) DEFAULT '0',
  `isCurrent` int(1) DEFAULT '0',
  `isClosed` int(1) DEFAULT '0',
  `closedByEmpID` varchar(100) DEFAULT NULL,
  `closedByEmpName` varchar(300) DEFAULT NULL,
  `closedDate` datetime DEFAULT NULL,
  `comments` varchar(200) DEFAULT NULL,
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) DEFAULT NULL,
  `createdUserGroup` int(11) DEFAULT NULL,
  `createdPCID` varchar(45) DEFAULT NULL,
  `createdUserID` varchar(45) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`companyFinanceYearID`),
  UNIQUE KEY `companyFinanceYearID` (`companyFinanceYearID`) USING BTREE,
  KEY `isActive` (`isActive`) USING BTREE,
  KEY `isCurrent` (`isCurrent`) USING BTREE,
  KEY `isClosed` (`isClosed`) USING BTREE,
  KEY `companyID` (`companyID`) USING BTREE,
  KEY `companyCode` (`companyCode`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table Use : to maintain financial years for all companies\r\nCreated By : Nusky Rauf\r\nDiscussed with : Mohamed Rishad,Mohamed Reyaas, Mohamed Hisham\r\nReviewed By : ';

-- ----------------------------
-- Records of srp_erp_companyfinanceyear
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_companypolicy
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_companypolicy`;
CREATE TABLE `srp_erp_companypolicy` (
  `companyPolicyAutoID` int(11) NOT NULL AUTO_INCREMENT,
  `companypolicymasterID` int(11) DEFAULT NULL,
  `companyID` int(11) DEFAULT NULL,
  `code` varchar(45) DEFAULT NULL,
  `documentID` varchar(45) DEFAULT NULL,
  `isYN` int(1) DEFAULT '0',
  `value` varchar(255) DEFAULT NULL,
  `createdUserGroup` varchar(100) DEFAULT NULL,
  `createdPCID` varchar(100) DEFAULT NULL,
  `createdUserID` varchar(100) DEFAULT NULL,
  `createdDateTime` varchar(100) DEFAULT NULL,
  `modifiedPCID` datetime DEFAULT NULL,
  `modifiedUserID` timestamp NULL DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(100) DEFAULT NULL,
  `timestamp` datetime DEFAULT NULL,
  PRIMARY KEY (`companyPolicyAutoID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of srp_erp_companypolicy
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_companypolicymaster
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_companypolicymaster`;
CREATE TABLE `srp_erp_companypolicymaster` (
  `companypolicymasterID` int(11) NOT NULL AUTO_INCREMENT,
  `companyPolicyDescription` varchar(255) DEFAULT NULL,
  `systemValue` varchar(255) DEFAULT NULL,
  `isDocumentLevel` int(11) DEFAULT '0',
  `code` varchar(45) DEFAULT NULL,
  `documentID` varchar(45) DEFAULT NULL,
  `defaultValue` varchar(255) DEFAULT NULL,
  `fieldType` enum('text','select','checkbox','radio') DEFAULT NULL,
  `is_active` int(1) DEFAULT '0',
  `isCompanyLevel` int(1) DEFAULT '0',
  `timestamp` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`companypolicymasterID`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of srp_erp_companypolicymaster
-- ----------------------------
INSERT INTO `srp_erp_companypolicymaster` VALUES ('1', 'Water mark in all print documents', null, '0', 'WM', 'All', '1', 'select', '1', '0', '2018-06-07 10:37:23');
INSERT INTO `srp_erp_companypolicymaster` VALUES ('2', 'DateFormat', null, '0', 'DF', 'All', 'dd-mm-yyyy', 'select', '1', '0', '2017-04-06 14:27:02');
INSERT INTO `srp_erp_companypolicymaster` VALUES ('3', 'Allow to Change PO Cost in GRV', null, '0', 'CPG', 'GRV', '1', 'select', '1', '0', '2017-04-06 14:27:17');
INSERT INTO `srp_erp_companypolicymaster` VALUES ('4', 'Time Format', null, '0', 'TF', 'All', 'hh:mm', 'select', '1', '0', '2017-04-06 14:27:21');
INSERT INTO `srp_erp_companypolicymaster` VALUES ('5', 'Payslip Template', null, '0', 'PT', 'SP', '0', 'select', '1', '1', '2017-04-06 14:27:24');
INSERT INTO `srp_erp_companypolicymaster` VALUES ('6', 'Non Payslip Template', null, '0', 'NPT', 'SPN', '0', 'select', '1', '1', '2017-04-06 14:27:32');
INSERT INTO `srp_erp_companypolicymaster` VALUES ('7', 'Water mark in all print documents', null, '0', 'WM', 'GRV', '1', 'select', '1', '0', '2017-04-06 14:34:14');
INSERT INTO `srp_erp_companypolicymaster` VALUES ('8', 'Document Code Setup', null, '0', 'DC', 'All', '1', 'select', '1', '0', null);
INSERT INTO `srp_erp_companypolicymaster` VALUES ('9', 'Is Project Enabled', null, '0', 'PE', 'All', '0', 'select', '1', '0', '2017-07-21 11:49:56');
INSERT INTO `srp_erp_companypolicymaster` VALUES ('10', 'Password Complexity', null, '0', 'PC', 'All', '0', 'select', '1', '0', '2017-07-26 15:04:49');
INSERT INTO `srp_erp_companypolicymaster` VALUES ('11', 'Approval for Employee Personal Detail Update', '', '0', 'EPD', 'All', '0', 'select', '1', '0', '2017-07-31 10:22:14');
INSERT INTO `srp_erp_companypolicymaster` VALUES ('12', 'Project Cost/Revenue Order', null, '0', 'PCR', 'P', '0', 'select', '1', '0', null);
INSERT INTO `srp_erp_companypolicymaster` VALUES ('13', 'Salary Proportion Formula', '', '0', 'SPF', 'All', '365', 'select', '1', '0', '2017-08-10 10:40:45');
INSERT INTO `srp_erp_companypolicymaster` VALUES ('14', 'Salary Calculation Days', null, '0', 'SCD', 'All', 'LAST_DAY(effectiveDate)', 'select', '1', '0', '2017-08-28 10:50:17');
INSERT INTO `srp_erp_companypolicymaster` VALUES ('15', 'Is Non Salary Process YES/NO', null, '0', 'NSP', 'All', '0', 'select', '1', '0', null);
INSERT INTO `srp_erp_companypolicymaster` VALUES ('16', 'Apply Sick Leave Based on Sort Order', null, '0', 'SL', 'All', '0', 'select', '1', '0', null);
INSERT INTO `srp_erp_companypolicymaster` VALUES ('17', 'Allow to Apply Leave If a Leave is not Approved', null, '0', 'LP', 'All', '0', 'select', '1', '0', null);
INSERT INTO `srp_erp_companypolicymaster` VALUES ('18', 'Payroll access controll ', null, '0', 'PAC', 'All', '0', 'select', '1', '0', '2017-11-13 17:25:00');
INSERT INTO `srp_erp_companypolicymaster` VALUES ('19', 'Employee System Code Auto Generated ', null, '0', 'ECG', 'All', '1', 'select', '1', '0', '2018-01-05 11:24:39');
INSERT INTO `srp_erp_companypolicymaster` VALUES ('20', 'Auto Generated Payment Voucher for Salary Transfer', null, '0', 'BTPV', 'All', '0', 'select', '1', '0', null);
INSERT INTO `srp_erp_companypolicymaster` VALUES ('21', 'Send Email Notifications', null, '0', 'SEN', 'All', '1', 'select', '1', '0', null);
INSERT INTO `srp_erp_companypolicymaster` VALUES ('22', 'Employee Master Edit Approval', null, '0', 'EMA', 'All', '0', 'select', '1', '0', null);
INSERT INTO `srp_erp_companypolicymaster` VALUES ('23', 'Invoice - Applicable Tax Type', null, '0', 'ATT', 'All', '0', 'select', '1', '0', null);
INSERT INTO `srp_erp_companypolicymaster` VALUES ('24', 'Location wise document code generation', null, '0', 'LDG', 'All', '0', 'select', '1', '0', null);
INSERT INTO `srp_erp_companypolicymaster` VALUES ('25', 'Third Party Applications', null, '0', 'TPA', 'ALL', '0', 'select', '1', '0', null);

-- ----------------------------
-- Table structure for srp_erp_companypolicymaster_value
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_companypolicymaster_value`;
CREATE TABLE `srp_erp_companypolicymaster_value` (
  `policyValueID` int(11) NOT NULL AUTO_INCREMENT,
  `companypolicymasterID` int(11) NOT NULL,
  `value` varchar(255) DEFAULT NULL,
  `systemValue` varchar(255) DEFAULT NULL,
  `companyID` int(11) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`policyValueID`)
) ENGINE=InnoDB AUTO_INCREMENT=335 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of srp_erp_companypolicymaster_value
-- ----------------------------
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('1', '1', 'Yes', '1', null, '2017-04-04 15:31:56');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('2', '1', 'No', '0', null, '2017-04-04 15:31:56');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('3', '2', 'dd-mm-yyyy', 'dd-mm-yyyy', null, '2017-04-04 15:31:56');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('4', '2', 'yyyy-mm-dd', 'yyyy-mm-dd', null, '2017-04-04 15:31:56');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('5', '3', 'Yes', '1', null, '2017-04-04 15:31:56');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('6', '3', 'No', '0', null, '2017-04-04 16:01:00');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('7', '4', 'hh:mm', 'hh:mm', null, '2017-04-04 15:31:56');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('8', '4', 'hh:mm:ss', 'hh:mm:ss', null, '2017-04-04 15:31:56');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('9', '5', 'Default', '0', '13', '2018-06-04 12:32:38');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('11', '6', 'Default', '0', '13', '2018-06-04 12:32:38');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('12', '6', 'Template2', '1', '13', '2018-06-04 12:32:38');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('19', '7', 'Yes', '1', null, '2017-04-06 14:35:08');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('20', '7', 'No', '0', null, '2017-04-06 14:35:18');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('22', '5', 'TEMP-3', '11', '13', '2018-06-04 12:32:38');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('25', '5', 'Envoy Template 1', '12', '1', '2018-06-04 12:32:38');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('28', '5', 'General', '0', '109', '2018-06-04 12:32:38');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('29', '6', 'General', '0', '109', '2018-06-04 12:32:38');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('32', '5', 'General', '0', '110', '2018-06-04 12:32:38');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('33', '6', 'General', '0', '110', '2018-06-04 12:32:38');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('68', '5', 'General', '0', '112', '2018-06-04 12:32:38');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('69', '6', 'General', '0', '112', '2018-06-04 12:32:38');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('70', '8', 'Standard', '1', null, '2017-06-06 10:22:03');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('71', '8', 'Based on Finance Year', '2', null, '2017-06-06 10:22:21');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('73', '6', '2151', '26', '11', '2018-06-04 12:32:38');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('74', '5', 'General', '0', '115', '2018-06-04 12:32:38');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('75', '6', 'General', '0', '115', '2018-06-04 12:32:38');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('78', '9', 'Yes', '1', null, '2017-07-21 12:01:26');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('79', '9', 'No', '0', null, '2017-07-21 12:01:39');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('81', '10', 'Yes', '1', null, '2017-07-25 12:52:46');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('82', '10', 'No', '0', null, '2017-07-25 12:52:53');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('84', '11', 'Yes', '1', null, '2017-07-31 10:24:07');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('85', '11', 'No', '0', null, '2017-07-31 10:24:07');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('86', '12', '0', '0', null, '2017-07-31 11:41:35');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('87', '12', '1', '1', null, '2017-07-31 11:41:43');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('88', '5', 'Totals', '23', '13', '2018-06-04 12:32:38');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('89', '13', '(Salary * 12) / 365 * worked days', '365', null, '2017-08-10 10:40:57');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('90', '13', '(Salary * 12) / 360 * worked days', '360', null, '2017-08-10 10:41:03');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('92', '14', 'No. of days in current month ', 'LAST_DAY(effectiveDate)', null, '2017-08-25 10:06:55');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('93', '14', '30 Days', '30', null, '2017-08-23 17:56:39');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('94', '5', 'RCC Paysheet Template', '13', '11', '2018-06-04 12:32:38');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('95', '6', 'Non Payroll', '16', '13', '2018-06-04 12:32:38');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('97', '15', 'No', '0', null, '2017-10-27 14:35:40');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('98', '15', 'Yes', '1', null, '2017-10-27 14:35:50');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('99', '16', 'No', '0', null, '2017-10-31 14:40:34');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('100', '16', 'Yes', '1', null, '2017-10-31 14:40:38');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('101', '17', 'No', '0', null, '2017-10-31 14:40:34');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('102', '17', 'Yes', '1', null, '2017-10-31 14:40:38');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('103', '18', 'No', '0', null, '2017-11-13 14:40:34');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('104', '18', 'Yes', '1', null, '2017-11-13 15:00:03');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('105', '13', '(Salary / No. of days in current month) * worked days', '1', null, '2017-12-28 15:38:03');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('106', '19', 'Yes', '1', null, '2017-04-04 15:31:56');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('107', '19', 'No', '0', null, '2017-04-04 15:31:56');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('108', '5', 'General', '0', '125', '2018-06-04 12:32:38');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('109', '6', 'General', '0', '125', '2018-06-04 12:32:38');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('110', '20', 'No', '0', null, '2017-04-04 15:31:56');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('111', '20', 'Yes', '1', null, '2017-04-04 15:31:56');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('112', '21', 'Yes', '1', null, '2017-04-04 15:31:56');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('113', '21', 'No', '0', null, '2017-04-04 15:31:56');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('114', '5', 'Envoy Template', '12', '13', '2018-06-04 12:32:38');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('115', '22', 'No', '0', null, '2018-03-20 15:03:41');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('116', '22', 'Yes', '1', null, '2018-03-20 15:03:57');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('117', '23', 'Line By Tax', '0', null, '2018-03-27 12:11:58');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('118', '23', 'General Tax', '1', null, '2018-03-27 12:12:13');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('321', '5', 'Envoy Default', 'Envoy', '13', '2018-08-07 12:52:54');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('322', '5', 'General', '0', '165', '2018-08-28 15:20:37');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('323', '6', 'General', '0', '165', '2018-08-28 15:20:37');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('328', '5', 'Paysheet Template', '30', '165', '2018-08-29 17:17:34');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('329', '5', 'Aitken Template', 'Aitken', '13', '2018-09-14 15:52:44');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('330', '24', 'No', '0', null, '2018-09-27 10:34:21');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('331', '24', 'Yes', '1', null, '2018-09-27 10:34:33');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('332', '5', 'New template', '31', '13', '2018-10-03 11:05:01');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('333', '25', 'No', '0', null, '2018-09-27 10:34:21');
INSERT INTO `srp_erp_companypolicymaster_value` VALUES ('334', '25', 'Yes', '1', null, '2018-09-27 10:34:33');

-- ----------------------------
-- Table structure for srp_erp_currencydenomination
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_currencydenomination`;
CREATE TABLE `srp_erp_currencydenomination` (
  `masterID` int(11) NOT NULL AUTO_INCREMENT,
  `currencyID` int(11) DEFAULT NULL,
  `currencyCode` varchar(3) DEFAULT NULL,
  `amount` double DEFAULT NULL COMMENT 'Eg: if 50 Cents then 50',
  `value` double DEFAULT NULL COMMENT 'Eg: If 50 Cents then 0.05',
  `isNote` tinyint(1) DEFAULT '1' COMMENT 'if coin 0 else 1',
  `caption` varchar(45) DEFAULT NULL COMMENT 'RS or $ etc',
  PRIMARY KEY (`masterID`),
  UNIQUE KEY `masterID` (`masterID`) USING BTREE,
  KEY `currencyID` (`currencyID`) USING BTREE,
  KEY `currencyCode` (`currencyCode`) USING BTREE,
  KEY `isNote` (`isNote`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table Use : This table is used to load currency denominators in pos open till windows. system level table to store denomiators of each currency\r\nCreated By : Mohamed Hisham\r\nDiscussed with : Mohamed Rishad,Mohamed Reyaas, Nasik Ahamed\r\nReviewed By : ';

-- ----------------------------
-- Records of srp_erp_currencydenomination
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
  PRIMARY KEY (`currencyID`),
  UNIQUE KEY `currencyID` (`currencyID`) USING BTREE,
  UNIQUE KEY `CurrencyCode` (`CurrencyCode`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of srp_erp_currencymaster
-- ----------------------------
INSERT INTO `srp_erp_currencymaster` VALUES ('1', 'Omani Rial', 'OMR', '3', '1', '1', '2012-04-03 00:00:00', 'admin', null, null, null, null, null, null, null);
INSERT INTO `srp_erp_currencymaster` VALUES ('2', 'US Dollar', 'USD', '2', '0.385', '0', '2012-04-03 00:00:00', 'admin', null, null, null, null, null, null, null);
INSERT INTO `srp_erp_currencymaster` VALUES ('3', 'UAE Dirham', 'AED', '2', '0.25', '0', null, null, null, null, null, null, null, null, null);
INSERT INTO `srp_erp_currencymaster` VALUES ('6', 'British Pound', 'GBP', '2', '0.61', '0', null, null, '', 'NATPC', '9090', 'NATPC', '9090', '2013-04-29 17:05:10', '2013-04-29 13:05:10');
INSERT INTO `srp_erp_currencymaster` VALUES ('7', 'Euro', 'EUR', '2', '0.51', '0', null, null, null, null, null, null, null, null, null);
INSERT INTO `srp_erp_currencymaster` VALUES ('8', 'Saudi Rial', 'SAR', '2', '0.1', '0', null, null, null, null, null, null, null, null, null);
INSERT INTO `srp_erp_currencymaster` VALUES ('9', 'Bahraini Dinar', 'BHD', '2', '0.9779', '0', null, null, null, null, null, null, null, null, null);
INSERT INTO `srp_erp_currencymaster` VALUES ('10', 'Canadian Dollar', 'CAD', '2', '0.37', '0', null, null, null, null, null, null, null, null, null);
INSERT INTO `srp_erp_currencymaster` VALUES ('11', 'Qatari Rial', 'QAR', '2', '1', '0', null, null, '', 'RISHAD-PC', '8888', 'RISHAD-PC', '8888', '2013-09-18 16:44:46', '2013-09-18 12:44:46');
INSERT INTO `srp_erp_currencymaster` VALUES ('12', 'Kuwaiti Dinar', 'KWD', '2', '1', '0', null, null, '', 'ZAHLANPC', '8888', 'ZAHLANPC', '8888', '2013-12-25 11:15:48', '2013-12-25 07:15:48');
INSERT INTO `srp_erp_currencymaster` VALUES ('13', 'Singapore Dollar', 'SGD', '2', '1', '0', null, null, '', 'ZAHLANPC', '8888', 'ZAHLANPC', '8888', '2013-12-26 10:40:55', '2013-12-26 06:40:55');
INSERT INTO `srp_erp_currencymaster` VALUES ('14', 'Sri Lankan Rupee', 'LKR', '2', '1', '0', null, null, '', 'ZAHLANPC', '8888', 'ZAHLANPC', '8888', '2014-09-15 10:58:43', '2014-09-15 06:58:43');
INSERT INTO `srp_erp_currencymaster` VALUES ('15', 'Indian Rupee', 'INR', '2', '1', '0', null, null, '', 'ZAHLANPC', '8888', 'ZAHLANPC', '8888', '2014-09-15 11:01:35', '2014-09-15 07:01:35');
INSERT INTO `srp_erp_currencymaster` VALUES ('16', 'Yemeni Rial', 'YER', '2', '1', '0', null, null, '', 'ZAHLANPC', '8888', 'ZAHLANPC', '8888', '2014-09-30 09:02:00', '2014-09-30 05:02:00');
INSERT INTO `srp_erp_currencymaster` VALUES ('17', 'Algerian Dinar', 'DZD', '2', '1', '1', null, null, '', 'ARSHAD-PC', '8888', 'ARSHAD-PC', '8888', '2015-04-07 14:45:44', '2015-04-07 10:45:44');
INSERT INTO `srp_erp_currencymaster` VALUES ('18', 'Australian Dollar', 'AUD', '2', '1', '1', null, null, null, null, null, null, null, null, null);
INSERT INTO `srp_erp_currencymaster` VALUES ('19', ' Maldivian rufiyaa', 'MVR', '2', '1', '0', null, null, null, null, null, null, null, null, null);
INSERT INTO `srp_erp_currencymaster` VALUES ('20', 'Japanese Yen', 'YEN', '2', '1', '0', null, null, null, null, null, null, null, null, null);
INSERT INTO `srp_erp_currencymaster` VALUES ('21', 'Kenyan Shilling', 'KES', '2', '1', null, null, null, null, null, null, null, null, null, null);

-- ----------------------------
-- Table structure for srp_erp_customerinvoicedetails
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_customerinvoicedetails`;
CREATE TABLE `srp_erp_customerinvoicedetails` (
  `invoiceDetailsAutoID` int(11) NOT NULL AUTO_INCREMENT,
  `invoiceAutoID` int(11) DEFAULT NULL,
  `tempinvoiceDetailID` int(11) DEFAULT NULL,
  `type` varchar(45) DEFAULT NULL COMMENT 'GL or Item',
  `contractAutoID` int(11) DEFAULT NULL,
  `contractDetailsAutoID` int(11) DEFAULT NULL,
  `contractCode` varchar(45) DEFAULT NULL,
  `projectID` int(11) DEFAULT NULL,
  `projectExchangeRate` double DEFAULT '1',
  `itemAutoID` int(11) DEFAULT NULL COMMENT 'autoID of added item',
  `itemSystemCode` varchar(45) DEFAULT NULL,
  `itemDescription` longtext,
  `itemCategory` varchar(50) DEFAULT NULL,
  `expenseGLAutoID` int(11) DEFAULT NULL COMMENT 'expenseGL of item',
  `expenseSystemGLCode` varchar(45) DEFAULT NULL,
  `expenseGLCode` varchar(45) DEFAULT NULL,
  `expenseGLDescription` varchar(255) DEFAULT NULL,
  `expenseGLType` varchar(5) DEFAULT NULL,
  `revenueGLAutoID` int(11) DEFAULT NULL COMMENT 'Revenue GL ID of added Item',
  `revenueGLCode` varchar(45) DEFAULT NULL,
  `revenueSystemGLCode` varchar(45) DEFAULT NULL,
  `revenueGLDescription` varchar(255) DEFAULT NULL,
  `revenueGLType` varchar(5) DEFAULT NULL,
  `assetGLAutoID` int(11) DEFAULT NULL COMMENT 'Asset GL Id of Added Item',
  `assetGLCode` varchar(45) DEFAULT NULL,
  `assetSystemGLCode` varchar(45) DEFAULT NULL,
  `assetGLDescription` longtext,
  `taxMasterAutoID` int(11) DEFAULT NULL,
  `taxPercentage` double DEFAULT '0' COMMENT 'Percentage of tax per item',
  `assetGLType` varchar(5) DEFAULT NULL,
  `wareHouseAutoID` int(11) NOT NULL DEFAULT '0',
  `wareHouseCode` varchar(45) DEFAULT NULL,
  `wareHouseLocation` varchar(100) DEFAULT NULL,
  `wareHouseDescription` varchar(255) DEFAULT NULL,
  `defaultUOMID` int(11) DEFAULT NULL,
  `defaultUOM` varchar(45) DEFAULT NULL,
  `unitOfMeasureID` int(11) DEFAULT NULL,
  `unitOfMeasure` varchar(45) DEFAULT NULL,
  `conversionRateUOM` double DEFAULT '0',
  `contractQty` double DEFAULT NULL,
  `contractAmount` double DEFAULT NULL,
  `requestedQty` double DEFAULT '0',
  `noOfItems` double DEFAULT NULL,
  `grossQty` double DEFAULT NULL,
  `noOfUnits` double DEFAULT NULL,
  `deduction` double DEFAULT NULL,
  `comment` longtext,
  `remarks` longtext,
  `description` longtext,
  `companyLocalWacAmount` double DEFAULT NULL,
  `unittransactionAmount` double DEFAULT NULL COMMENT 'amount per unit',
  `transactionAmount` double DEFAULT NULL COMMENT 'amount for total',
  `companyLocalAmount` double DEFAULT NULL,
  `companyReportingAmount` double DEFAULT NULL,
  `customerAmount` double DEFAULT NULL,
  `segmentID` int(11) DEFAULT NULL,
  `segmentCode` varchar(45) DEFAULT NULL,
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) DEFAULT NULL,
  `discountPercentage` double DEFAULT '0',
  `discountAmount` double DEFAULT '0',
  `taxDescription` varchar(255) DEFAULT NULL,
  `taxAmount` double DEFAULT NULL,
  `totalAfterTax` double DEFAULT '0' COMMENT 'total including tax',
  `taxShortCode` varchar(45) DEFAULT NULL,
  `taxSupplierAutoID` int(11) DEFAULT NULL COMMENT 'GLautoID of taxAutority',
  `taxSupplierSystemCode` varchar(45) DEFAULT NULL,
  `taxSupplierName` varchar(200) DEFAULT NULL,
  `taxSupplierliabilityAutoID` int(11) DEFAULT NULL,
  `taxSupplierliabilitySystemGLCode` varchar(45) DEFAULT NULL,
  `taxSupplierliabilityGLAccount` varchar(45) DEFAULT NULL,
  `taxSupplierliabilityDescription` varchar(255) DEFAULT NULL,
  `taxSupplierliabilityType` varchar(3) DEFAULT NULL,
  `taxSupplierCurrencyID` int(11) DEFAULT NULL,
  `taxSupplierCurrency` varchar(45) DEFAULT NULL COMMENT 'Default currency of supplier (tax Authority)',
  `taxSupplierCurrencyExchangeRate` double DEFAULT '0' COMMENT 'Exchange rate against transaction currency',
  `taxSupplierCurrencyAmount` double DEFAULT '0' COMMENT 'Transaction amount in supplier currency ',
  `taxSupplierCurrencyDecimalPlaces` int(1) DEFAULT '2' COMMENT 'Decimal places of Supplier currency',
  `createdUserGroup` int(11) DEFAULT NULL,
  `createdPCID` varchar(45) DEFAULT NULL,
  `createdUserID` varchar(45) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  `is_sync` int(1) DEFAULT '0',
  `id_store` int(255) NOT NULL DEFAULT '0',
  PRIMARY KEY (`invoiceDetailsAutoID`,`id_store`),
  KEY `invoiceAutoID` (`invoiceAutoID`) USING BTREE,
  KEY `contractAutoID` (`contractAutoID`) USING BTREE,
  KEY `contractDetailsAutoID` (`contractDetailsAutoID`) USING BTREE,
  KEY `type` (`type`) USING BTREE,
  KEY `projectID` (`projectID`) USING BTREE,
  KEY `itemAutoID` (`itemAutoID`) USING BTREE,
  KEY `expenseGLAutoID` (`expenseGLAutoID`) USING BTREE,
  KEY `revenueGLAutoID` (`revenueGLAutoID`) USING BTREE,
  KEY `assetGLAutoID` (`assetGLAutoID`) USING BTREE,
  KEY `taxMasterAutoID` (`taxMasterAutoID`) USING BTREE,
  KEY `taxSupplierAutoID` (`taxSupplierAutoID`) USING BTREE,
  KEY `taxSupplierliabilityAutoID` (`taxSupplierliabilityAutoID`) USING BTREE,
  KEY `invoiceDetailsAutoID` (`invoiceDetailsAutoID`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table Use : To record Item and GL details of customer invoices\r\nCreated By : Nusky Rauff \r\nDiscussed with : Mohamed Rishad,Mohamed Reyaas, Mohamed Hisham\r\nReviewed By : ';

-- ----------------------------
-- Records of srp_erp_customerinvoicedetails
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_customerinvoicedetails_sync
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_customerinvoicedetails_sync`;
CREATE TABLE `srp_erp_customerinvoicedetails_sync` (
  `invoiceDetailsAutoID` int(11) NOT NULL AUTO_INCREMENT,
  `invoiceAutoID` int(11) DEFAULT NULL,
  `tempinvoiceDetailID` int(11) DEFAULT NULL,
  `type` varchar(45) DEFAULT NULL COMMENT 'GL or Item',
  `contractAutoID` int(11) DEFAULT NULL,
  `contractDetailsAutoID` int(11) DEFAULT NULL,
  `contractCode` varchar(45) DEFAULT NULL,
  `projectID` int(11) DEFAULT NULL,
  `projectExchangeRate` double DEFAULT '1',
  `itemAutoID` int(11) DEFAULT NULL COMMENT 'autoID of added item',
  `itemSystemCode` varchar(45) DEFAULT NULL,
  `itemDescription` longtext,
  `itemCategory` varchar(50) DEFAULT NULL,
  `expenseGLAutoID` int(11) DEFAULT NULL COMMENT 'expenseGL of item',
  `expenseSystemGLCode` varchar(45) DEFAULT NULL,
  `expenseGLCode` varchar(45) DEFAULT NULL,
  `expenseGLDescription` varchar(255) DEFAULT NULL,
  `expenseGLType` varchar(5) DEFAULT NULL,
  `revenueGLAutoID` int(11) DEFAULT NULL COMMENT 'Revenue GL ID of added Item',
  `revenueGLCode` varchar(45) DEFAULT NULL,
  `revenueSystemGLCode` varchar(45) DEFAULT NULL,
  `revenueGLDescription` varchar(255) DEFAULT NULL,
  `revenueGLType` varchar(5) DEFAULT NULL,
  `assetGLAutoID` int(11) DEFAULT NULL COMMENT 'Asset GL Id of Added Item',
  `assetGLCode` varchar(45) DEFAULT NULL,
  `assetSystemGLCode` varchar(45) DEFAULT NULL,
  `assetGLDescription` longtext,
  `taxMasterAutoID` int(11) DEFAULT NULL,
  `taxPercentage` double DEFAULT '0' COMMENT 'Percentage of tax per item',
  `assetGLType` varchar(5) DEFAULT NULL,
  `wareHouseAutoID` int(11) NOT NULL DEFAULT '0',
  `wareHouseCode` varchar(45) DEFAULT NULL,
  `wareHouseLocation` varchar(100) DEFAULT NULL,
  `wareHouseDescription` varchar(255) DEFAULT NULL,
  `defaultUOMID` int(11) DEFAULT NULL,
  `defaultUOM` varchar(45) DEFAULT NULL,
  `unitOfMeasureID` int(11) DEFAULT NULL,
  `unitOfMeasure` varchar(45) DEFAULT NULL,
  `conversionRateUOM` double DEFAULT '0',
  `contractQty` double DEFAULT NULL,
  `contractAmount` double DEFAULT NULL,
  `requestedQty` double DEFAULT '0',
  `noOfItems` double DEFAULT NULL,
  `grossQty` double DEFAULT NULL,
  `noOfUnits` double DEFAULT NULL,
  `deduction` double DEFAULT NULL,
  `comment` longtext,
  `remarks` longtext,
  `description` longtext,
  `companyLocalWacAmount` double DEFAULT NULL,
  `unittransactionAmount` double DEFAULT NULL COMMENT 'amount per unit',
  `transactionAmount` double DEFAULT NULL COMMENT 'amount for total',
  `companyLocalAmount` double DEFAULT NULL,
  `companyReportingAmount` double DEFAULT NULL,
  `customerAmount` double DEFAULT NULL,
  `segmentID` int(11) DEFAULT NULL,
  `segmentCode` varchar(45) DEFAULT NULL,
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) DEFAULT NULL,
  `discountPercentage` double DEFAULT '0',
  `discountAmount` double DEFAULT '0',
  `taxDescription` varchar(255) DEFAULT NULL,
  `taxAmount` double DEFAULT NULL,
  `totalAfterTax` double DEFAULT '0' COMMENT 'total including tax',
  `taxShortCode` varchar(45) DEFAULT NULL,
  `taxSupplierAutoID` int(11) DEFAULT NULL COMMENT 'GLautoID of taxAutority',
  `taxSupplierSystemCode` varchar(45) DEFAULT NULL,
  `taxSupplierName` varchar(200) DEFAULT NULL,
  `taxSupplierliabilityAutoID` int(11) DEFAULT NULL,
  `taxSupplierliabilitySystemGLCode` varchar(45) DEFAULT NULL,
  `taxSupplierliabilityGLAccount` varchar(45) DEFAULT NULL,
  `taxSupplierliabilityDescription` varchar(255) DEFAULT NULL,
  `taxSupplierliabilityType` varchar(3) DEFAULT NULL,
  `taxSupplierCurrencyID` int(11) DEFAULT NULL,
  `taxSupplierCurrency` varchar(45) DEFAULT NULL COMMENT 'Default currency of supplier (tax Authority)',
  `taxSupplierCurrencyExchangeRate` double DEFAULT '0' COMMENT 'Exchange rate against transaction currency',
  `taxSupplierCurrencyAmount` double DEFAULT '0' COMMENT 'Transaction amount in supplier currency ',
  `taxSupplierCurrencyDecimalPlaces` int(1) DEFAULT '2' COMMENT 'Decimal places of Supplier currency',
  `createdUserGroup` int(11) DEFAULT NULL,
  `createdPCID` varchar(45) DEFAULT NULL,
  `createdUserID` varchar(45) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  `is_sync` int(1) DEFAULT '0',
  `id_store` int(255) DEFAULT '0',
  PRIMARY KEY (`invoiceDetailsAutoID`,`wareHouseAutoID`),
  KEY `invoiceAutoID` (`invoiceAutoID`) USING BTREE,
  KEY `contractAutoID` (`contractAutoID`) USING BTREE,
  KEY `contractDetailsAutoID` (`contractDetailsAutoID`) USING BTREE,
  KEY `type` (`type`) USING BTREE,
  KEY `projectID` (`projectID`) USING BTREE,
  KEY `itemAutoID` (`itemAutoID`) USING BTREE,
  KEY `expenseGLAutoID` (`expenseGLAutoID`) USING BTREE,
  KEY `revenueGLAutoID` (`revenueGLAutoID`) USING BTREE,
  KEY `assetGLAutoID` (`assetGLAutoID`) USING BTREE,
  KEY `taxMasterAutoID` (`taxMasterAutoID`) USING BTREE,
  KEY `taxSupplierAutoID` (`taxSupplierAutoID`) USING BTREE,
  KEY `taxSupplierliabilityAutoID` (`taxSupplierliabilityAutoID`) USING BTREE,
  KEY `invoiceDetailsAutoID` (`invoiceDetailsAutoID`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table Use : To record Item and GL details of customer invoices\r\nCreated By : Nusky Rauff \r\nDiscussed with : Mohamed Rishad,Mohamed Reyaas, Mohamed Hisham\r\nReviewed By : ';

-- ----------------------------
-- Records of srp_erp_customerinvoicedetails_sync
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_customerinvoicemaster
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_customerinvoicemaster`;
CREATE TABLE `srp_erp_customerinvoicemaster` (
  `invoiceAutoID` int(11) NOT NULL AUTO_INCREMENT,
  `wareHouseAutoID` int(11) NOT NULL DEFAULT '0',
  `invoiceType` varchar(12) DEFAULT NULL,
  `documentID` varchar(45) DEFAULT 'CINV' COMMENT 'document short code',
  `rrvrID` int(11) DEFAULT NULL,
  `posTypeID` int(1) DEFAULT '0' COMMENT '0 - Direct invoice 1 generated from general POS 2 - Generated from Restaurant POS',
  `posMasterAutoID` int(11) DEFAULT NULL COMMENT 'if ',
  `invoiceDate` date DEFAULT NULL,
  `invoiceDueDate` date DEFAULT NULL,
  `customerInvoiceDate` date DEFAULT NULL,
  `invoiceCode` varchar(45) DEFAULT '0' COMMENT 'documentsystemcode',
  `referenceNo` varchar(45) DEFAULT NULL,
  `invoiceNarration` longtext,
  `invoiceNote` longtext,
  `salesPersonID` int(11) DEFAULT NULL,
  `SalesPersonCode` varchar(45) DEFAULT NULL,
  `bankGLAutoID` int(11) DEFAULT NULL,
  `bankSystemAccountCode` varchar(45) DEFAULT NULL,
  `bankGLSecondaryCode` varchar(45) DEFAULT NULL,
  `bankCurrencyID` int(11) DEFAULT NULL,
  `bankCurrency` varchar(5) DEFAULT NULL,
  `invoicebank` varchar(100) DEFAULT NULL,
  `invoicebankBranch` varchar(50) DEFAULT NULL,
  `invoicebankSwiftCode` varchar(45) DEFAULT NULL,
  `invoicebankAccount` varchar(45) DEFAULT NULL,
  `invoicebankType` varchar(5) DEFAULT NULL,
  `companyFinanceYearID` int(11) DEFAULT NULL,
  `companyFinanceYear` varchar(100) DEFAULT NULL,
  `FYBegin` date DEFAULT NULL,
  `FYEnd` date DEFAULT NULL,
  `FYPeriodDateFrom` date DEFAULT NULL,
  `FYPeriodDateTo` date DEFAULT NULL,
  `companyFinancePeriodID` int(11) DEFAULT NULL,
  `contactPersonName` varchar(500) DEFAULT NULL,
  `contactPersonNumber` varchar(20) DEFAULT NULL,
  `customerID` int(11) DEFAULT NULL,
  `customerSystemCode` varchar(45) DEFAULT NULL,
  `customerName` varchar(500) DEFAULT NULL,
  `customerAddress` longtext,
  `customerTelephone` varchar(15) DEFAULT NULL,
  `customerFax` varchar(15) DEFAULT NULL,
  `customerEmail` varchar(50) DEFAULT NULL,
  `customerReceivableAutoID` int(11) DEFAULT NULL,
  `customerReceivableSystemGLCode` varchar(45) DEFAULT NULL,
  `customerReceivableGLAccount` varchar(45) DEFAULT NULL,
  `customerReceivableDescription` varchar(255) DEFAULT NULL,
  `customerReceivableType` varchar(5) DEFAULT NULL,
  `deliveryNoteSystemCode` varchar(45) DEFAULT NULL COMMENT 'Delivery note system code',
  `isPrintDN` int(1) DEFAULT '0' COMMENT 'Print Delivery note with invoice',
  `transactionCurrencyID` int(11) NOT NULL,
  `transactionCurrency` varchar(45) DEFAULT NULL COMMENT 'Document transaction currency',
  `transactionExchangeRate` double DEFAULT '0' COMMENT 'Always 1',
  `transactionAmount` double DEFAULT '0' COMMENT 'Amount of transaction in document',
  `transactionCurrencyDecimalPlaces` int(1) DEFAULT '2' COMMENT 'Decimal places of transaction currency ',
  `companyLocalCurrencyID` int(11) NOT NULL,
  `companyLocalCurrency` varchar(45) DEFAULT NULL COMMENT 'Local currency of company in company master',
  `companyLocalExchangeRate` double DEFAULT '0' COMMENT 'Exchange rate against transaction currency',
  `companyLocalAmount` double DEFAULT '0' COMMENT 'Transaction amount in local currency',
  `companyLocalCurrencyDecimalPlaces` int(1) DEFAULT '2' COMMENT 'Decimal places of company currency',
  `companyReportingCurrencyID` int(11) NOT NULL,
  `companyReportingCurrency` varchar(45) DEFAULT NULL COMMENT 'Reporting currency of company in company master',
  `companyReportingExchangeRate` double DEFAULT '0' COMMENT 'Exchange rate against transaction currency ',
  `companyReportingAmount` double DEFAULT '0' COMMENT '1-Payment Invoice, 4- Direct Payment',
  `companyReportingCurrencyDecimalPlaces` int(1) DEFAULT '2' COMMENT 'Decimal places of company currency',
  `customerCurrencyID` int(11) NOT NULL,
  `customerCurrency` varchar(45) DEFAULT NULL COMMENT 'Default currency of supplier ',
  `customerCurrencyExchangeRate` double DEFAULT '0' COMMENT 'Exchange rate against transaction currency',
  `customerCurrencyAmount` double DEFAULT '0' COMMENT 'Transaction amount in supplier currency ',
  `customerCurrencyDecimalPlaces` int(1) DEFAULT '2' COMMENT 'Decimal places of Supplier currency',
  `receiptInvoiceYN` int(1) DEFAULT '0' COMMENT 'if receipt created update 1',
  `receiptTotalAmount` double DEFAULT '0' COMMENT 'total amount of created receipt for the invoice',
  `creditNoteTotalAmount` double DEFAULT '0' COMMENT 'total matched amount of credit note',
  `advanceMatchedTotal` double DEFAULT '0',
  `isDeleted` int(2) DEFAULT '0',
  `deletedEmpID` int(10) DEFAULT NULL,
  `deletedDate` datetime(6) DEFAULT NULL,
  `confirmedYN` int(1) DEFAULT '0',
  `confirmedByEmpID` varchar(100) DEFAULT NULL,
  `confirmedByName` varchar(500) DEFAULT NULL,
  `confirmedDate` datetime DEFAULT NULL,
  `approvedYN` int(1) DEFAULT '0',
  `approvedDate` datetime DEFAULT NULL,
  `currentLevelNo` int(1) DEFAULT '1',
  `approvedbyEmpID` varchar(45) DEFAULT NULL,
  `approvedbyEmpName` varchar(500) DEFAULT NULL,
  `segmentID` int(11) DEFAULT NULL,
  `segmentCode` varchar(45) DEFAULT NULL,
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) DEFAULT NULL,
  `createdUserGroup` int(11) DEFAULT NULL,
  `createdPCID` varchar(45) DEFAULT NULL,
  `createdUserID` varchar(45) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `is_sync` int(1) DEFAULT '0',
  `id_store` int(255) NOT NULL DEFAULT '0',
  PRIMARY KEY (`invoiceAutoID`,`id_store`),
  KEY `invoiceAutoID_UNIQUE` (`invoiceAutoID`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of srp_erp_customerinvoicemaster
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_customerinvoicemaster_sync
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_customerinvoicemaster_sync`;
CREATE TABLE `srp_erp_customerinvoicemaster_sync` (
  `invoiceAutoID` int(11) NOT NULL AUTO_INCREMENT,
  `wareHouseAutoID` int(11) NOT NULL,
  `invoiceType` varchar(15) DEFAULT NULL,
  `documentID` varchar(45) DEFAULT 'CINV' COMMENT 'document short code',
  `rrvrID` int(11) DEFAULT NULL,
  `posTypeID` int(1) DEFAULT '0' COMMENT '0 - Direct invoice 1 generated from general POS 2 - Generated from Restaurant POS',
  `posMasterAutoID` int(11) DEFAULT NULL COMMENT 'if ',
  `invoiceDate` date DEFAULT NULL,
  `invoiceDueDate` date DEFAULT NULL,
  `customerInvoiceDate` date DEFAULT NULL,
  `invoiceCode` varchar(45) DEFAULT '0' COMMENT 'documentsystemcode',
  `referenceNo` varchar(45) DEFAULT NULL,
  `invoiceNarration` longtext,
  `invoiceNote` longtext,
  `salesPersonID` int(11) DEFAULT NULL,
  `SalesPersonCode` varchar(45) DEFAULT NULL,
  `bankGLAutoID` int(11) DEFAULT NULL,
  `bankSystemAccountCode` varchar(45) DEFAULT NULL,
  `bankGLSecondaryCode` varchar(45) DEFAULT NULL,
  `bankCurrencyID` int(11) DEFAULT NULL,
  `bankCurrency` varchar(5) DEFAULT NULL,
  `invoicebank` varchar(100) DEFAULT NULL,
  `invoicebankBranch` varchar(50) DEFAULT NULL,
  `invoicebankSwiftCode` varchar(45) DEFAULT NULL,
  `invoicebankAccount` varchar(45) DEFAULT NULL,
  `invoicebankType` varchar(5) DEFAULT NULL,
  `companyFinanceYearID` int(11) DEFAULT NULL,
  `companyFinanceYear` varchar(100) DEFAULT NULL,
  `FYBegin` date DEFAULT NULL,
  `FYEnd` date DEFAULT NULL,
  `FYPeriodDateFrom` date DEFAULT NULL,
  `FYPeriodDateTo` date DEFAULT NULL,
  `companyFinancePeriodID` int(11) DEFAULT NULL,
  `contactPersonName` varchar(500) DEFAULT NULL,
  `contactPersonNumber` varchar(20) DEFAULT NULL,
  `customerID` int(11) DEFAULT NULL,
  `customerSystemCode` varchar(45) DEFAULT NULL,
  `customerName` varchar(500) DEFAULT NULL,
  `customerAddress` longtext,
  `customerTelephone` varchar(15) DEFAULT NULL,
  `customerFax` varchar(15) DEFAULT NULL,
  `customerEmail` varchar(50) DEFAULT NULL,
  `customerReceivableAutoID` int(11) DEFAULT NULL,
  `customerReceivableSystemGLCode` varchar(45) DEFAULT NULL,
  `customerReceivableGLAccount` varchar(45) DEFAULT NULL,
  `customerReceivableDescription` varchar(255) DEFAULT NULL,
  `customerReceivableType` varchar(5) DEFAULT NULL,
  `deliveryNoteSystemCode` varchar(45) DEFAULT NULL COMMENT 'Delivery note system code',
  `isPrintDN` int(1) DEFAULT '0' COMMENT 'Print Delivery note with invoice',
  `transactionCurrencyID` int(11) DEFAULT NULL,
  `transactionCurrency` varchar(45) DEFAULT NULL COMMENT 'Document transaction currency',
  `transactionExchangeRate` double DEFAULT '0' COMMENT 'Always 1',
  `transactionAmount` double DEFAULT '0' COMMENT 'Amount of transaction in document',
  `transactionCurrencyDecimalPlaces` int(1) DEFAULT '2' COMMENT 'Decimal places of transaction currency ',
  `companyLocalCurrencyID` int(11) DEFAULT NULL,
  `companyLocalCurrency` varchar(45) DEFAULT NULL COMMENT 'Local currency of company in company master',
  `companyLocalExchangeRate` double DEFAULT '0' COMMENT 'Exchange rate against transaction currency',
  `companyLocalAmount` double DEFAULT '0' COMMENT 'Transaction amount in local currency',
  `companyLocalCurrencyDecimalPlaces` int(1) DEFAULT '2' COMMENT 'Decimal places of company currency',
  `companyReportingCurrencyID` int(11) DEFAULT NULL,
  `companyReportingCurrency` varchar(45) DEFAULT NULL COMMENT 'Reporting currency of company in company master',
  `companyReportingExchangeRate` double DEFAULT '0' COMMENT 'Exchange rate against transaction currency ',
  `companyReportingAmount` double DEFAULT '0' COMMENT '1-Payment Invoice, 4- Direct Payment',
  `companyReportingCurrencyDecimalPlaces` int(1) DEFAULT '2' COMMENT 'Decimal places of company currency',
  `customerCurrencyID` int(11) DEFAULT NULL,
  `customerCurrency` varchar(45) DEFAULT NULL COMMENT 'Default currency of supplier ',
  `customerCurrencyExchangeRate` double DEFAULT '0' COMMENT 'Exchange rate against transaction currency',
  `customerCurrencyAmount` double DEFAULT '0' COMMENT 'Transaction amount in supplier currency ',
  `customerCurrencyDecimalPlaces` int(1) DEFAULT '2' COMMENT 'Decimal places of Supplier currency',
  `receiptInvoiceYN` int(1) DEFAULT '0' COMMENT 'if receipt created update 1',
  `tempInvoiceID` int(255) DEFAULT NULL,
  `receiptTotalAmount` double DEFAULT '0' COMMENT 'total amount of created receipt for the invoice',
  `creditNoteTotalAmount` double DEFAULT '0' COMMENT 'total matched amount of credit note',
  `advanceMatchedTotal` double DEFAULT '0',
  `isDeleted` int(2) DEFAULT '0',
  `deletedEmpID` int(10) DEFAULT NULL,
  `deletedDate` datetime(6) DEFAULT NULL,
  `showTaxSummaryYN` int(1) DEFAULT '0',
  `confirmedYN` int(1) DEFAULT '0',
  `confirmedByEmpID` varchar(100) DEFAULT NULL,
  `confirmedByName` varchar(500) DEFAULT NULL,
  `confirmedDate` datetime DEFAULT NULL,
  `approvedYN` int(1) DEFAULT '0',
  `approvedDate` datetime DEFAULT NULL,
  `currentLevelNo` int(1) DEFAULT '1',
  `approvedbyEmpID` varchar(45) DEFAULT NULL,
  `approvedbyEmpName` varchar(500) DEFAULT NULL,
  `segmentID` int(11) DEFAULT NULL,
  `segmentCode` varchar(45) DEFAULT NULL,
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) DEFAULT NULL,
  `createdUserGroup` int(11) DEFAULT NULL,
  `createdPCID` varchar(45) DEFAULT NULL,
  `createdUserID` varchar(45) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `is_sync` int(1) DEFAULT '0',
  `id_store` int(255) DEFAULT '0',
  PRIMARY KEY (`invoiceAutoID`,`wareHouseAutoID`),
  KEY `invoiceType` (`invoiceType`) USING BTREE,
  KEY `rrvrID` (`rrvrID`) USING BTREE,
  KEY `posTypeID` (`posTypeID`) USING BTREE,
  KEY `invoiceDate` (`invoiceDate`) USING BTREE,
  KEY `invoiceDueDate` (`invoiceDueDate`) USING BTREE,
  KEY `customerInvoiceDate` (`customerInvoiceDate`) USING BTREE,
  KEY `salesPersonID` (`salesPersonID`) USING BTREE,
  KEY `bankGLAutoID` (`bankGLAutoID`) USING BTREE,
  KEY `bankCurrencyID` (`bankCurrencyID`) USING BTREE,
  KEY `companyFinanceYearID` (`companyFinanceYearID`) USING BTREE,
  KEY `companyFinancePeriodID` (`companyFinancePeriodID`) USING BTREE,
  KEY `customerID` (`customerID`) USING BTREE,
  KEY `customerReceivableAutoID` (`customerReceivableAutoID`) USING BTREE,
  KEY `isPrintDN` (`isPrintDN`) USING BTREE,
  KEY `confirmedYN` (`confirmedYN`) USING BTREE,
  KEY `isDeleted` (`isDeleted`) USING BTREE,
  KEY `approvedYN` (`approvedYN`) USING BTREE,
  KEY `segmentID` (`segmentID`) USING BTREE,
  KEY `companyID` (`companyID`) USING BTREE,
  KEY `segmentCode` (`segmentCode`) USING BTREE,
  KEY `companyCode` (`companyCode`) USING BTREE,
  KEY `invoiceAutoID_UNIQUE` (`invoiceAutoID`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table Use :Master table for customer invoices\r\nCreated By : Nusky Rauff \r\nDiscussed with : Mohamed Rishad,Mohamed Reyaas, Mohamed Hisham\r\nReviewed By : ';

-- ----------------------------
-- Records of srp_erp_customerinvoicemaster_sync
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_customermaster
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_customermaster`;
CREATE TABLE `srp_erp_customermaster` (
  `customerAutoID` int(11) NOT NULL AUTO_INCREMENT,
  `customerSystemCode` varchar(20) DEFAULT NULL,
  `customerName` varchar(200) DEFAULT NULL,
  `customerNameOtherLang` varchar(255) DEFAULT NULL,
  `receivableAutoID` int(11) DEFAULT NULL,
  `receivableSystemGLCode` varchar(45) DEFAULT NULL,
  `receivableGLAccount` varchar(45) DEFAULT NULL,
  `receivableDescription` varchar(255) DEFAULT NULL,
  `receivableType` varchar(5) DEFAULT NULL,
  `partyCategoryID` int(11) DEFAULT NULL COMMENT 'to maintain party Category',
  `customerAddress1` varchar(255) DEFAULT NULL,
  `customerAddress1OtherLang` varchar(255) DEFAULT NULL,
  `customerAddress2` varchar(255) DEFAULT NULL,
  `customerAddress2OtherLang` varchar(255) DEFAULT NULL,
  `customerCountry` varchar(100) DEFAULT NULL,
  `customerCountryOtherLang` varchar(255) DEFAULT NULL,
  `customerTelephone` varchar(100) DEFAULT NULL,
  `customerEmail` varchar(50) DEFAULT NULL,
  `customerUrl` varchar(100) DEFAULT NULL,
  `customerFax` varchar(20) DEFAULT NULL,
  `secondaryCode` varchar(20) DEFAULT NULL COMMENT 'VAT Identification No',
  `secondaryCodeOtherLang` varchar(255) DEFAULT NULL,
  `customerCurrencyID` int(11) NOT NULL,
  `customerCurrency` varchar(45) DEFAULT NULL,
  `customerCurrencyDecimalPlaces` int(1) DEFAULT NULL,
  `customerCreditPeriod` int(3) DEFAULT NULL,
  `customerCreditLimit` double DEFAULT NULL,
  `taxGroupID` int(11) DEFAULT '0',
  `vatIdNo` varchar(30) DEFAULT NULL,
  `communityMemberID` int(11) DEFAULT NULL,
  `isActive` int(1) DEFAULT '1',
  `capAmount` double DEFAULT '0',
  `finCompanyPercentage` double DEFAULT '100',
  `pvtCompanyPercentage` double DEFAULT '0',
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) DEFAULT NULL,
  `createdUserGroup` int(11) DEFAULT NULL,
  `createdPCID` varchar(45) DEFAULT NULL,
  `createdUserID` varchar(45) DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`customerAutoID`),
  UNIQUE KEY `customerAutoID` (`customerAutoID`) USING BTREE,
  KEY `customerName` (`customerName`) USING BTREE,
  KEY `customerSystemCode` (`customerSystemCode`) USING BTREE,
  KEY `receivableAutoID` (`receivableAutoID`) USING BTREE,
  KEY `partyCategoryID` (`partyCategoryID`) USING BTREE,
  KEY `customerCountry` (`customerCountry`) USING BTREE,
  KEY `secondaryCode` (`secondaryCode`) USING BTREE,
  KEY `customerCurrencyID` (`customerCurrencyID`) USING BTREE,
  KEY `customerCurrency` (`customerCurrency`) USING BTREE,
  KEY `customerCreditPeriod` (`customerCreditPeriod`) USING BTREE,
  KEY `customerCreditLimit` (`customerCreditLimit`) USING BTREE,
  KEY `taxGroupID` (`taxGroupID`) USING BTREE,
  KEY `vatIdNo` (`vatIdNo`) USING BTREE,
  KEY `isActive` (`isActive`) USING BTREE,
  KEY `companyID` (`companyID`) USING BTREE,
  KEY `companyCode` (`companyCode`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of srp_erp_customermaster
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_customertypemaster
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_customertypemaster`;
CREATE TABLE `srp_erp_customertypemaster` (
  `customerTypeID` int(11) NOT NULL AUTO_INCREMENT,
  `customerDescription` varchar(255) DEFAULT NULL,
  `isDefault` int(1) DEFAULT '0' COMMENT 'drop down option, 1 = > default selected value',
  `company_id` int(11) DEFAULT NULL COMMENT 'FK of company_id  company table ',
  `createdBy` varchar(255) DEFAULT NULL,
  `createdDatetime` datetime DEFAULT NULL,
  `createdPc` varchar(255) DEFAULT NULL,
  `timestamp` datetime DEFAULT NULL,
  PRIMARY KEY (`customerTypeID`),
  UNIQUE KEY `customerTypeID` (`customerTypeID`) USING BTREE,
  KEY `isDefault` (`isDefault`) USING BTREE,
  KEY `company_id` (`company_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of srp_erp_customertypemaster
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_documentapproved
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_documentapproved`;
CREATE TABLE `srp_erp_documentapproved` (
  `documentApprovedID` int(11) NOT NULL AUTO_INCREMENT,
  `wareHouseAutoID` int(11) NOT NULL DEFAULT '0',
  `departmentID` varchar(45) DEFAULT NULL,
  `documentID` varchar(45) DEFAULT NULL,
  `documentSystemCode` int(11) DEFAULT NULL,
  `documentCode` varchar(100) DEFAULT NULL,
  `isCancel` int(1) DEFAULT '0' COMMENT 'Approvals for canceling a document',
  `documentDate` datetime DEFAULT NULL,
  `approvalLevelID` int(11) DEFAULT NULL,
  `isReverseApplicableYN` int(1) DEFAULT '1' COMMENT '0 - No 1- Yes',
  `roleID` int(11) DEFAULT NULL,
  `approvalGroupID` int(11) DEFAULT NULL,
  `roleLevelOrder` int(1) DEFAULT NULL,
  `docConfirmedDate` datetime DEFAULT NULL,
  `docConfirmedByEmpID` varchar(45) DEFAULT NULL,
  `table_name` varchar(150) DEFAULT NULL,
  `table_unique_field_name` varchar(150) DEFAULT NULL,
  `approvedEmpID` varchar(45) DEFAULT NULL,
  `approvedYN` int(1) DEFAULT '0',
  `approvedDate` datetime DEFAULT NULL,
  `approvedComments` longtext,
  `approvedPC` varchar(100) DEFAULT NULL,
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) DEFAULT NULL,
  `timeStamp` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `is_sync` int(1) DEFAULT '0',
  `id_store` int(255) NOT NULL DEFAULT '0',
  PRIMARY KEY (`documentApprovedID`,`id_store`),
  KEY `departmentID` (`departmentID`) USING BTREE,
  KEY `documentID` (`documentID`) USING BTREE,
  KEY `documentSystemCode` (`documentSystemCode`) USING BTREE,
  KEY `isCancel` (`isCancel`) USING BTREE,
  KEY `approvalLevelID` (`approvalLevelID`) USING BTREE,
  KEY `isReverseApplicableYN` (`isReverseApplicableYN`) USING BTREE,
  KEY `roleID` (`roleID`) USING BTREE,
  KEY `approvalGroupID` (`approvalGroupID`) USING BTREE,
  KEY `docConfirmedByEmpID` (`docConfirmedByEmpID`) USING BTREE,
  KEY `table_name` (`table_name`) USING BTREE,
  KEY `table_unique_field_name` (`table_unique_field_name`) USING BTREE,
  KEY `approvedEmpID` (`approvedEmpID`) USING BTREE,
  KEY `approvedYN` (`approvedYN`) USING BTREE,
  KEY `companyID` (`companyID`) USING BTREE,
  KEY `companyCode` (`companyCode`) USING BTREE,
  KEY `documentApprovedID` (`documentApprovedID`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table Use : when a document is confirmed approval record will be inserted to this table if approval user is exist for particular document\r\nCreated By : Nusky Rauff\r\nDiscussed with : Mohamed Rishad,Mohamed Reyaas, Mohamed Hisham\r\nReviewed By : ';

-- ----------------------------
-- Records of srp_erp_documentapproved
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_documentapproved_sync
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_documentapproved_sync`;
CREATE TABLE `srp_erp_documentapproved_sync` (
  `documentApprovedID` int(11) NOT NULL AUTO_INCREMENT,
  `wareHouseAutoID` int(11) NOT NULL DEFAULT '0',
  `departmentID` varchar(45) DEFAULT NULL,
  `documentID` varchar(45) DEFAULT NULL,
  `documentSystemCode` int(11) DEFAULT NULL,
  `documentCode` varchar(100) DEFAULT NULL,
  `isCancel` int(1) DEFAULT '0' COMMENT 'Approvals for canceling a document',
  `documentDate` datetime DEFAULT NULL,
  `approvalLevelID` int(11) DEFAULT NULL,
  `isReverseApplicableYN` int(1) DEFAULT '1' COMMENT '0 - No 1- Yes',
  `roleID` int(11) DEFAULT NULL,
  `approvalGroupID` int(11) DEFAULT NULL,
  `roleLevelOrder` int(1) DEFAULT NULL,
  `docConfirmedDate` datetime DEFAULT NULL,
  `docConfirmedByEmpID` varchar(45) DEFAULT NULL,
  `table_name` varchar(150) DEFAULT NULL,
  `table_unique_field_name` varchar(150) DEFAULT NULL,
  `approvedEmpID` varchar(45) DEFAULT NULL,
  `approvedYN` int(1) DEFAULT '0',
  `approvedDate` datetime DEFAULT NULL,
  `approvedComments` longtext,
  `approvedPC` varchar(100) DEFAULT NULL,
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) DEFAULT NULL,
  `timeStamp` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `is_sync` int(1) DEFAULT '0',
  `id_store` int(255) DEFAULT '0',
  PRIMARY KEY (`documentApprovedID`,`wareHouseAutoID`),
  KEY `departmentID` (`departmentID`) USING BTREE,
  KEY `documentID` (`documentID`) USING BTREE,
  KEY `documentSystemCode` (`documentSystemCode`) USING BTREE,
  KEY `isCancel` (`isCancel`) USING BTREE,
  KEY `approvalLevelID` (`approvalLevelID`) USING BTREE,
  KEY `isReverseApplicableYN` (`isReverseApplicableYN`) USING BTREE,
  KEY `roleID` (`roleID`) USING BTREE,
  KEY `approvalGroupID` (`approvalGroupID`) USING BTREE,
  KEY `docConfirmedByEmpID` (`docConfirmedByEmpID`) USING BTREE,
  KEY `table_name` (`table_name`) USING BTREE,
  KEY `table_unique_field_name` (`table_unique_field_name`) USING BTREE,
  KEY `approvedEmpID` (`approvedEmpID`) USING BTREE,
  KEY `approvedYN` (`approvedYN`) USING BTREE,
  KEY `companyID` (`companyID`) USING BTREE,
  KEY `companyCode` (`companyCode`) USING BTREE,
  KEY `documentApprovedID` (`documentApprovedID`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table Use : when a document is confirmed approval record will be inserted to this table if approval user is exist for particular document\r\nCreated By : Nusky Rauff\r\nDiscussed with : Mohamed Rishad,Mohamed Reyaas, Mohamed Hisham\r\nReviewed By : ';

-- ----------------------------
-- Records of srp_erp_documentapproved_sync
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_documentcodemaster
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_documentcodemaster`;
CREATE TABLE `srp_erp_documentcodemaster` (
  `codeID` int(11) NOT NULL AUTO_INCREMENT,
  `documentID` varchar(45) NOT NULL,
  `document` varchar(100) DEFAULT NULL,
  `prefix` varchar(45) NOT NULL,
  `startSerialNo` int(11) DEFAULT NULL,
  `serialNo` int(11) NOT NULL,
  `formatLength` int(2) NOT NULL,
  `approvalLevel` int(2) DEFAULT NULL,
  `approvalSignatureLevel` int(1) DEFAULT '0',
  `format_1` varchar(10) DEFAULT NULL,
  `format_2` varchar(10) DEFAULT NULL,
  `format_3` varchar(10) DEFAULT NULL,
  `format_4` varchar(10) DEFAULT NULL,
  `format_5` varchar(10) DEFAULT NULL,
  `format_6` varchar(10) DEFAULT NULL,
  `isPushNotifyEnabled` int(1) DEFAULT '0',
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) DEFAULT NULL,
  `createdUserGroup` int(11) DEFAULT NULL,
  `createdPCID` varchar(45) DEFAULT NULL,
  `createdUserID` varchar(45) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`codeID`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='Table Use :use to generate the codes for all documents \r\nCreated By : Nusky Rauff\r\nDiscussed with : Mohamed Rishad,Mohamed Reyaas, Mohamed Hisham\r\nReviewed By : ';

-- ----------------------------
-- Records of srp_erp_documentcodemaster
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_generalledger
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_generalledger`;
CREATE TABLE `srp_erp_generalledger` (
  `generalLedgerAutoID` int(11) NOT NULL AUTO_INCREMENT,
  `wareHouseAutoID` int(11) NOT NULL DEFAULT '0',
  `documentCode` varchar(20) DEFAULT NULL,
  `documentMasterAutoID` int(11) DEFAULT NULL,
  `documentDetailAutoID` int(11) DEFAULT NULL,
  `documentSystemCode` varchar(45) DEFAULT '0',
  `documentType` varchar(50) DEFAULT NULL,
  `documentDate` date DEFAULT NULL,
  `documentYear` int(4) DEFAULT NULL,
  `documentMonth` int(2) DEFAULT NULL,
  `projectID` int(11) DEFAULT NULL,
  `projectExchangeRate` double DEFAULT '1',
  `documentNarration` text,
  `chequeNumber` varchar(50) DEFAULT NULL,
  `GLAutoID` int(11) DEFAULT NULL,
  `systemGLCode` varchar(255) DEFAULT NULL,
  `GLCode` varchar(45) DEFAULT '0',
  `GLDescription` varchar(255) DEFAULT NULL,
  `GLType` varchar(3) DEFAULT NULL,
  `amount_type` varchar(2) DEFAULT NULL,
  `isFromItem` tinyint(1) DEFAULT '0' COMMENT 'if revenue or expense is from item then 1 else 0',
  `transactionCurrencyID` int(11) NOT NULL,
  `transactionCurrency` varchar(45) DEFAULT NULL COMMENT 'Document transaction currency',
  `transactionExchangeRate` double DEFAULT '0' COMMENT 'Always 1',
  `transactionAmount` double DEFAULT '0' COMMENT 'Amount of transaction in document',
  `transactionCurrencyDecimalPlaces` int(1) DEFAULT NULL COMMENT 'Decimal places of transaction currency ',
  `companyLocalCurrencyID` int(11) NOT NULL,
  `companyLocalCurrency` varchar(45) DEFAULT NULL COMMENT 'Local currency of company in company master',
  `companyLocalExchangeRate` double DEFAULT '0' COMMENT 'Exchange rate against transaction currency',
  `companyLocalAmount` double DEFAULT '0' COMMENT 'Transaction amount in local currency',
  `companyLocalCurrencyDecimalPlaces` int(1) DEFAULT NULL COMMENT 'Decimal places of company currency',
  `companyReportingCurrencyID` int(11) NOT NULL,
  `companyReportingCurrency` varchar(45) DEFAULT NULL COMMENT 'Reporting currency of company in company master',
  `companyReportingExchangeRate` double DEFAULT '0' COMMENT 'Exchange rate against transaction currency ',
  `companyReportingAmount` double DEFAULT '0' COMMENT '1-Payment Invoice, 4- Direct Payment',
  `companyReportingCurrencyDecimalPlaces` int(1) DEFAULT NULL COMMENT 'Decimal places of company currency',
  `partyContractID` varchar(45) DEFAULT '0',
  `partyType` varchar(4) DEFAULT NULL,
  `partyAutoID` int(11) DEFAULT NULL,
  `partySystemCode` varchar(255) DEFAULT NULL,
  `partyName` varchar(255) DEFAULT NULL,
  `partyCurrencyID` int(11) DEFAULT NULL,
  `partyCurrency` varchar(45) DEFAULT NULL COMMENT 'Default currency of supplier ',
  `partyExchangeRate` double DEFAULT '0' COMMENT 'Exchange rate against transaction currency',
  `partyCurrencyAmount` double DEFAULT '0' COMMENT 'Transaction amount in supplier currency ',
  `partyCurrencyDecimalPlaces` int(1) DEFAULT NULL COMMENT 'Decimal places of Supplier currency',
  `subLedgerType` tinyint(1) DEFAULT NULL COMMENT 'Unbilled =1,Ap=2,AR=3, SC=4',
  `subLedgerDesc` varchar(5) DEFAULT NULL,
  `isAddon` int(1) DEFAULT '0',
  `confirmedByEmpID` varchar(100) DEFAULT NULL,
  `confirmedByName` varchar(300) DEFAULT NULL,
  `confirmedDate` datetime DEFAULT NULL,
  `approvedDate` datetime DEFAULT NULL,
  `approvedbyEmpID` varchar(45) DEFAULT NULL,
  `approvedbyEmpName` varchar(200) DEFAULT NULL,
  `segmentID` int(11) DEFAULT NULL,
  `segmentCode` varchar(45) DEFAULT NULL,
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) DEFAULT NULL,
  `createdUserGroup` int(11) DEFAULT NULL,
  `createdPCID` varchar(45) DEFAULT NULL,
  `createdUserID` varchar(45) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  `OtherFeesID` int(11) DEFAULT NULL,
  `taxMasterAutoID` int(11) DEFAULT NULL,
  `partyVatIdNo` varchar(30) DEFAULT NULL,
  `is_sync` int(1) DEFAULT '0',
  `id_store` int(255) NOT NULL DEFAULT '0',
  PRIMARY KEY (`generalLedgerAutoID`,`id_store`),
  KEY `documentCode` (`documentCode`) USING BTREE,
  KEY `documentYear` (`documentYear`) USING BTREE,
  KEY `GLAutoID` (`GLAutoID`) USING BTREE,
  KEY `transactionCurrencyID` (`transactionCurrencyID`) USING BTREE,
  KEY `companyLocalCurrencyID` (`companyLocalCurrencyID`) USING BTREE,
  KEY `companyReportingCurrencyID` (`companyReportingCurrencyID`) USING BTREE,
  KEY `segmentID` (`segmentID`) USING BTREE,
  KEY `companyID` (`companyID`) USING BTREE,
  KEY `generalLedgerAutoID` (`generalLedgerAutoID`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='Table Use : To Enter double Entries for all financial Entries\r\nCreated By : Nusky Rauff\r\nDiscussed with : Mohamed Rishad,Mohamed Reyaas, Mohamed Hisham\r\nReviewed By : ';

-- ----------------------------
-- Records of srp_erp_generalledger
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_generalledger_sync
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_generalledger_sync`;
CREATE TABLE `srp_erp_generalledger_sync` (
  `generalLedgerAutoID` int(11) NOT NULL AUTO_INCREMENT,
  `wareHouseAutoID` int(11) NOT NULL DEFAULT '0',
  `documentCode` varchar(20) DEFAULT NULL,
  `documentMasterAutoID` int(11) DEFAULT NULL,
  `documentDetailAutoID` int(11) DEFAULT NULL,
  `documentSystemCode` varchar(45) DEFAULT '0',
  `documentType` varchar(50) DEFAULT NULL,
  `documentDate` date DEFAULT NULL,
  `documentYear` int(4) DEFAULT NULL,
  `documentMonth` int(2) DEFAULT NULL,
  `projectID` int(11) DEFAULT NULL,
  `projectExchangeRate` double DEFAULT '1',
  `documentNarration` text,
  `chequeNumber` varchar(50) DEFAULT NULL,
  `GLAutoID` int(11) DEFAULT NULL,
  `systemGLCode` varchar(255) DEFAULT NULL,
  `GLCode` varchar(45) DEFAULT '0',
  `GLDescription` varchar(255) DEFAULT NULL,
  `GLType` varchar(3) DEFAULT NULL,
  `amount_type` varchar(2) DEFAULT NULL,
  `isFromItem` tinyint(1) DEFAULT '0' COMMENT 'if revenue or expense is from item then 1 else 0',
  `transactionCurrencyID` int(11) NOT NULL,
  `transactionCurrency` varchar(45) DEFAULT NULL COMMENT 'Document transaction currency',
  `transactionExchangeRate` double DEFAULT '0' COMMENT 'Always 1',
  `transactionAmount` double DEFAULT '0' COMMENT 'Amount of transaction in document',
  `transactionCurrencyDecimalPlaces` int(1) DEFAULT NULL COMMENT 'Decimal places of transaction currency ',
  `companyLocalCurrencyID` int(11) NOT NULL,
  `companyLocalCurrency` varchar(45) DEFAULT NULL COMMENT 'Local currency of company in company master',
  `companyLocalExchangeRate` double DEFAULT '0' COMMENT 'Exchange rate against transaction currency',
  `companyLocalAmount` double DEFAULT '0' COMMENT 'Transaction amount in local currency',
  `companyLocalCurrencyDecimalPlaces` int(1) DEFAULT NULL COMMENT 'Decimal places of company currency',
  `companyReportingCurrencyID` int(11) NOT NULL,
  `companyReportingCurrency` varchar(45) DEFAULT NULL COMMENT 'Reporting currency of company in company master',
  `companyReportingExchangeRate` double DEFAULT '0' COMMENT 'Exchange rate against transaction currency ',
  `companyReportingAmount` double DEFAULT '0' COMMENT '1-Payment Invoice, 4- Direct Payment',
  `companyReportingCurrencyDecimalPlaces` int(1) DEFAULT NULL COMMENT 'Decimal places of company currency',
  `partyContractID` varchar(45) DEFAULT '0',
  `partyType` varchar(4) DEFAULT NULL,
  `partyAutoID` int(11) DEFAULT NULL,
  `partySystemCode` varchar(255) DEFAULT NULL,
  `partyName` varchar(255) DEFAULT NULL,
  `partyCurrencyID` int(11) DEFAULT NULL,
  `partyCurrency` varchar(45) DEFAULT NULL COMMENT 'Default currency of supplier ',
  `partyExchangeRate` double DEFAULT '0' COMMENT 'Exchange rate against transaction currency',
  `partyCurrencyAmount` double DEFAULT '0' COMMENT 'Transaction amount in supplier currency ',
  `partyCurrencyDecimalPlaces` int(1) DEFAULT NULL COMMENT 'Decimal places of Supplier currency',
  `subLedgerType` tinyint(1) DEFAULT NULL COMMENT 'Unbilled =1,Ap=2,AR=3, SC=4',
  `subLedgerDesc` varchar(5) DEFAULT NULL,
  `taxMasterAutoID` int(11) DEFAULT NULL,
  `partyVatIdNo` varchar(30) DEFAULT NULL,
  `is_sync` int(1) DEFAULT '0',
  `id_store` int(255) DEFAULT '0',
  `isAddon` int(1) DEFAULT '0',
  `confirmedByEmpID` varchar(100) DEFAULT NULL,
  `confirmedByName` varchar(300) DEFAULT NULL,
  `confirmedDate` datetime DEFAULT NULL,
  `approvedDate` datetime DEFAULT NULL,
  `approvedbyEmpID` varchar(45) DEFAULT NULL,
  `approvedbyEmpName` varchar(200) DEFAULT NULL,
  `segmentID` int(11) DEFAULT NULL,
  `segmentCode` varchar(45) DEFAULT NULL,
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) DEFAULT NULL,
  `createdUserGroup` int(11) DEFAULT NULL,
  `createdPCID` varchar(45) DEFAULT NULL,
  `createdUserID` varchar(45) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  `OtherFeesID` int(11) DEFAULT NULL,
  PRIMARY KEY (`generalLedgerAutoID`,`wareHouseAutoID`),
  UNIQUE KEY `generalLedgerAutoID` (`generalLedgerAutoID`) USING BTREE,
  KEY `documentCode` (`documentCode`) USING BTREE,
  KEY `documentYear` (`documentYear`) USING BTREE,
  KEY `GLAutoID` (`GLAutoID`) USING BTREE,
  KEY `transactionCurrencyID` (`transactionCurrencyID`) USING BTREE,
  KEY `companyLocalCurrencyID` (`companyLocalCurrencyID`) USING BTREE,
  KEY `companyReportingCurrencyID` (`companyReportingCurrencyID`) USING BTREE,
  KEY `segmentID` (`segmentID`) USING BTREE,
  KEY `companyID` (`companyID`) USING BTREE,
  KEY `documentDate` (`documentDate`) USING BTREE,
  KEY `documentMasterAutoID` (`documentMasterAutoID`) USING BTREE,
  KEY `documentDetailAutoID` (`documentDetailAutoID`) USING BTREE,
  KEY `GLType` (`GLType`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table Use : To Enter double Entries for all financial Entries\r\nCreated By : Nusky Rauff\r\nDiscussed with : Mohamed Rishad,Mohamed Reyaas, Mohamed Hisham\r\nReviewed By : ';

-- ----------------------------
-- Records of srp_erp_generalledger_sync
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_itemledger
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_itemledger`;
CREATE TABLE `srp_erp_itemledger` (
  `itemLedgerAutoID` int(11) NOT NULL AUTO_INCREMENT,
  `documentID` varchar(45) DEFAULT NULL,
  `documentAutoID` int(11) DEFAULT NULL,
  `documentCode` varchar(45) DEFAULT NULL,
  `documentSystemCode` varchar(45) DEFAULT NULL,
  `documentDate` date DEFAULT NULL,
  `referenceNumber` varchar(45) DEFAULT NULL,
  `companyFinanceYearID` int(11) DEFAULT NULL,
  `companyFinanceYear` varchar(100) DEFAULT NULL,
  `FYBegin` date DEFAULT NULL,
  `FYEnd` date DEFAULT NULL,
  `FYPeriodDateFrom` date DEFAULT NULL,
  `FYPeriodDateTo` date DEFAULT NULL,
  `wareHouseAutoID` int(11) NOT NULL,
  `wareHouseCode` varchar(45) DEFAULT '1',
  `wareHouseLocation` varchar(100) DEFAULT NULL,
  `wareHouseDescription` varchar(255) DEFAULT NULL,
  `projectID` int(11) DEFAULT NULL,
  `projectExchangeRate` double DEFAULT '1',
  `itemAutoID` int(11) DEFAULT NULL,
  `itemSystemCode` varchar(45) DEFAULT NULL,
  `ItemSecondaryCode` varchar(45) DEFAULT NULL,
  `itemDescription` varchar(255) DEFAULT NULL,
  `defaultUOMID` int(11) DEFAULT NULL,
  `defaultUOM` varchar(45) DEFAULT NULL,
  `transactionUOMID` int(11) DEFAULT NULL,
  `transactionUOM` varchar(45) DEFAULT NULL,
  `transactionQTY` double DEFAULT NULL,
  `convertionRate` double DEFAULT NULL,
  `currentStock` double DEFAULT NULL,
  `PLGLAutoID` int(11) DEFAULT NULL COMMENT 'Expense GL Code',
  `PLSystemGLCode` varchar(45) DEFAULT NULL,
  `PLGLCode` varchar(45) DEFAULT NULL,
  `PLDescription` varchar(255) DEFAULT NULL,
  `PLType` varchar(3) DEFAULT NULL,
  `BLGLAutoID` int(11) DEFAULT NULL COMMENT 'Asset GL Code',
  `BLSystemGLCode` varchar(45) DEFAULT NULL,
  `BLGLCode` varchar(45) DEFAULT NULL,
  `BLDescription` varchar(255) DEFAULT NULL,
  `BLType` varchar(3) DEFAULT NULL,
  `transactionCurrencyID` int(11) DEFAULT NULL,
  `transactionCurrency` varchar(45) DEFAULT NULL COMMENT 'Document transaction currency',
  `transactionExchangeRate` double DEFAULT '0' COMMENT 'Always 1',
  `transactionAmount` double DEFAULT '0' COMMENT 'Amount of transaction in document',
  `transactionCurrencyDecimalPlaces` int(1) DEFAULT '2' COMMENT 'Decimal places of transaction currency ',
  `companyLocalCurrencyID` int(11) DEFAULT NULL,
  `companyLocalCurrency` varchar(45) DEFAULT NULL COMMENT 'Local currency of company in company master',
  `companyLocalExchangeRate` double DEFAULT '0' COMMENT 'Exchange rate against transaction currency',
  `companyLocalAmount` double DEFAULT '0' COMMENT 'Transaction amount in local currency',
  `companyLocalWacAmount` double DEFAULT NULL,
  `companyLocalCurrencyDecimalPlaces` int(1) DEFAULT NULL COMMENT 'Decimal places of company currency',
  `companyReportingCurrencyID` int(11) DEFAULT NULL,
  `companyReportingCurrency` varchar(45) DEFAULT NULL COMMENT 'Reporting currency of company in company master',
  `companyReportingExchangeRate` double DEFAULT '0' COMMENT 'Exchange rate against transaction currency ',
  `companyReportingAmount` double DEFAULT '0' COMMENT '1-Payment Invoice, 4- Direct Payment',
  `companyReportingWacAmount` double DEFAULT NULL,
  `companyReportingCurrencyDecimalPlaces` int(1) DEFAULT NULL COMMENT 'Decimal places of company currency',
  `partyCurrencyID` int(11) DEFAULT NULL,
  `partyCurrency` varchar(45) DEFAULT NULL COMMENT 'Default currency of party ',
  `partyCurrencyExchangeRate` double DEFAULT '0' COMMENT 'Exchange rate against transaction currency',
  `partyCurrencyAmount` double DEFAULT '0' COMMENT 'Transaction amount in party currency ',
  `partyCurrencyDecimalPlaces` int(1) DEFAULT '2' COMMENT 'Decimal places of Supplier currency',
  `salesPrice` double DEFAULT NULL COMMENT 'sales price of unit',
  `confirmedYN` int(1) DEFAULT '0',
  `confirmedByEmpID` varchar(100) DEFAULT NULL,
  `confirmedByName` varchar(300) DEFAULT NULL,
  `confirmedDate` datetime DEFAULT NULL,
  `approvedYN` int(1) DEFAULT '0',
  `approvedDate` datetime DEFAULT NULL,
  `approvedbyEmpID` varchar(45) DEFAULT NULL,
  `approvedbyEmpName` varchar(200) DEFAULT NULL,
  `segmentID` int(11) DEFAULT NULL,
  `segmentCode` varchar(45) DEFAULT NULL,
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) DEFAULT NULL,
  `narration` varchar(255) DEFAULT NULL,
  `createdUserGroup` int(11) DEFAULT NULL,
  `createdPCID` varchar(45) DEFAULT NULL,
  `createdUserID` varchar(45) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  `expenseGLAutoID` int(11) DEFAULT NULL,
  `expenseGLCode` varchar(45) DEFAULT NULL,
  `expenseSystemGLCode` varchar(45) DEFAULT NULL,
  `expenseGLDescription` varchar(255) DEFAULT NULL,
  `expenseGLType` varchar(5) DEFAULT NULL,
  `revenueGLAutoID` int(11) DEFAULT NULL,
  `revenueGLCode` varchar(45) DEFAULT NULL,
  `revenueSystemGLCode` varchar(45) DEFAULT NULL,
  `revenueGLDescription` varchar(255) DEFAULT NULL,
  `revenueGLType` varchar(5) DEFAULT NULL,
  `assetGLAutoID` int(11) DEFAULT NULL,
  `assetGLCode` varchar(45) DEFAULT NULL,
  `assetSystemGLCode` varchar(45) DEFAULT NULL,
  `assetGLDescription` varchar(255) DEFAULT NULL,
  `assetGLType` varchar(5) DEFAULT NULL,
  `is_sync` int(1) DEFAULT '0',
  `id_store` int(255) NOT NULL DEFAULT '0',
  PRIMARY KEY (`itemLedgerAutoID`,`id_store`),
  KEY `companyID` (`companyID`) USING BTREE,
  KEY `documentId` (`documentID`) USING BTREE,
  KEY `itemAutoID` (`itemAutoID`) USING BTREE,
  KEY `wareHouseCode` (`wareHouseCode`) USING BTREE,
  KEY `companyFinanceYearID` (`companyFinanceYearID`) USING BTREE,
  KEY `wareHouseAutoID` (`wareHouseAutoID`) USING BTREE,
  KEY `defaultUOMID` (`defaultUOMID`) USING BTREE,
  KEY `transactionUOMID` (`transactionUOMID`) USING BTREE,
  KEY `transactionQTY` (`transactionQTY`) USING BTREE,
  KEY `transactionCurrencyID` (`transactionCurrencyID`) USING BTREE,
  KEY `companyLocalCurrencyID` (`companyLocalCurrencyID`) USING BTREE,
  KEY `companyReportingCurrencyID` (`companyReportingCurrencyID`) USING BTREE,
  KEY `partyCurrencyID` (`partyCurrencyID`) USING BTREE,
  KEY `segmentCode` (`segmentCode`) USING BTREE,
  KEY `currentStock` (`currentStock`) USING BTREE,
  KEY `PLGLAutoID` (`PLGLAutoID`) USING BTREE,
  KEY `BLGLAutoID` (`BLGLAutoID`) USING BTREE,
  KEY `itemLedgerAutoID` (`itemLedgerAutoID`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='Table Use : To record the transaction of inventroy items from all documents\r\nCreated By : Mohamed Hisham\r\nDiscussed with : Mohamed Rishad,Mohamed Reyaas, Nusky Rauff\r\nReviewed By : ';

-- ----------------------------
-- Records of srp_erp_itemledger
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_itemledger_sync
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_itemledger_sync`;
CREATE TABLE `srp_erp_itemledger_sync` (
  `itemLedgerAutoID` int(11) NOT NULL AUTO_INCREMENT,
  `documentID` varchar(45) DEFAULT NULL,
  `documentAutoID` int(11) DEFAULT NULL,
  `documentCode` varchar(45) DEFAULT NULL,
  `documentSystemCode` varchar(45) DEFAULT NULL,
  `documentDate` date DEFAULT NULL,
  `referenceNumber` varchar(45) DEFAULT NULL,
  `companyFinanceYearID` int(11) DEFAULT NULL,
  `companyFinanceYear` varchar(100) DEFAULT NULL,
  `FYBegin` date DEFAULT NULL,
  `FYEnd` date DEFAULT NULL,
  `FYPeriodDateFrom` date DEFAULT NULL,
  `FYPeriodDateTo` date DEFAULT NULL,
  `wareHouseAutoID` int(11) NOT NULL,
  `wareHouseCode` varchar(45) DEFAULT '1',
  `wareHouseLocation` varchar(100) DEFAULT NULL,
  `wareHouseDescription` varchar(255) DEFAULT NULL,
  `projectID` int(11) DEFAULT NULL,
  `projectExchangeRate` double DEFAULT '1',
  `itemAutoID` int(11) DEFAULT NULL,
  `itemSystemCode` varchar(45) DEFAULT NULL,
  `ItemSecondaryCode` varchar(45) DEFAULT NULL,
  `itemDescription` varchar(255) DEFAULT NULL,
  `defaultUOMID` int(11) DEFAULT NULL,
  `defaultUOM` varchar(45) DEFAULT NULL,
  `transactionUOMID` int(11) DEFAULT NULL,
  `transactionUOM` varchar(45) DEFAULT NULL,
  `transactionQTY` double DEFAULT NULL,
  `convertionRate` double DEFAULT NULL,
  `currentStock` double DEFAULT NULL,
  `PLGLAutoID` int(11) DEFAULT NULL COMMENT 'Expense GL Code',
  `PLSystemGLCode` varchar(45) DEFAULT NULL,
  `PLGLCode` varchar(45) DEFAULT NULL,
  `PLDescription` varchar(255) DEFAULT NULL,
  `PLType` varchar(3) DEFAULT NULL,
  `BLGLAutoID` int(11) DEFAULT NULL COMMENT 'Asset GL Code',
  `BLSystemGLCode` varchar(45) DEFAULT NULL,
  `BLGLCode` varchar(45) DEFAULT NULL,
  `BLDescription` varchar(255) DEFAULT NULL,
  `BLType` varchar(3) DEFAULT NULL,
  `transactionCurrencyID` int(11) DEFAULT NULL,
  `transactionCurrency` varchar(45) DEFAULT NULL COMMENT 'Document transaction currency',
  `transactionExchangeRate` double DEFAULT '0' COMMENT 'Always 1',
  `transactionAmount` double DEFAULT '0' COMMENT 'Amount of transaction in document',
  `transactionCurrencyDecimalPlaces` int(1) DEFAULT '2' COMMENT 'Decimal places of transaction currency ',
  `companyLocalCurrencyID` int(11) DEFAULT NULL,
  `companyLocalCurrency` varchar(45) DEFAULT NULL COMMENT 'Local currency of company in company master',
  `companyLocalExchangeRate` double DEFAULT '0' COMMENT 'Exchange rate against transaction currency',
  `companyLocalAmount` double DEFAULT '0' COMMENT 'Transaction amount in local currency',
  `companyLocalWacAmount` double DEFAULT NULL,
  `companyLocalCurrencyDecimalPlaces` int(1) DEFAULT NULL COMMENT 'Decimal places of company currency',
  `companyReportingCurrencyID` int(11) DEFAULT NULL,
  `companyReportingCurrency` varchar(45) DEFAULT NULL COMMENT 'Reporting currency of company in company master',
  `companyReportingExchangeRate` double DEFAULT '0' COMMENT 'Exchange rate against transaction currency ',
  `companyReportingAmount` double DEFAULT '0' COMMENT '1-Payment Invoice, 4- Direct Payment',
  `companyReportingWacAmount` double DEFAULT NULL,
  `companyReportingCurrencyDecimalPlaces` int(1) DEFAULT NULL COMMENT 'Decimal places of company currency',
  `partyCurrencyID` int(11) DEFAULT NULL,
  `partyCurrency` varchar(45) DEFAULT NULL COMMENT 'Default currency of party ',
  `partyCurrencyExchangeRate` double DEFAULT '0' COMMENT 'Exchange rate against transaction currency',
  `partyCurrencyAmount` double DEFAULT '0' COMMENT 'Transaction amount in party currency ',
  `partyCurrencyDecimalPlaces` int(1) DEFAULT '2' COMMENT 'Decimal places of Supplier currency',
  `salesPrice` double DEFAULT NULL COMMENT 'sales price of unit',
  `confirmedYN` int(1) DEFAULT '0',
  `confirmedByEmpID` varchar(100) DEFAULT NULL,
  `confirmedByName` varchar(300) DEFAULT NULL,
  `confirmedDate` datetime DEFAULT NULL,
  `approvedYN` int(1) DEFAULT '0',
  `approvedDate` datetime DEFAULT NULL,
  `approvedbyEmpID` varchar(45) DEFAULT NULL,
  `approvedbyEmpName` varchar(200) DEFAULT NULL,
  `segmentID` int(11) DEFAULT NULL,
  `segmentCode` varchar(45) DEFAULT NULL,
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) DEFAULT NULL,
  `narration` varchar(255) DEFAULT NULL,
  `createdUserGroup` int(11) DEFAULT NULL,
  `createdPCID` varchar(45) DEFAULT NULL,
  `createdUserID` varchar(45) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  `expenseGLAutoID` int(11) DEFAULT NULL,
  `expenseGLCode` varchar(45) DEFAULT NULL,
  `expenseSystemGLCode` varchar(45) DEFAULT NULL,
  `expenseGLDescription` varchar(255) DEFAULT NULL,
  `expenseGLType` varchar(5) DEFAULT NULL,
  `revenueGLAutoID` int(11) DEFAULT NULL,
  `revenueGLCode` varchar(45) DEFAULT NULL,
  `revenueSystemGLCode` varchar(45) DEFAULT NULL,
  `revenueGLDescription` varchar(255) DEFAULT NULL,
  `revenueGLType` varchar(5) DEFAULT NULL,
  `assetGLAutoID` int(11) DEFAULT NULL,
  `assetGLCode` varchar(45) DEFAULT NULL,
  `assetSystemGLCode` varchar(45) DEFAULT NULL,
  `assetGLDescription` varchar(255) DEFAULT NULL,
  `assetGLType` varchar(5) DEFAULT NULL,
  `is_sync` int(1) DEFAULT '0',
  `id_store` int(255) DEFAULT '0',
  PRIMARY KEY (`itemLedgerAutoID`,`wareHouseAutoID`),
  UNIQUE KEY `itemLedgerAutoID` (`itemLedgerAutoID`) USING BTREE,
  KEY `companyID` (`companyID`),
  KEY `documentId` (`documentID`),
  KEY `itemAutoID` (`itemAutoID`),
  KEY `wareHouseCode` (`wareHouseCode`),
  KEY `companyFinanceYearID` (`companyFinanceYearID`) USING BTREE,
  KEY `wareHouseAutoID` (`wareHouseAutoID`) USING BTREE,
  KEY `defaultUOMID` (`defaultUOMID`) USING BTREE,
  KEY `transactionUOMID` (`transactionUOMID`) USING BTREE,
  KEY `transactionQTY` (`transactionQTY`) USING BTREE,
  KEY `transactionCurrencyID` (`transactionCurrencyID`) USING BTREE,
  KEY `companyLocalCurrencyID` (`companyLocalCurrencyID`) USING BTREE,
  KEY `companyReportingCurrencyID` (`companyReportingCurrencyID`) USING BTREE,
  KEY `partyCurrencyID` (`partyCurrencyID`) USING BTREE,
  KEY `segmentCode` (`segmentCode`) USING BTREE,
  KEY `currentStock` (`currentStock`) USING BTREE,
  KEY `PLGLAutoID` (`PLGLAutoID`) USING BTREE,
  KEY `BLGLAutoID` (`BLGLAutoID`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table Use : To record the transaction of inventroy items from all documents\r\nCreated By : Mohamed Hisham\r\nDiscussed with : Mohamed Rishad,Mohamed Reyaas, Nusky Rauff\r\nReviewed By : ';

-- ----------------------------
-- Records of srp_erp_itemledger_sync
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_itemmaster
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_itemmaster`;
CREATE TABLE `srp_erp_itemmaster` (
  `itemAutoID` int(5) NOT NULL AUTO_INCREMENT,
  `itemSystemCode` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `seconeryItemCode` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `itemImage` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `itemName` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `itemDescription` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `mainCategoryID` int(10) DEFAULT NULL,
  `mainCategory` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `subcategoryID` int(5) DEFAULT NULL,
  `subSubCategoryID` int(5) DEFAULT NULL,
  `itemUrl` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `barcode` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `financeCategory` varchar(5) CHARACTER SET utf8 DEFAULT NULL COMMENT '1-inventory 2-noninventory/service 3-fixed asset',
  `partNo` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `defaultUnitOfMeasureID` int(11) DEFAULT NULL,
  `defaultUnitOfMeasure` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `currentStock` double DEFAULT '0',
  `reorderPoint` double DEFAULT NULL,
  `maximunQty` double DEFAULT NULL,
  `minimumQty` double DEFAULT NULL,
  `revanueGLAutoID` int(11) DEFAULT NULL,
  `revanueSystemGLCode` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `revanueGLCode` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `revanueDescription` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `revanueType` varchar(3) CHARACTER SET utf8 DEFAULT NULL,
  `costGLAutoID` int(11) DEFAULT NULL,
  `costSystemGLCode` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `costGLCode` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `costDescription` varchar(150) CHARACTER SET utf8 DEFAULT NULL,
  `costType` varchar(3) CHARACTER SET utf8 DEFAULT NULL,
  `assteGLAutoID` int(11) DEFAULT NULL,
  `assteSystemGLCode` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `assteGLCode` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `assteDescription` varchar(150) CHARACTER SET utf8 DEFAULT NULL,
  `assteType` varchar(3) CHARACTER SET utf8 DEFAULT NULL,
  `stockAdjustmentGLAutoID` int(11) DEFAULT NULL,
  `stockAdjustmentSystemGLCode` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `stockAdjustmentGLCode` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `stockAdjustmentDescription` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `stockAdjustmentType` varchar(3) CHARACTER SET utf8 DEFAULT NULL,
  `faCostGLAutoID` int(11) DEFAULT NULL COMMENT 'if fixed asset',
  `faACCDEPGLAutoID` int(11) DEFAULT NULL COMMENT 'if fixed asset',
  `faDEPGLAutoID` int(11) DEFAULT NULL COMMENT 'if fixed asset',
  `faDISPOGLAutoID` int(11) DEFAULT NULL COMMENT 'if fixed asset',
  `salesTaxFormulaID` int(11) DEFAULT NULL,
  `purchaseTaxFormulaID` int(11) DEFAULT NULL,
  `isActive` tinyint(1) DEFAULT NULL,
  `comments` text CHARACTER SET utf8,
  `isSubitemExist` int(1) DEFAULT '0' COMMENT '1 have sub item, 0 not exist ',
  `companyLocalCurrencyID` int(11) NOT NULL,
  `companyLocalCurrency` varchar(45) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Local currency of company in company master',
  `companyLocalExchangeRate` double DEFAULT '0' COMMENT 'Exchange rate against transaction currency',
  `companyLocalSellingPrice` double DEFAULT '0' COMMENT 'Transaction amount in local currency',
  `companyLocalWacAmount` double DEFAULT '0' COMMENT 'Transaction amount in local currency',
  `companyLocalCurrencyDecimalPlaces` int(1) DEFAULT '2' COMMENT 'Decimal places of company currency',
  `companyReportingCurrencyID` int(11) NOT NULL,
  `companyReportingCurrency` varchar(45) CHARACTER SET utf8 DEFAULT NULL COMMENT 'Reporting currency of company in company master',
  `companyReportingExchangeRate` double DEFAULT '0' COMMENT 'Exchange rate against transaction currency ',
  `companyReportingSellingPrice` double DEFAULT '0' COMMENT '1-Payment Invoice, 4- Direct Payment',
  `companyReportingWacAmount` double DEFAULT '0' COMMENT '1-Payment Invoice, 4- Direct Payment',
  `companyReportingCurrencyDecimalPlaces` int(1) DEFAULT '2' COMMENT 'Decimal places of company currency',
  `finCompanyPercentage` double DEFAULT '100',
  `pvtCompanyPercentage` double DEFAULT '0',
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `createdUserGroup` int(11) DEFAULT NULL,
  `createdPCID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `createdUserID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `modifiedPCID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `modifiedUserID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`itemAutoID`),
  UNIQUE KEY `itemAutoID` (`itemAutoID`) USING BTREE,
  UNIQUE KEY `itemSystemCode` (`itemSystemCode`) USING BTREE,
  KEY `itemDescription` (`itemDescription`) USING BTREE,
  KEY `mainCategoryID` (`mainCategoryID`) USING BTREE,
  KEY `itemName` (`itemName`) USING BTREE,
  KEY `subcategoryID` (`subcategoryID`) USING BTREE,
  KEY `subSubCategoryID` (`subSubCategoryID`) USING BTREE,
  KEY `barcode` (`barcode`) USING BTREE,
  KEY `partNo` (`partNo`) USING BTREE,
  KEY `revanueGLAutoID` (`revanueGLAutoID`) USING BTREE,
  KEY `costGLAutoID` (`costGLAutoID`) USING BTREE,
  KEY `assteGLAutoID` (`assteGLAutoID`) USING BTREE,
  KEY `stockAdjustmentGLAutoID` (`stockAdjustmentGLAutoID`) USING BTREE,
  KEY `faCostGLAutoID` (`faCostGLAutoID`) USING BTREE,
  KEY `faACCDEPGLAutoID` (`faACCDEPGLAutoID`) USING BTREE,
  KEY `faDEPGLAutoID` (`faDEPGLAutoID`) USING BTREE,
  KEY `faDISPOGLAutoID` (`faDISPOGLAutoID`) USING BTREE,
  KEY `salesTaxFormulaID` (`salesTaxFormulaID`) USING BTREE,
  KEY `purchaseTaxFormulaID` (`purchaseTaxFormulaID`) USING BTREE,
  KEY `isActive` (`isActive`) USING BTREE,
  KEY `companyID` (`companyID`) USING BTREE,
  KEY ` companyCode` (`companyCode`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table Use :To maintain inventory, non inventory ,service items and fixed assets\r\nCreated By : Nusky Rauff\r\nDiscussed with : Mohamed Rishad,Mohamed Reyaas, Mohamed Hisham\r\nReviewed By : ';

-- ----------------------------
-- Records of srp_erp_itemmaster
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_lang_companylanguages
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_lang_companylanguages`;
CREATE TABLE `srp_erp_lang_companylanguages` (
  `companyLanguageID` int(11) NOT NULL AUTO_INCREMENT,
  `companyID` int(11) DEFAULT NULL,
  `primaryLanguageID` int(11) DEFAULT NULL,
  `secondaryLanguageID` int(11) DEFAULT NULL,
  PRIMARY KEY (`companyLanguageID`),
  UNIQUE KEY `companyLanguageID` (`companyLanguageID`) USING BTREE,
  KEY `companyID` (`companyID`) USING BTREE,
  KEY `primaryLanguageID` (`primaryLanguageID`) USING BTREE,
  KEY `secondaryLanguageID` (`secondaryLanguageID`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of srp_erp_lang_companylanguages
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_lang_languages
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_lang_languages`;
CREATE TABLE `srp_erp_lang_languages` (
  `languageID` int(11) NOT NULL AUTO_INCREMENT,
  `systemDescription` varchar(45) DEFAULT NULL COMMENT 'maintain as mentioned in PHP library',
  `description` varchar(45) DEFAULT NULL,
  `isActive` int(11) DEFAULT '1',
  PRIMARY KEY (`languageID`),
  UNIQUE KEY `languageID` (`languageID`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of srp_erp_lang_languages
-- ----------------------------
INSERT INTO `srp_erp_lang_languages` VALUES ('1', 'arabic', 'Arabic', '1');
INSERT INTO `srp_erp_lang_languages` VALUES ('2', 'english', 'English', '1');
INSERT INTO `srp_erp_lang_languages` VALUES ('4', 'tamil', 'Tamil', '0');

-- ----------------------------
-- Table structure for srp_erp_passwordcomplexcity
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_passwordcomplexcity`;
CREATE TABLE `srp_erp_passwordcomplexcity` (
  `projectComplexcityID` int(11) NOT NULL AUTO_INCREMENT,
  `minimumLength` int(2) NOT NULL DEFAULT '8',
  `maximumLength` int(2) DEFAULT '8',
  `isCapitalLettersMandatory` int(1) DEFAULT '0',
  `isSpecialCharactersMandatory` int(1) DEFAULT '0',
  `companyID` int(11) DEFAULT NULL,
  `createdUserGroup` int(11) DEFAULT NULL,
  `createdPCID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `createdUserID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `modifiedPCID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `modifiedUserID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`projectComplexcityID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of srp_erp_passwordcomplexcity
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pay_imagepath
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pay_imagepath`;
CREATE TABLE `srp_erp_pay_imagepath` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `imagePath` varchar(255) DEFAULT NULL,
  `isLocalPath` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of srp_erp_pay_imagepath
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_addon
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_addon`;
CREATE TABLE `srp_erp_pos_addon` (
  `autoID` int(11) NOT NULL AUTO_INCREMENT,
  `menuSalesItemID` int(11) DEFAULT NULL,
  `menuMasterID` int(11) DEFAULT NULL,
  PRIMARY KEY (`autoID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of srp_erp_pos_addon
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_auth_processassign
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_auth_processassign`;
CREATE TABLE `srp_erp_pos_auth_processassign` (
  `processAssignID` int(11) NOT NULL AUTO_INCREMENT,
  `processMasterID` int(11) DEFAULT NULL,
  `isActive` tinyint(4) DEFAULT '1',
  `companyID` int(11) DEFAULT NULL,
  `createdPCID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `createdUserID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `createdUserGroup` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `modifiedPCID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `modifiedUserID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `timeStamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`processAssignID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of srp_erp_pos_auth_processassign
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_auth_processmaster
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_auth_processmaster`;
CREATE TABLE `srp_erp_pos_auth_processmaster` (
  `processMasterID` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(100) DEFAULT NULL,
  `isActive` tinyint(4) DEFAULT '1',
  `createdPCID` varchar(45) DEFAULT NULL,
  `createdUserID` varchar(45) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `createdUserGroup` varchar(45) DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `timeStamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`processMasterID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of srp_erp_pos_auth_processmaster
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_auth_usergroupdetail
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_auth_usergroupdetail`;
CREATE TABLE `srp_erp_pos_auth_usergroupdetail` (
  `userGroupDetailID` int(11) NOT NULL AUTO_INCREMENT,
  `userGroupMasterID` int(11) DEFAULT NULL,
  `processMasterID` int(11) DEFAULT NULL,
  `wareHouseID` int(11) DEFAULT NULL,
  `companyID` int(11) DEFAULT NULL,
  `createdPCID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `createdUserID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `createdUserGroup` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `modifiedPCID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `modifiedUserID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `timeStamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`userGroupDetailID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of srp_erp_pos_auth_usergroupdetail
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_auth_usergroupmaster
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_auth_usergroupmaster`;
CREATE TABLE `srp_erp_pos_auth_usergroupmaster` (
  `userGroupMasterID` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(100) DEFAULT NULL,
  `isActive` tinyint(4) DEFAULT '1',
  `companyID` int(11) DEFAULT NULL,
  `createdPCID` varchar(45) DEFAULT NULL,
  `createdUserID` varchar(45) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `createdUserGroup` varchar(45) DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `timeStamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`userGroupMasterID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of srp_erp_pos_auth_usergroupmaster
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_camera_setup
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_camera_setup`;
CREATE TABLE `srp_erp_pos_camera_setup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url_host` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `port` int(11) DEFAULT '80',
  `outletID` int(11) DEFAULT NULL,
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `createdPCID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `createdUserID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `modifiedPCID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `modifiedUserID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `outletID` (`outletID`) USING BTREE,
  KEY `companyID` (`companyID`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of srp_erp_pos_camera_setup
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_cardissue
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_cardissue`;
CREATE TABLE `srp_erp_pos_cardissue` (
  `cardIssueID` int(11) NOT NULL AUTO_INCREMENT,
  `cardMasterID` int(11) DEFAULT NULL,
  `barCode` varchar(300) DEFAULT NULL,
  `posCustomerAutoID` int(11) DEFAULT NULL,
  `issuedDatetime` datetime DEFAULT NULL,
  `expiryDate` date DEFAULT NULL,
  `issuedOutletID` int(11) DEFAULT NULL,
  `companyID` int(11) DEFAULT NULL,
  `createdUserGroup` int(11) DEFAULT NULL,
  `createdPCID` varchar(45) DEFAULT NULL,
  `createdUserID` varchar(45) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`cardIssueID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of srp_erp_pos_cardissue
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_cardtopup
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_cardtopup`;
CREATE TABLE `srp_erp_pos_cardtopup` (
  `cardTopUpID` int(11) NOT NULL AUTO_INCREMENT,
  `wareHouseAutoID` int(11) NOT NULL DEFAULT '0',
  `giftCardReceiptID` int(11) DEFAULT '0' COMMENT 'to identify the transaction',
  `cardMasterID` int(11) DEFAULT NULL,
  `barCode` varchar(300) DEFAULT NULL,
  `posCustomerAutoID` int(11) DEFAULT NULL,
  `topUpAmount` double DEFAULT '0',
  `points` double DEFAULT '0',
  `glConfigMasterID` int(11) DEFAULT NULL,
  `glConfigDetailID` int(11) DEFAULT NULL,
  `menuSalesID` int(11) DEFAULT '0',
  `giftCardGLAutoID` int(11) DEFAULT NULL,
  `outletID` int(11) DEFAULT '0',
  `reference` varchar(600) DEFAULT NULL,
  `creditSalesCustomerID` int(11) DEFAULT NULL,
  `isCreditSale` int(1) DEFAULT '0' COMMENT '1 credit sales, 0 not credit sale',
  `shiftID` int(11) DEFAULT '0',
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) DEFAULT NULL,
  `createdPCID` varchar(45) DEFAULT NULL,
  `createdUserID` varchar(45) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `createdUserGroup` varchar(45) DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  `is_sync` int(1) DEFAULT '0',
  `id_store` int(255) NOT NULL DEFAULT '0',
  PRIMARY KEY (`cardTopUpID`,`id_store`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of srp_erp_pos_cardtopup
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_counters
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_counters`;
CREATE TABLE `srp_erp_pos_counters` (
  `counterID` int(11) NOT NULL AUTO_INCREMENT,
  `counterCode` varchar(45) DEFAULT NULL,
  `counterName` varchar(200) DEFAULT NULL,
  `isActive` tinyint(1) DEFAULT '1',
  `wareHouseID` int(11) DEFAULT NULL,
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) DEFAULT NULL,
  `createdPCID` varchar(45) DEFAULT NULL,
  `createdUserID` int(11) DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `createdUserGroup` varchar(45) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`counterID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table Use : To Create Cash registers for POS\r\nCreated By : Mohamed Hisham \r\nDiscussed with : Mohamed Rishad,Mohamed Reyaas, Nasik Ahamed\r\nReviewed By : ';

-- ----------------------------
-- Records of srp_erp_pos_counters
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_crewmembers
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_crewmembers`;
CREATE TABLE `srp_erp_pos_crewmembers` (
  `crewMemberID` int(11) NOT NULL AUTO_INCREMENT,
  `crewFirstName` varchar(200) DEFAULT NULL,
  `crewLastName` varchar(200) DEFAULT NULL,
  `EIdNo` int(11) DEFAULT NULL COMMENT 'Employee Auto ID from srp_employeeDetails Tables',
  `crewRoleID` int(11) DEFAULT NULL,
  `wareHouseAutoID` int(11) DEFAULT NULL,
  `segmentConfigID` int(11) DEFAULT NULL,
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) DEFAULT NULL,
  `segmentID` int(11) DEFAULT NULL,
  `segmentCode` varchar(50) DEFAULT NULL,
  `createdPCID` varchar(45) DEFAULT NULL,
  `createdUserID` varchar(45) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `createdUserGroup` varchar(45) DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `timeStamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`crewMemberID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table Use : Crew members for restaurants are recorded in this table\r\nCreated By : Mohamed Hisham 09/10/2016\r\nDiscussed with : Mohamed Rishad, Reyaas Rafaideen and Mohamed Shafry 09/10/2016\r\nReviewed By : Mohamed Zahlan 09/10/2016\r\n';

-- ----------------------------
-- Records of srp_erp_pos_crewmembers
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_crewroles
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_crewroles`;
CREATE TABLE `srp_erp_pos_crewroles` (
  `crewRoleID` int(11) NOT NULL AUTO_INCREMENT,
  `roleDescription` varchar(45) DEFAULT NULL,
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) DEFAULT NULL,
  `isWaiter` int(11) DEFAULT '0' COMMENT 'waiter 1, other 0',
  `createdPCID` varchar(45) DEFAULT NULL,
  `createdUserID` varchar(45) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `createdUserGroup` varchar(45) DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `timeStamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`crewRoleID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table Use : this is the master table maintained in system level. companies can assign roles for employees using this table  \r\nCreated By : Mohamed Hisham 09/10/2016\r\nDiscussed with : Mohamed Rishad, Reyaas Rafaideen and Mohamed Shafry 09/10/2016\r\nReviewed By : Mohamed Zahlan 09/10/2016\r\n';

-- ----------------------------
-- Records of srp_erp_pos_crewroles
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_customermaster
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_customermaster`;
CREATE TABLE `srp_erp_pos_customermaster` (
  `posCustomerAutoID` int(11) NOT NULL AUTO_INCREMENT,
  `wareHouseAutoID` int(11) DEFAULT '0',
  `serialNo` int(11) DEFAULT NULL,
  `CustomerAutoID` int(11) DEFAULT NULL,
  `CustomerSystemCode` varchar(20) DEFAULT NULL,
  `CustomerName` varchar(200) DEFAULT NULL,
  `DOB` date DEFAULT NULL COMMENT 'date of birth',
  `partyCategoryID` int(11) DEFAULT NULL COMMENT 'to maintain party Category',
  `CustomerAddress1` varchar(255) DEFAULT NULL,
  `customerAddress1Type` enum('Home','Office','Other') DEFAULT 'Home',
  `customerAddress2` varchar(255) DEFAULT NULL,
  `customerAddress2Type` enum('Home','Office','Other') DEFAULT 'Home',
  `customerCountry` varchar(100) DEFAULT NULL,
  `customerTelephone` varchar(20) DEFAULT NULL,
  `customerEmail` varchar(50) DEFAULT NULL,
  `customerUrl` varchar(100) DEFAULT NULL,
  `customerFax` varchar(20) DEFAULT NULL,
  `secondaryCode` varchar(11) DEFAULT NULL,
  `customerCurrencyID` int(11) NOT NULL,
  `customerCurrency` varchar(45) DEFAULT NULL,
  `customerCurrencyDecimalPlaces` int(1) DEFAULT NULL,
  `isActive` int(1) DEFAULT NULL,
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) DEFAULT NULL,
  `createdUserGroup` int(11) DEFAULT NULL,
  `createdPCID` varchar(45) DEFAULT NULL,
  `createdUserID` varchar(45) DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `isCardHolder` int(1) DEFAULT '1' COMMENT 'Available to Card Customer',
  `isFromERP` int(1) DEFAULT '1' COMMENT '1 from erp , 0 from MFQ',
  `isFromDelivery` int(11) DEFAULT '0' COMMENT '0 - not from delivery order , 1- delivery order',
  `is_sync` int(1) DEFAULT '1',
  `id_store` int(255) NOT NULL DEFAULT '0',
  PRIMARY KEY (`posCustomerAutoID`,`id_store`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of srp_erp_pos_customermaster
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_customers
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_customers`;
CREATE TABLE `srp_erp_pos_customers` (
  `customerID` int(11) NOT NULL AUTO_INCREMENT,
  `customerName` varchar(300) DEFAULT NULL,
  `customerTypeMasterID` int(11) DEFAULT NULL,
  `isOnTimePayment` int(1) DEFAULT NULL,
  `companyID` int(11) DEFAULT NULL,
  `isActive` int(1) DEFAULT '1' COMMENT '0 - inActive , 1 Active',
  `commissionPercentage` double DEFAULT '0',
  `expenseGLAutoID` int(11) DEFAULT NULL,
  `liabilityGLAutoID` int(11) DEFAULT NULL,
  `timestamp` datetime DEFAULT NULL,
  PRIMARY KEY (`customerID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table Use : used in POS restaurent System Level Table to maintain customer information and customer type\r\nCreated By : Shafri\r\nDiscussed with : Rishad\r\nReviewed By : ';

-- ----------------------------
-- Records of srp_erp_pos_customers
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_customertypemaster
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_customertypemaster`;
CREATE TABLE `srp_erp_pos_customertypemaster` (
  `customerTypeID` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`customerTypeID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table Use : used in POS restaurent System Level Table to maintain  customer type\r\nCreated By : Shafri\r\nDiscussed with : Rishad\r\nReviewed By : ';

-- ----------------------------
-- Records of srp_erp_pos_customertypemaster
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_deliveryorders
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_deliveryorders`;
CREATE TABLE `srp_erp_pos_deliveryorders` (
  `deliveryOrderID` int(11) NOT NULL AUTO_INCREMENT,
  `wareHouseAutoID` int(11) NOT NULL DEFAULT '0',
  `deliveryDate` date DEFAULT NULL,
  `deliveryTime` time DEFAULT NULL,
  `menuSalesMasterID` int(11) DEFAULT NULL,
  `posCustomerAutoID` int(11) DEFAULT NULL,
  `phoneNo` varchar(30) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `deliveryCharges` double DEFAULT NULL,
  `deliveryType` enum('Pick up','Delivery') DEFAULT NULL,
  `crewMemberID` int(11) DEFAULT NULL COMMENT 'internal Delivery Person ID',
  `landMarkLocation` varchar(600) DEFAULT '' COMMENT 'nearest land mark location',
  `isDispatched` int(1) DEFAULT '0' COMMENT '1  Dispatched, 0  not Dispatched',
  `dispatchedDatetime` datetime DEFAULT NULL,
  `dispatchedBy` int(11) DEFAULT NULL COMMENT 'employee id',
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) DEFAULT NULL,
  `createdUserGroup` int(11) DEFAULT NULL,
  `createdPCID` varchar(45) DEFAULT NULL,
  `createdUserID` varchar(45) DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `is_sync` int(1) DEFAULT '0',
  `id_store` int(255) NOT NULL DEFAULT '0',
  PRIMARY KEY (`deliveryOrderID`,`id_store`),
  KEY `menusalesID` (`deliveryOrderID`,`deliveryDate`,`deliveryTime`,`menuSalesMasterID`,`phoneNo`,`email`) USING BTREE,
  KEY `menuSalesMasterID` (`menuSalesMasterID`) USING BTREE,
  KEY `crewMemberID` (`crewMemberID`) USING BTREE,
  KEY `posCustomerAutoID` (`posCustomerAutoID`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of srp_erp_pos_deliveryorders
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_diningroommaster
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_diningroommaster`;
CREATE TABLE `srp_erp_pos_diningroommaster` (
  `diningRoomMasterID` int(11) NOT NULL AUTO_INCREMENT,
  `diningRoomDescription` varchar(100) DEFAULT NULL,
  `wareHouseAutoID` int(11) DEFAULT NULL,
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) DEFAULT NULL,
  `segmentID` int(11) DEFAULT NULL,
  `segmentCode` varchar(45) DEFAULT NULL,
  `createdUserGroup` int(11) DEFAULT NULL,
  `createdPCID` varchar(45) DEFAULT NULL,
  `createdUserID` varchar(45) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`diningRoomMasterID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table Use : to maintain dining areas (rooms) for restaurant POS\r\nCreated By : Mohamed Hisham10/10/2016\r\nDiscussed with : Mohamed Rishad, Reyaas Rafaideen and Mohamed Shafry 09/10/2016\r\nReviewed By : Mohamed Zahlan 09/10/2016\r\n\r\n';

-- ----------------------------
-- Records of srp_erp_pos_diningroommaster
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_diningtables
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_diningtables`;
CREATE TABLE `srp_erp_pos_diningtables` (
  `diningTableAutoID` int(11) NOT NULL AUTO_INCREMENT,
  `diningTableDescription` varchar(200) DEFAULT NULL,
  `noOfSeats` int(2) DEFAULT NULL,
  `diningRoomMasterID` int(11) DEFAULT NULL COMMENT 'FK - srp_erp_pos_diningroommaster',
  `status` int(1) DEFAULT '0' COMMENT '0 - free, 1 - occupied',
  `tmp_menuSalesID` int(11) DEFAULT NULL COMMENT 'tmp Bill ID',
  `tmp_crewID` int(11) DEFAULT NULL COMMENT 'tmp Crew ID',
  `tmp_numberOfPacks` int(11) DEFAULT '0',
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) DEFAULT NULL,
  `segmentID` int(11) DEFAULT NULL,
  `segmentCode` varchar(45) DEFAULT NULL,
  `createdUserGroup` int(11) DEFAULT NULL,
  `createdPCID` varchar(45) DEFAULT NULL,
  `createdUserID` varchar(45) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`diningTableAutoID`),
  KEY `status` (`status`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table Use : to maintain each dining tables under dining rooms for restaurant POS\r\nCreated By : Mohamed Hisham10/10/2016\r\nDiscussed with : Mohamed Rishad, Reyaas Rafaideen and Mohamed Shafry 09/10/2016\r\nReviewed By : Mohamed Zahlan 09/10/2016';

-- ----------------------------
-- Records of srp_erp_pos_diningtables
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_franchisemaster
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_franchisemaster`;
CREATE TABLE `srp_erp_pos_franchisemaster` (
  `franchiseID` int(11) NOT NULL AUTO_INCREMENT,
  `companyID` int(11) DEFAULT NULL,
  `warehouseAutoID` int(11) DEFAULT NULL,
  `franchiseName` varchar(255) DEFAULT NULL,
  `royaltyPercentage` int(11) DEFAULT NULL,
  `supplierAutoID` int(11) DEFAULT NULL,
  `royaltyLiabilityGLAutoID` int(11) DEFAULT NULL,
  `royaltyExpenseGLAutoID` int(11) DEFAULT NULL,
  PRIMARY KEY (`franchiseID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of srp_erp_pos_franchisemaster
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_giftcardmaster
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_giftcardmaster`;
CREATE TABLE `srp_erp_pos_giftcardmaster` (
  `cardMasterID` int(11) NOT NULL AUTO_INCREMENT,
  `barcode` varchar(600) DEFAULT NULL,
  `isActive` int(1) DEFAULT '1' COMMENT 'if deactivated  0',
  `deactivationRemarks` longtext,
  `outletID` int(11) DEFAULT NULL,
  `cardExpiryInMonths` int(3) DEFAULT '12' COMMENT 'months only',
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) DEFAULT NULL,
  `createdPCID` varchar(45) DEFAULT NULL,
  `createdUserID` varchar(45) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `createdUserGroup` varchar(45) DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`cardMasterID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of srp_erp_pos_giftcardmaster
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_invoice
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_invoice`;
CREATE TABLE `srp_erp_pos_invoice` (
  `invoiceID` int(11) NOT NULL AUTO_INCREMENT,
  `documentSystemCode` varchar(45) DEFAULT NULL,
  `documentCode` varchar(45) DEFAULT NULL,
  `serialNo` int(11) DEFAULT NULL,
  `invoiceSequenceNo` int(11) DEFAULT NULL,
  `invoiceCode` varchar(50) DEFAULT NULL,
  `financialYearID` int(11) DEFAULT NULL,
  `financialPeriodID` int(11) DEFAULT NULL,
  `FYBegin` date DEFAULT NULL,
  `FYEnd` date DEFAULT NULL,
  `FYPeriodDateFrom` date DEFAULT NULL,
  `FYPeriodDateTo` date DEFAULT NULL,
  `customerID` int(11) DEFAULT NULL,
  `customerCode` varchar(100) DEFAULT NULL,
  `counterID` int(11) DEFAULT NULL,
  `shiftID` int(11) DEFAULT NULL,
  `memberID` varchar(100) DEFAULT NULL,
  `memberName` varchar(200) DEFAULT NULL,
  `memberContactNo` varchar(20) DEFAULT NULL,
  `memberEmail` varchar(45) DEFAULT NULL,
  `invoiceDate` date DEFAULT NULL,
  `subTotal` double DEFAULT '0',
  `discountPer` double DEFAULT '0',
  `discountAmount` double DEFAULT '0',
  `generalDiscountPercentage` double(3,0) DEFAULT '0',
  `generalDiscountAmount` double DEFAULT '0',
  `netTotal` double DEFAULT NULL,
  `paidAmount` double DEFAULT NULL,
  `balanceAmount` double DEFAULT NULL,
  `cashAmount` double DEFAULT NULL,
  `chequeAmount` double DEFAULT NULL,
  `chequeNo` varchar(45) DEFAULT '0',
  `chequeDate` date DEFAULT NULL,
  `cardAmount` double DEFAULT NULL,
  `creditNoteID` int(11) DEFAULT NULL,
  `creditNoteAmount` double DEFAULT NULL,
  `giftCardID` int(11) DEFAULT NULL,
  `giftCardAmount` double DEFAULT NULL,
  `cardNumber` int(100) DEFAULT NULL,
  `cardRefNo` int(100) DEFAULT NULL,
  `cardBank` int(255) DEFAULT NULL,
  `isCreditSales` int(1) DEFAULT '0',
  `creditSalesAmount` double DEFAULT NULL,
  `wareHouseAutoID` int(11) DEFAULT NULL,
  `wareHouseCode` varchar(45) DEFAULT NULL,
  `wareHouseLocation` varchar(100) DEFAULT NULL,
  `wareHouseDescription` varchar(255) DEFAULT NULL,
  `transactionCurrencyID` int(11) DEFAULT NULL,
  `transactionCurrency` varchar(45) DEFAULT NULL COMMENT 'Document transaction currency',
  `transactionExchangeRate` double DEFAULT '0' COMMENT 'Always 1',
  `transactionCurrencyDecimalPlaces` int(1) DEFAULT '2' COMMENT 'Decimal places of transaction currency ',
  `companyLocalCurrencyID` int(11) DEFAULT NULL,
  `companyLocalCurrency` varchar(45) DEFAULT NULL COMMENT 'Local currency of company in company master',
  `companyLocalExchangeRate` double DEFAULT '0' COMMENT 'Exchange rate against transaction currency',
  `companyLocalCurrencyDecimalPlaces` int(1) DEFAULT '2' COMMENT 'Decimal places of company currency',
  `companyReportingCurrencyID` int(11) DEFAULT NULL,
  `companyReportingCurrency` varchar(45) DEFAULT NULL COMMENT 'Reporting currency of company in company master',
  `companyReportingExchangeRate` double DEFAULT '0' COMMENT 'Exchange rate against transaction currency ',
  `companyReportingCurrencyDecimalPlaces` int(1) DEFAULT '2' COMMENT 'Decimal places of company currency',
  `customerCurrencyID` int(11) DEFAULT NULL,
  `customerCurrency` varchar(45) DEFAULT NULL COMMENT 'Default currency of supplier ',
  `customerCurrencyExchangeRate` double DEFAULT '0' COMMENT 'Exchange rate against transaction currency',
  `customerCurrencyDecimalPlaces` int(1) DEFAULT '2' COMMENT 'Decimal places of Supplier currency',
  `segmentID` int(11) DEFAULT NULL,
  `segmentCode` varchar(45) DEFAULT NULL,
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) DEFAULT NULL,
  `customerReceivableAutoID` int(11) DEFAULT NULL,
  `customerReceivableSystemGLCode` varchar(45) DEFAULT NULL,
  `customerReceivableGLAccount` varchar(45) DEFAULT NULL,
  `customerReceivableDescription` varchar(255) DEFAULT NULL,
  `customerReceivableType` varchar(3) DEFAULT NULL,
  `bankGLAutoID` int(11) DEFAULT NULL,
  `bankSystemGLCode` varchar(45) DEFAULT NULL,
  `bankGLAccount` varchar(45) DEFAULT NULL,
  `bankGLDescription` varchar(255) DEFAULT NULL,
  `bankGLType` varchar(3) DEFAULT NULL,
  `bankCurrencyID` int(11) DEFAULT NULL,
  `bankCurrency` varchar(45) DEFAULT NULL COMMENT 'Document transaction currency',
  `bankCurrencyExchangeRate` double DEFAULT '0' COMMENT 'Always 1',
  `bankCurrencyDecimalPlaces` int(1) DEFAULT '2' COMMENT 'Decimal places of transaction currency ',
  `bankCurrencyAmount` double DEFAULT NULL,
  `createdUserGroup` int(11) DEFAULT NULL,
  `createdPCID` varchar(45) DEFAULT NULL,
  `createdUserID` varchar(45) DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`invoiceID`) USING BTREE,
  UNIQUE KEY `grvAutoID_UNIQUE` (`invoiceID`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of srp_erp_pos_invoice
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_invoicedetail
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_invoicedetail`;
CREATE TABLE `srp_erp_pos_invoicedetail` (
  `invoiceDetailsID` int(11) NOT NULL AUTO_INCREMENT,
  `invoiceID` int(11) DEFAULT NULL,
  `itemAutoID` int(11) DEFAULT NULL,
  `itemSystemCode` varchar(45) DEFAULT NULL,
  `itemDescription` longtext,
  `itemCategory` varchar(45) DEFAULT NULL,
  `financeCategory` int(1) DEFAULT NULL,
  `itemFinanceCategory` int(11) DEFAULT '0',
  `itemFinanceCategorySub` int(11) DEFAULT '0',
  `defaultUOM` varchar(45) DEFAULT NULL,
  `unitOfMeasure` varchar(45) DEFAULT NULL,
  `conversionRateUOM` double DEFAULT '0',
  `expenseGLAutoID` int(11) DEFAULT NULL,
  `expenseGLCode` varchar(45) DEFAULT NULL,
  `expenseSystemGLCode` varchar(45) DEFAULT NULL,
  `expenseGLDescription` varchar(255) DEFAULT NULL,
  `expenseGLType` varchar(5) DEFAULT NULL,
  `revenueGLAutoID` int(11) DEFAULT NULL,
  `revenueGLCode` varchar(45) DEFAULT NULL,
  `revenueSystemGLCode` varchar(45) DEFAULT NULL,
  `revenueGLDescription` varchar(255) DEFAULT NULL,
  `revenueGLType` varchar(5) DEFAULT NULL,
  `assetGLAutoID` int(11) DEFAULT NULL,
  `assetGLCode` varchar(45) DEFAULT NULL,
  `assetSystemGLCode` varchar(45) DEFAULT NULL,
  `assetGLDescription` varchar(255) DEFAULT NULL,
  `assetGLType` varchar(5) DEFAULT NULL,
  `qty` double DEFAULT '0',
  `price` double DEFAULT NULL,
  `discountPer` double DEFAULT NULL,
  `discountAmount` double DEFAULT '0' COMMENT 'individual discount amount',
  `generalDiscountPercentage` double(3,0) DEFAULT '0',
  `generalDiscountAmount` double(11,0) DEFAULT '0',
  `wacAmount` double DEFAULT '0',
  `transactionCurrencyID` int(11) DEFAULT NULL,
  `transactionCurrency` varchar(3) DEFAULT NULL,
  `transactionAmountBeforeDiscount` double DEFAULT '0',
  `transactionAmount` double DEFAULT NULL,
  `transactionCurrencyDecimalPlaces` tinyint(1) DEFAULT NULL,
  `transactionExchangeRate` double DEFAULT NULL,
  `companyLocalCurrencyID` int(11) DEFAULT NULL,
  `companyLocalCurrency` varchar(3) DEFAULT NULL,
  `companyLocalAmount` double DEFAULT NULL,
  `companyLocalExchangeRate` double DEFAULT NULL,
  `companyLocalCurrencyDecimalPlaces` tinyint(1) DEFAULT NULL,
  `companyReportingCurrencyID` int(11) DEFAULT NULL,
  `companyReportingCurrency` varchar(3) DEFAULT NULL,
  `companyReportingAmount` double DEFAULT NULL,
  `companyReportingCurrencyDecimalPlaces` tinyint(1) DEFAULT NULL,
  `companyReportingExchangeRate` double DEFAULT NULL,
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) DEFAULT NULL,
  `createdUserGroup` int(11) DEFAULT NULL,
  `createdPCID` varchar(45) DEFAULT NULL,
  `createdUserID` varchar(45) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`invoiceDetailsID`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of srp_erp_pos_invoicedetail
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_invoicehold
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_invoicehold`;
CREATE TABLE `srp_erp_pos_invoicehold` (
  `invoiceID` int(11) NOT NULL AUTO_INCREMENT,
  `documentSystemCode` varchar(45) DEFAULT NULL,
  `documentCode` varchar(45) DEFAULT NULL,
  `serialNo` int(11) DEFAULT NULL,
  `customerID` int(11) DEFAULT NULL,
  `customerCode` varchar(100) DEFAULT NULL,
  `invoiceDate` date DEFAULT NULL,
  `netTotal` double DEFAULT NULL,
  `localNetTotal` varchar(255) DEFAULT NULL,
  `reportingNetTotal` varchar(255) DEFAULT NULL,
  `discountPer` double DEFAULT NULL,
  `discountAmount` double DEFAULT NULL,
  `localDiscountAmount` double(255,0) DEFAULT NULL,
  `reportingDiscountAmount` double(255,0) DEFAULT NULL,
  `paidAmount` double DEFAULT NULL,
  `localPaidAmount` varchar(255) DEFAULT NULL,
  `reportingPaidAmount` varchar(255) DEFAULT NULL,
  `balanceAmount` double DEFAULT NULL,
  `localBalanceAmount` varchar(255) DEFAULT NULL,
  `reportingBalanceAmount` varchar(255) DEFAULT NULL,
  `cashAmount` double DEFAULT NULL,
  `chequeAmount` double DEFAULT NULL,
  `cardAmount` double DEFAULT NULL,
  `creditNoteID` int(11) DEFAULT NULL,
  `creditNoteAmount` double DEFAULT NULL,
  `giftCardID` int(11) DEFAULT NULL,
  `giftCardAmount` double DEFAULT NULL,
  `cardNumber` int(100) DEFAULT NULL,
  `cardRefNo` int(100) DEFAULT NULL,
  `cardBank` int(255) DEFAULT NULL,
  `isInvoiced` int(1) DEFAULT '0',
  `wareHouseAutoID` int(11) DEFAULT NULL,
  `wareHouseCode` varchar(45) DEFAULT NULL,
  `wareHouseLocation` varchar(100) DEFAULT NULL,
  `wareHouseDescription` varchar(255) DEFAULT NULL,
  `transactionCurrencyID` int(11) DEFAULT NULL,
  `transactionCurrency` varchar(45) DEFAULT NULL COMMENT 'Document transaction currency',
  `transactionExchangeRate` double DEFAULT '0' COMMENT 'Always 1',
  `transactionCurrencyDecimalPlaces` int(1) DEFAULT '2' COMMENT 'Decimal places of transaction currency ',
  `companyLocalCurrencyID` int(11) DEFAULT NULL,
  `companyLocalCurrency` varchar(45) DEFAULT NULL COMMENT 'Local currency of company in company master',
  `companyLocalExchangeRate` double DEFAULT '0' COMMENT 'Exchange rate against transaction currency',
  `companyLocalCurrencyDecimalPlaces` int(1) DEFAULT '2' COMMENT 'Decimal places of company currency',
  `companyReportingCurrencyID` int(11) DEFAULT NULL,
  `companyReportingCurrency` varchar(45) DEFAULT NULL COMMENT 'Reporting currency of company in company master',
  `companyReportingExchangeRate` double DEFAULT '0' COMMENT 'Exchange rate against transaction currency ',
  `companyReportingCurrencyDecimalPlaces` int(1) DEFAULT '2' COMMENT 'Decimal places of company currency',
  `segmentID` int(11) DEFAULT NULL,
  `segmentCode` varchar(45) DEFAULT NULL,
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) DEFAULT NULL,
  `createdUserGroup` int(11) DEFAULT NULL,
  `createdPCID` varchar(45) DEFAULT NULL,
  `createdUserID` varchar(45) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`invoiceID`) USING BTREE,
  UNIQUE KEY `grvAutoID_UNIQUE` (`invoiceID`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='Table Use : system level table to assign GL type for received payments\r\nCreated By : Mohamed Hisham\r\nDiscussed with : Shafri and Nasik\r\nReviewed By : ';

-- ----------------------------
-- Records of srp_erp_pos_invoicehold
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_invoiceholddetail
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_invoiceholddetail`;
CREATE TABLE `srp_erp_pos_invoiceholddetail` (
  `invoiceDetailsID` int(11) NOT NULL AUTO_INCREMENT,
  `invoiceID` int(11) DEFAULT NULL,
  `itemAutoID` int(11) DEFAULT NULL,
  `itemSystemCode` varchar(45) DEFAULT NULL,
  `itemDescription` longtext,
  `itemCategory` varchar(45) DEFAULT NULL,
  `financeCategory` int(1) DEFAULT NULL,
  `itemFinanceCategory` int(11) DEFAULT '0',
  `itemFinanceCategorySub` int(11) DEFAULT '0',
  `PLGLAutoID` int(11) DEFAULT NULL,
  `PLSystemGLCode` varchar(45) DEFAULT NULL,
  `PLGLCode` varchar(45) DEFAULT '0',
  `PLDescription` varchar(255) DEFAULT NULL,
  `PLType` varchar(3) DEFAULT NULL,
  `BLGLAutoID` int(11) DEFAULT NULL,
  `BLSystemGLCode` varchar(255) DEFAULT NULL,
  `BLGLCode` varchar(45) DEFAULT '0',
  `BLDescription` varchar(255) DEFAULT NULL,
  `BLType` varchar(3) DEFAULT NULL,
  `defaultUOM` varchar(45) DEFAULT NULL,
  `unitOfMeasure` varchar(45) DEFAULT NULL,
  `conversionRateUOM` double DEFAULT '0',
  `qty` double DEFAULT '0',
  `price` double DEFAULT NULL,
  `discountPer` double DEFAULT NULL,
  `transactionCurrencyID` int(11) DEFAULT NULL,
  `transactionCurrency` varchar(3) DEFAULT NULL,
  `transactionAmount` double(255,0) DEFAULT NULL,
  `transactionCurrencyDecimalPlaces` int(3) DEFAULT NULL,
  `transactionExchangeRate` double(255,0) DEFAULT NULL,
  `companyLocalCurrencyID` int(11) DEFAULT NULL,
  `companyLocalCurrency` varchar(3) DEFAULT NULL,
  `companyLocalAmount` double(255,0) DEFAULT NULL,
  `companyLocalExchangeRate` double(255,0) DEFAULT NULL,
  `companyLocalCurrencyDecimalPlaces` int(11) DEFAULT NULL,
  `companyReportingCurrencyID` int(11) DEFAULT NULL,
  `companyReportingCurrency` varchar(3) DEFAULT NULL,
  `companyReportingAmount` double(255,0) DEFAULT NULL,
  `companyReportingCurrencyDecimalPlaces` int(11) DEFAULT NULL,
  `companyReportingExchangeRate` double(255,0) DEFAULT NULL,
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) DEFAULT NULL,
  `createdUserGroup` int(11) DEFAULT NULL,
  `createdPCID` varchar(45) DEFAULT NULL,
  `createdUserID` varchar(45) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`invoiceDetailsID`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of srp_erp_pos_invoiceholddetail
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_invoicepayments
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_invoicepayments`;
CREATE TABLE `srp_erp_pos_invoicepayments` (
  `PaymentID` int(11) NOT NULL AUTO_INCREMENT,
  `invoiceID` int(11) DEFAULT NULL COMMENT 'POS Invoice ID',
  `paymentConfigMasterID` int(11) DEFAULT NULL,
  `paymentConfigDetailID` int(11) DEFAULT NULL,
  `glAccountType` int(2) DEFAULT NULL COMMENT 'pos_paymentconfigmaster.glAccountType',
  `GLCode` int(11) DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `reference` varchar(600) DEFAULT NULL,
  `customerAutoID` int(11) DEFAULT '0' COMMENT 'ERP Customer master ID',
  `isAdvancePayment` int(1) DEFAULT '0' COMMENT '1 - advancePayment, 0 not an advance payment',
  `createdUserGroup` int(11) DEFAULT NULL,
  `createdPCID` varchar(45) DEFAULT NULL,
  `createdUserID` varchar(45) DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`PaymentID`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of srp_erp_pos_invoicepayments
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_javaappredeemhistory
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_javaappredeemhistory`;
CREATE TABLE `srp_erp_pos_javaappredeemhistory` (
  `javaAppHistoryID` int(11) NOT NULL AUTO_INCREMENT,
  `menuSalesID` int(11) DEFAULT NULL,
  `outletID` int(11) NOT NULL DEFAULT '0',
  `appPIN` varchar(500) DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) DEFAULT NULL,
  `createdUserGroup` int(11) DEFAULT NULL,
  `createdPCID` varchar(45) DEFAULT NULL,
  `createdUserID` varchar(45) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  `is_sync` int(1) DEFAULT '0',
  `id_store` int(255) NOT NULL DEFAULT '0',
  PRIMARY KEY (`javaAppHistoryID`,`id_store`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of srp_erp_pos_javaappredeemhistory
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_kitchenlocation
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_kitchenlocation`;
CREATE TABLE `srp_erp_pos_kitchenlocation` (
  `kitchenLocationID` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(255) DEFAULT NULL,
  `companyID` int(11) DEFAULT NULL,
  `outletID` int(11) DEFAULT NULL,
  PRIMARY KEY (`kitchenLocationID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of srp_erp_pos_kitchenlocation
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_kitchennotesamples
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_kitchennotesamples`;
CREATE TABLE `srp_erp_pos_kitchennotesamples` (
  `sampleID` int(11) NOT NULL AUTO_INCREMENT,
  `noteDescription` longtext,
  `companyID` int(11) DEFAULT NULL,
  `warehouseAutoID` int(11) DEFAULT NULL,
  `createdUserGroup` int(11) DEFAULT NULL,
  `createdPCID` varchar(45) DEFAULT NULL,
  `createdUserID` varchar(45) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`sampleID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of srp_erp_pos_kitchennotesamples
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_menucategory
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_menucategory`;
CREATE TABLE `srp_erp_pos_menucategory` (
  `menuCategoryID` int(11) NOT NULL AUTO_INCREMENT,
  `menuCategoryDescription` varchar(45) NOT NULL COMMENT 'Eg: Sandwithches, Beverages etc...',
  `image` varchar(255) DEFAULT NULL COMMENT 'Thumbnail Image',
  `revenueGLAutoID` int(11) DEFAULT NULL,
  `companyID` int(11) DEFAULT NULL,
  `sortOrder` int(11) DEFAULT '0',
  `isPack` int(1) DEFAULT '0' COMMENT '0 - not pack, 1 pack (bunlde)',
  `masterLevelID` int(11) DEFAULT NULL COMMENT 'if null no level exist, this is to maintain the level ',
  `levelNo` int(11) DEFAULT '0' COMMENT 'master level 0 ',
  `bgColor` varchar(100) DEFAULT NULL COMMENT 'background color for category',
  `isActive` int(1) DEFAULT '1',
  `showImageYN` int(1) DEFAULT '0',
  `isDeleted` int(1) DEFAULT '0',
  `deletedBy` varchar(45) DEFAULT NULL,
  `deletedDatetime` datetime DEFAULT NULL,
  `createdPCID` varchar(45) DEFAULT NULL,
  `createdUserID` varchar(45) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `createdUserGroup` varchar(45) DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `timeStamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`menuCategoryID`),
  KEY `showImageYN` (`showImageYN`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table Use : To Create Categories of Restaurant Menus (Eg: Beverages, Side Dishes, Fast Foods etc..)\r\nCreated By : Mohamed Hisham 09/10/2016\r\nDiscussed with : Mohamed Rishad, Reyaas Rafaideen and Mohamed Shafry 09/10/2016\r\nReviewed By : Mohamed Zahlan 09/10/2016';

-- ----------------------------
-- Records of srp_erp_pos_menucategory
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_menudetails
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_menudetails`;
CREATE TABLE `srp_erp_pos_menudetails` (
  `menuDetailID` int(11) NOT NULL AUTO_INCREMENT,
  `menuDetailDescription` varchar(100) NOT NULL,
  `menuMasterID` int(11) DEFAULT NULL,
  `isYield` int(1) DEFAULT '0' COMMENT 'if added from yield table 1 (optional mix of ingedients )',
  `yieldAutoID` int(11) DEFAULT NULL,
  `itemAutoID` int(11) DEFAULT NULL,
  `qty` double DEFAULT '0',
  `UOM` varchar(45) DEFAULT NULL,
  `uomID` int(11) DEFAULT NULL,
  `cost` double DEFAULT '0',
  `oldCost` double DEFAULT NULL,
  `actualInventoryCost` double DEFAULT '0' COMMENT 'load based on the policy, maintain exact cost of the item master',
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) DEFAULT NULL,
  `segmentID` int(11) DEFAULT NULL,
  `segmentCode` varchar(50) DEFAULT NULL,
  `createdPCID` varchar(45) DEFAULT NULL,
  `createdUserID` varchar(45) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `createdUserGroup` varchar(45) DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `timeStamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`menuDetailID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table Use : contains ingredients , raw material qty and cost for Menus \r\nCreated By : Mohamed Hisham 09/10/2016\r\nDiscussed with : Mohamed Rishad, Reyaas Rafaideen and Mohamed Shafry 09/10/2016\r\nReviewed By : Mohamed Zahlan 09/10/2016';

-- ----------------------------
-- Records of srp_erp_pos_menudetails
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_menumaster
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_menumaster`;
CREATE TABLE `srp_erp_pos_menumaster` (
  `menuMasterID` int(11) NOT NULL AUTO_INCREMENT,
  `menuMasterDescription` varchar(100) NOT NULL,
  `menuImage` varchar(255) DEFAULT NULL,
  `menuCategoryID` int(11) NOT NULL,
  `menuCost` double DEFAULT '0',
  `barcode` varchar(255) DEFAULT NULL,
  `sellingPrice` double DEFAULT '0',
  `pricewithoutTax` double DEFAULT '0',
  `revenueGLAutoID` int(11) DEFAULT NULL,
  `TAXpercentage` double(3,0) DEFAULT NULL,
  `totalTaxAmount` double DEFAULT '0' COMMENT 'to matintain total tax amount from srp_erp_pos_menutaxes',
  `taxMasterID` int(11) DEFAULT NULL,
  `totalServiceCharge` double DEFAULT '0' COMMENT 'total service charge srp_erp_pos_menuservicecharge',
  `companyID` int(11) DEFAULT NULL,
  `menuStatus` int(1) DEFAULT '1' COMMENT '1 Active, 0 inActive',
  `kotID` int(11) DEFAULT '0' COMMENT 'Kitchen order Ticket ID',
  `isPack` int(1) DEFAULT '0' COMMENT '0 - not pax, 1 - pax (bundle) ',
  `isVeg` int(1) DEFAULT '0' COMMENT '0 : Non-Veg,  1 : vegetarian',
  `isAddOn` int(1) DEFAULT '0',
  `showImageYN` int(1) DEFAULT '0',
  `menuSizeID` int(11) DEFAULT NULL,
  `sortOrder` int(5) DEFAULT '0',
  `sortOder` int(5) DEFAULT '0',
  `isDeleted` int(1) DEFAULT '0' COMMENT '0 notDeleted, 1 deleted',
  `deletedBy` int(4) DEFAULT NULL,
  `deletedDatetime` datetime DEFAULT NULL,
  `createdPCID` varchar(45) DEFAULT NULL,
  `createdUserID` varchar(45) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `createdUserGroup` varchar(45) DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `timeStamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`menuMasterID`),
  KEY `barcode` (`barcode`) USING BTREE,
  KEY `showImageYN` (`showImageYN`) USING BTREE,
  KEY `companyID` (`companyID`) USING BTREE,
  KEY `kotID` (`kotID`) USING BTREE,
  KEY `isPack` (`isPack`) USING BTREE,
  KEY `isVeg` (`isVeg`) USING BTREE,
  KEY `isAddOn` (`isAddOn`) USING BTREE,
  KEY `sortOrder` (`sortOrder`) USING BTREE,
  KEY `isDeleted` (`isDeleted`) USING BTREE,
  KEY `menuStatus` (`menuStatus`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table Use : Used To Create Menus under Menu Categories ( Eg: Under Sandwithces Category -> Menu -> Club Sandwitch)\r\nCreated By : Mohamed Hisham 09/10/2016\r\nDiscussed with : Mohamed Rishad, Reyaas Rafaideen and Mohamed Shafry 09/10/2016\r\nReviewed By : Mohamed Zahlan 09/10/2016';

-- ----------------------------
-- Records of srp_erp_pos_menumaster
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_menupackcategory
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_menupackcategory`;
CREATE TABLE `srp_erp_pos_menupackcategory` (
  `menuPackCategoryID` int(11) NOT NULL AUTO_INCREMENT,
  `valuePackID` int(11) DEFAULT NULL,
  `menuCategoryID` int(11) DEFAULT NULL,
  `qty` int(11) DEFAULT '1' COMMENT 'how many item that user can select for this category',
  `createdDatetime` datetime DEFAULT NULL,
  `createdBy` varchar(255) DEFAULT NULL,
  `createdPc` varchar(255) DEFAULT NULL,
  `timestamp` datetime DEFAULT NULL,
  PRIMARY KEY (`menuPackCategoryID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of srp_erp_pos_menupackcategory
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_menupackgroupmaster
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_menupackgroupmaster`;
CREATE TABLE `srp_erp_pos_menupackgroupmaster` (
  `groupMasterID` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(255) DEFAULT NULL,
  `packMenuID` int(11) DEFAULT NULL,
  `qty` int(11) DEFAULT '1',
  `IsRequired` int(11) DEFAULT '0' COMMENT '1 required, 0 optional ',
  `createdBy` varchar(20) DEFAULT NULL,
  `createdPc` varchar(100) DEFAULT NULL,
  `createdDatetime` datetime DEFAULT NULL,
  `timestamp` datetime DEFAULT NULL,
  PRIMARY KEY (`groupMasterID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of srp_erp_pos_menupackgroupmaster
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_menupackitem
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_menupackitem`;
CREATE TABLE `srp_erp_pos_menupackitem` (
  `menuPackItemID` int(11) NOT NULL AUTO_INCREMENT,
  `PackMenuID` int(10) NOT NULL DEFAULT '0',
  `menuID` int(10) DEFAULT NULL,
  `menuCategoryID` int(10) DEFAULT NULL,
  `isRequired` int(1) DEFAULT '0' COMMENT '0 - optional item, 1- required',
  `createdBy` varchar(20) DEFAULT NULL,
  `createdDatetime` datetime DEFAULT NULL,
  `createdPC` varchar(255) DEFAULT NULL,
  `timestamp` datetime DEFAULT NULL,
  PRIMARY KEY (`menuPackItemID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of srp_erp_pos_menupackitem
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_menusalesitemdetails
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_menusalesitemdetails`;
CREATE TABLE `srp_erp_pos_menusalesitemdetails` (
  `menuSalesItemDetailID` int(11) NOT NULL AUTO_INCREMENT,
  `menuSalesItemID` int(11) NOT NULL,
  `menuSalesID` int(11) DEFAULT NULL,
  `itemAutoID` int(11) DEFAULT NULL,
  `qty` double DEFAULT '0',
  `UOM` varchar(100) DEFAULT NULL,
  `UOMID` int(11) DEFAULT NULL,
  `cost` double DEFAULT '0',
  `actualInventoryCost` double DEFAULT '0' COMMENT 'load based on the policy, maintain exact cost of the item master',
  `menuID` int(11) DEFAULT '0',
  `menuSalesQty` double DEFAULT NULL,
  `costGLAutoID` int(11) DEFAULT NULL COMMENT 'Raw material cost GL account from item master table',
  `assetGLAutoID` int(11) DEFAULT NULL COMMENT 'Raw material asset GL account from item master table',
  `isWastage` int(1) DEFAULT '0',
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) DEFAULT NULL,
  `segmentID` int(11) DEFAULT NULL,
  `segmentCode` varchar(50) DEFAULT NULL,
  `createdPCID` varchar(45) DEFAULT NULL,
  `createdUserID` varchar(45) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `createdUserGroup` varchar(45) DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `timeStamp` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `is_sync` int(11) DEFAULT '0',
  `id_store` int(11) NOT NULL,
  PRIMARY KEY (`menuSalesItemDetailID`,`id_store`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of srp_erp_pos_menusalesitemdetails
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_menusalesitems
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_menusalesitems`;
CREATE TABLE `srp_erp_pos_menusalesitems` (
  `menuSalesItemID` int(11) NOT NULL AUTO_INCREMENT,
  `menuSalesID` int(11) NOT NULL,
  `menuID` int(11) DEFAULT NULL,
  `menuCategoryID` int(11) DEFAULT NULL,
  `warehouseMenuID` int(11) DEFAULT NULL,
  `warehouseMenuCategoryID` int(11) DEFAULT NULL,
  `defaultUOM` varchar(45) DEFAULT NULL,
  `unitOfMeasure` varchar(45) DEFAULT NULL,
  `conversionRateUOM` double DEFAULT '0',
  `qty` double DEFAULT '0',
  `menuSalesPrice` double DEFAULT NULL,
  `salesPriceSubTotal` double DEFAULT '0' COMMENT 'qty * per item  : total without discount ',
  `salesPriceAfterDiscount` double DEFAULT '0',
  `salesPriceNetTotal` double DEFAULT '0' COMMENT '(qty * per item ) - discount ',
  `netRevenueTotal` double DEFAULT NULL,
  `totalMenuTaxAmount` double DEFAULT NULL,
  `totalMenuTaxAmountAfterDiscount` double(255,0) DEFAULT NULL,
  `totalMenuServiceCharge` double DEFAULT NULL,
  `totalMenuServiceChargeAfterDiscount` double(255,0) DEFAULT NULL,
  `discountPer` double DEFAULT NULL,
  `discountAmount` double DEFAULT '0',
  `menuCost` double DEFAULT NULL,
  `kotID` int(11) DEFAULT NULL,
  `kitchenNote` varchar(600) DEFAULT NULL,
  `KOTAlarm` int(11) DEFAULT '0',
  `KOTFrontPrint` int(1) DEFAULT '0' COMMENT 'realated to Send KOT button in the front',
  `parentMenuSalesItemID` int(11) DEFAULT '0' COMMENT 'Add-On ID',
  `isSamplePrinted` int(1) DEFAULT '0' COMMENT '0- No 1 - Yes',
  `TAXpercentage` double(3,0) DEFAULT NULL,
  `TAXAmount` double DEFAULT NULL,
  `taxMasterID` int(11) DEFAULT NULL,
  `isTaxEnabled` int(1) DEFAULT NULL COMMENT '0 = tax amount will be added to netRevenueTotal & salesPriceNetTotal.  1 = Tax will separate in different table',
  `transactionCurrencyID` int(11) DEFAULT NULL,
  `transactionCurrency` varchar(3) DEFAULT NULL,
  `transactionAmount` double DEFAULT NULL,
  `transactionCurrencyDecimalPlaces` tinyint(1) DEFAULT NULL,
  `transactionExchangeRate` double DEFAULT NULL,
  `companyLocalCurrencyID` int(11) DEFAULT NULL,
  `companyLocalCurrency` varchar(3) DEFAULT NULL,
  `companyLocalAmount` double(255,0) DEFAULT NULL,
  `companyLocalExchangeRate` double(255,0) DEFAULT NULL,
  `companyLocalCurrencyDecimalPlaces` tinyint(1) DEFAULT NULL,
  `companyReportingCurrencyID` int(11) DEFAULT NULL,
  `companyReportingCurrency` varchar(3) DEFAULT NULL,
  `companyReportingAmount` double(255,0) DEFAULT NULL,
  `companyReportingCurrencyDecimalPlaces` tinyint(1) DEFAULT NULL,
  `companyReportingExchangeRate` double(255,0) DEFAULT NULL,
  `isOrderPending` int(1) DEFAULT '1' COMMENT '1 - pending , 0 not pending',
  `isOrderInProgress` int(1) DEFAULT '0' COMMENT '1 - on going/cooking , 0 not taken yet',
  `isOrderCompleted` int(1) DEFAULT '0' COMMENT '1 ready to deliver , 0 not ready',
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) DEFAULT NULL,
  `revenueGLAutoID` int(11) DEFAULT NULL,
  `remarkes` longtext,
  `createdUserGroup` int(11) DEFAULT NULL,
  `createdPCID` varchar(45) DEFAULT NULL,
  `createdUserID` varchar(45) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  `is_sync` int(11) DEFAULT '0',
  `id_store` int(11) NOT NULL,
  PRIMARY KEY (`menuSalesItemID`,`id_store`) USING BTREE,
  KEY `isSamplePrinted` (`isSamplePrinted`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of srp_erp_pos_menusalesitems
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_menusalesmaster
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_menusalesmaster`;
CREATE TABLE `srp_erp_pos_menusalesmaster` (
  `menuSalesID` int(11) NOT NULL AUTO_INCREMENT,
  `invoiceSequenceNo` int(11) DEFAULT '0',
  `invoiceCode` varchar(100) DEFAULT NULL,
  `wareHouseAutoID` int(11) NOT NULL,
  `documentCode` varchar(45) DEFAULT NULL,
  `serialNo` int(11) DEFAULT NULL,
  `customerName` varchar(500) DEFAULT NULL,
  `customerTelephone` varchar(100) DEFAULT NULL,
  `customerTypeID` int(11) DEFAULT NULL COMMENT 'FK of customer type master',
  `customerID` int(11) DEFAULT NULL,
  `customerCode` varchar(100) DEFAULT NULL,
  `deliveryPersonID` int(11) DEFAULT NULL,
  `deliveryCommission` float(3,0) DEFAULT '0' COMMENT 'Percentage',
  `deliveryCommissionAmount` double DEFAULT NULL,
  `customerCommission` int(3) DEFAULT '0' COMMENT 'Percentage',
  `counterID` int(11) DEFAULT NULL,
  `shiftID` int(11) DEFAULT NULL,
  `menuSalesDate` date DEFAULT NULL,
  `menuCost` double DEFAULT '0' COMMENT 'actual cost from menu master',
  `totalQty` double DEFAULT '0',
  `subTotal` double DEFAULT '0',
  `grossTotal` double DEFAULT '0' COMMENT 'total without discount',
  `grossAmount` double NOT NULL DEFAULT '0',
  `discountPer` double DEFAULT NULL,
  `discountAmount` double DEFAULT NULL,
  `totalTaxPercentage` double DEFAULT NULL,
  `totalTaxAmount` double DEFAULT NULL,
  `serviceCharge` double DEFAULT NULL,
  `netTotal` double DEFAULT NULL,
  `netRevenueTotal` double DEFAULT NULL,
  `paidAmount` double DEFAULT NULL COMMENT 'actually paid by customer',
  `balanceAmount` double DEFAULT NULL,
  `cashReceivedAmount` double DEFAULT NULL COMMENT 'actually customer paid by cash there will be return',
  `cashAmount` double DEFAULT '0',
  `chequeAmount` double DEFAULT NULL,
  `chequeNo` varchar(45) DEFAULT '0',
  `chequeDate` date DEFAULT NULL,
  `cardAmount` double DEFAULT NULL,
  `creditNoteID` int(11) DEFAULT NULL,
  `creditNoteAmount` double DEFAULT NULL,
  `giftCardID` int(11) DEFAULT NULL,
  `giftCardAmount` double DEFAULT NULL,
  `cardNumber` int(100) DEFAULT NULL,
  `cardRefNo` int(100) DEFAULT NULL,
  `cardBank` int(255) DEFAULT NULL,
  `paymentMethod` int(11) DEFAULT NULL COMMENT '1-card , 2-visa card, 3-master, 4-check ',
  `isHold` int(11) DEFAULT '1',
  `holdByUserID` varchar(10) DEFAULT NULL,
  `holdByUsername` varchar(255) DEFAULT NULL,
  `holdPC` varchar(255) DEFAULT NULL,
  `holdDatetime` datetime DEFAULT NULL,
  `holdRemarks` varchar(255) DEFAULT NULL,
  `transactionCurrencyID` int(11) DEFAULT NULL,
  `transactionCurrency` varchar(45) DEFAULT NULL COMMENT 'Document transaction currency',
  `transactionExchangeRate` double DEFAULT '1' COMMENT 'Always 1',
  `transactionCurrencyDecimalPlaces` int(1) DEFAULT '2' COMMENT 'Decimal places of transaction currency ',
  `companyLocalCurrencyID` int(11) DEFAULT NULL,
  `companyLocalCurrency` varchar(45) DEFAULT NULL COMMENT 'Local currency of company in company master',
  `companyLocalExchangeRate` double DEFAULT '1' COMMENT 'Exchange rate against transaction currency',
  `companyLocalCurrencyDecimalPlaces` int(1) DEFAULT '2' COMMENT 'Decimal places of company currency',
  `companyReportingCurrencyID` int(11) DEFAULT NULL,
  `companyReportingCurrency` varchar(45) DEFAULT NULL COMMENT 'Reporting currency of company in company master',
  `companyReportingExchangeRate` double DEFAULT '0' COMMENT 'Exchange rate against transaction currency ',
  `companyReportingCurrencyDecimalPlaces` int(1) DEFAULT '2' COMMENT 'Decimal places of company currency',
  `customerCurrencyID` int(11) DEFAULT NULL,
  `customerCurrency` varchar(45) DEFAULT NULL COMMENT 'Default currency of supplier ',
  `customerCurrencyExchangeRate` double DEFAULT '0' COMMENT 'Exchange rate against transaction currency',
  `customerCurrencyAmount` double DEFAULT '0' COMMENT 'Transaction amount in supplier currency ',
  `customerCurrencyDecimalPlaces` int(1) DEFAULT '2' COMMENT 'Decimal places of Supplier currency',
  `segmentID` int(11) DEFAULT NULL,
  `segmentCode` varchar(45) DEFAULT NULL,
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) DEFAULT NULL,
  `customerReceivableAutoID` int(11) DEFAULT NULL,
  `commissionGLAutoID` int(11) DEFAULT NULL,
  `commisionLiabilityGLAutoID` int(11) DEFAULT NULL,
  `isOnTimeCommision` int(11) DEFAULT NULL,
  `bankGLAutoID` int(11) DEFAULT NULL,
  `bankCurrencyID` int(11) DEFAULT NULL,
  `bankCurrency` varchar(45) DEFAULT NULL COMMENT 'Document transaction currency',
  `bankCurrencyExchangeRate` double DEFAULT '0' COMMENT 'Always 1',
  `bankCurrencyDecimalPlaces` int(1) DEFAULT '2' COMMENT 'Decimal places of transaction currency ',
  `bankCurrencyAmount` double DEFAULT NULL,
  `salesDay` varchar(20) DEFAULT NULL,
  `salesDayNum` int(2) DEFAULT NULL,
  `isOrderPending` int(1) DEFAULT '0' COMMENT '1 - pending , 0 not pending ',
  `isOrderInProgress` int(1) DEFAULT '0' COMMENT '1 - on going/cooking , 0 not taken yet',
  `isOrderCompleted` int(1) DEFAULT '0' COMMENT '1 ready to deliver , 0 not ready',
  `tableID` int(11) DEFAULT NULL,
  `createdUserGroup` int(11) DEFAULT NULL,
  `createdPCID` varchar(45) DEFAULT NULL,
  `createdUserID` varchar(45) DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `is_sync` int(11) DEFAULT '0',
  `id_store` int(11) NOT NULL,
  `isVoid` int(1) DEFAULT '0' COMMENT '0 - not void, 1 canceled',
  `voidBy` int(11) DEFAULT NULL COMMENT 'bill cancelled by',
  `voidDatetime` datetime DEFAULT NULL,
  `isCreditSales` int(255) DEFAULT '0' COMMENT '0 - not credit sales , 1 - credit sales.',
  `documentMasterAutoID` int(11) DEFAULT NULL COMMENT 'Customer invoice master autoID',
  `documentSystemCode` varchar(45) NOT NULL DEFAULT '',
  `isDelivery` int(1) DEFAULT '0',
  `isPromotion` int(1) DEFAULT '0',
  `promotionID` int(11) DEFAULT NULL,
  `promotionDiscount` double DEFAULT '0',
  `KOTAlarm` int(1) DEFAULT '0',
  `promotionDiscountAmount` double DEFAULT '0',
  `tabUserID` int(11) DEFAULT NULL,
  `waiterID` int(11) DEFAULT '0' COMMENT 'crew ID ',
  `numberOfPacks` int(11) DEFAULT '0',
  `BOT` int(1) DEFAULT '0' COMMENT 'BOT is used in the Tablet window ',
  `BOTCreatedUser` int(11) DEFAULT NULL,
  `BOTCreatedDatetime` datetime DEFAULT NULL,
  `isFromTablet` int(1) DEFAULT '0' COMMENT 'if the record created from tablet window',
  PRIMARY KEY (`menuSalesID`,`id_store`),
  KEY `menuSalesID` (`menuSalesID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of srp_erp_pos_menusalesmaster
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_menusalespayments
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_menusalespayments`;
CREATE TABLE `srp_erp_pos_menusalespayments` (
  `menuSalesPaymentID` int(11) NOT NULL AUTO_INCREMENT,
  `wareHouseAutoID` int(11) NOT NULL DEFAULT '0',
  `menuSalesID` int(11) DEFAULT NULL,
  `paymentConfigMasterID` int(11) DEFAULT NULL,
  `paymentConfigDetailID` int(11) DEFAULT NULL,
  `glAccountType` int(2) DEFAULT NULL COMMENT 'pos_paymentconfigmaster.glAccountType',
  `GLCode` int(11) DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `reference` varchar(600) DEFAULT NULL,
  `customerAutoID` int(11) DEFAULT '0' COMMENT 'ERP Customer master ID',
  `isAdvancePayment` int(1) DEFAULT '0' COMMENT '1 - advancePayment, 0 not an advance payment',
  `createdUserGroup` int(11) DEFAULT NULL,
  `createdPCID` varchar(45) DEFAULT NULL,
  `createdUserID` varchar(45) DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `is_sync` int(1) DEFAULT '0',
  `id_store` int(255) NOT NULL DEFAULT '0',
  PRIMARY KEY (`menuSalesPaymentID`,`id_store`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of srp_erp_pos_menusalespayments
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_menusalesservicecharge
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_menusalesservicecharge`;
CREATE TABLE `srp_erp_pos_menusalesservicecharge` (
  `menusalesServiceChargeID` int(11) NOT NULL AUTO_INCREMENT,
  `wareHouseAutoID` int(11) NOT NULL DEFAULT '0',
  `menuSalesID` int(11) DEFAULT NULL,
  `menuServiceChargeID` int(11) DEFAULT NULL,
  `menuMasterID` int(11) DEFAULT NULL,
  `serviceChargePercentage` double DEFAULT '0',
  `serviceChargeAmount` double DEFAULT '0',
  `GLAutoID` int(11) DEFAULT NULL,
  `beforeDiscountTotalServiceCharge` double DEFAULT NULL,
  `menusalesDiscount` double DEFAULT NULL,
  `menusalesPromotionalDiscount` double DEFAULT NULL,
  `unitMenuServiceCharge` double DEFAULT NULL,
  `menusalesItemQty` int(11) DEFAULT NULL,
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `createdUserGroup` int(11) DEFAULT NULL,
  `createdPCID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `createdUserID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `modifiedPCID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `modifiedUserID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  `is_sync` int(1) DEFAULT '0',
  `id_store` int(255) NOT NULL DEFAULT '0',
  PRIMARY KEY (`menusalesServiceChargeID`,`id_store`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of srp_erp_pos_menusalesservicecharge
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_menusalestaxes
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_menusalestaxes`;
CREATE TABLE `srp_erp_pos_menusalestaxes` (
  `menuSalesTaxID` int(11) NOT NULL AUTO_INCREMENT,
  `wareHouseAutoID` int(11) NOT NULL DEFAULT '0',
  `menuSalesID` int(11) DEFAULT NULL,
  `menuSalesItemID` int(11) DEFAULT NULL,
  `menuID` int(11) DEFAULT NULL,
  `menutaxID` int(11) DEFAULT NULL,
  `taxmasterID` int(11) DEFAULT NULL,
  `GLCode` int(11) DEFAULT NULL,
  `taxPercentage` double DEFAULT NULL,
  `taxAmount` double DEFAULT NULL,
  `beforeDiscountTotalTaxAmount` double DEFAULT NULL,
  `menusalesDiscount` double DEFAULT NULL,
  `menusalesPromotionalDiscount` double DEFAULT NULL,
  `unitMenuTaxAmount` double DEFAULT NULL,
  `menusalesItemQty` int(11) DEFAULT NULL,
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `createdUserGroup` int(11) DEFAULT NULL,
  `createdPCID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `createdUserID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `modifiedPCID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `modifiedUserID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  `is_sync` int(1) DEFAULT '0',
  `id_store` int(255) NOT NULL DEFAULT '0',
  PRIMARY KEY (`menuSalesTaxID`,`id_store`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC COMMENT='transaction level tabled - created by shafri ';

-- ----------------------------
-- Records of srp_erp_pos_menusalestaxes
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_menuservicecharge
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_menuservicecharge`;
CREATE TABLE `srp_erp_pos_menuservicecharge` (
  `menuServiceChargeID` int(11) NOT NULL AUTO_INCREMENT,
  `menuMasterID` int(11) DEFAULT NULL,
  `serviceChargePercentage` double DEFAULT '0',
  `serviceChargeAmount` double DEFAULT '0',
  `GLAutoID` int(11) DEFAULT NULL,
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `createdUserGroup` int(11) DEFAULT NULL,
  `createdPCID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `createdUserID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `modifiedPCID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `modifiedUserID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`menuServiceChargeID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of srp_erp_pos_menuservicecharge
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_menusize
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_menusize`;
CREATE TABLE `srp_erp_pos_menusize` (
  `menuSizeID` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(100) DEFAULT NULL,
  `code` varchar(10) DEFAULT NULL,
  `colourCode` varchar(20) DEFAULT NULL,
  `companyID` int(11) DEFAULT NULL,
  `isActive` int(1) DEFAULT '1',
  `timestamp` datetime DEFAULT NULL,
  PRIMARY KEY (`menuSizeID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of srp_erp_pos_menusize
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_menutaxes
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_menutaxes`;
CREATE TABLE `srp_erp_pos_menutaxes` (
  `menutaxID` int(11) NOT NULL AUTO_INCREMENT,
  `menuMasterID` int(11) DEFAULT NULL,
  `taxmasterID` int(11) DEFAULT NULL,
  `taxPercentage` double DEFAULT '0',
  `taxAmount` double DEFAULT '0',
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `createdUserGroup` int(11) DEFAULT NULL,
  `createdPCID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `createdUserID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `modifiedPCID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `modifiedUserID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`menutaxID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='created by Shafry, \r\ndiscuessed with Hisham\r\nIdea by: Rilwan & Rishad';

-- ----------------------------
-- Records of srp_erp_pos_menutaxes
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_menutaxmaster
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_menutaxmaster`;
CREATE TABLE `srp_erp_pos_menutaxmaster` (
  `menuTaxAutoID` int(11) NOT NULL AUTO_INCREMENT,
  `taxDescription` varchar(200) NOT NULL,
  `taxShortCode` varchar(5) NOT NULL,
  `taxType` tinyint(1) DEFAULT '1' COMMENT 'sales tax -1 purchase tax -2',
  `isActive` tinyint(1) DEFAULT '1',
  `supplierAutoID` int(11) NOT NULL COMMENT 'Tax Authority - need to choose from supplier master',
  `supplierSystemCode` varchar(45) DEFAULT NULL,
  `supplierName` varchar(200) DEFAULT NULL,
  `supplierAddress` varchar(255) DEFAULT NULL,
  `supplierTelephone` int(15) DEFAULT NULL,
  `supplierFax` int(15) DEFAULT NULL,
  `supplierEmail` varchar(100) DEFAULT NULL,
  `supplierGLAutoID` int(11) DEFAULT NULL,
  `supplierGLSystemGLCode` varchar(45) DEFAULT NULL,
  `supplierGLAccount` varchar(45) DEFAULT NULL,
  `supplierGLDescription` varchar(100) DEFAULT NULL,
  `supplierGLType` varchar(3) DEFAULT NULL,
  `supplierCurrencyID` int(11) DEFAULT NULL,
  `supplierCurrency` varchar(45) DEFAULT NULL,
  `supplierCurrencyDecimalPlaces` int(1) DEFAULT NULL,
  `isApplicableforTotal` tinyint(1) DEFAULT '0' COMMENT '0- applicable for each items 1- applicable for invoice total',
  `taxPercentage` double DEFAULT '0',
  `effectiveFrom` date DEFAULT NULL COMMENT 'date tax effective from',
  `taxReferenceNo` varchar(45) DEFAULT NULL,
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) DEFAULT NULL,
  `createdUserGroup` int(11) DEFAULT NULL,
  `createdPCID` varchar(45) DEFAULT NULL,
  `createdUserID` varchar(45) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`menuTaxAutoID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table Use : Maintain Tax type with effective percentage amount\r\nCreated By : Mohamed Hisham11/10/2016\r\nDiscussed with : Mohamed Rishad, Reyaas Rafaideen and Nusky Rauf 11/10/2016\r\nReviewed By : ';

-- ----------------------------
-- Records of srp_erp_pos_menutaxmaster
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_menuyieldpreparation
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_menuyieldpreparation`;
CREATE TABLE `srp_erp_pos_menuyieldpreparation` (
  `yieldPreparationID` int(11) NOT NULL AUTO_INCREMENT,
  `yieldMasterID` int(11) NOT NULL,
  `itemAutoID` int(11) DEFAULT NULL,
  `assetGLAutoID` int(11) DEFAULT NULL,
  `warehouseAutoID` int(11) DEFAULT NULL,
  `documentID` varchar(20) DEFAULT 'YPRP',
  `documentDate` date DEFAULT NULL,
  `serialNo` int(11) DEFAULT NULL,
  `documentSystemCode` varchar(100) DEFAULT NULL,
  `narration` mediumtext,
  `uomID` int(11) DEFAULT NULL,
  `qty` double DEFAULT NULL,
  `transactionCurrencyID` int(11) DEFAULT NULL,
  `transactionCurrency` varchar(45) DEFAULT NULL COMMENT 'Document transaction currency',
  `transactionExchangeRate` double DEFAULT '1' COMMENT 'Always 1',
  `transactionCurrencyDecimalPlaces` int(1) DEFAULT '2' COMMENT 'Decimal places of transaction currency ',
  `companyLocalCurrencyDecimalPlaces` int(1) DEFAULT '2' COMMENT 'Decimal places of company currency',
  `companyLocalCurrencyID` int(11) DEFAULT NULL,
  `companyLocalCurrency` varchar(45) DEFAULT NULL COMMENT 'Local currency of company in company master',
  `companyLocalExchangeRate` double DEFAULT '1' COMMENT 'Exchange rate against transaction currency',
  `companyReportingCurrencyID` int(11) DEFAULT NULL,
  `companyReportingCurrency` varchar(45) DEFAULT NULL COMMENT 'Reporting currency of company in company master',
  `companyReportingExchangeRate` double DEFAULT '0' COMMENT 'Exchange rate against transaction currency ',
  `companyReportingCurrencyDecimalPlaces` int(1) DEFAULT '2' COMMENT 'Decimal places of company currency',
  `confirmedYN` int(1) DEFAULT '0',
  `confirmedUserName` varchar(255) DEFAULT NULL,
  `confirmedDateTime` datetime DEFAULT NULL,
  `confirmedUserID` varchar(255) DEFAULT NULL,
  `companyFinanceYearID` int(11) DEFAULT NULL,
  `companyFinanceYear` varchar(100) DEFAULT NULL,
  `FYBegin` date DEFAULT NULL,
  `FYEnd` date DEFAULT NULL,
  `FYPeriodDateFrom` date DEFAULT NULL,
  `FYPeriodDateTo` date DEFAULT NULL,
  `companyFinancePeriodID` int(11) DEFAULT NULL,
  `companyID` int(11) DEFAULT NULL,
  `createdPCID` varchar(45) DEFAULT NULL,
  `createdUserID` varchar(45) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `createdUserGroup` varchar(45) DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`yieldPreparationID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of srp_erp_pos_menuyieldpreparation
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_menuyieldpreparationdetails
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_menuyieldpreparationdetails`;
CREATE TABLE `srp_erp_pos_menuyieldpreparationdetails` (
  `yieldPreparationDetailID` int(11) NOT NULL AUTO_INCREMENT,
  `yieldPreparationID` int(11) NOT NULL,
  `yieldMasterID` int(11) DEFAULT NULL,
  `yieldDetailID` int(11) DEFAULT NULL,
  `itemAutoID` int(11) DEFAULT NULL,
  `assetGLAutoID` int(11) DEFAULT NULL,
  `uomID` int(11) DEFAULT NULL,
  `qty` double DEFAULT NULL,
  `totalQty` double DEFAULT NULL,
  `localWacAmount` double DEFAULT NULL,
  `localWacAmountTotal` double DEFAULT NULL,
  `companyID` int(11) DEFAULT NULL,
  `createdPCID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `createdUserID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `createdUserGroup` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `modifiedPCID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `modifiedUserID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `timeStamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`yieldPreparationDetailID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of srp_erp_pos_menuyieldpreparationdetails
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_menuyields
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_menuyields`;
CREATE TABLE `srp_erp_pos_menuyields` (
  `yieldID` int(11) NOT NULL AUTO_INCREMENT,
  `itemAutoID` int(11) DEFAULT NULL,
  `Description` varchar(255) DEFAULT NULL,
  `yielduomID` int(11) DEFAULT NULL,
  `yieldUOM` varchar(255) DEFAULT NULL,
  `qty` double DEFAULT NULL,
  `yieldAmount` double DEFAULT NULL,
  `yieldCost` double DEFAULT NULL,
  `yieldsubUOMID` int(11) DEFAULT NULL,
  `yieldsubUOM` varchar(255) DEFAULT NULL,
  `yieldsubAmount` double DEFAULT NULL,
  `yieldsubCost` double DEFAULT NULL,
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) DEFAULT NULL,
  `createdPCID` varchar(45) DEFAULT NULL,
  `createdUserID` varchar(45) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `createdUserGroup` varchar(45) DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `timeStamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`yieldID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of srp_erp_pos_menuyields
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_menuyieldsdetails
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_menuyieldsdetails`;
CREATE TABLE `srp_erp_pos_menuyieldsdetails` (
  `yieldDetailID` int(11) NOT NULL AUTO_INCREMENT,
  `yieldID` int(11) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `typeAutoId` int(1) DEFAULT NULL COMMENT '1=raw material 2=Yield',
  `itemAutoID` int(2) DEFAULT NULL,
  `qty` double DEFAULT NULL,
  `uom` varchar(255) DEFAULT NULL,
  `unitCost` double DEFAULT '0',
  `cost` double DEFAULT '0',
  `companyID` int(11) DEFAULT NULL,
  `createdUserGroup` int(11) DEFAULT NULL,
  `createdPCID` varchar(45) DEFAULT NULL,
  `createdUserID` varchar(45) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`yieldDetailID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of srp_erp_pos_menuyieldsdetails
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_outletprinters
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_outletprinters`;
CREATE TABLE `srp_erp_pos_outletprinters` (
  `outletPrinterID` int(11) NOT NULL AUTO_INCREMENT,
  `warehouseID` int(11) DEFAULT NULL,
  `printerID` longtext,
  `companyID` int(11) DEFAULT NULL,
  PRIMARY KEY (`outletPrinterID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of srp_erp_pos_outletprinters
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_outlettemplatedetail
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_outlettemplatedetail`;
CREATE TABLE `srp_erp_pos_outlettemplatedetail` (
  `outletTemplateDetailID` int(11) NOT NULL AUTO_INCREMENT,
  `outletTemplateMasterID` int(11) DEFAULT NULL,
  `companyID` int(11) DEFAULT NULL,
  `outletID` int(11) DEFAULT NULL,
  `createdPCID` varchar(45) DEFAULT NULL,
  `createdUserID` varchar(45) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `createdUserGroup` varchar(45) DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `timeStamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`outletTemplateDetailID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of srp_erp_pos_outlettemplatedetail
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_outlettemplatemaster
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_outlettemplatemaster`;
CREATE TABLE `srp_erp_pos_outlettemplatemaster` (
  `outletTemplateMasterID` int(11) NOT NULL AUTO_INCREMENT,
  `shortCode` enum('POSR','POSG') DEFAULT NULL,
  `description` varchar(600) DEFAULT NULL,
  `sortOrder` int(2) DEFAULT NULL,
  `isDefault` int(1) DEFAULT '0' COMMENT '1 - default selected tempate',
  PRIMARY KEY (`outletTemplateMasterID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of srp_erp_pos_outlettemplatemaster
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_packgroupdetail
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_packgroupdetail`;
CREATE TABLE `srp_erp_pos_packgroupdetail` (
  `packgroupdetailID` int(11) NOT NULL AUTO_INCREMENT,
  `groupMasterID` int(11) DEFAULT NULL,
  `packMenuID` int(11) DEFAULT NULL,
  `menuID` int(11) DEFAULT NULL,
  `menuPackItemID` int(11) DEFAULT NULL,
  `isActive` int(1) DEFAULT '1' COMMENT '0 inActive, 1 active',
  `createdBy` varchar(20) DEFAULT NULL,
  `createdPc` varchar(100) DEFAULT NULL,
  `createdDatetime` datetime DEFAULT NULL,
  `timestamp` datetime DEFAULT NULL,
  PRIMARY KEY (`packgroupdetailID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of srp_erp_pos_packgroupdetail
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_paymentglconfigdetail
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_paymentglconfigdetail`;
CREATE TABLE `srp_erp_pos_paymentglconfigdetail` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `paymentConfigMasterID` int(11) DEFAULT NULL COMMENT 'FK from srp_erp_pos_paymentglconfigmaster',
  `GLCode` int(11) DEFAULT NULL,
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) DEFAULT NULL,
  `warehouseID` int(11) DEFAULT NULL,
  `isAuthRequired` tinyint(4) DEFAULT '0',
  `createdUserGroup` int(11) DEFAULT NULL,
  `createdPCID` varchar(45) DEFAULT NULL,
  `createdUserID` varchar(45) DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table Use : To set gl codes for received payment of each companies (POS)\r\nModified By : Hisham on 02.01.2017 (added paymentConfigMasterID)\r\ntable  srp_erp_pos_glconfig renamed as srp_erp_pos_paymentglconfigdetail on 02.01.2017\r\nDiscussed with :Nasik and Shafri\r\nReviewed By : ';

-- ----------------------------
-- Records of srp_erp_pos_paymentglconfigdetail
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_paymentglconfigmaster
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_paymentglconfigmaster`;
CREATE TABLE `srp_erp_pos_paymentglconfigmaster` (
  `autoID` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(255) NOT NULL,
  `glAccountType` int(1) NOT NULL COMMENT '1 - Bank 2 - Card 3 - Liability  4 - expense (from srp_erp_chartofaccount)',
  `queryString` longtext,
  `image` varchar(500) DEFAULT NULL,
  `isActive` int(1) DEFAULT '1' COMMENT '1- Active 0- Inactive',
  `sortOrder` int(3) DEFAULT '0',
  `selectBoxName` varchar(50) DEFAULT NULL,
  `timesstamp` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`autoID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table Use : To record master records of grv\r\nCreated By : Hisham on 02.01.2017\r\nDiscussed with :Nasik and Shafri\r\nReviewed By : ';

-- ----------------------------
-- Records of srp_erp_pos_paymentglconfigmaster
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_paymentmethods
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_paymentmethods`;
CREATE TABLE `srp_erp_pos_paymentmethods` (
  `paymentMethodsID` int(11) NOT NULL AUTO_INCREMENT,
  `paymentDescription` varchar(255) DEFAULT NULL,
  `isActive` int(1) DEFAULT '1' COMMENT '1 active, 0 in-active',
  `timestamp` datetime DEFAULT NULL,
  PRIMARY KEY (`paymentMethodsID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of srp_erp_pos_paymentmethods
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_policydetail
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_policydetail`;
CREATE TABLE `srp_erp_pos_policydetail` (
  `posPolicyID` int(11) NOT NULL AUTO_INCREMENT,
  `posPolicyMasterID` int(11) DEFAULT NULL,
  `outletID` int(11) DEFAULT NULL,
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `createdPCID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `createdUserID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `createdUserGroup` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `modifiedPCID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `modifiedUserID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`posPolicyID`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of srp_erp_pos_policydetail
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_policymaster
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_policymaster`;
CREATE TABLE `srp_erp_pos_policymaster` (
  `posPolicyMasterID` int(11) NOT NULL,
  `policyCode` varchar(300) DEFAULT NULL,
  `policyDescription` varchar(600) DEFAULT NULL,
  `defaultValue` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`posPolicyMasterID`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of srp_erp_pos_policymaster
-- ----------------------------
INSERT INTO `srp_erp_pos_policymaster` VALUES ('1', 'KOT', 'KOT Print Out', null);
INSERT INTO `srp_erp_pos_policymaster` VALUES ('2', 'PRICE', 'is Price Required', null);
INSERT INTO `srp_erp_pos_policymaster` VALUES ('3', 'PRINT_SMPL', 'Before payment, Is sample bill required', null);
INSERT INTO `srp_erp_pos_policymaster` VALUES ('4', 'comp_email', 'Send company email', null);
INSERT INTO `srp_erp_pos_policymaster` VALUES ('5', 'KOT_BTN_HIDE', 'Hide KOT button in POS', null);
INSERT INTO `srp_erp_pos_policymaster` VALUES ('6', 'ITM_DISC', 'Show Item Level Discount', null);
INSERT INTO `srp_erp_pos_policymaster` VALUES ('7', 'WIFI_PW', 'Wifi Password in the bill', null);
INSERT INTO `srp_erp_pos_policymaster` VALUES ('8', 'CAM', 'CCTV Camera Feed', null);
INSERT INTO `srp_erp_pos_policymaster` VALUES ('9', 'HDO', 'Hide Delivery Order Form', '0');
INSERT INTO `srp_erp_pos_policymaster` VALUES ('10', 'DPM', 'Is delivery person mandatory', '0');

-- ----------------------------
-- Table structure for srp_erp_pos_printtemplatedetail
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_printtemplatedetail`;
CREATE TABLE `srp_erp_pos_printtemplatedetail` (
  `printTemplateDetailID` int(11) NOT NULL AUTO_INCREMENT,
  `printTemplateMasterID` int(11) DEFAULT NULL,
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `createdPCID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `createdUserID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `createdUserGroup` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `modifiedPCID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `modifiedUserID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`printTemplateDetailID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of srp_erp_pos_printtemplatedetail
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_printtemplatemaster
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_printtemplatemaster`;
CREATE TABLE `srp_erp_pos_printtemplatemaster` (
  `printTemplateMasterID` int(11) NOT NULL AUTO_INCREMENT,
  `templateLink` varchar(600) DEFAULT NULL,
  `isDefault` int(1) DEFAULT '0',
  `description` varchar(255) DEFAULT NULL,
  `templateType` enum('POS','VOID','VOIDH','SO','POSGEN') DEFAULT 'POS' COMMENT 'SO - sales order, voidH - void history',
  PRIMARY KEY (`printTemplateMasterID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of srp_erp_pos_printtemplatemaster
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_promotionapplicableitems
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_promotionapplicableitems`;
CREATE TABLE `srp_erp_pos_promotionapplicableitems` (
  `autoID` int(11) NOT NULL AUTO_INCREMENT,
  `promotionID` int(11) NOT NULL,
  `itemAutoID` int(11) NOT NULL,
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) DEFAULT NULL,
  `segmentID` int(11) DEFAULT NULL,
  `segmentCode` varchar(45) DEFAULT NULL,
  `createdPCID` varchar(45) DEFAULT NULL,
  `createdUserID` varchar(45) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `createdUserGroup` varchar(45) DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`autoID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table Use : this table will include all the items which are included for particular promotion with promotion Id\r\nCreated By : Mohamed Hisham10/10/2016\r\nDiscussed with : Reyaas Rafaideen and Mohamed Nasik10/10/2016\r\nReviewed By : ';

-- ----------------------------
-- Records of srp_erp_pos_promotionapplicableitems
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_promotionsetupdetail
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_promotionsetupdetail`;
CREATE TABLE `srp_erp_pos_promotionsetupdetail` (
  `promotionDetailID` int(11) NOT NULL AUTO_INCREMENT,
  `promotionID` int(11) DEFAULT NULL,
  `startRangeAmount` double DEFAULT NULL COMMENT 'minmum amount of offer applied',
  `discountPrc` double DEFAULT '0' COMMENT 'if promotion type is on sale disc',
  `coupenAmount` double DEFAULT '0' COMMENT 'if promotion type is on sale coupen ',
  `buyQty` int(11) DEFAULT NULL COMMENT 'if buy one get one free offer minimum qty offer is applied',
  `getFreeQty` int(11) DEFAULT NULL COMMENT 'if buy one get one free offer qry',
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) DEFAULT NULL,
  `segmentID` int(11) DEFAULT NULL,
  `segmentCode` varchar(45) DEFAULT NULL,
  `createdPCID` varchar(45) DEFAULT NULL,
  `createdUserID` varchar(45) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `createdUserGroup` varchar(45) DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`promotionDetailID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table Use : this table will contain promotion setup details as \r\n           if promotion type is on sale discount start range and discount prc will be stored \r\n          if promotion type is on sale coupen start range and coupen amount will be stored\r\n          if promotion type is buy one get one free buying qty  and free qty will be stored \r\nCreated By : Mohamed Hisham 10/10/2016\r\nDiscussed with : Reyaas Rafaideen and Mohamed Nasik 10/10/2016\r\nReviewed By : ';

-- ----------------------------
-- Records of srp_erp_pos_promotionsetupdetail
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_promotionsetupmaster
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_promotionsetupmaster`;
CREATE TABLE `srp_erp_pos_promotionsetupmaster` (
  `promotionID` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(200) DEFAULT NULL,
  `promotionTypeID` int(11) DEFAULT NULL,
  `isActive` tinyint(1) DEFAULT NULL,
  `dateFrom` date DEFAULT NULL,
  `dateTo` date DEFAULT NULL,
  `isApplicableForAllItem` tinyint(1) DEFAULT NULL,
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) DEFAULT NULL,
  `segmentID` int(11) DEFAULT NULL,
  `segmentCode` varchar(45) DEFAULT NULL,
  `createdPCID` varchar(45) DEFAULT NULL,
  `createdUserID` varchar(45) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `createdUserGroup` varchar(45) DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`promotionID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table Use : Promotion will be created in this table. datas such as promotion type, valid date range will be stored in this table\r\nCreated By : Mohamed Hisham10/10/2016\r\nDiscussed with : Reyaas Rafaideen and Mohamed Nasik10/10/2016\r\nReviewed By : ';

-- ----------------------------
-- Records of srp_erp_pos_promotionsetupmaster
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_promotiontypes
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_promotiontypes`;
CREATE TABLE `srp_erp_pos_promotiontypes` (
  `promotionTypeID` int(11) NOT NULL AUTO_INCREMENT,
  `Description` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`promotionTypeID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table Use : Master Table to store promotion types as 1. on sale disc  2. on sale coupen 3. buy one get one free\r\nCreated By : Mohamed Hisham10/10/2016\r\nDiscussed with : Reyaas Rafaideen and Mohamed Nasik 10/10/2016\r\nReviewed By : ';

-- ----------------------------
-- Records of srp_erp_pos_promotiontypes
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_promotionwarehouses
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_promotionwarehouses`;
CREATE TABLE `srp_erp_pos_promotionwarehouses` (
  `autoID` int(11) NOT NULL AUTO_INCREMENT,
  `promotionID` int(11) NOT NULL,
  `wareHouseID` int(11) NOT NULL,
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) DEFAULT NULL,
  `segmentID` int(11) DEFAULT NULL,
  `segmentCode` varchar(45) DEFAULT NULL,
  `createdPCID` varchar(45) DEFAULT NULL,
  `createdUserID` varchar(45) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `createdUserGroup` varchar(45) DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`autoID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table Use : this table is used to maintain one promotion for multiple warehouses. \r\nCreated By : Mohamed Hisham10/10/2016\r\nDiscussed with : Reyaas Rafaideen and Mohamed Nasik 10/10/2016\r\nReviewed By : ';

-- ----------------------------
-- Records of srp_erp_pos_promotionwarehouses
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_segmentconfig
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_segmentconfig`;
CREATE TABLE `srp_erp_pos_segmentconfig` (
  `segmentConfigID` int(11) NOT NULL AUTO_INCREMENT,
  `wareHouseAutoID` int(11) DEFAULT NULL,
  `industrytypeID` int(11) DEFAULT NULL,
  `posTemplateID` int(11) DEFAULT NULL,
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) DEFAULT NULL,
  `segmentID` int(11) DEFAULT NULL,
  `segmentCode` varchar(50) DEFAULT NULL,
  `isGeneralPOS` int(1) DEFAULT '0' COMMENT '1 General POS',
  `generalPrintTemplateID` int(2) DEFAULT '0',
  `isActive` int(1) DEFAULT '-1' COMMENT '0 = deleted, -1 = active',
  `deletedBy` varchar(45) DEFAULT NULL,
  `deletedDatetime` datetime DEFAULT NULL,
  `createdPCID` varchar(45) DEFAULT NULL,
  `createdUserID` varchar(45) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `timeStamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`segmentConfigID`),
  KEY `wareHouseAutoID` (`wareHouseAutoID`) USING BTREE,
  KEY `companyID` (`companyID`) USING BTREE,
  KEY `segmentID` (`segmentID`) USING BTREE,
  KEY `isGeneralPOS` (`isGeneralPOS`) USING BTREE,
  KEY `isActive` (`isActive`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table Use : used to configure the postemplate and warehouse details under each segments \r\nCreated By : Mohamed Hisham 09/10/2016\r\nDiscussed with : Mohamed Rishad, Reyaas Rafaideen and Mohamed Shafry 09/10/2016\r\nReviewed By : Mohamed Zahlan 09/10/2016\r\n';

-- ----------------------------
-- Records of srp_erp_pos_segmentconfig
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_shiftdetails
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_shiftdetails`;
CREATE TABLE `srp_erp_pos_shiftdetails` (
  `shiftID` int(11) NOT NULL AUTO_INCREMENT,
  `wareHouseID` int(11) NOT NULL,
  `empID` int(11) DEFAULT NULL,
  `counterID` int(11) DEFAULT NULL,
  `startTime` datetime DEFAULT NULL,
  `endTime` datetime DEFAULT NULL,
  `isClosed` tinyint(1) NOT NULL DEFAULT '0',
  `cashSales` double DEFAULT '0',
  `giftCardTopUp` double DEFAULT '0',
  `startingBalance_transaction` double DEFAULT NULL COMMENT 'starting transaction amount',
  `endingBalance_transaction` double DEFAULT NULL COMMENT 'ending transaction amount',
  `different_transaction` double DEFAULT '0',
  `cashSales_local` double DEFAULT '0',
  `giftCardTopUp_local` double DEFAULT '0',
  `startingBalance_local` double DEFAULT NULL COMMENT 'transcation in local currency ',
  `endingBalance_local` double DEFAULT NULL COMMENT 'transcation in local currency ',
  `different_local` double DEFAULT '0',
  `cashSales_reporting` double DEFAULT '0',
  `giftCardTopUp_reporting` double DEFAULT '0',
  `closingCashBalance_transaction` double DEFAULT '0' COMMENT 'Opening Cash Balance + Cash Sales',
  `closingCashBalance_local` double DEFAULT '0',
  `startingBalance_reporting` double DEFAULT NULL COMMENT 'transcation in reporting currency ',
  `endingBalance_reporting` double DEFAULT NULL COMMENT 'transcation in reporting currency ',
  `different_local_reporting` double DEFAULT '0',
  `closingCashBalance_reporting` double DEFAULT '0',
  `transactionCurrencyID` int(11) DEFAULT NULL,
  `transactionCurrency` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `transactionExchangeRate` double DEFAULT NULL,
  `transactionCurrencyDecimalPlaces` int(1) DEFAULT NULL,
  `companyLocalCurrencyID` int(11) DEFAULT NULL,
  `companyLocalCurrency` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `companyLocalExchangeRate` double DEFAULT NULL,
  `companyLocalCurrencyDecimalPlaces` int(1) DEFAULT NULL,
  `companyReportingCurrencyID` int(11) DEFAULT NULL,
  `companyReportingCurrency` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `companyReportingExchangeRate` double DEFAULT NULL,
  `companyReportingCurrencyDecimalPlaces` int(1) DEFAULT NULL,
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `segmentID` int(11) DEFAULT NULL,
  `segmentCode` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `createdUserGroup` int(11) DEFAULT NULL,
  `createdPCID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `createdUserID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `modifiedPCID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `modifiedUserID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `id_store` int(1) NOT NULL,
  `is_sync` int(1) DEFAULT '0',
  PRIMARY KEY (`shiftID`,`id_store`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of srp_erp_pos_shiftdetails
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_taxapplicablemenus
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_taxapplicablemenus`;
CREATE TABLE `srp_erp_pos_taxapplicablemenus` (
  `AutoID` int(11) NOT NULL AUTO_INCREMENT,
  `menuAutoID` int(11) NOT NULL,
  `menuTaxAutoID` int(11) NOT NULL,
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `createdUserGroup` int(11) DEFAULT NULL,
  `createdPCID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `createdUserID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `modifiedPCID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `modifiedUserID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`AutoID`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC COMMENT='Table Use : this table is to maintain each items applicable taxes \r\nCreated By : Mohamed Hisham10/10/2016\r\nDiscussed with : Mohamed Rishad, Reyaas Rafaideen and Mohamed Shafry 09/10/2016\r\nReviewed By : Mohamed Zahlan 09/10/2016';

-- ----------------------------
-- Records of srp_erp_pos_taxapplicablemenus
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_templatemaster
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_templatemaster`;
CREATE TABLE `srp_erp_pos_templatemaster` (
  `posTemplateID` int(11) NOT NULL AUTO_INCREMENT,
  `posTemplateDescription` varchar(100) NOT NULL,
  `templateLink` varchar(300) DEFAULT NULL,
  `isDefault` int(1) DEFAULT '0',
  PRIMARY KEY (`posTemplateID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table Use : This Tables used to maintain diffrent template UIs for POS \r\nCreated By : Mohamed Hisham 09/10/2016\r\nDiscussed with : Mohamed Rishad, Reyaas Rafaideen and Mohamed Shafry 09/10/2016\r\nReviewed By : Mohamed Zahlan 09/10/2016\r\n\r\n';

-- ----------------------------
-- Records of srp_erp_pos_templatemaster
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_valuepackdetail
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_valuepackdetail`;
CREATE TABLE `srp_erp_pos_valuepackdetail` (
  `valuePackDetailID` int(11) NOT NULL AUTO_INCREMENT,
  `menuSalesID` int(11) DEFAULT NULL,
  `menuSalesItemID` int(11) DEFAULT NULL,
  `menuMasterID` int(11) DEFAULT NULL,
  `warehouseMenuID` int(11) DEFAULT NULL,
  `menuPackItemID` int(11) DEFAULT NULL,
  `menuID` int(11) DEFAULT NULL,
  `qty` int(11) DEFAULT '1',
  `isRequired` int(1) DEFAULT '0' COMMENT '1 = required item, 0 = optional item',
  `createdBy` varchar(20) DEFAULT NULL,
  `createdPc` varchar(100) DEFAULT NULL,
  `createdDatetime` datetime DEFAULT NULL,
  `timestamp` datetime DEFAULT NULL,
  PRIMARY KEY (`valuePackDetailID`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of srp_erp_pos_valuepackdetail
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_wac_updatehistory
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_wac_updatehistory`;
CREATE TABLE `srp_erp_pos_wac_updatehistory` (
  `updatehistoryID` int(11) NOT NULL AUTO_INCREMENT,
  `companyID` int(11) DEFAULT NULL,
  `updatedDate` date DEFAULT NULL,
  `timestamp` datetime DEFAULT NULL,
  PRIMARY KEY (`updatehistoryID`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of srp_erp_pos_wac_updatehistory
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_warehousemenucategory
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_warehousemenucategory`;
CREATE TABLE `srp_erp_pos_warehousemenucategory` (
  `autoID` int(11) NOT NULL AUTO_INCREMENT,
  `menuCategoryID` int(11) DEFAULT NULL,
  `warehouseID` int(11) DEFAULT NULL,
  `companyID` int(11) DEFAULT NULL,
  `isActive` int(1) DEFAULT '1',
  `isDeleted` int(1) DEFAULT '0',
  `deletedBy` varchar(45) DEFAULT NULL,
  `deletedDatetime` datetime DEFAULT NULL,
  `createdPCID` varchar(45) DEFAULT NULL,
  `createdUserID` varchar(45) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `createdUserGroup` varchar(45) DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `timeStamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`autoID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of srp_erp_pos_warehousemenucategory
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_warehousemenumaster
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_warehousemenumaster`;
CREATE TABLE `srp_erp_pos_warehousemenumaster` (
  `warehouseMenuID` int(11) NOT NULL AUTO_INCREMENT,
  `warehouseID` int(11) NOT NULL,
  `menuMasterID` int(11) NOT NULL,
  `warehouseMenuCategoryID` int(11) DEFAULT NULL,
  `companyID` int(11) DEFAULT NULL,
  `isActive` int(1) DEFAULT '1',
  `isDeleted` int(1) DEFAULT '0',
  `deletedBy` varchar(45) DEFAULT NULL,
  `kotID` int(11) DEFAULT NULL COMMENT 'Kitchen Location ID',
  `isTaxEnabled` int(1) DEFAULT '1' COMMENT '0 - tax value should go to revenue, 1 - tax recorded to GL',
  `isShortcut` int(1) DEFAULT '0' COMMENT 'shows in font page of terminal 1, 0 not shortcut menu',
  `deletedDatetime` datetime DEFAULT NULL,
  `createdPCID` varchar(45) DEFAULT NULL,
  `createdUserID` varchar(45) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `createdUserGroup` varchar(45) DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `timeStamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`warehouseMenuID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of srp_erp_pos_warehousemenumaster
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_pos_wifipasswordsetup
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_pos_wifipasswordsetup`;
CREATE TABLE `srp_erp_pos_wifipasswordsetup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wifiPassword` varchar(255) DEFAULT NULL,
  `isUsed` int(1) DEFAULT '0',
  `menuSalesID` int(11) DEFAULT NULL,
  `filesName` varchar(255) DEFAULT NULL,
  `path` varchar(500) DEFAULT NULL,
  `outletID` int(11) DEFAULT NULL,
  `companyID` int(11) DEFAULT NULL,
  `createdBy` int(11) DEFAULT NULL,
  `createdDatetime` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `wifiPassword` (`wifiPassword`) USING BTREE,
  KEY `isUsed` (`isUsed`) USING BTREE,
  KEY `outletID` (`outletID`) USING BTREE,
  KEY `companyID` (`companyID`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of srp_erp_pos_wifipasswordsetup
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_taxmaster
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_taxmaster`;
CREATE TABLE `srp_erp_taxmaster` (
  `taxMasterAutoID` int(11) NOT NULL AUTO_INCREMENT,
  `taxDescription` varchar(200) NOT NULL,
  `taxShortCode` varchar(5) NOT NULL,
  `taxType` tinyint(1) DEFAULT '1' COMMENT 'sales tax -1 purchase tax -2',
  `isActive` tinyint(1) DEFAULT '1',
  `supplierAutoID` int(11) NOT NULL COMMENT 'Tax Authority - need to choose from supplier master',
  `supplierSystemCode` varchar(45) DEFAULT NULL,
  `supplierName` varchar(200) DEFAULT NULL,
  `supplierAddress` varchar(255) DEFAULT NULL,
  `supplierTelephone` int(15) DEFAULT NULL,
  `supplierFax` int(15) DEFAULT NULL,
  `supplierEmail` varchar(100) DEFAULT NULL,
  `supplierGLAutoID` int(11) DEFAULT NULL,
  `supplierGLSystemGLCode` varchar(45) DEFAULT NULL,
  `supplierGLAccount` varchar(45) DEFAULT NULL,
  `supplierGLDescription` varchar(100) DEFAULT NULL,
  `supplierGLType` varchar(3) DEFAULT NULL,
  `supplierCurrencyID` int(11) DEFAULT NULL,
  `supplierCurrency` varchar(45) DEFAULT NULL,
  `supplierCurrencyDecimalPlaces` int(1) DEFAULT NULL,
  `isApplicableforTotal` tinyint(1) DEFAULT '0' COMMENT '0- applicable for each items 1- applicable for invoice total',
  `taxPercentage` double DEFAULT '0',
  `effectiveFrom` date DEFAULT NULL COMMENT 'date tax effective from',
  `taxReferenceNo` varchar(45) DEFAULT NULL,
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) DEFAULT NULL,
  `createdUserGroup` int(11) DEFAULT NULL,
  `createdPCID` varchar(45) DEFAULT NULL,
  `createdUserID` varchar(45) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`taxMasterAutoID`),
  UNIQUE KEY `taxMasterAutoID` (`taxMasterAutoID`) USING BTREE,
  KEY `taxType` (`taxType`) USING BTREE,
  KEY `isActive` (`isActive`) USING BTREE,
  KEY `supplierAutoID` (`supplierAutoID`) USING BTREE,
  KEY `supplierGLAutoID` (`supplierGLAutoID`) USING BTREE,
  KEY `supplierCurrencyID` (`supplierCurrencyID`) USING BTREE,
  KEY `companyID` (`companyID`) USING BTREE,
  KEY `companyCode` (`companyCode`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table Use : Maintain Tax type with effective percentage amount\r\nCreated By : Mohamed Hisham11/10/2016\r\nDiscussed with : Mohamed Rishad, Reyaas Rafaideen and Nusky Rauf 11/10/2016\r\nReviewed By : ';

-- ----------------------------
-- Records of srp_erp_taxmaster
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_timezonedetail
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_timezonedetail`;
CREATE TABLE `srp_erp_timezonedetail` (
  `detailID` int(11) NOT NULL AUTO_INCREMENT,
  `masterID` int(11) DEFAULT NULL,
  `description` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`detailID`) USING BTREE,
  KEY `masterID_idx` (`masterID`) USING BTREE,
  CONSTRAINT `masterID` FOREIGN KEY (`masterID`) REFERENCES `srp_erp_timezonemaster` (`masterID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of srp_erp_timezonedetail
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_timezonemaster
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_timezonemaster`;
CREATE TABLE `srp_erp_timezonemaster` (
  `masterID` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`masterID`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of srp_erp_timezonemaster
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_unitsconversion
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_unitsconversion`;
CREATE TABLE `srp_erp_unitsconversion` (
  `unitsConversionAutoID` int(11) NOT NULL AUTO_INCREMENT,
  `masterUnitID` int(11) DEFAULT NULL,
  `subUnitID` int(11) DEFAULT NULL,
  `conversion` double DEFAULT NULL,
  `companyID` int(11) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`unitsConversionAutoID`),
  UNIQUE KEY `unitsConversionAutoID` (`unitsConversionAutoID`) USING BTREE,
  KEY `masterUnitID` (`masterUnitID`) USING BTREE,
  KEY `subUnitID` (`subUnitID`) USING BTREE,
  KEY `companyID` (`companyID`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of srp_erp_unitsconversion
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_unit_of_measure
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_unit_of_measure`;
CREATE TABLE `srp_erp_unit_of_measure` (
  `UnitID` int(11) NOT NULL AUTO_INCREMENT,
  `UnitShortCode` varchar(255) DEFAULT NULL,
  `UnitDes` varchar(255) DEFAULT NULL,
  `companyID` int(11) DEFAULT NULL,
  `createdUserGroup` varchar(45) DEFAULT NULL,
  `createdPCID` varchar(100) DEFAULT NULL,
  `createdUserID` varchar(100) DEFAULT NULL,
  `createdUserName` varchar(100) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `modifiedPCID` varchar(100) DEFAULT NULL,
  `modifiedUserID` varchar(100) DEFAULT NULL,
  `modifiedUserName` varchar(150) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `timeStamp` datetime DEFAULT NULL,
  PRIMARY KEY (`UnitID`),
  UNIQUE KEY `UnitID` (`UnitID`) USING BTREE,
  KEY `UnitShortCode` (`UnitShortCode`),
  KEY `companyID` (`companyID`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of srp_erp_unit_of_measure
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_warehouseitems
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_warehouseitems`;
CREATE TABLE `srp_erp_warehouseitems` (
  `warehouseItemsAutoID` int(11) NOT NULL AUTO_INCREMENT,
  `wareHouseAutoID` int(11) DEFAULT NULL,
  `wareHouseLocation` varchar(150) DEFAULT NULL,
  `wareHouseDescription` varchar(255) DEFAULT NULL,
  `itemAutoID` int(11) DEFAULT NULL,
  `itemSystemCode` varchar(45) DEFAULT NULL,
  `itemDescription` longtext,
  `unitOfMeasureID` int(11) DEFAULT NULL,
  `unitOfMeasure` varchar(45) DEFAULT NULL,
  `currentStock` double DEFAULT '0',
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`warehouseItemsAutoID`),
  UNIQUE KEY `warehouseItemsAutoID` (`warehouseItemsAutoID`) USING BTREE,
  KEY `itemcode` (`itemAutoID`),
  KEY `wareHouseAutoID` (`wareHouseAutoID`) USING BTREE,
  KEY `companyID` (`companyID`) USING BTREE,
  KEY `companyCode` (`companyCode`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of srp_erp_warehouseitems
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_warehousemaster
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_warehousemaster`;
CREATE TABLE `srp_erp_warehousemaster` (
  `wareHouseAutoID` int(11) NOT NULL AUTO_INCREMENT,
  `wareHouseCode` varchar(45) DEFAULT NULL,
  `wareHouseDescription` varchar(150) DEFAULT NULL,
  `wareHouseLocation` varchar(100) DEFAULT NULL,
  `isPosLocation` tinyint(1) DEFAULT '0',
  `isDefault` int(1) DEFAULT '0',
  `warehouseType` int(1) DEFAULT '1' COMMENT '1 - General 2 - Manufacturing 3 - Maintenance',
  `WIPGLAutoID` int(11) DEFAULT NULL,
  `warehouseAddress` varchar(255) DEFAULT NULL,
  `warehouseTel` varchar(255) DEFAULT NULL,
  `isActive` int(1) DEFAULT '1',
  `warehouseImage` varchar(300) DEFAULT NULL,
  `pos_footNote` text,
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) DEFAULT NULL,
  `createdUserGroup` int(11) DEFAULT NULL,
  `createdPCID` varchar(45) DEFAULT NULL,
  `createdUserID` varchar(45) DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) DEFAULT NULL,
  `modifiedPCID` varchar(45) DEFAULT NULL,
  `modifiedUserID` varchar(45) DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`wareHouseAutoID`),
  UNIQUE KEY `wareHouseAutoID` (`wareHouseAutoID`) USING BTREE,
  KEY `wareHouseCode_UNIQUE` (`wareHouseCode`),
  KEY `isPosLocation` (`isPosLocation`) USING BTREE,
  KEY `isDefault` (`isDefault`) USING BTREE,
  KEY `warehouseType` (`warehouseType`) USING BTREE,
  KEY `WIPGLAutoID` (`WIPGLAutoID`) USING BTREE,
  KEY `isActive` (`isActive`) USING BTREE,
  KEY `companyID` (`companyID`) USING BTREE,
  KEY `companyCode` (`companyCode`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of srp_erp_warehousemaster
-- ----------------------------

-- ----------------------------
-- Table structure for srp_erp_warehouse_users
-- ----------------------------
DROP TABLE IF EXISTS `srp_erp_warehouse_users`;
CREATE TABLE `srp_erp_warehouse_users` (
  `autoID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) DEFAULT NULL,
  `wareHouseID` int(11) DEFAULT NULL,
  `counterID` int(11) DEFAULT NULL,
  `isActive` tinyint(1) DEFAULT '1',
  `companyID` int(11) DEFAULT NULL,
  `companyCode` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `createdUserGroup` int(11) DEFAULT NULL,
  `createdPCID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `createdUserID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `createdDateTime` datetime DEFAULT NULL,
  `createdUserName` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `modifiedPCID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `modifiedUserID` varchar(45) CHARACTER SET utf8 DEFAULT NULL,
  `modifiedDateTime` datetime DEFAULT NULL,
  `modifiedUserName` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`autoID`),
  UNIQUE KEY `autoID` (`autoID`) USING BTREE,
  KEY `userID` (`userID`) USING BTREE,
  KEY `wareHouseID` (`wareHouseID`) USING BTREE,
  KEY `counterID` (`counterID`) USING BTREE,
  KEY `isActive` (`isActive`) USING BTREE,
  KEY `companyID` (`companyID`) USING BTREE,
  KEY `companyCode` (`companyCode`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of srp_erp_warehouse_users
-- ----------------------------

-- ----------------------------
-- Procedure structure for generateCalender
-- ----------------------------
DROP PROCEDURE IF EXISTS `generateCalender`;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `generateCalender`(p_start_date DATE, p_end_date DATE,companyID INT,sun INT ,mon INT,tue INT,wed INT,thur INT,fri INT,sat INT)
BEGIN



DECLARE v_full_date DATE;



SET v_full_date = p_start_date;



WHILE v_full_date < p_end_date 

DO

 INSERT INTO srp_erp_calender 

(

             fulldate ,

            dayofmonth ,

            dayofyear ,

            dayofweek ,

            dayname ,

            monthnumber,

            monthname,

            year,

            quarter,

          weekend_flag,

          companyID

  )

 VALUES 

(

              v_full_date,

            DAYOFMONTH(v_full_date),

            DAYOFYEAR(v_full_date),

            DAYOFWEEK(v_full_date),

            DAYNAME(v_full_date),

            MONTH(v_full_date),

            MONTHNAME(v_full_date),

            YEAR(v_full_date),

            QUARTER(v_full_date),

	CASE DAYOFWEEK(v_full_date) 

    WHEN 1 THEN sun 

    WHEN 2 THEN mon

    WHEN 3 THEN tue

    WHEN 4 THEN wed

    WHEN 5 THEN thur

	WHEN 6 THEN fri

    WHEN 7 THEN sat 

    ELSE 0

    END,

    companyID

   );



SET v_full_date = DATE_ADD(v_full_date, INTERVAL 1 DAY);



END WHILE;

END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for POSRDoubleEntries
-- ----------------------------
DROP PROCEDURE IF EXISTS `POSRDoubleEntries`;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `POSRDoubleEntries`(IN _shiftID int(11))
BEGIN 
   /******* New GL Entries ****/
/** ------------------ 1. REVENUE -----------------------------------------------------*/
SELECT
    'POSR' AS documentCode,
		 menusalesmaster.shiftID AS documentMasterAutoID,
		concat( 'POSR/', warehousemaster.wareHouseCode, '/', menusalesmaster.shiftID ) AS documentSystemCode,
		CURDATE() AS documentdate,
		YEAR (curdate()) AS documentYear,
		MONTH (curdate()) AS documentMonth,
		'POS Sales - Revenue' AS documentNarration,
		'' AS chequeNumber,
		item.revenueGLAutoID AS GLAutoID,
		chartOfAccount.systemAccountCode AS systemGLCode,
		chartOfAccount.GLSecondaryCode AS GLCode,
		chartOfAccount.GLDescription AS GLDescription,
		chartOfAccount.subCategory AS GLType,
		'cr' AS amount_type,
		'0' AS isFromItem,
		menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
		menusalesmaster.transactionCurrency AS transactionCurrency,
		'1' AS transactionExchangeRate,
		abs(sum(ifnull(item.salesPriceNetTotal,0))) *- 1 AS transactionAmount,
		currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
		company.company_default_currencyID AS companyLocalCurrencyID,
		company.company_default_currency AS companyLocalCurrency,
		getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS companyLocalExchangeRate,
		(( abs(sum(ifnull(item.salesPriceNetTotal,0))) *- 1 ) / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID )  )) AS companyLocalAmount, getDecimalPlaces ( company.company_default_currencyID  ) AS companyLocalCurrencyDecimalPlaces,
		company.company_reporting_currencyID as companyReportingCurrencyID,
		company.company_reporting_currency AS companyReportingCurrency,
		getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) AS companyReportingExchangeRate,
		(( abs(sum(ifnull(item.salesPriceNetTotal,0))) *- 1 ) / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID )  )) AS companyReportingAmount,
		getDecimalPlaces ( company.company_reporting_currencyID ) AS companyReportingCurrencyDecimalPlaces,
		menusalesmaster.segmentID AS segmentID,
		menusalesmaster.segmentCode AS segmentCode,
		menusalesmaster.companyID AS companyID,
		menusalesmaster.companyCode AS companyCode,
		shiftDetail.createdUserGroup AS createdUserGroup,
		shiftDetail.createdPCID as  createdPCID,
		shiftDetail.createdUserID as  createdUserID,
		shiftDetail.startTime as createdDateTime,
		shiftDetail.createdUserName as createdUserName,
		NULL AS modifiedPCID,
		NULL AS modifiedUserID,
		NULL AS modifiedDateTime,
		null AS modifiedUserName,
		CURRENT_TIMESTAMP() as `timestamp`
		FROM
		srp_erp_pos_menusalesitems item
		LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = item.revenueGLAutoID
		LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = item.menuSalesID
		LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
		LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
		LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
		LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID

		WHERE
		menusalesmaster.shiftID = _shiftID and menusalesmaster.isHold=0 AND menusalesmaster.isVoid=0
		GROUP BY
		revenueGLAutoID;


/**------------------  2. BANK OR CASH -----------------------------------------------------*/
SELECT
    'POSR' AS documentCode,
    menusalesmaster.shiftID AS documentMasterAutoID,
    concat(
        'POSR/',
        warehousemaster.wareHouseCode,
        '/',
        menusalesmaster.shiftID
    ) AS documentSystemCode,
    CURDATE() AS documentdate,
    YEAR (curdate()) AS documentYear,
    MONTH (curdate()) AS documentMonth,
    'POS Sales - Bank' AS documentNarration,
    '' AS chequeNumber,
    payments.GLCode AS GLAutoID,
    chartOfAccount.systemAccountCode AS systemGLCode,
    chartOfAccount.GLSecondaryCode AS GLCode,
    chartOfAccount.GLDescription AS GLDescription,
    chartOfAccount.subCategory AS GLType,
    'dr' AS amount_type,
    '0' AS isFromItem,
    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
    menusalesmaster.transactionCurrency AS transactionCurrency,
    '1' AS transactionExchangeRate,
    SUM(payments.amount) AS transactionAmount,
    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
    company.company_default_currencyID AS companyLocalCurrencyID,
    company.company_default_currency AS companyLocalCurrency,
    getExchangeRate (
        menusalesmaster.transactionCurrencyID,
        company.company_default_currencyID,
        menusalesmaster.companyID
    ) AS companyLocalExchangeRate,
    SUM(payments.amount) / (
        getExchangeRate (
            menusalesmaster.transactionCurrencyID,
            company.company_default_currencyID,
            menusalesmaster.companyID
        )
    ) AS companyLocalAmount,
    getDecimalPlaces (
        company.company_default_currencyID
    ) AS companyLocalCurrencyDecimalPlaces,
    company.company_reporting_currencyID AS companyReportingCurrencyID,
    company.company_reporting_currency AS companyReportingCurrency,
    getExchangeRate (
        menusalesmaster.transactionCurrencyID,
        company.company_reporting_currencyID,
        menusalesmaster.companyID
    ) AS companyReportingExchangeRate,
    SUM(payments.amount) / (
        getExchangeRate (
            menusalesmaster.transactionCurrencyID,
            company.company_reporting_currencyID,
            menusalesmaster.companyID
        )
    ) AS companyReportingAmount,
    getDecimalPlaces (
        company.company_reporting_currencyID
    ) AS companyReportingCurrencyDecimalPlaces,
    menusalesmaster.segmentID AS segmentID,
    menusalesmaster.segmentCode AS segmentCode,
    menusalesmaster.companyID AS companyID,
    menusalesmaster.companyCode AS companyCode,
    menusalesmaster.createdUserGroup AS createdUserGroup,
    menusalesmaster.createdPCID AS createdPCID,
    menusalesmaster.createdUserID createdUserID,
    shiftDetail.startTime createdDateTime,
    menusalesmaster.createdUserName createdUserName,
    NULL AS modifiedPCID,
    NULL AS modifiedUserID,
    NULL AS modifiedDateTime,
    NULL AS modifiedUserName,
    CURRENT_TIMESTAMP () `timestamp`
FROM
    srp_erp_pos_menusalespayments payments 
LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON payments.menuSalesID = menusalesmaster.menuSalesID
LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = payments.GLCode
LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID  
LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
WHERE
    menusalesmaster.shiftID = _shiftID
AND menusalesmaster.isHold = 0 AND menusalesmaster.isVoid = 0 AND payments.paymentConfigMasterID!=7  
GROUP BY
    payments.GLCode;


/** ------------------ 3. COGS -----------------------------------------------------*/
SELECT
    'POSR' AS documentCode,
    menusalesmaster.shiftID AS documentMasterAutoID,
    concat( 'POSR/', warehousemaster.wareHouseCode, '/', menusalesmaster.shiftID ) AS documentSystemCode,
    CURDATE() AS documentdate,
    YEAR (curdate()) AS documentYear,
    MONTH (curdate()) AS documentMonth,
    'POS Sales - COGS' AS documentNarration,
    '' AS chequeNumber,
    item.costGLAutoID AS GLAutoID,
    chartOfAccount.systemAccountCode AS systemGLCode,
    chartOfAccount.GLSecondaryCode AS GLCode,
    chartOfAccount.GLDescription AS GLDescription,
    chartOfAccount.subCategory AS GLType,
    'dr' AS amount_type,
    '0' AS isFromItem,
    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
    menusalesmaster.transactionCurrency AS transactionCurrency,
    '1' AS transactionExchangeRate,
    ( sum( IFNULL(item.cost,0) *   item.menuSalesQty  ) )  AS transactionAmount,
    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
    company.company_default_currencyID AS companyLocalCurrencyID,
    company.company_default_currency AS companyLocalCurrency,
    getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS companyLocalExchangeRate,
    ( sum( IFNULL(item.cost,0) *   item.menuSalesQty  ) ) / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) ) *-1 AS companyLocalAmount,
    getDecimalPlaces ( company.company_default_currencyID ) AS companyLocalCurrencyDecimalPlaces,
    company.company_reporting_currencyID AS companyReportingCurrencyID,
    company.company_reporting_currency AS companyReportingCurrency,
    getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) AS companyReportingExchangeRate,
    ( sum( IFNULL(item.cost,0) *   item.menuSalesQty  ) ) / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) )*-1 AS companyReportingAmount,
    getDecimalPlaces ( company.company_reporting_currencyID ) AS companyReportingCurrencyDecimalPlaces,
    menusalesmaster.segmentID AS segmentID,
    menusalesmaster.segmentCode AS segmentCode,
    menusalesmaster.companyID AS companyID,
    menusalesmaster.companyCode AS companyCode,
    menusalesmaster.createdUserGroup AS createdUserGroup,
    menusalesmaster.createdPCID AS createdPCID,
    menusalesmaster.createdUserID AS createdUserID,
    shiftDetail.startTime AS createdDateTime,
    menusalesmaster.createdUserName AS createdUserName,
    NULL AS modifiedPCID,
    NULL AS modifiedUserID,
    NULL AS modifiedDateTime,
    NULL AS modifiedUserName,
    CURRENT_TIMESTAMP() AS  `timestamp`
FROM
    srp_erp_pos_menusalesitemdetails item
LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = item.itemAutoID
LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = item.menuSalesID
LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = item.costGLAutoID
LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
LEFT JOIN srp_erp_unit_of_measure uom ON uom.UnitShortCode = item.UOM
LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
WHERE
    menusalesmaster.shiftID = _shiftID
AND menusalesmaster.isHold = 0 
GROUP BY
    item.costGLAutoID;


/**------------------ 4. INVENTORY -----------------------------------------------------*/
SELECT
    'POSR' AS documentCode,
    menusalesmaster.shiftID AS documentMasterAutoID,
    concat( 'POSR/', warehousemaster.wareHouseCode, '/', menusalesmaster.shiftID ) AS documentSystemCode,
    CURDATE() AS documentdate,
    YEAR (curdate()) AS documentYear,
    MONTH (curdate()) AS documentMonth,
    'POS Sales - Inventory' AS documentNarration,
    '' AS chequeNumber,
    item.assetGLAutoID AS GLAutoID,
    chartOfAccount.systemAccountCode AS systemGLCode,
    chartOfAccount.GLSecondaryCode AS GLCode,
    chartOfAccount.GLDescription AS GLDescription,
    chartOfAccount.subCategory AS GLType,
    'cr' AS amount_type,
    '0' AS isFromItem,
    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
    menusalesmaster.transactionCurrency AS transactionCurrency,
    '1' AS transactionExchangeRate,
    ( sum( IFNULL(item.cost,0) *   item.menuSalesQty  ) ) *- 1 AS transactionAmount,
    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
    company.company_default_currencyID AS companyLocalCurrencyID,
    company.company_default_currency AS companyLocalCurrency,
    getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) AS companyLocalExchangeRate,
    ( ( sum( IFNULL(item.cost,0) * item.menuSalesQty    ) ) *- 1 ) / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_default_currencyID, menusalesmaster.companyID ) ) AS companyLocalAmount,
    getDecimalPlaces ( company.company_default_currencyID ) AS companyLocalCurrencyDecimalPlaces,
    company.company_reporting_currencyID AS companyReportingCurrencyID,
    company.company_reporting_currency AS companyReportingCurrency,
    getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) AS companyReportingExchangeRate,
    ( ( sum( IFNULL(item.cost,0) *  item.menuSalesQty    ) ) *- 1 ) / ( getExchangeRate ( menusalesmaster.transactionCurrencyID, company.company_reporting_currencyID, menusalesmaster.companyID ) ) AS companyReportingAmount,
    getDecimalPlaces ( company.company_reporting_currencyID ) AS companyReportingCurrencyDecimalPlaces,
    menusalesmaster.segmentID AS segmentID,
    menusalesmaster.segmentCode AS segmentCode,
    menusalesmaster.companyID AS companyID,
    menusalesmaster.companyCode AS companyCode,
    menusalesmaster.createdUserGroup AS createdUserGroup,
    menusalesmaster.createdPCID AS createdPCID,
    menusalesmaster.createdUserID AS createdUserID,
    shiftDetail.startTime AS createdDateTime,
    menusalesmaster.createdUserName AS createdUserName,
    NULL AS modifiedPCID,
    NULL AS modifiedUserID,
    NULL AS modifiedDateTime,
    NULL AS modifiedUserName,
    CURRENT_TIMESTAMP () `timestamp`
FROM
    srp_erp_pos_menusalesitemdetails item
LEFT JOIN srp_erp_itemmaster itemmaster ON itemmaster.itemAutoID = item.itemAutoID
LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = item.menuSalesID
LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = item.assetGLAutoID
LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
LEFT JOIN srp_erp_unit_of_measure uom ON uom.UnitShortCode = item.UOM AND uom.companyID = menusalesmaster.companyID
LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
WHERE
    menusalesmaster.shiftID = _shiftID
AND menusalesmaster.isHold = 0  
GROUP BY
    item.assetGLAutoID;


/** ------------------ 5. TAX ----------------------------------------------------- */
SELECT
    'POSR' AS documentCode,
    menusalesmaster.shiftID AS documentMasterAutoID,
    concat(
        'POSR/',
        warehousemaster.wareHouseCode,
        '/',
        menusalesmaster.shiftID
    ) AS documentSystemCode,
    CURDATE() AS documentdate,
    YEAR (curdate()) AS documentYear,
    MONTH (curdate()) AS documentMonth,
    'POS Sales - TAX' AS documentNarration,
    menusalesTax.GLCode AS GLAutoID,
    chartOfAccount.systemAccountCode AS systemGLCode,
    chartOfAccount.GLSecondaryCode AS GLCode,
    chartOfAccount.GLDescription AS GLDescription,
    chartOfAccount.subCategory AS GLType,
    'cr' AS amount_type,
    '0' AS isFromItem,
    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
    menusalesmaster.transactionCurrency AS transactionCurrency,
    '1' AS transactionExchangeRate,
    abs(
        sum(
            ifnull(menusalesTax.taxAmount, 0)
        )
    ) *- 1 AS transactionAmount,
    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
    company.company_default_currencyID AS companyLocalCurrencyID,
    company.company_default_currency AS companyLocalCurrency,
    getExchangeRate (
        menusalesmaster.transactionCurrencyID,
        company.company_default_currencyID,
        menusalesmaster.companyID
    ) AS companyLocalExchangeRate,
    (
        (
            abs(
                sum(
                    ifnull(menusalesTax.taxAmount, 0)
                )
            ) *- 1
        ) / (
            getExchangeRate (
                menusalesmaster.transactionCurrencyID,
                company.company_default_currencyID,
                menusalesmaster.companyID
            )
        )
    ) AS companyLocalAmount,
    getDecimalPlaces (
        company.company_default_currencyID
    ) AS companyLocalCurrencyDecimalPlaces,
    company.company_reporting_currencyID AS companyReportingCurrencyID,
    company.company_reporting_currency AS companyReportingCurrency,
    getExchangeRate (
        menusalesmaster.transactionCurrencyID,
        company.company_reporting_currencyID,
        menusalesmaster.companyID
    ) AS companyReportingExchangeRate,
    (
        (
            abs(
                sum(
                    ifnull(menusalesTax.taxAmount, 0)
                )
            ) *- 1
        ) / (
            getExchangeRate (
                menusalesmaster.transactionCurrencyID,
                company.company_reporting_currencyID,
                menusalesmaster.companyID
            )
        )
    ) AS companyReportingAmount,
    getDecimalPlaces (
        company.company_reporting_currencyID
    ) AS companyReportingCurrencyDecimalPlaces,
    menusalesmaster.segmentID AS segmentID,
    menusalesmaster.segmentCode AS segmentCode,
    menusalesmaster.companyID AS companyID,
    menusalesmaster.companyCode AS companyCode,
    menusalesmaster.createdUserGroup AS createdUserGroup,
    menusalesmaster.createdPCID AS createdPCID,
    menusalesmaster.createdUserID AS createdUserID,
    shiftDetail.startTime AS createdDateTime,
    menusalesmaster.createdUserName AS createdUserName,
    NULL AS modifiedPCID,
    NULL AS modifiedUserID,
    NULL AS modifiedDateTime,
    NULL AS modifiedUserName,
    CURRENT_TIMESTAMP () AS `timestamp`
FROM
    srp_erp_pos_menusalestaxes menusalesTax
LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = menusalesTax.menuSalesID
LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = menusalesTax.GLCode
LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
WHERE
    menusalesmaster.shiftID = _shiftID
AND menusalesmaster.isHold = 0 AND menusalesmaster.isVoid = 0
GROUP BY
    chartOfAccount.GLAutoID;


/**------------------  6. COMMISSION EXPENSE ----------------------------------------------------- */
SELECT
    'POSR' AS documentCode,
    menusalesmaster.shiftID AS documentMasterAutoID,
    concat( 'POSR/', warehousemaster.wareHouseCode, '/', menusalesmaster.shiftID ) AS documentSystemCode,
    CURDATE() AS documentdate,
    YEAR (curdate()) AS documentYear,
    MONTH (curdate()) AS documentMonth,
    'POS Sales - Sales Commission' AS documentNarration,
    customers.expenseGLAutoID AS GLAutoID,
    chartOfAccount.systemAccountCode AS systemGLCode,
    chartOfAccount.GLSecondaryCode AS GLCode,
    chartOfAccount.GLDescription AS GLDescription,
    chartOfAccount.subCategory AS GLType,
    'dr' AS amount_type,
    '0' AS isFromItem,
    menusalesmaster.transactionCurrency AS transactionCurrency,
    '1' AS transactionExchangeRate,
    sum(
        menusalesmaster.deliveryCommissionAmount
    ) AS transactionAmount,
    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
    company.company_default_currencyID AS companyLocalCurrencyID,
    company.company_default_currency AS companyLocalCurrency,
    getExchangeRate (
        menusalesmaster.transactionCurrencyID,
        company.company_default_currencyID,
        menusalesmaster.companyID
    ) AS companyLocalExchangeRate,
    (
        sum(
            menusalesmaster.deliveryCommissionAmount
        )
    ) / (
        getExchangeRate (
            menusalesmaster.transactionCurrencyID,
            company.company_default_currencyID,
            menusalesmaster.companyID
        )
    ) AS companyLocalAmount,
    getDecimalPlaces (
        company.company_default_currencyID
    ) AS companyLocalCurrencyDecimalPlaces,
    company.company_reporting_currencyID AS companyReportingCurrencyID,
    company.company_reporting_currency AS companyReportingCurrency,
    getExchangeRate (
        menusalesmaster.transactionCurrencyID,
        company.company_reporting_currencyID,
        menusalesmaster.companyID
    ) AS companyReportingExchangeRate,
    (
        sum(
            IFNULL(menusalesmaster.deliveryCommissionAmount,0)
        )
    ) / (
        getExchangeRate (
            menusalesmaster.transactionCurrencyID,
            company.company_reporting_currencyID,
            menusalesmaster.companyID
        )
    ) AS companyReportingAmount,
    getDecimalPlaces (
        company.company_reporting_currencyID
    ) AS companyReportingCurrencyDecimalPlaces,
    menusalesmaster.segmentID AS segmentID,
    menusalesmaster.segmentCode AS segmentCode,
    menusalesmaster.companyID AS companyID,
    menusalesmaster.companyCode AS companyCode,
    menusalesmaster.createdUserGroup AS createdUserGroup,
    menusalesmaster.createdPCID AS createdPCID,
    menusalesmaster.createdUserID AS createdUserID,
    shiftDetail.startTime AS createdDateTime,
    menusalesmaster.createdUserName AS createdUserName,
    NULL AS modifiedPCID,
    NULL AS modifiedUserID,
    NULL AS modifiedDateTime,
    NULL AS modifiedUserName,
    CURRENT_TIMESTAMP () AS `timestamp`
FROM
    srp_erp_pos_menusalesmaster menusalesmaster 
LEFT JOIN srp_erp_pos_customers customers ON customers.customerID = menusalesmaster.deliveryPersonID
LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = customers.expenseGLAutoID
LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
WHERE
    menusalesmaster.shiftID = _shiftID
AND menusalesmaster.isHold = 0 AND menusalesmaster.isVoid = 0
AND (
    menusalesmaster.deliveryCommission IS NOT NULL
    AND menusalesmaster.deliveryCommission <> 0
)
AND menusalesmaster.isDelivery = 1
GROUP BY
    customers.expenseGLAutoID;


/** ------------------ 7. COMMISSION PAYABLE -----------------------------------------------------*/

SELECT
    'POSR' AS documentCode,
    menusalesmaster.shiftID AS documentMasterAutoID,
    concat( 'POSR/', warehousemaster.wareHouseCode, '/', menusalesmaster.shiftID ) AS documentSystemCode,
    CURDATE() AS documentdate,
    YEAR (curdate()) AS documentYear,
    MONTH (curdate()) AS documentMonth,
    'POS Sales - Sales Commission Payable' AS documentNarration,
    customers.liabilityGLAutoID AS GLAutoID,
    chartOfAccount.systemAccountCode AS systemGLCode,
    chartOfAccount.GLSecondaryCode AS GLCode,
    chartOfAccount.GLDescription AS GLDescription,
    chartOfAccount.subCategory AS GLType,
    'cr' AS amount_type,
    '0' AS isFromItem,
    menusalesmaster.transactionCurrency AS transactionCurrency,
    '1' AS transactionExchangeRate,
    ABS(
        sum(
            menusalesmaster.deliveryCommissionAmount
        )
    ) * - 1 AS transactionAmount,
    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
    company.company_default_currencyID AS companyLocalCurrencyID,
    company.company_default_currency AS companyLocalCurrency,
    getExchangeRate (
        menusalesmaster.transactionCurrencyID,
        company.company_default_currencyID,
        menusalesmaster.companyID
    ) AS companyLocalExchangeRate,
    (
        ABS(
            sum(
                menusalesmaster.deliveryCommissionAmount
            )
        ) * - 1
    ) / (
        getExchangeRate (
            menusalesmaster.transactionCurrencyID,
            company.company_default_currencyID,
            menusalesmaster.companyID
        )
    ) AS companyLocalAmount,
    getDecimalPlaces (
        company.company_default_currencyID
    ) AS companyLocalCurrencyDecimalPlaces,
    company.company_reporting_currencyID AS companyReportingCurrencyID,
    company.company_reporting_currency AS companyReportingCurrency,
    getExchangeRate (
        menusalesmaster.transactionCurrencyID,
        company.company_reporting_currencyID,
        menusalesmaster.companyID
    ) AS companyReportingExchangeRate,
    (
        ABS(
            sum(
               IFNULL( menusalesmaster.deliveryCommissionAmount,0)
            )
        ) * - 1
    ) / (
        getExchangeRate (
            menusalesmaster.transactionCurrencyID,
            company.company_reporting_currencyID,
            menusalesmaster.companyID
        )
    ) AS companyReportingAmount,
    getDecimalPlaces (
        company.company_reporting_currencyID
    ) AS companyReportingCurrencyDecimalPlaces,
    menusalesmaster.segmentID AS segmentID,
    menusalesmaster.segmentCode AS segmentCode,
    menusalesmaster.companyID AS companyID,
    menusalesmaster.companyCode AS companyCode,
    menusalesmaster.createdUserGroup AS createdUserGroup,
    menusalesmaster.createdPCID AS createdPCID,
    menusalesmaster.createdUserID AS createdUserID,
    shiftDetail.startTime AS createdDateTime,
    menusalesmaster.createdUserName AS createdUserName,
    NULL AS modifiedPCID,
    NULL AS modifiedUserID,
    NULL AS modifiedDateTime,
    NULL AS modifiedUserName,
    CURRENT_TIMESTAMP () AS `timestamp`
FROM
    srp_erp_pos_menusalesmaster menusalesmaster 
LEFT JOIN srp_erp_pos_customers customers ON customers.customerID = menusalesmaster.deliveryPersonID

LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = customers.liabilityGLAutoID
LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
WHERE
    menusalesmaster.shiftID = _shiftID
AND menusalesmaster.isHold = 0 AND menusalesmaster.isVoid = 0
AND (
    menusalesmaster.deliveryCommission IS NOT NULL
    AND menusalesmaster.deliveryCommission <> 0
)
AND menusalesmaster.isDelivery = 1
AND menusalesmaster.isOnTimeCommision = 0
GROUP BY
    customers.liabilityGLAutoID;

/** ------------------ 8. ROYALTY PAYABLE -----------------------------------------------------*/
SELECT
    'POSR' AS documentCode,
    menusalesmaster.shiftID AS documentMasterAutoID,
    concat(
        'POSR/',
        warehousemaster.wareHouseCode,
        '/',
        menusalesmaster.shiftID
    ) AS documentSystemCode,
    CURDATE() AS documentdate,
    YEAR (curdate()) AS documentYear,
    MONTH (curdate()) AS documentMonth,
    'POS Sales - Royalty Payable' AS documentNarration,
    franchisemaster.royaltyLiabilityGLAutoID AS GLAutoID,
    chartOfAccount.systemAccountCode AS systemGLCode,
    chartOfAccount.GLSecondaryCode AS GLCode,
    chartOfAccount.GLDescription AS GLDescription,
    chartOfAccount.subCategory AS GLType,
    'cr' AS amount_type,
    '0' AS isFromItem,
    menusalesmaster.transactionCurrency AS transactionCurrency,
    '1' AS transactionExchangeRate,
    ABS(
        sum(
            (IFNULL(menusalesmaster.netTotal,0)+IFNULL(menusalesmaster.serviceCharge,0)) * (
                franchisemaster.royaltyPercentage / 100
            )
        )
    ) * - 1 AS transactionAmount,
    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
    company.company_default_currencyID AS companyLocalCurrencyID,
    company.company_default_currency AS companyLocalCurrency,
    getExchangeRate (
        menusalesmaster.transactionCurrencyID,
        company.company_default_currencyID,
        menusalesmaster.companyID
    ) AS companyLocalExchangeRate,
    (
        ABS(
            sum(
                (IFNULL(menusalesmaster.netTotal,0)+IFNULL(menusalesmaster.serviceCharge,0)) * (
                    franchisemaster.royaltyPercentage / 100
                )
            )
        ) * - 1
    ) / (
        getExchangeRate (
            menusalesmaster.transactionCurrencyID,
            company.company_default_currencyID,
            menusalesmaster.companyID
        )
    ) AS companyLocalAmount,
    getDecimalPlaces (
        company.company_default_currencyID
    ) AS companyLocalCurrencyDecimalPlaces,
    company.company_reporting_currencyID AS companyReportingCurrencyID,
    company.company_reporting_currency AS companyReportingCurrency,
    getExchangeRate (
        menusalesmaster.transactionCurrencyID,
        company.company_reporting_currencyID,
        menusalesmaster.companyID
    ) AS companyReportingExchangeRate,
    (
        ABS(
            sum(
                (IFNULL(menusalesmaster.netTotal,0)+IFNULL(menusalesmaster.serviceCharge,0)) * (
                    franchisemaster.royaltyPercentage / 100
                )
            )
        ) * - 1
    ) / (
        getExchangeRate (
            menusalesmaster.transactionCurrencyID,
            company.company_reporting_currencyID,
            menusalesmaster.companyID
        )
    ) AS companyReportingAmount,
    getDecimalPlaces (
        company.company_reporting_currencyID
    ) AS companyReportingCurrencyDecimalPlaces,
    menusalesmaster.segmentID AS segmentID,
    menusalesmaster.segmentCode AS segmentCode,
    menusalesmaster.companyID AS companyID,
    menusalesmaster.companyCode AS companyCode,
    menusalesmaster.createdUserGroup AS createdUserGroup,
    menusalesmaster.createdPCID AS createdPCID,
    menusalesmaster.createdUserID AS createdUserID,
    shiftDetail.startTime AS createdDateTime,
    menusalesmaster.createdUserName AS createdUserName,
    NULL AS modifiedPCID,
    NULL AS modifiedUserID,
    NULL AS modifiedDateTime,
    NULL AS modifiedUserName,
    CURRENT_TIMESTAMP () AS `timestamp`
FROM
    srp_erp_pos_menusalesmaster menusalesmaster 
INNER JOIN srp_erp_pos_franchisemaster franchisemaster ON franchisemaster.warehouseAutoID = menusalesmaster.wareHouseAutoID
LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = franchisemaster.royaltyLiabilityGLAutoID
LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
WHERE
    menusalesmaster.shiftID = _shiftID
AND menusalesmaster.isHold = 0 AND menusalesmaster.isVoid = 0
GROUP BY
    franchisemaster.royaltyLiabilityGLAutoID;


/** ------------------ 9. ROYALTY EXPENSES -----------------------------------------------------*/
SELECT
    'POSR' AS documentCode,
    menusalesmaster.shiftID AS documentMasterAutoID,
    concat(
        'POSR/',
        warehousemaster.wareHouseCode,
        '/',
        menusalesmaster.shiftID
    ) AS documentSystemCode,
    CURDATE() AS documentdate,
    YEAR (curdate()) AS documentYear,
    MONTH (curdate()) AS documentMonth,
    'POS Sales - Royalty Expenses' AS documentNarration,
    franchisemaster.royaltyExpenseGLAutoID AS GLAutoID,
    chartOfAccount.systemAccountCode AS systemGLCode,
    chartOfAccount.GLSecondaryCode AS GLCode,
    chartOfAccount.GLDescription AS GLDescription,
    chartOfAccount.subCategory AS GLType,
    'dr' AS amount_type,
    '0' AS isFromItem,
    menusalesmaster.transactionCurrency AS transactionCurrency,
    '1' AS transactionExchangeRate,
    sum(
        ( IFNULL(menusalesmaster.netTotal,0)+IFNULL(menusalesmaster.serviceCharge,0) ) * (
            franchisemaster.royaltyPercentage / 100
        )
    ) AS transactionAmount,
    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
    company.company_default_currencyID AS companyLocalCurrencyID,
    company.company_default_currency AS companyLocalCurrency,
    getExchangeRate (
        menusalesmaster.transactionCurrencyID,
        company.company_default_currencyID,
        menusalesmaster.companyID
    ) AS companyLocalExchangeRate,
    (
        sum(
            ( menusalesmaster.netTotal + menusalesmaster.serviceCharge ) * (
                franchisemaster.royaltyPercentage / 100
            )
        )
    ) / (
        getExchangeRate (
            menusalesmaster.transactionCurrencyID,
            company.company_default_currencyID,
            menusalesmaster.companyID
        )
    ) AS companyLocalAmount,
    getDecimalPlaces (
        company.company_default_currencyID
    ) AS companyLocalCurrencyDecimalPlaces,
    company.company_reporting_currencyID AS companyReportingCurrencyID,
    company.company_reporting_currency AS companyReportingCurrency,
    getExchangeRate (
        menusalesmaster.transactionCurrencyID,
        company.company_reporting_currencyID,
        menusalesmaster.companyID
    ) AS companyReportingExchangeRate,
    (
        sum(
            ( menusalesmaster.netTotal + menusalesmaster.serviceCharge ) * (
                franchisemaster.royaltyPercentage / 100
            )
        )
    ) / (
        getExchangeRate (
            menusalesmaster.transactionCurrencyID,
            company.company_reporting_currencyID,
            menusalesmaster.companyID
        )
    ) AS companyReportingAmount,
    getDecimalPlaces (
        company.company_reporting_currencyID
    ) AS companyReportingCurrencyDecimalPlaces,
    menusalesmaster.segmentID AS segmentID,
    menusalesmaster.segmentCode AS segmentCode,
    menusalesmaster.companyID AS companyID,
    menusalesmaster.companyCode AS companyCode,
    menusalesmaster.createdUserGroup AS createdUserGroup,
    menusalesmaster.createdPCID AS createdPCID,
    menusalesmaster.createdUserID AS createdUserID,
    shiftDetail.startTime AS createdDateTime,
    menusalesmaster.createdUserName AS createdUserName,
    NULL AS modifiedPCID,
    NULL AS modifiedUserID,
    NULL AS modifiedDateTime,
    NULL AS modifiedUserName,
    CURRENT_TIMESTAMP () AS `timestamp`
FROM
    srp_erp_pos_menusalesmaster menusalesmaster 
INNER JOIN srp_erp_pos_franchisemaster franchisemaster ON franchisemaster.warehouseAutoID = menusalesmaster.wareHouseAutoID
LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = franchisemaster.royaltyExpenseGLAutoID
LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID

WHERE
    menusalesmaster.shiftID = _shiftID
AND menusalesmaster.isHold = 0 AND menusalesmaster.isVoid = 0
GROUP BY
    franchisemaster.royaltyExpenseGLAutoID;


/** ------------------ 10. SERVICE CHARGE -----------------------------------------------------*/
SELECT
    'POSR' AS documentCode,
    menusalesmaster.shiftID AS documentMasterAutoID,
    concat(
        'POSR/',
        warehousemaster.wareHouseCode,
        '/',
        menusalesmaster.shiftID
    ) AS documentSystemCode,
    CURDATE() AS documentdate,
    YEAR (curdate()) AS documentYear,
    MONTH (curdate()) AS documentMonth,
    'POS Sales - Service Charge' AS documentNarration,
    servicecharge.GLAutoID AS GLAutoID,
    chartOfAccount.systemAccountCode AS systemGLCode,
    chartOfAccount.GLSecondaryCode AS GLCode,
    chartOfAccount.GLDescription AS GLDescription,
    chartOfAccount.subCategory AS GLType,
    'cr' AS amount_type,
    '0' AS isFromItem,
    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
    menusalesmaster.transactionCurrency AS transactionCurrency,
    '1' AS transactionExchangeRate,
    abs(
        sum(
            ifnull(
                servicecharge.serviceChargeAmount,
                0
            )
        )
    ) *- 1 AS transactionAmount,
    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
    company.company_default_currencyID AS companyLocalCurrencyID,
    company.company_default_currency AS companyLocalCurrency,
    getExchangeRate (
        menusalesmaster.transactionCurrencyID,
        company.company_default_currencyID,
        menusalesmaster.companyID
    ) AS companyLocalExchangeRate,
    (
        (
            abs(
                sum(
                    ifnull(
                        servicecharge.serviceChargeAmount,
                        0
                    )
                )
            ) *- 1
        ) / (
            getExchangeRate (
                menusalesmaster.transactionCurrencyID,
                company.company_default_currencyID,
                menusalesmaster.companyID
            )
        )
    ) AS companyLocalAmount,
    getDecimalPlaces (
        company.company_default_currencyID
    ) AS companyLocalCurrencyDecimalPlaces,
    company.company_reporting_currencyID AS companyReportingCurrencyID,
    company.company_reporting_currency AS companyReportingCurrency,
    getExchangeRate (
        menusalesmaster.transactionCurrencyID,
        company.company_reporting_currencyID,
        menusalesmaster.companyID
    ) AS companyReportingExchangeRate,
    (
        (
            abs(
                sum(
                    ifnull(
                        servicecharge.serviceChargeAmount,
                        0
                    )
                )
            ) *- 1
        ) / (
            getExchangeRate (
                menusalesmaster.transactionCurrencyID,
                company.company_reporting_currencyID,
                menusalesmaster.companyID
            )
        )
    ) AS companyReportingAmount,
    getDecimalPlaces (
        company.company_reporting_currencyID
    ) AS companyReportingCurrencyDecimalPlaces,
    menusalesmaster.segmentID AS segmentID,
    menusalesmaster.segmentCode AS segmentCode,
    menusalesmaster.companyID AS companyID,
    menusalesmaster.companyCode AS companyCode,
    menusalesmaster.createdUserGroup AS createdUserGroup,
    menusalesmaster.createdPCID AS createdPCID,
    menusalesmaster.createdUserID AS createdUserID,
    shiftDetail.startTime AS createdDateTime,
    menusalesmaster.createdUserName AS createdUserName,
    NULL AS modifiedPCID,
    NULL AS modifiedUserID,
    NULL AS modifiedDateTime,
    NULL AS modifiedUserName,
    CURRENT_TIMESTAMP () AS `timestamp`
FROM
    srp_erp_pos_menusalesservicecharge servicecharge
LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON menusalesmaster.menuSalesID = servicecharge.menuSalesID
LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = servicecharge.GLAutoID
LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
LEFT JOIN srp_erp_pos_shiftdetails shiftDetail ON shiftDetail.shiftID = menusalesmaster.shiftID
WHERE
    menusalesmaster.shiftID = _shiftID
AND menusalesmaster.isHold = 0 AND menusalesmaster.isVoid = 0
GROUP BY
    chartOfAccount.GLAutoID;


/** ------------------ 11. CREDIT CUSTOMER PAYMENTS -----------------------------------------------------*/

SELECT
    'POSR' AS documentCode,
    menusalesmaster.shiftID AS documentMasterAutoID,
    concat(
        'POSR/',
        warehousemaster.wareHouseCode,
        '/',
        menusalesmaster.shiftID
    ) AS documentSystemCode,
    CURDATE() AS documentdate,
    YEAR (curdate()) AS documentYear,
    MONTH (curdate()) AS documentMonth,
    'POS Credit Sales' AS documentNarration,
    '' AS chequeNumber,
    chartOfAccount.GLAutoID AS GLAutoID,
    chartOfAccount.systemAccountCode AS systemGLCode,
    chartOfAccount.GLSecondaryCode AS GLCode,
    chartOfAccount.GLDescription AS GLDescription,
    chartOfAccount.subCategory AS GLType,
    'dr' AS amount_type,
    '0' AS isFromItem,
    menusalesmaster.transactionCurrencyID AS transactionCurrencyID,
    menusalesmaster.transactionCurrency AS transactionCurrency,
    '1' AS transactionExchangeRate,
    SUM(payments.amount) AS transactionAmount,
    currencymaster.DecimalPlaces AS transactionCurrencyDecimalPlaces,
    company.company_default_currencyID AS companyLocalCurrencyID,
    company.company_default_currency AS companyLocalCurrency,
    getExchangeRate (
        menusalesmaster.transactionCurrencyID,
        company.company_default_currencyID,
        menusalesmaster.companyID
    ) AS companyLocalExchangeRate,
    SUM(payments.amount) / (
        getExchangeRate (
            menusalesmaster.transactionCurrencyID,
            company.company_default_currencyID,
            menusalesmaster.companyID
        )
    ) AS companyLocalAmount,
    getDecimalPlaces (
        company.company_default_currencyID
    ) AS companyLocalCurrencyDecimalPlaces,
    company.company_reporting_currencyID AS companyReportingCurrencyID,
    company.company_reporting_currency AS companyReportingCurrency,
    getExchangeRate (
        menusalesmaster.transactionCurrencyID,
        company.company_reporting_currencyID,
        menusalesmaster.companyID
    ) AS companyReportingExchangeRate,
    SUM(payments.amount) / (
        getExchangeRate (
            menusalesmaster.transactionCurrencyID,
            company.company_reporting_currencyID,
            menusalesmaster.companyID
        )
    ) AS companyReportingAmount,
    getDecimalPlaces (
        company.company_reporting_currencyID
    ) AS companyReportingCurrencyDecimalPlaces,
    'CUS' AS partyType,
    payments.customerAutoID AS partyAutoID,
    customermaster.customerSystemCode AS partySystemCode,
    customermaster.customerName AS partyName,
    customermaster.customerCurrencyID AS partyCurrencyID,
    customermaster.customerCurrency AS partyCurrency,
    getExchangeRate (
        customermaster.customerCurrencyID,
        company.company_default_currencyID,
        menusalesmaster.companyID
    ) AS partyExchangeRate,
    SUM(payments.amount) / (
        getExchangeRate (
            customermaster.customerCurrencyID,
            company.company_reporting_currencyID,
            menusalesmaster.companyID
        )
    ) AS partyCurrencyAmount,
    customermaster.customerCurrencyDecimalPlaces AS partyCurrencyDecimalPlaces,
    3 AS subLedgerType,
    'AR' AS subLedgerDesc,
    menusalesmaster.segmentID AS segmentID,
    menusalesmaster.segmentCode AS segmentCode,
    menusalesmaster.companyID AS companyID,
    menusalesmaster.companyCode AS companyCode,
    menusalesmaster.createdUserGroup AS createdUserGroup,
    menusalesmaster.createdPCID AS createdPCID,
    menusalesmaster.createdUserID createdUserID,
    CURRENT_TIMESTAMP () createdDateTime,
    menusalesmaster.createdUserName createdUserName,
    NULL AS modifiedPCID,
    NULL AS modifiedUserID,
    NULL AS modifiedDateTime,
    NULL AS modifiedUserName,
    CURRENT_TIMESTAMP () `timestamp`
FROM
    srp_erp_pos_menusalespayments payments
LEFT JOIN srp_erp_pos_menusalesmaster menusalesmaster ON payments.menuSalesID = menusalesmaster.menuSalesID
LEFT JOIN srp_erp_customermaster customermaster ON customermaster.customerAutoID = payments.customerAutoID
LEFT JOIN srp_erp_chartofaccounts chartOfAccount ON chartOfAccount.GLAutoID = customermaster.receivableAutoID
LEFT JOIN srp_erp_warehousemaster warehousemaster ON warehousemaster.wareHouseAutoID = menusalesmaster.wareHouseAutoID
LEFT JOIN srp_erp_currencymaster currencymaster ON currencymaster.currencyID = menusalesmaster.transactionCurrencyID
LEFT JOIN srp_erp_company company ON company.company_id = menusalesmaster.companyID
WHERE
    menusalesmaster.shiftID = _shiftID
AND menusalesmaster.isHold = 0
AND menusalesmaster.isVoid = 0
AND payments.paymentConfigMasterID = 7
GROUP BY
    chartOfAccount.GLAutoID,
    payments.customerAutoID;






END
;;
DELIMITER ;

-- ----------------------------
-- Function structure for getCompanyFinancePeriodID
-- ----------------------------
DROP FUNCTION IF EXISTS `getCompanyFinancePeriodID`;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getCompanyFinancePeriodID`(`p_companyID` INT) RETURNS int(11)
BEGIN
DECLARE
	OUTPUT_id INT;

SET OUTPUT_id = ( SELECT companyFinancePeriodID FROM srp_erp_companyfinanceperiod WHERE dateFrom < CURDATE( ) AND dateTo > CURDATE( ) AND companyID = p_companyID );
RETURN ( OUTPUT_id );

END
;;
DELIMITER ;

-- ----------------------------
-- Function structure for getCompanyFinanceYearID
-- ----------------------------
DROP FUNCTION IF EXISTS `getCompanyFinanceYearID`;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getCompanyFinanceYearID`(`p_companyID` INT) RETURNS int(11)
BEGIN
DECLARE
	OUTPUT_id INT;

SET OUTPUT_id = (
SELECT
FY.companyFinanceYearID 
FROM
srp_erp_companyfinanceperiod FP
LEFT JOIN srp_erp_companyfinanceyear FY ON FY.companyFinanceYearID = FP.companyFinanceYearID 
WHERE
FP.dateFrom < CURDATE( ) AND FP.dateTo > CURDATE( ) 
	AND FP.companyID = p_companyID 
	);
RETURN ( OUTPUT_id );

END
;;
DELIMITER ;

-- ----------------------------
-- Function structure for getDecimalPlaces
-- ----------------------------
DROP FUNCTION IF EXISTS `getDecimalPlaces`;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getDecimalPlaces`(`p_currencyID` INT) RETURNS double
BEGIN 
  DECLARE conversion_OUTPUT DOUBLE ;
SET conversion_OUTPUT = (
	SELECT
		decimalplaces
	FROM
		srp_erp_currencymaster
WHERE
    currencyID=p_currencyID
) ; RETURN (conversion_OUTPUT) ;
END
;;
DELIMITER ;

-- ----------------------------
-- Function structure for getEmpCount
-- ----------------------------
DROP FUNCTION IF EXISTS `getEmpCount`;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getEmpCount`(`comID` int) RETURNS int(11)
BEGIN
	DECLARE eCount INT;
	SELECT  count(EIdNo) INTO eCount FROM srp_employeesdetails eTB WHERE Erp_companyID= comID;

	RETURN eCount;
END
;;
DELIMITER ;

-- ----------------------------
-- Function structure for getExchangeRate
-- ----------------------------
DROP FUNCTION IF EXISTS `getExchangeRate`;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getExchangeRate`(`p_transactionCurrencyID` INT, `p_subCurrencyID` INT, `p_companyID` INT) RETURNS double
BEGIN

DECLARE conversion_OUTPUT DOUBLE ;
SET conversion_OUTPUT = (
	SELECT
		conversion
	FROM
		srp_erp_companycurrencyconversion
WHERE
	companyID = p_companyID
AND masterCurrencyID = p_transactionCurrencyID
AND subCurrencyID = p_subCurrencyID
) ; RETURN (conversion_OUTPUT) ;
END
;;
DELIMITER ;

-- ----------------------------
-- Function structure for getFormatedAmount
-- ----------------------------
DROP FUNCTION IF EXISTS `getFormatedAmount`;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getFormatedAmount`(_amount double,_currencyCode varchar(3)) RETURNS double
BEGIN
   DECLARE decimalPlaces double;
    DECLARE formatedAmount double;
   SET decimalPlaces = (SELECT DecimalPlaces from srp_erp_currencymaster where CurrencyCode=_currencyCode);
   SET formatedAmount=(SELECT REPLACE(ifnull(format(_amount,decimalPlaces),0),',','') as formatedAmount);
   RETURN formatedAmount;
END
;;
DELIMITER ;

-- ----------------------------
-- Function structure for getFormatedAmount_copy
-- ----------------------------
DROP FUNCTION IF EXISTS `getFormatedAmount_copy`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` FUNCTION `getFormatedAmount_copy`(_amount double,_currencyCode varchar(4)) RETURNS double
BEGIN
   DECLARE decimalPlaces double;
    DECLARE formatedAmount double;
   SET decimalPlaces = (SELECT DecimalPlaces from srp_erp_currencymaster where CurrencyCode=_currencyCode);
   SET formatedAmount=(SELECT REPLACE(ifnull(format(_amount,decimalPlaces),0),',','') as formatedAmount);
   RETURN decimalPlaces;
END
;;
DELIMITER ;

-- ----------------------------
-- Function structure for getMenuSalesCustomerAutoID
-- ----------------------------
DROP FUNCTION IF EXISTS `getMenuSalesCustomerAutoID`;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getMenuSalesCustomerAutoID`(`p_menuSalesID` INT) RETURNS int(11)
BEGIN
DECLARE
	OUTPUT_id INT;

SET OUTPUT_id = (
SELECT
P.customerAutoID 
FROM
srp_erp_pos_menusalesmaster AS MS
LEFT JOIN srp_erp_pos_menusalespayments AS P ON P.menuSalesID = MS.menuSalesID 
WHERE
P.customerAutoID > 0 
AND MS.menuSalesID = p_menuSalesID 
);
RETURN ( OUTPUT_id );

END
;;
DELIMITER ;

-- ----------------------------
-- Function structure for getposbankGL
-- ----------------------------
DROP FUNCTION IF EXISTS `getposbankGL`;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getposbankGL`(p_paymentmethodID int,p_companyID int) RETURNS int(11)
    DETERMINISTIC
BEGIN 

  DECLARE bankGL int;

if p_paymentmethodID=1 or p_paymentmethodID=4 then

SET bankGL = (select GLCode from srp_erp_pos_paymentglconfigdetail where companyID=p_companyID and paymentConfigMasterID=1);

end if;

if p_paymentmethodID=2 then

 set bankGL=(select GLCode from srp_erp_pos_paymentglconfigdetail where companyID=p_companyID and paymentConfigMasterID=4);

end if;

if p_paymentmethodID=3 then

set bankGL=(select GLCode from srp_erp_pos_paymentglconfigdetail where companyID=p_companyID and paymentConfigMasterID=3);

end if;

 RETURN (bankGL) ;

END
;;
DELIMITER ;

-- ----------------------------
-- Function structure for getposbankGL_nsk
-- ----------------------------
DROP FUNCTION IF EXISTS `getposbankGL_nsk`;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getposbankGL_nsk`(p_paymentmethodID int,p_companyID int) RETURNS int(11)
    DETERMINISTIC
BEGIN 

  DECLARE bankGL int;

if p_paymentmethodID=1 or p_paymentmethodID=4 then

SET bankGL = (select GLCode from srp_erp_pos_paymentglconfigdetail where companyID=p_companyID and paymentConfigMasterID=1);

end if;

if p_paymentmethodID=2 then

 set bankGL=(select GLCode from srp_erp_pos_paymentglconfigdetail where companyID=p_companyID and paymentConfigMasterID=4);

end if;

if p_paymentmethodID=3 then

set bankGL=(select GLCode from srp_erp_pos_paymentglconfigdetail where companyID=p_companyID and paymentConfigMasterID=3);

end if;

 RETURN (bankGL) ;

END
;;
DELIMITER ;

-- ----------------------------
-- Function structure for getUoMConvertion
-- ----------------------------
DROP FUNCTION IF EXISTS `getUoMConvertion`;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getUoMConvertion`(`p_transactionUoM` INT, `p_defaultUoM` INT, `p_companyID` INT) RETURNS double
BEGIN
DECLARE conversion_OUTPUT DOUBLE;
SET conversion_OUTPUT = (
	SELECT conversion FROM `srp_erp_unitsconversion` WHERE `companyID` = p_companyID AND `masterUnitID` = p_defaultUoM  AND `subUnitID` = p_transactionUoM
) ; 
RETURN (conversion_OUTPUT) ;
END
;;
DELIMITER ;

-- ----------------------------
-- Function structure for policycompanyID
-- ----------------------------
DROP FUNCTION IF EXISTS `policycompanyID`;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `policycompanyID`() RETURNS int(11)
RETURN @var
;;
DELIMITER ;
