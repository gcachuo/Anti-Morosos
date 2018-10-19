<?php
/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 11/10/18
 * Time: 12:25 AM
 */

$db_config = [
    "host" => "localhost",
    "user" => "root",
    "password" => "sqlserver",
    "database" => "antimorosos"
];
/*$db_config = [
    "host" => "localhost",
    "user" => "antimoro_sos",
    "password" => "antimorosos",
    "database" => "antimoro_antimorosos"
];*/
$mysqli = db_connect();

function db_connect()
{
    global $db_config;
    return mysqli_connect($db_config['host'], $db_config['user'], $db_config['password'], $db_config['database']);
}

function db_query($sql)
{
    global $mysqli;
    return $mysqli->query($sql);
}

function db_result($sql)
{
    global $mysqli;
    $mysqli_result = db_query($sql);
    $result = null;
    if ($mysqli_result) {
        $result = $mysqli_result->fetch_assoc();
    } else {
        set_error($mysqli->error);
    }
    return $result;
}

function db_last_id()
{
    global $mysqli;
    return $mysqli->insert_id;
}

/**
 * @param string $sql
 * @param int $type
 * @return array|null
 * @throws Exception
 */
function db_all_results($sql, $type = MYSQLI_ASSOC)
{
    global $mysqli;
    $mysqli_result = db_query($sql);
    $result = null;
    if ($mysqli_result) {
        $result = $mysqli_result->fetch_all($type);
    } else {
        set_error($mysqli->error);
    }
    return $result;
}