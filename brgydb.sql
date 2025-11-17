CREATE DATABASE brgydb;
USE brgydb;

CREATE TABLE Users(
	id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    givenname VARCHAR(255) NOT NULL,
    surname VARCHAR(255) NOT NULL,
    middlename VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(255) NOT NULL,
    address VARCHAR(255) NOT NULL,
    sex ENUM('Female', 'Male') NOT NULL,
    birthdate DATE NOT NULL,
    valid_id VARCHAR(255) NOT NULL,
    is_verified TINYINT NOT NULL DEFAULT 0,
    type ENUM('admin', 'user') NOT NULL DEFAULT 'user'
);

CREATE TABLE Document_Types(
	doc_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    doc_name VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL
) AUTO_INCREMENT = 8;

INSERT INTO Document_Types (doc_id, doc_name, price) 
VALUES
(1, 'Community Tax Certificate (Cedula)', 20.00),
(2, 'Barangay Clearance', 20.00),
(3, 'Certificate of Residency', 20.00),
(4, 'Certificate of Indigency', 20.00),
(5, 'Certificate of Good Moral Character', 20.00),
(6, 'Certificate for Business', 20.00),
(7, 'Certificate of No Objection', 20.00);

CREATE TABLE Requirements(
	req_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    req_name VARCHAR(255) NOT NULL
) AUTO_INCREMENT = 8;

INSERT INTO Requirements (req_id, req_name) 
VALUES
(1, 'Government-issued ID'),
(2, 'Community Tax Certificate (Cedula)'),
(3, 'Proof of Residency (e.g., utility bill or lease agreement)'),
(4, 'Letter of Authorization'),
(5, 'Valid ID of the person being represented'),
(6, 'Proof of relationship'),
(7, 'Proof of income');

CREATE TABLE Doc_Type_Requirements (
	doc_id INT NOT NULL,
	req_id INT NOT NULL,
    CONSTRAINT dtr_fk_doc FOREIGN KEY (doc_id) REFERENCES document_types(doc_id),
	CONSTRAINT dtr_fk_req FOREIGN KEY (req_id) REFERENCES requirements(req_id)
);

INSERT INTO Doc_Type_Requirements (doc_id, req_id) 
VALUES
(1, 1), -- Cedula to Government-issued ID --
(2, 1), -- Brgy Clearance to Government-issued ID --
(2, 2), -- Brgy Clearance to Cedula --
(3, 1), -- Certificate of Residency to Government-issued ID --
(3, 2), -- Certificate of Residency to Cedula --
(3, 3), -- Certificate of Residency to Proof of Residency (e.g., utility bill or lease agreement) --
(5, 1), -- Certificate of Good Moral Character to Government-issued ID --
(5, 2), -- Certificate of Good Moral Character to Cedula --
(6, 7); -- Certificate for Business to Proof of income --

CREATE TABLE Requests (
	request_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    doc_id INT NOT NULL,
    request_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Pending', 'Approved', 'Denied', 'Processing', 'Shipping', 'Ready for Pick-up', 'Released') NOT NULL DEFAULT 'Pending',
    delivery_mode ENUM('Pick-up', 'Delivery') NOT NULL,
    shipping_fee DECIMAL(10, 2) DEFAULT 0.00,
    is_on_behalf TINYINT NOT NULL DEFAULT 0,
    payment_status ENUM('Pending', 'Paid', 'Refunded') NOT NULL DEFAULT 'Pending',
    shipping_date DATETIME DEFAULT NULL, 
    arrival_date DATETIME DEFAULT NULL,
    CONSTRAINT requests_fk_user FOREIGN KEY (user_id) REFERENCES users(id),
	CONSTRAINT requests_fk_docu FOREIGN KEY (doc_id) REFERENCES document_types(doc_id)
);

CREATE TABLE Uploaded_Documents (
	upload_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    req_id INT NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    CONSTRAINT ud_fk_requests FOREIGN KEY (request_id) REFERENCES Requests(request_id),
    CONSTRAINT ud_fk_requirements FOREIGN KEY (req_id) REFERENCES Requirements(req_id)
);

CREATE TABLE Wallet (
  user_id INT NOT NULL PRIMARY KEY,
  balance DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
  CONSTRAINT wallet_fk_user FOREIGN KEY (user_id) REFERENCES Users(id)
);

CREATE TABLE Payments (
  payment_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  amount DECIMAL(10, 2) NOT NULL,
  payment_date DATETIME NOT NULL DEFAULT current_timestamp(),
  request_id INT DEFAULT NULL,
  transaction_type ENUM('Request Payment', 'Wallet Load', 'Refund') NOT NULL,
  CONSTRAINT payments_fk_user FOREIGN KEY (user_id) REFERENCES Users(id),
  CONSTRAINT payments_fk_request FOREIGN KEY (request_id) REFERENCES Requests(request_id)
);
