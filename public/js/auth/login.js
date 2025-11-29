/**
 * Admin Login Handler - Pure JS with ApiService
 * File: public/js/auth/login.js
 */

class AdminLogin {
  constructor() {
    this.api = new ApiService('http://localhost:8000/api');
    this.init();
  }

  init() {
    this.checkAuth();

    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
      loginForm.addEventListener('submit', (e) => this.handleLogin(e));
    }
  }

  async checkAuth() {
    try {
      const response = await this.api.get('/auth/check');
      if (response.success && response.data.authenticated) {
        window.location.href = 'dashboard.html';
      }
    } catch (error) {
      console.log('Not authenticated');
    }
  }

  async handleLogin(e) {
    e.preventDefault();

    const submitBtn = document.getElementById('submitBtn');
    const identifier = document.getElementById('identifier').value.trim();
    const password = document.getElementById('password').value;

    const formData = {
      username: identifier,
      password: password
    };

    this.clearErrors();
    submitBtn.disabled = true;
    submitBtn.textContent = 'Logging in...';

    try {
      const response = await this.api.post('/auth/login', formData);

      if (response.success) {
        this.showSuccess('Login successful! Redirecting...');
        sessionStorage.setItem('admin', JSON.stringify(response.data.admin));
        setTimeout(() => {
          window.location.href = 'dashboard.html';
        }, 1000);
      } else {
        this.showError(response.message || 'Invalid credentials');
        submitBtn.disabled = false;
        submitBtn.textContent = 'Sign In';
      }
    } catch (error) {
      console.error('Login error:', error);
      this.showError(error.message || 'Login failed. Please try again.');
      submitBtn.disabled = false;
      submitBtn.textContent = 'Sign In';
    }
  }

  async logout() {
    try {
      const response = await this.api.post('/auth/logout', {});

      if (response.success) {
        sessionStorage.removeItem('admin');
        window.location.href = 'login.html';
      }
    } catch (error) {
      console.error('Logout failed:', error);
      sessionStorage.removeItem('admin');
      window.location.href = 'login.html';
    }
  }
  clearErrors() {
    const errorDiv = document.getElementById('errorMessage');
    if (errorDiv) {
      errorDiv.style.display = 'none';
      errorDiv.textContent = '';
    }
  }

  showError(message) {
    const errorDiv = document.getElementById('errorMessage');
    if (errorDiv) {
      errorDiv.textContent = message;
      errorDiv.style.display = 'block';
      errorDiv.className = 'alert alert-danger';
    }
  }

  showSuccess(message) {
    const errorDiv = document.getElementById('errorMessage');
    if (errorDiv) {
      errorDiv.textContent = message;
      errorDiv.style.display = 'block';
      errorDiv.className = 'alert alert-success';
    }
  }
}

// ✅ Tạo instance global để dùng logout ở nơi khác
let adminLogin;

document.addEventListener('DOMContentLoaded', () => {
  adminLogin = new AdminLogin();
});

// ✅ Function logout global để gọi từ button
function logout() {
  if (adminLogin) {
    adminLogin.logout();
  }
}