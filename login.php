<?php
session_start(); // Start session for user authentication

// Function to check if a user exists in the JSON file
function isUserExists($username, $password) {
    $usersData = json_decode(file_get_contents('users.json'), true);

    foreach ($usersData['users'] as $user) {
        if ($user['username'] === $username && $user['password'] === $password) {
            return $user; // Return the entire user data
        }
    }

    return null; // User not found
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $user = isUserExists($username, $password);

    if ($user) {
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $user['role']; // Store the user's role in the session

        // Redirect to the main page after successful login
        header("Location: index.php");
        exit();
    } else {
        echo "Invalid username or password. Would you like to <a href='register.php'>register</a>?";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- You can link your CSS file here if you have one -->
</head>
<body>

<?php include_once 'navbar.php'; ?>

<div>
    <h1>Login</h1>
    <form method="post" action="">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>

        <input type="submit" value="Login">
    </form>
</div>

</body>
</html>