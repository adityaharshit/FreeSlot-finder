<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CIE Duty Schedule</title>
    <link rel="stylesheet" href="css/responsive.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light">
    </nav>

    <div class="container mt-5">
        <?php
        include 'functions.php';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $departments = $_POST['departments'];
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
                    $dayOfWeek = substr(date('l', strtotime($date)), 0, 3);
                    $faculties = getFacultiesForCIE($dayOfWeek, 'morning', $morningData, $date, $departments);
                    $assigned_faculties = acceptFacultiesCIE($faculties, $date, 'morning', 'Examiner');
                    $allAssignedDuties = array_merge($allAssignedDuties, $assigned_faculties);

                    flush(); // Ensure all output is sent to the browser
                    ob_flush(); // Flush the output buffer
        
                }
                if (!empty($afternoonData)) {

                    $dayOfWeek = substr(date('l', strtotime($date)), 0, 3);
                    $faculties = getFacultiesForCIE($dayOfWeek, 'afternoon', $afternoonData, $date, $departments);
                    $assigned_faculties = acceptFacultiesCIE($faculties, $date, 'afternoon', 'Examiner');
                    $allAssignedDuties = array_merge($allAssignedDuties, $assigned_faculties);

                    flush(); // Ensure all output is sent to the browser
                    ob_flush(); // Flush the output buffer
                }

                echo "</pre>";


            }

            // After the loop, send emails
            // sendDutyEmail($allAssignedDuties, 'CIE');
        
            for ($day = 1; $day <= $daysInMonth; $day++) {
                // Skip Sundays
                if (isSunday($year, $monthNumber, $day)) {
                    continue;
                }

                $date = "$year-$monthNumber-$day";

                // Process morning and afternoon values for the day
                $morningKey = $day . '-morning';
                $afternoonKey = $day . '-afternoon';

                $tableContent = getScheduleCie($date);

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
            echo '<form method="post" action="export_faculty_duties_cie.php">';
            echo '<input type="hidden" name="month" value="' . $month . '">';
            echo '<input type="hidden" name="year" value="' . $year . '">';
            echo '<button type="submit" class="btn btn-success">Download Faculty Duty Report and Send Email</button>';
            echo '</form>';
            echo '</div>';

        }
        ?>
    </div>

    <!-- Footer section -->
    <div class="custom-footer"></div>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/index.js"></script>
</body>

</html>