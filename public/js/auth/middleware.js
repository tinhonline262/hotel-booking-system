/**
 * Auth Middleware - Protect Admin Pages
 * File: public/assets/js/auth/middleware.js
 * 
 * Include this at the top of every admin page:
 * <script src="/assets/js/auth/middleware.js"></script>
 */

class AuthMiddleware {
  constructor() {
    this.api = new ApiService('http://localhost:8000/api');
    this.checkAuthentication();
  }

  /**
   * Check if admin is authenticated
   */
  async checkAuthentication() {
    try {
      const response = await this.api.get('/auth/check');

      if (!response.success || !response.data.authenticated) {
        // Not authenticated, redirect to login
        this.redirectToLogin();
      } else {
        // Authenticated, store admin info
        this.setAdminInfo(response.data.admin);
        
        // Update UI with admin info
        this.updateUI(response.data.admin);
      }
    } catch (error) {
      console.error('Auth check failed:', error);
      this.redirectToLogin();
    }
  }

  /**
   * Redirect to login page
   */
  redirectToLogin() {
    sessionStorage.removeItem('admin');
    window.location.href = '/admin/login.html';
  }

  /**
   * Store admin info in sessionStorage
   */
  setAdminInfo(admin) {
    if (admin) {
      sessionStorage.setItem('admin', JSON.stringify(admin));
    }
  }

  /**
   * Get admin info from sessionStorage
   */
  static getAdminInfo() {
    const adminData = sessionStorage.getItem('admin');
    return adminData ? JSON.parse(adminData) : null;
  }

  /**
   * Update UI with admin information
   */
  updateUI(admin) {
    // Update admin name displays
    const adminNameElements = document.querySelectorAll('[data-admin-name]');
    adminNameElements.forEach(el => {
      el.textContent = admin.full_name || admin.username;
    });

    // Update admin username displays
    const adminUsernameElements = document.querySelectorAll('[data-admin-username]');
    adminUsernameElements.forEach(el => {
      el.textContent = admin.username;
    });

    // Update admin email displays
    const adminEmailElements = document.querySelectorAll('[data-admin-email]');
    adminEmailElements.forEach(el => {
      el.textContent = admin.email;
    });
  }

  /**
   * Logout handler
   */
  static async logout() {
    const api = new ApiService('http://localhost:8000/api');
    
    try {
      await api.post('/auth/logout', {});
      sessionStorage.removeItem('admin');
      window.location.href = '/admin/login.html';
    } catch (error) {
      console.error('Logout failed:', error);
      // Force redirect even on error
      sessionStorage.removeItem('admin');
      window.location.href = '/admin/login.html';
    }
  }
}

// Auto-initialize middleware on every admin page
document.addEventListener('DOMContentLoaded', () => {
  // Only run on admin pages (not on login page)
  if (!window.location.pathname.includes('login.html')) {
    new AuthMiddleware();
  }

  // Setup logout buttons
  const logoutButtons = document.querySelectorAll('[data-logout]');
  logoutButtons.forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      AuthMiddleware.logout();
    });
  });
});