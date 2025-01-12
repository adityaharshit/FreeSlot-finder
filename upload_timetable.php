<?php
require 'functions.php';
if (isset($_POST['submit'])) {
    // Get the selected department and semester
    $department = $_POST['department'];
    $semester = $_POST['semester'];
    $sections = $_POST['section'];

    // Check if a file was uploaded
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['file']['tmp_name'];
        $fileName = $_FILES['file']['name'];
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

        // Validate file extension
        if ($fileExtension === 'csv') {
            // Define the directory and new file name
            $uploadDir = __DIR__ . '/timetable/';
            // if $sections is not empty, append it to the filename
            if ($sections !== 'select') {
                $newFileName = $department . '_' . $semester . '_' . $sections . '.csv';
            } else {
                $newFileName = $department . '_' . $semester . '.csv';
            }
            $destPath = $uploadDir . $newFileName;

            // Create the timetable directory if it doesn't exist
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }


            // check whether file with same name exists or not
            if (file_exists($destPath)) {
                echo "<script>
                alert('File with the same name already exists.');
                window.location.href='dashboard.php';
                </script>";
            } else {


                // Move the uploaded file to the destination
                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    $res = update_timetable($destPath);
                    echo $res;
                    if($res[0] === -1){
                        echo $newFileName;
                        // delete_timetable.php?file=timetable/MCA_1_A.csv
                        echo "<script>
                        alert('faculty $res[1] ($res[2]) details do not exist. Please reupload the file after updating the database.');
                        </script>";
                        delete_data($destPath);
                        delete_file($destPath);
                        echo"<script>window.location.href='dashboard.php';</script>";
                    }else{
                        echo "<script>
                        alert('File uploaded and saved as $newFileName in the timetable directory.');
                        window.location.href='dashboard.php';
                        </script>";
                    }

                } else {
                    echo "<script>
                alert('There was an error moving the uploaded file.');
                window.location.href='dashboard.php';
                </script>";
                }
            }
        } else {
            echo "<script>
            alert('Only CSV files are allowed.');
            window.location.href='dashboard.php';
            </script>";
        }
    } else {
        echo "<script>
        alert('No file uploaded or there was an error during the upload.');
        window.location.href='dashboard.php';
        </script>";
    }
} else {
    echo "<script>
        alert('Invalid request.');
        window.location.href='dashboard.php';
        </script>";
}





function update_timetable($path)
{


    // Database connection
    require 'config.php';


    // Path to the uploaded CSV file
// $csvFile = 'timetable/UPDATED 3 SEM TT .csv'; // Replace with the actual path
    // Open the CSV file
    if (($handle = fopen($path, "r")) !== false) {
        $count=0;
        while (!feof($handle)) {
            // echo "Processing schedule...<br>";
            // Skip the first rows (header and empty rows)
            while (($row = fgetcsv($handle)) !== false) {
                // echo $row[0];
                // echo "1<br>";
                if (strpos(strToLower(trim($row[0])), 'day') !== false)
                    break;
            }
            // echo "2<br>";

            // Read the timetable data (Rows 3-8)
            $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            $schedule = [];
            foreach ($days as $dayIndex => $day) {
                $row = fgetcsv($handle);
                // echo $row
                // echo $row[0];
                if ($row == false) {
                    break;
                }
                $schedule[$day] = array_slice($row, 1); // Skip the "Day" column
            }

            // Skip empty rows until the subject-faculty mapping
            while (($row = fgetcsv($handle)) !== false) {
                if (trim($row[0]) === 'Subject')
                    break;
            }
            // Read subject-faculty mapping and process each faculty
            while (($row = fgetcsv($handle)) !== false) {
                $subject = $row[0];
                $faculty = $row[2];
                $email = $row[3];
                // echo $subject, $email;
                if (empty($subject) && empty($email)) {
                    break;
                }
                if (empty($subject) || empty($email)) {
                    continue; // Skip rows without email
                }

                $query = "SELECT * FROM faculties WHERE email = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();
                if($result->num_rows > 0){
                    $res = $result->fetch_assoc();
                    $fid = $res['Fid'];
                }else{
                    echo "<script>console.log('Faculty named $faculty $email does not exist. Please delete and reupload the file after updating the database.</script>";
                    return [-1, $faculty, $email];
                }


                // Create a faculty schedule with empty strings as default
                $facultySchedule = array_fill_keys($days, '');

                // Populate schedule with actual periods
                foreach ($days as $dayIndex => $day) {
                    $classes = $schedule[$day];
                    foreach ($classes as $periodIndex => $class) {
                        if (strpos($class, $subject) !== false) {
                            if ($facultySchedule[$day] === '') {
                                $facultySchedule[$day] = (string) ($periodIndex + 1); // First period
                            } else {
                                $facultySchedule[$day] .= ',' . ($periodIndex + 1); // Subsequent periods
                            }
                        }
                    }
                }

                // Check if the faculty already exists in the database
                $query = "SELECT * FROM schedule WHERE Fid = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("s", $fid);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    // Fetch existing schedule
                    $row = $result->fetch_assoc();
                    foreach ($days as $day) {
                        if (!empty($facultySchedule[$day])) {
                            $existing = $row[$day];
                            $newPeriods = explode(',', $facultySchedule[$day]);
                            $existingPeriods = $existing ? explode(',', $existing) : [];
                            $updatedPeriods = array_unique(array_merge($existingPeriods, $newPeriods));
                            sort($updatedPeriods);
                            $facultySchedule[$day] = implode(',', $updatedPeriods);
                        } else {
                            $facultySchedule[$day] = $row[$day]; // Keep the existing value
                        }
                    }

                    // Update existing row
                    $updateQuery = "
                        UPDATE schedule SET
                        mon = ?, tue = ?, wed = ?, thu = ?, fri = ?, sat = ?
                        WHERE Fid = ?";
                    $updateStmt = $conn->prepare($updateQuery);
                    $updateStmt->bind_param(
                        "sssssss",
                        $facultySchedule['Mon'],
                        $facultySchedule['Tue'],
                        $facultySchedule['Wed'],
                        $facultySchedule['Thu'],
                        $facultySchedule['Fri'],
                        $facultySchedule['Sat'],
                        $fid
                    );
                    $updateStmt->execute();
                    // echo "3<br>";
                } else {
                    // Insert new row
                    $insertQuery = "
                INSERT INTO schedule (Fid, mon, tue, wed, thu, fri, sat)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
                    $insertStmt = $conn->prepare($insertQuery);
                    $insertStmt->bind_param(
                        "sssssss",
                        $fid,
                        $facultySchedule['Mon'],
                        $facultySchedule['Tue'],
                        $facultySchedule['Wed'],
                        $facultySchedule['Thu'],
                        $facultySchedule['Fri'],
                        $facultySchedule['Sat']
                    );
                    $insertStmt->execute();
                }
            }
        }

        fclose($handle);
        echo "Schedule processed successfully.";
        return 1;
    } else {
        echo "Failed to open the CSV file.";
        
    }
}
?>