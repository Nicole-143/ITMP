-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 07, 2025 at 04:17 PM
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
  `valid-id` varchar(255) NOT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `type` enum('admin','user') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(12) NOT NULL AUTO_INCREMENT;
COMMIT;

--
-- Table structure for table `document_types` added by Khloe Nov 8
--

CREATE TABLE `document_types` (
  `doc_id` INT(12) NOT NULL,
  `doc_name` VARCHAR(255) NOT NULL,
  `price` DECIMAL(10, 2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `document_types` added by Khloe Nov 8
--

INSERT INTO `document_types` (`doc_id`, `doc_name`, `price`) VALUES
(1, 'Community Tax Certificate (Cedula)', 20.00),
(2, 'Barangay Clearance', 20.00),
(3, 'Certificate of Residency', 20.00),
(4, 'Certificate of Indigency', 20.00),
(5, 'Certificate of Good Moral Character', 20.00),
(6, 'Certificate for Business', 20.00),
(7, 'Certificate of No Objection', 20.00);


--
-- Table structure for table `requirements` added by Khloe Nov 8
--

CREATE TABLE `requirements` (
  `req_id` INT(12) NOT NULL,
  `req_name` VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `requirements` added by Khloe Nov 8
--

INSERT INTO `requirements` (`req_id`, `req_name`) VALUES
(1, 'Government-issued ID'),
(2, 'Community Tax Certificate (Cedula)'),
(3, 'Proof of Residency (e.g., utility bill or lease agreement)'),
(4, 'Letter of Authorization'),
(5, 'Valid ID of the person being represented'),
(6, 'Proof of relationship');


--
-- Table structure for table `doc_type_requirements` added by Khloe Nov 8
--

CREATE TABLE `doc_type_requirements` (
  `doc_id` INT(12) NOT NULL,
  `req_id` INT(12) NOT NULL,
  PRIMARY KEY (`doc_id`, `req_id`),
  FOREIGN KEY (`doc_id`) REFERENCES `document_types`(`doc_id`),
  FOREIGN KEY (`req_id`) REFERENCES `requirements`(`req_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doc_type_requirements` added by Khloe Nov 8
--

INSERT INTO `doc_type_requirements` (`doc_id`, `req_id`) VALUES
(1, 1),
(2, 1),
(2, 2),
(3, 1),
(3, 2),
(3, 3),
(5, 1),
(5, 2);


--
-- Table structure for table `requests` added by Khloe Nov 8
--

CREATE TABLE `requests` (
  `request_id` INT(12) NOT NULL,
  `user_id` INT(12) NOT NULL,
  `doc_id` INT(12) NOT NULL,
  `request_date` DATETIME NOT NULL DEFAULT current_timestamp(),
  `status` ENUM('Pending', 'Approved', 'Denied', 'Processing', 'Shipping', 'Ready for Pick-up', 'Released') NOT NULL DEFAULT 'Pending',
  `delivery_mode` ENUM('Pick-up', 'Delivery') NOT NULL,
  `shipping_fee` DECIMAL(10, 2) DEFAULT 0.00,
  `is_on_behalf` TINYINT(1) NOT NULL DEFAULT 0,
  `payment_status` ENUM('Pending', 'Paid', 'Refunded') NOT NULL DEFAULT 'Pending',
  PRIMARY KEY (`request_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`),
  FOREIGN KEY (`doc_id`) REFERENCES `document_types`(`doc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


--
-- Table structure for table `uploaded_documents` added by Khloe Nov 8
--

CREATE TABLE `uploaded_documents` (
  `upload_id` INT(12) NOT NULL,
  `request_id` INT(12) NOT NULL,
  `req_id` INT(12) NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`upload_id`),
  FOREIGN KEY (`request_id`) REFERENCES `requests`(`request_id`),
  FOREIGN KEY (`req_id`) REFERENCES `requirements`(`req_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


--
-- Table structure for table `wallet` added by Khloe Nov 8
--

CREATE TABLE `wallet` (
  `user_id` INT(12) NOT NULL,
  `balance` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`user_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `payments` added by Khloe Nov 8
--
-- will track successful check-out transactions
CREATE TABLE `payments` (
  `payment_id` INT(12) NOT NULL,
  `user_id` INT(12) NOT NULL,
  `amount` DECIMAL(10, 2) NOT NULL,
  `payment_date` DATETIME NOT NULL DEFAULT current_timestamp(),
  `request_id` INT(12) DEFAULT NULL,
  `transaction_type` ENUM('Request Payment', 'Wallet Load', 'Refund') NOT NULL,
  PRIMARY KEY (`payment_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`),
  FOREIGN KEY (`request_id`) REFERENCES `requests`(`request_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for new tables added by Khloe Nov 8
--

ALTER TABLE `document_types`
  ADD PRIMARY KEY (`doc_id`);

ALTER TABLE `requirements`
  ADD PRIMARY KEY (`req_id`);

ALTER TABLE `requests`
  MODIFY `request_id` INT(12) NOT NULL AUTO_INCREMENT;

ALTER TABLE `uploaded_documents`
  MODIFY `upload_id` INT(12) NOT NULL AUTO_INCREMENT;

ALTER TABLE `payments`
  MODIFY `payment_id` INT(12) NOT NULL AUTO_INCREMENT;

--
-- Auto Increment for new tables added by Khloe Nov 8
--

ALTER TABLE `document_types`
  MODIFY `doc_id` INT(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

ALTER TABLE `requirements`
  MODIFY `req_id` INT(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
