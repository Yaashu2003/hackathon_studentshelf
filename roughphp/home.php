<?php
session_start();

$servername = "localhost"; // Change if using a remote server
$username = "root"; // Your MySQL username
$password = ""; // Your MySQL password
$dbname = "test"; // Your database name

// Create connection
$mysqli = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and bind
    $stmt = $mysqli->prepare("SELECT registration_id FROM createacc WHERE email = ? AND password = ?");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($registration_id);
        $stmt->fetch();
        // Login successful
        $_SESSION['email'] = $email;
        $_SESSION['registration_id'] = $registration_id;
    } else {
        // Login failed
        $error = "Invalid email or password";
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
    <title>Student Book Portal</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #aee7f8;
        }
        header {
            background-color: #0073aa;
            color: white;
            padding: 20px 30px;
            text-align: center;
        }
        nav ul {
            list-style: none;
            padding: 0;
            text-align: center;
        }
        nav ul li {
            display: inline;
            margin-right: 20px;
        }
        nav ul li a {
            color: black;
            text-decoration: none;
            padding: 8px 16px;
        }
        main {
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        section {
            background-color: white;
            padding: 20px;
            margin-bottom: 20px;
            width: 80%;
            text-align: center;
        }
        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 20px 0;
            width: 100%;
        }
        .login-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .login-container input[type="text"], .login-container input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .login-container input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .login-container input[type="submit"]:hover {
            background-color: #218838;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <header>
        <h1>Student Book Portal</h1>
    </header>

    <nav style="background-color:rgb(197, 243, 197); padding: 10px 0;">
        <ul>
            <li><a href="home.php">Home</a></li>
            <li><a href="student_record.php?registration_id=<?= isset($_SESSION['registration_id']) ? $_SESSION['registration_id'] : '' ?>">Student Details</a></li>
            <li><a href="buy_books.php?registration_id=<?= isset($_SESSION['registration_id']) ? $_SESSION['registration_id'] : '' ?>">Buy Books</a></li>
            <li><a href="sell_books.php?registration_id=<?= isset($_SESSION['registration_id']) ? $_SESSION['registration_id'] : '' ?>">Sell Books</a></li>
            <li><a href="interview_preparation.php?registration_id=<?= isset($_SESSION['registration_id']) ? $_SESSION['registration_id'] : '' ?>">Interview Preparation</a></li>
            <li><a href="#contact">Contact</a></li>
        </ul>
    </nav>

    <main>
        <?php if (!isset($_SESSION['email'])): ?>
            <div class="login-container">
                <h1>Login</h1>
                <form action="" method="post">
                    <input type="text" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <input type="submit" value="Login">
                </form>
                <?php if (!empty($error)): ?>
                    <p class="error"><?= $error ?></p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <section id="home">
                <h2>Welcome to the Student Book Portal</h2>
                <p>Explore our extensive range of books and make your academic journey easier!</p>
                <p>Your Registration ID: <?= $_SESSION['registration_id'] ?></p>
                <p>You have successfully logged in.</p>
                <a href="logout.php">Logout</a>
            </section>
        <?php endif; ?>

        <section id="student-details">
            <h2>Student Details</h2>
            <p>Manage your profile and academic interests here.</p>
        </section>

        <section id="buy">
            <h2>Buy Books</h2>
            <p>Find your textbooks, reference books, and much more at competitive prices.</p>
        </section>

        <section id="sell">
            <h2>Sell Your Books</h2>
            <p>Have books you no longer need? Sell them here and earn money.</p>
        </section>

        <section id="interview">
            <h2>Interview Preparation</h2>
            <p>Access resources to prepare for academic and job interviews.</p>
        </section>

        <section id="contact">
            <h2>Contact Us</h2>
            <p>Have questions? Get in touch with us via email or phone.</p>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Student Book Portal. All Rights Reserved.</p>
    </footer>
</body>
</html>
