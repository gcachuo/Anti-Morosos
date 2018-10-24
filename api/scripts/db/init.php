#!/usr/bin/php
<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);
ini_set('display_errors', 1);

$users = <<<sql
CREATE TABLE IF NOT EXISTS users
(
  user_id                bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
  user_email             varchar(100)           NOT NULL,
  user_username          varchar(100)           NOT NULL,
  user_password          varchar(100)           NOT NULL,
  user_name              varchar(100)           NOT NULL,
  user_lastname_1        varchar(100),
  user_lastname_2        varchar(100),
  user_company           varchar(100),
  user_business_name     varchar(100),
  user_business_position varchar(100),
  user_business_phone    varchar(100),
  user_whatsapp          varchar(100),
  user_referrer          bigint(20),
  user_referral          int(11),
  user_validation        bit(1)                          DEFAULT b'0',
  user_status            bit(1) DEFAULT b'1'    NOT NULL,
  CONSTRAINT users_users_user_id_fk FOREIGN KEY (user_referrer) REFERENCES users (user_id),
  unique key users_user_email_uindex(user_email),
  unique key users_user_username_uindex(user_username),
  unique key users_user_referral_uindex(user_referral),
  index users_user_referrer_fk(user_referrer)
);
sql;

$complaints = <<<sql
CREATE TABLE IF NOT EXISTS complaints
(
    complaint_id bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
    user_id bigint(20) NOT NULL,
    topic_id bigint(20),
    complaint_message varchar(255) NOT NULL,
    complaint_date timestamp DEFAULT CURRENT_TIMESTAMP,
    complaint_deleted varchar(255),
    complaint_status bit(1) DEFAULT b'1' NOT NULL
);
sql;

$payers = <<<sql
CREATE TABLE IF NOT EXISTS payers
(
    payer_id bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
    payer_name varchar(100) NOT NULL,
    payer_status bit DEFAULT b'1' NOT NULL
);
sql;

$topics = <<<sql
CREATE TABLE IF NOT EXISTS topics
(
    topic_id int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
    topic_name varchar(100) NOT NULL,
    topic_status bit(1) DEFAULT b'1'
);
INSERT INTO topics (topic_id, topic_name, topic_status) VALUES (1, 'Morosos', true);
INSERT INTO topics (topic_id, topic_name, topic_status) VALUES (2, 'Empleados', true);
INSERT INTO topics (topic_id, topic_name, topic_status) VALUES (3, 'Recomendaciones', true);
INSERT INTO topics (topic_id, topic_name, topic_status) VALUES (4, 'Ventas', true);
sql;

$products = <<<sql
CREATE TABLE IF NOT EXISTS products
(
    product_id bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
    product_name varchar(100) NOT NULL,
    product_status bit(1) DEFAULT b'1'
);
CREATE UNIQUE INDEX products_product_name_uindex ON products (product_name);
INSERT INTO products (product_id, product_name, product_status) VALUES (1, 'Suela', true);
INSERT INTO products (product_id, product_name, product_status) VALUES (2, 'Caja', true);
INSERT INTO products (product_id, product_name, product_status) VALUES (3, 'Planta', true);
INSERT INTO products (product_id, product_name, product_status) VALUES (4, 'Forro', true);
INSERT INTO products (product_id, product_name, product_status) VALUES (5, 'Piel', true);
INSERT INTO products (product_id, product_name, product_status) VALUES (6, 'Sintetico', true);
INSERT INTO products (product_id, product_name, product_status) VALUES (7, 'Horma', true);
INSERT INTO products (product_id, product_name, product_status) VALUES (8, 'Tacon', true);
INSERT INTO products (product_id, product_name, product_status) VALUES (9, 'Herrajes', true);
INSERT INTO products (product_id, product_name, product_status) VALUES (10, 'Peletero', true);
INSERT INTO products (product_id, product_name, product_status) VALUES (11, 'Adhesivos', true);
INSERT INTO products (product_id, product_name, product_status) VALUES (12, 'Bondeado', true);
INSERT INTO products (product_id, product_name, product_status) VALUES (13, 'Moldes', true);
INSERT INTO products (product_id, product_name, product_status) VALUES (14, 'Textiles', true);
sql;

$users_products = <<<sql
CREATE TABLE IF NOT EXISTS users_products
(
    user_product_id bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
    user_id bigint(20) NOT NULL,
    product_id bigint(20) NOT NULL,
    CONSTRAINT users_products_users_user_id_fk FOREIGN KEY (user_id) REFERENCES users (user_id),
    CONSTRAINT users_products_products_product_id_fk FOREIGN KEY (product_id) REFERENCES products (product_id)
);
CREATE UNIQUE INDEX products_users_pk ON users_products (product_id, user_id);
CREATE INDEX users_products_users_user_id_fk ON users_products (user_id);
sql;

$complaints_history = <<<sql
CREATE TABLE IF NOT EXISTS complaints_history
(
    complaint_history_id bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
    complaint_id bigint(20) NOT NULL,
    complaint_history_change_date timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
    complaint_history_message varchar(255) NOT NULL,
    CONSTRAINT complaints_history_complaints_complaint_id_fk FOREIGN KEY (complaint_id) REFERENCES complaints (complaint_id)
);
CREATE INDEX complaints_history_complaints_complaint_id_fk ON complaints_history (complaint_id);
sql;

$complaints_users=<<<sql
CREATE TABLE complaints_users
(
    complaint_user_id bigint(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
    complaint_user_date timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
    complaint_id bigint(20) NOT NULL,
    user_id bigint(20) NOT NULL,
    complaint_user_read bit(1) DEFAULT b'0' NOT NULL
);
CREATE UNIQUE INDEX complaints_users_pk ON complaints_users (complaint_id, user_id);
sql;


try {
    require getcwd(). "/libs/database.php";
    $query =
        $users .
        $complaints .
        $payers .
        $topics .
        $products .
        $users_products .
        $complaints_history.
        $complaints_users;

    db_query($query);
    echo "Correct.";
} catch (Exception $ex) {
    echo $ex->getMessage();
    echo "<br><br><br> $query";
}
