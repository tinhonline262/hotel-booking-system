// Main JavaScript Application
(function($) {
    'use strict';

    // Initialize on document ready
    $(document).ready(function() {
        initializeDatePickers();
        initializeModals();
        initializeFormValidation();
        initializeImageGallery();
    });

    // Date Picker Configuration
    function initializeDatePickers() {
        const today = new Date().toISOString().split('T')[0];
        $('input[type="date"]').attr('min', today);

        // Auto-update checkout date based on checkin
        $('#check_in').on('change', function() {
            const checkIn = new Date($(this).val());
            const minCheckOut = new Date(checkIn);
            minCheckOut.setDate(minCheckOut.getDate() + 1);
            $('#check_out').attr('min', minCheckOut.toISOString().split('T')[0]);
        });
    }

    // Modal Management
    function initializeModals() {
        $('.modal-trigger').on('click', function(e) {
            e.preventDefault();
            const modalId = $(this).data('modal');
            $(`#${modalId}`).addClass('active');
        });

        $('.modal-close, .modal').on('click', function(e) {
            if ($(e.target).hasClass('modal') || $(e.target).hasClass('modal-close')) {
                $('.modal').removeClass('active');
            }
        });

        // Close on ESC key
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                $('.modal').removeClass('active');
            }
        });
    }

    // Form Validation
    function initializeFormValidation() {
        $('form[data-validate]').on('submit', function(e) {
            const form = $(this);
            let isValid = true;

            // Clear previous errors
            form.find('.error-message').remove();
            form.find('.input-error').removeClass('input-error');

            // Required fields
            form.find('[required]').each(function() {
                if (!$(this).val()) {
                    isValid = false;
                    showError($(this), 'This field is required');
                }
            });

            // Email validation
            form.find('input[type="email"]').each(function() {
                if ($(this).val() && !isValidEmail($(this).val())) {
                    isValid = false;
                    showError($(this), 'Please enter a valid email address');
                }
            });

            if (!isValid) {
                e.preventDefault();
            }
        });
    }

    // Image Gallery
    function initializeImageGallery() {
        $('.image-gallery').each(function() {
            const gallery = $(this);
            const images = gallery.find('img');

            images.on('click', function() {
                const src = $(this).attr('src');
                showImageModal(src);
            });
        });
    }

    // Helper Functions
    function showError(input, message) {
        input.addClass('input-error');
        input.after(`<span class="error-message">${message}</span>`);
    }

    function isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    function showImageModal(src) {
        const modal = $(`
            <div class="modal active">
                <div class="modal-content">
                    <span class="modal-close">&times;</span>
                    <img src="${src}" style="width: 100%; height: auto;">
                </div>
            </div>
        `);
        $('body').append(modal);

        modal.on('click', function(e) {
            if ($(e.target).hasClass('modal') || $(e.target).hasClass('modal-close')) {
                modal.remove();
            }
        });
    }

    // AJAX Helper
    window.ajaxRequest = function(url, method, data, callback) {
        $.ajax({
            url: url,
            method: method,
            data: data,
            dataType: 'json',
            success: callback,
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                alert('An error occurred. Please try again.');
            }
        });
    };

    // Show notification
    window.showNotification = function(message, type = 'success') {
        const notification = $(`
            <div class="alert alert-${type}" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
                ${message}
            </div>
        `);

        $('body').append(notification);

        setTimeout(() => {
            notification.fadeOut(() => notification.remove());
        }, 3000);
    };

})(jQuery);

