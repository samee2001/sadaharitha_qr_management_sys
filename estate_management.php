<?php
// Start the session at the beginning of your script
session_start();
if (!isset($_SESSION['email'])) {
    header('Location: logIn.php');
    exit();
}

// Include the database connection
include 'connect.php';

if (isset($_POST['submit'])) {
    // Sanitize and assign POST data to variables
    $name = mysqli_real_escape_string($conn, $_POST['estate_name']);
    $plantType = mysqli_real_escape_string($conn, $_POST['plant_type']);
    $landCalled = mysqli_real_escape_string($conn, $_POST['land_called']);

    // Check for duplicate estate name
    $check_sql = "SELECT estate_name FROM estate WHERE estate_name = ?";
    $check_stmt = mysqli_prepare($conn, $check_sql);

    if ($check_stmt) {
        mysqli_stmt_bind_param($check_stmt, "s", $name);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);

        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            // Duplicate found - set error message and redirect
            $_SESSION['status'] = 'Error: Estate with this name already exists!';
            header('Location: estate_management.php');
            mysqli_stmt_close($check_stmt);
            exit();
        }
        mysqli_stmt_close($check_stmt);
    } else {
        die("Error preparing check query: " . mysqli_error($conn));
    }

    // Proceed with insertion if no duplicate found
    $sql = "INSERT INTO `estate` (`estate_name`, `plant_type`, `land_called`) VALUES (?, ?, ?)";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        // Bind the parameters to the prepared statement
        mysqli_stmt_bind_param($stmt, "sss", $name, $plantType, $landCalled);

        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            // Set success message in the session
            $_SESSION['status'] = 'Record Inserted Successfully!';
            // Redirect to estate_management.php after 3 seconds 
            header('Location: estate_details_page.php');
            mysqli_stmt_close($stmt);
            exit();
        } else {
            // Handle error if statement execution fails
            die("Error executing query: " . mysqli_error($conn));
        }
    } else {
        // Handle error if statement preparation fails
        die("Error preparing query: " . mysqli_error($conn));
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Estate</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" type="text/css" href="estate.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

</head>

<body style="background-image: url('sdh_bg_2.png'); background-size: cover;">
    <div class="container my-5">
        <div class="text-center mb-4">
            <br>
            <br>
            <h1 class="fw-bold text-success">Manage Estates</h1>
            <p class="text-success fw-semibold ">Add a New Estate Details to the System
            </p>
        </div>

        <?php include 'components/navbar.php'; ?>
        <!-- Success Message Card -->
        <?php if (isset($_SESSION['status'])): ?>
            <div class="card mb-2 mx-auto alert alert-success" style="max-width: 700px; display: none;" id="successAlert">
                <div class="card-body text-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-check-circle-fill"></i>
                        <?php echo $_SESSION['status']; ?>
                    </h5>
                </div>
            </div>
            <?php unset($_SESSION['status']); ?>
        <?php endif; ?>
        <div class="card p-4 mx-auto"
            style="max-width: 700px; background-color:rgb(194, 244, 199); color: black; opacity: 0.85; border: 1px solid rgb(114, 234, 126);">
            <form method="post" enctype="multipart/form-data" id="myForm">
                <div class="mb-3">
                    <label for="name" class="form-label">Estate Name</label>
                    <input type="text" class="form-control" id="name" name="estate_name" required>
                </div>
                <div class="mb-3">
                    <label for="plantType" class="form-label">Plant Type</label>
                    <select class="form-select" id="plantType" name="plant_type" required>
                        <option value="Agarwood">Agarwood</option>
                        <option value="Sandalwood">Sandalwood</option>
                        <option value="Vanilla">Vanilla</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="landCalled" class="form-label">Land Called</label>
                    <input type="text" class="form-control" id="landCalled" name="land_called" required>
                </div>
                <div class="mb-2">
                    <button type="submit" class="btn btn-success w-100" name="submit" id="submitButton">Add
                        Estate</button>
                </div>
            </form>
        </div>
        <?php include 'components/footer.php'; ?>
    </div>
    <script src="success_message.js"></script>
</body>

</html>