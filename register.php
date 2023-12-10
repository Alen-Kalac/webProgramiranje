<?php
// Function to check if a username already exists in the JSON file
function isUsernameExists($username) {
    $usersData = json_decode(file_get_contents('users.json'), true);

    foreach ($usersData['users'] as $user) {
        if ($user['username'] === $username) {
            return true;
        }
    }

    return false;
}

// Function to add a new user to the JSON file
function addUser($username, $password) {
    $usersData = json_decode(file_get_contents('users.json'), true);

    $newUser = [
        "username" => $username,
        "password" => $password,
        "role" => "user" // Assuming all registered users have a "user" role
    ];

    $usersData['users'][] = $newUser;

    file_put_contents('users.json', json_encode($usersData, JSON_PRETTY_PRINT));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // Check if the passwords match
    if ($password !== $confirmPassword) {
        echo "<script>alert('Passwords do not match.');</script>";
    } else {
        // Check if the username already exists
        if (isUsernameExists($username)) {
            echo "<script>alert('Username already exists. Please choose a different one.');</script>";
        } else {
            // Add the new user
            addUser($username, $password);

            // Redirect to the login page after successful registration
            header("Location: login.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <!-- You can link your CSS file here if you have one -->
    <script>
        // JavaScript function to validate password and confirm password
        function validatePassword() {
            var password = document.getElementById("password").value;
            var confirmPassword = document.getElementById("confirm_password").value;

            if (password !== confirmPassword) {
                alert("Passwords do not match.");
                return false;
            }

            return true;
        }
    </script>
</head>
<body>

<?php include_once 'navbar.php'; ?>

<div>
    <h1>Register</h1>
    <form method="post" action="" onsubmit="return validatePassword()">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>

        <label for="confirm_password">Confirm Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required><br>

        <input type="submit" value="Register">
    </form>
</div>

</body>
</html>
