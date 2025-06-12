<?php
session_start();
require("../server/connection.php");

require_once('../vendor/autoload.php'); 

use SendinBlue\Client\Api\TransactionalEmailsApi;
use SendinBlue\Client\Configuration;
use GuzzleHttp\Client;

$showModal = false;

if (isset($_SESSION["logged_in"])) {
    if (isset($_SESSION["firstname"]) || isset($_SESSION["email"]) || isset($_SESSION["lastname"]) || isset($_SESSION["userid"])) {
        $firstname = $_SESSION["firstname"];
        $lastname = $_SESSION["lastname"];
        $user = $_SESSION["userid"];
        $useremail = $_SESSION["email"];
    } else {
        $textaccount = "Account";
    }
} else {
    $textaccount = "Account";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subjects = ucwords($_POST["subjects"]);
    $messages = ucfirst(strtolower($_POST["messages"]));

    $insertQuery = "INSERT INTO complaints (userid, subjects, messages) 
                    VALUES ('$user', '$subjects', '$messages')";
    $result = $connection->query($insertQuery);

    if (!$result) {
        $errorMessage = "Invalid query " . $connection->error;
    } else {
        // Send email using Brevo API
        $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', '');

        $apiInstance = new TransactionalEmailsApi(
            new Client(),
            $config
        );

        $sendSmtpEmail = new \SendinBlue\Client\Model\SendSmtpEmail([
            'subject' => 'Complaint Received - PLMun Student Complaint Portal',
            'sender' => ['name' => 'PLMun Portal', 'email' => 'kianna.businessemail@gmail.com'],
            'to' => [['email' => $useremail, 'name' => "$firstname $lastname"]],
            'htmlContent' => "
                <html>
                    <body>
                        <p>Dear $firstname,</p>
                        <p>Thank you for submitting your complaint. Our team has received the following details:</p>
                        <p><strong>Subject:</strong> $subjects</p>
                        <p><strong>Message:</strong> $messages</p>
                        <p>We will get back to you as soon as possible.</p>
                        <br>
                        <p>Regards,</p>
                        <p><strong>PLMun Support Team</strong></p>
                    </body>
                </html>",
        ]);

        try {
            $apiInstance->sendTransacEmail($sendSmtpEmail);
        } catch (Exception $e) {
            error_log('Exception when sending email: ' . $e->getMessage());
        }

        $showModal = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PLMun Student Complaint Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="../style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        
</head>
<body class="bg-dark">
    <nav class="navbar navbar-expand-lg bg-gray">
        <div class="container-fluid">
            <a class="navbar-brand" href="home.php"><img src="../images/plmunlogo.png" alt="PLMUN" class="img-fluid h-45"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item me-5">
                        <div class="btn-group dropstart">
                            <button class="btn dropdown-toggle px-4" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-gear text-white"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="home.php">Home</a></li>
                                <li><a class="dropdown-item" href="complaints.php">Add Complaint</a></li>
                                <li><a class="dropdown-item" href="../logout.php">Logout</a></li>
                            </ul>
                        </div>
                    </li>
                </ul>
          </div>

        </div>
    </nav>

    <div class="container d-flex justify-content-center">
        <div class="card mt-4 col-sm-9 bg-gray text-light p-3">
            <div class="row d-flex align-items-center">
                <div class="col-sm-1">
                    <i class="bi bi-house-door display-4 text-success"></i>
                </div>
                <div class="col pt-2">
                    <h4>Student Complaint Portal</h4>
                </div>
                <div class="col d-flex justify-content-end gap-2 mt-3 me-3">
                    <i class="bi bi-house-door"></i> / <p class="text-success"><?php echo $firstname; ?> <?php echo $lastname; ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="container d-flex justify-content-center">
        <div class="card mt-4 col-sm-9 bg-gray text-light p-5">

            <h5 class="text-center fw-bold">Submit Complaint</h5>
            <form action="<?php htmlspecialchars("SELF_PHP"); ?>" method="POST">
                <div class="mb-3">
                    <label for="subjects" class="form-label">Subject</label>
                    <input type="text" class="form-control" placeholder="Write your subject here" name="subjects" required>
                </div>
                <div class="mb-3">
                    <label for="messages" class="form-label">Message</label>
                    <textarea class="form-control" id="messages" name="messages" rows="5" placeholder="Compose a message here" required></textarea>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-greenblue text-info px-5 mt-3">Submit</button>
                </div>
            </form>

        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered text-center">
            <div class="modal-content bg-success text-white p-4">
                <div class="modal-body">
                    <i class="bi bi-check-circle display-1"></i>
                    <h4 class="mt-3">Complaint Submitted!</h4>
                    <button type="button" class="btn btn-light mt-3" id="okBtn">Okay</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        <?php if ($showModal): ?>
            const modal = new bootstrap.Modal(document.getElementById('successModal'));
            modal.show();

            document.addEventListener("DOMContentLoaded", () => {
                const okBtn = document.getElementById("okBtn");
                okBtn.addEventListener("click", () => {
                    location.reload();
                });
            });
        <?php endif; ?>
    </script>


</body>
</html>