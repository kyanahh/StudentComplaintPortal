<?php

session_start();

require("../server/connection.php");

if(isset($_SESSION["logged_in"])){
    if(isset($_SESSION["firstname"]) || isset($_SESSION["email"]) || isset($_SESSION["lastname"]) || isset($_SESSION["userid"])){
        $firstname = $_SESSION["firstname"];
        $lastname = $_SESSION["lastname"];
        $user = $_SESSION["userid"];
        $useremail = $_SESSION["email"];
    }else{
        $textaccount = "Account";
    }
}else{
    $textaccount = "Account";
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
                <ul class="navbar-nav ms-auto gap-4 me-5">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="dashboard.php">DASHBOARD</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="complaints.php">COMPLAINTS</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="../logout.php">LOGOUT</a>
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
                    <i class="bi bi-house-door"></i> / <p class="text-success"><?php echo $firstname; ?> </p>
                </div>
            </div>
        </div>
    </div>


    <div class="container mt-5 bg-light rounded p-4 col-sm-6 bg-gray">
        <h4 class="text-center mb-4">Complaint Status Overview</h4>
        <canvas id="statusChart" width="400" height="200"></canvas>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        fetch('complaints_data.php')
            .then(response => response.json())
            .then(data => { 
                const statuses = data.map(row => row.status);
                const counts = data.map(row => row.count);

                const ctx = document.getElementById('statusChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: statuses,
                        datasets: [{
                            label: 'No. of Complaints',
                            data: counts,
                            backgroundColor: ['#ffc107', '#0dcaf0', '#198754', '#dc3545'], // Pending, In Progress, Resolved, Unresolved
                            borderColor: '#343a40',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true,
                                precision: 0
                            }
                        }
                    }
                });
            });
    </script>

</body>
</html>