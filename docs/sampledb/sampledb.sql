CREATE DATABASE  IF NOT EXISTS `sample_flowers` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `sample_flowers`;
-- Host: localhost    Database: sample_flowers
-- ------------------------------------------------------

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
-- Dumping data for table `colors`
--

LOCK TABLES `colors` WRITE;
/*!40000 ALTER TABLE `colors` DISABLE KEYS */;
INSERT INTO `colors` VALUES (1,'White'),(2,'Yellow'),(3,'Black'),(4,'Gray'),(5,'Orange'),(6,'Purple'),(7,'Green'),(8,'Blue'),(9,'Red');
/*!40000 ALTER TABLE `colors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `flower_color_lnk`
--

LOCK TABLES `flower_color_lnk` WRITE;
/*!40000 ALTER TABLE `flower_color_lnk` DISABLE KEYS */;
INSERT INTO `flower_color_lnk` VALUES (1,1,2),(2,1,1),(3,2,3),(4,2,4),(5,2,1);
/*!40000 ALTER TABLE `flower_color_lnk` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `flowers`
--

LOCK TABLES `flowers` WRITE;
/*!40000 ALTER TABLE `flowers` DISABLE KEYS */;
INSERT INTO `flowers` VALUES (1,'Allium','Allium','1753-03-28','Also known as flowering onion, this plant grows from a bulb or from seed, and produces globes of purple clusters of flowers atop long stems. Plant in full sun, in moist but well-drained soil.',1),(2,'Anemone','Anemone','1753-03-28','Also known as windflower, these tuberous flowers produce poppy-like blooms in early-to-mid spring. Plant anemones in full sun or part shade.',2);
/*!40000 ALTER TABLE `flowers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `smells`
--

LOCK TABLES `smells` WRITE;
/*!40000 ALTER TABLE `smells` DISABLE KEYS */;
INSERT INTO `smells` VALUES (1,'Peppery smell',5),(2,'Vanilla smell',3),(3,'Sweet smell',5),(4,'Strong foul',8);
/*!40000 ALTER TABLE `smells` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'sample_flowers'
--

--
-- Dumping routines for database 'sample_flowers'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-03-28 19:53:37
