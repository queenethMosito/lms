/*
SQLyog Community v13.1.5  (64 bit)
MySQL - 5.6.21 : Database - lms
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`lms` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `lms`;

/*Table structure for table `acc_sessions` */

DROP TABLE IF EXISTS `acc_sessions`;

CREATE TABLE `acc_sessions` (
  `session_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `session_serial` varchar(100) NOT NULL DEFAULT '',
  `session_data` text NOT NULL,
  `expires` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `acc_sessions` */

/*Table structure for table `attendance` */

DROP TABLE IF EXISTS `attendance`;

CREATE TABLE `attendance` (
  `attendance_id` int(11) NOT NULL AUTO_INCREMENT,
  `class_id` int(11) NOT NULL,
  `attendee_id` int(11) NOT NULL,
  `required` tinyint(1) NOT NULL DEFAULT '1',
  `attended` tinyint(1) NOT NULL DEFAULT '0',
  `communicated` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`attendance_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `attendance` */

/*Table structure for table `attendees` */

DROP TABLE IF EXISTS `attendees`;

CREATE TABLE `attendees` (
  `attendee_id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(288) NOT NULL,
  `last_name` varchar(288) NOT NULL,
  `student_number` varchar(11) NOT NULL,
  `email_address` varchar(50) NOT NULL,
  `status_id` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`attendee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `attendees` */

/*Table structure for table `classes` */

DROP TABLE IF EXISTS `classes`;

CREATE TABLE `classes` (
  `class_id` bigint(11) NOT NULL AUTO_INCREMENT,
  `class_name` varchar(100) NOT NULL,
  `class_description` text NOT NULL,
  `class_start_date` date NOT NULL,
  `class_end_date` date NOT NULL,
  `class_start_time` time NOT NULL,
  `class_end_time` time NOT NULL,
  `status_id` tinyint(1) NOT NULL DEFAULT '1',
  `capacity` int(11) NOT NULL,
  PRIMARY KEY (`class_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `classes` */

/*Table structure for table `fines` */

DROP TABLE IF EXISTS `fines`;

CREATE TABLE `fines` (
  `fine_id` int(11) NOT NULL AUTO_INCREMENT,
  `attendance_id` int(11) NOT NULL,
  `fine_date` datetime NOT NULL,
  `fine_amount` int(3) NOT NULL,
  PRIMARY KEY (`fine_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `fines` */

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `user_id` bigint(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `surname` varchar(128) NOT NULL,
  `email_address` varchar(150) NOT NULL,
  `salt` varchar(64) NOT NULL,
  `password` varchar(50) NOT NULL,
  `date_created` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  `account_active` int(1) NOT NULL DEFAULT '1',
  `date_last_active` datetime NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `users` */

insert  into `users`(`user_id`,`name`,`surname`,`email_address`,`salt`,`password`,`date_created`,`date_modified`,`account_active`,`date_last_active`) values (1,'Admin','Admin','admin@admin.co.za','ee9efed9a8ae58834590bfbf82c92986','*c5360364275bd27079267ede917f6219','2020-09-02 13:37:02','2020-09-04 07:02:12',1,'0000-00-00 00:00:00');

/* Function  structure for function  `fnGET_MAX_STUDENT_NR` */

/*!50003 DROP FUNCTION IF EXISTS `fnGET_MAX_STUDENT_NR` */;
DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` FUNCTION `fnGET_MAX_STUDENT_NR`() RETURNS varchar(6) CHARSET utf8
BEGIN
DECLARE student_number_ret VARCHAR(6);
        
    SET student_number_ret = (SELECT COALESCE(MAX(student_number),0)+1 AS student_number_ret
                FROM attendees);
    IF LENGTH(student_number_ret) = 1 THEN
        SET student_number_ret = CONCAT('00000',CAST(student_number_ret AS CHAR));
    ELSEIF LENGTH(student_number_ret) = 2 THEN
        SET student_number_ret = CONCAT('0000',CAST(student_number_ret AS CHAR));
    ELSEIF LENGTH(student_number_ret) = 3 THEN
        SET student_number_ret = CONCAT('000',CAST(student_number_ret AS CHAR));
    ELSEIF LENGTH(student_number_ret) = 4 THEN
        SET student_number_ret = CONCAT('00',CAST(student_number_ret AS CHAR));
    ELSEIF LENGTH(student_number_ret) = 5 THEN
        SET student_number_ret = CONCAT('0',CAST(student_number_ret AS CHAR));
    END IF;
        
    RETURN student_number_ret;
    END */$$
DELIMITER ;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
