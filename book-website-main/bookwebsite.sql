-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 14, 2025 at 02:07 PM
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
-- Database: `bookwebsite`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `cover` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `available` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `title`, `author`, `cover`, `description`, `available`) VALUES
(1, 'Fortress Blood', 'L.D Goffigan', 'crime-and-mystery-cover-scaled-1.jpeg', 'A thrilling fantasy adventure.', 1),
(2, 'The Fortress of Shadow', 'L.D Goffigan', 'OIP.jpg', 'A tale of darkness and courage.', 1),
(3, 'Shade of Fae', 'J.L Myers', 'BWISIT.jpg', 'A fae fantasy full of intrigue.', 1),
(4, 'Forged in Blood', 'Lindsay Buroker', 'forged_blood.jpg', 'Epic battles and magic.', 1),
(5, 'Defying Roger', 'Sarah Edwards', 'historical-romance-book-cover-design-ebook-kindle-amazon-sarah-edwards-defying-roger_orig.jpg', 'A historical romance.', 1);

-- --------------------------------------------------------

--
-- Table structure for table `continue_reading`
--

CREATE TABLE `continue_reading` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `last_accessed` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `most_read`
--

CREATE TABLE `most_read` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `read_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `usertag` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `role`) VALUES
(1, 'glenn_berino@yahoo.com', '$2y$10$A9BrjyMVRCOZ0jqJ5Hpndutv/LurrrUxu79PCMASEPiDxeHtLsd6C', 'user'),
(2, 'glenn@gmail.com', '$2y$10$2BP3lD.15ZDRTlMCE.cb4eujhZWVUTOyIrJRMsA/nB0m1lmI/f6Q2', 'user'),
(3, 'mint@gmail.com', '$2y$10$5D7DF6yVVhPQmPDng.XqBe2NbQU4GwJfKJ3HSeGwK1O1FOspxVAEm', 'user'),
(4, 'leahmarieperina7@gmail.com', '$2y$10$EwruBQ1yILtGsjytjNFJOux2gFjqlRyZSYv65tdNBbqoVmaAsiS8C', 'user'),
(5, 'floyd05@gmail.com', '$2y$10$Kktv3NXntCUEQK.7Wh9IeuuoZIIKLf9Le.dwq2.SUoiqwYlg4Nz6y', 'user'),
(6, '123456@gmail.com', '$2y$10$lW/XHS7Qoga1DFQf8w2trOO4XRKH03ayv3y4Bl2RdTlCiJhDQMNlu', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `continue_reading`
--
ALTER TABLE `continue_reading`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_continue` (`user_id`,`book_id`);

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_favorite` (`user_id`,`book_id`);

--
-- Indexes for table `most_read`
--
ALTER TABLE `most_read`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `continue_reading`
--
ALTER TABLE `continue_reading`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `most_read`
--
ALTER TABLE `most_read`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
