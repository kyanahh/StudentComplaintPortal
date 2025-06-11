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

$status = '';

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
                    <h4>Student Complaints
                    </h4>
                </div>
                <div class="col d-flex justify-content-end gap-2 mt-3 me-3">
                    <i class="bi bi-house-door"></i> / <p class="text-success"><?php echo $firstname; ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="container d-flex justify-content-center">
        <div class="card my-4 col-sm-9 bg-gray text-light p-5">
            <div class="d-flex justify-content-end">
                <button title="Submit a Complaint" class="btn btn-greenblue" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="bi bi-plus text-info"></i></button>
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
                            <th scope="col">Student Number</th>
                            <th scope="col">Subject</th>
                            <th scope="col">Messages</th>
                            <th scope="col">Date Submitted</th>
                            <th scope="col">Status</th>
                            <th scope="col">Remarks</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider">
                    <?php

                        $result = $connection->query("SELECT * FROM complaints ORDER BY id DESC");

                        if ($result->num_rows > 0) {
                            $count = 1; 

                            while ($row = $result->fetch_assoc()) {
                                echo '<tr>';
                                echo '<td>' . $count . '</td>';
                                echo '<td>' . $row['userid'] . '</td>';
                                echo '<td>' . $row['subjects'] . '</td>';
                                echo '<td>' . $row['messages'] . '</td>';
                                echo '<td>' . date("F d, Y", strtotime($row['submitted_at'])) . '</td>';
                                echo '<td>' . $row['status'] . '</td>';
                                echo '<td>' . $row['remarks'] . '</td>';
                                echo '<td>';
                                echo '<div class="d-flex justify-content-center">';

                                if ( $row['status'] == "Pending") {
                                echo '<button title="Mark as In Progress" class="btn btn-warning me-2" data-bs-toggle="modal" 
                                        data-bs-target="#progressModal" onclick="openProgressModal(' . $row['id'] . ')">
                                        <i class="bi bi-hourglass-split"></i></button>';
                                        
                                echo '<button title="Mark as Unresolved" class="btn btn-danger me-2" data-bs-toggle="modal" 
                                        data-bs-target="#unresolvedModal" onclick="openUnresolvedModal(' . $row['id'] . ')">
                                        <i class="bi bi-x"></i></button>';
                                }

                                if ( $row['status'] == "In Progress") {
                                echo '<button title="Mark as Resolved" class="btn btn-success me-2" data-bs-toggle="modal" 
                                        data-bs-target="#resolvedModal" onclick="openResolvedModal(' . $row['id'] . ')">
                                        <i class="bi bi-check"></i></button>';

                                echo '<button title="Mark as Unresolved" class="btn btn-danger me-2" data-bs-toggle="modal" 
                                        data-bs-target="#unresolvedModal" onclick="openUnresolvedModal(' . $row['id'] . ')">
                                        <i class="bi bi-x"></i></button>';
                                }



                                echo '<button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#editModal" onclick="loadData(' . $row['id'] . ')"><i class="bi bi-pencil-square"></i></button>';
                                echo '<button class="btn btn-danger" onclick="del(' . $row['id'] . ')"><i class="bi bi-trash"></i></button>';
                                echo '</div>';
                                echo '</td>';
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

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-gray">
                    <h5 class="modal-title text-light" id="addModalLabel">Add Complaint</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body bg-gray">
                    <form id="addForm">
                        <div class="mb-3">
                            <label for="userid" class="form-label text-light">Student Number</label>
                            <input type="number" class="form-control bg-dark text-light" id="userid" name="userid" placeholder="Enter student number" required>
                        </div>
                        <div class="mb-3">
                            <label for="subjects" class="form-label text-light">Subject</label>
                            <input type="text" class="form-control bg-dark text-light" id="subjects" name="subjects" placeholder="Write subject here" required>
                        </div>
                        <div class="mb-3">
                            <label for="messages" class="form-label text-light">Message</label>
                            <textarea class="form-control bg-dark text-light" id="messages" name="messages" rows="3" placeholder="Compose a message here" required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-gray">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveComplaintButton">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-gray">
                    <h5 class="modal-title text-light" id="editModalLabel">Edit Complaint</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body bg-gray">
                    <form id="editForm">
                        <input type="hidden" id="editId" name="id">
                        <div class="mb-3">
                            <label for="editUser" class="form-label text-light">Student Number</label>
                            <input type="text" class="form-control bg-dark text-light" id="editUser" name="userid" required>
                        </div>
                        <div class="mb-3">
                            <label for="editSubjects" class="form-label text-light">Subject</label>
                            <input type="text" class="form-control bg-dark text-light" id="editSubjects" name="subject" required>
                        </div>
                        <div class="mb-3">
                            <label for="editMessages" class="form-label text-light">Messages</label>
                            <input type="text" class="form-control bg-dark text-light" id="editMessages" name="messages" required>
                        </div>
                        <div class="mb-3">
                            <label for="editStatus" class="form-label text-light">Status</label>
                            <select name="status" id="editStatus" class="form-control bg-dark text-light">
                                <option selected disabled>Select Status</option>
                                <option value="Pending" <?php echo ($status == "Pending") ? "selected" : ""; ?>>Pending</option>
                                <option value="In Progress" <?php echo ($status == "In Progress") ? "selected" : ""; ?>>In Progress</option>
                                <option value="Resolved" <?php echo ($status == "Resolved") ? "selected" : ""; ?>>Resolved</option>
                                <option value="Unresolved" <?php echo ($status == "Unresolved") ? "selected" : ""; ?>>Unresolved</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this record?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
        <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
            <strong class="me-auto" id="toastTitle">Notice</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="toastMessage">
            <!-- Message will go here -->
            </div>
        </div>
    </div>

    <!-- Progress Modal -->
    <div class="modal fade" id="progressModal" tabindex="-1" aria-labelledby="progressModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="progressModalLabel">In Progress</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to mark this as in progress?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="progressButton">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Resolved Modal -->
    <div class="modal fade" id="resolvedModal" tabindex="-1" aria-labelledby="resolvedModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="resolvedModalLabel">Mark as Resolved</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="resolvedId">
                <div class="mb-3">
                <label for="resolvedRemarks" class="form-label">Remarks</label>
                <textarea class="form-control" id="resolvedRemarks" rows="4" placeholder="Enter remarks for the resolution..." required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmResolved">Submit</button>
            </div>
            </div>
        </div>
    </div>

    <!-- Unresolved Modal -->
    <div class="modal fade" id="unresolvedModal" tabindex="-1" aria-labelledby="unresolvedModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="unresolvedModalLabel">Unresolved</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to mark this as in unresolved?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="unresolvedButton">Confirm</button>
                </div>
            </div>
        </div>
    </div>
        

    <script>

        //--------------------------- Dynamic Toast Notification ---------------------------//
        function showDynamicToast(message, type) {
                const toastElement = document.getElementById('liveToast');
                const toastBody = document.getElementById('toastMessage');

                // Set the message
                toastBody.textContent = message;

                // Set the type (e.g., success, error)
                toastElement.className = `toast align-items-center border-0 text-bg-${type}`;

                // Show the toast
                const toast = new bootstrap.Toast(toastElement);
                toast.show();
            }

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

        //--------------------------- Add ---------------------------//
        $(document).ready(function () {
            $('#saveComplaintButton').on('click', function () {
                const userid = $('#userid').val();
                const subjects = $('#subjects').val();
                const messages = $('#messages').val();

                if (subjects.trim() === '' && messages.trim() === '' && userid.trim() === '') {
                    showDynamicToast('Please fill up the required fields.', 'warning');
                    return;
                }

                // Send data to the server
                $.ajax({
                    url: 'add_complaint.php',
                    type: 'POST',
                    data: { userid: userid, 
                        subjects: subjects,
                        messages: messages 
                     },

                    dataType: 'text',  // prevent jQuery from auto-parsing

                    success: function (response) {
                        const result = JSON.parse(response);

                        if (result.success) {
                            showDynamicToast('Record added successfully!', 'success');
                            setTimeout(() => location.reload(), 2000);
                        } else {
                            showDynamicToast('Error adding recrd: ' + result.message, 'danger');
                        }
                    },
                    error: function () {
                        showDynamicToast('An error occurred while adding the record.', 'danger');
                    },
                });
            });
        });

         //--------------------------- Edit  ---------------------------//
            function loadData(id) {
                $.ajax({
                    url: 'get_complaint.php',
                    type: 'POST',
                    data: { id: id },
                    success: function (response) {
                        const result = JSON.parse(response);

                        if (result.success) {
                            $('#editId').val(result.data.id);
                            $('#editUser').val(result.data.userid);
                            $('#editSubjects').val(result.data.subjects);
                            $('#editMessages').val(result.data.messages);
                            $('#editStatus').val(result.data.status);
                        } else {
                            showDynamicToast('Error fetching record data: ' + result.message, 'danger');
                        }
                    },
                    error: function () {
                        showDynamicToast('An error occurred while fetching the record data.', 'danger');
                    },
                });
            }

            // Handle the form submission for editing a service
            $('#editForm').on('submit', function (e) {
                e.preventDefault();

                const id = $('#editId').val();
                const userid = $('#editUser').val();
                const subjects = $('#editSubjects').val();
                const messages = $('#editMessages').val();
                const status = $('#editStatus').val();

                $.ajax({
                    url: 'update_complaint.php',
                    type: 'POST',
                    data: { id: id, userid: userid, subjects: subjects, messages: messages, status: status },
                    
                    success: function (response) {
                        const result = JSON.parse(response);

                        if (result.success) {
                            $('#editModal').modal('hide');
                            showDynamicToast('Record updated successfully!', 'success');

                            // Optionally reload the page after a short delay
                            setTimeout(() => location.reload(), 2000);
                        } else {
                            showDynamicToast('Error updating record: ' + result.message, 'danger');
                        }
                    },
                    error: function () {
                        showDynamicToast('An error occurred while updating the record.', 'danger');
                    },
                });
            });

            //--------------------------- Delete  ---------------------------//
            let complaintIdToDelete = null;

            function del(id) {
                complaintIdToDelete = id; // Store the service ID to delete
                const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
                deleteModal.show();
            }

            document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
                if (complaintIdToDelete) {
                    $.ajax({
                        url: 'delete_complaint.php',
                        method: 'POST',
                        data: { id: complaintIdToDelete },
                        dataType: 'json',
                        success: function (response) {
                            if (response.success) {
                                showDynamicToast('Record deleted successfully!', 'danger');
                                setTimeout(() => location.reload(), 3000); // Wait 3 seconds before refreshing
                            } else {
                                showDynamicToast('Error deleting record: ' + response.error, 'danger');
                            }
                        },
                        error: function () {
                            showDynamicToast('An error occurred while deleting the record.', 'danger');
                        },
                    });
                }
            });

        // Function to display the toast
        function showToast(message, className) {
            // Get the toast elements
            const toastMessage = document.getElementById('toastMessage');
            const toastElement = document.getElementById('liveToast');

            // Update the toast message and class
            toastMessage.textContent = message;
            toastElement.className = `toast align-items-center text-white ${className} border-0`;

            // Initialize and show the toast
            const toast = new bootstrap.Toast(toastElement);
            toast.show();
        }

        //---------------------------Progress ---------------------------//
        let progressIdToConfirm = null;

        function openProgressModal(id) {
            console.log("Opening modal for record ID:", id); // Debugging log
            progressIdToConfirm = id;
            const progressModal = new bootstrap.Modal(document.getElementById('progressModal'));
            progressModal.show();
        }

        // Event listener for the confirmation button
        document.getElementById('progressButton').addEventListener('click', function () {
            if (progressIdToConfirm) {
                $.ajax({
                    url: "inprogress.php",
                    method: "POST",
                    data: { id: progressIdToConfirm },
                    dataType: "json",
                    success: function(response) {
                        if (response.success) {
                            // Display the success toast
                            showToast(response.success, "bg-success");
                            setTimeout(() => location.reload(), 2000); // Optional delay before reload
                        } else {
                            // Display an error toast
                            showToast(response.error, "bg-danger");
                        }
                    },
                    error: function(xhr, status, error) {
                        // Handle errors from the AJAX request
                        showToast('Error updating the record', 'bg-danger');
                    }
                });
            }
        });

        //---------------------------Unresolved ---------------------------//
        let unresolvedIdToConfirm = null;

        function openUnresolvedModal(id) {
            console.log("Opening modal for record ID:", id); // Debugging log
            unresolvedIdToConfirm = id;
            const unresolvedModal = new bootstrap.Modal(document.getElementById('unresolvedModal'));
            unresolvedModal.show();
        }

        // Event listener for the confirmation button
        document.getElementById('unresolvedButton').addEventListener('click', function () {
            if (unresolvedIdToConfirm) {
                $.ajax({
                    url: "unresolved.php",
                    method: "POST",
                    data: { id: unresolvedIdToConfirm },
                    dataType: "json",
                    success: function(response) {
                        if (response.success) {
                            // Display the success toast
                            showToast(response.success, "bg-success");
                            setTimeout(() => location.reload(), 2000); // Optional delay before reload
                        } else {
                            // Display an error toast
                            showToast(response.error, "bg-danger");
                        }
                    },
                    error: function(xhr, status, error) {
                        // Handle errors from the AJAX request
                        showToast('Error updating the record', 'bg-danger');
                    }
                });
            }
        });

        //---------------------------Resolved ---------------------------//
        function openResolvedModal(id) {
            $('#resolvedId').val(id);
            $('#resolvedRemarks').val('');
        }
        $('#confirmResolved').on('click', function () {
            const id = $('#resolvedId').val();
            const remarks = $('#resolvedRemarks').val();

            if (!remarks.trim()) {
                showDynamicToast('Remarks are required.', 'danger');
                return;
            }

           $.ajax({
                url: 'resolve_complaint.php',
                method: 'POST',
                data: { id: id, remarks: remarks },
                success: function(response) {
                    console.log("Success response:", response);
                if (!response.success) {
                    showDynamicToast(response.message, 'danger');
                } else {
                    showDynamicToast("Complaint marked as resolved and email sent!", 'success');
                    // Wait 2 seconds then reload the page
                    setTimeout(function () {
                        location.reload();
                    }, 2000); // 2000 milliseconds = 2 seconds
                    }
            }
            });
        });

    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Check if the session has the update success flag set
            <?php if (isset($_SESSION['update_success'])): ?>
                var updateToast = new bootstrap.Toast(document.getElementById('updateToast'));
                updateToast.show();
                <?php unset($_SESSION['update_success']); // Clear the session variable after showing the toast ?>
            <?php endif; ?>
        });
    </script>

</body>
</html>