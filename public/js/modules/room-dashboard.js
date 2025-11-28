/**
 * Room Dashboard Manager - jQuery version
 * Handles dashboard statistics and UI updates for Room Management Dashboard
 * Uses ApiService for backend communication
 * API route: /api/dashboard/stats
 * Response DTO: {
 *   total_rooms, available_rooms, occupied_rooms, cleaning_rooms, 
 *   maintenance_rooms, occupancy_rate, room_type_distribution: { [type]: {count, percentage} }
 * }
 */
class RoomDashboardManager {
    constructor(apiBaseUrl) {
        this.api = new ApiService(apiBaseUrl);
        this.endpoint = "/dashboard/stats";
        this.refreshInterval = null;
        this.init();
    }

    init() {
        this.setActiveSidebar();
        this.setupEventListeners();
        this.loadDashboardStats();
        this.startAutoRefresh();
    }

    setActiveSidebar() {
        const currentPage = window.location.pathname.split('/').pop() || 'rooms.html';
        $('.sidebar__link').removeClass('sidebar__link--active');
        $(`.sidebar__link[href="${currentPage}"]`).addClass('sidebar__link--active');
    }

    setupEventListeners() {
        // Sidebar toggle
        $("#toggle_btn").on("click", () => {
            $("#sidebar").toggleClass("opened");
            $(".page").toggleClass("closed");
            $(".header__left").toggleClass("expanded");
        });

        // Refresh button
        $('[data-action="refresh-data"]').on("click", () => {
            this.loadDashboardStats();
            this.showNotification("Đã làm mới dữ liệu!", "success");
        });

        // Modal close buttons
        $('#closeBookingModal, #closeBookingModalBtn').on('click', () => {
            this.closeBookingDetail();
        });

        // Close modal when clicking overlay
        $('#bookingDetailModal').on('click', (e) => {
            if (e.target.id === 'bookingDetailModal') {
                this.closeBookingDetail();
            }
        });
    }

    async loadDashboardStats() {
        try {
            const result = await this.api.get(this.endpoint);

            if (result.success) {
                const stats = result.data;
                this.updateOverviewStats(stats);
                this.updateRecentBookings(stats);
                this.updateTodayCheckIns(stats);
                this.updateTodayCheckOuts(stats);
            } else {
                this.showNotification(
                    result.message || "Không tải được thống kê!",
                    "error"
                );
            }
        } catch (error) {
            this.showNotification(
                "Lỗi tải thống kê: " + error.message,
                "error"
            );
            console.error("Error loading dashboard stats:", error);
        }
    }

    updateOverviewStats(stats) {
        // Update overview stat cards
        $('#totalRooms').text(stats.total_rooms || 0);

        // Update room breakdown in card 1
        $('#availableRoomsStat').text(stats.available_rooms || 0);
        $('#occupiedRoomsStat').text(stats.occupied_rooms || 0);
        $('#cleaningRoomsStat').text(stats.cleaning_rooms || 0);

        // Update check-in today count in card 2
        $('#todayCheckInsCountValue').text(stats.today_check_ins_count || 0);

        // Update pending bookings count in card 3
        $('#pendingBookingsCountValue').text(stats.pending_bookings_count || 0);
    }

