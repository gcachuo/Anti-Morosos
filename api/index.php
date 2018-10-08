<?php
/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 7/10/18
 * Time: 09:28 PM
 */

setcookie('XDEBUG_SESSION', 'PHPSTORM');
ini_set('display_errors', 1);
set_error_handler('error_handler');
register_shutdown_function('shutdown_function');

try {
    $action = isset_get($_REQUEST['action']);
    /** @var array|string|null $response */
    if (empty($action)) {
        $response = null;
    } elseif (function_exists($action)) {
        $response = $action();
    } else {
        set_error("The function does not exists. ($action)");
    }
} catch (Exception $exception) {
    http_response_code(500);
    $errno = $exception->getCode();
    $errstr = $exception->getMessage();
    $errfile = $exception->getFile();
    $errline = $exception->getLine();

    die(json_encode(['response' => 'App Error', 'code' => http_response_code(), 'error' => compact('errno', 'errstr', 'errfile', 'errline')]));
}

echo json_encode(['response' => $GLOBALS['response'], 'code' => http_response_code(), 'version' => '181008']);

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