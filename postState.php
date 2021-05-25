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
if (isset($_POST["state"])) {
    $iotState = new IotState($d);
    if (in_array($_POST["state"], $iotState->getStatesAllowed())) {
        try {
            $iotState->setState($_POST["state"])->save();
        }
        catch (Exception $e) {
            header("Access-Control-Allow-Origin: *");
            http_response_code(400);
            echo "Error: " . $e->getMessage();
            exit();
        }
        finally {
            header("Access-Control-Allow-Origin: *");
            http_response_code(200);
            echo $iotState->toJson();
            exit();
        }
    }
    else {
        header("Access-Control-Allow-Origin: *");
        http_response_code(400);
        echo "Invalid input";
        exit();
    }
}
else {
    header("Access-Control-Allow-Origin: *");
    http_response_code(400);
    echo "No data posted";
    exit();
}