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
try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}

// Fetch all books
$books = $conn->query("SELECT * FROM books");
$total_books = $books->num_rows; // Total number of books
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Total Book Inventory</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Total Book Inventory</h1>
        <a href="dashboard.php" class="back-btn">Back to Dashboard</a>

        <!-- Total Book Count -->
        <div class="total-count">
            <strong>Total Books in Library:</strong> <?= $total_books ?>
        </div>

        <!-- Total Book List -->
        <h2>Book List</h2>
        <?php if ($total_books > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Publisher</th>
                        <th>Year</th>
                        <th>Availability</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($book = $books->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($book['title']) ?></td>
                            <td><?= htmlspecialchars($book['author']) ?></td>
                            <td><?= htmlspecialchars($book['publisher']) ?></td>
                            <td><?= htmlspecialchars($book['year']) ?></td>
                            <td class="<?= $book['is_available'] ? 'available' : 'borrowed' ?>">
                                <?= $book['is_available'] ? 'Available' : 'Borrowed' ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-data">No books found in the library</p>
        <?php endif; ?>
    </div>
</body>
</html>