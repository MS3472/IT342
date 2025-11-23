<?php
require_once __DIR__ . '/../Include/db.php';

$db = getDB();

$result = $db->query("SHOW TABLES");

while ($row = $result->fetch_array()) {
    echo $row[0] . "<br>";
}
