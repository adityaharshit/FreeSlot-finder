<?php
require 'config.php'; // Include database connection

if (isset($_GET['course'])) {
    $course = $_GET['course'];
    $query = "SELECT DISTINCT semester, sections FROM courses WHERE course = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $course);
    $stmt->execute();
    $result = $stmt->get_result();

    $semesters = [];
    $sections = [];

    while ($row = $result->fetch_assoc()) {
        // Get semester range (e.g., 1 to N)
        for ($i = 1; $i <= intval($row['semester']); $i++) {
            if (!in_array($i, $semesters)) {
                $semesters[] = $i;
            }
        }

        // Get section range (e.g., A to Z for the given number of sections)
        $sectionCount = intval($row['sections']);
        for ($i = 0; $i < $sectionCount; $i++) {
            $section = chr(65 + $i); // Convert 0, 1, 2... to A, B, C...
            if (!in_array($section, $sections)) {
                $sections[] = $section;
            }
        }
    }

    echo json_encode([
        'semesters' => $semesters,
        'sections' => $sections
    ]);
}else{
    echo json_encode([
        'semesters' => [],
        'sections' => []
    ]);
}
?>