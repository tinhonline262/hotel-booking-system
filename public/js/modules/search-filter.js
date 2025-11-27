/**
 * Search Filter Manager
 * Handles date picker, guest selector, and URL-based filtering
 */
class SearchFilterManager {
    constructor() {
        this.checkInDate = null;
        this.checkOutDate = null;
        this.guests = 1;
        this.init();
    }

    init() {
        this.loadFromURL();
        this.setupDatePicker();
        this.setupGuestSelector();
        this.updateDisplay();
    }

    /**
     * Load filters from URL parameters
     */
    loadFromURL() {
        const urlParams = new URLSearchParams(window.location.search);

        if (urlParams.has('check_in')) {
            this.checkInDate = urlParams.get('check_in');
        }

        if (urlParams.has('check_out')) {
            this.checkOutDate = urlParams.get('check_out');
        }

        if (urlParams.has('guests')) {
            this.guests = parseInt(urlParams.get('guests')) || 1;
        }
    }

    /**
     * Setup date picker functionality
     */
    setupDatePicker() {
        const dateField = $('#datePickerField');
        const dateInput = dateField.find('.date-input');

        // Create date picker modal
        this.createDatePickerModal();

        // Toggle date picker on click
        dateField.on('click', (e) => {
            e.stopPropagation();
            this.toggleDatePicker();
        });
    }

    /**
     * Create date picker modal
     */
    createDatePickerModal() {
        const modal = $(`
            <div class="datepicker-modal" id="datePickerModal" style="display: none;">
                <div class="datepicker-content">
                    <div class="datepicker-header">
                        <h3>Chọn ngày</h3>
                        <button class="close-btn" onclick="searchFilter.closeDatePicker()">×</button>
                    </div>
                    <div class="datepicker-body">
                        <div class="date-input-group">
                            <label>Ngày nhận phòng</label>
                            <input type="date" id="checkInInput" min="${this.getTodayDate()}">
                        </div>
                        <div class="date-input-group">
                            <label>Ngày trả phòng</label>
                            <input type="date" id="checkOutInput" min="${this.getTodayDate()}">
                        </div>
                    </div>
                    <div class="datepicker-footer">
                        <button class="btn-secondary" onclick="searchFilter.clearDates()">Xóa</button>
                        <button class="btn-primary" onclick="searchFilter.applyDates()">Áp dụng</button>
                    </div>
                </div>
            </div>
        `);

        $('body').append(modal);

        // Set current values if any
        if (this.checkInDate) {
            $('#checkInInput').val(this.checkInDate);
        }
        if (this.checkOutDate) {
            $('#checkOutInput').val(this.checkOutDate);
        }

        // Update check-out min date when check-in changes
        $('#checkInInput').on('change', (e) => {
            const checkIn = e.target.value;
            $('#checkOutInput').attr('min', checkIn);
        });
    }

    /**
     * Setup guest selector functionality
     */
    setupGuestSelector() {
        const guestsField = $('#guestsField');

        // Create guest selector modal
        this.createGuestSelectorModal();

        // Toggle guest selector on click
        guestsField.on('click', (e) => {
            e.stopPropagation();
            this.toggleGuestSelector();
        });
    }

    /**
     * Create guest selector modal
     */
    createGuestSelectorModal() {
        const modal = $(`
            <div class="guest-modal" id="guestModal" style="display: none;">
                <div class="guest-content">
                    <div class="guest-header">
                        <h3>Số lượng khách</h3>
                        <button class="close-btn" onclick="searchFilter.closeGuestSelector()">×</button>
                    </div>
                    <div class="guest-body">
                        <div class="guest-row">
                            <div class="guest-info">
                                <h4>Số khách</h4>
                                <p>Tổng số người ở</p>
                            </div>
                            <div class="guest-controls">
                                <button class="guest-btn" onclick="searchFilter.decrementGuests()">−</button>
                                <span class="guest-count" id="guestCountDisplay">${this.guests}</span>
                                <button class="guest-btn" onclick="searchFilter.incrementGuests()">+</button>
                            </div>
                        </div>
                    </div>
                    <div class="guest-footer">
                        <button class="btn-primary" onclick="searchFilter.applyGuests()">Áp dụng</button>
                    </div>
                </div>
            </div>
        `);

        $('body').append(modal);
    }

    /**
     * Toggle date picker
     */
    toggleDatePicker() {
        const modal = $('#datePickerModal');
        if (modal.is(':visible')) {
            modal.hide();
        } else {
            this.closeGuestSelector();
            modal.show();
        }
    }

    /**
     * Toggle guest selector
     */
    toggleGuestSelector() {
        const modal = $('#guestModal');
        if (modal.is(':visible')) {
            modal.hide();
        } else {
            this.closeDatePicker();
            modal.show();
        }
    }

