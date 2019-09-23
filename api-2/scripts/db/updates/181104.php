#!/usr/bin/php
<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    require getcwd() . "/libs/database.php";

    $query = <<<sql
ALTER TABLE users ADD user_session varchar(255) NULL;
CREATE UNIQUE INDEX users_user_session_uindex ON users (user_session);
sql;

    db_query($query);
    echo "Correct.";
} catch (Exception $ex) {
    echo $ex->getMessage();
    echo "<br><br><br> $query";
}