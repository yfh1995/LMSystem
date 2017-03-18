-- MySQL dump 10.13  Distrib 5.7.9, for Win32 (AMD64)
--
-- Host: localhost    Database: lmsystem
-- ------------------------------------------------------
-- Server version	5.6.24

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admin_menu`
--

DROP TABLE IF EXISTS `admin_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `order` int(11) NOT NULL,
  `title` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `icon` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `uri` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_menu`
--

LOCK TABLES `admin_menu` WRITE;
/*!40000 ALTER TABLE `admin_menu` DISABLE KEYS */;
INSERT INTO `admin_menu` VALUES (1,0,1,'首页','fa-bar-chart','/','0000-00-00 00:00:00','0000-00-00 00:00:00'),(2,0,8,'认证','fa-tasks','','0000-00-00 00:00:00','2017-02-20 15:44:38'),(3,0,7,'管理员','fa-users','auth/users','0000-00-00 00:00:00','2017-02-20 15:44:27'),(4,2,9,'角色','fa-user','auth/roles','0000-00-00 00:00:00','2017-02-20 15:44:38'),(5,2,10,'权限','fa-user','auth/permissions','0000-00-00 00:00:00','2017-02-20 15:44:38'),(6,2,11,'菜单','fa-bars','auth/menu','0000-00-00 00:00:00','2017-02-20 15:44:38'),(7,0,2,'图书管理','fa-bars','','2017-02-16 10:35:09','2017-02-16 11:26:20'),(8,0,6,'用户管理','fa-bars','users','2017-02-16 10:36:17','2017-02-20 18:11:21'),(9,7,3,'书籍管理','fa-bars','/books','2017-02-16 11:27:01','2017-02-20 15:43:11'),(10,7,4,'分类管理','fa-bars','booksType','2017-02-16 11:28:34','2017-02-17 12:34:39'),(11,7,5,'借阅管理','fa-bars','/borrow','2017-02-20 15:42:41','2017-02-20 15:44:38'),(13,2,0,'系统配置','fa-bars','config','2017-03-03 13:16:33','2017-03-03 13:16:33');
/*!40000 ALTER TABLE `admin_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_permissions`
--

DROP TABLE IF EXISTS `admin_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_permissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `admin_permissions_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_permissions`
--

