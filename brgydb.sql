
CREATE DATABASE brgydb DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE brgydb;

CREATE TABLE document_types (
  doc_id int(11) NOT NULL,
  doc_name varchar(255) NOT NULL,
  price decimal(10,2) NOT NULL,
  shipping_fee decimal(10,2) NOT NULL,
  description varchar(255) NOT NULL,
  status enum('Active','Inactive') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO document_types (doc_id, doc_name, price, shipping_fee, description, status) VALUES(1, 'Community Tax Certificate (Cedula)', 20.00, 30.00, 'Proof of tax payment and identity, often required for official transactions.', 'Active');
INSERT INTO document_types (doc_id, doc_name, price, shipping_fee, description, status) VALUES(2, 'Barangay Clearance', 20.00, 30.00, 'Certification from the barangay confirming good standing in the community.', 'Active');
INSERT INTO document_types (doc_id, doc_name, price, shipping_fee, description, status) VALUES(3, 'Certificate of Residency', 20.00, 30.00, 'Verifies that an individual is a resident of a specific barangay.', 'Active');
INSERT INTO document_types (doc_id, doc_name, price, shipping_fee, description, status) VALUES(4, 'Certificate of Indigency', 20.00, 30.00, 'Declares that a person or family belongs to the low-income or indigent sector.', 'Active');
INSERT INTO document_types (doc_id, doc_name, price, shipping_fee, description, status) VALUES(5, 'Certificate of Good Moral Character', 20.00, 30.00, 'Affirms that a person has no record of misconduct in the community.', 'Active');
INSERT INTO document_types (doc_id, doc_name, price, shipping_fee, description, status) VALUES(6, 'Certificate for Business', 20.00, 30.00, 'Confirms that a business is operating within the barangay with approval.', 'Active');
INSERT INTO document_types (doc_id, doc_name, price, shipping_fee, description, status) VALUES(7, 'Certificate of No Objection', 20.00, 30.00, 'States that the barangay has no objection to a specific request or action.', 'Inactive');
INSERT INTO document_types (doc_id, doc_name, price, shipping_fee, description, status) VALUES(8, 'Certificate of Nothing', 20.00, 30.00, 'States absolutely nothing.', 'Inactive');

CREATE TABLE doc_type_requirements (
  doc_id int(11) NOT NULL,
  req_id int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO doc_type_requirements (doc_id, req_id) VALUES(1, 1);
INSERT INTO doc_type_requirements (doc_id, req_id) VALUES(2, 1);
INSERT INTO doc_type_requirements (doc_id, req_id) VALUES(2, 2);
INSERT INTO doc_type_requirements (doc_id, req_id) VALUES(3, 1);
INSERT INTO doc_type_requirements (doc_id, req_id) VALUES(3, 2);
INSERT INTO doc_type_requirements (doc_id, req_id) VALUES(3, 3);
INSERT INTO doc_type_requirements (doc_id, req_id) VALUES(5, 1);
INSERT INTO doc_type_requirements (doc_id, req_id) VALUES(5, 2);
INSERT INTO doc_type_requirements (doc_id, req_id) VALUES(6, 7);

CREATE TABLE payments (
  payment_id int(11) NOT NULL,
  user_id int(11) NOT NULL,
  amount decimal(10,2) NOT NULL,
  payment_date datetime NOT NULL DEFAULT current_timestamp(),
  request_id int(11) DEFAULT NULL,
  transaction_type enum('Request Payment','Wallet Load','Refund') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO payments (payment_id, user_id, amount, payment_date, request_id, transaction_type) VALUES(1, 2, 60.00, '2025-12-05 19:17:55', 1, 'Request Payment');
INSERT INTO payments (payment_id, user_id, amount, payment_date, request_id, transaction_type) VALUES(2, 2, 70.00, '2025-12-05 19:21:32', 2, 'Request Payment');

CREATE TABLE requests (
  request_id int(11) NOT NULL,
  user_id int(11) NOT NULL,
  doc_id int(11) NOT NULL,
  request_date datetime NOT NULL DEFAULT current_timestamp(),
  status enum('Pending','Approved','Denied','Processing','Ready for Shipping','Shipping','Ready for Pick-up','Released') NOT NULL DEFAULT 'Pending',
  delivery_mode enum('Pick-up','Delivery') NOT NULL,
  payment_mode enum('Cash_On_Delivery','Wallet') DEFAULT NULL,
  copies int(11) NOT NULL,
  is_on_behalf tinyint(4) NOT NULL DEFAULT 0,
  represented_surname varchar(255) DEFAULT NULL,
  represented_givenname varchar(255) DEFAULT NULL,
  represented_middlename varchar(255) DEFAULT NULL,
  represented_birthdate date DEFAULT NULL,
  represented_relationship enum('relative','senior','guardian') DEFAULT NULL,
  payment_status enum('Pending','Paid','Refunded') NOT NULL DEFAULT 'Pending',
  shipping_date datetime DEFAULT NULL,
  arrival_date datetime DEFAULT NULL,
  pickup_status enum('Not Picked Up','Picked Up') NOT NULL DEFAULT 'Not Picked Up',
  pickup_date datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO requests (request_id, user_id, doc_id, request_date, status, delivery_mode, payment_mode, copies, is_on_behalf, represented_surname, represented_givenname, represented_middlename, represented_birthdate, represented_relationship, payment_status, shipping_date, arrival_date, pickup_status, pickup_date) VALUES(1, 2, 4, '2025-11-01 19:17:55', 'Processing', 'Pick-up', NULL, 3, 0, '', '', '', '0000-00-00', '', 'Pending', NULL, NULL, 'Not Picked Up', NULL);
INSERT INTO requests (request_id, user_id, doc_id, request_date, status, delivery_mode, payment_mode, copies, is_on_behalf, represented_surname, represented_givenname, represented_middlename, represented_birthdate, represented_relationship, payment_status, shipping_date, arrival_date, pickup_status, pickup_date) VALUES(2, 2, 5, '2025-12-05 19:21:32', 'Pending', 'Delivery', 'Cash_On_Delivery', 2, 0, '', '', '', '0000-00-00', '', 'Pending', NULL, NULL, 'Not Picked Up', NULL);

CREATE TABLE requirements (
  req_id int(11) NOT NULL,
  req_name varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO requirements (req_id, req_name) VALUES(1, 'Government-issued ID');
INSERT INTO requirements (req_id, req_name) VALUES(2, 'Community Tax Certificate (Cedula)');
INSERT INTO requirements (req_id, req_name) VALUES(3, 'Proof of Residency (e.g., utility bill or lease agreement)');
INSERT INTO requirements (req_id, req_name) VALUES(4, 'Letter of Authorization');
INSERT INTO requirements (req_id, req_name) VALUES(5, 'Valid ID of the person being represented');
INSERT INTO requirements (req_id, req_name) VALUES(6, 'Proof of relationship');
INSERT INTO requirements (req_id, req_name) VALUES(7, 'Proof of income');

CREATE TABLE uploaded_documents (
  upload_id int(11) NOT NULL,
  request_id int(11) NOT NULL,
  req_id int(11) NOT NULL,
  file_path varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO uploaded_documents (upload_id, request_id, req_id, file_path) VALUES(1, 2, 1, 'documents/id2.png');
INSERT INTO uploaded_documents (upload_id, request_id, req_id, file_path) VALUES(2, 2, 2, 'documents/cedula2.png');

CREATE TABLE users (
  id int(11) NOT NULL,
  givenname varchar(255) NOT NULL,
  surname varchar(255) NOT NULL,
  middlename varchar(255) NOT NULL,
  email varchar(255) NOT NULL,
  password varchar(255) NOT NULL,
  phone varchar(255) NOT NULL,
  address varchar(255) NOT NULL,
  sex enum('Female','Male') NOT NULL,
  birthdate date NOT NULL,
  is_verified tinyint(4) NOT NULL DEFAULT 0,
  type enum('admin','user') NOT NULL DEFAULT 'user',
  upload_id varchar(255) NOT NULL,
  registerdate datetime NOT NULL DEFAULT current_timestamp(),
  comment varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO users (id, givenname, surname, middlename, email, password, phone, address, sex, birthdate, is_verified, type, upload_id, registerdate, comment) VALUES(1, 'Mojo', 'Utonium', 'Jojo', 'mojo.jojo@gmail.com', '$2y$10$1zT9KKdWfLFSYjOn69X2HuZ4kEa9B29UPwEIgrLrMAx1hyupp7mBy', '09185564421', '93 Blossom Heights, Brgy. Townsville, Megaville City', 'Female', '1995-09-28', 1, 'admin', 'id1.png', '2025-12-06 01:59:47', NULL);
INSERT INTO users (id, givenname, surname, middlename, email, password, phone, address, sex, birthdate, is_verified, type, upload_id, registerdate, comment) VALUES(2, 'Fiora', 'Santiago', 'Belle', 'fiora.santiago@gmail.com', '$2y$10$YcB.B4HqMnXRR4BFZ7xn.Os20bVs5ALWdimo6nphSI26kEg/QfZpS', '09237891105', '143 Buttercup St., Brgy. Townsville, Megaville City', 'Female', '2001-07-21', 1, 'user', 'id2.png', '2025-12-06 02:05:24', NULL);
INSERT INTO users (id, givenname, surname, middlename, email, password, phone, address, sex, birthdate, is_verified, type, upload_id, registerdate, comment) VALUES(3, 'Barbie', 'Reyes', 'Benez', 'barbie@gmail.com', '$2y$10$e4yCb4lJGSvgY6w3Jl/G2OYCB95hFKPEr0kGwlxZL93fIjsWtEG3a', '09562043378', '722 Rainbow Lane, Brgy. Townsville, Megaville City', 'Female', '2001-10-21', 0, 'user', 'id3.png', '2025-12-06 02:08:25', NULL);
INSERT INTO users (id, givenname, surname, middlename, email, password, phone, address, sex, birthdate, is_verified, type, upload_id, registerdate, comment) VALUES(4, 'Blossy', 'Cruz', 'Mae', 'blossy@yahoo.com', '$2y$10$iNpaLwwKgT5M/tUWSGaz7eKGj76eZTzFo58yBe.v9x8fPbt.9B22.', '09918736642', '55 Toughleaf Drive, Brgy. Townsville, Megaville City', 'Female', '2002-12-13', 0, 'user', 'id4.png', '2025-12-06 02:11:56', NULL);
INSERT INTO users (id, givenname, surname, middlename, email, password, phone, address, sex, birthdate, is_verified, type, upload_id, registerdate, comment) VALUES(5, 'Drake', 'Utonium', 'Prof', 'prof_utonium@gmail.com', '$2y$10$TrIUOC0V97xllq3D2CB0yujy0QkKV.6ctP4PZ3xQekaZTlodqI2KC', '09159981403', '100 Chemical X Avenue, Brgy. Townsville, Megaville City', 'Male', '1975-03-17', 0, 'user', 'id5.png', '2025-12-06 02:15:04', NULL);

CREATE TABLE wallet (
  user_id int(11) NOT NULL,
  balance decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO wallet (user_id, balance) VALUES(1, 0.00);
INSERT INTO wallet (user_id, balance) VALUES(2, 100.00);
INSERT INTO wallet (user_id, balance) VALUES(3, 20.00);
INSERT INTO wallet (user_id, balance) VALUES(4, 120.00);
INSERT INTO wallet (user_id, balance) VALUES(5, 50.00);

CREATE VIEW admin_shipments  AS SELECT r.request_id AS `request_id`, u.surname AS `surname`, u.givenname AS `givenname`, u.middlename AS `middlename`, u.phone AS `phone`, u.email AS `email`, u.address AS `address`, r.`status` AS `status`, r.request_date AS `request_date`, r.shipping_date AS `shipping_date`, r.arrival_date AS `arrival_date`, r.payment_mode AS `payment_mode`, r.payment_status AS `payment_status`, dt.doc_name AS `doc_name`, r.copies AS `copies`, dt.price AS `price`, dt.shipping_fee AS `shipping_fee` FROM ((requests r left join users u on(u.`id` = r.user_id)) left join document_types dt on(dt.doc_id = r.doc_id)) ;

CREATE VIEW dashboard_documents  AS SELECT document_types.doc_id AS `doc_id`, document_types.doc_name AS `doc_name`, document_types.description AS `description` FROM document_types WHERE document_types.`status` = 'Active' ;

CREATE VIEW document_requirements  AS SELECT r.req_name AS `req_name`, dtr.doc_id AS `doc_id` FROM (requirements r join doc_type_requirements dtr on(r.req_id = dtr.req_id)) ;


CREATE VIEW manage_documents  AS SELECT dt.doc_id AS `doc_id`, dt.doc_name AS `doc_name`, dt.description AS `description`, dt.price AS `price`, dt.shipping_fee AS `shipping_fee`, dt.`status` AS `status` FROM document_types AS `dt` ;

CREATE VIEW manage_shipments  AS SELECT r.request_id AS `request_id`, u.surname AS `surname`, u.givenname AS `givenname`, u.middlename AS `middlename`, r.`status` AS `status`, r.request_date AS `request_date`, r.shipping_date AS `shipping_date`, r.arrival_date AS `arrival_date` FROM (requests r left join users u on(u.`id` = r.user_id)) WHERE r.delivery_mode = 'Delivery' AND (r.`status` = 'Ready for Shipping' OR r.`status` = 'Shipping' OR r.`status` = 'Released' );


CREATE VIEW view_users  AS SELECT users.`id` AS `id`, users.givenname AS `givenname`, users.surname AS `surname`, users.middlename AS `middlename`, users.email AS `email`, users.phone AS `phone`, users.is_verified AS `is_verified`, users.`type` AS `type` FROM users ORDER BY users.`id` ASC ;


ALTER TABLE document_types
  ADD PRIMARY KEY (doc_id);

ALTER TABLE doc_type_requirements
  ADD KEY dtr_fk_doc (doc_id),
  ADD KEY dtr_fk_req (req_id);

ALTER TABLE payments
  ADD PRIMARY KEY (payment_id),
  ADD KEY payments_fk_user (user_id),
  ADD KEY payments_fk_request (request_id);

ALTER TABLE requests
  ADD PRIMARY KEY (request_id),
  ADD KEY requests_fk_user (user_id),
  ADD KEY requests_fk_docu (doc_id);

ALTER TABLE requirements
  ADD PRIMARY KEY (req_id);

ALTER TABLE uploaded_documents
  ADD PRIMARY KEY (upload_id),
  ADD KEY ud_fk_requests (request_id),
  ADD KEY ud_fk_requirements (req_id);

ALTER TABLE users
  ADD PRIMARY KEY (id);

ALTER TABLE wallet
  ADD PRIMARY KEY (user_id);


ALTER TABLE document_types
  MODIFY doc_id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

ALTER TABLE payments
  MODIFY payment_id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE requests
  MODIFY request_id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE requirements
  MODIFY req_id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

ALTER TABLE uploaded_documents
  MODIFY upload_id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE users
  MODIFY id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;


ALTER TABLE doc_type_requirements
  ADD CONSTRAINT dtr_fk_doc FOREIGN KEY (doc_id) REFERENCES document_types (doc_id),
  ADD CONSTRAINT dtr_fk_req FOREIGN KEY (req_id) REFERENCES requirements (req_id);

ALTER TABLE payments
  ADD CONSTRAINT payments_fk_request FOREIGN KEY (request_id) REFERENCES requests (request_id),
  ADD CONSTRAINT payments_fk_user FOREIGN KEY (user_id) REFERENCES `users` (id);

ALTER TABLE requests
  ADD CONSTRAINT requests_fk_docu FOREIGN KEY (doc_id) REFERENCES document_types (doc_id),
  ADD CONSTRAINT requests_fk_user FOREIGN KEY (user_id) REFERENCES `users` (id);

ALTER TABLE uploaded_documents
  ADD CONSTRAINT ud_fk_requests FOREIGN KEY (request_id) REFERENCES requests (request_id),
  ADD CONSTRAINT ud_fk_requirements FOREIGN KEY (req_id) REFERENCES requirements (req_id);

ALTER TABLE wallet
  ADD CONSTRAINT wallet_fk_user FOREIGN KEY (user_id) REFERENCES `users` (id);

