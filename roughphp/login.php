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
        header("Location: home.php");
        exit();
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
    <title>Student Needs - Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #aee7f8;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            text-align: center;
        }

        .login-box {
            background-color: white;
            padding: 20px;
            border: 1px solid #ddd;
            width: 300px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        .login-box h2 {
            margin: 0 0 20px;
        }

        .login-box label {
            display: block;
            text-align: left;
            margin: 10px 0 5px;
            font-size: 14px;
        }

        .login-box input {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 3px;
            box-sizing: border-box;
        }

        .login-box .forgot-password-link {
            color: #0781eb;
            text-decoration: none;
            display: block;
            margin: 10px 0;
        }

        .login-box .forgot-password-link:hover {
            text-decoration: underline;
        }

        .login-box button {
            width: 100%;
            padding: 10px;
            background-color: #0073aa;
            border: 1px solid whitesmoke;
            border-radius: 3px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }

        .login-box p {
            margin-top: 20px;
        }

        .login-box p a {
            color: #0066c0;
            text-decoration: none;
        }

        .login-box p a:hover {
            text-decoration: underline;
        }

        .error {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h2>STUDENT SHELF</h2>
        <div class="login-box">
            <form method="post" action="">
                <label for="email">Email or Registration number</label>
                <input type="text" id="email" name="email" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
                <a href="/forgot-password" class="forgot-password-link">Forgot Password?</a>
                <button type="submit">Log-In</button>
            </form>
            <p>Don't have an account? <a href="createaccount.php">Register here</a></p>
            <?php if (!empty($error)): ?>
                <p class="error"><?= $error ?></p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>
