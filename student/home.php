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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subjects =  ucwords($_POST["subjects"]);
    $messages = ucfirst(strtolower($_POST["messages"]));

    $insertQuery = "INSERT INTO complaints (userid, subjects, messages) 
    VALUES ('$user', '$subjects', '$messages')";
    $result = $connection->query($insertQuery);

    if (!$result) {
        $errorMessage = "Invalid query " . $connection->error;
    } else {
        $subjects = $messagess = "";
        
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
        <div class="card my-4 col-sm-9 bg-gray text-light p-5">
            <div class="d-flex justify-content-end">
                <a href="complaints.php" title="Submit a Complaint" class="btn btn-greenblue"><i class="bi bi-plus text-info"></i></a>
            </div>
            <h5 class="text-center fw-bold mb-3">Complaint History</h5>
            <div class="col input-group mb-3">
                <input type="text" class="form-control bg-dark text-light" id="searchInput" placeholder="Search" oninput="search()">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
            </div>
            <div class="table-responsive" style="height: 480px;">
                <table id="complaint-table" class="table table-bordered table-hover table-dark">
                    <thead class="table-light table-dark" style="position: sticky; top: 0;">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Subject</th>
                            <th scope="col">Messages</th>
                            <th scope="col">Date Submitted</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider">
                    <?php

                        $result = $connection->query("SELECT * FROM complaints WHERE userid ='$user' ORDER BY id DESC");

                        if ($result->num_rows > 0) {
                            $count = 1; 

                            while ($row = $result->fetch_assoc()) {
                                echo '<tr>';
                                echo '<td>' . $count . '</td>';
                                echo '<td>' . $row['subjects'] . '</td>';
                                echo '<td>' . $row['messages'] . '</td>';
                                echo '<td>' . date("F d, Y", strtotime($row['submitted_at'])) . '</td>';
                                echo '<td>' . $row['status'] . '</td>';
                                echo '</tr>';
                                $count++; 
                            }
                        } else {
                            echo '<tr><td colspan="5">No records found.</td></tr>';
                        }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
         //---------------------------Search Results---------------------------//
        function search() {
            const query = document.getElementById("searchInput").value;

            $.ajax({
                url: 'search_complaints.php', 
                method: 'POST',
                data: { query: query },
                success: function(data) {
                    // Update the user-table with the search results
                    $('#complaint-table tbody').html(data);
                },
                error: function(xhr, status, error) {
                    console.error("Error during search request:", error);
                }
            });
        }
    </script>

</body>
</html>