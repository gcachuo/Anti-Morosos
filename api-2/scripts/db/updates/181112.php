#!/usr/bin/php
<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    require getcwd() . "/libs/database.php";

    $query = <<<sql
    ALTER TABLE users ADD user_type int DEFAULT 1 NULL;
    ALTER TABLE complaints_history ADD user_id bigint(20) NOT NULL;
sql;

    db_query($query);
    echo "Correct.";
} catch (Exception $ex) {
    echo $ex->getMessage();
    echo "<br><br><br> $query";
}