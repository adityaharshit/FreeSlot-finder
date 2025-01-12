<?php
include 'functions.php'; // Assuming all reusable functions are here
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'];
    $session = $_POST['session'];
    $num = (int)$_POST['num'];

    $dayOfWeek = date('l', strtotime($date));
    $hoursToCheck = $session === 'morning' ? [1, 2, 4, 5] : [7, 8, 9, 10];
    $dayOfWeek = substr($dayOfWeek, 0, 3); // Get the first three characters of the day
    $faculties = getAvailableFaculties($dayOfWeek, $hoursToCheck, $num, $date); // Function to fetch data
    //print the values of $faculties in formatted manner where each value is printed on a new line
    // foreach ($faculties as $index => $faculty) {
    //     echo "Index $index: ";
    //     print_r($faculty);
    //     echo "<br>";
    // }
    
    // print_r($faculties);
    if (empty($faculties)) {
        echo "<p>No faculties available for the selected time. Expanding search...</p>";
        // $expandedFaculties = getAvailableFacultiesWithLessHours($dayOfWeek, $hoursToCheck, $num);
        // renderFacultyTable($expandedFaculties);
    } else {
        renderFacultyTable( -1);
    }
}


?>
