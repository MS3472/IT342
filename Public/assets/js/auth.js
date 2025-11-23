/**
 * Authentication JavaScript
 * Handles login and registration form submissions
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // Handle Login Form
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent default form submission
            
            const errorDiv = document.getElementById('loginError');
            if (errorDiv) {
                errorDiv.textContent = '';
                errorDiv.style.display = 'none';
            }
            
            // Get form values
            const email = loginForm.querySelector('input[name="email"]').value;
            const password = loginForm.querySelector('input[name="password"]').value;
            
            // Create FormData
            const formData = new FormData();
            formData.append('email', email);
            formData.append('password', password);
            
            // Disable submit button
            const submitButton = loginForm.querySelector('button[type="submit"]');
            const originalText = submitButton.textContent;
            submitButton.disabled = true;
            submitButton.textContent = 'Logging in...';
            
            // Send POST request
            fetch('/auth_login.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Re-enable submit button
                submitButton.disabled = false;
                submitButton.textContent = originalText;
                
                if (data.success) {
                    // Redirect to account page on success
                    window.location.href = '/Public/account.php';
                } else {
                    // Display error message
                    if (errorDiv) {
                        errorDiv.textContent = data.message || 'Login failed. Please try again.';
                        errorDiv.style.display = 'block';
                    }
                }
            })
            .catch(error => {
                // Re-enable submit button
                submitButton.disabled = false;
                submitButton.textContent = originalText;
                
                // Display error message
                if (errorDiv) {
                    errorDiv.textContent = 'An error occurred. Please try again.';
                    errorDiv.style.display = 'block';
                }
                console.error('Login error:', error);
            });
        });
    }
    
    // Handle Register Form
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent default form submission
            
            const errorDiv = document.getElementById('registerError');
            if (errorDiv) {
                errorDiv.textContent = '';
                errorDiv.style.display = 'none';
            }
            
            // Get form values
            const name = registerForm.querySelector('input[name="name"]').value;
            const email = registerForm.querySelector('input[name="email"]').value;
            const password = registerForm.querySelector('input[name="password"]').value;
            
            // Create FormData
            const formData = new FormData();
            formData.append('name', name);
            formData.append('email', email);
            formData.append('password', password);
            
            // Disable submit button
            const submitButton = registerForm.querySelector('button[type="submit"]');
            const originalText = submitButton.textContent;
            submitButton.disabled = true;
            submitButton.textContent = 'Registering...';
            
            // Send POST request
            fetch('/auth_register.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Re-enable submit button
                submitButton.disabled = false;
                submitButton.textContent = originalText;
                
                if (data.success) {
                    // Redirect to account page on success
                    window.location.href = '/Public/account.php';
                } else {
                    // Display error message
                    if (errorDiv) {
                        errorDiv.textContent = data.message || 'Registration failed. Please try again.';
                        errorDiv.style.display = 'block';
                    }
                }
            })
            .catch(error => {
                // Re-enable submit button
                submitButton.disabled = false;
                submitButton.textContent = originalText;
                
                // Display error message
                if (errorDiv) {
                    errorDiv.textContent = 'An error occurred. Please try again.';
                    errorDiv.style.display = 'block';
                }
                console.error('Registration error:', error);
            });
        });
    }
    
});