<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore Paris</title>
    <link rel="stylesheet" href="css/responsive.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
    </nav>

    <form action="upload_timetable.php" method="post" enctype="multipart/form-data" class="mt-5">
        <label for="department">Select Department:</label>
        <select name="department" id="department" required>

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

        <label for="semester">Select Semester:</label>
        <select name="semester" id="semester" required>
            <option value="">-- Select Section --</option>
        </select>
        <br><br>

        <label for="section">Select Section:</label>
        <select name="section" id="section" required>
            <option value="">-- Select Section --</option>
        </select>
        <br><br>
        <label for="file">Upload CSV File:</label>
        <input type="file" name="file" id="file" accept=".csv" required>
        <br><br>

        <button type="submit" name="submit">Save</button>
    </form>


    <h1>Already Uploaded Documents</h1>
    <table class="table table-bordered table-striped">
        <tr>
            <th>Department</th>
            <th>Semester</th>
            <th>File Name</th>
            <th>Download</th>
        </tr>
        <!-- Read the files from the timetable directory and split it to get the department and semester -->
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
            </tr>";
        }
        ?>
    </table>


    <h1>Check Availability</h1>

    <form id="availability-form">
        <label for="faculty">Select Faculty:</label>
        <select name="faculty" id="faculty" required>
            <?php
            // Connect to the database
            
            require 'config.php';


            // Fetch faculty details from the database
            $query = "SELECT DISTINCT email, CONCAT(SUBSTRING_INDEX(email, '@', 1), '(', email, ')') AS name_email FROM schedule";
            $result = $conn->query($query);

            while ($row = $result->fetch_assoc()) {
                echo "<option value='" . $row['email'] . "'>" . $row['name_email'] . "</option>";
            }
            ?>
        </select>
        <br><br>

        <button type="button" id="fetch-details">Fetch</button>
    </form>

    <table border="1" id="availability-table" class="table table-bordered table-striped">
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
    <script>
        document.getElementById('fetch-details').addEventListener('click', function () {
            const facultyEmail = document.getElementById('faculty').value;

            fetch('fetch_availability.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ email: facultyEmail }),
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
                        console.log(rowElement);
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



    <!-- Footer section -->
    <div class="custom-footer"></div>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.magnific-popup.min.js"></script>
    <script src="js/jquery.waypoints.js"></script>
    <script src="js/jquery.counterup.min.js"></script>
    <script src="js/jquery.barfiller.js"></script>
    <script src="js/index.js"></script>
</body>

</html>