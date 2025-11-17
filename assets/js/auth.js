
const AUTH_TOKEN_KEY = 'powerbank_auth_token';
const AUTH_USER_KEY = 'powerbank_user_data';

// API Base URL - Update this to your PHP backend URL
// Example: const API_BASE_URL = 'https://yoursite.com/api';
const API_BASE_URL = '/api'; // Placeholder for local mock

// ========================================
// Registration Function
// ========================================

async function register(email, password, name) {
    try {
        if (!email || !password || !name) {
            return { success: false, message: 'All fields are required' };
        }

        if (password.length < 8) {
            return { success: false, message: 'Password must be at least 8 characters' };
        }

        // Replace mock with real backend fetch when ready
        return mockRegister(email, password, name);

    } catch (error) {
        console.error('Registration error:', error);
        return { success: false, message: 'Registration failed. Please try again.' };
    }
}

// ========================================
// Login Function
// ========================================

async function login(email, password) {
    try {
        if (!email || !password) {
            return { success: false, message: 'Email and password are required' };
        }

        // Replace mock with real backend fetch when ready
        return mockLogin(email, password);

    } catch (error) {
        console.error('Login error:', error);
        return { success: false, message: 'Login failed. Please try again.' };
    }
}

// ========================================
// Logout Function
// ========================================

async function logout() {
    try {
        // Clear local storage
        localStorage.removeItem(AUTH_TOKEN_KEY);
        localStorage.removeItem(AUTH_USER_KEY);
        
        // Update UI
        updateAuthUI();
        
        return true;
    } catch (error) {
        console.error('Logout error:', error);
        return false;
    }
}

// ========================================
// Get Current User
// ========================================

function getCurrentUser() {
    try {
        const userData = localStorage.getItem(AUTH_USER_KEY);
        return userData ? JSON.parse(userData) : null;
    } catch (error) {
        console.error('Error getting current user:', error);
        return null;
    }
}

// ========================================
// Check Authentication
// ========================================

function isAuthenticated() {
    const token = localStorage.getItem(AUTH_TOKEN_KEY);
    const user = getCurrentUser();
    return !!(token && user);
}

// ========================================
// Require Authentication
// ========================================

function requireAuth(redirectUrl) {
    if (!isAuthenticated()) {
        const loginUrl = `login.html?redirect=${encodeURIComponent(redirectUrl)}`;
        window.location.href = loginUrl;
        return false;
    }
    return true;
}

// ========================================
// Update Authentication UI
// ========================================

function updateAuthUI() {
    const authNav = document.getElementById('authNav');
    if (!authNav) return;

    if (isAuthenticated()) {
        const user = getCurrentUser();
        authNav.innerHTML = `
            <span class="user-greeting">Hi, ${user.name}</span>
            <a href="account.html">Account</a>
            <a href="#" id="logoutBtn">Logout</a>
        `;

        const logoutBtn = document.getElementById('logoutBtn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', async function(e) {
                e.preventDefault();
                await logout();
                window.location.href = 'index.html';
            });
        }
    } else {
        authNav.innerHTML = `
            <a href="login.html">Login</a>
            <a href="register.html">Register</a>
        `;
    }
}

// ========================================
// MOCK FUNCTIONS (For Development Only)
// Remove these when integrating with real backend
// ========================================

function mockRegister(email, password, name) {
    const existingUsers = JSON.parse(localStorage.getItem('mock_users') || '[]');
    
    if (existingUsers.length === 0) {
        existingUsers.push({
            id: 'u-1',
            email: 'student@example.com',
            password: 'Password123!',
            name: 'Test User'
        });
    }

    if (existingUsers.find(u => u.email === email)) {
        return { success: false, message: 'Email already registered' };
    }

    const newUser = { id: 'u-' + Date.now(), email, password, name };
    existingUsers.push(newUser);
    localStorage.setItem('mock_users', JSON.stringify(existingUsers));

    const mockToken = 'mock_token_' + Date.now();

    localStorage.setItem(AUTH_TOKEN_KEY, mockToken);
    localStorage.setItem(AUTH_USER_KEY, JSON.stringify({ id: newUser.id, email: newUser.email, name: newUser.name }));

    updateAuthUI();

    return {
        success: true,
        message: 'Registration successful',
        user: { id: newUser.id, email: newUser.email, name: newUser.name },
        token: mockToken
    };
}

function mockLogin(email, password) {
    const existingUsers = JSON.parse(localStorage.getItem('mock_users') || '[]');
    
    if (existingUsers.length === 0) {
        existingUsers.push({
            id: 'u-1',
            email: 'student@example.com',
            password: 'Password123!',
            name: 'Test User'
        });
        localStorage.setItem('mock_users', JSON.stringify(existingUsers));
    }

    const user = existingUsers.find(u => u.email === email && u.password === password);

    if (!user) {
        return { success: false, message: 'Invalid email or password' };
    }

    const mockToken = 'mock_token_' + Date.now();

    localStorage.setItem(AUTH_TOKEN_KEY, mockToken);
    localStorage.setItem(AUTH_USER_KEY, JSON.stringify({ id: user.id, email: user.email, name: user.name }));

    updateAuthUI();

    return {
        success: true,
        message: 'Login successful',
        user: { id: user.id, email: user.email, name: user.name },
        token: mockToken
    };
}

document.addEventListener('DOMContentLoaded', function() {
    updateAuthUI();
    // updateCartBadge is defined in main.js; guard in case main.js isn't loaded yet
    if (typeof updateCartBadge === 'function') {
        updateCartBadge();
    }
});
