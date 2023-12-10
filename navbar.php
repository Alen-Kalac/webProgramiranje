
<nav>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="gallery.php">Gallery</a></li>
        <li><a href="blog.php">Blog</a></li>

        <?php
        // Check if the user is logged in
        if (isset($_SESSION['username'])) {
         
            echo '<li>';
            echo '<form id="logoutForm" method="post" action="logout.php" style="display:inline;">';
            echo '<button type="button" onclick="logout()" style="background: none; border: none; color: blue; cursor: pointer;">Log Out</button>';
            echo '</form>';
            echo '</li>';
            echo '<li>Logged in as: ' . $_SESSION['username'] . '</li>';
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
