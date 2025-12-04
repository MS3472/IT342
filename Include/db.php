<?php
// includes/db.php

function getDB(): mysqli {
    static $db = null;
    if ($db !== null) {
        return $db;
    }

    $host = getenv('DB_HOST') ?: 'db';
    $name = getenv('DB_NAME') ?: 'powerhub';
    $user = getenv('DB_USER') ?: 'powerhub_user';
    $pass = getenv('DB_PASS') ?: 'powerhub_pass';
    $port = 3306;

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        $db = new mysqli($host, $user, $pass, $name, $port);
        $db->set_charset('utf8mb4');
        return $db;
    } catch (mysqli_sql_exception $e) {
        error_log('DB connection error: ' . $e->getMessage());
        http_response_code(500);
        exit('Database connection error.');
    }
}
