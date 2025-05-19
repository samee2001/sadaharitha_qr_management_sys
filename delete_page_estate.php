<?php
session_start();
include 'connect.php';

// Validate ID parameter
if (!isset($_GET['deleteid']) || !is_numeric($_GET['deleteid'])) {
    $_SESSION['statusdelete'] = 'Invalid request!';
    header('Location: estate_details_page.php');
    exit();
}

$id = (int) $_GET['deleteid'];

try {
    // Check if record exists
    $check_stmt = mysqli_prepare($conn, "SELECT id FROM estate WHERE id = ?");
    mysqli_stmt_bind_param($check_stmt, "i", $id);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);

    if (mysqli_stmt_num_rows($check_stmt) === 0) {
        throw new Exception('Record not found');
    }

    // Delete record
    $delete_stmt = mysqli_prepare($conn, "DELETE FROM estate WHERE id = ?");
    mysqli_stmt_bind_param($delete_stmt, "i", $id);

    if (!mysqli_stmt_execute($delete_stmt)) {
        throw new Exception('Delete error: ' . mysqli_error($conn));
    }

    if (mysqli_stmt_affected_rows($delete_stmt) > 0) {
        $_SESSION['statusdelete'] = 'Record deleted successfully!';
        $_SESSION['alert_type'] = 'success';
    } else {
        throw new Exception('No records deleted');
    }

} catch (Exception $e) {
    $_SESSION['statusdelete'] = 'Error: ' . $e->getMessage();
    $_SESSION['alert_type'] = 'danger';
} finally {
    mysqli_close($conn);
    header('Location: estate_details_page.php');
    exit();
}
?>