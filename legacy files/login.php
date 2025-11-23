<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Login</title>
</head>
<body>
    <div class="login-container">
        <h1>Customer Login</h1>
        
        <!-- Error message display -->
        <div id="error-message" class="error" style="display: none;"></div>
        
        <!-- Success message display -->
        <div id="success-message" class="success" style="display: none;"></div>
        
        <!-- Login form -->
        <form id="login-form">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit">Login</button>
        </form>
        
        <p>Don't have an account? <a href="/public/register.php">Register here</a></p>
    </div>
    
    <script src="/example_login.js"></script>
</body>
</html>
