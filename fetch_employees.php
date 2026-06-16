<?php
$db = new SQLite3('database/database.sqlite');
$results = $db->query("SELECT email FROM users WHERE role != 1 LIMIT 3");
while ($row = $results->fetchArray()) {
    echo $row['email'] . "\n";
}
