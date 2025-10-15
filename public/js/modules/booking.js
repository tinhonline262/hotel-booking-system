// Booking Module
const BookingModule = (function($) {
    'use strict';

    function calculateTotal() {
        const checkIn = new Date($('#check_in').val());
        const checkOut = new Date($('#check_out').val());
        const pricePerNight = parseFloat($('#price_per_night').val());

        if (checkIn && checkOut && pricePerNight) {
            const days = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
            if (days > 0) {
                const total = days * pricePerNight;
                $('#total_nights').text(days);
                $('#total_price').text(total.toFixed(2));
                $('input[name="total_price"]').val(total);
            }
        }
    }

    function init() {
        $('#check_in, #check_out').on('change', calculateTotal);
        calculateTotal();
    }

    return {
        init: init
    };

})(jQuery);

// Initialize on page load
$(document).ready(function() {
    if ($('.booking-form').length) {
        BookingModule.init();
    }
});

