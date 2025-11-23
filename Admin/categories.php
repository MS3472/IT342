<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: /admin/login.php');
    exit;
}

// Hard-coded fake categories
$categories = [
    ['id' => 1, 'name' => 'Electronics', 'description' => 'Electronic devices and accessories', 'product_count' => 4],
    ['id' => 2, 'name' => 'Furniture', 'description' => 'Office and home furniture', 'product_count' => 2],
    ['id' => 3, 'name' => 'Stationery', 'description' => 'Office supplies and writing materials', 'product_count' => 2],
    ['id' => 4, 'name' => 'Books', 'description' => 'Educational and reference books', 'product_count' => 0],
    ['id' => 5, 'name' => 'Accessories', 'description' => 'Various accessories and add-ons', 'product_count' => 0],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categories - Admin</title>
</head>
<body>
    <div class="admin-container">
        <header>
            <h1>Category Management</h1>
            <nav>
                <a href="/admin/dashboard.php">Dashboard</a>
                <a href="/admin/products.php">Products</a>
                <a href="/admin/categories.php">Categories</a>
                <a href="/admin/logout.php">Logout</a>
            </nav>
        </header>
        
        <main>
            <h2>Categories</h2>
            
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Category Name</th>
                        <th>Description</th>
                        <th>Product Count</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($category['id']); ?></td>
                            <td><?php echo htmlspecialchars($category['name']); ?></td>
                            <td><?php echo htmlspecialchars($category['description']); ?></td>
                            <td><?php echo htmlspecialchars($category['product_count']); ?></td>
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