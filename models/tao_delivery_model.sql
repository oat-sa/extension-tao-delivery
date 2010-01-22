-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 29, 2009 at 04:39 PM
-- Server version: 5.1.36
-- PHP Version: 5.2.11

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `taotrans_demo`
--

-- --------------------------------------------------------

--
-- Table structure for table `statements`
--

CREATE TABLE IF NOT EXISTS `statements` (
  `modelID` int(11) NOT NULL DEFAULT '0',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `predicate` varchar(255) NOT NULL DEFAULT '',
  `object` longtext,
  `l_language` varchar(255) DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author` varchar(255) DEFAULT NULL,
  `stread` varchar(255) NOT NULL DEFAULT 'yyy[]',
  `stedit` varchar(255) NOT NULL DEFAULT 'yy-[]',
  `stdelete` varchar(255) NOT NULL DEFAULT 'y--[Administrators]',
  `epoch` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_statements_modelID` (`modelID`),
  KEY `idx_statements_subject` (`subject`),
  KEY `idx_statements_predicate` (`predicate`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

--
-- Dumping data for table `statements`
--

INSERT INTO `statements` (`modelID`, `subject`, `predicate`, `object`, `l_language`, `id`, `author`, `stread`, `stedit`, `stdelete`, `epoch`) VALUES
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#Calendar', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#WidgetClass', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#Calendar', 'http://www.w3.org/2000/01/rdf-schema#label', 'Calendar', 'en', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#Calendar', 'http://www.w3.org/2000/01/rdf-schema#comment', 'Dynamic Calendar for easy date selecting', 'en', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#Calendar', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#rangeConstraint', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#rangeConstraint-Literal', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#Calendar', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#textHeight', '1', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#Calendar', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#textLength', '3', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),

(11, 'http://www.tao.lu/Ontologies/TAOGroup.rdf#Deliveries', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(11, 'http://www.tao.lu/Ontologies/TAOGroup.rdf#Deliveries', 'http://www.w3.org/2000/01/rdf-schema#label', 'Deliveries', 'EN', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-12-04 16:59:53'),
(11, 'http://www.tao.lu/Ontologies/TAOGroup.rdf#Deliveries', 'http://www.w3.org/2000/01/rdf-schema#comment', 'Deliveries', 'EN', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-12-04 16:59:53'),
(11, 'http://www.tao.lu/Ontologies/TAOGroup.rdf#Deliveries', 'http://www.w3.org/2000/01/rdf-schema#domain', 'http://www.tao.lu/Ontologies/TAOGroup.rdf#Group', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(11, 'http://www.tao.lu/Ontologies/TAOGroup.rdf#Deliveries', 'http://www.w3.org/2000/01/rdf-schema#range', 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Delivery', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(11, 'http://www.tao.lu/Ontologies/TAOGroup.rdf#Deliveries', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TreeView', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),

(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Delivery', 'http://www.w3.org/2000/01/rdf-schema#label', 'Delivery', 'EN', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Delivery', 'http://www.w3.org/2000/01/rdf-schema#comment', 'Delivery', 'EN', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Delivery', 'http://www.w3.org/2000/01/rdf-schema#subClassOf', 'http://www.tao.lu/Ontologies/TAO.rdf#TAOObject', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),

(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Maxexec', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Maxexec', 'http://www.w3.org/2000/01/rdf-schema#label', 'MaxExec', 'EN', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Maxexec', 'http://www.w3.org/2000/01/rdf-schema#comment', 'Maximum Times of Execution', 'EN', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Maxexec', 'http://www.w3.org/2000/01/rdf-schema#domain', 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Delivery', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Maxexec', 'http://www.w3.org/2000/01/rdf-schema#range', 'http://www.w3.org/2000/01/rdf-schema#Literal', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Maxexec', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TextBox', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Maxexec', 'http://www.tao.lu/Ontologies/generis.rdf#is_language_dependent', 'http://www.tao.lu/Ontologies/generis.rdf#False', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),

(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#PeriodStart', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#PeriodStart', 'http://www.w3.org/2000/01/rdf-schema#label', 'PeriodStart', 'EN', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#PeriodStart', 'http://www.w3.org/2000/01/rdf-schema#comment', 'The start date of the delivery', 'EN', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#PeriodStart', 'http://www.w3.org/2000/01/rdf-schema#domain', 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Delivery', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#PeriodStart', 'http://www.w3.org/2000/01/rdf-schema#range', 'http://www.w3.org/2000/01/rdf-schema#Literal', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#PeriodStart', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#Calendar', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#PeriodStart', 'http://www.tao.lu/Ontologies/generis.rdf#is_language_dependent', 'http://www.tao.lu/Ontologies/generis.rdf#False', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),

(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#PeriodEnd', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#PeriodEnd', 'http://www.w3.org/2000/01/rdf-schema#label', 'PeriodEnd', 'EN', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#PeriodEnd', 'http://www.w3.org/2000/01/rdf-schema#comment', 'The end date of the delivery', 'EN', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#PeriodEnd', 'http://www.w3.org/2000/01/rdf-schema#domain', 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Delivery', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#PeriodEnd', 'http://www.w3.org/2000/01/rdf-schema#range', 'http://www.w3.org/2000/01/rdf-schema#Literal', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#PeriodEnd', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#Calendar', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#PeriodEnd', 'http://www.tao.lu/Ontologies/generis.rdf#is_language_dependent', 'http://www.tao.lu/Ontologies/generis.rdf#False', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),

(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Compiled', 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy', ' ', 'EN', null, 'generis', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Compiled', 'http://www.w3.org/2000/01/rdf-schema#label', 'Compiled', 'EN', null, 'generis', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Compiled', 'http://www.w3.org/2000/01/rdf-schema#seeAlso', '', 'EN', null, 'generis', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Compiled', 'http://www.w3.org/2000/01/rdf-schema#comment', 'Compiled state', 'EN', null, 'generis', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Compiled', 'http://www.w3.org/2000/01/rdf-schema#domain', 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Delivery', '', null, 'generis', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Compiled', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#RadioBox', '', null, 'generis', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Compiled', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', '', null, 'generis', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Compiled', 'http://www.w3.org/2000/01/rdf-schema#range', 'http://www.tao.lu/Ontologies/generis.rdf#Boolean', '', null, 'generis', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Compiled', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#value', '', 'EN', null, 'generis', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', null),

(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ExcludedSubjects', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ExcludedSubjects', 'http://www.w3.org/2000/01/rdf-schema#label', 'ExcludedSubjects', 'EN', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ExcludedSubjects', 'http://www.w3.org/2000/01/rdf-schema#comment', 'ExcludedSubjects', 'EN', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ExcludedSubjects', 'http://www.w3.org/2000/01/rdf-schema#domain', 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Delivery', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ExcludedSubjects', 'http://www.w3.org/2000/01/rdf-schema#range', 'http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ExcludedSubjects', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TreeView', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),

(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ExcludedGroups', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ExcludedGroups', 'http://www.w3.org/2000/01/rdf-schema#label', 'ExcludedGroups', 'EN', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ExcludedGroups', 'http://www.w3.org/2000/01/rdf-schema#comment', 'ExcludedGroups', 'EN', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ExcludedGroups', 'http://www.w3.org/2000/01/rdf-schema#domain', 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Delivery', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ExcludedGroups', 'http://www.w3.org/2000/01/rdf-schema#range', 'http://www.tao.lu/Ontologies/TAOGroup.rdf#group', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ExcludedGroups', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TreeView', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),

(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#History', 'http://www.w3.org/2000/01/rdf-schema#label', 'History', 'EN', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#History', 'http://www.w3.org/2000/01/rdf-schema#comment', 'History', 'EN', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#History', 'http://www.w3.org/2000/01/rdf-schema#subClassOf', 'http://www.tao.lu/Ontologies/TAO.rdf#TAOObject', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),

(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#HistoryDelivery', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#HistoryDelivery', 'http://www.w3.org/2000/01/rdf-schema#label', 'HistoryDelivery', 'EN', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#HistoryDelivery', 'http://www.w3.org/2000/01/rdf-schema#comment', 'The related delivery', 'EN', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#HistoryDelivery', 'http://www.w3.org/2000/01/rdf-schema#domain', 'http://www.tao.lu/Ontologies/TAODelivery.rdf#History', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#HistoryDelivery', 'http://www.w3.org/2000/01/rdf-schema#range', 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Delivery', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),

(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#HistorySubject', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#HistorySubject', 'http://www.w3.org/2000/01/rdf-schema#label', 'HistorySubject', 'EN', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#HistorySubject', 'http://www.w3.org/2000/01/rdf-schema#comment', 'The related Subject', 'EN', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#HistorySubject', 'http://www.w3.org/2000/01/rdf-schema#domain', 'http://www.tao.lu/Ontologies/TAODelivery.rdf#History', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#HistorySubject', 'http://www.w3.org/2000/01/rdf-schema#range', 'http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),

(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#HistoryTimestamp', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#HistoryTimestamp', 'http://www.w3.org/2000/01/rdf-schema#label', 'HistoryTimestamp', 'EN', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#HistoryTimestamp', 'http://www.w3.org/2000/01/rdf-schema#comment', 'Timestamp of the delivery history', 'EN', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#HistoryTimestamp', 'http://www.w3.org/2000/01/rdf-schema#domain', 'http://www.tao.lu/Ontologies/TAODelivery.rdf#History', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#HistoryTimestamp', 'http://www.w3.org/2000/01/rdf-schema#range', 'http://www.w3.org/2000/01/rdf-schema#Literal', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),

(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Campaign', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Campaign', 'http://www.w3.org/2000/01/rdf-schema#label', 'Campaign', 'EN', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Campaign', 'http://www.w3.org/2000/01/rdf-schema#comment', 'Campaign', 'EN', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Campaign', 'http://www.w3.org/2000/01/rdf-schema#domain', 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Delivery', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Campaign', 'http://www.w3.org/2000/01/rdf-schema#range', 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryCampaign', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Campaign', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TreeView', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),

(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryCampaign', 'http://www.w3.org/2000/01/rdf-schema#label', 'DeliveryCampaign', 'EN', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryCampaign', 'http://www.w3.org/2000/01/rdf-schema#comment', 'DeliveryCampaign', 'EN', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryCampaign', 'http://www.w3.org/2000/01/rdf-schema#subClassOf', 'http://www.tao.lu/Ontologies/TAO.rdf#TAOObject', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),

(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#CampaignStart', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#CampaignStart', 'http://www.w3.org/2000/01/rdf-schema#label', 'CampaignStart', 'EN', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#CampaignStart', 'http://www.w3.org/2000/01/rdf-schema#comment', 'The start date of the campaign', 'EN', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#CampaignStart', 'http://www.w3.org/2000/01/rdf-schema#domain', 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryCampaign', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#CampaignStart', 'http://www.w3.org/2000/01/rdf-schema#range', 'http://www.w3.org/2000/01/rdf-schema#Literal', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#CampaignStart', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#Calendar', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),

(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#CampaignEnd', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#CampaignEnd', 'http://www.w3.org/2000/01/rdf-schema#label', 'CampaignEnd', 'EN', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#CampaignEnd', 'http://www.w3.org/2000/01/rdf-schema#comment', 'The end date of the campaign', 'EN', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#CampaignEnd', 'http://www.w3.org/2000/01/rdf-schema#domain', 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryCampaign', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#CampaignEnd', 'http://www.w3.org/2000/01/rdf-schema#range', 'http://www.w3.org/2000/01/rdf-schema#Literal', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#CampaignEnd', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#Calendar', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),

(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ResultServer', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ResultServer', 'http://www.w3.org/2000/01/rdf-schema#label', 'ResultServer', 'EN', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ResultServer', 'http://www.w3.org/2000/01/rdf-schema#comment', 'ResultServer', 'EN', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ResultServer', 'http://www.w3.org/2000/01/rdf-schema#domain', 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Delivery', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ResultServer', 'http://www.w3.org/2000/01/rdf-schema#range', 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryResultServer', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ResultServer', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#ComboBox', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),

(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryResultServer', 'http://www.w3.org/2000/01/rdf-schema#label', 'ResultServer', 'EN', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryResultServer', 'http://www.w3.org/2000/01/rdf-schema#comment', 'the ResultServer for the Delivery', 'EN', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryResultServer', 'http://www.w3.org/2000/01/rdf-schema#subClassOf', 'http://www.tao.lu/Ontologies/TAO.rdf#TAOObject', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),

(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ResultServerUrl', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ResultServerUrl', 'http://www.w3.org/2000/01/rdf-schema#label', 'ResultServerUrl', 'EN', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ResultServerUrl', 'http://www.w3.org/2000/01/rdf-schema#comment', 'The Url to the WSDL of the ResultServer', 'EN', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ResultServerUrl', 'http://www.w3.org/2000/01/rdf-schema#domain', 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryResultServer', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ResultServerUrl', 'http://www.w3.org/2000/01/rdf-schema#range', 'http://www.w3.org/2000/01/rdf-schema#Literal', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ResultServerUrl', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TextBox', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),

(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#SubjectCache', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#SubjectCache', 'http://www.w3.org/2000/01/rdf-schema#label', 'SubjectCache', 'EN', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#SubjectCache', 'http://www.w3.org/2000/01/rdf-schema#comment', 'The reference to the cached subjects ontology', 'EN', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#SubjectCache', 'http://www.w3.org/2000/01/rdf-schema#domain', 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Delivery', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#SubjectCache', 'http://www.w3.org/2000/01/rdf-schema#range', 'http://www.w3.org/2000/01/rdf-schema#Literal', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),

(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryContent', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryContent', 'http://www.w3.org/2000/01/rdf-schema#label', 'DeliveryContent', 'EN', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryContent', 'http://www.w3.org/2000/01/rdf-schema#comment', 'DeliveryContent', 'EN', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryContent', 'http://www.w3.org/2000/01/rdf-schema#domain', 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Delivery', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryContent', 'http://www.w3.org/2000/01/rdf-schema#range', 'http://www.w3.org/2000/01/rdf-schema#Literal', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryContent', 'http://www.w3.org/2000/01/rdf-schema#range', 'http://www.w3.org/2000/01/rdf-schema#Literal', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryContent', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TextArea', '', null, 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', null);
