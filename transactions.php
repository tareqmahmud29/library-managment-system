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

// Create connection with error handling
try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}

// Handle Delete
if (isset($_GET['delete'])) {
    try {
        $transaction_id = intval($_GET['delete']);
        $stmt = $conn->prepare("DELETE FROM transactions WHERE transaction_id = ?");
        $stmt->bind_param("i", $transaction_id);
        
        if (!$stmt->execute()) {
            throw new Exception("Delete failed: " . $stmt->error);
        }
        
        $_SESSION['success'] = "Transaction deleted successfully!";
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
    header('Location: transactions.php');
    exit();
}

// Handle Return Book
if (isset($_GET['return'])) {
    try {
        $transaction_id = intval($_GET['return']);
        
        // Start transaction
        $conn->begin_transaction();
        
        // Update return date
        $stmt = $conn->prepare("UPDATE transactions SET return_date = CURDATE() WHERE transaction_id = ?");
        $stmt->bind_param("i", $transaction_id);
        $stmt->execute();
        
        // Update book availability
        $stmt = $conn->prepare("UPDATE books b
                              JOIN transactions t ON b.book_id = t.book_id
                              SET b.is_available = TRUE 
                              WHERE t.transaction_id = ?");
        $stmt->bind_param("i", $transaction_id);
        $stmt->execute();
        
        $conn->commit();
        $_SESSION['success'] = "Book returned successfully!";
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = "Error returning book: " . $e->getMessage();
    }
    header('Location: transactions.php');
    exit();
}

// Handle New Transaction
if (isset($_POST['add_transaction'])) {
    try {
        $book_id = intval($_POST['book_id']);
        $borrower_id = intval($_POST['borrower_id']);
        $checkout_date = $conn->real_escape_string($_POST['checkout_date']);
        
        // Validate date format
        if (!DateTime::createFromFormat('Y-m-d', $checkout_date)) {
            throw new Exception("Invalid date format");
        }

        // Start transaction
        $conn->begin_transaction();
        
        // Insert transaction
        $stmt = $conn->prepare("INSERT INTO transactions (book_id, borrower_id, checkout_date) 
                               VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $book_id, $borrower_id, $checkout_date);
        $stmt->execute();
        
        // Update book availability
        $stmt = $conn->prepare("UPDATE books SET is_available = FALSE WHERE book_id = ?");
        $stmt->bind_param("i", $book_id);
        $stmt->execute();
        
        $conn->commit();
        $_SESSION['success'] = "Transaction added successfully!";
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
    header('Location: transactions.php');
    exit();
}

// Fetch data
try {
    // Available books (for dropdown)
    $books = $conn->query("SELECT * FROM books WHERE is_available = TRUE");
    if (!$books) throw new Exception("Error fetching books: " . $conn->error);
    
    // All borrowers
    $borrowers = $conn->query("SELECT * FROM borrowers");
    if (!$borrowers) throw new Exception("Error fetching borrowers: " . $conn->error);
    
    // Transaction list
    $transactions = $conn->query("SELECT t.transaction_id, b.title, br.name, 
                                 t.checkout_date, t.return_date 
                                 FROM transactions t
                                 JOIN books b ON t.book_id = b.book_id
                                 JOIN borrowers br ON t.borrower_id = br.borrower_id");
    if (!$transactions) throw new Exception("Error fetching transactions: " . $conn->error);
    
} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Transactions</title>
    <link rel="stylesheet" href="chatgpt.css">
</head>
<body>
    <div class="container">
        <h1>Manage Transactions</h1>
        <a href="dashboard.php" class="back-btn">Back to Dashboard</a>

        <!-- Display messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <!-- Add Transaction Form -->
        <h2>Create New Transaction</h2>
        <form method="post" class="transaction-form">
            <div class="form-group">
                <label>Book:</label>
                <select name="book_id" required>
                    <?php if ($books->num_rows > 0): ?>
                        <?php while($book = $books->fetch_assoc()): ?>
                            <option value="<?= $book['book_id'] ?>"><?= htmlspecialchars($book['title']) ?></option>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <option disabled selected>No available books</option>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Borrower:</label>
                <select name="borrower_id" required>
                    <?php if ($borrowers->num_rows > 0): ?>
                        <?php while($borrower = $borrowers->fetch_assoc()): ?>
                            <option value="<?= $borrower['borrower_id'] ?>"><?= htmlspecialchars($borrower['name']) ?></option>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <option disabled selected>No registered borrowers</option>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Checkout Date:</label>
                <input type="date" name="checkout_date" 
                       value="<?= date('Y-m-d') ?>" 
                       min="2000-01-01" 
                       max="<?= date('Y-m-d') ?>" 
                       required>
            </div>

            <button type="submit" name="add_transaction" 
                <?= ($books->num_rows === 0 || $borrowers->num_rows === 0) ? 'disabled' : '' ?>>
                Create Transaction
            </button>
        </form>

        <!-- Transaction List -->
        <h2>Transaction List</h2>
        <?php if ($transactions->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Book Title</th>
                        <th>Borrower Name</th>
                        <th>Checkout Date</th>
                        <th>Return Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $transactions->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['title']) ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['checkout_date']) ?></td>
                            <td><?= $row['return_date'] ? htmlspecialchars($row['return_date']) : 'Not Returned' ?></td>
                            <td>
                                <a href="edit_transaction.php?id=<?= $row['transaction_id'] ?>" class="btn-edit">Edit</a>
                                <?php if(!$row['return_date']): ?>
                                    | <a href="transactions.php?return=<?= $row['transaction_id'] ?>" class="btn-return">Return</a>
                                <?php endif; ?>
                                | <a href="transactions.php?delete=<?= $row['transaction_id'] ?>" 
                                   class="btn-delete" 
                                   onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-data">No transactions found</p>
        <?php endif; ?>
    </div>
    
</body>
</html> 