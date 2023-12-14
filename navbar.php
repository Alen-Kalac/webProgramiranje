
<nav>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="gallery.php">Gallery</a></li>
        <li><a href="blog.php">Blog</a></li>

        <?php
        // Check if the user is logged in
        if (isset($_SESSION['username'])) {
            echo '<li><a href="logout.php" onclick="logout()">Log Out</a></li>';
            echo '<li><a href="profile.php">My Profile</a></li>';
        } else {
            // If not logged in, show login link
            echo '<li><a href="login.php">Login</a></li>';
        }
        ?>
    </ul>
</nav>

<script>
function logout() {
    document.getElementById('logoutForm').submit();
}
</script>
