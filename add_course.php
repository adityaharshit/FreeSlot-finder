<?php
// Database connection
require 'config.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course = $_POST['course'];
    $semester = $_POST['semester'];
    $sections = $_POST['sections'];

    // Insert the data into the 'courses' table
    $stmt = $conn->prepare("INSERT INTO courses (course, semester, sections) VALUES (?, ?, ?)");
    $stmt->bind_param("sii", $course, $semester, $sections);

    if ($stmt->execute()) {
        echo "<script>alert('Course added successfully'); window.location.href= 'course.php';</script>";
        
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>
