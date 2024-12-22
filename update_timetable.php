<?php
// Database connection
require 'config.php';

$conn = new mysqli($servername, $username, $password, $dbname);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Path to the uploaded CSV file
$csvFile = 'timetable/MCA_3.csv'; // Replace with the actual path

// Open the CSV file
if (($handle = fopen($csvFile, "r")) !== false) {
    // Skip the first two rows (header and empty rows)
    fgetcsv($handle);
    fgetcsv($handle);

    // Read the timetable data (Rows 3-8)
    $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    $schedule = [];
    foreach ($days as $dayIndex => $day) {
        $row = fgetcsv($handle);
        $schedule[$day] = array_slice($row, 1); // Skip the "Day" column
    }

    // Skip empty rows until the subject-faculty mapping
    while (($row = fgetcsv($handle)) !== false) {
        if (trim($row[0]) === 'Subject name') break;
    }

    // Read subject-faculty mapping and process each faculty
    while (($row = fgetcsv($handle)) !== false) {
        $subject = $row[0];
        $faculty = $row[1];
        $email = $row[2];

        if (empty($subject) || empty($email)) {
            continue; // Skip rows without email
        }

        // Create a faculty schedule with empty strings as default
        $facultySchedule = array_fill_keys($days, '');

        // Populate schedule with actual periods
        foreach ($days as $dayIndex => $day) {
            $classes = $schedule[$day];
            foreach ($classes as $periodIndex => $class) {
                if ($class === $subject) {
                    if ($facultySchedule[$day] === '') {
                        $facultySchedule[$day] = (string)($periodIndex + 1); // First period
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
            foreach ($days as $day) {
                if (!empty($facultySchedule[$day])) {
                    $existing = $row[$day];
                    $newPeriods = explode(',', $facultySchedule[$day]);
                    $existingPeriods = $existing ? explode(',', $existing) : [];
                    $updatedPeriods = array_unique(array_merge($existingPeriods, $newPeriods));
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
        } else {
            // Insert new row
            $insertQuery = "
                INSERT INTO schedule (email, mon, tue, wed, thu, fri, sat)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param(
                "sssssss",
                $email,
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

    fclose($handle);
    echo "Schedule processed successfully.";
} else {
    echo "Failed to open the CSV file.";
}

// Close the database connection
$conn->close();
?>