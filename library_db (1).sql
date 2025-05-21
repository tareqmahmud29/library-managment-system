-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 02, 2025 at 11:25 AM
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
-- Database: `library_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `book_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) DEFAULT NULL,
  `publisher` varchar(255) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`book_id`, `title`, `author`, `publisher`, `year`, `is_available`) VALUES
(2, '1984', 'George Orwell', 'Secker & Warburg', 1949, 1),
(7, 'ফেলুদা সমগ্র ১', 'Satyajit Ray', 'tareq', 2020, 0),
(8, 'সেই সময়	সেই সময়', 'Sunil Gangopadhyay', 'tareq', 2004, 0),
(9, 'শঙ্কু সমগ্র', 'Satyajit Ray ', 'tareq', 2004, 1),
(11, 'দীপু নাম্বার টু', 'Muhammed Zafar Iqbal', 'tareq', 2004, 0),
(12, 'হাজার বছর ধরে', 'Zahir Raihan', 'tareq', 2004, 0),
(13, 'শঙ্খনীল কারাগার', 'Humayun Ahmed', 'tareq', 2004, 0),
(14, '	আমি তপু', 'Muhammed Zafar Iqbal', 'tareq', 2004, 0),
(15, 'দেশে বিদেশে', ' Syed Mujtaba Ali', 'tareq', 2004, 0),
(16, 'পুতুলনাচের ইতিকথা', 'Manik Bandopadhyay', 'tareq', 2004, 0),
(18, 'shokal hobe ', 'mahmud', 'TM ', 2001, 0),
(19, 'bangladesh', 'baglali', 'mahmud', 2011, 1);

-- --------------------------------------------------------

--
-- Table structure for table `borrowers`
--

CREATE TABLE `borrowers` (
  `borrower_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `contact_info` varchar(255) DEFAULT NULL,
  `book_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrowers`
--

INSERT INTO `borrowers` (`borrower_id`, `name`, `address`, `contact_info`, `book_name`) VALUES
(1, 'John Doe', '123 Main St', 'tareq@gamil.com', 'ফেলুদা সমগ্র ১'),
(2, 'Jane Smith', '456 Elm St', 'jane@example.com', NULL),
(3, 'rimon hasan ', 'raipur, laxmipur ', '01760145026', NULL),
(4, 'rimon hasan ', 'raipur, laxmipur ', '0188544854', NULL),
(5, 'tareq mahmud ', 'sadfsadfsdf', '045423154', NULL),
(6, 'rakib islam ', 'borisal ', '015557217', 'ফেলুদা সমগ্র ১');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `transaction_id` int(11) NOT NULL,
  `borrower_id` int(11) DEFAULT NULL,
  `book_id` int(11) DEFAULT NULL,
  `checkout_date` date DEFAULT NULL,
  `return_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`transaction_id`, `borrower_id`, `book_id`, `checkout_date`, `return_date`) VALUES
(10, 1, 11, '2025-02-02', NULL),
(11, 1, 12, '2025-02-02', NULL),
(12, 1, 13, '2025-02-02', NULL),
(13, 1, 14, '2025-02-02', NULL),
(14, 1, 15, '2025-02-02', NULL),
(15, 1, 16, '2025-02-02', NULL),
(16, 1, 18, '2025-02-02', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `contact_info` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `address`, `contact_info`) VALUES
(1, 'tareq', 'tareqpc29@gmail.com', '$2y$10$df2tujIbyddBOsaVacqbMuPvYdFtv/y951x9SgAqv0qc6B42FT4Zy', 'mangrove hostel-2 , khalishpur , khulna', '018895203132'),
(3, 'tareq use pc', 'hamza@gmail.com', '$2y$10$BBV/5Z2GkSsW2aGLeKMDouQIitmGZsDCaYv2r92AYE7BnViUuMzWa', 'Khulna', '01889520313');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`book_id`);

--
-- Indexes for table `borrowers`
--
ALTER TABLE `borrowers`
  ADD PRIMARY KEY (`borrower_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `borrower_id` (`borrower_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `book_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `borrowers`
--
ALTER TABLE `borrowers`
  MODIFY `borrower_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`borrower_id`) REFERENCES `borrowers` (`borrower_id`),
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
