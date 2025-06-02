<?php
// Include the database connection file
include 'connect.php'; // Ensure this defines $mysqli

header('Content-Type: application/json');

// Check if ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid or missing ID']);
    exit;
}

$id = intval($_GET['id']);

// Prepare the SQL statement to fetch start and step
$sql = "SELECT start, step FROM qr_management WHERE id = ?";
$stmt = $mysqli->prepare($sql);
if ($stmt) {
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode(['success' => true, 'start' => $row['start'], 'step' => $row['step']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No record found']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}

$mysqli->close();
?>