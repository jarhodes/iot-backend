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

// Require an ID in the query string
if (!isset($_GET["id"]) || empty($_GET["id"])) {
    http_response_code(400);
    exit();
}
$id = (int) $_GET["id"];

// Data model
include "IotState.php";

// Database connection
$d = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_SCHEMA, DB_PORT);

// Instantiate an IotState object
$iotState = new IotState($d);

// Fetch the requested state
try {
    $iotState->setId($id)->fetchById();
}
catch (Exception $e) {
    http_response_code(404);
    exit();
}

// Output a JSON object
header("Access-Control-Allow-Origin: *");
http_response_code(200);
echo json_encode([
    "id" => $iotState->getId(),
    "state" => $iotState->getState(),
    "finished" => $iotState->isFinished(),
    "stamp" => intval($iotState->getStamp()->format("U")
    )]);
exit();
