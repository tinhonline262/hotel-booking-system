
const api = new ApiService(''); 

const statusConfig = {
    'pending':      { label: 'Chờ xác nhận', class: 'badge-pending', icon: 'fa-clock' },
    'confirmed':    { label: 'Đã xác nhận',  class: 'badge-confirmed', icon: 'fa-check-circle' },
    'checked_in':   { label: 'Đã Check-in',  class: 'badge-checked_in', icon: 'fa-luggage-cart' },
    'checked_out':  { label: 'Đã Check-out', class: 'badge-checked_out', icon: 'fa-door-open' },
    'cancelled':    { label: 'Đã hủy',       class: 'badge-cancelled', icon: 'fa-times-circle' }
};

// Các hàm Helper Format
const formatMoney = (amount) => {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
};

const formatDate = (dateString) => {
    if(!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('vi-VN'); // Định dạng ngày/tháng/năm
}

const formatDateTime = (dateString) => {
    if(!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleString('vi-VN', { hour: '2-digit', minute: '2-digit', day: '2-digit', month: '2-digit', year: 'numeric' });
}

//  Logic chính khi trang tải xong
document.addEventListener('DOMContentLoaded', () => {
    const btnSearch = document.getElementById('btnSearch');
    const inputCode = document.getElementById('inputCode');
    const errorBox = document.getElementById('errorBox');
    const resultArea = document.getElementById('resultArea');

    const handleSearch = async () => {
        const code = inputCode.value.trim().toUpperCase();

        // Reset giao diện: Ẩn lỗi và kết quả cũ
        errorBox.style.display = 'none';
        resultArea.style.display = 'none';

        // Validate đầu vào
        if (!code) {
            errorBox.innerHTML = `<i class="fa-solid fa-circle-exclamation"></i> Vui lòng nhập mã đặt phòng!`;
            errorBox.style.display = 'block';
            inputCode.focus();
            return;
        }

        // Hiển thị trạng thái Loading
        btnSearch.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang tìm...';
        btnSearch.disabled = true;

        try {
            // === GỌI API (GET) ===
            const response = await api.get('/api/booking-lookup', { code: code });
            
            // Lấy dữ liệu booking từ phản hồi API
            const booking = response.data;
            
            // Lấy thông tin badge (màu sắc, icon) dựa trên status
            // Nếu status lạ, dùng mặc định màu xám
            const statusInfo = statusConfig[booking.status] || { label: booking.status, class: 'badge-secondary', icon: 'fa-info-circle' };

            // Render HTML kết quả (Khớp với CSS booking-lookup.css)
            resultArea.innerHTML = `
                <div class="result-header">
                    <div>
                        <div style="font-size: 0.9rem; opacity: 0.9; margin-bottom: 2px;">Mã đặt phòng</div>
                        <div class="booking-code">#${booking.booking_code}</div>
                    </div>
                    <span class="badge ${statusInfo.class}">
                        <i class="fa-solid ${statusInfo.icon}"></i> ${statusInfo.label}
                    </span>
                </div>

                <div class="result-body">
                    <div class="info-column">
                        <div class="info-group">
                            <h4><i class="fa-solid fa-user-check"></i> Thông tin khách hàng</h4>
                            <div class="info-row">
                                <span class="info-label">Họ tên:</span> 
                                <span class="info-value">${booking.customer_name}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Email:</span> 
                                <span class="info-value">${booking.customer_email}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">SĐT:</span> 
                                <span class="info-value">${booking.customer_phone}</span>
                            </div>
                        </div>

                        <div class="info-group" style="margin-top: 25px;">
                            <h4><i class="fa-solid fa-hotel"></i> Chi tiết phòng</h4>
                            <div class="info-row">
                                <span class="info-label">Loại phòng:</span> 
                                <span class="info-value" style="color: #007bff; font-weight: 600;">
                                    ${booking.room_type_name}
                                </span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Số phòng:</span> 
                                <span class="info-value"><strong>${booking.room_number}</strong></span>
                            </div>
                        </div>
                    </div>

                    <div class="info-column">
                        <div class="info-group">
                            <h4><i class="fa-regular fa-calendar-check"></i> Thời gian</h4>
                            <div class="info-row">
                                <span class="info-label">Check-in:</span> 
                                <span class="info-value">${formatDate(booking.check_in_date)}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Check-out:</span> 
                                <span class="info-value">${formatDate(booking.check_out_date)}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Ngày tạo:</span> 
                                <span class="info-value" style="font-style: italic;">${formatDateTime(booking.created_at)}</span>
                            </div>
                        </div>
                        
                        <div class="info-group" style="margin-top: 30px; text-align: right;">
                            <div style="font-size: 0.9rem; color: #666; margin-bottom: 5px;">Tổng thanh toán</div>
                            <div class="total-price">
                                ${formatMoney(booking.total_price)}
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Hiển thị vùng kết quả (Slide Down animation sẽ tự chạy nhờ CSS)
            resultArea.style.display = 'block';

        } catch (error) {
            console.error("Search Error:", error);
            
            let msg = "Có lỗi xảy ra, vui lòng thử lại sau.";
            
            // Xử lý thông báo lỗi thân thiện
            if (error.message && (error.message.includes("Not Found") || error.message.includes("404"))) {
                msg = `Không tìm thấy đơn đặt phòng với mã <b>${code}</b>.`;
            } else if (error.message) {
                msg = error.message;
            }

            errorBox.innerHTML = `<i class="fa-solid fa-triangle-exclamation"></i> ${msg}`;
            errorBox.style.display = 'block';
        } finally {
            // Reset nút tìm kiếm về trạng thái ban đầu
            btnSearch.innerHTML = '<i class="fa-solid fa-magnifying-glass"></i> Tra cứu';
            btnSearch.disabled = false;
        }
    };

    // Bắt sự kiện Click nút
    btnSearch.addEventListener('click', handleSearch);

    // Bắt sự kiện Nhấn Enter trong ô input
    inputCode.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') handleSearch();
    });
});