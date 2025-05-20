<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: index.php');
    exit();
}

$borrower_id = intval($_GET['edit']);
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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $address = $_POST['address'];
    $contact_info = $_POST['contact_info'];
    $book_name = $_POST['book_name']; // New field

    $sql = "UPDATE borrowers SET name = '$name', address = '$address', contact_info = '$contact_info', book_name = '$book_name' WHERE borrower_id = $borrower_id";
    $conn->query($sql);

    header('Location: borrowers.php');
    exit();
}

// Fetch borrower details
$sql = "SELECT * FROM borrowers WHERE borrower_id = $borrower_id";
$result = $conn->query($sql);
$borrower = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Borrower</title>
    <link rel="stylesheet" href="edit_borrower.css">
</head>
<body>
    <div class="container">
        <h1>Edit Borrower</h1>
        <a href="borrowers.php" class="back-btn">Back to Borrowers</a>

        <!-- Edit Borrower Form -->
        <form method="post" class="edit-borrower-form">
            <input type="text" name="name" value="<?php echo $borrower['name']; ?>" placeholder="Name" required>
            <input type="text" name="address" value="<?php echo $borrower['address']; ?>" placeholder="Address" required>
            <input type="text" name="contact_info" value="<?php echo $borrower['contact_info']; ?>" placeholder="Contact Info" required>
            <input type="text" name="book_name" value="<?php echo $borrower['book_name']; ?>" placeholder="Book Name" required> <!-- New field -->
            <button type="submit">Update Borrower</button>
        </form>
    </div>
</body>
</html>