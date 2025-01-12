<?php
session_start();
function getAvailableFaculties($day, $hours, $num, $date)
{
    global $conn; // Database connection
    $hoursStr = implode(',', $hours);
    $month = date('F', strtotime($date));
    $month = strtolower($month);
    $_SESSION['fetched_faculty'] = array();
    $_SESSION['limit'] = 0;
    $_SESSION['fetched_faculty_less_hours'] = array();
    $_SESSION['limit_extra'] = 0;
    echo "<script>console.log('$month')</script>";
    echo "<script>console.log('$day')</script>";
    $query = "
        SELECT f.fid, f.name, f.department, d.$month
        FROM faculties f
        JOIN duties d ON f.fid = d.fid 
        WHERE f.fid NOT IN (
            SELECT fid
            FROM schedule
            WHERE $day IN ($hoursStr)
        )
        ORDER BY d.$month ASC
    ";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $fetched_faculty = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    if (count($fetched_faculty) < $num) {
        // echo $num-count($fetched_faculty);
        getAvailableFacultiesWithLessHours($day, $hours, $num - count($fetched_faculty), $date);
    }
    $_SESSION['fetched_faculty'] = $fetched_faculty;
    $_SESSION['limit'] = $num;
    return $fetched_faculty;
}

function getAvailableFacultiesWithLessHours($day, $hours, $num, $date)
{
    global $conn; // Database connection
    $hoursStr = implode(',', $hours);
    $month = date('F', strtotime($date));
    $month = strtolower($month);
    $hour = array();
    if ($hours[0] == 1) {
        $hour[0] = implode(',', array(1, 2, 4));
        $hour[1] = implode(',', array(1, 2, 5));
        $hour[2] = implode(',', array(1, 4, 5));
        $hour[3] = implode(',', array(2, 4, 5));
    } else {
        $hour[0] = implode(',', array(7, 8, 9));
        $hour[1] = implode(',', array(7, 8, 10));
        $hour[2] = implode(',', array(7, 9, 10));
        $hour[3] = implode(',', array(8, 9, 10));
    }
    echo "<script>console.log('$month')</script>";
    echo "<script>console.log('$day')</script>";
    $query = "
        SELECT f.fid, f.name, f.department, d.$month
        FROM faculties f
        JOIN duties d ON f.fid = d.fid join schedule s on f.fid = s.fid
        WHERE f.fid IN (
            SELECT fid
            FROM schedule
            WHERE ($day not IN ($hour[0]) OR $day not IN ($hour[1]) OR $day  not IN ($hour[2]) OR $day not IN ($hour[3])) and $day IN ($hoursStr)
        )
        ORDER BY d.$month ASC
    ";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $fetched_faculty = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $_SESSION['fetched_faculty_less_hours'] = $fetched_faculty;
    $_SESSION['extra_limit'] = $num;
    // print value of fetched_faculty in a formatted manner
    // foreach ($fetched_faculty as $index => $faculty) {
    //     echo "Index $index: ";
    //     print_r($faculty);
    //     echo "<br>";
    // }
    // echo $num;  
    return $fetched_faculty;
}



function renderFacultyTable($facid)
{
    $faculties = $_SESSION['fetched_faculty'];
    $limit = $_SESSION['limit'];
    $ind = -1;
    if ($facid !== -1)
        for ($i = 1; $i <= $limit; $i++) {
            if ($faculties[$i]['fid'] == $facid) {
                $ind = $i;
                break;
            }
        }

    if ($ind != -1)
        array_splice($faculties, $ind, 1);
    $_SESSION['fetched_faculty'] = $faculties;

    echo "<table border='1' class='table table-bordered table-striped mt-3 faculty-table'>
            <tr>
                <th>S.No</th>
                <th>Name</th>
                <th>Action</th>
            </tr>";
    $i = 0;
    for ($i = 0; $i < min([$limit, count($faculties)]); $i++) {
        echo "<tr class='faculty-row' data-fid='{$faculties[$i]['fid']}'>
                <td>{$i}</td>
                <td>{$faculties[$i]['name']} ({$faculties[$i]['department']})</td>
                <td>
                    <button class='reject-faculty' data-fid='{$faculties[$i]['fid']}'>Reject</button>
                </td>
            </tr>";
    }
    echo "</table>";
    echo "<button class='accept-all btn btn-primary'>Accept All</button>";
    if ($i < $limit) {
        $_SESSION['extra_limit'] = $limit - $i;
        renderExtraFacultyTable(-1);
    }
}

function renderExtraFacultyTable($facid)
{
    $faculties = $_SESSION['fetched_faculty_less_hours'];
    $limit = $_SESSION['extra_limit'];
    $ind = 0;
    if ($facid !== -1)
        for ($i = 1; $i <= $limit; $i++) {
            if ($faculties[$i]['fid'] == $facid) {
                $ind = $i;
                break;
            }
        }
    array_splice($faculties, $ind, 1);
    $_SESSION['fetched_faculty_less_hours'] = $faculties;
    echo "<h2 class='mt-5'>Faculties who would miss lectures</h2>";
    echo "<table border='1' class='table table-bordered table-striped mt-3 faculty-table-extra'>
            <tr>
                <th>S.No</th>
                <th>Name</th>
                <th>Action</th>
            </tr>";
    for ($i = 0; $i < min([$limit, count($faculties)]); $i++) {
        echo "<tr class='faculty-row-extra' data-fid='{$faculties[$i]['fid']}'>
                <td>{$i}</td>
                <td>{$faculties[$i]['name']} ({$faculties[$i]['department']})</td>
                <td>
                    <button class='reject-faculty-extra' data-fid='{$faculties[$i]['fid']}'>Reject</button>
                </td>
            </tr>";
    }
    echo "</table>";
    echo "<button class='accept-all-extra btn btn-primary'>Accept All</button>";
}



function renderDuties($duty_date, $duty_session)
{
    require 'config.php';

    $query = "select f.name, f.department, f.email from faculties f join Log l on f.fid = l.fid where l.Duty_date = ? and l.Duty_session = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $duty_date, $duty_session);
    $stmt->execute();
    $faculties = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    if (empty($faculties)) {
        return "<h2 class='mt-5'>No faculties assigned duty for that day</h2>";
    }
    
    // Build the HTML output
    $html = "<h2 class='mt-5'>Faculties assigned duty for that day</h2>";
    $html .= "<table border='1' class='table table-bordered table-striped mt-3 faculty-table-extra'>
                <tr>
                    <th>S.No</th>
                    <th>Name</th>
                    <th>Email</th>
                </tr>";

    for ($i = 0; $i < count($faculties); $i++) {
        $html .= "<tr class='''>
                    <td>{$i}</td>
                    <td>{$faculties[$i]['name']} ({$faculties[$i]['department']})</td>
                    <td>{$faculties[$i]['email']}</td>
                </tr>";
    }

    $html .= "</table>";

    return $html;
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['functionname'])) {
    if ($_POST['functionname'] === 'renderDuties') {
        $duty_date = $_POST['date'];
        $duty_session = $_POST['session'];
        echo renderDuties($duty_date, $duty_session);
    }
}

































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
                if ($result->num_rows > 0) {
                    // print_r( $result);
                    $result = $result->fetch_assoc();
                    $fid = $result['Fid'];
                } else {
                    echo "<script>alert('Faculty named $faculty does not exist. Please delete and reupload the file after updating the database.</script>";
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
                $query = "SELECT * FROM schedule WHERE fid = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("s", $fid);
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
                }
            }
        }

        echo "Schedule processed successfully.";
    } else {
        echo "Failed to open the CSV file.";
    }
}
?>