<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-table@1.24.0/dist/bootstrap-table.min.css"> -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg">
    </nav>
    <?php
    // session_start();
// if (!isset($_SESSION['username'])) {
//     header('location: index.php');
// }
    
    include 'config.php';
    include 'functions.php';
    // Function to get the number of days in the selected month
    

    // Get the submitted data
    $month = $_POST['month']; // Selected month
    $year = date("Y"); // Assuming the current year
    $daysInMonth = getDaysInMonth($month);
    $m = date("n");
    $data = [];
    $monthNumber = date("n", strtotime($month)); // Convert month name to numeric
    $allAssignedDuties = [];

    if ($m > $monthNumber) {
        $year = $year + 1;
    }
    $res = checkIfAlreadyScheduled($year, $monthNumber);
    if ($res) {
        echo "<script>
            if (confirm('Schedule already exists for this month. Do you want to override it?')) {
                // User chose to override — continue execution
            } else {
                // User cancelled — redirect
                window.location.href = 'schedule_all.php';
            }
        </script>";
        clearData($year, $monthNumber);
    }
    resetCounter($month);



    // Traverse each day of the month
    for ($day = 1; $day <= $daysInMonth; $day++) {
        // Skip Sundays
        if (isSunday($year, $monthNumber, $day)) {
            continue;
        }

        $date = "$year-$monthNumber-$day";

        // Process morning and afternoon values for the day
        $morningKey = $day . '-morning';
        $afternoonKey = $day . '-afternoon';

        $morningData = isset($_POST[$morningKey]) ? trim($_POST[$morningKey]) : '';
        $afternoonData = isset($_POST[$afternoonKey]) ? trim($_POST[$afternoonKey]) : '';


        // echo $date;
        if (!empty($morningData)) {
            list($facultyCount, $relieverCount) = explode(',', $morningData);
            $dayOfWeek = substr(date('l', strtotime($date)), 0, 3);
            $relievers = getRelievers($dayOfWeek, 'morning', $relieverCount, $date);
            $assigned_relievers = acceptFaculties($relievers, $date, 'morning', 'Reliever');
            $allAssignedDuties = array_merge($allAssignedDuties, $assigned_relievers);
            flush(); // Ensure all output is sent to the browser
            ob_flush(); // Flush the output buffer
    
            $faculties = getFaculties($dayOfWeek, 'morning', $facultyCount, $date);
            $assigned_faculties = acceptFaculties($faculties, $date, 'morning', 'Examiner');
            $allAssignedDuties = array_merge($allAssignedDuties, $assigned_faculties);

            flush(); // Ensure all output is sent to the browser
            ob_flush(); // Flush the output buffer
    
        }
        if (!empty($afternoonData)) {
            list($facultyCount, $relieverCount) = explode(',', $afternoonData);
            $dayOfWeek = substr(date('l', strtotime($date)), 0, 3);
            $relievers = getRelievers($dayOfWeek, 'afternoon', $relieverCount, $date);
            $assigned_relievers = acceptFaculties($relievers, $date, 'afternoon', 'Reliever');
            $allAssignedDuties = array_merge($allAssignedDuties, $assigned_relievers);
            flush(); // Ensure all output is sent to the browser
            ob_flush(); // Flush the output buffer
    
            $faculties = getFaculties($dayOfWeek, 'afternoon', $facultyCount, $date);
            $assigned_faculties = acceptFaculties($faculties, $date, 'afternoon', 'Examiner');
            $allAssignedDuties = array_merge($allAssignedDuties, $assigned_faculties);

            flush(); // Ensure all output is sent to the browser
            ob_flush(); // Flush the output buffer
        }

        echo "</pre>";


    }

    // After the loop, send emails
    // sendDutyEmail($allAssignedDuties, 'SEE');
    
    for ($day = 1; $day <= $daysInMonth; $day++) {
        // Skip Sundays
        if (isSunday($year, $monthNumber, $day)) {
            continue;
        }

        $date = "$year-$monthNumber-$day";

        // Process morning and afternoon values for the day
        $morningKey = $day . '-morning';
        $afternoonKey = $day . '-afternoon';

        $tableContent = getSchedule($date);

        if (!empty($tableContent)) {
            echo "<div class='table-container'>";
            echo "<h2>Schedule for $date</h2>";
            echo "<table>";
            echo "<tr><th>Staff ID</th><th>Name</th><th>Department</th><th>Session</th><th>Role</th></tr>";
            echo $tableContent;
            echo "</table>";
            echo "</div>";
        }
    }


    echo '<div class="text-center mt-4">';
    echo '<form method="post" action="export_faculty_duties.php">';
    echo '<input type="hidden" name="month" value="' . $month . '">';
    echo '<input type="hidden" name="year" value="' . $year . '">';
    echo '<button type="submit" class="btn btn-success">Download Faculty Duty Report And Send Email</button>';
    echo '</form>';
    echo '</div>';

    ?>

    <div class="custom-footer"></div>
    <script src="js/bootstrap.min.js"></script>
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap-table@1.24.0/dist/bootstrap-table.min.js"></script> -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.magnific-popup.min.js"></script>
    <script src="js/jquery.waypoints.js"></script>
    <script src="js/jquery.counterup.min.js"></script>
    <script src="js/jquery.barfiller.js"></script>
    <script src="js/index.js"></script>


</body>

</html>