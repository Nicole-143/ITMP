-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 17, 2025 at 05:47 PM
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
-- Database: `brgydb`
--

-- --------------------------------------------------------

--
-- Table structure for table `document_types`
--

CREATE TABLE `document_types` (
  `doc_id` int(12) NOT NULL,
  `doc_name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `document_types`
--

INSERT INTO `document_types` (`doc_id`, `doc_name`, `price`, `description`) VALUES
(1, 'Community Tax Certificate (Cedula)', 20.00, 'Proof of tax payment and identity, often required for official transactions.'),
(2, 'Barangay Clearance', 20.00, 'Certification from the barangay confirming good standing in the community.'),
(3, 'Certificate of Residency', 20.00, 'Verifies that an individual is a resident of a specific barangay.'),
(4, 'Certificate of Indigency', 20.00, 'Declares that a person or family belongs to the low-income or indigent sector.'),
(5, 'Certificate of Good Moral Character', 20.00, 'Affirms that a person has no record of misconduct in the community.'),
(6, 'Certificate for Business', 20.00, 'Confirms that a business is operating within the barangay with approval.'),
(7, 'Certificate of No Objection', 20.00, 'States that the barangay has no objection to a specific request or action.');

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

CREATE TABLE `requests` (
  `request_id` int(12) NOT NULL,
  `user_id` int(12) NOT NULL,
  `doc_id` int(12) NOT NULL,
  `status` enum('pending','approved','denied','cancelled') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `requirements`
--

CREATE TABLE `requirements` (
  `req_id` int(12) NOT NULL,
  `req_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `requirements`
--

INSERT INTO `requirements` (`req_id`, `req_name`) VALUES
(1, 'Government-issued ID'),
(2, 'Community Tax Certificate (Cedula)'),
(3, 'Proof of Residency (e.g., utility bill or lease agreement)'),
(4, 'Letter of Authorization'),
(5, 'Valid ID of the person being represented'),
(6, 'Proof of relationship');

-- --------------------------------------------------------

--
-- Table structure for table `uploaded_files`
--

CREATE TABLE `uploaded_files` (
  `file_id` int(12) NOT NULL,
  `user_id` int(12) NOT NULL,
  `request_id` int(12) DEFAULT NULL,
  `filename` varchar(255) NOT NULL,
  `file_type` enum('verification','id_photo','proof_of_residency') NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(12) NOT NULL,
  `givenname` varchar(255) NOT NULL,
  `surname` varchar(255) NOT NULL,
  `middlename` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `sex` enum('Female','Male','Other') NOT NULL,
  `birthdate` date NOT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `type` enum('admin','user') NOT NULL DEFAULT 'user',
  `upload_id` varchar(255) NOT NULL,
  `registerdate` date NOT NULL DEFAULT current_timestamp(),
  `comment` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `document_types`
--
ALTER TABLE `document_types`
  ADD PRIMARY KEY (`doc_id`);

--
-- Indexes for table `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `fk_requests_user` (`user_id`),
  ADD KEY `fk_requests_document` (`doc_id`);

--
-- Indexes for table `uploaded_files`
--
ALTER TABLE `uploaded_files`
  ADD PRIMARY KEY (`file_id`),
  ADD KEY `fk_uploaded_files_user_id` (`user_id`),
  ADD KEY `fk_uploaded_files_requests` (`request_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `document_types`
--
ALTER TABLE `document_types`
  MODIFY `doc_id` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `requests`
--
ALTER TABLE `requests`
  MODIFY `request_id` int(12) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(12) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `requests`
--
ALTER TABLE `requests`
  ADD CONSTRAINT `fk_requests_document` FOREIGN KEY (`doc_id`) REFERENCES `document_types` (`doc_id`),
  ADD CONSTRAINT `fk_requests_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `uploaded_files`
--
ALTER TABLE `uploaded_files`
  ADD CONSTRAINT `fk_uploaded_files_requests` FOREIGN KEY (`request_id`) REFERENCES `requests` (`request_id`),
  ADD CONSTRAINT `fk_uploaded_files_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_uploaded_files_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
