SET FOREIGN_KEY_CHECKS=0;

DROP DATABASE IF EXISTS `orm_test_0`;

CREATE DATABASE `orm_test_0`
    CHARACTER SET 'utf8'
    COLLATE 'utf8_general_ci';

USE `orm_test_0`;

DROP TABLE IF EXISTS `_test_Categories`;

CREATE TABLE `_test_Categories` (
  `categoryID` INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `parentCategoryID` INTEGER(10) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`categoryID`),
  KEY `IDX_Categories_1` (`parentCategoryID`),
  CONSTRAINT `Categories_Categories` FOREIGN KEY (`parentCategoryID`) REFERENCES `_test_Categories` (`categoryID`)
)ENGINE=InnoDB
AUTO_INCREMENT=11 CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

DROP TABLE IF EXISTS `_test_Customers`;

CREATE TABLE `_test_Customers` (
  `customerID` INTEGER(10) UNSIGNED NOT NULL,
  `phone` VARCHAR(40) COLLATE utf8_general_ci DEFAULT NULL,
  `fax` VARCHAR(40) COLLATE utf8_general_ci DEFAULT NULL,
  `country` VARCHAR(40) COLLATE utf8_general_ci DEFAULT NULL,
  `parentCustomerID` INTEGER(11) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`customerID`),
  KEY `parentCustomerID` (`parentCustomerID`),
  CONSTRAINT `_test_Customers_fk` FOREIGN KEY (`parentCustomerID`) REFERENCES `_test_Customers` (`customerID`),
  CONSTRAINT `Users_Customers` FOREIGN KEY (`customerID`) REFERENCES `_test_Users` (`userID`)
)ENGINE=InnoDB
CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

DROP TABLE IF EXISTS `_test_DataTypes`;

