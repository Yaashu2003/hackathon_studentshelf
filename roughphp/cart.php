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

$search_year = $_GET['search_year'] ?? '';
$search_subject = $_GET['search_subject'] ?? '';
$search_name = $_GET['search_name'] ?? '';

$query = "SELECT * FROM book WHERE 1=1";
if ($search_year) {
    $query .= " AND clg_year = '$search_year'";
}
if ($search_subject) {
    $query .= " AND subject LIKE '%$search_subject%'";
}
if ($search_name) {
    $query .= " AND book_name LIKE '%$search_name%'";
}

$result = $mysqli->query($query);

$books = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
}

$mysqli->close();

$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Buying Website - Buy Books</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #aee7f8;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: auto;
            overflow: hidden;
        }

        header {
            background: #0073aa;
            color: #fff;
            padding-top: 30px;
            min-height: 70px;
            border-bottom: #0779e4 3px solid;
        }

        header a {
            color: #fff;
            text-decoration: none;
            text-transform: uppercase;
            font-size: 16px;
        }

        header ul {
            padding: 0;
            list-style: none;
        }

        header li {
            float: left;
            display: inline;
            padding: 0 20px 0 20px;
        }

        header #branding {
            float: left;
        }

        header #branding h1 {
            margin: 0;
        }

        header nav {
            float: right;
            margin-top: 10px;
        }

        #main-content {
            padding: 20px;
            background: #fff;
            margin-top: 10px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input,
        .form-group select {
            width: 95%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .btn {
            display: inline-block;
            background: #333;
            color: #fff;
            padding: 10px 20px;
            text-align: center;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn:hover {
            background: #0779e4;
        }

        .book {
            border-bottom: 1px solid #ccc;
            padding: 10px 0;
        }

        .book img {
            width: 100px;
            height: 100px;
            object-fit: cover;
        }

        .book-details {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .book-info {
            display: flex;
            align-items: center;
        }

        .book-info div {
            margin-left: 20px;
        }

        .book-info h3 {
            margin: 0;
        }
        
        .cart-count {
            background: red;
            color: white;
            border-radius: 50%;
            padding: 5px 10px;
            font-size: 12px;
            vertical-align: super;
        }
    </style>
</head>

<body>
    <header>
        <div class="container">
            <div id="branding">
                <h1>Book Buying Website</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="register.html">Buyers Registration</a></li>
                    <li><a href="cart.php">Cart <span class="cart-count"><?= $cart_count ?></span></a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <div id="main-content">
            <h2>Buy Books</h2>
            <form method="get" action="">
                <div class="form-group">
                    <label for="search_year">Year:</label>
                    <input type="text" id="search_year" name="search_year" value="<?= htmlspecialchars($search_year) ?>">
                </div>
                <div class="form-group">
                    <label for="search_subject">Subject:</label>
                    <input type="text" id="search_subject" name="search_subject" value="<?= htmlspecialchars($search_subject) ?>">
                </div>
                <div class="form-group">
                    <label for="search_name">Book Name:</label>
                    <input type="text" id="search_name" name="search_name" value="<?= htmlspecialchars($search_name) ?>">
                </div>
                <button type="submit" class="btn">Search</button>
            </form>
            <hr>
            <?php if (empty($books)): ?>
                <p>No books found</p>
            <?php else: ?>
                <?php foreach ($books as $book): ?>
                    <div class="book">
                        <div class="book-details">
                            <div class="book-info">
                                <img src="data:image/jpeg;base64,<?= base64_encode($book['book_img']) ?>" alt="Book Image">
                                <div>
                                    <h3><?= htmlspecialchars($book['book_name']) ?></h3>
                                    <p><?= htmlspecialchars($book['descryption']) ?></p>
                                    <p><strong>Price:</strong> <?= htmlspecialchars($book['book_price']) ?></p>
                                    <p><strong>Year:</strong> <?= htmlspecialchars($book['clg_year']) ?></p>
                                    <p><strong>Subject:</strong> <?= htmlspecialchars($book['subject']) ?></p>
                                </div>
                            </div>
                            <div>
                                <form method="post" action="cart.php">
                                    <input type="hidden" name="book_id" value="<?= $book['book_id'] ?>">
                                    <button type="submit" class="btn">Add to Cart</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>
