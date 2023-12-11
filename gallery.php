<?php
session_start(); // Start session for user authentication
// Load gallery items from JSON file
$galleryData = json_decode(file_get_contents('gallery_items.json'), true);

// Check if sorting by price is requested
$sortPrice = isset($_GET['sort']) && ($_GET['sort'] === 'price_asc' || $_GET['sort'] === 'price_desc');

// Sort items by price if requested
if ($sortPrice) {
    $sortOrder = ($_GET['sort'] === 'price_asc') ? 'asc' : 'desc';

    usort($galleryData['items'], function ($a, $b) use ($sortOrder) {
        if ($sortOrder === 'asc') {
            return $a['price'] - $b['price'];
        } else {
            return $b['price'] - $a['price'];
        }
    });
}

// Collect unique tags from gallery items
$allTags = array();
foreach ($galleryData['items'] as $item) {
    $allTags = array_merge($allTags, $item['tags']);
}
$uniqueTags = array_unique($allTags);
sort($uniqueTags); // Sort tags alphabetically

// Check if a specific tag is requested for filtering
$tagFilter = isset($_GET['tag']) ? urldecode($_GET['tag']) : null;

// Check if the user is an admin
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <?php include_once 'navbar.php'; ?>

    <?php
    // Display the form for adding a new gallery item only if the user is an admin
    if ($isAdmin) {
        echo '<div class="addNewItemForm">';
        echo '<h2>Add New Gallery Item</h2>';
        echo '<form  method="post" action="add_item.php">';
        echo '<div class="inputField"><label for="title">Title:</label>';
        echo '<input type="text" id="title" name="title" required></div>';

        echo '<div class="inputField"><label for="image">Image URL:</label>';
        echo '<input type="text" id="image" name="image" required></div>';

        echo '<div class="inputField"><label for="description">Description:</label>';
        echo '<textarea id="description" name="description" required></textarea></div>';

        echo '<div class="inputField"><label for="tags">Tags (comma-separated):</label>';
        echo '<input type="text" id="tags" name="tags" required></div>';

        echo '<div class="inputField"><label for="price">Price:</label>';
        echo '<input type="number" id="price" name="price" step="0.01" required></div>';

        echo '<input class="submitButton" type="submit" value="Add Item">';
        echo '</form>';
        echo '</div>';
    }
    ?>

    <div class="galleryPage">
        <div class="filters">
            <div class="filter">
                <p>Sort by price:</p>
                <a href="?sort=price_asc">Price (Low to High)</a>
                <a href="?sort=price_desc">Price (High to Low)</a>
            </div>

            <?php
            // Display filter for sorting by tags
            echo '<div class="filter">';
            echo '<p>Sort by tag:</p>';
            foreach ($uniqueTags as $tag) {
                $tagUrl = '?tag=' . urlencode($tag);
                $isActive = ($tagFilter === $tag) ? 'active' : '';
                echo '<a href="' . $tagUrl . '" class="' . $isActive . '">' . $tag . '</a>';
            }
            echo '</div>';

            // Display filter to remove all filters
            echo '<div class="filter">';
            echo '<p>Remove filters:</p>';
            echo '<a href="?">Remove All Filters</a>';
            echo '</div>';
            ?>
        </div>
        <div class="galleryItemGrid">
            <?php
            // Display filtered gallery items based on tag
            foreach ($galleryData['items'] as $item) {
                // Check if tag filter is active
                if (!$tagFilter || in_array($tagFilter, $item['tags'])) {
                    echo '<div class="galleryItem">';
                    echo '<h3>' . $item['title'] . '</h3>';
                    echo '<img src="' . $item['image'] . '" alt="' . $item['title'] . '">';
                    echo '<p class="description">' . $item['description'] . '</p>';
                    echo '<p class="tags">Tags: ' . implode(', ', $item['tags']) . '</p>';
                    echo '<p class="price">Price: $' . $item['price'] . '</p>';
                    echo '</div>';
                }
            }
            ?>
        </div>
    </div>

</body>

</html>
