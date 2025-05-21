<?php
session_start();
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

// Handle Search
$search_term = '';
$sql = "SELECT * FROM books";
if (isset($_POST['search'])) {
    $search_term = $_POST['search_term'];
    $sql = "SELECT * FROM books 
            WHERE title LIKE '%$search_term%' 
            OR author LIKE '%$search_term%'";
}
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Insights</title>
    <style>
        /* General Styles */
        body {
            font-family: 'Poppins', sans-serif;
            background: url('books4.jpg');
            background-size: cover; /* Ensure the image covers the entire body */
            background-position: center; /* Center the background image */
            background-attachment: fixed; /* Keep the background fixed when scrolling */
            color: #333;
            padding: 20px;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        h1 {
            text-align: center;
            color:rgb(52, 58, 64);
            font-size: 2em; /* Reduced font size for mobile */
            margin-bottom: 20px;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            padding: 20px;
        }

        /* Search Form Styles */
        form {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
            flex-direction: column; /* Stack form elements vertically on mobile */
            align-items: center;
        }

        input[type="text"] {
            padding: 10px;
            width: 100%; /* Full width on mobile */
            max-width: 400px; /* Limit width on larger screens */
            border: 2px solid #adb5bd;
            border-radius: 5px;
            font-size: 16px;
            outline: none;
            margin-bottom: 10px; /* Add spacing between input and button */
        }

        button {
            padding: 10px 20px;
            background-color: #495057;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
            width: 100%; /* Full width on mobile */
            max-width: 400px; /* Limit width on larger screens */
        }

        button:hover {
            background-color: #343a40;
        }

        /* Table Styles */
        table {
            border-collapse: collapse;
            width: 100%;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }

        th, td {
            padding: 12px; /* Slightly reduced padding for mobile */
            text-align: left;
            border-bottom: 1px solidrgb(186, 190, 194);
        }

        th {
            background-color: #495057;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        tr:hover {
            background-color: #f1f3f5;
        }

        td {
            font-size: 14px;
        }

        /* Responsive Design for Mobile */
        @media (max-width: 600px) {
            h1 {
                font-size: 1.5em; /* Smaller font size for mobile */
            }

            table, thead, tbody, th, td, tr {
                display: block; /* Convert table to block layout */
            }

            thead tr {
                position: absolute;
                top: -9999px;
                left: -9999px; /* Hide headers */
            }

            tr {
                margin-bottom: 10px; /* Add spacing between rows */
                border: 1px solid #dee2e6;
                border-radius: 5px;
            }

            td {
                border: none;
                position: relative;
                padding-left: 50%; /* Add space for labels */
                text-align: right;
            }

            td::before {
                content: attr(data-label); /* Add labels for each cell */
                position: absolute;
                left: 10px;
                width: 45%;
                padding-right: 10px;
                white-space: nowrap;
                text-align: left;
                font-weight: bold;
                color: #495057;
            }

            td:last-child {
                border-bottom: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Library Insights</h1>

        <!-- Search Form -->
        <form method="post">
            <input type="text" name="search_term" placeholder="Search by title or author" value="<?php echo $search_term; ?>">
            <button type="submit" name="search">Search</button>
        </form>

        <!-- Display Books -->
        <?php if ($result->num_rows > 0): ?>
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
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td data-label="Title"><?php echo $row['title']; ?></td>
                            <td data-label="Author"><?php echo $row['author']; ?></td>
                            <td data-label="Publisher"><?php echo $row['publisher']; ?></td>
                            <td data-label="Year"><?php echo $row['year']; ?></td>
                            <td data-label="Availability"><?php echo $row['is_available'] ? 'Available' : 'Checked Out'; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No books found.</p>
        <?php endif; ?>
    </div>
</body>
</html> +