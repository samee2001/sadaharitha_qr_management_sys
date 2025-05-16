<?php
session_start();

// Include the database connection file
include 'connect.php';
$id = $_GET['updateid'];
if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $estate = $_POST['estate'];
    $range = $_POST['range'];

    $sql = "UPDATE `qr_management` SET updater_name='$name', email='$email', estate='$estate', field='$range' WHERE id=$id"; // Replace 'table_name' with your actual table name='$mobile', password='$password' WHERE id=$id";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        $_SESSION['statusupdate'] = 'Record Updated Successfully!';
        header('location:plantation_management.php');
    } else {
        die(mysqli_error($conn));
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
    <title>Update QR Details</title>

    <style>
        .btn:hover {
            background-color: white !important;
            color: black !important;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container my-5">
        <h2 class="text-center my-3" style="color: rgb(210, 234, 211);">Update the QR Details</h2>
        <h4 class="text-center my-3" style="color:rgb(210, 234, 211);">Please fill the details below to update the
            Record</h4>

        <div class="card p-4 mx-auto bg-light" style="max-width: 800px; opacity: 0.85; border-radius: 10px;">
            <form method="post" id="updateForm">
                <!-- Name Input with Icon -->
                <div class="form-group mb-4">
                    <label for="name" class="form-label">Name</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                        </div>
                        <input type="text" class="form-control" placeholder="Enter Your Name" name="name" id="name"
                            required>
                    </div>
                </div>

                <!-- Email Dropdown with Icon -->
                <div class="form-group mb-4">
                    <label for="email" class="form-label">Email</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        </div>
                        <input type="email" class="form-control" placeholder="Enter Your Email" name="email" id="email"
                            required>
                    </div>
                </div>

                <!-- QR Range Input with Icon -->
                <div class="form-group mb-4">
                    <label for="range" class="form-label">Range</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="bi bi-hash"></i></span>
                        </div>
                        <input type="text" class="form-control" placeholder="Enter Your QR Range" name="range"
                            id="range">
                    </div>
                </div>

                <!-- Estate Dropdown with Icon -->
                <div class="form-group mb-4">
                    <label for="estate" class="form-label">Estate QR Belongs to</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="bi bi-house-door"></i></span>
                        </div>
                        <select name="estate" class="form-control" id="estate">
                            <option value="">Select an Estate</option>
                            <?php
                            // Fetch values from the 'estate' table using the SELECT command
                            $sql = "SELECT estate_name FROM estate";
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                                // Output data of each row
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='" . $row['estate_name'] . "'>" . $row['estate_name'] . "</option>"; // Adjust column name accordingly
                                }
                            } else {
                                echo "<option value=''>No estates available</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="btn" style="width: 100%; background-color: green;"><button type="submit" name="submit" class="btn"
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