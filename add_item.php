<?php
session_start(); // Start session for user authentication

// Function to add a new gallery item to the JSON file
function addGalleryItem($newItem) {
    $galleryData = json_decode(file_get_contents('gallery_items.json'), true);

    // Generate a unique ID for the new item (you may use a more robust method)
    $newItem['id'] = uniqid();

    // Add the new item to the items array
    $galleryData['items'][] = $newItem;

    // Save the updated data back to the JSON file
    file_put_contents('gallery_items.json', json_encode($galleryData, JSON_PRETTY_PRINT));

    // Redirect to the gallery page after adding the item
    header("Location: gallery.php");
    exit();
}

// Check if the user is logged in and has the "admin" role
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isAdmin) {
    // Get form data
    $newItem = [
        'title' => $_POST['title'],
        'image' => $_POST['image'],
        'description' => $_POST['description'],
        'tags' => explode(',', $_POST['tags']), // Convert comma-separated tags to an array
        'price' => floatval($_POST['price']),
    ];

    // Add the new item to the JSON file
    addGalleryItem($newItem);
} else {
    // Redirect to the gallery page if the form is not submitted or user is not an admin
    header("Location: gallery.php");
    exit();
}
?>
