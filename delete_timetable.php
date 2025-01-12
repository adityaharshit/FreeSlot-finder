<?php
require 'functions.php';
$path = $_GET['file'];
// $path = 'timetable/MCA_1_A.csv';

delete_data($path);
delete_file($path);

echo "<script>
                    alert('File deleted successfully.');
                    window.location.href='dashboard.php';
                    </script>";



?>