/**
 * Room Details Manager
 * Handles expandable room details with image management
 */
class RoomDetailsManager {
    constructor(apiBaseUrl) {
        this.api = new ApiService(apiBaseUrl);
        this.currentRoomId = null;
        this.currentImages = [];
        this.draggedElement = null;
        this.init();
    }

    init() {
        this.setupImageUploadModal();
        this.setupDragAndDrop();
    }

    setupImageUploadModal() {
        // File input change event
        $('#imageFiles').on('change', (e) => this.handleFileSelect(e));

        // Upload form submit
        $('#uploadImagesForm').on('submit', (e) => this.handleImageUpload(e));

        // Drag and drop for file upload area
        const $uploadArea = $('#fileUploadArea');

        $uploadArea.on('dragover', (e) => {
            e.preventDefault();
            e.stopPropagation();
            $uploadArea.addClass('drag-over');
        });

        $uploadArea.on('dragleave', (e) => {
            e.preventDefault();
            e.stopPropagation();
            $uploadArea.removeClass('drag-over');
        });

        $uploadArea.on('drop', (e) => {
            e.preventDefault();
            e.stopPropagation();
            $uploadArea.removeClass('drag-over');

            const files = e.originalEvent.dataTransfer.files;
            $('#imageFiles')[0].files = files;
            this.handleFileSelect({ target: { files } });
        });
    }

