-- CasaOne Project - Complete Database Schema
-- Run this file in phpMyAdmin (select database 'project' first, or create it)
-- Or: mysql -u root project < database/project.sql

-- ============================================================
-- Part 1: Base schema
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `project`
--
CREATE DATABASE IF NOT EXISTS `project` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `project`;

-- --------------------------------------------------------
-- Table structure for table `admin`
-- --------------------------------------------------------
CREATE TABLE `admin` (
  `a_id` int NOT NULL,
  `a_name` varchar(100) DEFAULT NULL,
  `a_email` varchar(100) DEFAULT NULL,
  `a_password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `admin` (`a_id`, `a_name`, `a_email`, `a_password`) VALUES
(1, 'admin', 'admin@gmail.com', 'admin@123');

-- --------------------------------------------------------
-- Table structure for table `complaint`
-- --------------------------------------------------------
CREATE TABLE `complaint` (
  `c_id` int NOT NULL,
  `a_id` int DEFAULT NULL,
  `description` text,
  `status` varchar(50) DEFAULT NULL,
  `c_date` date DEFAULT NULL,
  `c_name` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
-- Table structure for table `hostel`
-- --------------------------------------------------------
CREATE TABLE `hostel` (
  `h_id` int NOT NULL,
  `u_id` int DEFAULT NULL,
  `a_id` int DEFAULT NULL,
  `h_name` varchar(100) DEFAULT NULL,
  `location` varchar(150) DEFAULT NULL,
  `h_email` varchar(100) DEFAULT NULL,
  `h_phone` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
-- Table structure for table `payment`
-- --------------------------------------------------------
CREATE TABLE `payment` (
  `p_id` int NOT NULL,
  `u_id` int DEFAULT NULL,
  `a_id` int DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `pay_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
-- Table structure for table `room`
-- --------------------------------------------------------
CREATE TABLE `room` (
  `room_id` int NOT NULL,
  `h_id` int DEFAULT NULL,
  `room_no` varchar(20) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `availability` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
-- Table structure for table `roomtype`
-- --------------------------------------------------------
CREATE TABLE `roomtype` (
  `r_id` int NOT NULL,
  `room_id` int DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `capacity` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------
-- Table structure for table `users`
-- --------------------------------------------------------
CREATE TABLE `users` (
  `u_id` int NOT NULL,
  `h_id` int DEFAULT NULL,
  `u_name` varchar(100) DEFAULT NULL,
  `u_email` varchar(100) DEFAULT NULL,
  `u_phone` varchar(15) DEFAULT NULL,
  `u_address` varchar(255) DEFAULT NULL,
  `u_password` varchar(255) DEFAULT NULL,
  `reg_date` date DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Indexes
ALTER TABLE `admin` ADD PRIMARY KEY (`a_id`);
ALTER TABLE `complaint` ADD PRIMARY KEY (`c_id`), ADD KEY `a_id` (`a_id`);
ALTER TABLE `hostel` ADD PRIMARY KEY (`h_id`), ADD KEY `u_id` (`u_id`), ADD KEY `a_id` (`a_id`);
ALTER TABLE `payment` ADD PRIMARY KEY (`p_id`), ADD KEY `u_id` (`u_id`), ADD KEY `a_id` (`a_id`);
ALTER TABLE `room` ADD PRIMARY KEY (`room_id`), ADD KEY `h_id` (`h_id`);
ALTER TABLE `roomtype` ADD PRIMARY KEY (`r_id`), ADD KEY `room_id` (`room_id`);
ALTER TABLE `users` ADD PRIMARY KEY (`u_id`), ADD KEY `h_id` (`h_id`);

-- AUTO_INCREMENT
ALTER TABLE `admin` MODIFY `a_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `complaint` MODIFY `c_id` int NOT NULL AUTO_INCREMENT;
ALTER TABLE `hostel` MODIFY `h_id` int NOT NULL AUTO_INCREMENT;
ALTER TABLE `payment` MODIFY `p_id` int NOT NULL AUTO_INCREMENT;
ALTER TABLE `room` MODIFY `room_id` int NOT NULL AUTO_INCREMENT;
ALTER TABLE `roomtype` MODIFY `r_id` int NOT NULL AUTO_INCREMENT;
ALTER TABLE `users` MODIFY `u_id` int NOT NULL AUTO_INCREMENT;

-- Foreign keys
ALTER TABLE `complaint` ADD CONSTRAINT `complaint_ibfk_2` FOREIGN KEY (`a_id`) REFERENCES `admin` (`a_id`);
ALTER TABLE `hostel` ADD CONSTRAINT `hostel_ibfk_1` FOREIGN KEY (`u_id`) REFERENCES `users` (`u_id`), ADD CONSTRAINT `hostel_ibfk_2` FOREIGN KEY (`a_id`) REFERENCES `admin` (`a_id`);
ALTER TABLE `payment` ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`u_id`) REFERENCES `users` (`u_id`), ADD CONSTRAINT `payment_ibfk_2` FOREIGN KEY (`a_id`) REFERENCES `admin` (`a_id`);
ALTER TABLE `room` ADD CONSTRAINT `room_ibfk_1` FOREIGN KEY (`h_id`) REFERENCES `hostel` (`h_id`);
ALTER TABLE `roomtype` ADD CONSTRAINT `roomtype_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `room` (`room_id`);
ALTER TABLE `users` ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`h_id`) REFERENCES `hostel` (`h_id`);
COMMIT;

-- ============================================================
-- Part 2: Extensions (booking, tokens, feedback, FAQ, etc.)
-- ============================================================

USE project;

-- Payment type for admin panel
ALTER TABLE `payment` ADD COLUMN `pay_type` varchar(50) DEFAULT NULL;

-- Room photo column for admin room images
ALTER TABLE `room` ADD COLUMN `photo` varchar(255) DEFAULT NULL;

-- Booking (user room booking)
CREATE TABLE IF NOT EXISTS `booking` (
  `b_id` int NOT NULL AUTO_INCREMENT,
  `u_id` int NOT NULL,
  `room_id` int NOT NULL,
  `h_id` int DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `amount` decimal(10,2) DEFAULT NULL,
  `book_date` date DEFAULT NULL,
  `check_in` date DEFAULT NULL,
  `check_out` date DEFAULT NULL,
  PRIMARY KEY (`b_id`),
  KEY `u_id` (`u_id`),
  KEY `room_id` (`room_id`),
  KEY `h_id` (`h_id`),
  CONSTRAINT `booking_ibfk_1` FOREIGN KEY (`u_id`) REFERENCES `users` (`u_id`),
  CONSTRAINT `booking_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `room` (`room_id`),
  CONSTRAINT `booking_ibfk_3` FOREIGN KEY (`h_id`) REFERENCES `hostel` (`h_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Remember me tokens (session cookie for users)


-- User complaints: add u_id for registered users
ALTER TABLE `complaint` ADD COLUMN `u_id` int DEFAULT NULL;
ALTER TABLE `complaint` ADD CONSTRAINT `complaint_u_id_fk` FOREIGN KEY (`u_id`) REFERENCES `users` (`u_id`);

-- Feedback table
CREATE TABLE IF NOT EXISTS `feedback` (
  `f_id` int NOT NULL AUTO_INCREMENT,
  `u_id` int DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `rating` int DEFAULT 5,
  `message` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`f_id`),
  KEY `u_id` (`u_id`),
  CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`u_id`) REFERENCES `users` (`u_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- FAQ table
CREATE TABLE IF NOT EXISTS `faq` (
  `id` int NOT NULL AUTO_INCREMENT,
  `question` text NOT NULL,
  `answer` text NOT NULL,
  `sort_order` int DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `faq` (`question`, `answer`, `sort_order`) VALUES
('How do I book a room?', 'Go to the Booking page to select a room and check-in date. You can also apply from the Rooms page.', 1),
('What types of rooms are available?', 'See the Rooms page for room types, capacity, and availability.', 2),
('What is the fee structure?', 'See the Fee page for monthly and annual rates by room type.', 3);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
