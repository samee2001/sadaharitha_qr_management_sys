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
</head>

<body class="bg-light-5 ">
    <h2 class="text-center my-5" style="color: #30bf32;">Update the QR Details</h2>
    <div class="container my-5 bg-light p-5 transparent-container "
        style="max-width: 800px; opacity: 0.75; border-radius: 10px;">
        <form method="post">
            <div class="form-group">
                <label>Name</label>
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
                <select name="estate" class="form-control" required>
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
            <div class="text-center "><button type="submit" class="btn btn-success" name="submit">Submit</button></div>
        </form>
    </div>
</body>

</html>