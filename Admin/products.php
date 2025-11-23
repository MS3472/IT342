<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: /admin/login.php');
    exit;
}

// Hard-coded fake products
$products = [
    ['id' => 1, 'name' => 'Laptop', 'category' => 'Electronics', 'price' => 999.99, 'stock' => 15],
    ['id' => 2, 'name' => 'Wireless Mouse', 'category' => 'Electronics', 'price' => 29.99, 'stock' => 50],
    ['id' => 3, 'name' => 'Office Chair', 'category' => 'Furniture', 'price' => 249.99, 'stock' => 8],
    ['id' => 4, 'name' => 'Desk Lamp', 'category' => 'Furniture', 'price' => 39.99, 'stock' => 25],
    ['id' => 5, 'name' => 'Notebook', 'category' => 'Stationery', 'price' => 4.99, 'stock' => 100],
    ['id' => 6, 'name' => 'Pen Set', 'category' => 'Stationery', 'price' => 12.99, 'stock' => 75],
    ['id' => 7, 'name' => 'Monitor', 'category' => 'Electronics', 'price' => 299.99, 'stock' => 12],
    ['id' => 8, 'name' => 'Keyboard', 'category' => 'Electronics', 'price' => 79.99, 'stock' => 30],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Admin</title>
</head>
<body>
    <div class="admin-container">
        <header>
            <h1>Product Management</h1>
            <nav>
                <a href="/admin/dashboard.php">Dashboard</a>
                <a href="/admin/products.php">Products</a>
                <a href="/admin/categories.php">Categories</a>
                <a href="/admin/logout.php">Logout</a>
            </nav>
        </header>
        
        <main>
            <h2>Products</h2>
            
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($product['id']); ?></td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td><?php echo htmlspecialchars($product['category']); ?></td>
                            <td>$<?php echo number_format($product['price'], 2); ?></td>
                            <td><?php echo htmlspecialchars($product['stock']); ?></td>
                            <td>
                                <a href="#">Edit</a> | 
                                <a href="#">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </main>
    </div>
</body>
</html>