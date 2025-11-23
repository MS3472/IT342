<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Registration</title>
</head>
<body>
    <div class="register-container">
        <h1>Customer Registration</h1>
        
        <!-- Error message display -->
        <div id="error-message" class="error" style="display: none;"></div>
        
        <!-- Success message display -->
        <div id="success-message" class="success" style="display: none;"></div>
        
        <!-- Registration form -->
        <form id="register-form">
            <div class="form-group">
                <label for="name">Full Name:</label>
                <input type="text" id="name" name="name" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required minlength="6">
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
            </div>
            
            <button type="submit">Register</button>
        </form>
        
        <p>Already have an account? <a href="/public/login.php">Login here</a></p>
    </div>
    
    <script src="/example_register.js"></script>
</body>
</html>
