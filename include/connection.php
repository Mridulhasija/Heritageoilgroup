<?php

require_once __DIR__ . '/config.php';

function db()
{
    static $conn;
    if ($conn === NULL) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
    }
    return $conn;
}
