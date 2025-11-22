<?php 
    include __DIR__ . '/../layouts/header.php'; 
    
    // Hàm helper để map trạng thái sang class CSS và Label tiếng Việt
    function getStatusInfo($status) {
        return match($status) {
            'pending' => ['class' => 'badge-pending', 'label' => 'Chờ xác nhận'],
            'confirmed' => ['class' => 'badge-confirmed', 'label' => 'Đã xác nhận'],
            'checked_in' => ['class' => 'badge-checked_in', 'label' => 'Đã Check-in'],
            'checked_out' => ['class' => 'badge-checked_out', 'label' => 'Đã Check-out'],
            'cancelled' => ['class' => 'badge-cancelled', 'label' => 'Đã hủy'],
            default => ['class' => 'badge-secondary', 'label' => $status]
        };
    }
?>

<link rel="stylesheet" href="/css/booking-lookup.css">

<div class="lookup-container">
    <div class="search-box">
        <h1 style="margin-bottom: 10px;">Tra Cứu Đặt Phòng</h1>
        <p style="color: #666;">Nhập mã booking (VD: BK-xxxx) để kiểm tra trạng thái</p>
        
        <form action="/booking-lookup/search" method="GET" class="search-form">
            <input type="text" name="code" class="search-input" placeholder="Nhập mã booking..." value="<?php echo htmlspecialchars($searchCode ?? ''); ?>" required>
            <button type="submit" class="search-submit">Tìm kiếm</button>
        </form>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert-error">
            ⚠️ <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($booking)): 
        $statusInfo = getStatusInfo($booking['status']);
    ?>
        <div class="result-card">
            <div class="result-header">
                <div class="booking-code">#<?php echo htmlspecialchars($booking['booking_code']); ?></div>
                <span class="badge <?php echo $statusInfo['class']; ?>">
                    <?php echo $statusInfo['label']; ?>
                </span>
            </div>
            
            <div class="result-body">
                <div class="info-column">
                    <div class="info-group">
                        <h4>Thông tin khách hàng</h4>
                        <div class="info-row"><span class="info-label">Họ tên:</span> <span class="info-value"><?php echo htmlspecialchars($booking['customer_name']); ?></span></div>
                        <div class="info-row"><span class="info-label">Email:</span> <span class="info-value"><?php echo htmlspecialchars($booking['customer_email']); ?></span></div>
                        <div class="info-row"><span class="info-label">SĐT:</span> <span class="info-value"><?php echo htmlspecialchars($booking['customer_phone']); ?></span></div>
                    </div>

                    <div class="info-group">
                        <h4>Chi tiết phòng</h4>
                        <div class="info-row"><span class="info-label">Loại phòng:</span> <span class="info-value"><?php echo htmlspecialchars($booking['room_type_name']); ?></span></div>
                        <div class="info-row"><span class="info-label">Số phòng:</span> <span class="info-value"><strong><?php echo htmlspecialchars($booking['room_number']); ?></strong></span></div>
                    </div>
                </div>

                <div class="info-column">
                    <div class="info-group">
                        <h4>Thời gian</h4>
                        <div class="info-row"><span class="info-label">Check-in:</span> <span class="info-value"><?php echo date('d/m/Y', strtotime($booking['check_in_date'])); ?></span></div>
                        <div class="info-row"><span class="info-label">Check-out:</span> <span class="info-value"><?php echo date('d/m/Y', strtotime($booking['check_out_date'])); ?></span></div>
                        <div class="info-row"><span class="info-label">Ngày tạo:</span> <span class="info-value"><?php echo date('d/m/Y H:i', strtotime($booking['created_at'])); ?></span></div>
                    </div>
                    
                    <div class="info-group" style="margin-top: 30px;">
                        <div class="total-price">
                            <?php echo number_format($booking['total_price']); ?> VND
                        </div>
                        <div style="text-align: right; font-size: 0.9rem; color: #666;">Tổng thanh toán</div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>