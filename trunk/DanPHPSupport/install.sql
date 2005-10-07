-- phpMyAdmin SQL Dump
-- version 2.6.0-pl3
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Sep 28, 2005 at 02:18 PM
-- Server version: 4.0.24
-- PHP Version: 4.3.10-16
-- 
-- Database: `DanPHPSupport`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `kb_articles`
-- 

CREATE TABLE `kb_articles` (
  `ID` mediumint(8) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `body` mediumtext NOT NULL,
  `views` int(10) unsigned NOT NULL default '0',
  `categoryID` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`ID`),
  FULLTEXT KEY `searchIndex` (`title`,`body`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `kb_articles`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `kb_categories`
-- 

CREATE TABLE `kb_categories` (
  `ID` smallint(5) unsigned NOT NULL auto_increment,
  `name` varchar(50) NOT NULL default '',
  `parentID` smallint(5) unsigned NOT NULL default '0',
  `count` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`ID`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `kb_categories`
-- 

INSERT INTO `kb_categories` VALUES (1, 'Default KB Category', 0, 1);
-- --------------------------------------------------------

-- 
-- Table structure for table `settings`
-- 

CREATE TABLE `settings` (
  `field` tinytext NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY  (`field`(30))
) TYPE=MyISAM;

-- 
-- Dumping data for table `settings`
-- 

INSERT INTO `settings` VALUES ('adminEmail', 'admin@localhost');
INSERT INTO `settings` VALUES ('emailNewTicket', '1');
INSERT INTO `settings` VALUES ('fromEmail', 'support-noreply@localhost');

-- --------------------------------------------------------

-- 
-- Table structure for table `ticket_categories`
-- 

CREATE TABLE `ticket_categories` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `name` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `ticket_categories`
-- 

INSERT INTO `ticket_categories` VALUES (1, 'Test Ticket Category');

-- --------------------------------------------------------

-- 
-- Table structure for table `ticket_messages`
-- 

CREATE TABLE `ticket_messages` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `message` mediumtext NOT NULL,
  `ticketID` int(10) unsigned NOT NULL default '0',
  `userID` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`ID`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `ticket_messages`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `ticket_severities`
-- 

CREATE TABLE `ticket_severities` (
  `ID` tinyint(3) unsigned NOT NULL auto_increment,
  `name` varchar(25) NOT NULL default '',
  `colour` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`ID`)
) TYPE=MyISAM AUTO_INCREMENT=7 ;

-- 
-- Dumping data for table `ticket_severities`
-- 

INSERT INTO `ticket_severities` VALUES (2, 'Low', 'green');
INSERT INTO `ticket_severities` VALUES (3, 'Medium', 'yellow');
INSERT INTO `ticket_severities` VALUES (4, 'High', 'red');
INSERT INTO `ticket_severities` VALUES (5, 'Critical', 'orange');
INSERT INTO `ticket_severities` VALUES (6, 'Urgent', 'blue');

-- --------------------------------------------------------

-- 
-- Table structure for table `tickets`
-- 

CREATE TABLE `tickets` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `userID` int(10) unsigned NOT NULL default '0',
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `category` smallint(5) unsigned NOT NULL default '0',
  `subject` varchar(100) NOT NULL default '',
  `severity` tinyint(4) NOT NULL default '0',
  `status` tinyint(1) NOT NULL default '0',
  `staffID` int(10) unsigned NOT NULL default '0',
  `lastPost` int(10) unsigned NOT NULL default '0',
  `replyCount` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`ID`)
) TYPE=MyISAM AUTO_INCREMENT=6 ;

-- 
-- Dumping data for table `tickets`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `users`
-- 

CREATE TABLE `users` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `firstName` varchar(30) NOT NULL default '',
  `lastName` varchar(30) NOT NULL default '',
  `email` varchar(100) NOT NULL default '',
  `username` varchar(50) NOT NULL default '',
  `password` varchar(32) NOT NULL default '',
  `admin` tinyint(1) unsigned NOT NULL default '0',
  `lastLogin` datetime NOT NULL default '0000-00-00 00:00:00',
  `lastLoginOld` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`ID`)
) TYPE=MyISAM AUTO_INCREMENT=4 ;

-- 
-- Dumping data for table `users`
-- 

INSERT INTO `users` VALUES (1, 'Default', 'Administrator', 'admin@localhost', 'admin', '21232f297a57a5a743894a0e4a801fc3', 1, '2005-09-28 13:29:16', '2005-09-28 13:29:08');
        