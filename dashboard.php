<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finder</title>
    <link rel="stylesheet" href="css/responsive.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
    </nav>

    <div class="container">


        <form action="upload_timetable.php" method="post" enctype="multipart/form-data" class="mt-5">
            <label for="department" class="form-label">Select Department:</label>
            <select name="department" id="department" required class="form-control">

                <option value="">-- Select Course --</option>
                <!-- Courses will be populated here by PHP -->
                <?php
                require 'config.php'; // Include database connection
                $query = "SELECT DISTINCT course FROM courses";
                $result = $conn->query($query);
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='{$row['course']}'>{$row['course']}</option>";
                }
                ?>
            </select>
            <br><br>

            <label for="semester" class="form-label">Select Semester:</label>
            <select name="semester" id="semester" required class="form-control">
                <option value="">-- Select Section --</option>
            </select>
            <br><br>

            <label for="section" class="form-label">Select Section:</label>
            <select name="section" id="section" required class="form-control">
                <option value="">-- Select Section --</option>
            </select>
            <br><br>
            <label for="file" class="form-label">Upload CSV File:</label>
            <input type="file" name="file" id="file" accept=".csv" required class="form-control">
            <br><br>

            <button type="submit" name="submit" class="btn btn-primary">Save</button>
            <a href="format/TimeTableFormat.csv" class = "btn btn-primary">Download Format</a>

            
        </form>

    </div>

    <div class="container">


        <h1>Already Uploaded Documents</h1>
        <input class="form-control" id="myInput" type="text" placeholder="Search..">
        <br>
        <table class="table table-bordered table-striped">
            <thead>

                <tr>
                    <th>Department</th>
                    <th>Semester</th>
                    <th>File Name</th>
                    <th>Download</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <!-- Read the files from the timetable directory and split it to get the department and semester -->
            <tbody id="scheduleTable"   >

                <?php
                $timetableDir = __DIR__ . '/timetable/';
                $files = scandir($timetableDir);
                foreach ($files as $file) {
                    if ($file === '.' || $file === '..') {
                        continue;
                    }
                    $fileParts = explode('_', $file);
                    $department = $fileParts[0];
                    $semester = explode('.', $fileParts[1])[0];
                    echo "<tr>
                <td>$department</td>
                <td>$semester</td>
                <td>$file</td>
                <td><a href='timetable/$file'>Download</a></td>
                <td><a href='delete_timetable.php?file=timetable/$file'>Delete</a></td>
            </tr>";
                }
                ?>
            </tbody>
            </table>
    </div>

    <div class="container mt-5">
        <h1>CIE Duty Allocation</h1>
        <a href="schedule_cie.php" class="btn btn-primary">Schedule CIE Duties</a>
    </div>

    <div class="container mt-5">
        <h1>Faculty Duty Details</h1>
        <a href="faculty_duties.php" class="btn btn-primary">View Faculty Duties</a>
    </div>

    <div class="container">

        <h1>Check Availability</h1>

        <form id="availability-form">
            <label for="faculty" class="form-label">Select Faculty:</label>
            <select name="faculty" id="faculty" required class="form-control">
                <?php
                // Connect to the database
                
                require 'config.php';


                // Fetch faculty details from the database
                $query = "Select f.fid, f.name, f.email from faculties f join schedule s on f.fid = s.fid";
                $result = $conn->query($query);

                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row['fid'] . "'>" . $row['email'] . ' (' . $row['name'] . ')' . "</option>";
                }
                ?>
            </select>
            <br>
            <button type="button" id="fetch-details" class="btn btn-primary">Fetch</button>
            <br>
            <br>
        </form>
    </div>

    <table border="1" id="availability-table" class="table table-bordered table-striped container">
        <thead>
            <tr>
                <th>Day</th>
                <th>1</th>
                <th>2</th>
                <th> </th>
                <th>3</th>
                <th>4</th>
                <th> </th>
                <th>5</th>
                <th>6</th>
                <th>7</th>
                <th>8</th>
            </tr>
        </thead>
        <tbody id='table-body'>
            <!-- <tr id="mon"><td>Monday</td>   </tr>
        <tr><td>Tuesday</td>  <td colspan="8" id="tue"></td></tr>
        <tr><td>Wednesday</td><td colspan="8" id="wed"></td></tr>
        <tr><td>Thursday</td> <td colspan="8" id="thu"></td></tr>
        <tr><td>Friday</td>   <td colspan="8" id="fri"></td></tr>
        <tr><td>Saturday</td> <td colspan="8" id="sat"></td></tr> -->
        </tbody>
    </table>
    <script src="https://code.jquery.com/jquery-3.6.0.js">
    </script>
    <!-- Footer section -->
    <div class="custom-footer"></div>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.magnific-popup.min.js"></script>
    <script src="js/jquery.waypoints.js"></script>
    <script src="js/jquery.counterup.min.js"></script>
    <script src="js/jquery.barfiller.js"></script>
    <script src="js/index.js"></script>
    <script>


        $("#myInput").on("keyup", function () {
            var value = $(this).val().toLowerCase();
            $("#scheduleTable tr").filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });



        document.getElementById('fetch-details').addEventListener('click', function () {
            const facultyId = document.getElementById('faculty').value;
            // console.log(facultyId)
            fetch('fetch_availability.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ fid: facultyId }),
            })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('availability-table').style.display = 'table';
                    const tableBody = document.getElementById('table-body');
                    tableBody.innerHTML = '';
                    ['mon', 'tue', 'wed', 'thu', 'fri', 'sat'].forEach(day => {
                        const hours = Array(10).fill('Free');
                        hours[2] = 'Lunch';
                        hours[5] = 'Lunch';
                        if (data[day]) {
                            data[day].split(',').forEach(period => {
                                hours[+period - 1] = ' ';
                            });
                        }

                        let str = `<td>${day}</td>` + hours.map(hour => `<td>${hour}</td>`).join('');
                        // Code to convert string str to html element
                        let rowElement = document.createElement('tr');

                        // Set the innerHTML of the rowElement to the string
                        rowElement.innerHTML = str;

                        // Now rowElement is a DOM element
                        // console.log(rowElement);
                        // console.log($.parseHTML(str));
                        document.getElementById('table-body').appendChild(rowElement);
                    });
                })
                .catch(error => {
                    console.error('Error fetching availability:', error);
                });
        });

        document.getElementById("department").addEventListener("change", function () {
            const course = this.value;
            fetch(`fetch_semesters_sections.php?course=${encodeURIComponent(course)}`)
                .then(response => response.json())
                .then(data => {
                    const semesterDropdown = document.getElementById("semester");
                    const sectionDropdown = document.getElementById("section");

                    // Clear existing options
                    semesterDropdown.innerHTML = "";
                    sectionDropdown.innerHTML = "";
                    let optionSem = document.createElement("option");
                    optionSem.value = "select";
                    optionSem.textContent = "-- Select Semester --";
                    semesterDropdown.appendChild(optionSem);
                    let optionSec = document.createElement("option");
                    optionSec.value = "select";
                    optionSec.textContent = "-- Select Section --";
                    sectionDropdown.appendChild(optionSec);

                    // Populate semesters
                    data.semesters.forEach(sem => {
                        const option = document.createElement("option");
                        option.value = sem;
                        option.textContent = sem;
                        semesterDropdown.appendChild(option);
                    });

                    // Populate sections
                    data.sections.forEach(sec => {
                        const option = document.createElement("option");
                        option.value = sec;
                        option.textContent = sec;
                        sectionDropdown.appendChild(option);
                    });
                })
                .catch(error => console.error("Error fetching data:", error));
        });




    </script>




</body>

</html>