    handleFileSelect(e) {
        const files = e.target.files;
        const $preview = $('#filePreview');
        $preview.empty();

        if (files.length > 0) {
            $('.file-upload-placeholder').hide();
            $preview.show();

            Array.from(files).forEach((file, index) => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        $preview.append(`
                            <div class="file-preview-item">
                                <img src="${e.target.result}" alt="Preview">
                                <span class="file-preview-name">${this.escapeHtml(file.name)}</span>
                                <button type="button" class="file-preview-remove" data-index="${index}">×</button>
                            </div>
                        `);
                    };
                    reader.readAsDataURL(file);
                }
            });
        } else {
            $('.file-upload-placeholder').show();
            $preview.hide();
        }

        // Remove file from preview
        $(document).off('click', '.file-preview-remove').on('click', '.file-preview-remove', (e) => {
            const index = $(e.currentTarget).data('index');
            this.removeFileFromInput(index);
        });
    }

    removeFileFromInput(indexToRemove) {
        const $input = $('#imageFiles')[0];
        const dt = new DataTransfer();

        Array.from($input.files).forEach((file, index) => {
            if (index !== indexToRemove) {
                dt.items.add(file);
            }
        });

        $input.files = dt.files;
        this.handleFileSelect({ target: { files: dt.files } });
    }

    async handleImageUpload(e) {
        e.preventDefault();

        const roomId = $('#upload_room_id').val();
        const files = $('#imageFiles')[0].files;
        const storageType = $('#storage_type').val();

        console.log('=== Upload Debug ===');
        console.log('Room ID:', roomId);
        console.log('Files object:', files);
        console.log('Files length:', files.length);
        console.log('Storage type:', storageType);

        if (!files || files.length === 0) {
            this.showNotification('Vui lòng chọn ít nhất một hình ảnh', 'warning');
            return;
        }

        const formData = new FormData();
        Array.from(files).forEach((file, index) => {
            console.log(`Adding file ${index}:`, file.name, file.size, file.type);
            formData.append('images[]', file);
        });
        formData.append('storage_type', storageType);

        console.log('FormData entries:');
        for (let pair of formData.entries()) {
            console.log(pair[0], pair[1]);
        }

        try {
            const result = await this.api.upload(`/rooms/${roomId}/images`, formData);

            if (result.success) {
                this.showNotification(result.message || 'Tải lên hình ảnh thành công!', 'success');
                $('#uploadImagesForm')[0].reset();
                $('.file-upload-placeholder').show();
                $('#filePreview').hide().empty();

                // Reload images
                await this.loadRoomImages(roomId);
            } else {
                this.showNotification(result.message || 'Tải lên thất bại!', 'error');
                console.error('Upload failed:', result);
            }
        } catch (error) {
            this.showNotification('Lỗi: ' + error.message, 'error');
            console.error('Upload error:', error);
        }
    }

    async openImageManager(roomId) {
        this.currentRoomId = roomId;
        $('#upload_room_id').val(roomId);

        await this.loadRoomImages(roomId);

        $('#upload_images').fadeIn(200);
        $('body').css('overflow', 'hidden');
    }

    async loadRoomImages(roomId) {
        try {
            const result = await this.api.get(`/rooms/${roomId}/details`);

            if (result.success && result.data) {
                this.currentImages = result.data.images || [];
                this.renderImagesGrid();
            }
        } catch (error) {
            console.error('Error loading images:', error);
            this.currentImages = [];
            this.renderImagesGrid();
        }
    }

    renderImagesGrid() {
        const $grid = $('#imagesGrid');

        if (this.currentImages.length === 0) {
            $grid.html('<p class="no-images">Chưa có hình ảnh nào</p>');
            return;
        }

        const imagesHtml = this.currentImages
            .sort((a, b) => a.displayOrder - b.displayOrder)
            .map(img => `
                <div class="image-card" data-image-id="${img.imageId}" data-order="${img.displayOrder}" draggable="true">
                    <div class="image-card__header">
                        ${img.isPrimary ? '<span class="image-card__badge">Chính</span>' : ''}
                        <button class="image-card__delete" data-id="${img.imageId}" title="Xóa">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 6h18"></path>
                                <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="image-card__image">
                        <img src="${this.escapeHtml(img.imageUrl)}" alt="Room image">
                    </div>
                    <div class="image-card__footer">
                        <div class="image-card__info">
                            <span class="image-card__size">${this.formatFileSize(img.fileSize)}</span>
                            <span class="image-card__type">${img.storageType}</span>
                        </div>
                        ${!img.isPrimary ? `<button class="btn btn--sm btn--primary" data-action="set-primary" data-id="${img.imageId}">Đặt làm chính</button>` : ''}
                    </div>
                    <div class="image-card__drag-handle">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="9" cy="5" r="1"></circle>
                            <circle cx="9" cy="12" r="1"></circle>
                            <circle cx="9" cy="19" r="1"></circle>
                            <circle cx="15" cy="5" r="1"></circle>
                            <circle cx="15" cy="12" r="1"></circle>
                            <circle cx="15" cy="19" r="1"></circle>
                        </svg>
                    </div>
                </div>
            `).join('');

        $grid.html(imagesHtml);
        this.attachImageActions();
    }

    attachImageActions() {
        // Set primary image
        $('[data-action="set-primary"]').off('click').on('click', async (e) => {
            const imageId = $(e.currentTarget).data('id');
            await this.setPrimaryImage(imageId);
        });

        // Delete image
        $('.image-card__delete').off('click').on('click', async (e) => {
            const imageId = $(e.currentTarget).data('id');
            if (confirm('Bạn có chắc chắn muốn xóa hình ảnh này?')) {
                await this.deleteImage(imageId);
            }
        });
    }

    setupDragAndDrop() {
        $(document).on('dragstart', '.image-card', (e) => {
            this.draggedElement = e.currentTarget;
            $(e.currentTarget).addClass('dragging');
        });

        $(document).on('dragend', '.image-card', (e) => {
            $(e.currentTarget).removeClass('dragging');
        });

        $(document).on('dragover', '.image-card', (e) => {
            e.preventDefault();
            const afterElement = this.getDragAfterElement($('#imagesGrid')[0], e.clientY);
            const dragging = $('.dragging')[0];

            if (afterElement == null) {
                $('#imagesGrid').append(dragging);
            } else {
                $('#imagesGrid')[0].insertBefore(dragging, afterElement);
            }
        });

        $(document).on('drop', '.image-card', async (e) => {
            e.preventDefault();
            await this.updateImageOrder();
        });
    }

    getDragAfterElement(container, y) {
        const draggableElements = [...container.querySelectorAll('.image-card:not(.dragging)')];

        return draggableElements.reduce((closest, child) => {
            const box = child.getBoundingClientRect();
            const offset = y - box.top - box.height / 2;

            if (offset < 0 && offset > closest.offset) {
                return { offset: offset, element: child };
            } else {
                return closest;
            }
        }, { offset: Number.NEGATIVE_INFINITY }).element;
    }

    async updateImageOrder() {
        const orders = [];
        $('.image-card').each((index, el) => {
            const imageId = $(el).data('image-id');
            orders.push({
                imageId: parseInt(imageId),
                displayOrder: index
            });
        });

        try {
            const result = await this.api.put('/rooms/images/order', { orders });

            if (result.success) {
                this.showNotification('Đã cập nhật thứ tự hiển thị', 'success');
                await this.loadRoomImages(this.currentRoomId);
            }
        } catch (error) {
            this.showNotification('Lỗi cập nhật thứ tự: ' + error.message, 'error');
        }
    }

    async setPrimaryImage(imageId) {
        try {
            const result = await this.api.put(`/rooms/${this.currentRoomId}/images/${imageId}/primary`, {});

            if (result.success) {
                this.showNotification('Đã đặt làm hình ảnh chính', 'success');
                await this.loadRoomImages(this.currentRoomId);
            } else {
                this.showNotification(result.message || 'Không thể đặt làm hình ảnh chính', 'error');
            }
        } catch (error) {
            this.showNotification('Lỗi: ' + error.message, 'error');
        }
    }

    async deleteImage(imageId) {
        try {
            const result = await this.api.delete(`/rooms/images/${imageId}`);

            if (result.success) {
                this.showNotification('Đã xóa hình ảnh', 'success');
                await this.loadRoomImages(this.currentRoomId);
            } else {
                this.showNotification(result.message || 'Không thể xóa hình ảnh', 'error');
            }
        } catch (error) {
            this.showNotification('Lỗi: ' + error.message, 'error');
        }
    }

    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }

    escapeHtml(text) {
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return String(text).replace(/[&<>"']/g, m => map[m]);
    }

    showNotification(message, type = 'info') {
        // Reuse notification system from main page
        if (window.roomPageManager && window.roomPageManager.showNotification) {
            window.roomPageManager.showNotification(message, type);
        }
    }
}
