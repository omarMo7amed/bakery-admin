<?php
session_start();
include('../config/db.php');
include('../includes/functions.php');

if (!$_SESSION['admin_logged_in']) {
    header("Location: login.php");
    return;
}

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header("Location: login.php");
    exit;
}

$products = getProducts($conn);
$categories = getCategories($conn);

// Routing
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo $_SESSION['admin_logged_in'];
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            // Check if the image data is set
            $imageNames = isset($_POST['image']) ? (is_array($_POST['image']) ? $_POST['image'] : [$_POST['image']]) : [];

            // Format the image names into the correct path format
            $imagePaths = array_map(function($imageName) {
                return './assests/images/products/' . $imageName; 
            }, $imageNames);

            // Convert the array of image paths into a JSON string, without escaping the slashes
            $image = json_encode($imagePaths, JSON_UNESCAPED_SLASHES);

            addProduct($conn, $_POST['name'], $_POST['description'], $_POST['price'], $_POST['category_id'], $_POST['quantity'], $image);
        } elseif ($_POST['action'] === 'update') {
            $imageNames = isset($_POST['image']) ? (is_array($_POST['image']) ? $_POST['image'] : [$_POST['image']]) : [];
            $imagePaths = array_map(function($imageName) {
                return './assests/images/products/' . $imageName;
            }, $imageNames);
            // Convert the array of image paths into a JSON string, without escaping the slashes
            $image = json_encode($imagePaths, JSON_UNESCAPED_SLASHES);

            updateProduct($conn, $_POST['id'], $_POST['name'], $_POST['description'], $_POST['price'], $_POST['quantity'], $_POST['category_id'], $image);
        } elseif ($_POST['action'] === 'delete') {
            deleteProduct($conn, $_POST['id']);
        }
    }

    header("Location: dashboard.php");
    exit;
}

// Logout functionality
if (isset($_POST['logout'])) {
    session_destroy();
    $_SESSION['admin_logged_in'] = null;
    header( "Location: login.php");
    exit;
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/dashboard.css">
    <link rel="icon" type="image/webp" href="../assets/images/icon.webp" />
</head>
<body>
    <div class="container">
        <h1>Admin Dashboard</h1>

        <!-- Add Product Form -->
        <form method="POST">
            <h3>Add Product</h3>
            <input type="hidden" name="action" value="add">
            <input type="text" name="name" placeholder="Product Name" required>
            <textarea name="description" placeholder="Product Description" required></textarea>
            <input type="number" name="price" placeholder="Product Price" step="0.01" required>
            <input type="number" name="quantity" placeholder="Product Quantity" required>

            <!-- Image Name Input -->
            <input type="text" name="image[]" placeholder="First Image Name" required>
            <input type="text" name="image[]" placeholder="Second Image Name" required>

            <!-- Category Dropdown -->
            <select name="category_id" required>
                <option value="">Select Category</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= htmlspecialchars($category['id']) ?>"><?= htmlspecialchars($category['name']) ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Add Product</button>
        </form>

        <!-- Product Table -->
        <h3>Product List</h3>

        <div class="list">

            <table >
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Category</th>
                        <th>Image Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <form method="POST">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($product['id']) ?>">
    
                                <td><?= htmlspecialchars($product['id']) ?></td>
                                <td><input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required></td>
                                <td><textarea name="description" required><?= htmlspecialchars($product['description']) ?></textarea></td>
                                <td><input type="number" name="price" value="<?= htmlspecialchars($product['price']) ?>" step="0.01" required></td>
                                <td><input type="number" name="quantity" value="<?= htmlspecialchars($product['quantity']) ?>" required></td>
    
                                <td>
                                    <select name="category_id" required>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?= htmlspecialchars($category['id']) ?>" <?= $category['id'] == $product['category_id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($category['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
    
                                <td>
                                    <!-- Separate Textareas for each image name -->
                                    <?php
                                        // Decode the JSON image path and loop through each image
                                        $imagePaths = json_decode($product['image'], true);
                                        $imageNames = array_map('basename', $imagePaths); // Get only image names
                                    ?>
                                    <input type="text" name="image[]" value="<?= htmlspecialchars($imageNames[0]) ?>" placeholder="First Image Name" required>
                                    <input type="text" name="image[]" value="<?= htmlspecialchars($imageNames[1]) ?>" placeholder="Second Image Name" required>
                                </td>
    
                                <td>
                                    <button type="submit">Update</button>
                            </form>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($product['id']) ?>">
                                <button type="submit" style="background-color: #dc3545;">Delete</button>
                            </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        </div>


        <!-- Logout Button -->
        <div class="logout">
            <form method="POST" action="login.php">
                <button type="submit" name="logout" >Logout</button>
            </form>

            <a class="bakery-menu" href="http://localhost/Bakery/menu">&larr; Menu</a>
        </div>
    </div>
</body>
</html>
