<?php
header('Content-Type: application/json');

// Database connection

require 'config.php';


// Get the JSON payload
$input = json_decode(file_get_contents('php://input'), true);
$email = $input['email'] ?? '';

if (empty($email)) {
    echo json_encode(['error' => 'Email is required.']);
    exit;
}

// Fetch availability from the schedule table
$query = "SELECT mon, tue, wed, thu, fri, sat FROM schedule WHERE email = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    echo json_encode(['error' => 'Failed to prepare statement: ' . $conn->error]);
    exit;
}

$stmt->bind_param('s', $email);
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
