<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Duties</title>
    <link rel="stylesheet" href="css/responsive.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light">
    </nav>

    <div class="container mt-5">
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
        </div>
    </div>

    <!-- Footer section -->
    <div class="custom-footer"></div>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/index.js"></script>
    <script>
        $(document).ready(function() {
            $('#fetch-duties').on('click', function() {
                var facultyId = $('#faculty_id').val();
                if (facultyId) {
                    $.ajax({
                        url: 'fetch_faculty_duties.php',
                        method: 'POST',
                        data: {
                            faculty_id: facultyId
                        },
                        success: function(response) {
                            $('#duty-details').html(response);
                        }
                    });
                }
            });
        });
    </script>
</body>

</html>