<link rel="stylesheet" href="style.css">
<nav>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="gallery.php">Gallery</a></li>
        <li><a href="blog.php">Blog</a></li>

        <?php
        // Check if the user is logged in
        if (isset($_SESSION['username'])) {
            echo '<li><a href="logout.php" onclick="logout()">Log Out</a></li>';
            echo '<li><a>Welcome ' . $_SESSION['username'] . ' ! </a></li>';
        } else {
            // If not logged in, show login and register links
            echo '<li><a href="login.php">Login</a></li>';
            echo '<li><a href="register.php">Register</a></li>';
        }
        ?>
    </ul>
</nav>

<script>
function logout() {
    document.getElementById('logoutForm').submit();
}
</script>
