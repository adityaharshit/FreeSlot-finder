<?php
include 'functions.php'; // Assuming database connection and utility functions are defined here

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rejectedFid = $_POST['rejectedFid']; // Rejected faculty ID
    renderFacultyTable( $rejectedFid);
}
?>
