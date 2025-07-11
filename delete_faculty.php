<?php
require 'config.php'; // Database connection

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Delete the course
    $deleteQuery = "DELETE FROM faculties WHERE Fid = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Faculty deleted successfully'); window.location.href= 'faculty.php';</script>";
        exit;
    } else {
        echo $id;
        echo "Failed to delete Faculty.";
    }
} else {
    echo "Invalid request.";
}
?>
