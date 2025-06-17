<?php
session_start();
?>
<div class="container-fluid navbar fixed-top">
    <div class="container">

        <a class="navbar-brand" href="#">Freeslot Finder</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php if (isset($_SESSION['username'])): ?>
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="course.php">Add Course</a></li>
                    <li class="nav-item"><a class="nav-link" href="faculty.php">Add Faculty</a></li>
                    <li class="nav-item"><a class="nav-link" href="fetch_details.php">Fetch Details</a></li>
                    <!-- <li class="nav-item"><a class="nav-link" href="scheduler.php">Scheduler</a></li> -->
                    <li class="nav-item"><a class="nav-link" href="schedule_all.php">Schedule All</a></li>
                    <li class="nav-item"><a class="nav-link" href="schedule_cie.php">Schedule CIE</a></li>
                    <li class="nav-item"><a class="nav-link" href="faculty_duties.php">Faculty Duties</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                <?php endif; ?>

            </ul>
        </div>
    </div>
</div>