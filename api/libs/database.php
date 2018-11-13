<?php
/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 11/10/18
 * Time: 12:25 AM
 */

$mysqli = db_connect();

/**
 * @return mysqli
 */
function db_connect()
{
    $config = [
        'developer' => [
            "user" => "root",
            "password" => "sqlserver",
            "database" => "antimorosos"
        ],
        'production' => [
            "user" => "antimoro_sos",
            "password" => "antimorosos",
            "database" => "antimoro_antimorosos"
        ]
    ];
    $db_config = $config[getenv('CONFIG_DB') ?: 'production'];
    return mysqli_connect("localhost", $db_config['user'], $db_config['password'], $db_config['database']);
}

/**
 * @param $sql
 * @return bool|mysqli_result
 */
function db_query($sql)
{
    global $mysqli;
    return $mysqli->query($sql);
}

/**
 * @param $sql
 * @return array|null
 * @throws Exception
 */
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

/**
 * @return int
 */
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

/**
 * @throws Exception
 */
function db_error()
{
    global $mysqli;
    set_error("(" . $mysqli->errno . ") " . $mysqli->error, 500);
}
