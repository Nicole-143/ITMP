CREATE DATABASE brgydb;
USE brgydb;

CREATE TABLE Document_Types(
	doc_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    doc_name VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    shipping_fee DECIMAL(10, 2) NOT NULL,
    description VARCHAR(255) NOT NULL,
    status ENUM('Active', 'Inactive')
) AUTO_INCREMENT = 9;

INSERT INTO Document_Types (doc_id, doc_name, price, shipping_fee, description, status) 
VALUES
(1, 'Community Tax Certificate (Cedula)', 20.00, 30.00, 'Proof of tax payment and identity, often required for official transactions.', 'Active'),
(2, 'Barangay Clearance', 20.00, 30.00, 'Certification from the barangay confirming good standing in the community.', 'Active'),
(3, 'Certificate of Residency', 20.00, 30.00, 'Verifies that an individual is a resident of a specific barangay.', 'Active'),
(4, 'Certificate of Indigency', 20.00, 30.00, 'Declares that a person or family belongs to the low-income or indigent sector.', 'Active'),
(5, 'Certificate of Good Moral Character', 20.00, 30.00, 'Affirms that a person has no record of misconduct in the community.', 'Active'),
(6, 'Certificate for Business', 20.00, 30.00, 'Confirms that a business is operating within the barangay with approval.', 'Active'),
(7, 'Certificate of No Objection', 20.00, 30.00, 'States that the barangay has no objection to a specific request or action.', 'Active'),
(8, 'Certificate of Nothing', 20.00, 30.00, 'States absolutely nothing.', 'Inactive');

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
    is_verified TINYINT NOT NULL DEFAULT 0,
    type ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    upload_id varchar(255),
    registerdate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    comment VARCHAR(255) DEFAULT NULL
);

CREATE TABLE Requests (
	request_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    doc_id INT NOT NULL,
    request_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Pending', 'Approved', 'Denied', 'Processing', 'Ready for Shipping', 'Shipping', 'Ready for Pick-up', 'Released') NOT NULL DEFAULT 'Pending',
    delivery_mode ENUM('Pick-up', 'Delivery') NOT NULL,
    payment_mode ENUM('Cash_On_Delivery','Wallet') DEFAULT NULL,
    copies INT NOT NULL,
    is_on_behalf TINYINT NOT NULL DEFAULT 0,
    represented_surname VARCHAR(255) NULL,
    represented_givenname VARCHAR(255) NULL,
    represented_middlename VARCHAR(255) NULL,
    represented_birthdate DATE NULL,
    represented_relationship ENUM('relative','senior') NULL,
    payment_status ENUM('Pending', 'Paid', 'Refunded') NOT NULL DEFAULT 'Pending',
    shipping_date DATETIME DEFAULT NULL, 
    arrival_date DATETIME DEFAULT NULL,
    pickup_status ENUM('Not Picked Up','Picked Up') NOT NULL DEFAULT 'Not Picked Up',
    pickup_date DATETIME DEFAULT NULL,
    CONSTRAINT requests_fk_user FOREIGN KEY (user_id) REFERENCES users(id),
	CONSTRAINT requests_fk_docu FOREIGN KEY (doc_id) REFERENCES document_types(doc_id)
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

-- VIEWS --
CREATE VIEW view_users AS
SELECT id, givenname, surname, middlename, email, phone, is_verified, type 
FROM Users ORDER BY id ASC;

CREATE VIEW manage_shipments AS
SELECT r.request_id, u.surname, u.givenname, u.middlename, r.status, r.request_date, r.shipping_date, r.arrival_date
FROM Requests r
LEFT JOIN Users u
	ON u.id = r.user_id
WHERE r.delivery_mode = 'Delivery' AND r.status = 'Ready for Shipping' OR r.status = 'Shipping' OR r.status = 'Released';

CREATE VIEW admin_shipments AS 
SELECT r.request_id, u.surname, u.givenname, u.middlename, u.phone, u.email, u.address,
		r.status,  r.request_date, r.shipping_date, r.arrival_date, r.payment_mode, r.payment_status, 
		dt.doc_name, r.copies, dt.price, dt.shipping_fee
FROM Requests r
LEFT JOIN Users u
	ON u.id = r.user_id
LEFT JOIN Document_Types dt
	ON dt.doc_id = r.doc_id;

CREATE VIEW manage_documents AS
SELECT dt.doc_id, dt.doc_name, dt.description, dt.price, dt.shipping_fee, dt.status
FROM Document_Types dt;

CREATE VIEW dashboard_documents AS
SELECT doc_id, doc_name, description
FROM document_types
WHERE status = 'Active';

CREATE VIEW user_documents AS
SELECT r.req_name, dtr.doc_id
FROM doc_type_requirements dtr
JOIN requirements r ON dtr.req_id = r.req_id;

CREATE VIEW document_requirements AS
SELECT r.req_name, dtr.doc_id
FROM Requirements r
JOIN Doc_Type_Requirements dtr
	ON r.req_id = dtr.req_id