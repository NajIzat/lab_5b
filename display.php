<?php
// Display Page (display.php)

// Start the session
session_start();

// Check if the user is logged in (optional, if login system is implemented)
if (!isset($_SESSION['matric'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit();
}

// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'lab_5b'); // Replace credentials if needed

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Update and Delete Requests
if (isset($_POST['update'])) {
    // Update user data
    $matric = $_POST['matric'];
    $name = $_POST['name'];
    $role = $_POST['role'];

    // SQL query to update user
    $sql = "UPDATE users SET matric = ?, name = ?, role = ? WHERE matric = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $matric, $name, $role, $matric); // Use 'ssss' to bind four strings
    $stmt->execute();
    $stmt->close();
    header("Location: display.php"); // Refresh to reflect changes
    exit();
} elseif (isset($_GET['delete'])) {
    // Delete user data
    $matric = $_GET['delete'];

    // SQL query to delete user
    $sql = "DELETE FROM users WHERE matric = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $matric);
    $stmt->execute();
    $stmt->close();
    header("Location: display.php"); // Refresh to reflect changes
    exit();
}

// Fetch data from the users table
$sql = "SELECT matric, name, role FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users List</title>
    <style>
        table {
            width: 50%;
            margin: auto;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        h1 {
            text-align: center;
        }
        .logout-btn {
            display: block;
            text-align: center;
            margin: 20px auto;
            padding: 10px;
            background-color: red;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h1>Users List</h1>
    
    <!-- Logout Button -->
    <a href="logout.php" class="logout-btn">Logout</a>

    <table>
        <thead>
            <tr>
                <th>Matric</th>
                <th>Name</th>
                <th>Access Level</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['matric']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['role']) . "</td>";
                    echo "<td>
                        <a href='?edit=" . $row['matric'] . "'>Edit</a> | 
                        <a href='?delete=" . $row['matric'] . "' onclick='return confirm(\"Are you sure you want to delete?\")'>Delete</a>
                    </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No records found</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <?php
    // Handle edit
    if (isset($_GET['edit'])) {
        $matric = $_GET['edit'];
        $sql = "SELECT matric, name, role FROM users WHERE matric = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $matric);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($matric, $name, $role);
        $stmt->fetch();
        $stmt->close();
    ?>
        <h2>Edit User</h2>
        <form method="POST" action="display.php">
            <label for="matric">Matric:</label>
            <input type="text" name="matric" value="<?php echo htmlspecialchars($matric); ?>" required readonly><br><br> <!-- Matric is readonly -->

            <label for="name">Name:</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required><br><br>

            <label for="role">Role:</label>
            <select name="role">
                <option value="lecturer" <?php echo ($role == 'lecturer') ? 'selected' : ''; ?>>Lecturer</option>
                <option value="student" <?php echo ($role == 'student') ? 'selected' : ''; ?>>Student</option>
            </select><br><br>

            <button type="submit" name="update">Update</button>
        </form>
    <?php
    }
    ?>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
