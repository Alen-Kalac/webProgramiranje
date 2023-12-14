<?php
session_start(); // Start session for user authentication

// Function to check if the user is logged in
function isLoggedIn()
{
    return isset($_SESSION['username']);
}

// Function to get the username of the current user
function getCurrentUsername()
{
    return isset($_SESSION['username']) ? $_SESSION['username'] : null;
}

// Function to load blog posts from JSON file
function loadBlogPosts()
{
    $blogData = json_decode(file_get_contents('blog_posts.json'), true);
    return $blogData['posts'];
}

// Function to add a new blog post
function addBlogPost($title, $description, $imageFile, $username)
{
    $blogData = json_decode(file_get_contents('blog_posts.json'), true);

    // Generate a unique ID for the new post
    $newPostID = uniqid();

    // Save image to the 'blogImages' folder with postID as the filename
    $imageExtension = pathinfo($imageFile['name'], PATHINFO_EXTENSION);
    $imageFileName = "blogImages/{$newPostID}.{$imageExtension}";
    move_uploaded_file($imageFile['tmp_name'], $imageFileName);

    $newPost = [
        "id" => $newPostID,
        "title" => $title,
        "description" => $description,
        "img" => $imageFileName,
        "username" => $username,
        "comments" => [],
        "likes" => []
    ];

    $blogData['posts'][] = $newPost;

    file_put_contents('blog_posts.json', json_encode($blogData, JSON_PRETTY_PRINT));
}

// Check if the form for adding a new blog post is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_post' && isLoggedIn()) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $img = $_FILES['img'];
    $username = getCurrentUsername();

    addBlogPost($title, $description, $img, $username);

    // Redirect to the same page to avoid form resubmission on page refresh
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
// Function to get a subset of blog posts based on page and limit
function getPaginatedPosts($page, $limit)
{
    $allPosts = loadBlogPosts();

    $startIndex = ($page - 1) * $limit;
    $endIndex = $startIndex + $limit;

    return array_slice($allPosts, $startIndex, $limit);
}
function addComment($postID, $username, $commentText)
{
    $blogData = json_decode(file_get_contents('blog_posts.json'), true);

    // Find the post by ID
    $postIndex = array_search($postID, array_column($blogData['posts'], 'id'));

    if ($postIndex !== false) {
        // Generate a unique ID for the new comment
        $newCommentID = count($blogData['posts'][$postIndex]['comments']) + 1;

        // Create a new comment
        $newComment = [
            "commentID" => $newCommentID,
            "username" => $username,
            "commentText" => $commentText
        ];

        // Add the new comment to the post
        $blogData['posts'][$postIndex]['comments'][] = $newComment;

        // Update the JSON file
        file_put_contents('blog_posts.json', json_encode($blogData, JSON_PRETTY_PRINT));
    }
}
// Check if the form for adding a new comment is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'comment' && isLoggedIn()) {
    $postID = $_POST['post_id'];
    $username = getCurrentUsername();
    $commentText = $_POST['new_comment_text'];

    // Check if the comment input is not empty
    if (empty($commentText)) {
        echo '<script>alert("Cannot enter an empty comment");</script>';
    } else {
        addComment($postID, $username, $commentText);

        // Redirect to the same page to avoid form resubmission on page refresh
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}


// Function to check if the current user has liked a post
function hasLiked($postID, $username)
{
    $blogData = json_decode(file_get_contents('blog_posts.json'), true);

    $postIndex = array_search($postID, array_column($blogData['posts'], 'id'));

    if ($postIndex !== false) {
        $likes = $blogData['posts'][$postIndex]['likes'];
        foreach ($likes as $like) {
            if ($like['username'] === $username) {
                return true; // User has already liked the post
            }
        }
    }

    return false; // User has not liked the post
}

// Function to add a like to a post
function addLike($postID, $username)
{
    $blogData = json_decode(file_get_contents('blog_posts.json'), true);

    $postIndex = array_search($postID, array_column($blogData['posts'], 'id'));

    if ($postIndex !== false) {
        // Check if the user has already liked the post
        if (!hasLiked($postID, $username)) {
            // Add a new like
            $newLike = ["username" => $username];
            $blogData['posts'][$postIndex]['likes'][] = $newLike;

            // Update the JSON file
            file_put_contents('blog_posts.json', json_encode($blogData, JSON_PRETTY_PRINT));
        }
    }
}

