<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Swap Duties</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg"></nav>
    <div class="container mt-5">
        <h2>Swap Duties</h2>
        <?php
        include 'functions.php';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['duty1']) && isset($_POST['duty2'])) {
            $duty1_id = $_POST['duty1'];
            $duty2_id = $_POST['duty2'];

            if ($duty1_id === $duty2_id) {
                echo "<div class='alert alert-danger'>You cannot swap the same duty.</div>";
            } else {
                if (swapDuties($duty1_id, $duty2_id)) {
                    echo "<div class='alert alert-success'>Duties swapped successfully.</div>";
                } else {
                    echo "<div class='alert alert-danger'>Failed to swap duties.</div>";
                }
            }
        }
        ?>

        <form action="swap_duties.php" method="GET">
            <div class="row">
                <div class="col-md-4">
                    <label for="exam_type">Exam Type:</label>
                    <select name="exam_type" id="exam_type" class="form-control">
                        <option value="SEE">SEE</option>
                        <option value="CIE">CIE</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="duty_date1">Duty Date 1:</label>
                    <input type="date" name="duty_date1" id="duty_date1" class="form-control">
                </div>
                <div class="col-md-4">
                    <label for="duty_date2">Duty Date 2:</label>
                    <input type="date" name="duty_date2" id="duty_date2" class="form-control">
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Fetch Duties</button>
        </form>

        <?php
        if (isset($_GET['exam_type']) && isset($_GET['duty_date1']) && isset($_GET['duty_date2'])) {
            $exam_type = $_GET['exam_type'];
            $duty_date1 = $_GET['duty_date1'];
            $duty_date2 = $_GET['duty_date2'];

            $duties1 = getAllocatedDutiesByDateAndType($duty_date1, $exam_type);
            $duties2 = getAllocatedDutiesByDateAndType($duty_date2, $exam_type);
        ?>
            <form action="swap_duties.php" method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <h4>Select First Duty</h4>
                        <select name="duty1" class="form-control">
                            <?php foreach ($duties1 as $duty): ?>
                                <option value="<?php echo $duty['Lid']; ?>">
                                    <?php echo "{$duty['Name']} - {$duty['Duty_date']} ({$duty['Duty_Session']}) - {$duty['ExamType']}"; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <h4>Select Second Duty</h4>
                        <select name="duty2" class="form-control">
                            <?php foreach ($duties2 as $duty): ?>
                                <option value="<?php echo $duty['Lid']; ?>">
                                    <?php echo "{$duty['Name']} - {$duty['Duty_date']} ({$duty['Duty_Session']}) - {$duty['ExamType']}"; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Swap Duties</button>
            </form>
        <?php } ?>
    </div>
    <div class="custom-footer"></div>
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/index.js"></script>
</body>
</html>