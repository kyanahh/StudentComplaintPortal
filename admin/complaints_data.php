<?php
require("../server/connection.php");

$query = "SELECT status, COUNT(*) as count FROM complaints GROUP BY status";
$result = $connection->query($query);

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);
?>