LOCK TABLES `admin_permissions` WRITE;
/*!40000 ALTER TABLE `admin_permissions` DISABLE KEYS */;
INSERT INTO `admin_permissions` VALUES (1,'用户','用户','2017-02-15 17:01:45','2017-02-15 17:01:45'),(2,'test1','test1','2017-03-12 13:05:22','2017-03-12 13:05:22');
/*!40000 ALTER TABLE `admin_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_role_menu`
--

DROP TABLE IF EXISTS `admin_role_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_role_menu` (
  `role_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `admin_role_menu_role_id_menu_id_index` (`role_id`,`menu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_role_menu`
--

LOCK TABLES `admin_role_menu` WRITE;
/*!40000 ALTER TABLE `admin_role_menu` DISABLE KEYS */;
INSERT INTO `admin_role_menu` VALUES (1,2,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(1,13,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(1,3,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(1,4,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(1,5,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(1,6,'0000-00-00 00:00:00','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `admin_role_menu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_role_permissions`
--

DROP TABLE IF EXISTS `admin_role_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `admin_role_permissions_role_id_permission_id_index` (`role_id`,`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_role_permissions`
--

LOCK TABLES `admin_role_permissions` WRITE;
/*!40000 ALTER TABLE `admin_role_permissions` DISABLE KEYS */;
INSERT INTO `admin_role_permissions` VALUES (4,2,'0000-00-00 00:00:00','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `admin_role_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_role_users`
--

DROP TABLE IF EXISTS `admin_role_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_role_users` (
  `role_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `admin_role_users_role_id_user_id_index` (`role_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_role_users`
--

LOCK TABLES `admin_role_users` WRITE;
/*!40000 ALTER TABLE `admin_role_users` DISABLE KEYS */;
INSERT INTO `admin_role_users` VALUES (1,1,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(0,1,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(0,1,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(2017,1,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(2,2,'0000-00-00 00:00:00','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `admin_role_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_roles`
--

DROP TABLE IF EXISTS `admin_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `admin_roles_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_roles`
--

LOCK TABLES `admin_roles` WRITE;
/*!40000 ALTER TABLE `admin_roles` DISABLE KEYS */;
INSERT INTO `admin_roles` VALUES (1,'Administrator','administrator','2017-02-13 02:49:03','2017-02-13 02:49:03'),(2,'Admin','admin','2017-02-16 10:34:29','2017-02-16 10:34:29');
/*!40000 ALTER TABLE `admin_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_users`
--

DROP TABLE IF EXISTS `admin_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(190) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `admin_users_username_unique` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_users`
--

LOCK TABLES `admin_users` WRITE;
/*!40000 ALTER TABLE `admin_users` DISABLE KEYS */;
INSERT INTO `admin_users` VALUES (1,'admin','$2y$10$n6ekpI9f7b4In8FQ3o5ij.IQUmGcFtJ98yC4o7hL11mapa/uF8z.i','Administrator','k5W16nmI2nfm1O5DtY2dTbApvv8EDnmCP27cyjVIOoluwhlk7OfV9pDdymlH','2017-02-13 02:49:03','2017-03-18 05:53:20'),(2,'user','$2y$10$aFnpaaHiQcqm6DYADRhskOKzX6AJzric5KgNQFX4.aypKNRbz3Cay','user','y97KmXCagtNiO8Miq1pAl4h3odc8emRetdkyuffhZ2H5IgXRyXPqf9Co5dOj','2017-03-18 05:53:16','2017-03-18 05:53:41');
/*!40000 ALTER TABLE `admin_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `books_info`
--

DROP TABLE IF EXISTS `books_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `books_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `book_number` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '书号',
  `type_id` int(11) NOT NULL COMMENT '图书类别',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '书名',
  `press` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '出版社',
  `publication_year` int(11) NOT NULL COMMENT '出版年份',
  `author` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '作者',
  `price` double(8,2) NOT NULL COMMENT '价格',
  `cur_total` int(11) NOT NULL DEFAULT '0' COMMENT '在库量',
  `total` int(11) NOT NULL DEFAULT '0' COMMENT '总藏量',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `books_info`
--

LOCK TABLES `books_info` WRITE;
/*!40000 ALTER TABLE `books_info` DISABLE KEYS */;
INSERT INTO `books_info` VALUES (1,'9787539962870',4,'雪中悍刀行','江苏文艺出版社',2013,'烽火戏诸侯',30.00,8,10,'2017-02-20 12:58:06','0000-00-00 00:00:00'),(2,'9787539962871',4,'升龙道','不知道什么出版社',2013,'血红',30.00,10,10,'2017-02-17 13:25:58','0000-00-00 00:00:00'),(3,'9787806803691',3,'龙域','太白文艺出版社',2013,'众生',30.00,10,10,'2017-02-20 11:31:20','0000-00-00 00:00:00'),(4,'9787806808368',3,'步步生莲','太白文艺出版社',2010,'月关',30.00,10,10,'2017-02-20 11:37:08','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `books_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `books_type`
--

DROP TABLE IF EXISTS `books_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `books_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '分类名字',
  `parent_id` int(11) NOT NULL DEFAULT '0' COMMENT '父分类id',
  `sort` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `books_type`
--

LOCK TABLES `books_type` WRITE;
/*!40000 ALTER TABLE `books_type` DISABLE KEYS */;
INSERT INTO `books_type` VALUES (1,'自然科学',0,1,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(2,'物理',1,2,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(3,'高能物理',2,4,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(4,'宏观物理',2,3,'0000-00-00 00:00:00','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `books_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `borrow_info`
--

DROP TABLE IF EXISTS `borrow_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `borrow_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '借阅者id',
  `book_id` int(11) NOT NULL COMMENT '图书id',
  `end_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '截止时间',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `borrow_info`
--

LOCK TABLES `borrow_info` WRITE;
/*!40000 ALTER TABLE `borrow_info` DISABLE KEYS */;
INSERT INTO `borrow_info` VALUES (1,1,1,'2017-03-20 17:02:16','2017-02-20 17:02:21','0000-00-00 00:00:00',2),(2,1,1,'2017-03-22 17:42:12','2017-02-20 17:42:12','0000-00-00 00:00:00',0);
/*!40000 ALTER TABLE `borrow_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `config`
--

DROP TABLE IF EXISTS `config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '配置名',
  `value` text COLLATE utf8_unicode_ci NOT NULL COMMENT '值',
  `remarks` text COLLATE utf8_unicode_ci COMMENT '备注',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否启用配置，0：禁用，1：启用',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `config`
--

LOCK TABLES `config` WRITE;
/*!40000 ALTER TABLE `config` DISABLE KEYS */;
INSERT INTO `config` VALUES (1,'page_num','15','每页数据数量',1,'2017-02-16 10:21:33','2017-02-16 10:21:33'),(2,'borrow_term','2592000','借阅时长',1,'2017-02-20 17:27:45','2017-02-20 17:27:47'),(3,'borrow_num','10','默认借阅数量',1,'2017-03-02 18:02:41','2017-03-02 18:02:43'),(4,'index_false','1','首页假数据开关（0：关，1：开）',1,'2017-03-03 13:15:21','2017-03-18 07:04:17');
/*!40000 ALTER TABLE `config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `migration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES ('2016_01_04_173148_create_admin_tables',1),('2014_10_12_000000_create_users_table',2),('2014_10_12_100000_create_password_resets_table',2),('2017_02_15_170515_create_user_info_table',3),('2017_02_15_170705_create_books_info_table',3),('2017_02_15_170831_create_borrow_info_table',3),('2017_02_15_174401_create_config_table',3),('2017_02_15_174732_create_book_type_table',3);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `password_resets_email_index` (`email`),
  KEY `password_resets_token_index` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_info`
--

DROP TABLE IF EXISTS `user_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) COLLATE utf8_unicode_ci NOT NULL COMMENT '名字',
  `identity` int(11) DEFAULT NULL COMMENT '身份，0：学生，1：老师',
  `major` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '专业',
  `grade` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '年级',
  `class` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `id_number` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '证件号码',
  `sex` tinyint(1) NOT NULL COMMENT '性别，true：男，false：女',
  `available_num` int(11) NOT NULL COMMENT '可借阅数量',
  `sum_num` int(11) NOT NULL COMMENT '总借阅数量',
  `phone` varchar(20) COLLATE utf8_unicode_ci NOT NULL COMMENT '手机号',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_info`
--

LOCK TABLES `user_info` WRITE;
/*!40000 ALTER TABLE `user_info` DISABLE KEYS */;
INSERT INTO `user_info` VALUES (1,'admin',0,'物联网工程','2013','3','13311020315',1,9,10,'13311020315','2017-02-20 17:06:56','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `user_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-03-18 15:11:14
