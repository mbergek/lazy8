CREATE TABLE IF NOT EXISTS Template (
  id int(11) NOT NULL AUTO_INCREMENT,
  companyId int(11) DEFAULT '0',
  `name` varchar(100) NOT NULL,
  `desc` varchar(255) DEFAULT NULL,
  sortOrder int(11) DEFAULT '0',
  allowAccountingView tinyint(1) DEFAULT NULL,
  allowFreeTextField tinyint(1) DEFAULT NULL,
  freeTextFieldDefault varchar(255) DEFAULT NULL,
  allowFilingTextField tinyint(1) DEFAULT NULL,
  filingTextFieldDefault varchar(255) DEFAULT NULL,
  forceDateToday tinyint(1) DEFAULT NULL,
  changedBy varchar(128) DEFAULT NULL,
  dateChanged datetime DEFAULT NULL,
  PRIMARY KEY (id),
  KEY FK_Template_Company (companyId)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=66 ;
CREATE TABLE IF NOT EXISTS TemplateRow (
  id int(11) NOT NULL AUTO_INCREMENT,
  templateId int(11) NOT NULL,
  sortOrder int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `desc` varchar(255) DEFAULT NULL,
  isDebit tinyint(1) DEFAULT NULL,
  defaultAccountId int(11) DEFAULT NULL,
  defaultValue double DEFAULT NULL,
  allowMinus tinyint(1) DEFAULT NULL,
  phpFieldCalc text,
  allowChangeValue tinyint(1) DEFAULT NULL,
  allowRepeatThisRow tinyint(1) DEFAULT NULL,
  allowCustomer tinyint(1) DEFAULT NULL,
  allowNotes tinyint(1) DEFAULT NULL,
  isFinalBalance tinyint(1) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY FK_TemplateRow_Account (defaultAccountId),
  KEY FK_TemplateRow_Template (templateId)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=133 ;
CREATE TABLE IF NOT EXISTS TemplateRowAccount (
  id int(11) NOT NULL AUTO_INCREMENT,
  templateRowId int(11) NOT NULL,
  accountId int(11) NOT NULL,
  PRIMARY KEY (id),
  KEY FK_TemplateRowAccount_Account (accountId),
  KEY FK_TemplateRowAccount_TemplateRow (templateRowId)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=230 ;

