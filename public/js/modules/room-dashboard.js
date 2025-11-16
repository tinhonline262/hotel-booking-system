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
    }

    async loadDashboardStats() {
        try {
            const result = await this.api.get(this.endpoint);
            
            if (result.success) {
                const stats = result.data;
                this.updateOverviewStats(stats);
                this.updateStatusProgress(stats);
                this.updateRoomTypeDistribution(stats);
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
        $('#availableRooms').text(stats.available_rooms || 0);
        $('#occupiedRooms').text(stats.occupied_rooms || 0);
        
        const occupancyRate = stats.occupancy_rate || 0;
        $('#occupancyRate').text(occupancyRate.toFixed(1));
    }

    updateStatusProgress(stats) {
        const totalRooms = stats.total_rooms || 1; // Avoid division by zero

        if (totalRooms > 0) {
            // Calculate percentages
            const availablePct = ((stats.available_rooms || 0) / totalRooms * 100).toFixed(1);
            const occupiedPct = ((stats.occupied_rooms || 0) / totalRooms * 100).toFixed(1);
            const cleaningPct = ((stats.cleaning_rooms || 0) / totalRooms * 100).toFixed(1);
            const maintenancePct = ((stats.maintenance_rooms || 0) / totalRooms * 100).toFixed(1);

            // Update percentage labels
            $('#availablePercentage').text(availablePct + '%');
            $('#occupiedPercentage').text(occupiedPct + '%');
            $('#cleaningPercentage').text(cleaningPct + '%');
            $('#maintenancePercentage').text(maintenancePct + '%');

            // Update progress bars with animation
            $('#availableBar').css('width', availablePct + '%');
            $('#occupiedBar').css('width', occupiedPct + '%');
            $('#cleaningBar').css('width', cleaningPct + '%');
            $('#maintenanceBar').css('width', maintenancePct + '%');
        } else {
            // Reset to 0 if no rooms
            $('#availablePercentage, #occupiedPercentage, #cleaningPercentage, #maintenancePercentage')
                .text('0%');
            $('#availableBar, #occupiedBar, #cleaningBar, #maintenanceBar')
                .css('width', '0%');
        }
    }

    updateRoomTypeDistribution(stats) {
        const distribution = stats.room_type_distribution || {};
        const $container = $('#roomTypeDistribution');

        if (Object.keys(distribution).length === 0) {
            $container.html(`
                <div style="text-align:center;padding:20px;color:#999;">
                    Không có dữ liệu phân bố loại phòng
                </div>
            `);
            return;
        }

        // Generate distribution HTML
        const distributionHtml = Object.entries(distribution)
            .map(([type, data]) => `
                <div class="distribution-item">
                    <div class="distribution-info">
                        <span class="distribution-label">${this.escapeHtml(type)}</span>
                        <span class="distribution-value">
                            ${data.count || 0} phòng (${(data.percentage || 0).toFixed(1)}%)
                        </span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar" 
                             style="width:${data.percentage || 0}%;background:rgb(99,102,241)">
                        </div>
                    </div>
                </div>
            `).join('');
        
        $container.html(distributionHtml);
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