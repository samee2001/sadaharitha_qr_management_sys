<?php
include 'connect.php';
session_start();
if (!isset($_SESSION['email'])) {
    header('Location: logIn.php');
    exit();
}

$id = mysqli_real_escape_string($conn, $_GET['updateid']);

// Fetch existing data
$sql_select = "SELECT * FROM users WHERE id=?";
$stmt = mysqli_prepare($conn, $sql_select);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

if (!$row) {
    $_SESSION['statusupdate'] = 'Record updated!';
    header('location:manage_users.php');
    exit();
}

// Handle form submission
if (isset($_POST['submit'])) {
    $emailUser = mysqli_real_escape_string($conn, $_POST['email_user']);
    $userName = mysqli_real_escape_string($conn, $_POST['user_name']);
    //$csvPrivilege = mysqli_real_escape_string($conn, $_POST['csv_privilege']);
    $qrGeneratePrivilege = mysqli_real_escape_string($conn, $_POST['qr_generate_privilege']);
    $qrDetailsPrivilege = mysqli_real_escape_string($conn, $_POST['qr_details_privilege']);
    $estateManagementPrivilege = mysqli_real_escape_string($conn, $_POST['estate_management_privilege']);
    $userLevel = mysqli_real_escape_string($conn, $_POST['user_level']);

    // Determine user level value for privileges

    if ($userLevel === 'admin') {
        $userLevelValue = rand(20, 30);
    } elseif ($userLevel === 'super_user') {
        $userLevelValue = rand(11, 20);
    } elseif ($userLevel === 'user') {
        $userLevelValue = rand(1, 10);
    }

    // Convert "allow"/"deny" to integer values based on user level for privileges
    //$csvPrivilegeValue = ($csvPrivilege === 'allow') ? rand(20, 30) : 0;
    $qrGeneratePrivilegeValue = ($qrGeneratePrivilege === 'allow') ? rand(1, 30) : 0;
    $qrDetailsPrivilegeValue = ($qrDetailsPrivilege === 'allow') ? rand(10, 30) : 0;
    $estateManagementPrivilegeValue = ($estateManagementPrivilege === 'allow') ? rand(20, 30) : 0;
    $userLevelValue = ($userLevel === 'admin') ? rand(20, 30) : (($userLevel === 'super_user') ? rand(10, 20) : (($userLevel === 'user') ? rand(1, 10) : 0));

    // Use prepared statement for update
    $sql = "UPDATE users SET email=?, name=?,  gen_qr_privillages=?, qr_details_privillages=?, estate_privillages=?, user_level=? WHERE id=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssiiiii", $emailUser, $userName,  $qrGeneratePrivilegeValue, $qrDetailsPrivilegeValue, $estateManagementPrivilegeValue, $userLevelValue, $id);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['statusupdate'] = 'Record Updated Successfully!';
        header('location:manage_users_update.php?updateid=' . $id);
        exit();
    } else {
        $_SESSION['statusupdate'] = 'Update failed: ' . mysqli_error($conn);
        header("location:manage_users_update.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update User</title>

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
                <h1 class="text-success fw-bold">Update Users</h1>
                <p class="text-success fw-semibold">Update User Details</p>
            </div>

            <?php include 'components/navbar.php'; ?>
            <!-- Success Message Card -->
            <?php if (isset($_SESSION['statusupdate'])): ?>
                <div class="card mb-2 mx-auto alert alert-success" style="max-width: 700px; display: none;"
                    id="successAlert">
                    <div class="card-body text-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-check-circle-fill"></i>
                            <?php echo $_SESSION['statusupdate']; ?>
                        </h5>
                    </div>
                </div>
                <?php unset($_SESSION['statusupdate']); ?>
            <?php endif; ?>
            <div class="card p-4 mx-auto"
                style="max-width: 700px; background-color: rgb(194, 244, 199); color: black; opacity: 0.85; border: 1px solid rgb(114, 234, 126);">
                <form method="post" enctype="multipart/form-data" id="myForm">
                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="text" class="form-control" id="email" name="email_user"
                            value="<?php echo htmlspecialchars($row['email']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">User Name</label>
                        <input type="text" class="form-control" id="name" name="user_name"
                            value="<?php echo htmlspecialchars($row['name']); ?>" required>
                    </div>
                    <!-- <div class="mb-3">
                        <label for="csvPrivilege" class="form-label">CSV Privilege</label>
                        <select class="form-select" id="csvPrivilege" name="csv_privilege" required>
                            <option value="allow" <?php //echo ($row['handle_csv_privillages'] >= 20) ? 'selected' : ''; ?>>
                                Allow</option>
                            <option value="deny" <?php //echo ($row['handle_csv_privillages'] < 20) ? 'selected' : ''; ?>>
                                Deny</option>
                        </select>
                    </div> -->
                    <div class="mb-3">
                        <label for="qrGeneratePrivilege" class="form-label">QR Generate Privilege</label>
                        <select class="form-select" id="qrGeneratePrivilege" name="qr_generate_privilege" required>
                            <option value="allow" <?php echo ($row['gen_qr_privillages'] > 0) ? 'selected' : ''; ?>>
                                Allow</option>
                            <option value="deny" <?php echo ($row['gen_qr_privillages'] == 0) ? 'selected' : ''; ?>>Deny
                            </option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="qrDetailsPrivilege" class="form-label">Batch Creation Privilege</label>
                        <select class="form-select" id="qrDetailsPrivilege" name="qr_details_privilege" required>
                            <option value="allow" <?php echo ($row['qr_details_privillages'] >= 10) ? 'selected' : ''; ?>>
                                Allow</option>
                            <option value="deny" <?php echo ($row['qr_details_privillages'] < 10) ? 'selected' : ''; ?>>
                                Deny</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="estateManagementPrivilege" class="form-label">Estate Management Privilege</label>
                        <select class="form-select" id="estateManagementPrivilege" name="estate_management_privilege"
                            required>
                            <option value="allow" <?php echo ($row['estate_privillages'] >= 20) ? 'selected' : ''; ?>>
                                Allow</option>
                            <option value="deny" <?php echo ($row['estate_privillages'] < 20) ? 'selected' : ''; ?>>Deny
                            </option>
                        </select>
                    </div>
                    <!-- User Level -->
                    <div class="mb-3">
                        <label for="userLevel" class="form-label">User Level</label>
                        <select class="form-select" id="userLevel" name="user_level" required>
                            <option value="admin" <?php echo ($row['user_level'] === 'admin') ? 'selected' : ''; ?>>Admin
                            </option>
                            <option value="super_user" <?php echo ($row['user_level'] === 'super_user') ? 'selected' : ''; ?>>Super User</option>
                            <option value="user" <?php echo ($row['user_level'] === 'user') ? 'selected' : ''; ?>>User
                            </option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <button type="submit" class="btn btn-success w-100" name="submit" id="submitButton">Update
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