CREATE TABLE `_test_DataTypes` (
  `ID` INTEGER(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tpTINYINT` TINYINT(4) DEFAULT NULL,
  `tpSMALLINT` SMALLINT(6) DEFAULT NULL,
  `tpMEDIUMINT` MEDIUMINT(9) DEFAULT NULL,
  `tpBIGINT` BIGINT(20) DEFAULT NULL,
  `tpFLOAT` FLOAT(9,3) DEFAULT NULL,
  `tpDOUBLE` DOUBLE(15,3) DEFAULT NULL,
  `tpDECIMAL` DECIMAL(11,0) DEFAULT NULL,
  `tpDATE` DATE DEFAULT NULL,
  `tpDATETIME` DATETIME DEFAULT NULL,
  `tpTIMESTAMP` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
  `tpTIME` TIME DEFAULT NULL,
  `tpYEAR` YEAR(4) DEFAULT NULL,
  `tpCHAR` CHAR(20) COLLATE utf8_general_ci DEFAULT NULL,
  `tpVARCHAR` VARCHAR(20) COLLATE utf8_general_ci DEFAULT NULL,
  `tpTINYBLOB` TINYBLOB,
  `tpBLOB` BLOB,
  `tpMEDIUMBLOB` MEDIUMBLOB,
  `tpLONGBLOB` LONGBLOB,
  `tpTINYTEXT` TINYTEXT,
  `tpTEXT` TEXT COLLATE utf8_general_ci,
  `tpMEDIUMTEXT` MEDIUMTEXT,
  `tpLONGTEXT` LONGTEXT,
  `tpENUM` ENUM('one','two','three') DEFAULT NULL,
  `tpSET` SET('A','B','C','D','0') DEFAULT NULL,
  `tpBINARY` BINARY(1) DEFAULT NULL,
  `tpVARBINARY` VARBINARY(1) DEFAULT NULL,
  `tpBIT` BIT(1) DEFAULT NULL,
  `tpBOOLEAN` TINYINT(1) DEFAULT NULL,
  PRIMARY KEY (`ID`)
)ENGINE=InnoDB
AUTO_INCREMENT=1 CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

DROP TABLE IF EXISTS `_test_LookupOrderStatuses`;

CREATE TABLE `_test_LookupOrderStatuses` (
  `statusID` CHAR(3) COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `status` VARCHAR(40) COLLATE utf8_general_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`statusID`)
)ENGINE=InnoDB
CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

DROP TABLE IF EXISTS `_test_LookupProductAttributes`;

CREATE TABLE `_test_LookupProductAttributes` (
  `attributeID` INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `categoryID` INTEGER(10) UNSIGNED NOT NULL,
  `attribute` VARCHAR(40) COLLATE utf8_general_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`attributeID`),
  KEY `IDX_LookupProductAttributes_1` (`categoryID`),
  CONSTRAINT `Categories_LookupProductAttributes` FOREIGN KEY (`categoryID`) REFERENCES `_test_Categories` (`categoryID`)
)ENGINE=InnoDB
AUTO_INCREMENT=21 CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

DROP TABLE IF EXISTS `_test_LookupUserTypes`;

CREATE TABLE `_test_LookupUserTypes` (
  `typeID` CHAR(1) COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `typeName` VARCHAR(40) COLLATE utf8_general_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`typeID`)
)ENGINE=InnoDB
CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

DROP TABLE IF EXISTS `_test_Managers`;

CREATE TABLE `_test_Managers` (
  `managerID` INTEGER(10) UNSIGNED NOT NULL,
  `contractSigned` DATETIME DEFAULT NULL,
  `salary` FLOAT DEFAULT NULL,
  `fired` DATETIME DEFAULT NULL,
  PRIMARY KEY (`managerID`),
  CONSTRAINT `Users_Managers` FOREIGN KEY (`managerID`) REFERENCES `_test_users` (`userID`)
)ENGINE=InnoDB
CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

DROP TABLE IF EXISTS `_test_OrderProductOptions`;

CREATE TABLE `_test_OrderProductOptions` (
  `orderProductOptionID` INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `orderID` INTEGER(10) UNSIGNED NOT NULL,
  `productID` INTEGER(10) UNSIGNED NOT NULL,
  `supplierID` INTEGER(10) UNSIGNED NOT NULL,
  `productAttributeID` INTEGER(10) UNSIGNED NOT NULL,
  `optionID` INTEGER(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`orderProductOptionID`),
  KEY `IDX_OrderProductOptions_1` (`productAttributeID`),
  KEY `IDX_OrderProductOptions_2` (`optionID`),
  KEY `IDX_OrderProductOptions_3` (`orderID`, `productAttributeID`, `optionID`),
  KEY `OrderProducts_OrderProductOptions` (`orderID`, `productID`, `supplierID`),
  KEY `Products_OrderProductOptions` (`productID`),
  KEY `Suppliers_OrderProductOptions` (`supplierID`),
  CONSTRAINT `Suppliers_OrderProductOptions` FOREIGN KEY (`supplierID`) REFERENCES `_test_suppliers` (`supplierID`),
  CONSTRAINT `OrderProducts_OrderProductOptions` FOREIGN KEY (`orderID`, `productID`, `supplierID`) REFERENCES `_test_orderproducts` (`orderID`, `productID`, `supplierID`),
  CONSTRAINT `ProductAttributeOptions_OrderProductOptions` FOREIGN KEY (`optionID`) REFERENCES `_test_productattributeoptions` (`optionID`),
  CONSTRAINT `ProductAttributes_OrderProductOptions` FOREIGN KEY (`productAttributeID`) REFERENCES `_test_productattributes` (`productAttributeID`),
  CONSTRAINT `Products_OrderProductOptions` FOREIGN KEY (`productID`) REFERENCES `_test_products` (`productID`)
)ENGINE=InnoDB
AUTO_INCREMENT=1 CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

DROP TABLE IF EXISTS `_test_OrderProducts`;

CREATE TABLE `_test_OrderProducts` (
  `orderID` INTEGER(10) UNSIGNED NOT NULL,
  `productID` INTEGER(10) UNSIGNED NOT NULL,
  `supplierID` INTEGER(10) UNSIGNED NOT NULL,
  `quantity` INTEGER(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`orderID`, `productID`, `supplierID`),
  KEY `IDX_OrderProducts_1` (`orderID`),
  KEY `Stock_OrderProducts` (`productID`, `supplierID`),
  KEY `Suppliers_OrderProducts` (`supplierID`),
  CONSTRAINT `Suppliers_OrderProducts` FOREIGN KEY (`supplierID`) REFERENCES `_test_suppliers` (`supplierID`),
  CONSTRAINT `Orders_OrderProducts` FOREIGN KEY (`orderID`) REFERENCES `_test_orders` (`orderID`),
  CONSTRAINT `Products_OrderProducts` FOREIGN KEY (`productID`) REFERENCES `_test_products` (`productID`),
  CONSTRAINT `Stock_OrderProducts` FOREIGN KEY (`productID`, `supplierID`) REFERENCES `_test_stock` (`productID`, `supplierID`)
)ENGINE=InnoDB
CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

DROP TABLE IF EXISTS `_test_Orders`;

CREATE TABLE `_test_Orders` (
  `orderID` INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `customerID` INTEGER(10) UNSIGNED NOT NULL,
  `created` DATETIME DEFAULT NULL,
  `statusID` CHAR(3) COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `managerID` INTEGER(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`orderID`),
  KEY `IDX_Orders_1` (`customerID`),
  KEY `IDX_Orders_2` (`statusID`),
  KEY `Managers_Orders` (`managerID`),
  CONSTRAINT `Managers_Orders` FOREIGN KEY (`managerID`) REFERENCES `_test_Managers` (`managerID`),
  CONSTRAINT `Customers_Orders` FOREIGN KEY (`customerID`) REFERENCES `_test_Customers` (`customerID`),
  CONSTRAINT `LookupOrderStatuses_Orders` FOREIGN KEY (`statusID`) REFERENCES `_test_LookupOrderStatuses` (`statusID`)
)ENGINE=InnoDB
AUTO_INCREMENT=1 CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

DROP TABLE IF EXISTS `_test_ProductAttributeOptions`;

CREATE TABLE `_test_ProductAttributeOptions` (
  `optionID` INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `productAttributeID` INTEGER(10) UNSIGNED NOT NULL,
  `value` VARCHAR(40) COLLATE utf8_general_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`optionID`),
  KEY `IDX_ProductAttributeOptions_1` (`productAttributeID`),
  CONSTRAINT `ProductAttributes_ProductAttributeOptions` FOREIGN KEY (`productAttributeID`) REFERENCES `_test_productattributes` (`productAttributeID`)
)ENGINE=InnoDB
AUTO_INCREMENT=1 CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

DROP TABLE IF EXISTS `_test_ProductAttributes`;

CREATE TABLE `_test_ProductAttributes` (
  `productAttributeID` INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `productID` INTEGER(10) UNSIGNED DEFAULT NULL,
  `attributeID` INTEGER(10) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`productAttributeID`),
  KEY `IDX_ProductAttributes_1` (`productID`),
  KEY `IDX_ProductAttributes_2` (`attributeID`),
  CONSTRAINT `LookupProductAttributes_ProductAttributes` FOREIGN KEY (`attributeID`) REFERENCES `_test_LookupProductAttributes` (`attributeID`),
  CONSTRAINT `Products_ProductAttributes` FOREIGN KEY (`productID`) REFERENCES `_test_products` (`productID`)
)ENGINE=InnoDB
AUTO_INCREMENT=1 CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

DROP TABLE IF EXISTS `_test_Products`;

CREATE TABLE `_test_Products` (
  `productID` INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(40) COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `description` VARCHAR(255) COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `categoryID` INTEGER(10) UNSIGNED DEFAULT NULL,
  `managerID` INTEGER(10) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`productID`),
  KEY `IDX_Products_1` (`categoryID`),
  KEY `IDX_Products_2` (`managerID`),
  CONSTRAINT `Managers_Products` FOREIGN KEY (`managerID`) REFERENCES `_test_Managers` (`managerID`),
  CONSTRAINT `Categories_Products` FOREIGN KEY (`categoryID`) REFERENCES `_test_Categories` (`categoryID`)
)ENGINE=InnoDB
AUTO_INCREMENT=1 CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

DROP TABLE IF EXISTS `_test_Stock`;

CREATE TABLE `_test_Stock` (
  `productID` INTEGER(10) UNSIGNED NOT NULL,
  `supplierID` INTEGER(10) UNSIGNED NOT NULL,
  `quantity` INTEGER(10) UNSIGNED NOT NULL DEFAULT '0',
  `price` FLOAT UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`productID`, `supplierID`),
  KEY `IDX_Stock_1` (`productID`),
  KEY `IDX_Stock_2` (`supplierID`),
  CONSTRAINT `Suppliers_Stock` FOREIGN KEY (`supplierID`) REFERENCES `_test_suppliers` (`supplierID`),
  CONSTRAINT `Products_Stock` FOREIGN KEY (`productID`) REFERENCES `_test_Products` (`productID`)
)ENGINE=InnoDB
CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

DROP TABLE IF EXISTS `_test_Suppliers`;

CREATE TABLE `_test_Suppliers` (
  `supplierID` INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) COLLATE utf8_general_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`supplierID`)
)ENGINE=InnoDB
AUTO_INCREMENT=5 CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

DROP TABLE IF EXISTS `_test_Users`;

CREATE TABLE `_test_Users` (
  `userID` INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `typeID` CHAR(1) COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `firstName` VARCHAR(40) COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `lastName` VARCHAR(40) COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `email` VARCHAR(255) COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `password` VARCHAR(20) COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `created` DATETIME DEFAULT NULL,
  `updated` DATETIME DEFAULT NULL,
  `deleted` DATETIME DEFAULT NULL,
  `lastActivity` DATETIME DEFAULT NULL,
  PRIMARY KEY (`userID`),
  KEY `LookupUserTypes_Users` (`typeID`),
  CONSTRAINT `LookupUserTypes_Users` FOREIGN KEY (`typeID`) REFERENCES `_test_LookupUserTypes` (`typeID`)
)ENGINE=InnoDB
AUTO_INCREMENT=1 CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

INSERT INTO `_test_Categories` (`categoryID`, `name`, `parentCategoryID`) VALUES 
  (1,'Clothes',NULL),
  (2,'Cars',NULL),
  (3,'T-Shirts',1),
  (4,'Jeans',1),
  (5,'Underwear',1),
  (6,'Footwear',1),
  (7,'SUV',2),
  (8,'Sport Cars',2),
  (9,'Luxury',2),
  (10,'Mini-vans',2);
COMMIT;

INSERT INTO `_test_LookupOrderStatuses` (`statusID`, `status`) VALUES 
  ('inp','In Processing'),
  ('new','New'),
  ('shp','Shipped'),
  ('x','Cancelled');
COMMIT;

INSERT INTO `_test_LookupProductAttributes` (`attributeID`, `categoryID`, `attribute`) VALUES 
  (1,3,'Color'),
  (2,3,'Size'),
  (3,4,'Size Width'),
  (4,4,'Size Height'),
  (5,4,'Style'),
  (6,5,'Color'),
  (7,5,'Size'),
  (8,6,'Size'),
  (9,7,'Color'),
  (10,7,'Engine'),
  (11,7,'Navigation'),
  (12,8,'Color'),
  (13,8,'Engine'),
  (14,8,'Navigation'),
  (15,9,'Color'),
  (16,9,'Engine'),
  (17,9,'Navigation'),
  (18,10,'Color'),
  (19,10,'Engine'),
  (20,10,'Navigation');
COMMIT;

INSERT INTO `_test_LookupUserTypes` (`typeID`, `typeName`) VALUES 
  ('C','Customer'),
  ('M','Manager');
COMMIT;

INSERT INTO `_test_Suppliers` (`supplierID`, `name`) VALUES 
  (1,'Goods Market'),
  (2,'Amazon'),
  (3,'Public Market'),
  (4,'Albertsons');
COMMIT;