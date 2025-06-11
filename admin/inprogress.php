<?php

require("../server/connection.php");

if (isset($_POST['id'])) {
    $id = $_POST['id'];

    $id = $connection->real_escape_string($id);

    $updateQuery = "UPDATE complaints SET status = 'In Progress' WHERE id = '$id'";
    $updateResult = $connection->query($updateQuery);

    if ($updateResult) {
        echo json_encode(['success' => 'Complaint marked as In Progress']);
    } else {
        error_log("Error: " . $connection->error);
        echo json_encode(['error' => 'Error updating the record']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}

$connection->close();

?>