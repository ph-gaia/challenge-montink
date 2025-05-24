<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

require_once '/var/www/html/vendor/autoload.php';
try {

    $app = new \Core\Bootstrap\InitApp();
    $app->run();

} catch (Exception $ex) {
    echo $ex->getMessage();
}
