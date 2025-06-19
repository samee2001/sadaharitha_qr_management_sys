<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['email'])) {
    header('location:login.php');
}

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
    mysqli_stmt_close($check_stmt);

    // Check if estate is referenced in issued_estate
    $ref_stmt = mysqli_prepare($conn, "SELECT 1 FROM issued_estate WHERE estate_id = ? LIMIT 1");
    mysqli_stmt_bind_param($ref_stmt, "i", $id);
    mysqli_stmt_execute($ref_stmt);
    mysqli_stmt_store_result($ref_stmt);

    if (mysqli_stmt_num_rows($ref_stmt) > 0) {
        throw new Exception("Can't delete: QR codes are already issued to this estate.");
    }
    mysqli_stmt_close($ref_stmt);

    // Delete record
    $delete_stmt = mysqli_prepare($conn, "DELETE FROM estate WHERE id = ?");
    mysqli_stmt_bind_param($delete_stmt, "i", $id);

    if (!mysqli_stmt_execute($delete_stmt)) {
        throw new Exception('Delete error: ' . mysqli_error($conn));
    }

    if (mysqli_stmt_affected_rows($delete_stmt) > 0) {
        $_SESSION['statusupdate'] = 'Record deleted successfully!';
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