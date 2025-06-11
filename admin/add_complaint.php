<?php

require("../server/connection.php");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subjects']) && isset($_POST['messages'])) {
    $userid = $connection->real_escape_string($_POST['userid']);
    $subjects = $connection->real_escape_string($_POST['subjects']);
    $messages = $connection->real_escape_string($_POST['messages']);
    $status = "Pending";
    $submitted_at = date("Y-m-d H:i:s");

    $query = "INSERT INTO complaints (userid, subjects, messages, submitted_at, status) 
    VALUES ('$userid', '$subjects', '$messages', '$submitted_at', '$status')";

    if ($connection->query($query)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add record.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
