<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Constants
include "secrets.php";

// Data model
include "IotCommand.php";

// Database connection
$d = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_SCHEMA);

// Get data from POST string
if (isset($_POST["degrees"])) {
    $degrees = (int) $_POST["degrees"];
    $iotCommand = new IotCommand($d);
    $iotCommand->setDegrees($degrees);
    $iotCommand->save();
    header("Access-Control-Allow-Origin: *");
    http_response_code(200);

}
else {
    http_response_code(400);
}