    updateRecentBookings(stats) {
        const bookings = stats.recent_bookings || [];
        const $tbody = $('#recentBookingsTable');

        if (!bookings || bookings.length === 0) {
            $tbody.html(`
                <tr>
                    <td colspan="7" style="text-align:center;padding:40px;color:#999;">
                        Không có booking nào
                    </td>
                </tr>
            `);
            return;
        }

        const rows = bookings.map(booking => {
            const statusClass = `status-badge--${booking.status || 'pending'}`;
            const statusText = this.getStatusText(booking.status);
            const createdDate = booking.created_at ? this.formatDate(booking.created_at) : 'N/A';
            const checkInDate = booking.check_in_date ? this.formatDate(booking.check_in_date) : 'N/A';

            return `
                <tr>
                    <td><span class="booking-code">${this.escapeHtml(booking.booking_code || 'N/A')}</span></td>
                    <td>${this.escapeHtml(booking.customer_name || 'N/A')}</td>
                    <td><strong>${this.escapeHtml(booking.room_number || 'N/A')}</strong></td>
                    <td>${checkInDate}</td>
                    <td><span class="status-badge ${statusClass}">${statusText}</span></td>
                    <td>${createdDate}</td>
                    <td>
                        <button class="btn-detail" data-id="${booking.id}" data-action="view-detail">Xem chi tiết</button>
                    </td>
                </tr>
            `;
        }).join('');

        $tbody.html(rows);

        // Attach event listeners for detail buttons
        $('#recentBookingsTable').find('.btn-detail').on('click', (e) => {
            const bookingId = $(e.target).data('id');
            this.openBookingDetail(bookingId);
        });
    }

    getStatusText(status) {
        const statusMap = {
            'pending': 'Chờ xác nhận',
            'confirmed': 'Đã xác nhận',
            'checked_in': 'Đã check-in',
            'checked_out': 'Đã check-out',
            'cancelled': 'Đã hủy'
        };
        return statusMap[status] || status;
    }

