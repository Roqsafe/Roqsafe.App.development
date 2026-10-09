-- MariaDB dump 10.19  Distrib 10.4.28-MariaDB, for osx10.10 (x86_64)
--
-- Host: localhost    Database: svi_db
-- ------------------------------------------------------
-- Server version	10.4.28-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `blocked_ips`
--

DROP TABLE IF EXISTS `blocked_ips`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blocked_ips` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL COMMENT 'IPv4 or IPv6',
  `reason` varchar(255) NOT NULL DEFAULT '',
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `blocked_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_ip` (`ip_address`),
  KEY `idx_active` (`active`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blocked_ips`
--

LOCK TABLES `blocked_ips` WRITE;
/*!40000 ALTER TABLE `blocked_ips` DISABLE KEYS */;
INSERT INTO `blocked_ips` VALUES (2,'74.248.146.253','404 abuse — 3 attempts, last URI: /kj.php',1,'2026-04-04 21:57:46'),(3,'74.234.80.3','404 abuse — 3 attempts, last URI: /taff.php',1,'2026-04-05 00:36:24'),(4,'20.48.232.178','404 abuse — 3 attempts, last URI: /file.php',1,'2026-04-05 05:20:03'),(5,'158.101.15.41','404 abuse — 3 attempts, last URI: /administrator/',1,'2026-04-05 07:02:13'),(6,'140.245.49.159','404 abuse — 3 attempts, last URI: /administrator/',1,'2026-04-05 07:02:21');
/*!40000 ALTER TABLE `blocked_ips` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `live_user_active`
--

DROP TABLE IF EXISTS `live_user_active`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `live_user_active` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `country` varchar(80) DEFAULT NULL,
  `city` varchar(80) DEFAULT NULL,
  `street` varchar(120) DEFAULT NULL,
  `postal_code` varchar(40) DEFAULT NULL,
  `full_address` text DEFAULT NULL,
  `latitude` varchar(40) DEFAULT NULL,
  `longitude` varchar(40) DEFAULT NULL,
  `accuracy` varchar(40) DEFAULT NULL,
  `location_timestamp` varchar(80) DEFAULT NULL,
  `os_name` varchar(80) DEFAULT NULL,
  `os_version` varchar(80) DEFAULT NULL,
  `device_model` varchar(80) DEFAULT NULL,
  `device_type` varchar(80) DEFAULT NULL,
  `device_vendor` varchar(80) DEFAULT NULL,
  `website_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `live_user_active`
--

LOCK TABLES `live_user_active` WRITE;
/*!40000 ALTER TABLE `live_user_active` DISABLE KEYS */;
INSERT INTO `live_user_active` VALUES (1,'213.10.224.238','Nederland','Emmen','Laan van de Marel','7823 CM','504, Laan van de Marel, Emmerhout, Emmen, Drenthe, Nederland, 7823 CM, Nederland','52.78655092314','6.934713196784','35','2026-04-16T16:35:31.000Z','MacOS','Unknown','MacIntel','Desktop','Google Inc.','http://localhost/Verifi/data/user/default','2026-04-16 14:35:31'),(2,'213.10.224.238','Nederland','Emmen','Laan van de Marel','7823 CM','504, Laan van de Marel, Emmerhout, Emmen, Drenthe, Nederland, 7823 CM, Nederland','52.786550924354','6.9347131975249','35','2026-04-16T16:36:31.000Z','MacOS','Unknown','MacIntel','Desktop','Google Inc.','http://localhost/Verifi/data/user/default','2026-04-16 14:36:32'),(3,'213.10.224.238','Nederland','Emmen','Laan van de Marel','7823 CM','508, Laan van de Marel, Emmerhout, Emmen, Drenthe, Nederland, 7823 CM, Nederland','52.786649605446','6.9346874413719','35','2026-04-16T16:37:31.000Z','MacOS','Unknown','MacIntel','Desktop','Google Inc.','http://localhost/Verifi/data/user/default','2026-04-16 14:37:32'),(4,'213.10.224.238','Nederland','Emmen','Laan van de Marel','7823 CM','506, Laan van de Marel, Emmerhout, Emmen, Drenthe, Nederland, 7823 CM, Nederland','52.786596882588','6.9345885279484','35','2026-04-16T16:38:31.000Z','MacOS','Unknown','MacIntel','Desktop','Google Inc.','http://localhost/Verifi/data/user/default','2026-04-16 14:38:32'),(5,'213.10.224.238','Nederland','Emmen','Laan van de Marel','7823 CM','506, Laan van de Marel, Emmerhout, Emmen, Drenthe, Nederland, 7823 CM, Nederland','52.78659674667','6.9345882813524','35','2026-04-16T16:39:32.000Z','MacOS','Unknown','MacIntel','Desktop','Google Inc.','http://localhost/Verifi/data/user/default','2026-04-16 14:39:33'),(6,'213.10.224.238','Nederland','Emmen','Laan van de Marel','7823 CM','506, Laan van de Marel, Emmerhout, Emmen, Drenthe, Nederland, 7823 CM, Nederland','52.786608040562','6.934694679876','35','2026-04-16T16:40:32.000Z','MacOS','Unknown','MacIntel','Desktop','Google Inc.','http://localhost/Verifi/data/user/default','2026-04-16 14:40:32'),(7,'213.10.224.238','Netherlands','Erica',NULL,NULL,'Erica, Drenthe, Netherlands','52.7098','6.9259','10000','2026-04-16T16:40:44.000Z','MacOS','Unknown','MacIntel','Desktop','Apple Computer, Inc.','http://localhost/','2026-04-16 14:40:44'),(8,'213.10.224.238','Netherlands','Erica',NULL,NULL,'Erica, Drenthe, Netherlands','52.7098','6.9259','10000','2026-04-16T16:40:49.000Z','MacOS','Unknown','MacIntel','Desktop','Apple Computer, Inc.','http://localhost/myspeed','2026-04-16 14:40:49'),(9,'213.10.224.238','Netherlands','Erica',NULL,NULL,'Erica, Drenthe, Netherlands','52.7098','6.9259','10000','2026-04-16T16:41:28.000Z','MacOS','Unknown','MacIntel','Desktop','Apple Computer, Inc.','http://localhost/TorContribution','2026-04-16 14:41:28'),(10,'213.10.224.238','Nederland','Emmen','Laan van de Marel','7823 CM','506, Laan van de Marel, Emmerhout, Emmen, Drenthe, Nederland, 7823 CM, Nederland','52.786617358684','6.934642547862','35','2026-04-16T16:41:34.000Z','MacOS','Unknown','MacIntel','Desktop','Google Inc.','http://localhost/Verifi/data/user/default','2026-04-16 14:41:35'),(11,'213.10.224.238','Netherlands','Erica',NULL,NULL,'Erica, Drenthe, Netherlands','52.7098','6.9259','10000','2026-04-16T16:42:16.000Z','MacOS','Unknown','MacIntel','Desktop','Apple Computer, Inc.','http://localhost/','2026-04-16 14:42:16'),(12,'213.10.224.238','Nederland','Emmen','Laan van de Marel','7823 CM','506, Laan van de Marel, Emmerhout, Emmen, Drenthe, Nederland, 7823 CM, Nederland','52.786618389781','6.9346576300792','35','2026-04-16T16:42:34.000Z','MacOS','Unknown','MacIntel','Desktop','Google Inc.','http://localhost/Verifi/data/user/default','2026-04-16 14:42:36'),(13,'213.10.224.238','Nederland','Emmen','Laan van de Marel','7823 CM','506, Laan van de Marel, Emmerhout, Emmen, Drenthe, Nederland, 7823 CM, Nederland','52.786618282472','6.9346575672996','35','2026-04-16T16:43:34.000Z','MacOS','Unknown','MacIntel','Desktop','Google Inc.','http://localhost/Verifi/data/user/default','2026-04-16 14:43:35'),(14,'213.10.224.238','Netherlands','Erica',NULL,NULL,'Erica, Drenthe, Netherlands','52.7098','6.9259','10000','2026-04-16T16:43:36.000Z','MacOS','Unknown','MacIntel','Desktop','Apple Computer, Inc.','http://localhost/Verifi/data/user/default','2026-04-16 14:43:36'),(15,'213.10.224.238','Netherlands','Erica',NULL,NULL,'Erica, Drenthe, Netherlands','52.7098','6.9259','10000','2026-04-16T16:43:52.000Z','MacOS','Unknown','MacIntel','Desktop','Apple Computer, Inc.','http://localhost/Verifi/data/user/default','2026-04-16 14:43:52'),(16,'213.10.224.238','Nederland','Emmen','Laan van de Marel','7823 CM','502, Laan van de Marel, Emmerhout, Emmen, Drenthe, Nederland, 7823 CM, Nederland','52.786493683281','6.9348105007989','35','2026-04-16T16:44:34.000Z','MacOS','Unknown','MacIntel','Desktop','Google Inc.','http://localhost/Verifi/data/user/default','2026-04-16 14:44:36'),(17,'213.10.224.238','Netherlands','Erica',NULL,NULL,'Erica, Drenthe, Netherlands','52.7098','6.9259','10000','2026-04-16T16:44:52.000Z','MacOS','Unknown','MacIntel','Desktop','Apple Computer, Inc.','http://localhost/Verifi/data/user/default','2026-04-16 14:44:52'),(18,'213.10.224.238','Nederland','Emmen','Laan van de Marel','7823 CM','502, Laan van de Marel, Emmerhout, Emmen, Drenthe, Nederland, 7823 CM, Nederland','52.786508826326','6.9347081105444','40','2026-04-16T16:45:35.000Z','MacOS','Unknown','MacIntel','Desktop','Google Inc.','http://localhost/Verifi/data/user/default','2026-04-16 14:45:36'),(19,'213.10.224.238','Netherlands','Erica',NULL,NULL,'Erica, Drenthe, Netherlands','52.7098','6.9259','10000','2026-04-16T16:45:52.000Z','MacOS','Unknown','MacIntel','Desktop','Apple Computer, Inc.','http://localhost/Verifi/data/user/default','2026-04-16 14:45:52'),(20,'213.10.224.238','Netherlands','Erica',NULL,NULL,'Erica, Drenthe, Netherlands','52.7098','6.9259','10000','2026-04-16T16:45:55.000Z','MacOS','Unknown','MacIntel','Desktop','Apple Computer, Inc.','http://localhost/','2026-04-16 14:45:55'),(21,'213.10.224.238','Netherlands','Erica',NULL,NULL,'Erica, Drenthe, Netherlands','52.7098','6.9259','10000','2026-04-16T16:46:07.000Z','MacOS','Unknown','MacIntel','Desktop','Google Inc.','http://localhost/admin/','2026-04-16 14:46:07'),(22,'213.10.224.238','Nederland','Emmen','Laan van de Marel','7823 CM','504, Laan van de Marel, Emmerhout, Emmen, Drenthe, Nederland, 7823 CM, Nederland','52.786563657906','6.9346433568097','35','2026-04-16T16:48:52.000Z','MacOS','Unknown','MacIntel','Desktop','Google Inc.','http://localhost/admin/','2026-04-16 14:48:52');
/*!40000 ALTER TABLE `live_user_active` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `svi_admins`
--

DROP TABLE IF EXISTS `svi_admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `svi_admins` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(60) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(120) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `login_attempts` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `locked_until` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `svi_admins`
--

LOCK TABLES `svi_admins` WRITE;
/*!40000 ALTER TABLE `svi_admins` DISABLE KEYS */;
INSERT INTO `svi_admins` VALUES (1,'admin','$2y$10$wQzV3tZY5nXk8uLpMeR0OeQGfZ2B1C4sY9D7rJ6kN8gA3mHvP5Wui','admin@ericksonholding.com',NULL,1,NULL,'2026-04-05 11:11:37'),(6,'admin_roqsafe','$2y$10$HZrs.XRUJaAHKqsbZ/ATT.IrgxhuSBe811OjQkuWqGPDmS8OQH6DO','admin.roqsafe@ericksonholding.com',NULL,0,NULL,'2026-04-16 14:36:55');
/*!40000 ALTER TABLE `svi_admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `svi_blocked_ips`
--

DROP TABLE IF EXISTS `svi_blocked_ips`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `svi_blocked_ips` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(45) NOT NULL,
  `block_type` enum('soft','permanent') NOT NULL DEFAULT 'soft',
  `reason` text DEFAULT NULL,
  `deblock_req` tinyint(1) NOT NULL DEFAULT 0,
  `deblocked` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `blocked_website` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_ip` (`ip`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `svi_blocked_ips`
--

LOCK TABLES `svi_blocked_ips` WRITE;
/*!40000 ALTER TABLE `svi_blocked_ips` DISABLE KEYS */;
INSERT INTO `svi_blocked_ips` VALUES (45,'::1','soft','Auto-blocked: multiple unknown route access attempts',0,0,'2026-04-11 20:21:46','2026-04-11 20:21:46',NULL);
/*!40000 ALTER TABLE `svi_blocked_ips` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `svi_check_log`
--

DROP TABLE IF EXISTS `svi_check_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `svi_check_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(45) NOT NULL,
  `target_ip` varchar(45) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_ip_date` (`ip`,`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `svi_check_log`
--

LOCK TABLES `svi_check_log` WRITE;
/*!40000 ALTER TABLE `svi_check_log` DISABLE KEYS */;
INSERT INTO `svi_check_log` VALUES (1,'::1','404_NOT_FOUND','2026-04-05 11:17:43'),(2,'::1','404_NOT_FOUND','2026-04-05 11:17:44'),(3,'::1','162.247.243.29','2026-04-05 12:03:30'),(4,'::1','162.247.243.29','2026-04-05 12:04:17'),(5,'::1','162.247.243.21','2026-04-05 12:07:34'),(6,'::1','20.48.232.178','2026-04-05 13:45:03'),(7,'::1','192.168.1.100','2026-04-05 13:58:25'),(8,'::1','192.168.1.100','2026-04-05 13:59:04'),(9,'::1','192.168.1.100','2026-04-05 13:59:08'),(10,'::1','192.168.1.100','2026-04-05 14:00:02'),(11,'::1','404_NOT_FOUND','2026-04-05 17:12:34'),(12,'::1','404_NOT_FOUND','2026-04-05 17:46:38'),(13,'::1','404_NOT_FOUND','2026-04-05 17:46:38'),(14,'::1','404_NOT_FOUND','2026-04-06 20:40:30'),(15,'::1','404_NOT_FOUND','2026-04-06 20:40:32'),(16,'::1','404_NOT_FOUND','2026-04-11 16:08:01'),(17,'::1','404_NOT_FOUND','2026-04-11 16:08:02'),(18,'::1','158.101.15.41','2026-04-11 17:57:20'),(19,'::1','158.101.15.41','2026-04-11 17:57:33'),(20,'::1','20.166.9.204','2026-04-11 18:03:50'),(21,'::1','20.166.9.204','2026-04-11 18:04:04'),(22,'::1','74.248.146.253','2026-04-11 18:04:46'),(23,'::1','140.245.49.159','2026-04-11 18:05:12'),(24,'::1','10.0.0.1','2026-04-11 18:06:51'),(25,'::1','192.168.1.100','2026-04-11 18:07:38'),(26,'::1','20.48.232.178','2026-04-11 18:09:44'),(27,'::1','140.245.49.159','2026-04-11 18:11:01'),(28,'::1','404_NOT_FOUND','2026-04-11 18:14:09'),(29,'::1','404_NOT_FOUND','2026-04-11 18:24:02'),(30,'::1','404_NOT_FOUND','2026-04-11 18:28:57'),(31,'::1','404_NOT_FOUND','2026-04-11 20:21:46'),(32,'::1','404_NOT_FOUND','2026-04-11 20:24:04'),(33,'::1','404_NOT_FOUND','2026-04-11 20:26:50'),(34,'::1','404_NOT_FOUND','2026-04-11 20:34:18'),(35,'::1','404_NOT_FOUND','2026-04-11 20:34:19'),(36,'::1','404_NOT_FOUND','2026-04-16 13:46:41'),(37,'::1','404_NOT_FOUND','2026-04-16 14:19:35'),(38,'::1','404_NOT_FOUND','2026-04-16 14:34:08'),(39,'::1','404_NOT_FOUND','2026-04-16 14:46:05'),(40,'::1','404_NOT_FOUND','2026-04-16 14:48:51');
/*!40000 ALTER TABLE `svi_check_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `svi_content`
--

DROP TABLE IF EXISTS `svi_content`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `svi_content` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `page` varchar(80) NOT NULL DEFAULT 'main',
  `key` varchar(120) NOT NULL,
  `value` text NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_page_key` (`page`,`key`)
) ENGINE=InnoDB AUTO_INCREMENT=185 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `svi_content`
--

LOCK TABLES `svi_content` WRITE;
/*!40000 ALTER TABLE `svi_content` DISABLE KEYS */;
INSERT INTO `svi_content` VALUES (1,'main','brand_eh','Erickson<br>Holding','2026-04-05 11:11:37'),(2,'main','brand_svi_title','SVI','2026-04-05 11:11:37'),(3,'main','brand_svi_sub','Security and verification inspector','2026-04-05 11:11:37'),(4,'main','page_title','SVI — Security and Verification Inspector','2026-04-05 11:11:37'),(5,'main','hero_heading','Oh no…','2026-04-05 11:11:37'),(6,'main','hero_emoji','🚫','2026-04-05 11:11:37'),(7,'main','intro_p1','During a recent review, SVI (Security &amp; Verification Inspector) detected unusual or suspicious activity associated with your IP address and/or account usage. Based on these findings, your access to the app and our services has been temporarily restricted or fully blocked.','2026-04-05 11:11:37'),(8,'main','intro_p2','Our systems continuously perform automated and manual checks to ensure the safety, integrity, and reliability of our platform. When abnormal patterns are identified—such as unauthorized access attempts, misuse of features, or violations of our terms of service—this may result in an immediate restriction.','2026-04-05 11:11:37'),(9,'main','ip_input_placeholder','type your ip in for deblock','2026-04-05 11:11:37'),(10,'main','ip_input_hint','check or the deblock work for you','2026-04-05 11:11:37'),(11,'main','ip_submit_btn','Check IP','2026-04-05 11:11:37'),(12,'main','section1_title','What does this mean for you?','2026-04-05 11:11:37'),(13,'main','section1_body','At this time, your account has been flagged for further investigation. This means you may not be able to access certain features or may be completely restricted from the platform until the verification process is completed.','2026-04-05 11:11:37'),(14,'main','section2_title','Verification Process','2026-04-05 11:11:37'),(15,'main','section2_body','You may be eligible for a review of this restriction. To proceed, you will need to complete a verification process, which may require additional information such as identity confirmation, device verification, or details about recent activity. This helps us determine whether access can be safely restored.','2026-04-05 11:11:37'),(16,'main','section3_title','Please note:','2026-04-05 11:11:37'),(17,'main','section3_body','In some cases, the restriction may be permanent. If our investigation confirms serious or repeated violations of our policies, the block will remain in place and cannot be reversed. In such situations, we are unable to provide further assistance regarding account reactivation.','2026-04-05 11:11:37'),(18,'main','section4_title','Important','2026-04-05 11:11:37'),(19,'main','section4_item1','Any attempt to bypass this restriction may result in additional actions.','2026-04-05 11:11:37'),(20,'main','section4_item2','Creating multiple accounts or using alternative access methods is strictly prohibited.','2026-04-05 11:11:37'),(21,'main','section4_item3','We strongly recommend completing the verification process accurately for a possible review.','2026-04-05 11:11:37'),(22,'main','closing_note','We understand this situation may be frustrating, and we are committed to handling every case carefully and fairly. Thank you for your understanding and cooperation.','2026-04-05 11:11:37'),(23,'main','footer_text','SVI – Security &amp; Verification Inspector Team','2026-04-05 11:11:37'),(24,'main','result_not_found','This IP address is not in our records and has no active block.','2026-04-05 11:11:37'),(25,'main','result_soft_blocked','This IP address is currently <strong>soft-blocked</strong>. A deblock request has been initiated — please follow the verification steps below.','2026-04-05 11:11:37'),(26,'main','result_perm_blocked','This IP address has been <strong>permanently blocked</strong>. This decision cannot be reversed and no further assistance can be provided.','2026-04-05 11:11:37'),(27,'main','result_deblocked','This IP address has already been successfully deblocked and is no longer restricted.','2026-04-05 11:11:37'),(28,'404','page_title','404 Not Found — SVI','2026-04-05 11:11:37'),(29,'404','heading','Page Not Found','2026-04-05 11:11:37'),(30,'404','emoji','🧐','2026-04-05 11:11:37'),(31,'404','body','The page you are looking for does not exist or has been moved.','2026-04-05 11:11:37'),(32,'404','btn_label','Return to Home','2026-04-05 11:11:37'),(33,'500','page_title','500 Server Error — SVI','2026-04-05 11:11:37'),(34,'500','heading','Internal Server Error','2026-04-05 11:11:37'),(35,'500','emoji','🔥','2026-04-05 11:11:37'),(36,'500','body','Something went wrong on our end. We are looking into it.','2026-04-05 11:11:37'),(37,'500','btn_label','Return to Home','2026-04-05 11:11:37'),(38,'403','page_title','403 Forbidden — SVI','2026-04-05 11:11:37'),(39,'403','heading','Access Denied','2026-04-05 11:11:37'),(40,'403','emoji','⛔','2026-04-05 11:11:37'),(41,'403','body','You do not have permission to view this resource.','2026-04-05 11:11:37'),(42,'403','btn_label','Return to Home','2026-04-05 11:11:37'),(127,'main','hero_heading_safe','All Good!','2026-04-05 12:47:31'),(128,'main','hero_emoji_safe','✅','2026-04-05 12:47:31'),(129,'main','intro_p1_safe','You are a great user! Your IP address has been verified and is currently safe on our platform. No further action is required on your part to access our services.','2026-04-05 12:47:31'),(130,'main','intro_p2_safe','Our systems continuously monitor network health to ensure Erickson Holding remains a secure environment. Your connection checks out perfectly.','2026-04-05 12:47:31'),(173,'main','section1_title_safe','Security First','2026-04-05 12:58:18'),(174,'main','section1_body_safe','We prioritize your online safety by actively monitoring our network infrastructure. Because of this, you can browse with confidence.','2026-04-05 12:58:18'),(175,'main','section2_title_safe','Continuous Verification','2026-04-05 12:58:18'),(176,'main','section2_body_safe','Occasional soft-checks help guarantee the Erickson Holding ecosystem stays protected against automated bots and malicious activity.','2026-04-05 12:58:18'),(177,'main','section3_title_safe','Data Privacy','2026-04-05 12:58:18'),(178,'main','section3_body_safe','Your session and connection details remain securely encrypted. We value your digital privacy and ensure your data remains strictly confidential.','2026-04-05 12:58:18'),(179,'main','section4_title_safe','Best Practices','2026-04-05 12:58:18'),(180,'main','section4_item1_safe','Always use strong, unique passwords for maximum security.','2026-04-05 12:58:18'),(181,'main','section4_item2_safe','Enable Two-Factor Authentication (2FA) wherever possible.','2026-04-05 12:58:18'),(182,'main','section4_item3_safe','Regularly monitor your active sessions and device history.','2026-04-05 12:58:18'),(183,'main','closing_note_safe','Thank you for being a part of our secure platform.','2026-04-05 12:58:18');
/*!40000 ALTER TABLE `svi_content` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `svi_login_log`
--

DROP TABLE IF EXISTS `svi_login_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `svi_login_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(60) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `success` tinyint(1) NOT NULL DEFAULT 0,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `svi_login_log`
--

LOCK TABLES `svi_login_log` WRITE;
/*!40000 ALTER TABLE `svi_login_log` DISABLE KEYS */;
INSERT INTO `svi_login_log` VALUES (1,'admin@ericksonholding.com','::1',0,'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.6 Safari/605.1.15','2026-04-05 13:04:24');
/*!40000 ALTER TABLE `svi_login_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `svi_settings`
--

DROP TABLE IF EXISTS `svi_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `svi_settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(80) NOT NULL,
  `value` text NOT NULL,
  `label` varchar(120) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `type` enum('boolean','integer','text','email','textarea') NOT NULL DEFAULT 'text',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_key` (`setting_key`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `svi_settings`
--

LOCK TABLES `svi_settings` WRITE;
/*!40000 ALTER TABLE `svi_settings` DISABLE KEYS */;
INSERT INTO `svi_settings` VALUES (1,'allow_deblock','1','Allow Deblock Requests','When disabled users cannot submit deblock requests from the public page.','boolean','2026-04-05 11:11:37'),(2,'deblock_cooldown_hours','24','Deblock Cooldown (hours)','Minimum hours between two deblock requests from the same IP.','integer','2026-04-05 11:11:37'),(3,'max_check_attempts','10','Max IP Check Attempts / day','Maximum times a single IP can call the block-check endpoint per day.','integer','2026-04-05 11:11:37'),(4,'maintenance_mode','0','Maintenance Mode','Show a maintenance notice on the public page instead of the normal content.','boolean','2026-04-05 11:11:37'),(5,'maintenance_message','We are currently performing scheduled maintenance. The SVI portal will be back shortly.','Maintenance Message','Shown when maintenance mode is active.','textarea','2026-04-05 11:11:37'),(6,'admin_email','admin@ericksonholding.com','Admin Email','Used for system notifications and deblock alerts.','email','2026-04-05 11:11:37'),(7,'session_timeout_minutes','60','Admin Session Timeout (min)','Minutes of inactivity before the admin session expires.','integer','2026-04-05 11:11:37'),(8,'login_max_attempts','5','Max Login Attempts','Failed attempts before the admin account is temporarily locked.','integer','2026-04-05 11:11:37'),(9,'login_lockout_minutes','15','Login Lockout Duration (min)','Minutes an admin account remains locked after too many failed attempts.','integer','2026-04-05 11:11:37'),(10,'show_ip_tool','1','Show IP Check Tool','Display the IP check / deblock form on the public page.','boolean','2026-04-05 11:11:37'),(11,'site_notice','','Site Notice Banner','Optional notice displayed at the top of the public page (leave empty to hide).','textarea','2026-04-05 11:11:37'),(12,'auto_block_on_exceed','1','Auto-Block on Attempt Exceed','Automatically soft-block IPs that exceed the daily check limit.','boolean','2026-04-05 11:11:37'),(13,'require_reason_for_block','0','Require Reason When Blocking','Admin must enter a reason when adding a new block via the panel.','boolean','2026-04-05 11:11:37'),(54,'admin_access_token','portal-access-77','Admin Access Token','Secret URL token for accessing the admin panel','text','2026-04-16 14:36:55');
/*!40000 ALTER TABLE `svi_settings` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-04-16 16:54:28
