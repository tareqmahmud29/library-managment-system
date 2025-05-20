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

$borrower_id = null;
$borrower_details = null;
$borrowed_books = [];

// Handle Search
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_borrower'])) {
    $search_term = trim($_POST['search_term']);

    // Search by ID or Name
    if (is_numeric($search_term)) {
        // Search by ID
        $borrower_id = intval($search_term);
        $stmt = $conn->prepare("SELECT * FROM borrowers WHERE borrower_id = ?");
        $stmt->bind_param("i", $borrower_id);
    } else {
        // Search by Name
        $search_term = "%$search_term%";
        $stmt = $conn->prepare("SELECT * FROM borrowers WHERE name LIKE ?");
        $stmt->bind_param("s", $search_term);
    }

    $stmt->execute();
    $borrower_details = $stmt->get_result()->fetch_assoc();

    // Fetch borrowed books if borrower found
    if ($borrower_details) {
        $borrower_id = $borrower_details['borrower_id'];
        $stmt = $conn->prepare("SELECT b.title, t.checkout_date, t.return_date 
                              FROM transactions t
                              JOIN books b ON t.book_id = b.book_id
                              WHERE t.borrower_id = ?");
        $stmt->bind_param("i", $borrower_id);
        $stmt->execute();
        $borrowed_books = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrower Search</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Borrower Search</h1>
        <a href="dashboard.php" class="back-btn">Back to Dashboard</a>

        <!-- Search Form -->
        <form method="post" class="search-form">
            <div class="form-group">
                <label>Search by Name or ID:</label>
                <input type="text" name="search_term" placeholder="Enter borrower name or ID" required>
                <button type="submit" name="search_borrower">Search</button>
            </div>
        </form>

        <!-- Borrower Details -->
        <?php if ($borrower_details): ?>
            <div class="borrower-details">
                <h2>Borrower Details</h2>
                <p><strong>Name:</strong> <?= htmlspecialchars($borrower_details['name']) ?></p>
                <p><strong>Address:</strong> <?= htmlspecialchars($borrower_details['address']) ?></p>
                <p><strong>Contact Info:</strong> <?= htmlspecialchars($borrower_details['contact_info']) ?></p>
            </div>

            <!-- Borrowed Books -->
            <div class="borrowed-books">
                <h2>Borrowed Books</h2>
                <?php if (count($borrowed_books) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Book Title</th>
                                <th>Checkout Date</th>
                                <th>Return Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($borrowed_books as $book): ?>
                                <tr>
                                    <td><?= htmlspecialchars($book['title']) ?></td>
                                    <td><?= htmlspecialchars($book['checkout_date']) ?></td>
                                    <td><?= $book['return_date'] ? htmlspecialchars($book['return_date']) : 'Not Returned' ?></td>
                                    <td class="<?= $book['return_date'] ? 'returned' : 'borrowed' ?>">
                                        <?= $book['return_date'] ? 'Returned' : 'Borrowed' ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="no-books">No books currently borrowed</p>
                <?php endif; ?>
            </div>
        <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <p class="no-data">No borrower found with the given search term</p>
        <?php endif; ?>
    </div>
</body>
</html>