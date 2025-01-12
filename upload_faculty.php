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
                $name = trim($row[1]);
                $department = trim($row[2]);
                $email = trim($row[3]);

                // Skip rows with missing essential data
                if (empty($staffid) || empty($name) || empty($email) || empty($department)) {
                    continue;
                }

                // Insert into the faculties table
                $query = "INSERT INTO faculties (name, staffid, email, department) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ssss", $name, $staffid, $email, $department);
                $stmt->execute();
            }

            fclose($handle);
            echo "<script>alert('Faculty details uploaded successfully');window.location.href = 'faculty.php'</script>";
        } else {
            echo "Failed to open the CSV file.";
        }
    } else {
        echo "Error uploading file.";
    }
} else {
    echo "Invalid request method.";
}
?>
