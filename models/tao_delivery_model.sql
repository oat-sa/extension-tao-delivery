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


-- Dumping data for table `statements`
--

INSERT INTO `statements` (`modelID`, `subject`, `predicate`, `object`, `l_language`,  `author`, `stread`, `stedit`, `stdelete`) VALUES
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#Calendar', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#WidgetClass', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#Calendar', 'http://www.w3.org/2000/01/rdf-schema#label', 'Calendar', 'en', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#Calendar', 'http://www.w3.org/2000/01/rdf-schema#comment', 'Dynamic Calendar for easy date selecting', 'en', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#Calendar', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#rangeConstraint', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#rangeConstraint-Literal', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#Calendar', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#textHeight', '1', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(3, 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#Calendar', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#textLength', '3', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),

(11, 'http://www.tao.lu/Ontologies/TAOGroup.rdf#Deliveries', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(11, 'http://www.tao.lu/Ontologies/TAOGroup.rdf#Deliveries', 'http://www.w3.org/2000/01/rdf-schema#label', 'Deliveries', 'EN', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-12-04 16:59:53'),
(11, 'http://www.tao.lu/Ontologies/TAOGroup.rdf#Deliveries', 'http://www.w3.org/2000/01/rdf-schema#comment', 'Deliveries', 'EN', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]', '2009-12-04 16:59:53'),
(11, 'http://www.tao.lu/Ontologies/TAOGroup.rdf#Deliveries', 'http://www.w3.org/2000/01/rdf-schema#domain', 'http://www.tao.lu/Ontologies/TAOGroup.rdf#Group', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(11, 'http://www.tao.lu/Ontologies/TAOGroup.rdf#Deliveries', 'http://www.w3.org/2000/01/rdf-schema#range', 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Delivery', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(11, 'http://www.tao.lu/Ontologies/TAOGroup.rdf#Deliveries', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TreeView', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),

(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Delivery', 'http://www.w3.org/2000/01/rdf-schema#label', 'Delivery', 'EN', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Delivery', 'http://www.w3.org/2000/01/rdf-schema#comment', 'Delivery', 'EN', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Delivery', 'http://www.w3.org/2000/01/rdf-schema#subClassOf', 'http://www.tao.lu/Ontologies/TAO.rdf#TAOObject', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),

(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Maxexec', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Maxexec', 'http://www.w3.org/2000/01/rdf-schema#label', 'MaxExec', 'EN', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Maxexec', 'http://www.w3.org/2000/01/rdf-schema#comment', 'Maximum Times of Execution per subject', 'EN', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Maxexec', 'http://www.w3.org/2000/01/rdf-schema#domain', 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Delivery', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Maxexec', 'http://www.w3.org/2000/01/rdf-schema#range', 'http://www.w3.org/2000/01/rdf-schema#Literal', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Maxexec', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TextBox', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Maxexec', 'http://www.tao.lu/Ontologies/generis.rdf#is_language_dependent', 'http://www.tao.lu/Ontologies/generis.rdf#False', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),

(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#PeriodStart', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#PeriodStart', 'http://www.w3.org/2000/01/rdf-schema#label', 'PeriodStart', 'EN', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#PeriodStart', 'http://www.w3.org/2000/01/rdf-schema#comment', 'The start date of the delivery', 'EN', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#PeriodStart', 'http://www.w3.org/2000/01/rdf-schema#domain', 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Delivery', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#PeriodStart', 'http://www.w3.org/2000/01/rdf-schema#range', 'http://www.w3.org/2000/01/rdf-schema#Literal', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#PeriodStart', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#Calendar', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#PeriodStart', 'http://www.tao.lu/Ontologies/generis.rdf#is_language_dependent', 'http://www.tao.lu/Ontologies/generis.rdf#False', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),

(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#PeriodEnd', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#PeriodEnd', 'http://www.w3.org/2000/01/rdf-schema#label', 'PeriodEnd', 'EN', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#PeriodEnd', 'http://www.w3.org/2000/01/rdf-schema#comment', 'The end date of the delivery', 'EN', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#PeriodEnd', 'http://www.w3.org/2000/01/rdf-schema#domain', 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Delivery', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#PeriodEnd', 'http://www.w3.org/2000/01/rdf-schema#range', 'http://www.w3.org/2000/01/rdf-schema#Literal', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#PeriodEnd', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#Calendar', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#PeriodEnd', 'http://www.tao.lu/Ontologies/generis.rdf#is_language_dependent', 'http://www.tao.lu/Ontologies/generis.rdf#False', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),

(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Compiled', 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy', ' ', 'EN', 'generis', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Compiled', 'http://www.w3.org/2000/01/rdf-schema#label', 'Compiled', 'EN', 'generis', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Compiled', 'http://www.w3.org/2000/01/rdf-schema#seeAlso', '', 'EN', 'generis', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Compiled', 'http://www.w3.org/2000/01/rdf-schema#comment', 'Compiled state', 'EN', 'generis', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Compiled', 'http://www.w3.org/2000/01/rdf-schema#domain', 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Delivery', '', 'generis', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Compiled', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#RadioBox', '', 'generis', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Compiled', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', '', 'generis', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Compiled', 'http://www.w3.org/2000/01/rdf-schema#range', 'http://www.tao.lu/Ontologies/generis.rdf#Boolean', '', 'generis', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Compiled', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#value', '', 'EN', 'generis', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),

(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ExcludedSubjects', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ExcludedSubjects', 'http://www.w3.org/2000/01/rdf-schema#label', 'ExcludedSubjects', 'EN', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ExcludedSubjects', 'http://www.w3.org/2000/01/rdf-schema#comment', 'ExcludedSubjects', 'EN', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ExcludedSubjects', 'http://www.w3.org/2000/01/rdf-schema#domain', 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Delivery', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ExcludedSubjects', 'http://www.w3.org/2000/01/rdf-schema#range', 'http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ExcludedSubjects', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TreeView', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),

(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ExcludedGroups', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ExcludedGroups', 'http://www.w3.org/2000/01/rdf-schema#label', 'ExcludedGroups', 'EN', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ExcludedGroups', 'http://www.w3.org/2000/01/rdf-schema#comment', 'ExcludedGroups', 'EN', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ExcludedGroups', 'http://www.w3.org/2000/01/rdf-schema#domain', 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Delivery', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ExcludedGroups', 'http://www.w3.org/2000/01/rdf-schema#range', 'http://www.tao.lu/Ontologies/TAOGroup.rdf#group', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ExcludedGroups', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TreeView', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),

(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#History', 'http://www.w3.org/2000/01/rdf-schema#label', 'History', 'EN', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#History', 'http://www.w3.org/2000/01/rdf-schema#comment', 'History', 'EN', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#History', 'http://www.w3.org/2000/01/rdf-schema#subClassOf', 'http://www.tao.lu/Ontologies/TAO.rdf#TAOObject', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),

(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#HistoryDelivery', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#HistoryDelivery', 'http://www.w3.org/2000/01/rdf-schema#label', 'HistoryDelivery', 'EN', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#HistoryDelivery', 'http://www.w3.org/2000/01/rdf-schema#comment', 'The related delivery', 'EN', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#HistoryDelivery', 'http://www.w3.org/2000/01/rdf-schema#domain', 'http://www.tao.lu/Ontologies/TAODelivery.rdf#History', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#HistoryDelivery', 'http://www.w3.org/2000/01/rdf-schema#range', 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Delivery', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),

(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#HistorySubject', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#HistorySubject', 'http://www.w3.org/2000/01/rdf-schema#label', 'HistorySubject', 'EN', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#HistorySubject', 'http://www.w3.org/2000/01/rdf-schema#comment', 'The related Subject', 'EN', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#HistorySubject', 'http://www.w3.org/2000/01/rdf-schema#domain', 'http://www.tao.lu/Ontologies/TAODelivery.rdf#History', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#HistorySubject', 'http://www.w3.org/2000/01/rdf-schema#range', 'http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),

(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#HistoryTimestamp', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#HistoryTimestamp', 'http://www.w3.org/2000/01/rdf-schema#label', 'HistoryTimestamp', 'EN', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#HistoryTimestamp', 'http://www.w3.org/2000/01/rdf-schema#comment', 'Timestamp of the delivery history', 'EN', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#HistoryTimestamp', 'http://www.w3.org/2000/01/rdf-schema#domain', 'http://www.tao.lu/Ontologies/TAODelivery.rdf#History', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#HistoryTimestamp', 'http://www.w3.org/2000/01/rdf-schema#range', 'http://www.w3.org/2000/01/rdf-schema#Literal', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),

(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Campaign', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Campaign', 'http://www.w3.org/2000/01/rdf-schema#label', 'Campaign', 'EN', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Campaign', 'http://www.w3.org/2000/01/rdf-schema#comment', 'Campaign', 'EN', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Campaign', 'http://www.w3.org/2000/01/rdf-schema#domain', 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Delivery', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Campaign', 'http://www.w3.org/2000/01/rdf-schema#range', 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryCampaign', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Campaign', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TreeView', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),

(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryCampaign', 'http://www.w3.org/2000/01/rdf-schema#label', 'DeliveryCampaign', 'EN', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryCampaign', 'http://www.w3.org/2000/01/rdf-schema#comment', 'DeliveryCampaign', 'EN', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryCampaign', 'http://www.w3.org/2000/01/rdf-schema#subClassOf', 'http://www.tao.lu/Ontologies/TAO.rdf#TAOObject', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),

(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#CampaignStart', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#CampaignStart', 'http://www.w3.org/2000/01/rdf-schema#label', 'CampaignStart', 'EN', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#CampaignStart', 'http://www.w3.org/2000/01/rdf-schema#comment', 'The start date of the campaign', 'EN', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#CampaignStart', 'http://www.w3.org/2000/01/rdf-schema#domain', 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryCampaign', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#CampaignStart', 'http://www.w3.org/2000/01/rdf-schema#range', 'http://www.w3.org/2000/01/rdf-schema#Literal', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#CampaignStart', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#Calendar', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),

(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#CampaignEnd', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#CampaignEnd', 'http://www.w3.org/2000/01/rdf-schema#label', 'CampaignEnd', 'EN', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#CampaignEnd', 'http://www.w3.org/2000/01/rdf-schema#comment', 'The end date of the campaign', 'EN', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#CampaignEnd', 'http://www.w3.org/2000/01/rdf-schema#domain', 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryCampaign', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#CampaignEnd', 'http://www.w3.org/2000/01/rdf-schema#range', 'http://www.w3.org/2000/01/rdf-schema#Literal', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#CampaignEnd', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#Calendar', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),

(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ResultServer', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ResultServer', 'http://www.w3.org/2000/01/rdf-schema#label', 'ResultServer', 'EN', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ResultServer', 'http://www.w3.org/2000/01/rdf-schema#comment', 'ResultServer', 'EN', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ResultServer', 'http://www.w3.org/2000/01/rdf-schema#domain', 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Delivery', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ResultServer', 'http://www.w3.org/2000/01/rdf-schema#range', 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryResultServer', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ResultServer', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#ComboBox', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),

(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryResultServer', 'http://www.w3.org/2000/01/rdf-schema#label', 'ResultServer', 'EN', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryResultServer', 'http://www.w3.org/2000/01/rdf-schema#comment', 'the ResultServer for the Delivery', 'EN', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryResultServer', 'http://www.w3.org/2000/01/rdf-schema#subClassOf', 'http://www.tao.lu/Ontologies/TAO.rdf#TAOObject', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),

(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ResultServerUrl', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ResultServerUrl', 'http://www.w3.org/2000/01/rdf-schema#label', 'ResultServerUrl', 'EN', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ResultServerUrl', 'http://www.w3.org/2000/01/rdf-schema#comment', 'The Url to the WSDL of the ResultServer', 'EN', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ResultServerUrl', 'http://www.w3.org/2000/01/rdf-schema#domain', 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryResultServer', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ResultServerUrl', 'http://www.w3.org/2000/01/rdf-schema#range', 'http://www.w3.org/2000/01/rdf-schema#Literal', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#ResultServerUrl', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TextBox', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),

(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#SubjectCache', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#SubjectCache', 'http://www.w3.org/2000/01/rdf-schema#label', 'SubjectCache', 'EN', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#SubjectCache', 'http://www.w3.org/2000/01/rdf-schema#comment', 'The reference to the cached subjects ontology', 'EN', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#SubjectCache', 'http://www.w3.org/2000/01/rdf-schema#domain', 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Delivery', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#SubjectCache', 'http://www.w3.org/2000/01/rdf-schema#range', 'http://www.w3.org/2000/01/rdf-schema#Literal', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),

(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryContent', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryContent', 'http://www.w3.org/2000/01/rdf-schema#label', 'DeliveryContent', 'EN', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryContent', 'http://www.w3.org/2000/01/rdf-schema#comment', 'DeliveryContent', 'EN', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryContent', 'http://www.w3.org/2000/01/rdf-schema#domain', 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Delivery', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryContent', 'http://www.w3.org/2000/01/rdf-schema#range', 'http://www.tao.lu/middleware/taoqual.rdf#i118588753722590', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryContent', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#Authoring', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),

(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#AuthoringMode', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#AuthoringMode', 'http://www.w3.org/2000/01/rdf-schema#label', 'AuthoringMode', 'EN', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#AuthoringMode', 'http://www.w3.org/2000/01/rdf-schema#comment', 'AuthoringMode', 'EN', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#AuthoringMode', 'http://www.w3.org/2000/01/rdf-schema#domain', 'http://www.tao.lu/Ontologies/TAODelivery.rdf#Delivery', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#AuthoringMode', 'http://www.w3.org/2000/01/rdf-schema#range', 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryAuthoringModes', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#AuthoringMode', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#RadioBox', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),

(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryAuthoringModes', 'http://www.w3.org/2000/01/rdf-schema#label', 'DeliveryAuthoringModes', 'EN', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryAuthoringModes', 'http://www.w3.org/2000/01/rdf-schema#comment', 'DeliveryAuthoringModes', 'EN', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryAuthoringModes', 'http://www.w3.org/2000/01/rdf-schema#subClassOf', 'http://www.tao.lu/Ontologies/TAO.rdf#TAOObject', '', 'generis', 'yyy[]', 'yy-[]', 'y--[Administrators]'),

(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#i1268049036038811802', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryAuthoringModes', 'EN', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#i1268049036038811802', 'http://www.w3.org/2000/01/rdf-schema#label', 'simple', '', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#i1268049036038811802', 'http://www.w3.org/2000/01/rdf-schema#comment', 'simple delivery authoring mode', '', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),

(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#i1268049036038811803', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.tao.lu/Ontologies/TAODelivery.rdf#DeliveryAuthoringModes', 'EN', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#i1268049036038811803', 'http://www.w3.org/2000/01/rdf-schema#label', 'advanced', '', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(14, 'http://www.tao.lu/Ontologies/TAODelivery.rdf#i1268049036038811803', 'http://www.w3.org/2000/01/rdf-schema#comment', 'advanced delivery authoring mode', '', 'tao', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),

(15, 'http://www.tao.lu/middleware/taoqual.rdf#activityReference', 'http://www.w3.org/2000/01/rdf-schema#label', 'activityReference', 'EN', 'taoqual', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(15, 'http://www.tao.lu/middleware/taoqual.rdf#activityReference', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TreeView', '', 'taoqual', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(15, 'http://www.tao.lu/middleware/taoqual.rdf#activityReference', 'http://www.w3.org/2000/01/rdf-schema#domain', 'http://www.tao.lu/middleware/taoqual.rdf#i118589215756172', '', 'taoqual', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(15, 'http://www.tao.lu/middleware/taoqual.rdf#activityReference', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', '', 'taoqual', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(15, 'http://www.tao.lu/middleware/taoqual.rdf#activityReference', 'http://www.w3.org/2000/01/rdf-schema#range', 'http://www.tao.lu/middleware/taoqual.rdf#i118588757437650', '', 'taoqual', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(15, 'http://www.tao.lu/middleware/taoqual.rdf#activityReference', 'http://www.tao.lu/Ontologies/generis.rdf#is_language_dependent', 'http://www.tao.lu/Ontologies/generis.rdf#False', '', 'taoqual', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(15, 'http://www.tao.lu/middleware/taoqual.rdf#activityReference', 'http://www.w3.org/2000/01/rdf-schema#comment', 'the reference to the activity at the time of creating', 'EN', 'taoqual', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),

(15, 'http://www.tao.lu/middleware/taoqual.rdf#var_processinstance', 'http://www.w3.org/2000/01/rdf-schema#label', 'var_processinstance', 'EN', 'taoqual', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(15, 'http://www.tao.lu/middleware/taoqual.rdf#var_processinstance', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#ComboBox', '', 'taoqual', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(15, 'http://www.tao.lu/middleware/taoqual.rdf#var_processinstance', 'http://www.w3.org/2000/01/rdf-schema#domain', 'http://www.tao.lu/middleware/taoqual.rdf#i119010455660544', '', 'taoqual', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(15, 'http://www.tao.lu/middleware/taoqual.rdf#var_processinstance', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', '', 'taoqual', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(15, 'http://www.tao.lu/middleware/taoqual.rdf#var_processinstance', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://10.13.1.225/middleware/taoqual.rdf#i118589004639950', '', 'taoqual', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(15, 'http://www.tao.lu/middleware/taoqual.rdf#var_processinstance', 'http://www.w3.org/2000/01/rdf-schema#range', 'http://www.tao.lu/middleware/taoqual.rdf#i119010455660544', '', 'taoqual', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(15, 'http://www.tao.lu/middleware/taoqual.rdf#var_processinstance', 'http://www.tao.lu/Ontologies/generis.rdf#is_language_dependent', 'http://www.tao.lu/Ontologies/generis.rdf#False', '', 'taoqual', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(15, 'http://www.tao.lu/middleware/taoqual.rdf#var_processinstance', 'http://www.w3.org/2000/01/rdf-schema#comment', 'the uri of the process execution: used in Term_SPX and for process variables', 'EN', 'taoqual', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),

(15, 'http://www.tao.lu/middleware/taoqual.rdf#code', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property', '', 'taoqual', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(15, 'http://www.tao.lu/middleware/taoqual.rdf#code', 'http://www.w3.org/2000/01/rdf-schema#domain', 'http://www.tao.lu/middleware/taoqual.rdf#i118589004639950', '', 'taoqual', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(15, 'http://www.tao.lu/middleware/taoqual.rdf#code', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget', 'http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TextBox', '', 'taoqual', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(15, 'http://www.tao.lu/middleware/taoqual.rdf#code', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#value', '', 'EN', 'taoqual', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(15, 'http://www.tao.lu/middleware/taoqual.rdf#code', 'http://www.w3.org/2000/01/rdf-schema#comment', 'for process variables only', 'EN', 'taoqual', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(15, 'http://www.tao.lu/middleware/taoqual.rdf#code', 'http://www.w3.org/2000/01/rdf-schema#label', 'Code', 'EN', 'taoqual', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(15, 'http://www.tao.lu/middleware/taoqual.rdf#code', 'http://www.w3.org/2000/01/rdf-schema#seeAlso', '', 'EN', 'taoqual', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(15, 'http://www.tao.lu/middleware/taoqual.rdf#code', 'http://www.tao.lu/Ontologies/generis.rdf#is_language_dependent', 'http://www.tao.lu/Ontologies/generis.rdf#False', '', 'taoqual', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]'),
(15, 'http://www.tao.lu/middleware/taoqual.rdf#code', 'http://www.w3.org/2000/01/rdf-schema#isDefinedBy', ' ', 'EN', 'taoqual', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]', 'yyy[admin,administrators,authors]');

