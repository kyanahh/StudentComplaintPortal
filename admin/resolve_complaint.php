<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require("../server/connection.php");
require_once '../vendor/autoload.php';
header('Content-Type: application/json');

use SendinBlue\Client\Configuration;
use SendinBlue\Client\Api\TransactionalEmailsApi;
use GuzzleHttp\Client;
use SendinBlue\Client\Model\SendSmtpEmail;

try {
    if (!isset($_POST['id'], $_POST['remarks'])) {
        throw new Exception("Invalid request data");
    }

    $id = $_POST['id'];
    $remarks = $_POST['remarks'];

    // Step 1: Fetch user info
    $stmt = $connection->prepare("SELECT users.userid, users.email, users.firstname, users.lastname FROM complaints JOIN users ON complaints.userid = users.userid WHERE complaints.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) {
        throw new Exception("User not found.");
    }

    $email = $row['email'];
    $name = $row['firstname'] . ' ' . $row['lastname'];

    // Step 2: Update complaint
    $update = $connection->prepare("UPDATE complaints SET status = 'Resolved', remarks = ? WHERE id = ?");
    $update->bind_param("si", $remarks, $id);

    if (!$update->execute()) {
        throw new Exception("Failed to update complaint.");
    }

    // Step 3: Send email via Brevo
    $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', '');
    $apiInstance = new TransactionalEmailsApi(new Client(), $config);

    $emailData = new SendSmtpEmail([
        'subject' => 'Your Complaint Has Been Resolved',
        'sender' => ['name' => 'PLMun Student Complaint Portal', 'email' => 'kianna.businessemail@gmail.com'],
        'to' => [['email' => $email, 'name' => $name]],
        'htmlContent' => "<p>Dear $name,</p><p>Your complaint has been resolved. <br><strong>Remarks:</strong><br>$remarks</p><p>Thank you.</p>"
    ]);

    try {
        $response = $apiInstance->sendTransacEmail($emailData);
        echo json_encode(['success' => true, 'response' => $response]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
    }

} catch (Exception $e) {
    // This will send the error message back to your JS console
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

?>