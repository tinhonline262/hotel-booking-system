<?php 
    /* * $data được truyền từ Controller:
     * $data['hotelName']
     * $data['featuredRooms']
     */
    
    // Tải layout header (header sẽ tự động tải home.css)
    include __DIR__ . '/../layouts/header.php'; 
?>

<section class="hero-section">
    <div class="hero-content">
        <h1><?php echo htmlspecialchars($data['hotelName']); ?></h1>
        <p>Trải nghiệm kỳ nghỉ sang trọng và thoải mái bậc nhất.</p>
    </div>

    <div class="search-form-container">
        <form action="/search" method="GET" class="search-form" id="main-search-form">
            <div class="form-group">
                <label for="check-in">Check-in</label>
                <input type="date" id="check-in" name="check_in" required>
            </div>
            <div class="form-group">
                <label for="check-out">Check-out</label>
                <input type="date" id="check-out" name="check_out" required>
            </div>
            <div class="form-group">
                <label for="guests">Số khách</label>
                <input type="number" id="guests" name="guests" min="1" value="1" required>
            </div>
            <button type="submit" class="search-btn" id="search-submit-btn">Tìm kiếm</button>
        </form>
    </div>
</section>

<section class="featured-rooms">
    <h2>Phòng Nổi Bật</h2>
    <div class="room-grid">
        
        <?php foreach ($data['featuredRooms'] as $room): ?>
            <div class="room-card">
                <img 
                    src="<?php echo htmlspecialchars($room['primary_image_url'] ?? '/images/default-room.jpg'); ?>" 
                    alt="<?php echo htmlspecialchars($room['name']); ?>" 
                    class="room-card-image"
                    loading="lazy"> <div class="room-card-content">
                    <h3><?php echo htmlspecialchars($room['name']); ?></h3>
                    <div class="room-card-price">
                        <?php echo number_format($room['price_per_night']); ?> VND / đêm
                    </div>
                    <a href="/room-type/<?php echo $room['id']; ?>" class="room-card-details-btn">
                        Xem chi tiết
                    </a>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (empty($data['featuredRooms'])): ?>
            <p>Không tìm thấy phòng nổi bật nào.</p>
        <?php endif; ?>

    </div>
</section>

<section class="about-section">
    <h2>Về chúng tôi</h2>
    <p>Chào mừng bạn đến với <?php echo htmlspecialchars($data['hotelName']); ?>, nơi sự sang trọng hòa quyện cùng sự thoải mái. Khách sạn của chúng tôi... (Mô tả ngắn gọn)</p>
</section>

<footer class="main-footer">
    <p>Thông tin liên hệ: 123 Đường ABC, Quận 1, TP. HCM | Email: contact@geminihotel.com</p>
</footer>

<script>
    const searchForm = document.getElementById('main-search-form');
    const searchBtn = document.getElementById('search-submit-btn');

    if (searchForm) {
        searchForm.addEventListener('submit', function() {
            searchBtn.classList.add('is-loading');
            searchBtn.disabled = true;
            searchBtn.textContent = 'Đang tìm...';
        });
    }
</script>

<?php 
    // Tải layout footer
    include __DIR__ . '/../layouts/footer.php'; 
?>