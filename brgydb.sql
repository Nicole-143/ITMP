SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `document_types` (
  `doc_id` int(12) NOT NULL,
  `doc_name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `document_types` (`doc_id`, `doc_name`, `price`, `description`) VALUES
(1, 'Community Tax Certificate (Cedula)', 20.00, 'Proof of tax payment and identity, often required for official transactions.'),
(2, 'Barangay Clearance', 20.00, 'Certification from the barangay confirming good standing in the community.'),
(3, 'Certificate of Residency', 20.00, 'Verifies that an individual is a resident of a specific barangay.'),
(4, 'Certificate of Indigency', 20.00, 'Declares that a person or family belongs to the low-income or indigent sector.'),
(5, 'Certificate of Good Moral Character', 20.00, 'Affirms that a person has no record of misconduct in the community.'),
(6, 'Certificate for Business', 20.00, 'Confirms that a business is operating within the barangay with approval.'),
(7, 'Certificate of No Objection', 20.00, 'States that the barangay has no objection to a specific request or action.');

CREATE TABLE `doc_type_requirements` (
  `doc_id` int(11) NOT NULL,
  `req_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `doc_type_requirements` (`doc_id`, `req_id`) VALUES
(1, 1),
(2, 1),
(2, 2),
(3, 1),
(3, 2),
(3, 3),
(5, 1),
(5, 2),
(6, 7);

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` datetime NOT NULL DEFAULT current_timestamp(),
  `request_id` int(11) DEFAULT NULL,
  `transaction_type` enum('Request Payment','Wallet Load','Refund') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `requests` (
  `request_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `doc_id` int(11) NOT NULL,
  `request_date` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('Pending','Approved','Denied','Processing','Shipping','Ready for Pick-up','Released') NOT NULL DEFAULT 'Pending',
  `delivery_mode` enum('Pick-up','Delivery') NOT NULL,
  `shipping_fee` decimal(10,2) DEFAULT 0.00,
  `is_on_behalf` tinyint(4) NOT NULL DEFAULT 0,
  `payment_status` enum('Pending','Paid','Refunded') NOT NULL DEFAULT 'Pending',
  `shipping_date` datetime DEFAULT NULL,
  `arrival_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `requirements` (
  `req_id` int(12) NOT NULL,
  `req_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `requirements` (`req_id`, `req_name`) VALUES
(1, 'Government-issued ID'),
(2, 'Community Tax Certificate (Cedula)'),
(3, 'Proof of Residency (e.g., utility bill or lease agreement)'),
(4, 'Letter of Authorization'),
(5, 'Valid ID of the person being represented'),
(6, 'Proof of relationship'),
(7, 'Proof of income');

CREATE TABLE `uploaded_documents` (
  `upload_id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `req_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

CREATE TABLE `wallet` (
  `user_id` int(11) NOT NULL,
  `balance` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


ALTER TABLE `document_types`
  ADD PRIMARY KEY (`doc_id`);

ALTER TABLE `doc_type_requirements`
  ADD KEY `dtr_fk_doc` (`doc_id`),
  ADD KEY `dtr_fk_req` (`req_id`);

ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `payments_fk_user` (`user_id`),
  ADD KEY `payments_fk_request` (`request_id`);

ALTER TABLE `requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `requests_fk_user` (`user_id`),
  ADD KEY `requests_fk_docu` (`doc_id`);

ALTER TABLE `requirements`
  ADD PRIMARY KEY (`req_id`);

ALTER TABLE `uploaded_documents`
  ADD PRIMARY KEY (`upload_id`),
  ADD KEY `ud_fk_requests` (`request_id`),
  ADD KEY `ud_fk_requirements` (`req_id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `wallet`
  ADD PRIMARY KEY (`user_id`);


ALTER TABLE `document_types`
  MODIFY `doc_id` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `requirements`
  MODIFY `req_id` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

ALTER TABLE `uploaded_documents`
  MODIFY `upload_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `users`
  MODIFY `id` int(12) NOT NULL AUTO_INCREMENT;


ALTER TABLE `doc_type_requirements`
  ADD CONSTRAINT `dtr_fk_doc` FOREIGN KEY (`doc_id`) REFERENCES `document_types` (`doc_id`),
  ADD CONSTRAINT `dtr_fk_req` FOREIGN KEY (`req_id`) REFERENCES `requirements` (`req_id`);

ALTER TABLE `payments`
  ADD CONSTRAINT `payments_fk_request` FOREIGN KEY (`request_id`) REFERENCES `requests` (`request_id`),
  ADD CONSTRAINT `payments_fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `requests`
  ADD CONSTRAINT `requests_fk_docu` FOREIGN KEY (`doc_id`) REFERENCES `document_types` (`doc_id`),
  ADD CONSTRAINT `requests_fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `uploaded_documents`
  ADD CONSTRAINT `ud_fk_requests` FOREIGN KEY (`request_id`) REFERENCES `requests` (`request_id`),
  ADD CONSTRAINT `ud_fk_requirements` FOREIGN KEY (`req_id`) REFERENCES `requirements` (`req_id`);

ALTER TABLE `wallet`
  ADD CONSTRAINT `wallet_fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
