<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test";

// Create connection
$mysqli = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$sellers_reg_id = $_SESSION['registration_id'] ?? '';
$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $book_name = $_POST['book_name'];
    $book_price = $_POST['book_price'];
    $clg_year = $_POST['clg_year'];
    $subject = $_POST['subject'];
    $descryption = $_POST['descryption'];
    $book_img = null;

    if (isset($_FILES['book_img']) && $_FILES['book_img']['error'] == UPLOAD_ERR_OK) {
        $book_img = file_get_contents($_FILES['book_img']['tmp_name']);
    }

    $stmt = $mysqli->prepare("INSERT INTO books (sellers_reg_id, book_name, book_price, clg_year, subject, descryption, book_img) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdssss", $sellers_reg_id, $book_name, $book_price, $clg_year, $subject, $descryption, $book_img);

    if ($stmt->execute()) {
        $success = "Book listed for sale successfully.";
    } else {
        $error = "Failed to list book for sale.";
    }

    $stmt->close();
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sell Books</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #e0f7fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .navbar {
            background-color: #01579b;
            overflow: hidden;
            width: 100%;
            position: fixed;
            top: 0;
            z-index: 1000;
        }
        .navbar a {
            float: left;
            display: block;
            color: #e0f7fa;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
        }
        .navbar a:hover {
            background-color: #00c853;
            color: white;
        }
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            position: relative;
            width: 50%;
            margin-top: 100px; /* Space for the fixed navbar */
        }
        .container h2 {
            color: #01579b;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #004d40;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .form-group button {
            padding: 10px 20px;
            background-color: #01579b;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .form-group button:hover {
            background-color: #00c853;
        }
        .message {
            color: red;
            margin-top: 10px;
        }
        .success {
            color: green;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="home.php">Home</a>
        <a href="buy_books.php">Buy Books</a>
        <a href="sell_books.php">Sell Books</a>
        <a href="interview_preparation.php">Interview Preparation</a>
        <a href="student_record.php">Student Details</a>
    </div>
    
    <div class="container">
        <h2>Sell Books</h2>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="book_name">Book Name</label>
                <input type="text" id="book_name" name="book_name" required>
            </div>
            <div class="form-group">
                <label for="book_price">Book Price</label>
                <input type="number" id="book_price" name="book_price" required>
            </div>
            <div class="form-group">
                <label for="clg_year">College Year</label>
                <input type="number" id="clg_year" name="clg_year" required>
            </div>
            <div class="form-group">
                <label for="subject">Subject</label>
                <input type="text" id="subject" name="subject" required>
            </div>
            <div class="form-group">
                <label for="descryption">Description</label>
                <textarea id="descryption" name="descryption" rows="4" required></textarea>
            </div>
            <div class="form-group">
                <label for="book_img">Book Image</label>
                <input type="file" id="book_img" name="book_img" accept="image/*">
            </div>
            <div class="form-group">
                <button type="submit">List Book for Sale</button>
            </div>
            <?php if ($error): ?>
                <p class="message"><?= $error ?></p>
            <?php endif; ?>
            <?php if ($success): ?>
                <p class="message success"><?= $success ?></p>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>
