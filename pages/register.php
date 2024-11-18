<?php
session_start();
require '../includes/config.php'; 


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    // Validation
    if (empty($username) || empty($password)) {
        $error = "Both username and password are required.";
    } else {
        try {
            // Check if the username already exists
            $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = :username");
            $stmt->execute(['username' => $username]);
            if ($stmt->rowCount() > 0) {
                $error = "Username already exists. Choose a different username.";
            } else {
                // Hash the password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $pdo->prepare("INSERT INTO admin (username, password) VALUES (:username, :password)");
                $stmt->execute(['username' => $username, 'password' => $hashedPassword]);

                header("Location: ../login.php?message=Registration successful. Please log in.");
                exit;
            }
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Registration</title>
    <link rel="stylesheet" href="../stylesheets/register.css"> 
</head>
<body>
<div class="container">
    <h2>Register New Admin</h2>

    <?php
    if (isset($error)) {
        echo "<p style='color:red;'>$error</p>";
    }
    ?>

    <form action="register.php" method="POST">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>

        <button type="submit">Register</button>
    </form>

    <p>Already have an account? <a href="../login.php">Log in here</a></p>
    </div>
</body>
</html>
