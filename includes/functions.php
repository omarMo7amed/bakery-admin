<?php
// Get products from the database
function getProducts($conn) {
    $sql = "SELECT * FROM products";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Get categories from the database
function getCategories($conn) {
    $sql = "SELECT * FROM categories";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// يارب الخلاص عشان خلاص 
function addProduct($conn, $name, $description, $price, $category_id, $quantity, $image) {
    // Get the category name based on category_id
    $stmt = $conn->prepare("SELECT name FROM categories WHERE id = ?");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $category = $result->fetch_assoc();
    $category_name = $category['name']; // Get the category name
    
    // Insert product with category name
    $stmt = $conn->prepare("INSERT INTO products (name, description, price, category_id, category_name, quantity, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdisss", $name, $description, $price, $category_id, $category_name, $quantity, $image);

    try {
        $stmt->execute();
    } catch (mysqli_sql_exception $e) {
        echo "Error: " . $e->getMessage();
        exit;
    }
}

// خامس محاولة 
// Update an existing product
// function updateProduct($conn, $id, $name, $description, $price, $quantity, $category_id, $image) {
//     $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, quantity = ?, category_id = ?, image = ? WHERE id = ?");
//     $stmt->bind_param("ssdiisi", $name, $description, $price, $quantity, $category_id, $image, $id);
//     $stmt->execute();
// }

function updateProduct($conn, $id, $name, $description, $price, $quantity, $category_id, $image) {
    // Get the category name based on category_id
    $stmt = $conn->prepare("SELECT name FROM categories WHERE id = ?");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $category = $result->fetch_assoc();
    $category_name = $category['name']; // Get the category name

    // Update product with category name
    $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, category_id = ?, category_name = ?, quantity = ?, image = ? WHERE id = ?");
    $stmt->bind_param("ssdisssi", $name, $description, $price, $category_id, $category_name, $quantity, $image, $id);

    try {
        $stmt->execute();
    } catch (mysqli_sql_exception $e) {
        echo "Error: " . $e->getMessage();
        exit;
    }
}


// Delete a product
function deleteProduct($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}
?>
