<?php
session_start();

// Function to get user data by ID
function getUserById($userId) {
    $usersData = json_decode(file_get_contents('users.json'), true);

    foreach ($usersData['users'] as $user) {
        if ($user['id'] === $userId) {
            return $user;
        }
    }

    return null; // User not found
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Get user data based on the user ID in the session
$userData = getUserById($_SESSION['user_id']);

if (!$userData) {
    echo "User not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="styleProfile.css">
</head>
<body>

<?php include_once 'navbar.php'; ?>

<div class="profilePage">
    <h1>User Profile</h1>
    <div class="profileInfo">
        <p><strong>User ID:</strong> <?php echo $userData['id']; ?></p>
        <p><strong>Username:</strong> <?php echo $userData['username']; ?></p>
        <p><strong>Email:</strong> <?php echo $userData['email']; ?></p>
        <p><strong>Name:</strong> <?php echo $userData['name']; ?></p>
        <p><strong>Last Name:</strong> <?php echo $userData['lastName']; ?></p>
        <p><strong>Birth Date:</strong> <?php echo $userData['birthDate']; ?></p>
        <p><strong>Profile Picture:</strong> <img src="<?php echo $userData['imagePath']; ?>" alt="Profile Picture"></p>
    </div>
</div>

</body>
</html>
