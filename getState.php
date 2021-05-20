<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Data model
include "IotCommand.php";

// Database connection
include "dbConnection.php";

// Connect to the database
$d = dbConnection();

// Instantiate an IotState object
$iotState = new IotState($d);

// Fetch the latest state
$iotState->fetchLatest();

// Output a JSON object
header("Access-Control-Allow-Origin: *");
echo json_encode([
        "state" => $iotState->getState(),
        "stamp" => intval($iotState->getStamp()->format("U")
    )]);
exit();
