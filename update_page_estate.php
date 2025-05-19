<?php
include 'connect.php';
session_start();
$id = mysqli_real_escape_string($conn, $_GET['updateid']);

// Fetch existing data
$sql_select = "SELECT * FROM estate WHERE id=?";
$stmt = mysqli_prepare($conn, $sql_select);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

if (!$row) {
    $_SESSION['statusupdate'] = 'Record not found!';
    header('location:estate_details_page.php');
    exit();
}

// Handle form submission
if (isset($_POST['submit'])) {
    $ename = mysqli_real_escape_string($conn, $_POST['ename']);
    $land = mysqli_real_escape_string($conn, $_POST['land']);
    $plant_type = mysqli_real_escape_string($conn, $_POST['plant_type']);

    // Use prepared statement for update
    $sql = "UPDATE estate SET estate_name=?, land_called=?, plant_type=? WHERE id=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssi", $ename, $land, $plant_type, $id);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['statusupdate'] = 'Record Updated Successfully!';
        header('location:estate_details_page.php');
        exit();
    } else {
        $_SESSION['statusupdate'] = 'Update failed: ' . mysqli_error($conn);
        header("location: estate_details_page.php");
        exit();
    }
}
?>

<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <title>Update Estate Details</title>

    <style>
        .btn:hover {
            background-color: white !important;
            color: black !important;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container my-5">
        <h2 class="text-center my-3" style="color: rgb(210, 234, 211);">Update Estate Details</h2>
        <h4 class="text-center my-3" style="color:rgb(210, 234, 211);">Please fill the details below to update the
            Record</h4>
        <div class="card p-4 mx-auto bg-light" style="max-width: 800px; opacity: 0.85; border-radius: 10px;">
            <form method="post" id="updateForm">
                <!-- Name Input with Icon -->
                <div class="form-group mb-4">
                    <label for="name" class="form-label">Estate Name</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="bi bi-house"></i></span>
                        </div>
                        <input type="text" class="form-control" placeholder="Enter Estate Name" name="ename" id="ename"
                            required>
                    </div>
                </div>

                <!-- QR Range Input with Icon -->
                <div class="form-group mb-4">
                    <label for="lands" class="form-label">Land Called</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="bi bi-hash"></i></span>
                        </div>
                        <input type="text" class="form-control" placeholder="Enter Land Called" name="land" id="land">
                    </div>
                </div>

                <!-- Estate Dropdown with Icon -->
                <div class="form-group mb-4">
                    <label for="estate" class="form-label">Plant Type</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="bi bi-tree"></i></span>
                        </div>
                        <select name="plant_type" class="form-control" id="plant_type" required>
                            <option value="" disabled selected>Select Plant Type</option>
                            <option value="Agarwood">Agarwood</option>
                            <option value="Vanilla">Vanilla</option>
                            <option value="Sandalwood">Sandalwood</option>
                        </select>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="btn" style="width: 100%; background-color: green;"><button type="submit" name="submit"
                        class="btn"
                        style=" background-color: green; border: none; width: 100%; color: white; font-size: 17px;">Update</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Bootstrap Icons -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.js"></script>
</body>

</html>