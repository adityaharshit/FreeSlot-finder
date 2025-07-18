<?php
// Database connection
require 'config.php';


// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $facultyid = $_POST['facultyid'];
    $email = $_POST['email'];
    $department = $_POST['department'];
    $designation = $_POST['designation'];
    $yearsOfExperience = $_POST['yearsOfExperience'];
    $caderValue = $_POST['caderValue'];
    $contactNumber = $_POST['contactNumber'];
    $stmt = $conn->prepare("INSERT INTO faculties (Name, StaffId, Email, Department, Designation, YearsOfExperience, CaderRatio, ContactNumber) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssiis", $name, $facultyid, $email, $department, $designation, $yearsOfExperience, $caderValue, $contactNumber);
    if ($stmt->execute()) {
        echo "<script>alert('Faculty added successfully'); window.location.href= 'faculty.php';</script>";
        
    } else {
        echo "<script>alert('faculty already exists'); window.location.href= 'faculty.php';</script>";
    }

    $stmt->close();
}
?>
