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
include "IotState.php";

// Database connection
$d = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_SCHEMA, DB_PORT);

// Get data from POST string
if (isset($_POST["finished"]) && isset($_POST["id"])) {
    $iotState = new IotState($d);
    $id = (int) $_POST["id"];
    try {
        $iotState->setId($id)->fetchById();
    }
    catch (Exception $e) {
        header("Access-Control-Allow-Origin: *");
        http_response_code(404);
        echo "Resource not found";
        exit();
    }

    // Finished as a boolean value
    $finished = (bool) $_POST["finished"];

    // Save and output the latest version of the record
    $iotState->setFinished($finished)->save();
    header("Access-Control-Allow-Origin: *");
    http_response_code(200);
    echo json_encode([
        "id" => $iotState->getId(),
        "state" => $iotState->getState(),
        "finished" => $iotState->isFinished(),
        "stamp" => (int) $iotState->getStamp()->format("U")
    ]);
}
else {
    header("Access-Control-Allow-Origin: *");
    http_response_code(400);
    echo "No data posted";
    exit();
}