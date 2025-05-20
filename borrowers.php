<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: index.php');
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "library_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Add Borrower
if (isset($_POST['add_borrower'])) {
    $name = $_POST['name'];
    $address = $_POST['address'];
    $contact_info = $_POST['contact_info'];
    $book_name = $_POST['book_name']; // New field

    $sql = "INSERT INTO borrowers (name, address, contact_info, book_name) VALUES ('$name', '$address', '$contact_info', '$book_name')";
    $conn->query($sql);
}

// Handle Delete Borrower
if (isset($_GET['delete'])) {
    $borrower_id = $_GET['delete'];
    $sql = "DELETE FROM borrowers WHERE borrower_id = $borrower_id";
    $conn->query($sql);
}

// Fetch All Borrowers
$sql = "SELECT * FROM borrowers";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Borrowers</title>
    <link rel="stylesheet" href="borrowers.css">
</head>
<body>
    <div class="container">
        <h1>Manage Borrowers</h1>
        <a href="dashboard.php" class="back-btn">Back to Dashboard</a>

        <!-- Add Borrower Form -->
        <h2>Add New Borrower</h2>
        <form method="post" class="add-borrower-form">
            <input type="text" name="name" placeholder="Name" required>
            <input type="text" name="address" placeholder="Address" required>
            <input type="text" name="contact_info" placeholder="Contact Info" required>
            <input type="text" name="book_name" placeholder="Book Name" required> <!-- New field -->
            <button type="submit" name="add_borrower">Add Borrower</button>
        </form>

        <!-- Borrower List -->
        <h2>Borrower List</h2>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Contact Info</th>
                    <th>Book Name</th> <!-- New column -->
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['address']; ?></td>
                        <td><?php echo $row['contact_info']; ?></td>
                        <td><?php echo $row['book_name']; ?></td> <!-- New column -->
                        <td>
                            <a href="edit_borrower.php?edit=<?php echo $row['borrower_id']; ?>" class="edit-btn">Edit</a> |
                            <a href="borrowers.php?delete=<?php echo $row['borrower_id']; ?>" class="delete-btn" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>