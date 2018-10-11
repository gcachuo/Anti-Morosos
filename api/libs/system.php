<?php
/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 8/10/18
 * Time: 01:57 AM
 */

/**
 * @param $variable
 * @return string
 */
function isset_get(&$variable)
{
    $variable = isset($variable) ? $variable : '';
    return $variable;
}

/**
 * @param string $error
 * @param int $code
 * @throws Exception
 */
function set_error($error, $code = 0)
{
    throw new Exception($error, $code);
}

/**
 * @param int $errno
 * @param string $errstr
 * @param string $errfile
 * @param int $errline
 */
function error_handler($errno, $errstr, $errfile, $errline)
{
    http_response_code(500);
    die(json_encode(['response' => 'System Error', 'code' => http_response_code(), 'error' => compact('errno', 'errstr', 'errfile', 'errline')]));
}

function shutdown_function()
{
}

function auto_loader($clase) {
    include 'controllers/' . $clase . '.php';
}