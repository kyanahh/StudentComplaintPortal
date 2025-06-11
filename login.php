<?php

session_start();
require("server/connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentnumber = $_POST["studentnumber"];
    $pin = $_POST["pin"];
    $captcha = $_POST["captcha"];

    if ($captcha != $_SESSION['captcha_sum']) {
        header("Location: login.php?error=captcha");
        exit;
    }

    $result = $connection->query("SELECT * FROM users 
        WHERE userid = '$studentnumber' AND pin = '$pin'");

    if ($result->num_rows === 1) {
        $record = $result->fetch_assoc();
        $_SESSION["userid"] = $record["userid"];
        $_SESSION["firstname"] = $record["firstname"];
        $_SESSION["lastname"] = $record["lastname"];
        $_SESSION["email"] = $record["email"];
        $_SESSION["usertype"] = $record["usertype"];
        $_SESSION["logged_in"] = true;

        $usertype = $record["usertype"];
        if ($usertype == "Admin") {
            header("Location: /studentcomplaint/admin/dashboard.php");
        } elseif ($usertype == "Student") {
            header("Location: /studentcomplaint/student/home.php");
        }
        exit;
    } else {
        header("Location: login.php?error=credentials");
        exit;
    }
} else {

    $num1 = rand(1, 9);
    $num2 = rand(1, 9);
    $_SESSION['captcha_sum'] = $num1 + $num2;

}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PLMun Student Portal: Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
    <link href="style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        
</head>
<body class="login">
    <nav class="navbar navbar-expand-lg bg-gray">
        <div class="container-fluid">
            <a class="navbar-brand" href="login.php"><img src="images/plmunlogo.png" alt="PLMUN" class="img-fluid h-45"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>

    <div class="container">
        <div class="div d-flex justify-content-center">
            <div class="card col-sm-4 my-5 bg-gray text-white p-3">
                <div class="row">
                    <div class="col-sm-2">
                        <i class="bi bi-shield-lock display-4 float-start"></i>
                    </div>
                    <div class="col mt-2">
                        <h5>PLMun Student Complaint Portal</h5>
                        <p>Fill out the Form to login</p>
                    </div>
                </div>
                <hr>
                <form method="POST" action="<?php htmlspecialchars("SELF_PHP"); ?>">
                    <label for="studentnumber" class="text-secondary fw-bold mb-1">Student Number</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text bg-gray" id="studentnumber1"><i class="bi bi-person-fill text-white"></i></span>
                        <input type="text" class="form-control bg-dark text-white" name="studentnumber" aria-label="studentnumber" aria-describedby="studentnumber1">
                    </div>
                    <label for="pin" class="text-secondary fw-bold mb-1">Pin Number</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text bg-gray" id="pin1"><i class="bi bi-key-fill text-white"></i></span>
                        <input type="password" class="form-control bg-dark text-white" name="pin" aria-label="pin" aria-describedby="pin1">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">What is <?= $num1 ?> + <?= $num2 ?>?</label>
                        <input type="number" class="form-control bg-gray text-secondary" name="captcha" required>
                    </div>

                    <div class="">
                        <div class="col d-grid gap-2 d-flex justify-content-end">
                            <button type="submit" class="btn btn-greenblue text-info mt-3 fw-bold col-sm-3">Sign in</button>
                        </div>
                    </div>
                </form>
                <hr>
                <p>If you forgot your PIN or need an assistance to activate your account, please email us at 
                    <a href="mailto:support@plmun.edu.ph" class="text-decoration-none text-success">support@plmun.edu.ph</a> 
                    using your official IE account. We will only entertain messages coming 
                    from PLMun official IE.</p>
            </div>
        </div>
    </div>
    
    <!-- CAPTCHA Error Modal -->
    <div class="modal fade" id="captchaErrorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center py-3">
        <div class="modal-body">
            <i class="bi bi-x-circle display-3 text-danger"></i>
            <h3 class="mt-3">Error on Form</h3>
            <p>CAPTCHA is not valid.</p>
            <button type="button" class="btn btn-primary px-5 mt-2" data-bs-dismiss="modal">OK</button>
        </div>
        </div>
    </div>
    </div>

    <!-- Credentials Error Modal -->
    <div class="modal fade" id="loginErrorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center py-3">
        <div class="modal-body">
            <i class="bi bi-x-circle display-3 text-danger"></i>
            <h3 class="mt-3">Error on Form</h3>
            <p>Invalid student number or password.</p>
            <button type="button" class="btn btn-primary px-5 mt-2" data-bs-dismiss="modal">OK</button>
        </div>
        </div>
    </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const urlParams = new URLSearchParams(window.location.search);
            const error = urlParams.get('error');

            if (error === 'captcha') {
                new bootstrap.Modal(document.getElementById('captchaErrorModal')).show();
            } else if (error === 'credentials') {
                new bootstrap.Modal(document.getElementById('loginErrorModal')).show();
            }
        });
    </script>

</body>
</html>