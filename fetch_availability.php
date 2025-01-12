<?php
header('Content-Type: application/json');

// Database connection

require 'config.php';


// Get the JSON payload
$input = json_decode(file_get_contents('php://input'), true);
$fid = $input['fid'] ?? '';
if (empty($fid)) {
    echo json_encode(['error' => 'fid is required.']);
    exit;
}

// Fetch availability from the schedule table
$query = "SELECT mon, tue, wed, thu, fri, sat FROM schedule WHERE fid = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    echo json_encode(['error' => 'Failed to prepare statement: ' . $conn->error]);
    exit;
}

$stmt->bind_param('i', $fid);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode([
        'mon' => $row['mon'],
        'tue' => $row['tue'],
        'wed' => $row['wed'],
        'thu' => $row['thu'],
        'fri' => $row['fri'],
        'sat' => $row['sat'],
    ]);
} else {
    echo json_encode(['error' => 'No schedule found for the selected faculty.']);
}

$stmt->close();
?>
