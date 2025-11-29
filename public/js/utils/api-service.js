/**
 * Generic API Service Class - jQuery version
 * Reusable for any REST API endpoints across different pages
 *
 * Usage:
 *   const api = new ApiService('http://localhost:8000/api');
 *   const result = await api.get('/room-types');
 */
class ApiService {
  constructor(baseUrl) {
    this.baseUrl = baseUrl;
  }

  /**
   * Generic AJAX request with error handling
   * @param {string} endpoint - API endpoint (e.g., '/room-types')
   * @param {object} options - jQuery AJAX options
   * @returns {Promise<object>} - Response data
   */
  async request(endpoint, options = {}) {
    const url = `${this.baseUrl}${endpoint}`;

    return new Promise((resolve, reject) => {
      $.ajax({
        url: url,
        method: options.method || 'GET',
        data: options.data || options.body,  // ✅ Dùng trực tiếp
        contentType: options.contentType || 'application/json',
        dataType: 'json',
        processData: options.processData !== false,  // ✅ Thêm này
        statusCode: {
          422: function (xhr) {
            resolve(xhr.responseJSON);
          }
        },
        success: function (data) {
          resolve(data);
        },
        error: function (xhr) {
          const error = xhr.responseJSON || { message: 'Request failed' };
          reject(new Error(error.message));
        }
      });
    });
  }

  /**
   * GET request
   * @param {string} endpoint - API endpoint
   * @param {object} params - Query parameters
   * @returns {Promise<object>}
   */
  get(endpoint, params = {}) {
    return this.request(endpoint, {
      method: "GET",
      data: params,
    });
  }

  /**
   * POST request
   * @param {string} endpoint - API endpoint
   * @param {object} data - Request body data
   * @returns {Promise<object>}
   */
  post(endpoint, data) {
    return this.request(endpoint, {
      method: "POST",
      data: JSON.stringify(data),  // Stringify ở đây
      processData: false  // ✅ Quan trọng: Không để jQuery xử lý data
    });
  }

  /**
   * PUT request
   * @param {string} endpoint - API endpoint
   * @param {object} data - Request body data
   * @returns {Promise<object>}
   */
  put(endpoint, data) {
    return this.request(endpoint, {
      method: "PUT",
      data: JSON.stringify(data),
    });
  }

  /**
   * DELETE request
   * @param {string} endpoint - API endpoint
   * @returns {Promise<object>}
   */
  delete(endpoint) {
    return this.request(endpoint, {
      method: "DELETE",
    });
  }

  /**
   * PATCH request
   * @param {string} endpoint - API endpoint
   * @param {object} data - Request body data
   * @returns {Promise<object>}
   */
  patch(endpoint, data) {
    return this.request(endpoint, {
      method: "PATCH",
      data: JSON.stringify(data),
    });
  }

  /**
   * Upload file (multipart/form-data)
   * @param {string} endpoint - API endpoint
   * @param {FormData} formData - Form data with files
   * @returns {Promise<object>}
   */
  upload(endpoint, formData) {
    return this.request(endpoint, {
      method: "POST",
      data: formData,
      processData: false,
      contentType: false,
    });
  }

  /**
   * Download file
   * @param {string} endpoint - API endpoint
   * @param {string} filename - Filename for download
   */
  download(endpoint, filename) {
    const url = `${this.baseUrl}${endpoint}`;
    const link = document.createElement("a");
    link.href = url;
    link.download = filename;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  }
}