// Check if the form for adding a like or dislike is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && (($_POST['action'] === 'like') || ($_POST['action'] === 'dislike')) && isLoggedIn()) {
    $postID = $_POST['post_id'];
    $username = getCurrentUsername();

    // Check if the user has already liked the post
    $hasLiked = hasLiked($postID, $username);

    // If the user has already liked the post and clicked "Like" again, remove the like (dislike)
    if ($hasLiked && $_POST['action'] === 'like') {
        removeLike($postID, $username);
    } else {
        // Otherwise, add or remove the like based on the action
        if ($_POST['action'] === 'like') {
            addLike($postID, $username);
        } elseif ($_POST['action'] === 'dislike') {
            removeLike($postID, $username);
        }
    }

    // Redirect to the same page to avoid form resubmission on page refresh
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Function to remove a like from a post
function removeLike($postID, $username)
{
    $blogData = json_decode(file_get_contents('blog_posts.json'), true);

    $postIndex = array_search($postID, array_column($blogData['posts'], 'id'));

    if ($postIndex !== false) {
        // Find and remove the like by the current user
        $likes = &$blogData['posts'][$postIndex]['likes'];
        foreach ($likes as $key => $like) {
            if ($like['username'] === $username) {
                unset($likes[$key]);
                // Update the JSON file
                file_put_contents('blog_posts.json', json_encode($blogData, JSON_PRETTY_PRINT));
                break;
            }
        }
    }
}
// Function to delete a blog post by ID
function deleteBlogPost($postID)
{
    $blogData = json_decode(file_get_contents('blog_posts.json'), true);

    // Check if the user is an admin
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        // Find the post by ID
        $postIndex = array_search($postID, array_column($blogData['posts'], 'id'));

        if ($postIndex !== false) {
            // Remove the post from the array
            array_splice($blogData['posts'], $postIndex, 1);

            // Update the JSON file
            file_put_contents('blog_posts.json', json_encode($blogData, JSON_PRETTY_PRINT));
        }
    }
}

// Check if the form for deleting a blog post is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_post') {
    $postID = $_POST['post_id'];

    // Call the deleteBlogPost function
    deleteBlogPost($postID);

    // Redirect to the same page to avoid form resubmission on page refresh
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog</title>

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="styleBlog.css">
</head>

<body>
    <?php include_once 'navbar.php'; ?>
    <div class="blogPage">


        <?php
        // Check if the user is logged in
        if (isLoggedIn()) {
            echo '<div class="newPost">';
            echo '<h2>New Blog Post</h2>';
            echo '<form method="post" action="" enctype="multipart/form-data">';

            echo '<div class="inputField">';
            echo '<label for="title">Title:</label>';
            echo '<input type="text" id="title" name="title" required>';
            echo '</div>';
            echo '<div class="inputField">';
            echo '<label for="description">Description:</label>';
            echo '<textarea id="description" name="description" required></textarea>';
            echo '</div>';
            echo '<div class="inputField">';
            echo '<label for="img">Image:</label>';
            echo '<input type="file" name="img" accept="image/*" required>';
            echo '</div>';

            echo '<input type="hidden" name="action" value="add_post">';
            echo '<input class="newPostButton" type="submit" value="Create Post" name="submit">';
            echo '</form>';
            echo '</div>';
        }

        // Load and display blog posts
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $limit = 7;
        $paginatedPosts = getPaginatedPosts($page, $limit);

        foreach ($paginatedPosts as $post) {
            echo '<div class="blogPost">';
            // Delete button
            if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
                echo '<form method="post" action="">';
                echo '<input type="hidden" name="post_id" value="' . $post['id'] . '">';
                echo '<button class="deleteButton" type="submit" name="action" value="delete_post">Delete</button>';
                echo '</form>';
            }

            // Top section
            echo '<div class="top">';
            // Left section (title, description, likes)
            echo '<div class="left">';
            echo '<h2>' . $post['title'] . '</h2>';
            echo '<p class="description">' . $post['description'] . '</p>';
            // Display likes
            echo '<div class="likes">';
            $likesCount = count($post['likes']);
            echo '<p>Likes: ' . $likesCount . '</p>';

            // Check if the user has already liked the post
            $username = getCurrentUsername();
            $hasLiked = hasLiked($post['id'], $username);

            
            // Like button and its logic
            echo '<form method="post" action="">';
            echo '<input type="hidden" name="post_id" value="' . $post['id'] . '">';
            if ($hasLiked) {
                echo '<button type="submit" name="action" value="dislike">Dislike</button>';
            } else {
                echo '<button type="submit" name="action" value="like">Like</button>';
            }
            echo '</form>';
            echo '</div>';
            echo '</div>'; // Close the "left" div
  
            // Image container
            echo '<div class="img-container">';
            echo '<img src="' . $post['img'] . '" alt="' . $post['title'] . '">';
            echo '</div>'; // Close the "img-container" div
        
            echo '</div>'; // Close the "top" div
        
            // Bottom section (comments and delete button)
            echo '<div class="bottom">';
            echo '<h3>Comments</h3>';

            // Display a message if there are no comments
            if (empty($post['comments'])) {
                echo '<p>Be the first to comment!</p>';
            } else {
                // Display comments
                echo '<div class="comments">';
                foreach ($post['comments'] as $comment) {
                    echo '<div class="comment">' . '<p class="username">'. $comment['username'] . "</p>"  . "<p>" . $comment['commentText'] ."</p>" . '</div>';
                }
                echo '</div>';
            }

            // Input field for adding new comments
            echo '<form class="addComment" method="post" action="">';
            echo '<input type="hidden" name="post_id" value="' . $post['id'] . '">';
            echo '<textarea type="text" id="newComment" name="new_comment_text">'. '</textarea>';
            echo '<button type="submit" name="action" value="comment">Add Comment</button>';
            echo '</form>';

            

            echo '</div>'; // Close the "bottom" div
            echo '<p class="postedBy">Posted by: ' . $post['username'] . '</p>';
            
            echo '</div>'; // Close the "blogPost" div
        }
        ?>

    </div>

</body>

</html>