<?php
include 'config.php';
include 'functions.php';

// Get month and year from POST
$month = $_POST['month'];
$year = $_POST['year'];
$monthNumber = date("n", strtotime($month));

// Get start and end dates
$startDate = "$year-$monthNumber-01";
$endDate = "$year-$monthNumber-" . date('t', strtotime($startDate));

// Get all faculties with their duties
$query = "SELECT f.fid, f.StaffID, f.name, f.department, f.email, 
          l.Duty_date, l.Duty_Session, l.Role
          FROM faculties f
          LEFT JOIN log l ON f.fid = l.fid 
          AND l.Duty_date BETWEEN ? AND ?
          ORDER BY f.name, l.Duty_date, 
            CASE l.Duty_Session 
                WHEN 'morning' THEN 1 
                WHEN 'afternoon' THEN 2 
                ELSE 3 
            END";

$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $startDate, $endDate);
$stmt->execute();
$result = $stmt->get_result();

// Organize data by faculty
$facultyData = [];
$maxDuties = 0;

while ($row = $result->fetch_assoc()) {
    $fid = $row['fid'];
    
    if (!isset($facultyData[$fid])) {
        $facultyData[$fid] = [
            'name' => $row['name'],
            'department' => $row['department'],
            'email' => $row['email'],
            'duties' => [],
            'duty_count' => 0
        ];
    }
    
    if ($row['Duty_date']) {
        $dutyString = date('d-M', strtotime($row['Duty_date'])) . ' ' . 
                      ucfirst($row['Duty_Session']) . ' (' . $row['Role'] . ')';
        
        $facultyData[$fid]['duties'][] = $dutyString;
        $facultyData[$fid]['duty_count']++;
        
        if ($facultyData[$fid]['duty_count'] > $maxDuties) {
            $maxDuties = $facultyData[$fid]['duty_count'];
        }
    }
}

// Generate CSV content
$csv = "Name,Department,Email,Total Duties";
for ($i = 1; $i <= $maxDuties; $i++) {
    $csv .= ",Duty $i";
}
$csv .= "\n";

foreach ($facultyData as $faculty) {
    $csv .= '"' . $faculty['name'] . '","' . 
            $faculty['department'] . '","' . 
            $faculty['email'] . '",' . 
            $faculty['duty_count'];
    
    // Add individual duties
    for ($i = 0; $i < $maxDuties; $i++) {
        $duty = $faculty['duties'][$i] ?? '';
        $csv .= ',"' . $duty . '"';
    }
    $csv .= "\n";
}

// Set headers for download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="faculty_duties_' . $month . '_' . $year . '.csv"');
echo $csv;
exit;
?>