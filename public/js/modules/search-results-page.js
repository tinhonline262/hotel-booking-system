/**
 * Search Results Page Manager
 * Handles fetching and displaying rooms with details from API
 * API endpoint: /api/rooms/details
 */
class SearchResultsManager {
    constructor(apiBaseUrl) {
        this.api = new ApiService(apiBaseUrl);
        this.endpoint = "/rooms/details";
        this.currentRooms = [];
        this.displayedCount = 12; // Initial display count
        this.allRooms = [];
        this.init();
    }

    init() {
        this.loadRooms();
        this.setupEventListeners();
    }

    setupEventListeners() {
        // Load more button
        $('.load_more_btn').on('click', () => this.loadMore());
        
        // You can add search/filter listeners here later
        // Example: $('#searchForm').on('submit', (e) => this.handleSearch(e));
    }

    async loadRooms() {
        try {
            const result = await this.api.get(this.endpoint);
            
            if (result.success && Array.isArray(result.data)) {
                this.allRooms = result.data;
                this.displayedCount = Math.min(12, this.allRooms.length);
                this.renderRooms();
                this.updateResultCount();
            } else {
                this.showError("Không thể tải danh sách phòng");
            }
        } catch (error) {
            console.error("Error loading rooms:", error);
            this.showError("Lỗi khi tải dữ liệu: " + error.message);
        }
    }

    renderRooms() {
        const container = $('.results_list_wrapper');
        container.empty();

        const roomsToDisplay = this.allRooms.slice(0, this.displayedCount);

        if (roomsToDisplay.length === 0) {
            container.html(`
                <div class="no-results">
                    <p>Không tìm thấy phòng nào phù hợp.</p>
                </div>
            `);
            return;
        }

        roomsToDisplay.forEach(room => {
            const card = this.createRoomCard(room);
            container.append(card);
        });

        // Hide/show load more button
        if (this.displayedCount >= this.allRooms.length) {
            $('.load_more__container').hide();
        } else {
            $('.load_more__container').show();
        }
    }

    createRoomCard(room) {
        // Extract room data
        const roomNumber = room.room_number || 'N/A';
        const roomType = room.room_type || {};
        const typeName = roomType.name || 'Phòng tiêu chuẩn';
        const description = roomType.description || 'Không có mô tả';
        const price = roomType.base_price ? parseFloat(roomType.base_price) : 0;
        const capacity = roomType.max_capacity || 2;
        const status = room.status || 'available';
        const images = room.images || [];
        
        // Get primary image or first image
        let primaryImage = images.find(img => img.is_primary === 1 || img.is_primary === '1');
        if (!primaryImage && images.length > 0) {
            primaryImage = images[0];
        }
        const imageUrl = primaryImage ? primaryImage.image_url : '/images/default-room.jpg';

        // Format price
        const formattedPrice = new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(price);

        // Status badge
        const statusText = status === 'available' ? 'Còn trống' : 'Đã đặt';
        const statusClass = status === 'available' ? 'available' : 'booked';

        return `
            <div class="hotel-card" data-room-id="${room.id}">
                <a href="/room-detail?id=${room.id}" class="hotel-link">
                    <div class="hotel-inner">
                        <!-- image -->
                        <div class="hotel-image">
                            <div class="image-container">
                                <img src="${imageUrl}" alt="${typeName}" class="room-image">
                                <div class="image-badge ${statusClass}">${statusText}</div>
                            </div>
                            <button class="wishlist-btn" data-room-id="${room.id}" onclick="event.preventDefault(); toggleWishlist(${room.id})">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-width="2" d="M12 21l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.18L12 21z"/>
                                </svg>
                            </button>
                            ${images.length > 1 ? `
                                <div class="image-count">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
                                        <path d="M2.002 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2h-12zm12 1a1 1 0 0 1 1 1v6.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12V3a1 1 0 0 1 1-1h12z"/>
                                    </svg>
                                    ${images.length}
                                </div>
                            ` : ''}
                        </div>

                        <!-- information -->
                        <div class="hotel-info">
                            <div class="hotel-header">
                                <div class="hotel-title-section">
                                    <h3 class="hotel-name">${typeName}</h3>
                                    <div class="hotel-location">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/>
                                        </svg>
                                        Phòng số ${roomNumber}
                                    </div>
                                </div>
                            </div>

                            <div class="hotel-amenities">
                                <span class="amenity">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1H7zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                                        <path fill-rule="evenodd" d="M5.216 14A2.238 2.238 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.325 6.325 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1h4.216z"/>
                                        <path d="M4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z"/>
                                    </svg>
                                    ${capacity} khách
                                </span>
                            </div>

                            <p class="hotel-description">${description.substring(0, 100)}${description.length > 100 ? '...' : ''}</p>

                            <div class="hotel-footer">
                                <div class="hotel-price">
                                    <span class="price-label">Giá từ</span>
                                    <div class="price-amount">
                                        <span class="price-value">${formattedPrice}</span>
                                        <span class="price-period">/đêm</span>
                                    </div>
                                </div>
                                <button class="view-details-btn">Xem chi tiết</button>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        `;
    }

    loadMore() {
        this.displayedCount = Math.min(this.displayedCount + 12, this.allRooms.length);
        this.renderRooms();
    }

    updateResultCount() {
        const totalCount = this.allRooms.length;
        $('.results__title span').text(totalCount);
    }

    showError(message) {
        const container = $('.results_list_wrapper');
        container.html(`
            <div class="error-message">
                <p>${message}</p>
            </div>
        `);
    }
}

// Helper function for wishlist (you can implement this later)
function toggleWishlist(roomId) {
    console.log('Toggle wishlist for room:', roomId);
    // Implement wishlist functionality here
}

// Initialize when DOM is ready
$(document).ready(function() {
    // Assuming API base URL - adjust if needed
    const apiBaseUrl = window.location.origin + '/api';
    new SearchResultsManager(apiBaseUrl);
});
