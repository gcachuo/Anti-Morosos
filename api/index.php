<?php
include_once "Lib/System.php";

$path = "vendor/autoload.php";
if (file_exists($path)) {
    require_once($path);
}

$system = new System();
$system->init(['DIR' => __DIR__]);

