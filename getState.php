<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Constants
include "secrets.php";

// Require the API key in the query string
if (!isset($_GET["key"]) || $_GET["key"] !== API_KEY) {
    http_response_code(403);
    exit();
}

// Data model
include "IotCommand.php";

// Database connection
$d = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_SCHEMA);

// Instantiate an IotState object
$iotState = new IotState($d);

// Fetch the latest state
try {
    $iotState->fetchLatest();
}
catch (Exception $e) {
    http_response_code(500);
    echo "Database error";
    exit();
}

// Output a JSON object
header("Access-Control-Allow-Origin: *");
http_response_code(200);
echo json_encode([
        "state" => $iotState->getState(),
        "stamp" => intval($iotState->getStamp()->format("U")
    )]);
exit();