    formatDate(dateStr) {
        if (!dateStr) return 'N/A';
        try {
            const date = new Date(dateStr);
            return date.toLocaleDateString('vi-VN', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit'
            });
        } catch (e) {
            return dateStr;
        }
    }

    updateTodayCheckIns(stats) {
        const checkIns = stats.today_check_ins || [];
        const $tbody = $('#todayCheckInsTable');

        if (!checkIns || checkIns.length === 0) {
            $tbody.html(`
                <tr>
                    <td colspan="5" style="text-align:center;padding:30px;color:#999;">
                        Không có check-in hôm nay
                    </td>
                </tr>
            `);
            return;
        }

        const rows = checkIns.map(checkin => {
            const checkInTime = checkin.check_in_time || '09:00';

            return `
                <tr>
                    <td><span class="booking-code">${this.escapeHtml(checkin.booking_code || 'N/A')}</span></td>
                    <td>${this.escapeHtml(checkin.customer_name || 'N/A')}</td>
                    <td><strong>${this.escapeHtml(checkin.room_number || 'N/A')}</strong></td>
                    <td>${checkInTime}</td>
                    <td>
                        <button class="btn-checkin" data-id="${checkin.id}" data-action="check-in">Check In</button>
                    </td>
                </tr>
            `;
        }).join('');

        $tbody.html(rows);

        // Attach event listeners
        $('#todayCheckInsTable').find('.btn-checkin').on('click', (e) => {
            const bookingId = $(e.target).data('id');
            this.handleCheckIn(bookingId);
        });
    }

    updateTodayCheckOuts(stats) {
        const checkOuts = stats.today_check_outs || [];
        const $tbody = $('#todayCheckOutsTable');

        if (!checkOuts || checkOuts.length === 0) {
            $tbody.html(`
                <tr>
                    <td colspan="4" style="text-align:center;padding:30px;color:#999;">
                        Không có check-out hôm nay
                    </td>
                </tr>
            `);
            return;
        }

        const rows = checkOuts.map(checkout => {
            return `
                <tr>
                    <td><span class="booking-code">${this.escapeHtml(checkout.booking_code || 'N/A')}</span></td>
                    <td>${this.escapeHtml(checkout.customer_name || 'N/A')}</td>
                    <td><strong>${this.escapeHtml(checkout.room_number || 'N/A')}</strong></td>
                    <td>
                        <button class="btn-checkout" data-id="${checkout.id}" data-action="check-out">Check Out</button>
                    </td>
                </tr>
            `;
        }).join('');

        $tbody.html(rows);

        // Attach event listeners
        $('#todayCheckOutsTable').find('.btn-checkout').on('click', (e) => {
            const bookingId = $(e.target).data('id');
            this.handleCheckOut(bookingId);
        });
    }

    handleCheckIn(bookingId) {
        if (confirm('Xác nhận check-in cho booking này?')) {
            this.api.put(`/bookings/${bookingId}`, { status: 'checked_in' })
                .then(result => {
                    if (result.success) {
                        this.showNotification('Check-in thành công!', 'success');
                        this.loadDashboardStats();
                    } else {
                        this.showNotification(result.message || 'Lỗi check-in', 'error');
                    }
                })
                .catch(error => {
                    this.showNotification('Lỗi: ' + error.message, 'error');
                });
        }
    }

    handleCheckOut(bookingId) {
        if (confirm('Xác nhận check-out cho booking này?')) {
            this.api.put(`/bookings/${bookingId}`, { status: 'checked_out' })
                .then(result => {
                    if (result.success) {
                        this.showNotification('Check-out thành công!', 'success');
                        this.loadDashboardStats();
                    } else {
                        this.showNotification(result.message || 'Lỗi check-out', 'error');
                    }
                })
                .catch(error => {
                    this.showNotification('Lỗi: ' + error.message, 'error');
                });
        }
    }

    openBookingDetail(bookingId) {
        const $modal = $('#bookingDetailModal');
        const $content = $('#bookingDetailContent');

        // Show loading
        $content.html(`
            <div style="text-align:center;padding:40px;color:#999;">
                Đang tải dữ liệu...
            </div>
        `);

        $modal.removeClass('modal--hidden');
        $('body').css('overflow', 'hidden');

        // Fetch booking detail
        this.api.get(`/bookings/${bookingId}`)
            .then(result => {
                if (result.success && result.data) {
                    this.renderBookingDetail(result.data);
                } else {
                    $content.html(`
                        <div style="text-align:center;padding:40px;color:#e74c3c;">
                            Không thể tải chi tiết booking
                        </div>
                    `);
                }
            })
            .catch(error => {
                $content.html(`
                    <div style="text-align:center;padding:40px;color:#e74c3c;">
                        Lỗi: ${error.message}
                    </div>
                `);
            });
    }

    renderBookingDetail(booking) {
        const $content = $('#bookingDetailContent');
        const statusText = this.getStatusText(booking.status);
        const checkInDate = booking.check_in_date ? this.formatDate(booking.check_in_date) : 'N/A';
        const checkOutDate = booking.check_out_date ? this.formatDate(booking.check_out_date) : 'N/A';
        const createdDate = booking.created_at ? this.formatDate(booking.created_at) : 'N/A';

        const statusClass = `status-badge--${booking.status || 'pending'}`;

        const html = `
            <div class="detail-section detail-section--info">
                <div class="detail-section__title">Thông tin chung</div>
                <div class="detail-row">
                    <span class="detail-row__label">Mã Booking:</span>
                    <span class="detail-row__value detail-row__value--code">${this.escapeHtml(booking.booking_code || 'N/A')}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-row__label">Khách hàng:</span>
                    <span class="detail-row__value">${this.escapeHtml(booking.customer_name || 'N/A')}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-row__label">Email:</span>
                    <span class="detail-row__value">${this.escapeHtml(booking.customer_email || 'N/A')}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-row__label">Số điện thoại:</span>
                    <span class="detail-row__value">${this.escapeHtml(booking.customer_phone || 'N/A')}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-row__label">Trạng thái:</span>
                    <span class="detail-status status-badge ${statusClass}">${statusText}</span>
                </div>
            </div>

            <div class="detail-section detail-section--checkin">
                <div class="detail-section__title">Thông tin nhận phòng</div>
                <div class="detail-row">
                    <span class="detail-row__label">Số phòng:</span>
                    <span class="detail-row__value">${this.escapeHtml(booking.room_number || 'N/A')}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-row__label">Ngày nhận:</span>
                    <span class="detail-row__value">${checkInDate}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-row__label">Số khách:</span>
                    <span class="detail-row__value">${booking.num_guests || 1} người</span>
                </div>
            </div>

            <div class="detail-section detail-section--checkout">
                <div class="detail-section__title">Thông tin trả phòng</div>
                <div class="detail-row">
                    <span class="detail-row__label">Ngày trả:</span>
                    <span class="detail-row__value">${checkOutDate}</span>
                </div>
            </div>

            <div class="detail-section detail-section--price">
                <div class="detail-section__title">Thông tin thanh toán</div>
                <div class="detail-row">
                    <span class="detail-row__label">Giá phòng:</span>
                    <span class="detail-row__value">${this.formatCurrency(booking.total_price || 0)}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-row__label">Ngày đặt:</span>
                    <span class="detail-row__value">${createdDate}</span>
                </div>
            </div>
        `;

        $content.html(html);
    }

    formatCurrency(value) {
        if (!value) return '0 ₫';
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(value);
    }

    closeBookingDetail() {
        $('#bookingDetailModal').addClass('modal--hidden');
        $('body').css('overflow', 'auto');
    }

    startAutoRefresh() {
        // Auto refresh every 30 seconds
        this.refreshInterval = setInterval(() => {
            this.loadDashboardStats();
        }, 30000);
    }

    stopAutoRefresh() {
        if (this.refreshInterval) {
            clearInterval(this.refreshInterval);
            this.refreshInterval = null;
        }
    }

    showNotification(message, type = "info") {
        // Create notification container if it doesn't exist
        if ($("#notification-container").length === 0) {
            $("body").append(`
                <div id="notification-container" style="
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    z-index: 9999;
                    display: flex;
                    flex-direction: column;
                    gap: 10px;
                    max-width: 420px;
                "></div>
            `);
        }

        const colors = {
            success: { bg: "#d4edda", border: "#c3e6cb", text: "#155724" },
            error: { bg: "#f8d7da", border: "#f5c6cb", text: "#721c24" },
            warning: { bg: "#fff3cd", border: "#ffeaa7", text: "#856404" },
            info: { bg: "#d1ecf1", border: "#bee5eb", text: "#0c5460" }
        };
        const color = colors[type] || colors.info;

        const $notification = $(`
            <div class="notification notification--${type}" style="
                background-color: ${color.bg};
                border: 1px solid ${color.border};
                color: ${color.text};
                padding: 15px 20px;
                border-radius: 4px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.09);
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 15px;
                animation: slideIn 0.3s;
                min-width: 300px;
            ">
                <span style="flex: 1;">${this.escapeHtml(message)}</span>
                <button class="notification-close" style="
                    background: none;
                    border: none;
                    color: ${color.text};
                    cursor: pointer;
                    font-size: 19px;
                    line-height: 1;
                    padding: 0;
                ">&times;</button>
            </div>
        `);

        $notification.find(".notification-close").on("click", function () {
            $(this).closest(".notification").fadeOut(250, function () {
                $(this).remove();
            });
        });

        $("#notification-container").append($notification);

        // Auto remove after 4.4 seconds
        setTimeout(() => {
            $notification.fadeOut(350, function () {
                $(this).remove();
            });
        }, 4400);
    }

    escapeHtml(text) {
        if (!text) return "";
        return String(text).replace(/[&<>"']/g, (m) => ({
            "&": "&amp;",
            "<": "&lt;",
            ">": "&gt;",
            '"': "&quot;",
            "'": "&#039;"
        }[m]));
    }

    // Cleanup method - call when leaving page
    destroy() {
        this.stopAutoRefresh();
    }
}

// Add notification animation styles
if ($("#notification-animations").length === 0) {
    $("head").append(`
        <style id="notification-animations">
            @keyframes slideIn {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
        </style>
    `);
}