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

        console.log('=== API Request Debug ===');
        console.log('URL:', url);
        console.log('Method:', options.method || 'GET');
        console.log('ProcessData:', options.processData === undefined ? true : options.processData);
        console.log('ContentType:', options.contentType);

        return new Promise((resolve, reject) => {
          const ajaxOptions = {
            url: url,
            method: options.method || 'GET',
            data: options.data || options.body,
            dataType: 'json',
            processData: options.processData === undefined ? true : options.processData,
            statusCode: {
              422: function (xhr) {
                resolve(xhr.responseJSON);
              }
            },
            success: function (data) {
              console.log('Request success:', data);
              resolve(data);
            },
            error: function (xhr) {
              console.error('Request error:', xhr.status, xhr.responseText);
              const error = xhr.responseJSON || { message: 'Request failed' };
              reject(new Error(error.message));
            }
          };

          // Only set contentType if not explicitly set to false
          if (options.contentType !== false) {
            ajaxOptions.contentType = options.contentType || 'application/json';
          } else {
            // Explicitly set to false for file uploads
            ajaxOptions.contentType = false;
          }

          console.log('Final AJAX options:', {
            url: ajaxOptions.url,
            method: ajaxOptions.method,
            processData: ajaxOptions.processData,
            contentType: ajaxOptions.contentType,
            dataType: ajaxOptions.dataType
          });

          $.ajax(ajaxOptions);
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
            data: JSON.stringify(data),
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