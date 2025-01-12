<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Course</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
    </nav>

    <div class="container mt-5">
        <h1>Add Course</h1>
        <form action="add_course.php" method="post">
            <div class="mb-3">
                <label for="course" class="form-label">Course Name</label>
                <input type="text" class="form-control" id="course" name="course" placeholder="Enter course name"
                    required>
            </div>

            <div class="mb-3">
                <label for="semester" class="form-label">Total Number of Semesters</label>
                <input type="number" class="form-control" id="semester" name="semester"
                    placeholder="Enter total semesters" required>
            </div>

            <div class="mb-3">
                <label for="sections" class="form-label">Total Number of Sections</label>
                <input type="number" class="form-control" id="sections" name="sections"
                    placeholder="Enter total sections" required>
            </div>

            <button type="submit" class="btn btn-primary">Add Course</button>
        </form>
    </div>

    <!-- Display Existing Courses -->
    <div class="container">

    
    <h2 class="text-center">Existing Courses</h2>
    <input class="form-control" id="myInput" type="text" placeholder="Search..">
    <br>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Course</th>
                <th>Semesters</th>
                <th>Sections</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id = "courseTable">
            <?php
            // Database connection
            require 'config.php';

            // Fetch existing rows
            $query = "SELECT * FROM courses";
            $result = $conn->query($query);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['Course']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Semester']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Sections']) . "</td>";
                    echo "<td>";
                    echo "<a href='delete_course.php?id=" . $row['Id'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this course?\");'>Delete</a>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4' class='text-center'>No courses found</td></tr>";
            }
            ?>
        </tbody>
    </table>
    </div>

    <div class="custom-footer"></div>
    <script src="js/bootstrap.min.js"></script>

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
            $("#courseTable tr").filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    </script>


</body>

</html>