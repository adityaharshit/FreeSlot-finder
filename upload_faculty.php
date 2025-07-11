<?php
include 'config.php'; // Include database connection and utility functions

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['faculty_csv']) && $_FILES['faculty_csv']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['faculty_csv']['tmp_name'];

        if (($handle = fopen($fileTmpPath, "r")) !== false) {
            // Skip the header row (if present)
            $headerSkipped = false;

            while (($row = fgetcsv($handle)) !== false) {
                if (!$headerSkipped) {
                    $headerSkipped = true;
                    continue;
                }

                // Get row data
                $staffid = trim($row[0]);
                $name = ucwords(strtolower(trim($row[1])));
                $department = trim($row[2]);
                $email = trim($row[3]);
                $designation = trim($row[4]);
                $yearsOfExperience = trim($row[5]);
                // echo "<script>console.log('Processing staffId: {$staffid}, Name: {$name}, Department: {$department}, Email: {$email}, Designation: {$designation}, Years of Experience: {$yearsOfExperience}');</script>";
                // Validate and normalize designation
                $valid_designations = ['Professor', 'Assistant Professor', 'Associate Professor', 'HOD', 'Lab Assistant'];
                $normalized_designation = ucwords(strtolower($designation));
                if (!in_array($normalized_designation, $valid_designations)) {
                    echo "<script>alert('Invalid designation: {$designation} for facultyId {$staffid}'); window.location.href = 'faculty.php';</script>";
                    exit;
                }
                $designation = $normalized_designation;

                // Normalize years of experience
                if (strpos(strtolower($yearsOfExperience), 'month') !== false) {
                    $yearsOfExperience = 1;
                } else {
                    $yearsOfExperience = ceil((float)filter_var($yearsOfExperience, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION));
                }

                $caderRatio = trim($row[6]);
                $contactNumber = trim($row[7]);
                // Skip rows with missing essential data
                if (empty($staffid) || empty($name) || empty($email) || empty($department) || empty($designation) || empty($yearsOfExperience) || empty($caderRatio) || empty($contactNumber)) {
                    continue;
                }
                $query = "Select * from faculties where StaffId = ? or Email = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param('ss', $staffid, $email);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows <= 0) {
                    // Faculty does not exist, proceed with insertion
                    // Insert into the faculties table
                    $query = "INSERT INTO faculties (Name, StaffId, Email, Department, Designation, YearsOfExperience, CaderRatio, ContactNumber) VALUES (?, ?,?,?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("sssssiis", $name, $staffid, $email, $department, $designation, $yearsOfExperience, $caderRatio, $contactNumber);
                    if ($stmt->execute()) {

                    } else {
                        echo $staffid, $name, $department, $email, $designation, $joiningDate;
                        $flag = True;
                        break;
                    }
                }
            }
            fclose($handle);
            if ($flag) {
                echo "<script>alert('Encountered some error');window.location.href = 'faculty.php'</script>";
            } else {
                echo "<script>alert('Faculty Details Updated Successfully');window.location.href = 'faculty.php'</script>";
                
            }
        } else {
            echo "<script>alert('Failed to open the CSV file.');window.location.href = 'faculty.php'</script>";
        }
    } else {
        echo "<script>alert('Error uploading file');window.location.href = 'faculty.php'</script>";
    }
} else {
    echo "<script>alert('Invalid request method');window.location.href = 'faculty.php'</script>";
}
?>