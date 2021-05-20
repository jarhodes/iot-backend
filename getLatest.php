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

// Connect to the database
$d = dbConnection();

// Create object
// The object automatically fetches the most recent command
$iotCommand = new IotCommand($d);

// Return the number of degrees in JSON format
/*
 * {
 *    degrees: 180
 * }
 */
header("Access-Control-Allow-Origin: *");
echo json_encode([  "degrees" => $iotCommand->getDegrees(),
                    "stamp" => intval($iotCommand->getStamp()->format("U")) ] );
exit();