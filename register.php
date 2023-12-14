<?php
// Function to check if a username already exists in the JSON file
function isUsernameExists($username)
{
    $usersData = json_decode(file_get_contents('users.json'), true);

    foreach ($usersData['users'] as $user) {
        if ($user['username'] === $username) {
            return true;
        }
    }

    return false;
}

// Function to add a new user to the JSON file
function addUser($username, $email, $password, $name, $lastName, $birthDate, $image)
{
    $usersData = json_decode(file_get_contents('users.json'), true);

    // Generate a unique ID for the new user
    $uniqueId = uniqid();

    // Extract file extension from the image name
    $imageExtension = pathinfo($image['name'], PATHINFO_EXTENSION);

    // Create a unique filename based on the user's ID and file extension
    $imagePath = 'userPFP/' . $uniqueId . '.' . $imageExtension;

    $newUser = [
        "id" => $uniqueId,
        "username" => $username,
        "email" => $email,
        "password" => $password,
        "name" => $name,
        "lastName" => $lastName,
        "birthDate" => $birthDate,
        "imagePath" => $imagePath,
        "role" => "user" // Assuming all registered users have a "user" role
    ];

    $usersData['users'][] = $newUser;

    // Move the uploaded image to the userPFP folder with the unique filename
    move_uploaded_file($image['tmp_name'], $imagePath);

    file_put_contents('users.json', json_encode($usersData, JSON_PRETTY_PRINT));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $name = $_POST['name'];
    $lastName = $_POST['last_name'];
    $birthDate = $_POST['birth_date'];
    $image = $_FILES['user_image'];

    // Check if the passwords match
    if ($password !== $confirmPassword) {
        echo "<script>alert('Passwords do not match.');</script>";
    } else {
        // Check if the username already exists
        if (isUsernameExists($username)) {
            echo "<script>alert('Username already exists. Please choose a different one.');</script>";
        } else {
            // Check if the password meets requirements
            if (!preg_match('/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
                echo "<script>alert('Password does not meet requirements. It must contain at least one letter, one number, and one symbol.');</script>";
            } else {
                // Add the new user
                addUser($username, $email, $password, $name, $lastName, $birthDate, $image);

                // Redirect to the login page after successful registration
                header("Location: login.php");
                exit();
            }
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
    <link rel="stylesheet" href="style.css">
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
        <div class="registerForm">
            <form method="post" action="" onsubmit="return validatePassword()" enctype="multipart/form-data">
                <div class="inputField">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>

                <div class="inputField">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="inputField">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                    <small>Password must contain at least one letter, one number, and one symbol, and be at least 8
                        characters long.</small>
                </div>

                <div class="inputField">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>

                <div class="inputField">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>

                <div class="inputField">
                    <label for="last_name">Last Name:</label>
                    <input type="text" id="last_name" name="last_name" required>
                </div>

                <div class="inputField">
                    <label for="birth_date">Birth Date:</label>
                    <input type="date" id="birth_date" name="birth_date" required>
                </div>

                <div class="inputField">
                    <label for="user_image">Profile Picture:</label>
                    <input type="file" id="user_image" name="user_image" accept="image/*">
                </div>

                <input type="submit" value="Register">
            </form>
        </div>
    </div>

</body>

</html>