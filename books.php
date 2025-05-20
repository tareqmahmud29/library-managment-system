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

// Handle Add Book
if (isset($_POST['add_book'])) {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $publisher = $_POST['publisher'];
    $year = $_POST['year'];
    $quantity = intval($_POST['quantity']);

    // Insert book with quantity
    $sql = "INSERT INTO books (title, author, publisher, year, quantity) 
            VALUES ('$title', '$author', '$publisher', $year, $quantity)";
    $conn->query($sql);
}

// Handle Delete Book
if (isset($_GET['delete'])) {
    $book_id = $_GET['delete'];
    $sql = "DELETE FROM books WHERE book_id = $book_id";
    $conn->query($sql);
}

// Handle Edit Quantity
if (isset($_POST['edit_quantity'])) {
    $book_id = $_POST['book_id'];
    $new_quantity = intval($_POST['new_quantity']);

    // Update quantity in the database
    $sql = "UPDATE books SET quantity = $new_quantity WHERE book_id = $book_id";
    $conn->query($sql);
}

// Handle Search
$search_term = '';
if (isset($_GET['search'])) {
    $search_term = $_GET['search_term'];
    $sql = "SELECT * FROM books WHERE title LIKE '%$search_term%' OR author LIKE '%$search_term%'";
} else {
    $sql = "SELECT * FROM books";
}
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Books</title>
    <link rel="stylesheet" href="books.css">
</head>
<body>
    <div class="container">
        <h1>Manage Books</h1>
        <a href="dashboard.php" class="back-btn">Back to Dashboard</a>

        <!-- Add Search Form -->
        <h2>Search Books</h2>
        <form method="get" class="search-form">
            <input type="text" name="search_term" placeholder="Search by title or author" value="<?php echo $search_term; ?>">
            <button type="submit" name="search">Search</button>
        </form>

        <!-- Add Book Form -->
        <h2>Add New Book</h2>
        <form method="post" class="add-book-form">
            <input type="text" name="title" placeholder="Title" required>
            <input type="text" name="author" placeholder="Author" required>
            <input type="text" name="publisher" placeholder="Publisher" required>
            <input type="number" name="year" placeholder="Year" required>
            <input type="number" name="quantity" placeholder="Quantity" min="1" required>
            <button type="submit" name="add_book">Add Book</button>
        </form>

        <!-- Book List -->
        <h2>Book List</h2>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Publisher</th>
                    <th>Year</th>
                    <th>Quantity</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['title']; ?></td>
                        <td><?php echo $row['author']; ?></td>
                        <td><?php echo $row['publisher']; ?></td>
                        <td><?php echo $row['year']; ?></td>
                        <td><?php echo $row['quantity']; ?></td>
                        <td>
                            <!-- Edit Quantity Form -->
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="book_id" value="<?php echo $row['book_id']; ?>">
                                <input type="number" name="new_quantity" value="<?php echo $row['quantity']; ?>" min="0" required>
                                <button type="submit" name="edit_quantity" class="edit-btn">Edit</button>
                            </form>
                            <a href="books.php?delete=<?php echo $row['book_id']; ?>" class="delete-btn" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>