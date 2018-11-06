DROP DATABASE IF EXISTS bills;
CREATE DATABASE bills
    DEFAULT CHARACTER SET utf8mb4
    DEFAULT COLLATE utf8mb4_unicode_ci;

USE bills;

CREATE TABLE customers (
    id INT(11) NOT NULL AUTO_INCREMENT,
    gender ENUM('none', 'male', 'female') NOT NULL DEFAULT 'none' ,
    degree VARCHAR(32),
    forename VARCHAR(128),
    surname VARCHAR(128),
    company VARCHAR(256),
    street VARCHAR(128),
    city VARCHAR(128),
    country VARCHAR(128),
    vatin VARCHAR(32),
    PRIMARY KEY (id)
);

CREATE TABLE invoices (
    id INT(11) NOT NULL AUTO_INCREMENT,
    invoice_number INT(11) NOT NULL,
    invoice_date DATE NOT NULL,
    customer_id INT(11) NOT NULL,
    reference VARCHAR(256),
    PRIMARY KEY (id),
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON UPDATE RESTRICT ON DELETE RESTRICT
);

CREATE TABLE line_items (
    id INT(11) NOT NULL AUTO_INCREMENT,
    invoice_id INT(11) NOT NULL,
    description VARCHAR(2048),
    price DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON UPDATE CASCADE ON DELETE CASCADE
);
