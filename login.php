<?php
session_start();

// Check if the user is already logged in, redirect to dashboard
if (isset($_SESSION['matric'])) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Connect to the database
    $conn = new mysqli("localhost", "root", "", "lab_5b");

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get input values
    $matric = $_POST['matric'];
    $password = $_POST['password'];

    // Prepare and bind
    $stmt = $conn->prepare("SELECT * FROM users WHERE matric = ?");
    $stmt->bind_param("s", $matric);  // 's' indicates a string
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the result has any rows
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Direct password comparison if the password is stored as plain text
        if ($password === $user['password']) {
            // Password is correct, set session and redirect
            $_SESSION['matric'] = $matric;
            header("Location: dashboard.php"); // Redirect to the dashboard page
            exit();
        } else {
            // Authentication failed
            $error_message = "Invalid matric or password!";
        }
    } else {
        // No user found with the given matric
        $error_message = "Invalid matric or password!";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>

        <!-- Error message if authentication fails -->
        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <label for="matric">Matric:</label>
            <input type="text" name="matric" id="matric" required><br><br>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required><br><br>

            <button type="submit">Login</button>
        </form>

        <!-- Link to the registration page -->
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</body>
</html>
