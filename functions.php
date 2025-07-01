<?php
session_start();
require 'config.php';

function getFaculties($day, $session, $num, $date)
{
    global $conn, $limit; // Database connection
    $hours = $session === 'morning' ? [1, 2, 4, 5] : [7, 8, 9, 10];
    $month = date('F', strtotime($date));
    $month = strtolower($month);


    // Get average
    $query = "SELECT ceil(avg($month)) as avg from duties";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $limit = $stmt->get_result()->fetch_assoc()['avg'];


    // Check is we have enough faculties or not
    $query = "SELECT count(*) as count from duties d inner join faculties f on d.fid = f.fid 
        where d.$month<=$limit and f.designation in ('Assistant Professor', 'Associate Professor')";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $count = $stmt->get_result()->fetch_assoc()['count'];

    if ($count < $num)
        $limit = $limit + 3;
    $query = "
        SELECT
        f.Fid, f.name, f.department,f.YearsOfExperience, d.$month,
        s.$day,
        (LENGTH($day) - LENGTH(REPLACE($day, '$hours[0]', '')) > 0) +
        (LENGTH($day) - LENGTH(REPLACE($day, '$hours[1]', '')) > 0) +
        (LENGTH($day) - LENGTH(REPLACE($day, '$hours[2]', '')) > 0) +
        (LENGTH($day) - LENGTH(REPLACE($day, '$hours[3]', '')) > 0) AS MatchCount
        FROM faculties f JOIN duties d ON f.fid = d.fid 
        JOIN schedule s on f.fid = s.fid where d.$month < $limit and 
        f.designation not in ('HOD', 'Professor') and f.fid not in 
        (select fid from Log where Duty_date = '$date' and Role = 'Reliever' 
        and Duty_Session = '$session') and d.$month <= $limit 
        order by MatchCount ASC, f.Designation ASC, d.$month ASC, f.YearsOfExperience ASC limit $num;
    ";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $fetched_faculty = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    return $fetched_faculty;
}
function getRelievers($day, $session, $num, $date)
{
    global $conn, $limit; // Database connection
    $hours = $session === 'morning' ? [1, 2, 4, 5] : [7, 8, 9, 10];
    $month = date('F', strtotime($date));
    $month = strtolower($month);

    // Get average
    $query = "SELECT ceil(avg($month)) as avg from duties d inner join faculties f on d.fid = f.fid where f.designation in ('Associate Professor', 'Professor')";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $limit = $stmt->get_result()->fetch_assoc()['avg'];


    // Check is we have enough faculties or not
    $query = "SELECT count(*) as count from duties d inner join faculties f on d.fid = f.fid where d.$month<=$limit and f.designation in ('Professor', 'Associate Professor')";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $count = $stmt->get_result()->fetch_assoc()['count'];

    if ($count < $num)
        $limit = $limit + 3;
    $query = "
        SELECT f.Fid, f.name, f.department,f.YearsOfExperience,f.Designation, d.$month, s.$day, 
        ifnull((LENGTH(s.$day) - LENGTH(REPLACE(s.$day, '$hours[0]', '')) > 0) + 
        (LENGTH(s.$day) - LENGTH(REPLACE(s.$day, '$hours[1]', '')) > 0) + 
        (LENGTH(s.$day) - LENGTH(REPLACE(s.$day, '$hours[2]', '')) > 0) + 
        (LENGTH(s.$day) - LENGTH(REPLACE(s.$day, '$hours[3]', '')) > 0),0) AS MatchCount 
        FROM faculties f JOIN duties d ON f.fid = d.fid 
        JOIN schedule s on f.fid = s.fid 
        where d.$month < 20 and f.designation in ('Professor', 'Associate Professor') and 
        d.$month <= $limit order by MatchCount ASC, f.Designation DESC, d.$month ASC, 
        f.YearsOfExperience DESC limit $num;
    ";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $fetched_faculty = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    return $fetched_faculty;
}


function logFacultiesToCSV($file, $faculties, $date, $session, $role)
{
    foreach ($faculties as $faculty) {
        fputcsv($file, [$date, $session, $role, $faculty['Fid'], $faculty['name'], $faculty['department']]);
    }
}


function generateTableRows($faculties, $session, $role)
{
    $rows = "";
    foreach ($faculties as $faculty) {
        $rows .= "<tr>
                    <td>{$faculty['Fid']}</td>
                    <td>{$faculty['name']}</td>
                    <td>{$faculty['department']}</td>
                    <td>{$session}</td>
                    <td>{$role}</td>
                </tr>";
    }
    return $rows;
}


function checkIfAlreadyScheduled($year, $monthNumber){
    global $conn;
    $query = "SELECT * FROM log where year(Duty_date) = ? and month(Duty_date) = ? and ExamType = 'SEE'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $year, $monthNumber);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    if(count($res) > 0){
        return true;
    }
    return false;

}

