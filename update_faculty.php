<?php

include 'config.php';
include 'functions.php';
if (isset($_POST['updateFaculty'])) {
    global $conn;
    $staffID = $_POST['staffID'];
    $name = $_POST['Name'];
    $department = $_POST['Department'];
    $email = $_POST['Email'];
    $designation = $_POST['Designation'];
    $experience = $_POST['YearsOfExperience'];
    $cader = $_POST['CaderRatio'];
    $contact = $_POST['ContactNumber'];
    $query = "UPDATE faculties SET Name=?, Department=?, Email=?, Designation=?, YearsOfExperience=?, CaderRatio=?, ContactNumber=? WHERE StaffId=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssddss", $name, $department, $email, $designation, $experience, $cader, $contact, $staffID);

    if ($stmt->execute()) {
        echo "<script>alert('Faculty details upadted successfully'); window.location.href= 'faculty_duties.php';</script>";
    } else {
        echo "Error updating record: " . $stmt->error;
    }
}

?>