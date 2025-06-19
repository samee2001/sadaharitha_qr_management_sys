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
    $emailUser = mysqli_real_escape_string($conn, $_POST['email_user']);
    $userName = mysqli_real_escape_string($conn, $_POST['user_name']);
   // $csvPrivilege = mysqli_real_escape_string($conn, $_POST['csv_privilege']);
    $qrGeneratePrivilege = mysqli_real_escape_string($conn, $_POST['qr_generate_privilege']);
    $qrDetailsPrivilege = mysqli_real_escape_string($conn, $_POST['qr_details_privilege']);
    $estateManagementPrivilege = mysqli_real_escape_string($conn, $_POST['estate_management_privilege']);
    $userLevel = mysqli_real_escape_string($conn, $_POST['user_level']);

    // Check for duplicate email
    $check_sql = "SELECT email FROM users WHERE email = ?";
    $check_stmt = mysqli_prepare($conn, $check_sql);

    if ($check_stmt) {
        mysqli_stmt_bind_param($check_stmt, "s", $emailUser);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);

        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            // Duplicate found - set error message and redirect
            $_SESSION['status'] = 'Error: User with this email already exists!';
            header('Location: manage_users.php');
            mysqli_stmt_close($check_stmt);
            exit();
        }
        mysqli_stmt_close($check_stmt);
    } else {
        die("Error preparing check query: " . mysqli_error($conn));
    }

    // Convert "allow"/"deny" to integer values
    //$csvPrivilegeValue = ($csvPrivilege === 'allow') ? rand(20, 30) : 0;
    $qrGeneratePrivilegeValue = ($qrGeneratePrivilege === 'allow') ? rand(1, 30) : 0;
    $qrDetailsPrivilegeValue = ($qrDetailsPrivilege === 'allow') ? rand(10, 30) : 0;
    $estateManagementPrivilegeValue = ($estateManagementPrivilege === 'allow') ? rand(20, 30) : 0;
    $userLevelValue = ($userLevel === 'admin') ? rand(20, 30) : (($userLevel === 'super_user') ? rand(10, 20) : (($userLevel === 'user') ? rand(1, 10) : 0));

    // Proceed with insertion if no duplicate found
    $sql = "INSERT INTO `users` (`email`, `name`,  `gen_qr_privillages`, `qr_details_privillages`, `estate_privillages`, `user_level`) VALUES (?, ?, ?, ?, ?, ?)";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        // Bind the parameters to the prepared statement
        mysqli_stmt_bind_param($stmt, "ssiiii", $emailUser, $userName,  $qrGeneratePrivilegeValue, $qrDetailsPrivilegeValue, $estateManagementPrivilegeValue, $userLevelValue);

        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            // Set success message in the session
            $_SESSION['status'] = 'User Added Successfully!';
            // Redirect to manage_users.php
            header('Location: manage_users_add.php');
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
    <title>Add User</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" type="text/css" href="estate.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body style="background-image: url('sdh_bg_2.png'); background-size: cover;">
    <section>
        <div class="container my-5">
            <div class="text-center mb-4">
                <br>
                <h1 class="text-success fw-bold">Manage Users</h1>
                <p class="text-success fw-semibold">Add a New User to the System</p>
            </div>

            <?php include 'components/navbar.php'; ?>
            <!-- Success Message Card -->
            <?php if (isset($_SESSION['status'])): ?>
                <div class="card mb-2 mx-auto alert alert-success" style="max-width: 700px; display: none;"
                    id="successAlert">
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
                style="max-width: 700px; background-color: rgb(194, 244, 199); color: black; opacity: 0.85; border: 1px solid rgb(114, 234, 126);">
                <form method="post" enctype="multipart/form-data" id="myForm">
                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="text" class="form-control" id="email" name="email_user" required>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">User Name</label>
                        <input type="text" class="form-control" id="name" name="user_name" required>
                    </div>
                    <!-- <div class="mb-3">
                        <label for="csvPrivilege" class="form-label">CSV Privilege</label>
                        <select class="form-select" id="csvPrivilege" name="csv_privilege" required>
                            <option value="allow">Allow</option>
                            <option value="deny">Deny</option>
                        </select>
                    </div> -->
                    <div class="mb-3">
                        <label for="qrGeneratePrivilege" class="form-label">QR Generation Privilege</label>
                        <select class="form-select" id="qrGeneratePrivilege" name="qr_generate_privilege" required>
                            <option value="allow">Allow</option>
                            <option value="deny">Deny</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="qrDetailsPrivilege" class="form-label">Batch Creation Privilege</label>
                        <select class="form-select" id="qrDetailsPrivilege" name="qr_details_privilege" required>
                            <option value="allow">Allow</option>
                            <option value="deny">Deny</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="estateManagementPrivilege" class="form-label">Estate Management Privilege</label>
                        <select class="form-select" id="estateManagementPrivilege" name="estate_management_privilege"
                            required>
                            <option value="allow">Allow</option>
                            <option value="deny">Deny</option>
                        </select>
                    </div>
                    <!--user level-->
                    <div class="mb-3">
                        <label for="userLevel" class="form-label">User Level</label>
                        <select class="form-select" id="userLevel" name="user_level" required>
                            <option value="admin">Admin</option>
                            <option value="super_user">Super User</option>
                            <option value="user">User</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <button type="submit" class="btn btn-success w-100" name="submit" id="submitButton">Add New
                            User</button>
                    </div>
                </form>
            </div>
            <br>
            <br>
            <br>
            <?php include 'components/footer.php'; ?>
        </div>
    </section>
    <script src="success_message.js"></script>
</body>

</html>