function clearData($year, $monthNumber)
{
    global $conn;
    $query = "DELETE FROM log WHERE year(Duty_date) = ? and month(Duty_date) = ? and ExamType = 'SEE'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $year, $monthNumber);
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}

function getFacultyDetails($staffID)
{
    global $conn;
    $query = "SELECT * FROM faculties WHERE StaffID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $staffID);
    $stmt->execute();
    $faculties = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $formFields = "";
    foreach ($faculties as $faculty) {
        $formFields .= "<div class='mb-2'>
                          <label>Staff ID</label>
                          <input type='text' class='form-control' name='StaffId' value='{$faculty['StaffId']}' readonly>
                        </div>";
        $formFields .= "<div class='mb-2'>
                          <label>Name</label>
                          <input type='text' class='form-control' name='Name' value='{$faculty['Name']}' required>
                        </div>";
        $formFields .= "<div class='mb-2'>
                          <label>Department</label>
                          <input type='text' class='form-control' name='Department' value='{$faculty['Department']}' required>
                        </div>";
        $formFields .= "<div class='mb-2'>
                          <label>Email</label>
                          <input type='text' class='form-control' name='Email' value='{$faculty['Email']}' required>
                        </div>";
        $formFields .= "<div class='mb-2'>
                          <label>Designation</label>
                          <input type='text' class='form-control' name='Designation' value='{$faculty['Designation']}' required>
                        </div>";
        $formFields .= "<div class='mb-2'>
                          <label>Years Of Experience</label>
                          <input type='number' class='form-control' name='YearsOfExperience' value='{$faculty['YearsOfExperience']}' required>
                        </div>";
        $formFields .= "<div class='mb-2'>
                          <label>Cader Ratio</label>
                          <input type='number' step='0.01' class='form-control' name='CaderRatio' value='{$faculty['CaderRatio']}' required>
                        </div>";
        $formFields .= "<div class='mb-2'>
                          <label>Contact Number</label>
                          <input type='text' class='form-control' name='ContactNumber' value='{$faculty['ContactNumber']}' required>
                        </div>";
    }
    return $formFields;
}



function getSchedule($date)
{
    global $conn;
    $query = "SELECT f.fid,f.StaffID, f.name, f.department, l.role, l.Duty_Session from log l inner join faculties f on l.fid = f.fid where l.Duty_date = ? and l.ExamType = 'SEE' order by l.Duty_Session desc, role desc";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $faculties = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $rows = "";
    foreach ($faculties as $faculty) {
        $rows .= "<tr>
                    <td>{$faculty['StaffID']}</td>
                    <td>{$faculty['name']}</td>
                    <td>{$faculty['department']}</td>
                    <td>{$faculty['Duty_Session']}</td>
                    <td>{$faculty['role']}</td>
                </tr>";
    }
    return $rows;
}


function getScheduleCie($date)
{
    global $conn;
    $query = "SELECT f.fid,f.StaffID, f.name, f.department, l.role, l.Duty_Session from log l inner join faculties f on l.fid = f.fid where l.Duty_date = ? and l.ExamType = 'CIE' order by l.Duty_Session desc, role desc";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $faculties = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $rows = "";
    foreach ($faculties as $faculty) {
        $rows .= "<tr>
                    <td>{$faculty['StaffID']}</td>
                    <td>{$faculty['name']}</td>
                    <td>{$faculty['department']}</td>
                    <td>{$faculty['Duty_Session']}</td>
                    <td>{$faculty['role']}</td>
                </tr>";
    }
    return $rows;
}

