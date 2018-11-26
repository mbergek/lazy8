
CREATE TABLE IF NOT EXISTS `User` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(128) NOT NULL,
  `password` varchar(128) NOT NULL,
  `salt` varchar(128) NOT NULL,
  `displayname` varchar(128) NOT NULL,
  `mobil` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `selectedCompanyId` int(11) DEFAULT 0,
  `selectedPeriodId` int(11) DEFAULT 0,
  `changedBy` varchar(128),
  `dateChanged` datetime,
  `dateLastLogin` datetime,
  `dateLastLogout` datetime,
  PRIMARY KEY  (`id`),
  UNIQUE  (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `Options` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(128) NOT NULL,
  `datavalue` text,
  `companyId` int(11) DEFAULT 0,
  `userId` int(11) DEFAULT 0,
  `changedBy` varchar(128),
  `dateChanged` datetime,
  PRIMARY KEY  (`id`),
  UNIQUE  (`name`,`companyId`,`userId`),
  CONSTRAINT FK_Options_Company FOREIGN KEY (companyId)
    REFERENCES Company (id) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT FK_Options_User FOREIGN KEY (userId)
    REFERENCES User (id) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ChangeLog` (
  `id` int(11) NOT NULL auto_increment,
  `tableName` varchar(128),
  `logType` enum('UPDATE','ADD','DELETE','OTHER'),
  `companyId` int(11),
  `desc` text,
  `changedBy` varchar(128),
  `dateChanged` datetime,
  PRIMARY KEY  (`id`),
  CONSTRAINT FK_ChangeLog_Company FOREIGN KEY (companyId)
    REFERENCES Company (id) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `Report` (
  `id` int(11) NOT NULL auto_increment,
  `companyId` int(11) DEFAULT 0,
  `name` varchar(100) NOT NULL,
  `desc` varchar(255),
  `sortOrder` int(11) DEFAULT 0,
  `selectSql` text,
  `cssColorFileName` varchar(255),
  `cssBwFileName` varchar(255),
  `changedBy` varchar(128),
  `dateChanged` datetime,
  PRIMARY KEY  (`id`),
  CONSTRAINT FK_Report_Company FOREIGN KEY (companyId)
    REFERENCES Company (id) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ReportUserLastUsedParams` (
  `id` int(11) NOT NULL auto_increment,
  `reportId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `paramId` int(11) NOT NULL,
  `LastUsedValue` varchar(255),
  PRIMARY KEY  (`id`),
  CONSTRAINT FK_ReportUserLast_User FOREIGN KEY (userId)
    REFERENCES User (id) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT FK_ReportUserLast_Report FOREIGN KEY (reportId)
    REFERENCES Report (id) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT FK_ReportUserLast_ReportParameters FOREIGN KEY (paramId)
    REFERENCES ReportParameters (id) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ReportParameters` (
  `id` int(11) NOT NULL auto_increment,
  `reportId` int(11) NOT NULL,
  `sortOrder` int(11),
  `name` varchar(100),
  `desc` varchar(255),
  `alias` varchar(100),
  `dataType` enum('FREE_TEXT','DROP_DOWN','DATE','BOOLEAN','HIDDEN_SHOW_HEAD','HIDDEN_NO_SHOW_HEAD'),
  `phpSecondaryInfo` text,
  `isDefaultPhp` BOOLEAN,
  `defaultValue` text,
  `isDate` BOOLEAN,
  `isDecimal` BOOLEAN,
  PRIMARY KEY  (`id`),
  CONSTRAINT FK_ReportParameters_Report FOREIGN KEY (reportId)
    REFERENCES Report (id) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ReportGroups` (
  `id` int(11) NOT NULL auto_increment,
  `reportId` int(11) NOT NULL,
  `sortOrder` int(11),
  `breakingField` varchar(100) ,
  `pageBreak` BOOLEAN,
  `showGrid` BOOLEAN,
  `showHeader` BOOLEAN,
  `continueSumsOverGroup` BOOLEAN,
  PRIMARY KEY  (`id`),
  CONSTRAINT FK_ReportGroups_Report FOREIGN KEY (reportId)
    REFERENCES Report (id) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ReportGroupFields` (
  `id` int(11) NOT NULL auto_increment,
  `reportGroupId` int(11) NOT NULL,
  `sortOrder` int(11),
  `fieldName` varchar(100) ,
  `fieldCalc` text ,
  `fieldWidth` varchar(10),
  `row` int(11),
  `isDate` BOOLEAN,
  `isDecimal` BOOLEAN,
  PRIMARY KEY  (`id`),
  CONSTRAINT FK_ReportGroupFields_ReportGroups FOREIGN KEY (reportGroupId)
    REFERENCES ReportGroups (id) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ReportRows` (
  `id` int(11) NOT NULL auto_increment,
  `reportId` int(11) NOT NULL,
  `sortOrder` int(11),
  `fieldName` varchar(100) ,
  `fieldCalc` text ,
  `fieldWidth` varchar(10),
  `row` int(11),
  `isSummed` BOOLEAN,
  `isAlignRight` BOOLEAN,
  `isDate` BOOLEAN,
  `isDecimal` BOOLEAN,
  PRIMARY KEY  (`id`),
  CONSTRAINT FK_ReportRows_Report FOREIGN KEY (reportId)
    REFERENCES Report (id) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `Company` (
  `id` int(11) NOT NULL auto_increment,
  `code` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `lastAbsTransNum` int(11) NOT NULL,
  `changedBy` varchar(128),
  `dateChanged` datetime,
  PRIMARY KEY  (`id`),
  UNIQUE  (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `CompanyUser` (
  `userId` int(11) NOT NULL,
  `companyId` int(11) NOT NULL,
  PRIMARY KEY  (`userId`,`companyId`),
  CONSTRAINT FK_CompanyUser_Company FOREIGN KEY (companyId)
    REFERENCES Company (id) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT FK_CompanyUser_User FOREIGN KEY (userId)
    REFERENCES User (id) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `Customer` (
  `id` int(11) NOT NULL auto_increment,
  `companyId` int(11) NOT NULL,
  `code` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `desc` varchar(255),
  `accountId` int(11) NOT NULL,
  `changedBy` varchar(128),
  `dateChanged` datetime,
  PRIMARY KEY  (`id`),
  UNIQUE  (`companyId`,`code`),
  CONSTRAINT FK_Customer_Account FOREIGN KEY (accountId)
    REFERENCES Account (id) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT FK_Customer_Company FOREIGN KEY (companyId)
    REFERENCES Company (id) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `TempTrans` (
  `id` int(11) NOT NULL auto_increment,
  `rownum` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `invDate` date,
  `regDate` date,
  `periodNum` int(11) NOT NULL,
  `companyNum` int(11) NOT NULL,
  `notesheader` varchar(255),
  `fileInfo` varchar(255),
  `accountId` int(11) NOT NULL,
  `customerId` int(11),
  `notes` varchar(255),
  `amountdebit` DOUBLE  DEFAULT NULL,
  `amountcredit` DOUBLE  DEFAULT NULL,
  `changedBy` varchar(128),
  `dateChanged` datetime,
  PRIMARY KEY  (`id`),
  CONSTRAINT FK_TempTrans_Account FOREIGN KEY (accountId)
    REFERENCES Account (id) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT FK_TempTrans_Customer FOREIGN KEY (customerId)
    REFERENCES Customer (id) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `Trans` (
  `id` int(11) NOT NULL auto_increment,
  `companyId` int(11) NOT NULL,
  `regDate` date,
  `invDate` date,
  `periodId` int(11) NOT NULL,
  `periodNum` int(11) NOT NULL,
  `companyNum` int(11) NOT NULL,
  `notes` varchar(255),
  `fileInfo` varchar(255),
  `changedBy` varchar(128),
  `dateChanged` datetime,
  PRIMARY KEY  (`id`),
  UNIQUE  (`companyId`,`periodId`,`periodNum`),
  UNIQUE  (`companyId`,`companyNum`),
  CONSTRAINT FK_Trans_Period FOREIGN KEY (periodId)
    REFERENCES Period (id) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT FK_Trans_Company FOREIGN KEY (companyId)
    REFERENCES Company (id) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `TransRow` (
  `id` int(11) NOT NULL auto_increment,
  `transId` int(11) NOT NULL,
  `accountId` int(11) NOT NULL,
  `customerId` int(11),
  `notes` varchar(255),
  `amount` DOUBLE  DEFAULT NULL,
  PRIMARY KEY  (`id`),
  CONSTRAINT FK_TransRow_Trans FOREIGN KEY (transId)
    REFERENCES Trans (id) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT FK_TransRow_Account FOREIGN KEY (accountId)
    REFERENCES Account (id) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT FK_TransRow_Customer FOREIGN KEY (customerId)
    REFERENCES Customer (id) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `AccountType` (
  `id` int(11) NOT NULL auto_increment,
  `companyId` int(11) NOT NULL,
  `code` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `sortOrder` int(11) NOT NULL,
  `isInBalance` BOOLEAN NOT NULL,
  `changedBy` varchar(128),
  `dateChanged` datetime,
  PRIMARY KEY  (`id`),
  UNIQUE  (`companyId`,`code`),
  UNIQUE  (`companyId`,`sortOrder`),
  CONSTRAINT FK_AccountType_Company FOREIGN KEY (companyId)
    REFERENCES Company (id) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `Account` (
  `id` int(11) NOT NULL auto_increment,
  `companyId` int(11) NOT NULL,
  `accountTypeId` int(11) NOT NULL,
  `code` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `changedBy` varchar(128),
  `dateChanged` datetime,
  PRIMARY KEY  (`id`),
  UNIQUE  (`companyId`,`code`),
  CONSTRAINT FK_Account_AccountType FOREIGN KEY (accountTypeId)
    REFERENCES AccountType (id) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT FK_Account_Company FOREIGN KEY (companyId)
    REFERENCES Company (id) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `Period` (
  `id` int(11) NOT NULL auto_increment,
  `companyId` int(11) NOT NULL,
  `dateStart` date,
  `dateEnd` date,
  `lastPeriodTransNum` int(11),
  `changedBy` varchar(128),
  `dateChanged` datetime,
  PRIMARY KEY  (`id`),
  CONSTRAINT FK_Period_Company FOREIGN KEY (companyId)
    REFERENCES Company (id) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS SourceMessage
(
  `id` int(11) NOT NULL,
    category VARCHAR(32),
    message TEXT,
  PRIMARY KEY  (`id`)
);


CREATE TABLE IF NOT EXISTS Message
(
    id INTEGER,
    language VARCHAR(16),
    translation TEXT,
    PRIMARY KEY (id, language),
    CONSTRAINT FK_Message_SourceMessage FOREIGN KEY (id)
         REFERENCES SourceMessage (id) ON DELETE CASCADE ON UPDATE RESTRICT
);


