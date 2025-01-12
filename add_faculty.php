<?php
// Database connection
require 'config.php';


// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $facultyid = $_POST['facultyid'];
    $email = $_POST['email'];
    $department = $_POST['department'];
    echo $name,'', $facultyid,'', $email,'',$department;
    // Insert the data into the 'courses' table
    $stmt = $conn->prepare("INSERT INTO faculties (Name, StaffId, Email, Department) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $facultyid, $email, $department);

    if ($stmt->execute()) {
        echo "<script>alert('Faculty added successfully'); window.location.href= 'faculty.php';</script>";
        
    } else {
        echo "<script>alert('faculty already exists'); window.location.href= 'faculty.php';</script>";
    }

    $stmt->close();
}
?>
