<?php

$path = $_GET['file'];
// $path = 'timetable/MCA_1_A.csv';

delete_data($path);
delete_file($path);

echo "<script>
                    alert('File deleted successfully.');
                    window.location.href='dashboard.php';
                    </script>";

function delete_file($file)
{
    if (!unlink($file)) {
        echo ("$file cannot be deleted due to an error");
    } else {
        echo ("$file has been deleted");
    }
}

function delete_data($path)
{

    require 'config.php';


    if (($handle = fopen($path, "r")) !== false) {
        while (!feof($handle)) {
            echo "Processing schedule...<br>";
            // Skip the first rows (header and empty rows)
            while (($row = fgetcsv($handle)) !== false) {
                // echo $row[0];
                if (strpos(strToLower(trim($row[0])), 'day') !== false)
                    break;
            }

            // Read the timetable data (Rows 3-8)
            $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            $schedule = [];
            foreach ($days as $dayIndex => $day) {
                $row = fgetcsv($handle);
                if($row == false){
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
                if (empty($subject) && empty($email)) {
                    break;
                }
                if (empty($subject) || empty($email)) {
                    continue; // Skip rows without email
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
                $query = "SELECT * FROM schedule WHERE email = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    // Fetch existing schedule
                    $row = $result->fetch_assoc();
                    // echo "$email<br>";
                    foreach ($days as $day) {
                        if (!empty($facultySchedule[$day])) {
                            $existing = $row[$day];
                            $newPeriods = explode(',', $facultySchedule[$day]);
                            // echo '<pre>'; print_r($newPeriods); echo '</pre>';
                            $existingPeriods = $existing ? explode(',', $existing) : [];
                            $updatedPeriods = array_diff($existingPeriods, $newPeriods);
                            // echo '<pre>'; print_r($updatedPeriods); echo '</pre>';
                            // print_r($existingPeriods);
                            $facultySchedule[$day] = implode(',', $updatedPeriods);
                        } else {
                            $facultySchedule[$day] = $row[$day]; // Keep the existing value
                        }
                    }

                    // Update existing row
                    $updateQuery = "
                        UPDATE schedule SET
                        mon = ?, tue = ?, wed = ?, thu = ?, fri = ?, sat = ?
                        WHERE email = ?";
                    $updateStmt = $conn->prepare($updateQuery);
                    $updateStmt->bind_param(
                        "sssssss",
                        $facultySchedule['Mon'],
                        $facultySchedule['Tue'],
                        $facultySchedule['Wed'],
                        $facultySchedule['Thu'],
                        $facultySchedule['Fri'],
                        $facultySchedule['Sat'],
                        $email
                    );
                    $updateStmt->execute();
                    // echo "3<br>";
                }
            }
        }

        echo "Schedule processed successfully.";
    } else {
        echo "Failed to open the CSV file.";
    }
}
?>