function getDutiesSee($date, $staffid, $wholeYear)
{
    global $conn;
    if($wholeYear){
        $query = "SELECT f.fid,f.StaffID, f.name, f.department,l.Duty_date, l.role, l.Duty_Session from log l inner join faculties f on l.fid = f.fid where f.StaffID = ? and year(l.Duty_date) = ? and l.ExamType = 'SEE' order by l.Duty_Session desc, role desc";
    }else{
        $query = "SELECT f.fid,f.StaffID, f.name, f.department,l.Duty_date,l.role, l.Duty_Session from log l inner join faculties f on l.fid = f.fid where f.StaffID = ? and l.Duty_date = ? and l.ExamType = 'SEE' order by l.Duty_Session desc, role desc";
    }
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss",$staffid, $date);
    $stmt->execute();
    $faculties = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $rows = "";
    foreach ($faculties as $faculty) {
        $rows .= "<tr>
                    <td>{$faculty['StaffID']}</td>
                    <td>{$faculty['name']}</td>
                    <td>{$faculty['department']}</td>";
                    if($wholeYear)
                        $rows .= "<td>{$faculty['Duty_date']}</td>";

        $rows .= "
                    <td>{$faculty['Duty_Session']}</td>
                    <td>{$faculty['role']}</td>
                </tr>";
    }
    return $rows;
}
function getDutiesCie($date, $staffid, $wholeYear)
{
    global $conn;
    if($wholeYear){   
        $query = "SELECT f.fid,f.StaffID, f.name, f.department,l.Duty_date, l.role, l.Duty_Session from log l inner join faculties f on l.fid = f.fid where f.StaffID = ? and year(l.Duty_date) = ? and l.ExamType = 'CIE' order by l.Duty_Session desc, role desc";
    }else{
        $query = "SELECT f.fid,f.StaffID, f.name, f.department,l.Duty_date, l.role, l.Duty_Session from log l inner join faculties f on l.fid = f.fid where f.StaffID = ? and l.Duty_date = ? and l.ExamType = 'CIE' order by l.Duty_Session desc, role desc";
    }
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss",$staffid, $date);
    $stmt->execute();
    $faculties = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $rows = "";
    foreach ($faculties as $faculty) {
        $rows .= "<tr>
                    <td>{$faculty['StaffID']}</td>
                    <td>{$faculty['name']}</td>
                    <td>{$faculty['department']}</td>";
                    if($wholeYear)
                        $rows .= "<td>{$faculty['Duty_date']}</td>";

        $rows .= "
                    <td>{$faculty['Duty_Session']}</td>
                    <td>{$faculty['role']}</td>
                </tr>";
    }
    return $rows;
}








function getFacultiesForOneDay($day, $session, $num, $date)
{
    global $conn, $limit; // Database connection
    $hours = $session === 'morning' ? [1, 2, 4, 5] : [7, 8, 9, 10];
    $month = date('F', strtotime($date));
    $month = strtolower($month);


    // Get average
    $query = "SELECT ceil(avg($month)) as avg from duties";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $limit = $stmt->get_result()->fetch_assoc()['avg'];


    // Check is we have enough faculties or not
    $query = "SELECT count(*) as count from duties d inner join faculties f on d.fid = f.fid 
        where d.$month<=$limit and f.designation in ('Assistant Professor', 'Associate Professor')";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $count = $stmt->get_result()->fetch_assoc()['count'];

    if ($count < $num)
        $limit = $limit + 3;
    // echo "<script>alert('$limit'+ ' $num'+' $count')</script>";
    echo "<script>alert('$date'+' $day'+' $session'+' $month')</script>";
    $query = "
        SELECT
        f.Fid, f.name, f.department,f.YearsOfExperience, d.$month,
        s.$day,
        (LENGTH($day) - LENGTH(REPLACE($day, '$hours[0]', '')) > 0) +
        (LENGTH($day) - LENGTH(REPLACE($day, '$hours[1]', '')) > 0) +
        (LENGTH($day) - LENGTH(REPLACE($day, '$hours[2]', '')) > 0) +
        (LENGTH($day) - LENGTH(REPLACE($day, '$hours[3]', '')) > 0) AS MatchCount
        FROM faculties f JOIN duties d ON f.Fid = d.fid 
        JOIN schedule s on f.Fid = s.fid where
        f.designation not in ('HOD', 'Professor') and f.Fid not in 
        (select fid from Log where Duty_date = '$date' 
        and Duty_Session = '$session') and d.$month <= $limit 
        order by MatchCount ASC, f.Designation ASC, d.$month ASC, f.YearsOfExperience ASC limit $num;
    ";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $fetched_faculty = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $_SESSION['fetched_faculty'] = $fetched_faculty;
    $_SESSION['limit'] = $num;
    $c = count($fetched_faculty);
    echo "<script>alert('$c')</script>";
    return $fetched_faculty;
}






function getAvailableFacultiesWithLessHours($day, $hours, $num, $date)
{
    global $conn, $limit; // Database connection
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
        )and d.$month < $limit
        ORDER BY d.$month ASC, f.JoiningDate DESC
    ";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $fetched_faculty = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $_SESSION['fetched_faculty_less_hours'] = $fetched_faculty;
    $_SESSION['extra_limit'] = $num;
    return $fetched_faculty;
}



