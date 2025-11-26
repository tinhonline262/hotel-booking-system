        /**
 * VacationRenter Landing Page Manager - Clean Version
 * Only uses real backend data fields
 * Removed: destination search, gallery filters, fake data
 */
        class VacationRenterManager {
            constructor(apiBaseUrl) {
                this.api = new ApiService(apiBaseUrl);
                this.allListings = [];
                this.galleryImages = [];
                this.allRooms = [];
                this.listingsPage = 1;
                this.galleryPage = 1;
                this.ITEMS_PER_PAGE = 4;
                this.GALLERY_ITEMS_PER_PAGE = 6;
                this.isExpanded = false;
                this.currentLightboxIndex = 0;

                // Search state - only dates and guests
                this.searchCheckin = null;
                this.searchCheckout = null;
                this.searchGuests = 1;

                this.init();
            }

            async init() {
                await this.loadRoomTypes();
                await this.loadGalleryImages();
                this.setupEventListeners();
                this.setupDatePicker();
                this.setupGuestsSelector();
            }

            /**
             * Load room types for listings section
             */
            async loadRoomTypes() {
                try {
                    const result = await this.api.get('/room-types');

                    if (result.success && result.data) {
                        this.allListings = this.mapRoomTypesToListings(result.data);
                        this.renderListings();
                    }
                } catch (error) {
                    console.error('Error loading room types:', error);
                    this.showEmptyState('listingsGrid', 'Không thể tải danh sách phòng');
                }
            }

            /**
             * Load all rooms with images for gallery
             */
            async loadGalleryImages() {
                try {
                    const result = await this.api.get('/rooms');

                    if (result.success && result.data) {
                        this.allRooms = result.data;

                        // Load images from rooms with details
                        const imagePromises = this.allRooms.slice(0, 12).map(async (room) => {
                            try {
                                const detailResult = await this.api.get(`/rooms/${room.id}/details`);
                                if (detailResult.success && detailResult.data && detailResult.data.images) {
                                    // Map each image to gallery format
                                    return detailResult.data.images.map(img => ({
                                        id: img.imageId,
                                        roomId: detailResult.data.roomId,
                                        image: this.getFullImageUrl(img.imageUrl, img.storageType),
                                        title: detailResult.data.roomType,
                                        location: `Room ${detailResult.data.roomNumber}`,
                                        capacity: detailResult.data.capacity,
                                        price: detailResult.data.pricePerNight
                                    }));
                                }
                            } catch (error) {
                                console.error(`Error loading images for room ${room.id}:`, error);
                            }
                            return [];
                        });

                        const imageArrays = await Promise.all(imagePromises);
                        this.galleryImages = imageArrays.flat().filter(img => img);
                        this.renderGallery();
                    }
                } catch (error) {
                    console.error('Error loading gallery:', error);
                    this.showEmptyState('galleryGrid', 'Không thể tải thư viện ảnh');
                }
            }

            /**
             * Get full image URL based on storage type
             */
            getFullImageUrl(imageUrl, storageType) {
                if (storageType === 'cloudinary' || imageUrl.startsWith('http')) {
                    return imageUrl;
                }
                // Local storage - add base URL
                const baseUrl = this.api.baseUrl.replace('/api', '');
                return imageUrl.startsWith('/') ? `${baseUrl}${imageUrl}` : `${baseUrl}/${imageUrl}`;
            }

            /**
             * Map room types data to listings format (using real fields only)
             */
            mapRoomTypesToListings(roomTypes) {
                return roomTypes.map(rt => {
                    // Parse amenities safely
                    let amenitiesStr = '';
                    if (Array.isArray(rt.amenities)) {
                        amenitiesStr = rt.amenities.join(' · ');
                    } else if (typeof rt.amenities === 'string') {
                        amenitiesStr = rt.amenities;
                    }

                    return {
                        id: rt.id,
                        title: rt.name,
                        description: rt.description || '',
                        capacity: rt.capacity,
                        details: `${rt.capacity} guests · ${Math.floor(rt.capacity / 2) || 1} bedroom`,
                        amenities: amenitiesStr || 'Standard amenities included',
                        pricePerNight: rt.pricePerNight,
                        price: this.formatPriceLevel(rt.pricePerNight),
                        // Generated/placeholder data
                        image: this.getPlaceholderImage(rt.id),

                    };
                });
            }

            /**
             * Get placeholder image (cycle through Unsplash images)
             */
            getPlaceholderImage(id) {
                const images = [
                    'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=600',
                    'https://images.unsplash.com/photo-1582268611958-ebfd161ef9cf?w=600',
                    'https://images.unsplash.com/photo-1613490493576-7fde63acd811?w=600',
                    'https://images.unsplash.com/photo-1602002418082-a4443e081dd1?w=600',
                    'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=600',
                    'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=600'
                ];
                return images[id % images.length];
            }


            /**
             * Format price to $ symbols
             */
            formatPriceLevel(price) {

                return '$';
            }

            /**
             * Format currency to VND
             */
            formatCurrency(price) {
                return new Intl.NumberFormat('vi-VN', {
                    style: 'currency',
                    currency: 'VND'
                }).format(price);
            }

            /**
             * Render listings section
             */
            renderListings() {
                const listingsGrid = document.getElementById('listingsGrid');
                if (!listingsGrid) return;

                if (this.allListings.length === 0) {
                    this.showEmptyState('listingsGrid', 'Không có phòng nào');
                    return;
                }

                const startIndex = 0;
                const endIndex = this.listingsPage * this.ITEMS_PER_PAGE;
                const listingsToShow = this.allListings.slice(startIndex, endIndex);

                listingsGrid.innerHTML = listingsToShow.map(listing => this.createListingCard(listing)).join('');
                this.updateListingsButton();
            }

            /**
             * Create listing card HTML
             */
            createListingCard(listing) {
                return `
            <div class="listing-card">
                <div class="image-container">
                    <img src="${listing.image}" alt="${listing.title}" class="listing-image">
                    ${listing.badge ? `<div class="badge">${listing.badge}</div>` : ''}
                    

                </div>
                <div class="card-content">
                    <h3 class="listing-title">${this.escapeHtml(listing.title)}</h3>
                    <div class="listing-details">${listing.details}</div>
                    <div class="listing-amenities">${this.escapeHtml(listing.amenities)}</div>
                    <div class="card-footer">
                        <div style="display: flex; align-items: center; gap: 16px;">
                            <span class="price">${listing.price}</span>
                            <span style="color: #6b6b6b; font-size: 14px;">${this.formatCurrency(listing.pricePerNight)}/night</span>
                        </div>
                        <button class="view-deal-btn" data-room-id="${listing.id}">
    Xem chi tiết
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <polyline points="9 18 15 12 9 6"></polyline>
    </svg>
</button>
                    </div>
                </div>
            </div>
        `;
            }

            /**
             * Update listings load more button
             */
            updateListingsButton() {
                const loadMoreBtn = document.getElementById('loadMoreBtn');
                if (!loadMoreBtn) return;

                const btnText = loadMoreBtn.querySelector('.btn-text');
                const endIndex = this.listingsPage * this.ITEMS_PER_PAGE;

                if (this.listingsPage > 1) {
                    loadMoreBtn.classList.add('collapse');
                    btnText.textContent = 'Show less';
                    this.isExpanded = true;
                } else {
                    loadMoreBtn.classList.remove('collapse');
                    btnText.textContent = 'Tải thêm';
                    this.isExpanded = false;
                }

                if (endIndex >= this.allListings.length && !this.isExpanded) {
                    loadMoreBtn.style.display = 'none';
                } else {
                    loadMoreBtn.style.display = 'inline-flex';
                }
            }

            /**
             * Render gallery section
             */
            renderGallery() {
                const galleryGrid = document.getElementById('galleryGrid');
                if (!galleryGrid) return;

                if (this.galleryImages.length === 0) {
                    this.showEmptyState('galleryGrid', 'Không có hình ảnh nào');
                    return;
                }

                const itemsToShow = this.galleryImages.slice(0, this.galleryPage * this.GALLERY_ITEMS_PER_PAGE);

                galleryGrid.innerHTML = itemsToShow.map((item, index) => this.createGalleryItem(item, index)).join('');
                this.updateGalleryButton();
            }

            /**
             * Create gallery item HTML
             */
            createGalleryItem(item, index) {
                return `
            <div class="gallery-item" data-index="${index}">
                <img src="${item.image}" alt="${item.title}" loading="lazy">
                <div class="gallery-item-overlay">
                    <h3 class="gallery-item-title">${this.escapeHtml(item.title)}</h3>
                    <p class="gallery-item-location">${this.escapeHtml(item.location)}</p>
                </div>
            </div>
        `;
            }

            /**
             * Update gallery load more button
             */
            updateGalleryButton() {
                const loadMoreBtn = document.getElementById('galleryLoadMore');
                if (!loadMoreBtn) return;

                const btnText = loadMoreBtn.querySelector('.btn-text');

                if (this.galleryPage * this.GALLERY_ITEMS_PER_PAGE >= this.galleryImages.length) {
                    if (this.galleryPage > 1) {
                        loadMoreBtn.classList.add('collapse');
                        btnText.textContent = 'Show less';
                    } else {
                        loadMoreBtn.style.display = 'none';
                    }
                } else {
                    loadMoreBtn.classList.remove('collapse');
                    loadMoreBtn.style.display = 'inline-flex';
                    btnText.textContent = 'Tải thêm ảnh';
                }
            }

            /**
             * Search rooms by guests (capacity)
             */
            async searchRooms() {
                try {
                    // Filter by available status
                    const result = await this.api.get('/rooms/filter/status?status=available');

                    if (result.success && result.data) {
                        let availableRoomIds = result.data.map(r => r.room_type_id);

                        // Get room types that match capacity
                        const rtResult = await this.api.get('/room-types');
                        if (rtResult.success && rtResult.data) {
                            let filteredRoomTypes = rtResult.data.filter(rt =>
                                availableRoomIds.includes(rt.id) && rt.capacity >= this.searchGuests
                            );

                            if (filteredRoomTypes.length > 0) {
                                this.allListings = this.mapRoomTypesToListings(filteredRoomTypes);
                            } else {
                                this.allListings = [];
                            }
                        }

                        this.listingsPage = 1;
                        this.renderListings();

                        // Scroll to results
                        setTimeout(() => {
                            document.querySelector('.listings-section')?.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                        }, 200);
                    }
                } catch (error) {
                    console.error('Error searching rooms:', error);
                    this.showEmptyState('listingsGrid', 'Không tìm thấy phòng phù hợp');
                }
            }

            /**
             * Setup event listeners
             */
            setupEventListeners() {
                // Listings load more
                const loadMoreBtn = document.getElementById('loadMoreBtn');
                if (loadMoreBtn) {
                    loadMoreBtn.addEventListener('click', () => {
                        if (this.isExpanded) {
                            this.listingsPage = 1;
                            this.renderListings();
                            setTimeout(() => {
                                document.querySelector('.listings-section')?.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'start'
                                });
                            }, 100);
                        } else {
                            this.listingsPage++;
                            this.renderListings();
                        }
                    });
                }

                // Gallery load more
                const galleryLoadMore = document.getElementById('galleryLoadMore');
                if (galleryLoadMore) {
                    galleryLoadMore.addEventListener('click', () => {
                        if (galleryLoadMore.classList.contains('collapse')) {
                            this.galleryPage = 1;
                            this.renderGallery();
                            document.querySelector('.gallery-section')?.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                        } else {
                            this.galleryPage++;
                            this.renderGallery();
                        }
                    });
                }

                // Search button
                const searchBtn = document.querySelector('.btn-search');
                if (searchBtn) {
                    searchBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        this.searchRooms();
                    });
                }

                // Lightbox
                this.setupLightbox();
                document.addEventListener('click', (e) => {
    const btn = e.target.closest('.view-deal-btn');
    if (btn) {
        const roomId = btn.dataset.roomId;
        window.location.href = `listing-detail.html?id=${roomId}`;
    }
});
            }

            /**
             * Setup lightbox functionality
             */
            setupLightbox() {
                // Open lightbox on gallery item click
                document.addEventListener('click', (e) => {
                    const galleryItem = e.target.closest('.gallery-item');
                    if (galleryItem) {
                        const index = parseInt(galleryItem.dataset.index);
                        this.openLightbox(index);
                    }
                });

                // Close button
                const lightboxClose = document.getElementById('lightboxClose');
                if (lightboxClose) {
                    lightboxClose.addEventListener('click', () => this.closeLightbox());
                }

                // Navigation buttons
                const lightboxNext = document.getElementById('lightboxNext');
                if (lightboxNext) {
                    lightboxNext.addEventListener('click', () => this.nextImage());
                }

                const lightboxPrev = document.getElementById('lightboxPrev');
                if (lightboxPrev) {
                    lightboxPrev.addEventListener('click', () => this.prevImage());
                }

                // Click overlay to close
                const lightboxOverlay = document.querySelector('.lightbox-overlay');
                if (lightboxOverlay) {
                    lightboxOverlay.addEventListener('click', () => this.closeLightbox());
                }

                // Keyboard navigation
                document.addEventListener('keydown', (e) => {
                    const lightbox = document.getElementById('lightbox');
                    if (!lightbox?.classList.contains('active')) return;

                    if (e.key === 'Escape') this.closeLightbox();
                    if (e.key === 'ArrowRight') this.nextImage();
                    if (e.key === 'ArrowLeft') this.prevImage();
                });
            }

            openLightbox(index) {
                this.currentLightboxIndex = index;
                this.updateLightboxImage();
                const lightbox = document.getElementById('lightbox');
                if (lightbox) {
                    lightbox.classList.add('active');
                    document.body.style.overflow = 'hidden';
                }
            }

            closeLightbox() {
                const lightbox = document.getElementById('lightbox');
                if (lightbox) {
                    lightbox.classList.remove('active');
                    document.body.style.overflow = '';
                }
            }

            updateLightboxImage() {
                const item = this.galleryImages[this.currentLightboxIndex];
                if (!item) return;

                const lightboxImage = document.getElementById('lightboxImage');
                const lightboxTitle = document.getElementById('lightboxTitle');
                const lightboxLocation = document.getElementById('lightboxLocation');
                const lightboxCounter = document.getElementById('lightboxCounter');

                if (lightboxImage) lightboxImage.src = item.image;
                if (lightboxTitle) lightboxTitle.textContent = item.title;
                if (lightboxLocation) lightboxLocation.textContent = item.location;
                if (lightboxCounter) {
                    lightboxCounter.textContent = `${this.currentLightboxIndex + 1} / ${this.galleryImages.length}`;
                }
            }

            nextImage() {
                this.currentLightboxIndex = (this.currentLightboxIndex + 1) % this.galleryImages.length;
                this.updateLightboxImage();
            }

            prevImage() {
                this.currentLightboxIndex = (this.currentLightboxIndex - 1 + this.galleryImages.length) % this.galleryImages.length;
                this.updateLightboxImage();
            }

            /**
             * Setup date picker
             */
            setupDatePicker() {
                let currentDate = new Date();
                let selectedStartDate = null;
                let selectedEndDate = null;

                const datepickerContainer = document.getElementById('datepickerContainer');
                const dateRangeInput = document.getElementById('dateRangeInput');
                const prevMonthBtn = document.getElementById('prevMonth');
                const nextMonthBtn = document.getElementById('nextMonth');

                if (!datepickerContainer || !dateRangeInput) return;

                const renderCalendar = () => {
                    const monthNames = ["January", "February", "March", "April", "May", "June",
                        "July", "August", "September", "October", "November", "December"];
                    const currentMonthEl = document.getElementById('currentMonth');
                    if (currentMonthEl) {
                        currentMonthEl.textContent = `${monthNames[currentDate.getMonth()]} ${currentDate.getFullYear()}`;
                    }

                    const daysContainer = document.getElementById('calendarDays');
                    if (!daysContainer) return;

                    daysContainer.innerHTML = '';

                    const firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
                    const lastDay = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
                    const prevLastDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 0);

                    const firstDayIndex = firstDay.getDay();
                    const lastDayDate = lastDay.getDate();
                    const prevLastDayDate = prevLastDay.getDate();

                    // Previous month days
                    for (let i = firstDayIndex; i > 0; i--) {
                        const day = document.createElement('div');
                        day.className = 'datepicker-day disabled';
                        day.textContent = prevLastDayDate - i + 1;
                        daysContainer.appendChild(day);
                    }

                    // Current month days
                    for (let i = 1; i <= lastDayDate; i++) {
                        const day = document.createElement('div');
                        day.className = 'datepicker-day';
                        day.textContent = i;

                        const currentDateObj = new Date(currentDate.getFullYear(), currentDate.getMonth(), i);

                        if (selectedStartDate && currentDateObj.getTime() === selectedStartDate.getTime()) {
                            day.classList.add('selected', 'range-start');
                        }
                        if (selectedEndDate && currentDateObj.getTime() === selectedEndDate.getTime()) {
                            day.classList.add('selected', 'range-end');
                        }
                        if (selectedStartDate && selectedEndDate &&
                            currentDateObj > selectedStartDate && currentDateObj < selectedEndDate) {
                            day.classList.add('in-range');
                        }

                        day.addEventListener('click', (e) => {
                            e.stopPropagation();
                            if (!selectedStartDate || (selectedStartDate && selectedEndDate)) {
                                selectedStartDate = currentDateObj;
                                selectedEndDate = null;
                                this.searchCheckin = currentDateObj;
                                this.searchCheckout = null;
                            } else if (currentDateObj > selectedStartDate) {
                                selectedEndDate = currentDateObj;
                                this.searchCheckout = currentDateObj;
                            } else {
                                selectedStartDate = currentDateObj;
                                selectedEndDate = null;
                                this.searchCheckin = currentDateObj;
                                this.searchCheckout = null;
                            }

                            updateDateDisplay();
                            renderCalendar();
                        });

                        daysContainer.appendChild(day);
                    }
                };

                const updateDateDisplay = () => {
                    const options = { month: 'short', day: 'numeric', year: 'numeric' };
                    const checkinText = selectedStartDate ?
                        selectedStartDate.toLocaleDateString('en-US', options) : 'Select date';
                    const checkoutText = selectedEndDate ?
                        selectedEndDate.toLocaleDateString('en-US', options) : 'Select date';

                    const checkinDateEl = document.getElementById('checkinDate');
                    const checkoutDateEl = document.getElementById('checkoutDate');

                    if (checkinDateEl) checkinDateEl.textContent = checkinText;
                    if (checkoutDateEl) checkoutDateEl.textContent = checkoutText;

                    if (selectedStartDate && selectedEndDate) {
                        dateRangeInput.value = `${checkinText} - ${checkoutText}`;
                    } else if (selectedStartDate) {
                        dateRangeInput.value = checkinText;
                    } else {
                        dateRangeInput.value = 'Add dates';
                    }
                };

                if (prevMonthBtn) {
                    prevMonthBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        currentDate.setMonth(currentDate.getMonth() - 1);
                        renderCalendar();
                    });
                }

                if (nextMonthBtn) {
                    nextMonthBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        currentDate.setMonth(currentDate.getMonth() + 1);
                        renderCalendar();
                    });
                }

                if (dateRangeInput) {
                    dateRangeInput.addEventListener('click', (e) => {
                        e.stopPropagation();
                        datepickerContainer.classList.toggle('active');
                    });
                }

                renderCalendar();
            }

            /**
             * Setup guests selector
             */
            setupGuestsSelector() {
                let adults = 1, children = 0;
                const guestsField = document.getElementById('guestsField');
                const guestsDropdown = document.getElementById('guestsDropdown');
                const guestsInput = document.getElementById('guestsInput');

                if (!guestsField || !guestsInput) return;

                const updateGuests = () => {
                    const total = adults + children;
                    this.searchGuests = total;
                    guestsInput.value = `${total} guest${total > 1 ? 's' : ''}` +
                        (children > 0 ? ` (${adults} adult${adults > 1 ? 's' : ''}, ${children} child${children > 1 ? 'ren' : ''})` : '');

                    const adultsCountEl = document.getElementById('adultsCount');
                    const childrenCountEl = document.getElementById('childrenCount');
                    const adultsDecrementEl = document.getElementById('adultsDecrement');
                    const childrenDecrementEl = document.getElementById('childrenDecrement');

                    if (adultsCountEl) adultsCountEl.textContent = adults;
                    if (childrenCountEl) childrenCountEl.textContent = children;
                    if (adultsDecrementEl) adultsDecrementEl.disabled = adults === 1;
                    if (childrenDecrementEl) childrenDecrementEl.disabled = children === 0;
                };

                document.getElementById('adultsDecrement')?.addEventListener('click', (e) => {
                    e.stopPropagation();
                    if (adults > 1) {
                        adults--;
                        updateGuests();
                    }
                });

                document.getElementById('adultsIncrement')?.addEventListener('click', (e) => {
                    e.stopPropagation();
                    adults++;
                    updateGuests();
                });

                document.getElementById('childrenDecrement')?.addEventListener('click', (e) => {
                    e.stopPropagation();
                    if (children > 0) {
                        children--;
                        updateGuests();
                    }
                });

                document.getElementById('childrenIncrement')?.addEventListener('click', (e) => {
                    e.stopPropagation();
                    children++;
                    updateGuests();
                });

                guestsField.addEventListener('click', (e) => {
                    e.stopPropagation();
                    if (guestsDropdown) {
                        guestsDropdown.classList.toggle('active');
                    }
                });
            }

            /**
             * Show empty state message
             */
            showEmptyState(elementId, message) {
                const element = document.getElementById(elementId);
                if (element) {
                    element.innerHTML = `
                <div style="grid-column: 1/-1; text-align: center; padding: 60px 20px; color: #999;">
                    <p style="font-size: 18px; margin: 0;">${this.escapeHtml(message)}</p>
                </div>
            `;
                }
            }

            /**
             * Escape HTML to prevent XSS
             */
            escapeHtml(text) {
                if (!text) return '';
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }
        }

        // Initialize when DOM is ready
        $(document).ready(function () {
            const manager = new VacationRenterManager('http://localhost:8000/api');
            window.vacationRenterManager = manager;
        });
