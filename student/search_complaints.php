<?php
require("../server/connection.php");
session_start();

if (isset($_SESSION["logged_in"]) && isset($_SESSION["userid"])) {
    $user = $_SESSION["userid"];
} else {
    echo '<tr><td colspan="5">Unauthorized access.</td></tr>';
    exit;
}

if (isset($_POST['query'])) {
    $query = mysqli_real_escape_string($connection, $_POST['query']);
    
    if (!empty($query)) {
        $sql = "SELECT * FROM complaints  
                WHERE userid = '$user' AND (
                    subjects LIKE '%$query%' OR 
                    messages LIKE '%$query%' OR
                    submitted_at LIKE '%$query%' OR
                    status LIKE '%$query%' OR
                    DATE_FORMAT(submitted_at, '%M %d, %Y') LIKE '%$query%' OR
                    DATE_FORMAT(submitted_at, '%m/%d/%Y') LIKE '%$query%'
                )
                ORDER BY id DESC";
    } else {
        $sql = "SELECT * FROM complaints WHERE userid = '$user' ORDER BY id DESC";
    }

    $result = mysqli_query($connection, $sql);

    if ($result && $result->num_rows > 0) {
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
}
?>
