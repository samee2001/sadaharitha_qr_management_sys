<?php
// Include the database connection file
session_start();
include 'connect.php'; // Ensure the database connection is included

$email = $_SESSION['email'];
if (isset($_POST['submit'])) {
    // Get the data from the form
    $start = $_POST['rangeStart'];
    $step = $_POST['rangeStep'];
    $estate = $_POST['estate'];
    $background_color = $_POST['cellColorSelect'];


    // Prepare the SQL statement to prevent SQL injection
    $sql = "INSERT INTO qr_management (email, start, step, estate, background_color) 
            VALUES (?, ?, ?, ?, ?)";

    // Use a prepared statement
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        // Bind the parameters (all integers for start and step, strings for others)
        $stmt->bind_param("siiss", $email, $start, $step, $estate, $background_color);

        // Execute the statement
        if ($stmt->execute()) {
            // Redirect to plantation_management.php after successful insertion
            header("Location: generate_pdf.php");
            exit();
        } else {
            // Error message if insertion fails
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Prepare failed: " . $conn->error;
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

    <title>Fill QR Details</title>

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
        <h2 class="text-center my-3" style="color: rgb(210, 234, 211);">Fill the QR Details</h2>
        <h4 class="text-center my-3" style="color: rgb(210, 234, 211);">Please fill the details below to add a new Record</h4>
        <section id="qrFormSection">
            <div class="card p-4 mx-auto bg-light" style="max-width: 800px; opacity: 0.85; border-radius: 10px;">
                <form method="post" id="qrForm">
                    <!-- Email Input with Icon -->
                    <!--<div class="form-group mb-4">
                        <label for="email" class="form-label">Email</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            </div>
                            <input type="email" class="form-control" placeholder="Enter Your Email" name="email" id="email" required>
                        </div>
                    </div>-->
                    <div class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <label for="rangeStart" class="form-label">Start</label>
                            <input type="number" class="form-control" id="rangeStart" name="rangeStart" required>
                        </div>
                        <div class="col-md-6">
                            <label for="rangeStep" class="form-label">Step</label>
                            <input type="number" class="form-control" id="rangeStep" name="rangeStep" max="5000" min="1" required>
                        </div>
                    </div>
                    <div class="form-text">Specify the starting row and how many QR codes to generate (step, max 5000)</div>
                    <br>
                    <div class="form-group mb-4">
                        <label for="cellColorSelect" class="form-label">Background Color</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="bi bi-palette"></i></span>
                            </div>
                            <select name="cellColorSelect" id="cellColorSelect" class="form-select" required>
                                <option value="">Select a Color</option>
                                <?php
                                $sql = "SELECT color_name, color_code FROM colors";
                                $result = $conn->query($sql);
                                if ($result && $result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()):
                                        $normalized = strtolower(trim($row['color_name']));
                                ?>
                                        <option value="<?= htmlspecialchars($normalized) ?>"
                                            style="background-color: rgb(<?= $row['color_code'] ?>)">
                                            <?= ucfirst(htmlspecialchars($normalized)) ?>
                                        </option>
                                <?php endwhile;
                                    $result->free();
                                } else {
                                    echo "<option value=''>No colors available</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <!-- Estate Dropdown with Icon -->
                    <div class="form-group mb-4">
                        <label for="estate" class="form-label">Estate QR Belongs to</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="bi bi-house-door"></i></span>
                            </div>
                            <select name="estate" class="form-control" id="estate" required>
                                <option value="">Select an Estate</option>
                                <?php
                                $sql = "SELECT estate_name FROM estate";
                                $result = $conn->query($sql);
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<option value='" . $row['estate_name'] . "'>" . $row['estate_name'] . "</option>";
                                    }
                                } else {
                                    echo "<option value=''>No estates available</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <!-- Submit Button -->
                    <div class="btn" style="width: 100%; background-color: green;">
                        <button type="submit" name="submit"
                            style="background-color: green; border: none; width: 100%; color: white; font-size: 17px;"
                            class="btn">Add Details</button>
                    </div>
                </form>
            </div>
        </section>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Smooth Scroll JavaScript -->
    <?php include 'components/footer.php'; ?>
</body>
</html>