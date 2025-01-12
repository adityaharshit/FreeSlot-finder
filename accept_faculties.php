<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedFaculties = $_POST['selectedFaculties']; // Array of faculty IDs
    $date = $_POST['date'];
    $session = $_POST['session'];

    $month = date('F', strtotime($date)); // Get the month from the date

    foreach ($selectedFaculties as $fid) {
        // Insert into the log table
        $logQuery = "INSERT INTO log (fid, Duty_date, Duty_Session) VALUES (?, ?, ?)";
        $logStmt = $conn->prepare($logQuery);
        $logStmt->bind_param("iss", $fid, $date, $session);
        $logStmt->execute();
    }

    echo "Slots successfully assigned to selected faculties.";
}
?>
