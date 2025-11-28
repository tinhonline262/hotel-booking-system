class SearchResultsManager {
    constructor(apiBaseUrl) {
        this.api = new ApiService(apiBaseUrl);
        this.currentRooms = [];
        this.displayedCount = 12; // Initial display count
        this.allRooms = [];
        this.filters = {}; // Store current filters
        this.init();
    }

    init() {
        this.performSearch();
        this.setupEventListeners();
    }

    setupEventListeners() {
        // Load more button
        $('.load_more_btn').on('click', () => this.loadMore());
    }

    /**
     * Perform search with current filters
     */
    async performSearch() {
        // Get filters from URL and filter manager
        const urlFilters = this.getFiltersFromURL();
        const filterManagerFilters = window.searchFilter ? window.searchFilter.getFilters() : {};

        // Merge filters
        this.filters = { ...urlFilters, ...filterManagerFilters };

        await this.loadRooms(this.filters);
    }

    /**
     * Get filters from URL parameters
     */
    getFiltersFromURL() {
        const params = new URLSearchParams(window.location.search);
        const filters = {};

        if (params.has('check_in')) filters.check_in = params.get('check_in');
        if (params.has('check_out')) filters.check_out = params.get('check_out');
        if (params.has('guests')) filters.guests = params.get('guests');
        if (params.has('room_type')) filters.room_type = params.get('room_type');
        if (params.has('min_price')) filters.min_price = params.get('min_price');
        if (params.has('max_price')) filters.max_price = params.get('max_price');

        return filters;
    }

    async loadRooms(filters = {}) {
        try {
            // Show loading overlay
            this.showLoading();

            // Determine which endpoint to use based on filters
            let endpoint = '/search/rooms/available';

            // If has dates, use date search endpoint
            if (filters.check_in && filters.check_out) {
                endpoint = '/search/rooms/dates';
            }
            // If has any other filters (guests, room_type, price, etc.), use general search
            else if (Object.keys(filters).length > 0) {
                endpoint = '/search/rooms';
            }

            // Build query string from filters
            const queryParams = new URLSearchParams(filters).toString();
            const fullEndpoint = queryParams ? `${endpoint}?${queryParams}` : endpoint;

            console.log('Calling endpoint:', fullEndpoint); // Debug log

            const result = await this.api.get(fullEndpoint);

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
        } finally {
            // Hide loading overlay
            this.hideLoading();
        }
    }

    /**
     * Apply filters to search
     * @param {Object} filters - Filter object with keys: status, room_type, min_price, max_price, capacity, amenities
     */
    async applyFilters(filters) {
        this.filters = filters;
        await this.loadRooms(filters);
    }

    /**
     * Clear all filters and reload
     */
    async clearFilters() {
        this.filters = {};
        await this.loadRooms();
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
        // Extract room data - API returns flat structure
        const roomId = room.roomId || 0;
        const roomNumber = room.roomNumber || 'N/A';
        const typeName = room.roomType || 'Phòng tiêu chuẩn';
        const price = room.pricePerNight ? parseFloat(room.pricePerNight) : 0;
        const capacity = room.capacity || 2;
        const status = room.status || 'available';
        const images = room.images || [];
        
        // Get total price and number of nights from API response (if date search)
        const numberOfNights = room.numberOfNights || 1;
        const totalPrice = room.totalPrice || price;

        // Parse amenities
        const amenities = Array.isArray(room.amenities) ? room.amenities : [];

        // Get primary image or first image
        let primaryImage = images.find(img => img.isPrimary === true || img.isPrimary === 1);
        if (!primaryImage && images.length > 0) {
            primaryImage = images[0];
        }

        // Fix image URL - ensure proper path for local images
        const getImageUrl = (img) => {
            if (!img) return '/images/default-room.jpg';
            let url = img.imageUrl;

            // If it's a local image and doesn't start with http or /
            if (img.storageType === 'local' && !url.startsWith('http') && !url.startsWith('/')) {
                url = '/' + url;
            }
            return url;
        };

        const imageUrl = getImageUrl(primaryImage);

        // Format price
        const formattedPricePerNight = new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(price);

        const formattedTotalPrice = new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(totalPrice);

        // Generate image slider HTML
        const imageSliderHTML = images.length > 0
            ? images.map(img => `<img src="${getImageUrl(img)}" alt="${typeName}" />`).join('\n')
            : `<img src="${imageUrl}" alt="${typeName}" />`;

        // Generate slider dots
        const sliderDotsHTML = images.length > 1
            ? images.map((_, index) => `<span class="dot ${index === 0 ? 'active' : ''}"></span>`).join('\n')
            : '<span class="dot active"></span>';

        return `
            <div class="hotel-card" data-room-id="${roomId}">
                <a href="/listing-detail.html?id=${roomId}" target="_blank" class="hotel-link">
                    <div class="hotel-inner">
                        <!-- image-->
                        <div class="hotel-image">
                            <picture class="image-slider" data-current-slide="0">
                                ${imageSliderHTML}
                            </picture>

                            ${images.length > 1 ? `
                            <button class="slide-btn prev icon" onclick="event.preventDefault(); slideImage(this, -1)"><span><svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="1em"
                                        height="1em"
                                        fill="none"
                                        viewBox="0 0 12 7"
                                        class="text-white rotate-90 text-ml"
                                    >
                                        <path
                                            stroke="currentColor"
                                            stroke-linecap="square"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M2.377 2.188 6 5.812l3.624-3.624"
                                        ></path>
                                    </svg></span></button>
                            <button class="slide-btn next icon" onclick="event.preventDefault(); slideImage(this, 1)"><span><svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="1em"
                                        height="1em"
                                        fill="none"
                                        viewBox="0 0 12 7"
                                        class="text-white rotate-[270deg] text-ml"
                                    >
                                        <path
                                            stroke="currentColor"
                                            stroke-linecap="square"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M2.377 2.188 6 5.812l3.624-3.624"
                                        ></path>
                                    </svg></span></button>
                            ` : ''}

                            <!-- wisslist and share-->
                            <div class="image-actions">
                                <button class="favorite-btn icon" onclick="event.preventDefault(); toggleWishlist(${roomId})"><span><svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="1em"
                                            height="1em"
                                            fill="none"
                                            viewBox="0 0 16 16"
                                            class="shrink-0 hover:scale-125 transition-transform duration-200 text-black"
                                        >
                                            <path
                                                stroke="currentColor"
                                                stroke-linecap="square"
                                                stroke-width="1.333"
                                                d="m7.437 13.575-.001-.001c-1.816-1.667-3.27-3.002-4.277-4.249-1-1.237-1.492-2.306-1.492-3.429 0-1.82 1.403-3.23 3.183-3.23 1.013 0 1.998.481 2.64 1.244l.51.606.51-.606a3.5 3.5 0 0 1 2.64-1.243c1.78 0 3.183 1.408 3.183 3.23 0 1.122-.493 2.191-1.492 3.43-1.008 1.247-2.461 2.584-4.277 4.254q0 0 0 0l-.562.514z"
                                            ></path>
                                        </svg></span></button>
                                <button class="share-btn icon" onclick="event.preventDefault(); shareRoom(${roomId})"><span><svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="1em"
                                            height="1em"
                                            fill="none"
                                            viewBox="0 0 16 16"
                                            class="shrink-0 text-black"
                                        >
                                            <path
                                                fill="currentColor"
                                                d="m10.667 3.333-.947.946-1.06-1.06v7.447H7.34V3.219L6.28 4.28l-.947-.946L8 .666zm2.666 3.333v7.333c0 .734-.6 1.334-1.333 1.334H4c-.74 0-1.333-.6-1.333-1.334V6.666c0-.74.593-1.333 1.333-1.333h2v1.333H4v7.333h8V6.666h-2V5.333h2c.733 0 1.333.593 1.333 1.333"
                                            ></path>
                                        </svg></span></button>
                            </div>

                            <!-- slider navigation -->
                            <div class="slider-dots">
                                ${sliderDotsHTML}
                            </div>
                        </div>

                        <!-- information -->
                        <div class="hotel-info">
                            <div class="hotel-rating">
                                <div class="hotel__no_rating">9.2 / 10</div>
                                <div class="hotel__count_rate">&#40;&nbsp;100 đánh giá&nbsp;&#41;</div>
                            </div>
                            <div class="hotel-name">
                                <h2>${typeName}</h2>
                            </div>
                            <div class="hotel-description">
                                <div class="hotel__location">Phòng số ${roomNumber}</div>
                                <div class="hotel__room_summary">${capacity} khách</div>
                                <div class="hotel__utilities">${amenities.length > 0 ? amenities.slice(0, 3).join(', ') : 'Các tiện nghi cơ bản'}</div>
                            </div>
                            <div class="card-footer">
                                <div class="hotel-price">
                                    <div class="hotel__price_one_night">
                                        <span>${formattedPricePerNight}</span>
                                        <span> / đêm</span>
                                    </div>
                                    <div class="hotel__total_price">
                                        <span class="total_price">${formattedTotalPrice}</span>
                                        <span> ${numberOfNights > 1 ? `cho ${numberOfNights} đêm` : 'tổng'}</span>
                                    </div>
                                </div>
                                <div class="hotel-booking">
                                    <a
                                        href="/listing-detail.html?id=${roomId}"
                                        rel="noopener noreferrer"
                                        type="button"
                                    >Đặt ngay <span><svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                width="1em"
                                                height="1em"
                                                fill="none"
                                                viewBox="0 0 16 16"
                                            >
                                                <path
                                                    fill="currentColor"
                                                    d="M12.667 12.667H3.333V3.333H8V2H3.333C2.593 2 2 2.6 2 3.333v9.334C2 13.4 2.593 14 3.333 14h9.334C13.4 14 14 13.4 14 12.667V8h-1.333zM9.333 2v1.333h2.394L5.173 9.887l.94.94 6.554-6.554v2.394H14V2z"
                                                ></path>
                                            </svg></span></a>
                                </div>
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

    showLoading() {
        // Show a loading overlay or spinner
        $('.loading-overlay').fadeIn();
    }

    hideLoading() {
        // Hide the loading overlay or spinner
        $('.loading-overlay').fadeOut();
    }
}

// Helper function for wishlist (you can implement this later)
function toggleWishlist(roomId) {
    console.log('Toggle wishlist for room:', roomId);
    // Implement wishlist functionality here
}

// Helper function for sharing
function shareRoom(roomId) {
    console.log('Share room:', roomId);
    // Implement share functionality here
}

// Image slider function
function slideImage(button, direction) {
    const card = button.closest('.hotel-card');
    const slider = card.querySelector('.image-slider');
    const images = slider.querySelectorAll('img');
    const dots = card.querySelectorAll('.slider-dots .dot');

    if (images.length <= 1) return;

    let currentSlide = parseInt(slider.getAttribute('data-current-slide')) || 0;

    // Hide current image
    images[currentSlide].style.display = 'none';
    dots[currentSlide].classList.remove('active');

    // Calculate next slide
    currentSlide += direction;
    if (currentSlide >= images.length) currentSlide = 0;
    if (currentSlide < 0) currentSlide = images.length - 1;

    // Show next image
    images[currentSlide].style.display = 'block';
    dots[currentSlide].classList.add('active');

    // Update current slide
    slider.setAttribute('data-current-slide', currentSlide);
}

// Initialize when DOM is ready
$(document).ready(function() {
    // Assuming API base URL - adjust if needed
    const apiBaseUrl = window.location.origin + '/api';
    window.searchManager = new SearchResultsManager(apiBaseUrl);
});
