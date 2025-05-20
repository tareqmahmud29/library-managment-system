<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: index.php');
    exit();
}

$transaction_id = intval($_GET['id']);
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
    $new_checkout_date = $_POST['checkout_date'];
    $new_return_date = $_POST['return_date']; // Can be null if the book hasn't been returned yet
    
    $stmt = $conn->prepare("UPDATE transactions SET checkout_date = ?, return_date = ? WHERE transaction_id = ?");
    $stmt->bind_param("ssi", $new_checkout_date, $new_return_date, $transaction_id);
    $stmt->execute();
    
    if ($stmt->error) {
        $_SESSION['error'] = "Error updating transaction: " . $stmt->error;
    } else {
        $_SESSION['success'] = "Transaction updated successfully!";
    }
    header('Location: transactions.php');
    exit();
}

// Fetch transaction details
$stmt = $conn->prepare("SELECT * FROM transactions WHERE transaction_id = ?");
$stmt->bind_param("i", $transaction_id);
$stmt->execute();
$transaction = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Transaction</title>
    <link rel="stylesheet" href="edit_transaction.css">
</head>
<body>
    <div class="container">
        <h1>Edit Transaction</h1>

        <!-- Display success/error messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <!-- Edit Transaction Form -->
        <form method="post" class="edit-transaction-form">
            <!-- Checkout Date -->
            <label for="checkout_date">Checkout Date:</label>
            <input type="date" name="checkout_date" id="checkout_date" value="<?php echo $transaction['checkout_date']; ?>" required>

            <!-- Return Date (if book has been returned) -->
            <label for="return_date">Return Date (optional):</label>
            <input type="date" name="return_date" id="return_date" value="<?php echo $transaction['return_date']; ?>">

            <button type="submit">Update Transaction</button>
        </form>

        <a href="transactions.php" class="back-btn">Back to Transactions</a>
    </div>
</body>
</html>