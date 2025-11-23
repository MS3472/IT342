<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: /admin/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
</head>
<body>
    <div class="dashboard-container">
        <header>
            <h1>Admin Dashboard</h1>
            <nav>
                <a href="/admin/dashboard.php">Dashboard</a>
                <a href="/admin/products.php">Products</a>
                <a href="/admin/categories.php">Categories</a>
                <a href="/admin/logout.php">Logout</a>
            </nav>
        </header>
        
        <main>
            <h2>Welcome to the Admin Portal</h2>
            
            <div class="dashboard-cards">
                <div class="card">
                    <h3>Products</h3>
                    <p>Manage your product catalog</p>
                    <a href="/admin/products.php">View Products</a>
                </div>
                
                <div class="card">
                    <h3>Categories</h3>
                    <p>Organize your product categories</p>
                    <a href="/admin/categories.php">View Categories</a>
                </div>
            </div>
        </main>
    </div>
</body>
</html>