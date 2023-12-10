
<?php  
session_start(); // Start session for user authentication
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery</title>
    <!-- You can link your CSS file here if you have one -->
</head>
<body>
    <style>
        .galleryItem{
            border: 1px solid black;
            margin: 10px;
        }
        img{
            width: 200px;
            height: 200px;
        }
    </style>

<?php include_once 'navbar.php'; ?>

<div>
    <h1>Gallery</h1>

    <?php
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

    // Display gallery items
    foreach ($galleryData['items'] as $item) {
        echo '<div class="galleryItem" >';
        echo '<img src="' . $item['image'] . '" alt="' . $item['title'] . '">';
        echo '<h3>' . $item['title'] . '</h3>';
        echo '<p>' . $item['description'] . '</p>';
        echo '<p>Price: $' . $item['price'] . '</p>';
        echo '<p>Tags: ' . implode(', ', $item['tags']) . '</p>';
        echo '</div>';
    }
    ?>

    <p>Sort by:
        <a href="?sort=price_asc">Price (Low to High)</a> |
        <a href="?sort=price_desc">Price (High to Low)</a>
    </p>
</div>

</body>
</html>

</body>
</html>
