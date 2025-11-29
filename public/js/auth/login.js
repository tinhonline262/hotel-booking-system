/**
 * Admin Login Handler - Pure JS with ApiService
 * File: public/assets/js/auth/login.js
 */


class AdminLogin {
  constructor() {
    this.api = new ApiService('http://localhost:8000/api');
    this.init();
  }

  init() {
    // Check if already logged in
    this.checkAuth();

    // Setup form handler
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
      loginForm.addEventListener('submit', (e) => this.handleLogin(e));
    }
  }

  /**
   * Check authentication status
   */
  async checkAuth() {
    try {
      const response = await this.api.get('/auth/check');

      if (response.success && response.data.authenticated) {
        // Already logged in, redirect to dashboard
        window.location.href = 'dashboard.html';
      }
    } catch (error) {
      console.log('Not authenticated');
    }
  }

  /**
   * Handle login form submission
   */
  async handleLogin(e) {
    e.preventDefault();

    const submitBtn = document.getElementById('submitBtn');
    const errorDiv = document.getElementById('errorMessage');

    // ✅ Đọc giá trị
    const identifier = document.getElementById('identifier').value.trim();
    const password = document.getElementById('password').value;

    // ✅ Debug đúng
    console.log('=== DEBUG LOGIN ===');
    console.log('Identifier value:', identifier);
    console.log('Password value:', password);
    console.log('Identifier length:', identifier.length);
    console.log('Password length:', password.length);

    // ✅ Tạo formData với key "username" để gửi đi
    const formData = {
      username: identifier,  // Backend cần key "username"
      password: password
    };

    console.log('Form data to send:', formData);

    // Clear previous errors
    this.clearErrors();

    // Disable submit button
    submitBtn.disabled = true;
    submitBtn.textContent = 'Logging in...';

    try {
      console.log('Sending request...');
      const response = await this.api.post('/auth/login', formData);
      console.log('Response:', response);

      if (response.success) {
        this.showSuccess('Login successful! Redirecting...');
        sessionStorage.setItem('admin', JSON.stringify(response.data.admin));
        setTimeout(() => {
          window.location.href = 'dashboard.html';
        }, 1000);
      } else {
        // ✅ Xử lý response.success = false
        this.showError(response.message || 'Invalid credentials');
        submitBtn.disabled = false;
        submitBtn.textContent = 'Login';
      }
    } catch (error) {
      console.error('Login error:', error);
      const errorMsg = error.message || 'Login failed. Please try again.';
      this.showError(errorMsg);

      submitBtn.disabled = false;
      submitBtn.textContent = 'Login';
    }
  }

  /**
   * Clear all error messages
   */
  clearErrors() {
    const errorDiv = document.getElementById('errorMessage');
    if (errorDiv) {
      errorDiv.style.display = 'none';
      errorDiv.textContent = '';
    }
  }

  /**
   * Show error message
   */
  showError(message) {
    const errorDiv = document.getElementById('errorMessage');
    if (errorDiv) {
      errorDiv.textContent = message;
      errorDiv.style.display = 'block';
      errorDiv.className = 'alert alert-danger';
    }
  }

  /**
   * Show success message
   */
  showSuccess(message) {
    const errorDiv = document.getElementById('errorMessage');
    if (errorDiv) {
      errorDiv.textContent = message;
      errorDiv.style.display = 'block';
      errorDiv.className = 'alert alert-success';
    }
  }
}

document.addEventListener('DOMContentLoaded', () => {
  new AdminLogin();
});