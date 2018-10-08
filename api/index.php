<?php
/**
 * Created by PhpStorm.
 * User: Cachu
 * Date: 7/10/18
 * Time: 09:28 PM
 */

setcookie('XDEBUG_SESSION', 'PHPSTORM');
ini_set('display_errors', 1);
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

/** @var array|string|null $response */
if (function_exists($action)) {
    $response = $action();
} else {
    http_response_code(500);
    $response = 'Error (1000).';
}

ob_clean();
echo json_encode(['response' => $response, 'code' => http_response_code(), 'version' => '181008']);