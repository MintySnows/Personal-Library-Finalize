-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 10, 2025 at 10:25 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `student_enrollment`
--

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `course_code` varchar(20) NOT NULL,
  `course_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `department` enum('BSIT','BSIS') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `course_code`, `course_name`, `description`, `department`, `created_at`) VALUES
(1, 'IT101', 'Introduction to Programming', 'Basic programming concepts', 'BSIT', '2025-06-09 03:53:25'),
(2, 'IT102', 'Web Development', 'HTML, CSS, JavaScript fundamentals', 'BSIT', '2025-06-09 03:53:25'),
(3, 'IS101', 'Information Systems Fundamentals', 'Introduction to IS concepts', 'BSIS', '2025-06-09 03:53:25'),
(4, 'IS102', 'Database Management', 'Relational database concepts', 'BSIS', '2025-06-09 03:53:25');

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `enrollment_date` date NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`id`, `student_id`, `course_id`, `enrollment_date`, `status`, `created_at`) VALUES
(1, 2, 1, '2025-06-09', 'pending', '2025-06-09 05:57:47'),
(2, 2, 2, '2025-06-09', 'pending', '2025-06-09 05:57:54'),
(4, 4, 1, '2025-06-09', 'approved', '2025-06-09 11:28:46'),
(6, 4, 2, '2025-06-10', 'approved', '2025-06-10 01:53:15'),
(7, 4, 4, '2025-06-10', 'approved', '2025-06-10 08:10:21');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `student_id` varchar(20) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `year_level` enum('1st Year','2nd Year','3rd Year','4th Year','5th Year') DEFAULT NULL,
  `active` tinyint(1) DEFAULT 1,
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `user_id`, `student_id`, `first_name`, `last_name`, `email`, `phone`, `address`, `created_at`, `date_of_birth`, `gender`, `year_level`, `active`, `profile_picture`) VALUES
(1, 2, '109793100082', 'Marc Anghelo', 'Cordero', 'marcanghelo315@gmail.com', '09273939876', 'kwjfkqn;egkqe', '2025-06-09 04:50:28', '2025-06-02', 'Male', '1st Year', 1, NULL),
(2, 3, '15468432', 'delw', 'derder', 'ghelo@gmail.com', '65465321564', 'dJHKDJGAFDKJFFDA', '2025-06-09 05:56:45', '2025-06-03', 'Male', '1st Year', 1, NULL),
(3, 4, '97971097', 'ponchi', 'pochi', 'pochipochi@gmail.com', '4767426742675', 'bikinibottom', '2025-06-09 06:02:26', '2025-06-04', 'Male', '1st Year', 1, NULL),
(4, 5, '25291820', 'ana', 'delos santos', 'anamariedlssnts@gmail.com', '09094510667', 'sa tabi ng puno', '2025-06-09 11:28:15', '2005-06-29', 'Female', '2nd Year', 1, 'uploads/profile_pictures/profile_6847e8bee7a65.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('admin','registrar','student') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `active` tinyint(1) DEFAULT 1,
  `last_login` datetime DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `role`, `created_at`, `active`, `last_login`, `first_name`, `last_name`, `phone`, `address`, `date_of_birth`, `gender`, `profile_picture`) VALUES
(1, 'admin', 'ghelo31', 'marcanghelo@gmail.com', 'admin', '2025-06-09 03:53:25', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'anghelo', '109793100092', 'marcanghelo315@gmail.com', 'student', '2025-06-09 04:50:28', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'anghelo45', '$2y$10$bk/iBmp68yvbXdFcWNB6jOVq8wWoOPrg7z6DYop4D/B8n.SilYgo2', 'ghelo@gmail.com', 'admin', '2025-06-09 05:56:45', 1, NULL, 'Marc', 'Anghelo', '09308355507', 'San gregorio purok 3', '2025-06-03', 'Male', 'uploads/admin_profile_pictures/admin_profile_6847ea2e9b0d0.jpg'),
(4, 'pochi', '$2y$10$dBw2Z3qMAdsWv8.WtBXCzes1QzNw/5L3pJTtn0.4/T1Op2fXhNHuK', 'pochipochi@gmail.com', 'registrar', '2025-06-09 06:02:26', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 'ana', '$2y$10$AaghbpNqwqBN97q/jtGfIupfdJ8OWwRNBda73V5uSMiW/o9nNR2m2', 'anamariedlssnts@gmail.com', 'student', '2025-06-09 11:28:15', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `course_code` (`course_code`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_enrollment` (`student_id`,`course_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
  ADD CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`);

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
