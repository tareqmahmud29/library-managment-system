<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <div class="dashboard-container">
        <h1>Admin Dashboard</h1>
        <nav>
            <a href="books.php">Manage Books</a>
            <a href="borrowers.php">Manage Borrowers</a>
            <a href="transactions.php">Manage Transactions</a>
            <a href="borrower_search.php">Borrower Search</a>
            <a href="total_books.php">Total Books</a>
            <a href="report.php">Report book</a>
            <a href="logout.php" class="logout-btn">Logout</a>
        </nav>
    </div>
</body>
</html>