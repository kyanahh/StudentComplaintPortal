<?php
require("../server/connection.php");

if (isset($_POST['id'], $_POST['userid'], $_POST['subjects'], $_POST['messages'], $_POST['status'])) {
    $id = $_POST['id'];
    $userid = $_POST['userid'];
    $subjects = $_POST['subjects'];
    $messages = $_POST['messages'];
    $status = $_POST['status'];

    $query = $connection->prepare("UPDATE complaints SET userid = ?, subjects = ?, messages = ?, status = ? WHERE id = ?");
    $query->bind_param("isssi", $userid, $subjects, $messages, $status, $id);

    if ($query->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update record.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
