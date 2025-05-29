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
$sql = "SELECT * FROM `users` LIMIT $start_from, $records_per_page";
$result = $conn->query($sql);

// Fetch total number of records to calculate total pages
$total_records_query = "SELECT COUNT(*) as total_records FROM `users`";
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
    <title>Manage Users</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

</head>

<body style="background-image: url('sdh_bg_2.png'); background-size: cover;">
    <?php include 'components/navbar.php'; ?>
    <br><br><br><br>
    <?php
    if (isset($_SESSION['statusupdate'])) {
        ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Success!</strong> <?php echo $_SESSION['statusupdate']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php
        echo $_SESSION['statusupdate'];
        unset($_SESSION['statusupdate']);
    }
    if (isset($_SESSION['statusdelete'])) {
        ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Success!</strong> <?php echo $_SESSION['statusdelete']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php
        echo $_SESSION['statusdelete'];
        unset($_SESSION['statusdelete']);
    }
    ?>
    <div class="container my-3 p-3 w-75 "
        style="background-color: rgb(231, 231, 231); border: 1px solid rgb(114, 234, 126); border-radius: 10px; opacity: 0.8;">
        <button type="button" class="btn btn-success"><a href="manage_users_add.php"
                class="text-light text-decoration-none">Add User</a></button>
        <table class="table my-4">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Name</th>
                    <th scope="col">E-mail</th>
                    <th scope="col" class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch and display each row from the database
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                        echo "<td class='text-center'>";
                        echo "<a href='manage_users_update.php?updateid=" . $row["id"] . "' class='btn btn-primary btn-sm me-2 text-light text-decoration-none'>Update</a>";
                        echo "<a href='manage_users_delete.php?deleteid=" . $row["id"] . "' class='btn btn-danger btn-sm text-light text-decoration-none' onclick='return confirm(\"Are you sure you want to delete this record?\");'>Delete</a>";
                        echo "</td>";
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