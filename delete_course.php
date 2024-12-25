<?php
require 'config.php'; // Database connection

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Delete the course
    $deleteQuery = "DELETE FROM courses WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Course deleted successfully'); window.location.href= 'course.php';</script>";
        exit;
    } else {
        echo "Failed to delete course.";
    }
} else {
    echo "Invalid request.";
}
?>
