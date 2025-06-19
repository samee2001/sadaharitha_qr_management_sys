<?php
session_start();
include 'connect.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get user input
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare the SQL query to fetch the user from the 'users' table
    $stmt = $conn->prepare("SELECT id, name, password, email, gen_qr_privillages, qr_details_privillages, estate_privillages, user_level FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // Fetch user data from the result
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Successful login, set session variables
            $_SESSION['email'] = $email;
            $_SESSION['name'] = $user['name'];
            //$_SESSION['role'] = $user['role'];
           // $_SESSION['handle_csv_privillages'] = $user['handle_csv_privillages'];
            $_SESSION['gen_qr_privillages'] = $user['gen_qr_privillages'];
            $_SESSION['qr_details_privillages'] = $user['qr_details_privillages'];
            $_SESSION['estate_privillages'] = $user['estate_privillages'];
            $_SESSION['user_level'] = $user['user_level'];

            if ( $user['gen_qr_privillages'] && $user['qr_details_privillages'] && $user['estate_privillages'] == 0) {
                header("Location: logIn.php");
                session_destroy();
                exit();
            }
            elseif (( $user['gen_qr_privillages'] || $user['qr_details_privillages'] || $user['estate_privillages']) > 0) {
                header("Location: dashboard.php");
                exit();
            }
        } else {
            // Invalid password
            $error = "Invalid password!";
        }
    } else {
        // User not found
        $error = "User not found!";
    }

    $stmt->close();
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css">
    <!-- Google Fonts for a modern look -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="login.css">
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-lg">
                    <div class="card-header text-center">
                        <img src="sadaharitha_logo_black.png" alt="Logo" class="forest-logo">
                        <h3>Sadaharitha Plantations Limited</h3>
                        <h5 class="mb-0">Admin Panel QR Management System</h5>
                        <h4 class="mb-0 mt-2">Log In</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        <form method="post" autocomplete="on">
                            <div class="form-group">
                                <label for="email"><span class="mr-1" aria-hidden="true">📧</span>Email</label>
                                <input type="email" id="email" name="email" class="form-control" required
                                    autocomplete="username">
                            </div>
                            <div class="form-group">
                                <label for="password"><span class="mr-1" aria-hidden="true">🔒</span>Password</label>
                                <input type="password" id="password" name="password" class="form-control" required
                                    autocomplete="current-password">
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-success btn-block btn-animate">Log In</button>
                            </div>
                            <hr class="my-4" style="border-top: 1.5px solid #e0e0e0;">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>