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

// Fetch total number of books
$total_books = $conn->query("SELECT COUNT(*) AS total FROM books")->fetch_assoc()['total'];

// Fetch total number of borrowers
$total_borrowers = $conn->query("SELECT COUNT(*) AS total FROM borrowers")->fetch_assoc()['total'];

// Fetch total number of transactions
$total_transactions = $conn->query("SELECT COUNT(*) AS total FROM transactions")->fetch_assoc()['total'];

// Fetch borrowed books with borrower details
$borrowed_books = $conn->query("
    SELECT b.title, br.name AS borrower_name, t.checkout_date, t.return_date 
    FROM transactions t
    JOIN books b ON t.book_id = b.book_id
    JOIN borrowers br ON t.borrower_id = br.borrower_id
    WHERE t.return_date IS NULL
");

// Fetch available books
$available_books = $conn->query("SELECT title, author, publisher, year, quantity FROM books WHERE is_available = TRUE");

// Fetch overdue books (example: books not returned within 14 days)
$overdue_books = $conn->query("
    SELECT b.title, br.name AS borrower_name, t.checkout_date 
    FROM transactions t
    JOIN books b ON t.book_id = b.book_id
    JOIN borrowers br ON t.borrower_id = br.borrower_id
    WHERE t.return_date IS NULL AND t.checkout_date < DATE_SUB(CURDATE(), INTERVAL 14 DAY)
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Report</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Library Report</h1>
        <a href="dashboard.php" class="back-btn">Back to Dashboard</a>

        <!-- Summary Section -->
        <h2>Summary</h2>
        <div class="summary">
            <p><strong>Total Books:</strong> <?= $total_books ?></p>
            <p><strong>Total Borrowers:</strong> <?= $total_borrowers ?></p>
            <p><strong>Total Transactions:</strong> <?= $total_transactions ?></p>
        </div>

        <!-- Borrowed Books Section -->
        <h2>Borrowed Books</h2>
        <?php if ($borrowed_books->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Book Title</th>
                        <th>Borrower Name</th>
                        <th>Checkout Date</th>
                        <th>Return Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $borrowed_books->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['title']) ?></td>
                            <td><?= htmlspecialchars($row['borrower_name']) ?></td>
                            <td><?= htmlspecialchars($row['checkout_date']) ?></td>
                            <td><?= $row['return_date'] ? htmlspecialchars($row['return_date']) : 'Not Returned' ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-data">No books currently borrowed</p>
        <?php endif; ?>

        <!-- Available Books Section -->
        <h2>Available Books</h2>
        <?php if ($available_books->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Publisher</th>
                        <th>Year</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $available_books->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['title']) ?></td>
                            <td><?= htmlspecialchars($row['author']) ?></td>
                            <td><?= htmlspecialchars($row['publisher']) ?></td>
                            <td><?= htmlspecialchars($row['year']) ?></td>
                            <td><?= htmlspecialchars($row['quantity']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-data">No books currently available</p>
        <?php endif; ?>

        <!-- Overdue Books Section -->
        <h2>Overdue Books</h2>
        <?php if ($overdue_books->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Book Title</th>
                        <th>Borrower Name</th>
                        <th>Checkout Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $overdue_books->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['title']) ?></td>
                            <td><?= htmlspecialchars($row['borrower_name']) ?></td>
                            <td><?= htmlspecialchars($row['checkout_date']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-data">No overdue books</p>
        <?php endif; ?>
    </div>
</body>
</html>