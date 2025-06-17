-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 17, 2025 at 02:24 PM
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
(5, 'Defying Roger', 'Sarah Edwards', 'historical-romance-book-cover-design-ebook-kindle-amazon-sarah-edwards-defying-roger_orig.jpg', 'A historical romance.', 1),
(6, 'Solo Levelling', 'Sung-Lak Jang', 'solo levelling.jpg', 'Status : Ongoing\r\n\r\nGenres : Action , Adventure , Fantasy , Shounen , Webtoons\r\n\r\nI am the only the one who levels up, I level up alone, Na Honjaman Lebel-eob, Only I Level Up, Ore Dake Level Up na Ken, Поднятие уровня в одиночку, 나 혼자만 레벨업, 俺だけレベルアップな件, 我独自升级', 1);

-- --------------------------------------------------------

--
-- Table structure for table `chapters`
--

CREATE TABLE `chapters` (
  `id` int(11) NOT NULL,
  `book_id` int(11) DEFAULT NULL,
  `chapter_number` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `image` int(11) NOT NULL,
  `chapter_type` enum('text','image') NOT NULL DEFAULT 'text'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chapters`
--

INSERT INTO `chapters` (`id`, `book_id`, `chapter_number`, `title`, `content`, `image`, `chapter_type`) VALUES
(2, 1, 1, 'car led', 'ipt ipt ah ah', 0, 'text'),
(9, 6, 1, 'chapter 0 PROLOGUE', 'uploads/chapters/6850b1e4e91c4.jpg\nuploads/chapters/6850b1e4e953a.jpg\nuploads/chapters/6850b1e4e97d7.jpg\nuploads/chapters/6850b1e4e9a42.jpg\nuploads/chapters/6850b1e4e9d0d.jpg\nuploads/chapters/6850b1e4e9fed.jpg\nuploads/chapters/6850b1e4ea261.jpg\nuploads/chapters/6850b1e4ea4b0.jpg', 0, 'image'),
(10, 5, 1, 'chapter 0 PROLOGUE', ';sdagha;ioghdfbl;dahb;;oihg\'f\'pbj]pojbadfopd\'kbjadfopfjabpdfjob\'dkjajabjkbjdfklj\'lkjb\'sdfhlknhknhldfjbijdfpojphj\'skhjfkljb\'afkdjbs;fkjbkls;jnlksjnklj', 0, 'text');

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
-- Table structure for table `recently_read`
--

CREATE TABLE `recently_read` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `read_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recently_read`
--

INSERT INTO `recently_read` (`id`, `user_id`, `book_id`, `read_at`) VALUES
(7, 6, 3, '2025-06-16 15:36:07'),
(12, 6, 1, '2025-06-16 16:20:40'),
(36, 6, 5, '2025-06-17 09:37:21'),
(37, 6, 6, '2025-06-17 09:38:01');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `usertag` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `display_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `username` varchar(255) DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT 'default_profile.jpg',
  `display_name` varchar(100) DEFAULT NULL,
  `bio` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `role`, `username`, `profile_pic`, `display_name`, `bio`) VALUES
(1, 'glenn_berino@yahoo.com', '$2y$10$A9BrjyMVRCOZ0jqJ5Hpndutv/LurrrUxu79PCMASEPiDxeHtLsd6C', 'user', NULL, 'default_profile.jpg', NULL, NULL),
(2, 'glenn@gmail.com', '$2y$10$2BP3lD.15ZDRTlMCE.cb4eujhZWVUTOyIrJRMsA/nB0m1lmI/f6Q2', 'user', NULL, 'default_profile.jpg', NULL, NULL),
(3, 'mint@gmail.com', '$2y$10$5D7DF6yVVhPQmPDng.XqBe2NbQU4GwJfKJ3HSeGwK1O1FOspxVAEm', 'user', NULL, 'default_profile.jpg', NULL, NULL),
(4, 'leahmarieperina7@gmail.com', '$2y$10$EwruBQ1yILtGsjytjNFJOux2gFjqlRyZSYv65tdNBbqoVmaAsiS8C', 'user', NULL, 'default_profile.jpg', NULL, NULL),
(5, 'floyd05@gmail.com', '$2y$10$Kktv3NXntCUEQK.7Wh9IeuuoZIIKLf9Le.dwq2.SUoiqwYlg4Nz6y', 'user', NULL, 'default_profile.jpg', NULL, NULL),
(6, '123456@gmail.com', '$2y$10$USUIEPSWrCZFT.k/xpMMye0IHUgHz0PDd2v.ezoNSZeSTiyI2IKd2', 'admin', 'MANALO MATALO', 'uploads/profile_pics/user_6_1750154532.jpg', 'MATALO', 'LUTANG PALAGI');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chapters`
--
ALTER TABLE `chapters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `book_id` (`book_id`);

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
-- Indexes for table `recently_read`
--
ALTER TABLE `recently_read`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `chapters`
--
ALTER TABLE `chapters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

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
-- AUTO_INCREMENT for table `recently_read`
--
ALTER TABLE `recently_read`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

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

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chapters`
--
ALTER TABLE `chapters`
  ADD CONSTRAINT `chapters_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`);

--
-- Constraints for table `recently_read`
--
ALTER TABLE `recently_read`
  ADD CONSTRAINT `recently_read_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `recently_read_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