function renderFacultyTable($facid)
{
    $faculties = $_SESSION['fetched_faculty'];
    $limit = $_SESSION['limit'];
    $ind = -1;
    if ($facid !== -1)
        for ($i = 1; $i <= $limit; $i++) {
            if ($faculties[$i]['Fid'] == $facid) {
                $ind = $i;
                break;
            }
        }

    if ($ind != -1)
        array_splice($faculties, $ind, 1);
    $_SESSION['fetched_faculty'] = $faculties;
    $c = count($faculties);
    // echo "<script>alert('$limit'+' $c')</script>";
    echo "<table border='1' class='table table-bordered table-striped mt-3 faculty-table'>
            <tr>
                <th>S.No</th>
                <th>Name</th>
                <th>Action</th>
            </tr>";
    $i = 0;
    for ($i = 0; $i < min([$limit, count($faculties)]); $i++) {
        echo "<tr class='faculty-row' data-fid='{$faculties[$i]['Fid']}'>
                <td>{$i}</td>
                <td>{$faculties[$i]['name']} ({$faculties[$i]['department']})</td>
                <td>
                    <button class='reject-faculty' data-fid='{$faculties[$i]['Fid']}'>Reject</button>
                </td>
            </tr>";
    }
    echo "</table>";
    echo "<button class='accept-all btn btn-primary'>Accept All</button>";
    // if ($i < $limit) {
    //     $_SESSION['extra_limit'] = $limit - $i;
    //     renderExtraFacultyTable(-1);
    // }
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


function acceptFaculties($faculties, $date, $session, $role)
{
    global $conn;
    $month = date('F', strtotime($date)); // Get the month from the date

    foreach ($faculties as $fid => $faculty) {
        // Insert into the log table
        $logQuery = "INSERT INTO log (fid, Duty_date, Duty_Session, Role) VALUES (?, ?, ?, ?)";
        $logStmt = $conn->prepare($logQuery);
        $logStmt->bind_param("isss", $faculty['Fid'], $date, $session, $role);
        $logStmt->execute();
    }
}


function getDaysInMonth($month)
{
    $year = date("Y"); // Assuming the current year
    $monthNumber = date("n", strtotime($month)); // Convert month name to numeric
    return cal_days_in_month(CAL_GREGORIAN, $monthNumber, $year);
}

// Function to check if a given day is Sunday
function isSunday($year, $month, $day)
{
    return date('N', strtotime("$year-$month-$day")) == 7; // Sunday corresponds to '7'
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
function get_departments()
{
    global $conn;
    $query = "SELECT DISTINCT course FROM courses";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
    $departments = [];
    while ($row = $result->fetch_assoc()) {
        $departments[] = $row['course'];
    }
    return $departments;
}

function getFacultiesForCIE($day, $session, $num, $date, $departments)
{
    global $conn, $limit; // Database connection
    $hours = $session === 'morning' ? [1, 2, 4, 5] : [7, 8, 9, 10];

    $month = date('F', strtotime($date));
    $month = strtolower($month);


    // Get average
    $query = "SELECT ceil(avg($month)) as avg from dutiescie";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $limit = $stmt->get_result()->fetch_assoc()['avg'];


    // Check is we have enough faculties or not
    $query = "SELECT count(*) as count from dutiescie d inner join faculties f on d.fid = f.fid 
        where d.$month<=$limit and f.designation not in ('HOD')";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $count = $stmt->get_result()->fetch_assoc()['count'];

    if ($count < $num)
        $limit = $limit + 3;
    // Convert departments array to placeholders
    $placeholders = implode(',', array_fill(0, count($departments), '?'));

    // Build SQL
    $query = "
    SELECT
        f.Fid, f.name, f.department, f.YearsOfExperience, d.$month,
        s.$day,
        (LENGTH(s.$day) - LENGTH(REPLACE(s.$day, ?, ''))) > 0 +
        (LENGTH(s.$day) - LENGTH(REPLACE(s.$day, ?, ''))) > 0 +
        (LENGTH(s.$day) - LENGTH(REPLACE(s.$day, ?, ''))) > 0 +
        (LENGTH(s.$day) - LENGTH(REPLACE(s.$day, ?, ''))) > 0 AS MatchCount
    FROM faculties f
    JOIN dutiescie d ON f.fid = d.fid
    JOIN schedule s ON f.fid = s.fid
    WHERE f.department IN ($placeholders)
      AND f.designation NOT IN ('HOD')
      AND d.$month <= ?
    ORDER BY MatchCount ASC, f.Designation ASC, d.$month ASC, f.YearsOfExperience ASC
    LIMIT ?
";

    // Merge parameters
    $params = array_merge($hours, $departments, [$limit, $num]);

    // Prepare and bind
    $stmt = $conn->prepare($query);
    $types = str_repeat('s', 4 + count($departments)) . 'ii';
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $fetched_faculty = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    return $fetched_faculty;
}

function acceptFacultiesCIE($faculties, $date, $session, $role)
{
    global $conn;
    $month = date('F', strtotime($date)); // Get the month from the date
    $ExamType = 'CIE';
    foreach ($faculties as $fid => $faculty) {
        // Insert into the log table
        $logQuery = "INSERT INTO log (fid, Duty_date, Duty_Session, Role, ExamType) VALUES (?, ?, ?, ?, ?)";
        $logStmt = $conn->prepare($logQuery);
        $logStmt->bind_param("issss", $faculty['Fid'], $date, $session, $role, $ExamType);
        $logStmt->execute();
    }
}


?>