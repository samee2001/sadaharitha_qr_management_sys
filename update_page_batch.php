<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('Location: logIn.php');
    exit();
}

// Include the database connection file
include 'connect.php';
$email = $_SESSION['email'];
$id = isset($_GET['updateid']) ? (int)$_GET['updateid'] : 0;

// Fetch current values for pre-filling the form
$currentData = [];
$sql_select = "SELECT start, step, background_color FROM `qr_batch_details` WHERE batch_id = ?";
$stmt_select = mysqli_prepare($conn, $sql_select);
if ($stmt_select) {
    mysqli_stmt_bind_param($stmt_select, "i", $id);
    mysqli_stmt_execute($stmt_select);
    $result_select = mysqli_stmt_get_result($stmt_select);
    $currentData = mysqli_fetch_assoc($result_select);
    mysqli_stmt_close($stmt_select);
} else {
    die("Prepare failed: " . mysqli_error($conn));
}

$currentStart = isset($currentData['start']) ? $currentData['start'] : '';
$currentStep = isset($currentData['step']) ? $currentData['step'] : '';
//$currentEstate = isset($currentData['estate']) ? $currentData['estate'] : '';
$currentColor = isset($currentData['background_color']) ? $currentData['background_color'] : '';

if (isset($_POST['submit'])) {
    // Get and validate form data
    $start = isset($_POST['rangeStart']) ? (int)$_POST['rangeStart'] : 1;
    $step = isset($_POST['rangeStep']) ? (int)$_POST['rangeStep'] : 1;
    //$estate = isset($_POST['estate']) ? trim($_POST['estate']) : '';
    $color = isset($_POST['cellColorSelect']) ? trim($_POST['cellColorSelect']) : '';

    // Validate inputs
    // if (empty($estate)) {
    //     die("Error: Estate is required.");
    // }
    if ($start < 1 || $step < 1 || $step > 5000) {
        die("Error: Start must be positive and step must be between 1 and 5000.");
    }
    if (!preg_match('/^\d{1,3},\d{1,3},\d{1,3}$/', $color)) {
        die("Error: Invalid RGB color format.");
    }

    // Validate color exists in colors table
    $color_check = $conn->prepare("SELECT color_code FROM colors WHERE color_code = ?");
    $color_check->bind_param("s", $color);
    $color_check->execute();
    $result = $color_check->get_result();
    if ($result->num_rows === 0) {
        die("Error: Selected color is invalid.");
    }
    $color_check->close();

    // Update the record using prepared statement
    $sql = "UPDATE `qr_batch_details` SET start = ?, step = ?,  background_color = ? WHERE batch_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "iisi", $start, $step,  $color, $id);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['statusupdate'] = 'Record Updated Successfully!';
            header('Location: plantation_management.php');
            exit();
        } else {
            die("Error: " . htmlspecialchars(mysqli_stmt_error($stmt)));
        }
        mysqli_stmt_close($stmt);
    } else {
        die("Prepare failed: " . htmlspecialchars(mysqli_error($conn)));
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="styles.css">

    <title>Update Batch Details</title>

    <style>
        .btn:hover {
            background-color: white !important;
            color: black !important;
        }
    </style>
</head>

<body class="bg-light">
    <?php include 'components/navbar.php'; ?>
    <br>
    <div class="container my-5">
        <h2 class="text-center my-3" style="color: rgb(210, 234, 211);">Update Batch Details</h2>
        <h4 class="text-center my-3" style="color: rgb(210, 234, 211);">Please update the details below</h4>

        <div class="card p-4 mx-auto bg-light" style="max-width: 800px; opacity: 0.85; border-radius: 10px;">
            <form method="post" id="qrForm">
                <div class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label for="rangeStart" class="form-label">Start</label>
                        <input type="number" class="form-control" id="rangeStart" name="rangeStart" value="<?= htmlspecialchars($currentStart) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="rangeStep" class="form-label">Step</label>
                        <input type="number" class="form-control" id="rangeStep" name="rangeStep" max="5000" min="1" value="<?= htmlspecialchars($currentStep) ?>" required>
                    </div>
                </div>
                <div class="form-text">Specify the starting row and how many QR codes to generate (step, max 5000)</div>
                <br>
                <div class="form-group mb-4">
                    <label for="cellColorSelect" class="form-label">Background Color</label>
                    <div class="input-group">
                        <select name="cellColorSelect" id="cellColorSelect" class="form-select" required>
                            <option value="">Select a Color</option>
                            <?php
                            $sql = "SELECT color_name, color_code FROM colors";
                            $result = $conn->query($sql);
                            if ($result && $result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $color_code = htmlspecialchars($row['color_code']);
                                    $color_name = ucfirst(htmlspecialchars(strtolower(trim($row['color_name']))));
                                    $selected = ($color_code === $currentColor) ? 'selected' : '';
                            ?>
                                    <option value="<?= $color_code ?>" <?= $selected ?>
                                        style="background-color: rgb(<?= $color_code ?>)">
                                        <?= $color_name ?>
                                    </option>
                            <?php
                                }
                                $result->free();
                            } else {
                                echo "<option value=''>No colors available</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                
                <!-- Submit Button -->
                <div class="btn" style="width: 100%; background-color: green;">
                    <button type="submit" name="submit"
                        style="background-color: green; border: none;  color: white; font-size: 17px;"
                        class="btn">Update Details</button>
                </div>
        </div>
        </form>
    </div>

    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <?php include 'components/footer.php'; ?>
</body>

</html>