    /**
     * Close date picker
     */
    closeDatePicker() {
        $('#datePickerModal').hide();
    }

    /**
     * Close guest selector
     */
    closeGuestSelector() {
        $('#guestModal').hide();
    }

    /**
     * Apply selected dates
     */
    applyDates() {
        const checkIn = $('#checkInInput').val();
        const checkOut = $('#checkOutInput').val();

        if (!checkIn || !checkOut) {
            alert('Vui lòng chọn cả ngày nhận và trả phòng');
            return;
        }

        // Allow same day check-in and check-out (counts as 1 night)
        if (new Date(checkIn) > new Date(checkOut)) {
            alert('Ngày trả phòng không thể trước ngày nhận phòng');
            return;
        }

        this.checkInDate = checkIn;
        this.checkOutDate = checkOut;

        this.closeDatePicker();
        this.updateDisplay();
        this.updateURL();

        // Trigger search
        if (window.searchManager) {
            window.searchManager.performSearch();
        }
    }

    /**
     * Clear dates
     */
    clearDates() {
        this.checkInDate = null;
        this.checkOutDate = null;
        $('#checkInInput').val('');
        $('#checkOutInput').val('');
        this.closeDatePicker();
        this.updateDisplay();
        this.updateURL();

        // Trigger search
        if (window.searchManager) {
            window.searchManager.performSearch();
        }
    }

    /**
     * Increment guests
     */
    incrementGuests() {
        if (this.guests < 10) {
            this.guests++;
            $('#guestCountDisplay').text(this.guests);
        }
    }

    /**
     * Decrement guests
     */
    decrementGuests() {
        if (this.guests > 1) {
            this.guests--;
            $('#guestCountDisplay').text(this.guests);
        }
    }

    /**
     * Apply guest count
     */
    applyGuests() {
        this.closeGuestSelector();
        this.updateDisplay();
        this.updateURL();

        // Trigger search
        if (window.searchManager) {
            window.searchManager.performSearch();
        }
    }

    /**
     * Update display
     */
    updateDisplay() {
        // Update date display
        const dateDisplay = $('.date-input');
        if (this.checkInDate && this.checkOutDate) {
            const checkIn = this.formatDateDisplay(this.checkInDate);
            const checkOut = this.formatDateDisplay(this.checkOutDate);
            dateDisplay.text(`${checkIn} - ${checkOut}`);
        } else {
            dateDisplay.text('Chọn ngày');
        }

        // Update guest display
        const guestDisplay = $('.guest-count');
        guestDisplay.text(`${this.guests} khách`);
    }

    /**
     * Update URL with current filters
     */
    updateURL() {
        const params = new URLSearchParams();

        if (this.checkInDate) {
            params.set('check_in', this.checkInDate);
        }

        if (this.checkOutDate) {
            params.set('check_out', this.checkOutDate);
        }

        if (this.guests > 1) {
            params.set('guests', this.guests);
        }

        const newURL = params.toString() ? `?${params.toString()}` : window.location.pathname;
        window.history.pushState({}, '', newURL);
    }

    /**
     * Get current filters
     */
    getFilters() {
        const filters = {};

        if (this.checkInDate) {
            filters.check_in = this.checkInDate;
        }

        if (this.checkOutDate) {
            filters.check_out = this.checkOutDate;
        }

        if (this.guests > 1) {
            filters.guests = this.guests;
        }

        return filters;
    }

    /**
     * Get today's date in YYYY-MM-DD format
     */
    getTodayDate() {
        const today = new Date();
        return today.toISOString().split('T')[0];
    }

    /**
     * Format date for display
     */
    formatDateDisplay(dateStr) {
        const date = new Date(dateStr);
        const months = ['Thg 1', 'Thg 2', 'Thg 3', 'Thg 4', 'Thg 5', 'Thg 6',
                       'Thg 7', 'Thg 8', 'Thg 9', 'Thg 10', 'Thg 11', 'Thg 12'];
        return `${date.getDate()} ${months[date.getMonth()]}`;
    }

    /**
     * Calculate number of nights
     */
    getNumberOfNights() {
        if (!this.checkInDate || !this.checkOutDate) {
            return 0;
        }

        const checkIn = new Date(this.checkInDate);
        const checkOut = new Date(this.checkOutDate);
        const diffTime = Math.abs(checkOut - checkIn);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

        return diffDays;
    }
}

// Initialize filter manager
let searchFilter;

$(document).ready(function() {
    searchFilter = new SearchFilterManager();

    // Close modals when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.datepicker-modal, .guest-modal, #datePickerField, #guestsField').length) {
            searchFilter.closeDatePicker();
            searchFilter.closeGuestSelector();
        }
    });
});
