async function logout() {
  try {
    const api = new ApiService('http://localhost:8000/api');
    const response = await api.post('/auth/logout', {});
    
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

// Check auth khi load trang
document.addEventListener('DOMContentLoaded', async () => {
  const api = new ApiService('http://localhost:8000/api');
  try {
    const response = await api.get('/auth/check');
    if (!response.success || !response.data.authenticated) {
      window.location.href = 'login.html';
    }
  } catch (error) {
    window.location.href = 'login.html';
  }
});