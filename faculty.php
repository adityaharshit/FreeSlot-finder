<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Faculty</title>
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-table@1.24.0/dist/bootstrap-table.min.css"> -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
    </nav>

    <div class="container mt-5">
        <h1>Add Faculty</h1>
        <form action="add_faculty.php" method="post">
            <div class="row mb-2">
                <div class="col-md-6">
                    <label for="name" class="form-label">Faculty Name</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter faculty name"
                        required>
                </div>
                <div class="col-md-6">
                    <label for="facultyid" class="form-label">FacultyId</label>
                    <input type="text" class="form-control" id="facultyid" name="facultyid"
                        placeholder="Enter faculty id" required>
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-md-6">
                    <label for="email" class="form-label">Email</label>
                    <input type="text" class="form-control" id="email" name="email" placeholder="Enter Email" required>
                </div>
                <div class="col-md-6">
                    <label for="department" class="form-label">Department</label>
                    <input type="text" class="form-control" id="department" name="department"
                        placeholder="Enter Department" required>
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-md-6">
                    <label for="designation" class="form-label">Designation</label>
                    <input type="text" class="form-control" id="designation" name="designation"
                        placeholder="Enter Designation" required>
                </div>
                <div class="col-md-6">
                    <label for="yearsOfExperience" class="form-label">Experience (in yrs)</label>
                    <input type="number" class="form-control" id="yearsOfExperience" name="yearsOfExperience" required>
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-md-6">
                    <label for="caderValue" class="form-label">Cader Value</label>
                    <input type="number" class="form-control" id="caderValue" name="caderValue" required>
                </div>
                <div class="col-md-6">
                    <label for="contactNumber" class="form-label">Contact Number</label>
                    <input type="number" class="form-control" id="contactNumber" name="contactNumber" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Add Details</button>
        </form>


        <br>
        <h2>Upload Faculty CSV</h2>
        <form action="upload_faculty.php" method="post" enctype="multipart/form-data">
            <label for="faculty_csv" class="form-label">Choose CSV File:</label>
            <input type="file" name="faculty_csv" class="form-control" id="faculty_csv" accept=".csv" required>
            <br>
            <button type="submit" class="btn btn-primary">Upload</button>
            <a href="format/FacultyDetailFormat.csv" class="btn btn-primary">Download Format</a>
        </form>
    </div>

    <!-- Display Existing Courses -->
    <div class="container">


        <h2 class="text-center">Existing Faculties</h2>
        <input class="form-control" id="myInput" type="text" placeholder="Search..">
        <br>
        <table class="table table-bordered table-striped" data-toggle="table" data-search="true">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Email</th>
                    <th>Designation</th>
                    <th>Experience</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="facultyTable">
                <?php
                require 'config.php';

                $limit = 30; // Records per page
                $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
                if ($page < 1)
                    $page = 1;
                $offset = ($page - 1) * $limit;

                // Count total records
                $totalResult = $conn->query("SELECT COUNT(*) as total FROM faculties");
                $total = $totalResult->fetch_assoc()['total'];
                $totalPages = ceil($total / $limit);

                // Fetch paginated records
                $query = "SELECT * FROM faculties ORDER BY StaffId LIMIT $limit OFFSET $offset";
                $result = $conn->query($query);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['StaffId']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Department']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Email']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Designation']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['YearsOfExperience']) . "</td>";
                        echo "<td><a href='delete_faculty.php?id=" . $row['Fid'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this faculty detail?\");'>Delete</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' class='text-center'>No faculties found</td></tr>";
                }
                ?>

            </tbody>
        </table>
        <nav aria-label="Page navigation example">
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                    <li class="page-item"><a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a></li>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <li class="page-item"><a class="page-link" href="?page=<?= $page + 1 ?>">Next</a></li>
                <?php endif; ?>
            </ul>
        </nav>

    </div>





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


    <script>
        $("#myInput").on("keyup", function () {
            var value = $(this).val().toLowerCase();
            $("#facultyTable tr").filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    </script>
</body>

</html>