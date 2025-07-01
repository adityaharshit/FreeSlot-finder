<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Faculty</title>
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-table@1.24.0/dist/bootstrap-table.min.css"> -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .table-container {
            margin-bottom: 30px;
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #007BFF;
            color: white;
        }

        h2 {
            background: #28a745;
            color: white;
            padding: 10px;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
    </nav>

    <div class="container mt-5">
        <h1>Fetch Faculty Duties</h1>
        <form action="" method="post">
            <div class="mb-3">
                <div class="mb-3">
                    <label for="StaffID" class="form-label">Enter StaffId:</label>
                    <input type="text" class="form-control" id="StaffID" name="staffID">
                </div>
                <label for="month" class="form-label">Select Month:</label>
                <select id="month" class="form-select" name="month">
                    <option value="">--Select Month--</option>
                    <option value="January">January</option>
                    <option value="February">February</option>
                    <option value="March">March</option>
                    <option value="April">April</option>
                    <option value="May">May</option>
                    <option value="June">June</option>
                    <option value="July">July</option>
                    <option value="August">August</option>
                    <option value="September">September</option>
                    <option value="October">October</option>
                    <option value="November">November</option>
                    <option value="December">December</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="year" class="form-label">Select year:</label>
                <select id="year" class="form-select" name="year">
                    <option value="">--Select Year--</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="type" class="form-label">Select Type:</label>
                <select id="type" class="form-select" name="type">
                    <option value="cie">CIE</option>
                    <option value="see">SEE</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" name="fetchSchedule" id="fetchSchedule">Fetch Details</button>
        </form>
    </div>

    <div class="container mt-5">
        <h1>Fetch Faculty Details</h1>
        <form action="" method="post">
            <div class="mb-3">
                <label for="StaffID" class="form-label">Enter StaffId:</label>
                <input type="text" class="form-control" id="StaffID" name="staffID">
            </div>
            <button type="submit" class="btn btn-primary" name="fetchFaculty" id="fetchFaculty">Fetch Details</button>
        </form>
    </div>



    <div class="container" id="result">
        <?php

        include 'config.php';
        include 'functions.php';
        // Function to get the number of days in the selected month
        
        if (isset($_POST['fetchSchedule'])) {

            $type = $_POST['type'];
            $year = $_POST['year'];
            $staffid = $_POST['staffID'];
            // Get the submitted data
            if (!isset($_POST['month']) || empty($_POST['month'])) {
                if ($type == 'cie')
                    $tableContent = getDutiesCie($year, $staffid, true);
                else
                    $tableContent = getDutiesSee($year, $staffid, true);
                if (!empty($tableContent)) {
                    echo "<div class='table-container'>";
                    echo "<h2>Schedule for $year</h2>";
                    echo "<table>";
                    echo "<tr><th>Staff ID</th><th>Name</th><th>Department</th><th>Date</th></th><th>Session</th><th>Role</th></tr>";
                    echo $tableContent;
                    echo "</table>";
                    echo "</div>";
                }
            } else {


                $month = $_POST['month']; // Selected month
                $daysInMonth = getDaysInMonth($month);
                $data = [];
                $monthNumber = date("n", strtotime($month)); // Convert month name to numeric
        
                // echo "<table>";
                for ($day = 1; $day <= $daysInMonth; $day++) {
                    // Skip Sundays
                    if (isSunday($year, $monthNumber, $day)) {
                        continue;
                    }

                    $date = "$year-$monthNumber-$day";

                    // Process morning and afternoon values for the day
                    $morningKey = $day . '-morning';
                    $afternoonKey = $day . '-afternoon';
                    if ($type == 'cie')
                        $tableContent = getDutiesCie($date, $staffid, false);
                    else
                        $tableContent = getDutiesSee($date, $staffid, false);
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
            }
            // echo "</table>";
        }

        if (isset($_POST['fetchFaculty'])) {
            $staffID = $_POST['staffID'];
            $facultyDetails = getFacultyDetails($staffID);
            if (!empty($facultyDetails)) {
                echo "<div class='form-container'>";
                echo "<h2>Edit Faculty Details</h2>";
                echo "<form method='post' action='update_faculty.php'>";
                echo $facultyDetails;
                echo "<input type='hidden' name='staffID' value='" . htmlspecialchars($staffID) . "'>";
                echo "<button type='submit' name='updateFaculty' class='btn btn-primary mt-2'>Save Changes</button>";
                echo "</form>";
                echo "</div>";


            }
        }


        ?>
    </div>



    <script>
        const startYear = 2025;
        const endYear = new Date().getFullYear() + 1; // current year
        const select = document.getElementById('year');

        for (let year = startYear; year <= endYear; year++) {
            const option = document.createElement('option');
            option.value = year;
            option.textContent = year;
            select.appendChild(option);
        }
    </script>

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


<!-- <div class="container mt-5">
        <h1>Faculty Duty Details</h1>
        <form id="faculty-search-form">
            <div class="mb-3">
                <label for="faculty_id" class="form-label">Select Faculty:</label>
                <select name="faculty_id" id="faculty_id" required class="form-control">
                    <option value="">-- Select Faculty --</option>
                    <?php
                    require 'config.php'; // Include database connection
                    $query = "SELECT Fid, Name, FacultyId FROM faculties";
                    $result = $conn->query($query);
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='{$row['Fid']}'>{$row['Name']} ({$row['FacultyId']})</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="button" id="fetch-duties" class="btn btn-primary">Fetch Duties</button>
        </form>

        <div id="duty-details" class="mt-5">
            <!-- Duty details will be displayed here -->
<!-- </div>
    </div> --> -->