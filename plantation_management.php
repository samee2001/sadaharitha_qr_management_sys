<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('Location: logIn.php');
    exit();
}
include 'connect.php'; // Include your database connection file
// Set the number of records per page
$records_per_page = 5;

// Get the current page number from the URL, default to 1 if not set
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

// Calculate the starting record for the current page
$start_from = ($page - 1) * $records_per_page;

// Fetch data for the current page
$sql = "SELECT * FROM `qr_management` LIMIT $start_from, $records_per_page";
$result = $conn->query($sql);

// Fetch total number of records to calculate total pages
$total_records_query = "SELECT COUNT(*) as total_records FROM `qr_management`";
$total_records_result = $conn->query($total_records_query);
$total_records_row = $total_records_result->fetch_assoc();
$total_records = $total_records_row['total_records'];

// Calculate total pages
$total_pages = ceil($total_records / $records_per_page);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Details</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

</head>

<body style="background-image: url('sdh_bg_2.png'); background-size: cover;">
    <?php include 'components/navbar.php'; ?>
    <br><br><br><br>
    
    <div class="container my-5 p-5" style="background-color: rgb(231, 231, 231); border: 1px solid rgb(114, 234, 126); border-radius: 10px; opacity: 0.8;">
        <button type="button" class="btn btn-success"><a href="add_details.php"
                class="text-light text-decoration-none">Add QR Details</a></button>
        <table class="table my-5" >
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <!--<th scope="col">Name</th>-->
                    <th scope="col">Email</th>
                    <th scope="col">Start</th>
                    <th scope="col">Step</th>
                    <!--<th scope="col">Time</th>-->
                    <th scope="col">Issued Estate</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody >
                <?php
                // Fetch and display each row from the database
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                        //echo "<td>" . htmlspecialchars($row['updater_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($_SESSION['email']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['start']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['step']) . "</td>";
                        //echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['estate']) . "</td>";
                        echo "<td><button type='button' class='btn btn-primary'><a href='update_page.php?updateid=" . $row["id"] . "' class='text-light text-decoration-none'>Update</a></button></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center'>No data available</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <nav aria-label="Page navigation example">
            <ul class="pagination justify-content-center">
                <?php
                // Display Previous button
                if ($page > 1) {
                    echo '<li class="page-item"><a class="page-link" href="?page=' . ($page - 1) . '">Previous</a></li>';
                }

                // Display page numbers
                for ($i = 1; $i <= $total_pages; $i++) {
                    echo '<li class="page-item text-success fw-bold ' . ($i == $page ? 'active' : '') . '"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
                }

                // Display Next button
                if ($page < $total_pages) {
                    echo '<li class="page-item"><a class="page-link" href="?page=' . ($page + 1) . '">Next</a></li>';
                }
                ?>
            </ul>
        </nav>
    </div>
    <?php include 'components/footer.php'; ?>
</body>

</html>