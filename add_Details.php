<?php
// Include the database connection file
include 'connect.php'; // Ensure the database connection is included

if (isset($_POST['submit'])) {
    // Get the data from the form
    $updater_name = $_POST['name'];
    $email = $_POST['email'];
    $range = $_POST['range'];
    $estate = $_POST['estate'];

    // Get current date and time automatically
    $currentDate = date('Y-m-d'); // Get current date in YYYY-MM-DD format
    $currentTime = date('H:i:s'); // Get current time in HH:MM:SS format

    // Insert data into the database (note: backticks for table name, not single quotes)
    $sql = "INSERT INTO `qr_management` (updater_name, email, field, date, time, estate) 
            VALUES ('$updater_name', '$email', '$range', '$currentDate', '$currentTime', '$estate')";

    if ($conn->query($sql) === TRUE) {
        // Redirect to plantation_management.php after successful insertion
        header("Location: plantation_management.php");
        exit();
    } else {
        // Error message if insertion fails
        echo "Error: " . $sql . "<br>" . $conn->error;
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

    <title>Fill QR Details</title>
</head>

<body class="bg-light-5 ">
    <h2 class="text-center my-5">Fill in the details</h2>
    <div class="container my-5 bg-light p-5 transparent-container">
        <form method="post">
            <div class="form-group">
                <label>Updater Name</label>
                <input type="text" class="form-control" placeholder="Enter Your Name" name="name" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <select class="form-control" name="email" required>
                    <option value="admin@gmail.com">admin@gmail.com</option>
                    <option value="sadaharitha@gmail.com">sadaharitha@gmail.com</option>
                </select>
            </div>
            <div class="form-group">
                <label>Range</label>
                <input type="text" class="form-control" placeholder="Enter Your QR Range" name="range" required>
            </div>
            <div class="form-group">
                <label>Estate QR Belongs to</label>
                <input type="text" class="form-control" placeholder="Enter the Estate" name="estate" required>
            </div>
            <button type="submit" class="btn btn-primary" name="submit">Submit</button>
        </form>
    </div>
</body>

</html>