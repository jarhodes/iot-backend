<?php

/**
 * Returns a connected mysqli instance
 * @return mysqli
 */
function dbConnection() {
    return new mysqli("localhost", "nettivai_iotphp", "4E2!idbg3mVnZNF", "nettivai_iot");
}