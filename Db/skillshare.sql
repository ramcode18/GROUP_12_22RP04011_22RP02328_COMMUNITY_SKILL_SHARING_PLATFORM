-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 14, 2025 at 04:44 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `skillshare`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `lesson_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `lesson_id`, `user_id`, `comment`, `created_at`) VALUES
(3, 6, 33, 'Nice lesson', '2025-05-13 23:43:45'),
(4, 7, 33, 'hy', '2025-05-14 11:08:36');

-- --------------------------------------------------------

--
-- Table structure for table `lessons`
--

CREATE TABLE `lessons` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_approved` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lessons`
--

INSERT INTO `lessons` (`id`, `admin_id`, `title`, `content`, `created_at`, `updated_at`, `is_approved`) VALUES
(5, 33, 'Java Script', 'The Skill Sharing Platform uses several tables to manage users, lessons, and interactions. The users table stores basic user info and roles like admin or user. The lessons table holds lesson content created by admins, which must be approved. Users can leave feedback through the comments table and request new topics using the lesson_requests table. Admins can approve or reject these through logs in the approvals table', '2025-05-13 22:52:53', NULL, 1),
(6, 33, 'advanced web technology', 'phpMyAdmin is a free, open-source web-based application written in PHP that allows users to manage MySQL and MariaDB databases through an intuitive graphical interface. It simplifies common database operations such as creating, modifying, and deleting databases, tables, and records without the need to write raw SQL commands.', '2025-05-13 23:31:56', NULL, 0),
(7, 33, 'System Analyst', 'more new knowledge', '2025-05-14 11:01:51', NULL, 1),
(8, 33, 'IOT', 'physical things', '2025-05-14 14:24:52', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `lesson_requests`
--

CREATE TABLE `lesson_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `topic` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('pending','fulfilled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lesson_requests`
--

INSERT INTO `lesson_requests` (`id`, `user_id`, `topic`, `description`, `status`, `created_at`, `updated_at`) VALUES
(2, NULL, '1', NULL, 'pending', '2025-05-12 23:35:08', NULL),
(3, NULL, '1', NULL, 'pending', '2025-05-12 23:35:31', NULL),
(4, NULL, '2', NULL, 'pending', '2025-05-12 23:40:31', NULL),
(9, 34, 'Python', 'Python is a high-level, interpreted programming language known for its simplicity, readability, and versatility. It supports multiple programming paradigms, including procedural, object-oriented, and functional programming, making it suitable for a wide range of applications—from web development and data analysis to artificial intelligence and automation.', 'pending', '2025-05-13 23:34:34', NULL),
(10, 34, 'CProgramming', 'Fine and amazinga Lesson', 'fulfilled', '2025-05-14 10:10:42', NULL),
(11, 34, 'CProgramming', 'Fine and amazinga Lesson', 'pending', '2025-05-14 10:11:59', NULL),
(12, 35, 'java script', 'all in all', 'fulfilled', '2025-05-14 10:54:38', NULL),
(13, 34, 'embedded system', 'all in all', 'pending', '2025-05-14 14:26:43', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `id` int(11) NOT NULL,
  `lesson_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `likes`
--

INSERT INTO `likes` (`id`, `lesson_id`, `user_id`, `created_at`) VALUES
(5, 5, 33, '2025-05-13 23:44:18');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `month` varchar(7) DEFAULT NULL,
  `paid` tinyint(1) DEFAULT 1,
  `paid_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `user_id`, `month`, `paid`, `paid_at`) VALUES
(1, 2, '2025-05', 1, '2025-05-13 22:09:56'),
(2, 34, '2025-05', 1, '2025-05-14 11:03:19');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `registered_via` enum('web','ussd') DEFAULT 'web',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `username` varchar(255) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `role`, `registered_via`, `created_at`, `username`, `name`, `phone`) VALUES
(33, 'Manishimwe Rambert', 'mramcode@gmail.com', '$2y$10$yW5rqiN1zro8YLseHTrEne4irNwR..Yq9I7baO1KPM82n1CE9J146', 'admin', 'ussd', '2025-05-13 22:47:33', NULL, '', '250789033570'),
(34, 'IGIHOZO Patience', 'igihozopatience1@gmail.com', '$2y$10$tI3pBUUiNMIjcpoDWt4ZwO4mUIfkOUWEcjiYEJ./N0nPznjjv8kLi', 'user', 'ussd', '2025-05-13 22:59:25', NULL, '', '250725050985'),
(35, 'KWIHANGANA Lullaby', 'kwihanganalullaby@gmail.com', '$2y$10$XyTggjEk3.cFfyM0viwiB.1IHt.gzCUsdM/TppKKgRBv/iHCM0OvW', 'user', 'ussd', '2025-05-14 10:52:53', NULL, '', '250725050980'),
(36, 'pax', 'mapci@gmail.com', '$2y$10$dBO1z9FcyYeX9Ph5YglvquzEVAvKBWII42EprtYIj.sFG23ChRPja', 'user', 'ussd', '2025-05-14 14:21:54', NULL, '', '250789234560');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lesson_id` (`lesson_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `lessons`
--
ALTER TABLE `lessons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `lesson_requests`
--
ALTER TABLE `lesson_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lesson_id` (`lesson_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `phone_number` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `lessons`
--
ALTER TABLE `lessons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `lesson_requests`
--
ALTER TABLE `lesson_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lessons`
--
ALTER TABLE `lessons`
  ADD CONSTRAINT `lessons_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lesson_requests`
--
ALTER TABLE `lesson_requests`
  ADD CONSTRAINT `lesson_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
