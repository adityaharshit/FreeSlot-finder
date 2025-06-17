<?php
require 'config.php';

if (isset($_POST['faculty_id'])) {
    $faculty_id = $_POST['faculty_id'];

    // Fetch faculty details
    $query = "SELECT * FROM faculties WHERE Fid = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $faculty_id);
    $stmt->execute();
    $faculty = $stmt->get_result()->fetch_assoc();

    // Fetch duty details
    $query = "SELECT * FROM duties WHERE Fid = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $faculty_id);
    $stmt->execute();
    $duties = $stmt->get_result()->fetch_assoc();

    // Fetch duty log
    $query = "SELECT * FROM Log WHERE Fid = ? ORDER BY Duty_date DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $faculty_id);
    $stmt->execute();
    $log = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Display faculty details
    echo "<h3>Faculty Details</h3>";
    echo "<table class='table table-bordered'>";
    echo "<tr><th>Name</th><td>{$faculty['Name']}</td></tr>";
    echo "<tr><th>Faculty ID</th><td>{$faculty['FacultyId']}</td></tr>";
    echo "<tr><th>Email</th><td>{$faculty['Email']}</td></tr>";
    echo "<tr><th>Department</th><td>{$faculty['Department']}</td></tr>";
    echo "<tr><th>Designation</th><td>{$faculty['Designation']}</td></tr>";
    echo "<tr><th>Joining Date</th><td>{$faculty['JoiningDate']}</td></tr>";
    echo "</table>";

    // Display total duties per month
    echo "<h3>Total Duties per Month</h3>";
    echo "<table class='table table-bordered'>";
    echo "<thead><tr><th>Month</th><th>Duties</th></tr></thead>";
    echo "<tbody>";
    foreach ($duties as $month => $count) {
        if ($month !== 'Did' && $month !== 'Fid') {
            echo "<tr><td>$month</td><td>$count</td></tr>";
        }
    }
    echo "</tbody></table>";

    // Display duty log
    echo "<h3>Duty Log</h3>";
    echo "<table class='table table-bordered'>";
    echo "<thead><tr><th>Date</th><th>Session</th></tr></thead>";
    echo "<tbody>";
    foreach ($log as $entry) {
        echo "<tr><td>{$entry['Duty_date']}</td><td>{$entry['Duty_Session']}</td></tr>";
    }
    echo "</tbody></table>";
}
?>