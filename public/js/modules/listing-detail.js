document.addEventListener('DOMContentLoaded', function(){
    const params = new URLSearchParams(window.location.search);
    const roomId = params.get('id');
    const apiRoomUrl = `/api/rooms/${roomId}/details`;
    const apiCheckUrl = (id, checkIn, checkOut) => `/api/booking/rooms/${id}?checkInDate=${encodeURIComponent(checkIn)}&checkOutDate=${encodeURIComponent(checkOut)}`;
    const apiBookingUrl = (id) => `/api/booking/rooms/${id}`;

    // Elements
    const hero = document.getElementById('hero-image');
    const thumbs = document.getElementById('thumbs');
    const roomTitle = document.getElementById('room-title');
    const pricePerNightEl = document.getElementById('price-per-night');
    const roomTypeEl = document.getElementById('room-type');
    const capacityEl = document.getElementById('capacity');
    const descEl = document.getElementById('description-text');
    const amenitiesList = document.getElementById('amenities-list');

    const checkInEl = document.getElementById('check-in');
    const checkOutEl = document.getElementById('check-out');
    const checkBtn = document.getElementById('check-availability');
    const availabilityResult = document.getElementById('availability-result');

    const bookingForm = document.getElementById('booking-form');
    const roomIdInput = document.getElementById('room-id');
    const bookingCodeInput = document.getElementById('booking-code');
    const custName = document.getElementById('customer_name');
    const custEmail = document.getElementById('customer_email');
    const custPhone = document.getElementById('customer_phone');
    const numGuests = document.getElementById('num_guests');
    const specialRequests = document.getElementById('special_requests');
    const nightsEl = document.getElementById('nights');
    const totalPriceEl = document.getElementById('total-price');
    const submitBtn = document.getElementById('submit-booking');
    const bookingFeedback = document.getElementById('booking-feedback');

    let roomData = null;

    function viMoney(x){ return Number(x).toLocaleString('vi-VN') + ' đ'; }

    if (!roomId) {
        hero.innerHTML = '<div style="padding:18px;color:#b91c1c">Không có ID phòng trong URL</div>';
        return;
    }

    // Helper: local YYYY-MM-DD (avoid timezone issues)
    function toLocalISO(date){
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    }
    function nextDayISOFromISO(iso){
        const parts = iso.split('-').map(Number);
        const dt = new Date(parts[0], parts[1]-1, parts[2]);
        dt.setDate(dt.getDate() + 1);
        return toLocalISO(dt);
    }
    function validDates(d1, d2){
        if(!d1 || !d2) return false;
        return new Date(d1) < new Date(d2); // strict: checkout must be greater than checkin
    }
    function nightsBetween(d1, d2){
        const a = new Date(d1); const b = new Date(d2);
        return Math.ceil((b - a) / (1000*60*60*24));
    }

    // Booking code generation (PHP equivalent: BK-{roomId}-{YmdHis}-{uniqid})
    function pad(n, width = 2){
        return String(n).padStart(width, '0');
    }
    function ymdHisFromDate(d){
        return d.getFullYear().toString()
            + pad(d.getMonth() + 1)
            + pad(d.getDate())
            + pad(d.getHours())
            + pad(d.getMinutes())
            + pad(d.getSeconds());
    }
    function generateBookingCode(id){
        const random10 = Math.random().toString(16).slice(2, 12);
        return `BK-${id}-${random10}`;
    }

    // Modal success popup
    function showSuccessModal(code) {
        // Remove existing modal if any
        const existing = document.getElementById('booking-success-modal');
        if (existing) existing.remove();

        // backdrop
        const backdrop = document.createElement('div');
        backdrop.id = 'booking-success-modal';
        backdrop.style.position = 'fixed';
        backdrop.style.left = '0';
        backdrop.style.top = '0';
        backdrop.style.right = '0';
        backdrop.style.bottom = '0';
        backdrop.style.background = 'rgba(0,0,0,0.5)';
        backdrop.style.display = 'flex';
        backdrop.style.alignItems = 'center';
        backdrop.style.justifyContent = 'center';
        backdrop.style.zIndex = 10000;

        // modal box
        const box = document.createElement('div');
        box.style.width = 'min(520px, 92%)';
        box.style.background = '#fff';
        box.style.borderRadius = '12px';
        box.style.padding = '20px';
        box.style.boxShadow = '0 12px 40px rgba(2,6,23,0.3)';
        box.style.color = '#111';
        box.style.textAlign = 'left';
        box.setAttribute('role', 'dialog');
        box.setAttribute('aria-modal', 'true');

        // content
        box.innerHTML = `
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                <div style="font-size:1.1rem;font-weight:700;color:#059669">Đặt phòng thành công</div>
                <button id="booking-success-close" aria-label="Đóng" style="background:transparent;border:0;font-size:18px;cursor:pointer;">✕</button>
            </div>
            <div style="margin-top:6px;color:#374151;">Cảm ơn bạn! Đơn đặt phòng đã được ghi nhận.</div>
            <div style="margin-top:12px;padding:12px;background:#f1f9f3;border-radius:8px;">
                <div style="font-size:0.95rem;color:#333">Mã booking:</div>
                <div id="booking-success-code" style="margin-top:6px;font-weight:700;font-size:1.05rem;color:#0b5132;">${code}</div>
                <div style="margin-top:8px;">
                    <button id="copy-booking-code" style="margin-right:8px;padding:8px 10px;border-radius:8px;border:0;background:#0b5132;color:#fff;cursor:pointer;">Sao chép mã</button>
                    <a id="view-bookings-link" href="/homepage.html" style="color:#0b5132;text-decoration:underline;">Quay về danh sách phòng</a>
                </div>
            </div>
            <div style="margin-top:14px;text-align:right;">
                <button id="booking-success-ok" style="padding:8px 12px;border-radius:8px;border:0;background:#059669;color:#fff;cursor:pointer;">Đóng</button>
            </div>
        `;

        backdrop.appendChild(box);
        document.body.appendChild(backdrop);

        // focus management
        const closeBtn = document.getElementById('booking-success-close');
        const okBtn = document.getElementById('booking-success-ok');
        const copyBtn = document.getElementById('copy-booking-code');

        // copy booking code to clipboard
        copyBtn.addEventListener('click', function(){
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(code).then(() => {
                    copyBtn.textContent = 'Đã sao chép';
                    setTimeout(()=> copyBtn.textContent = 'Sao chép mã', 2000);
                }).catch(()=> {
                    copyBtn.textContent = 'Không thể sao chép';
                });
            } else {
                // fallback
                const ta = document.createElement('textarea');
                ta.value = code;
                document.body.appendChild(ta);
                ta.select();
                try { document.execCommand('copy'); copyBtn.textContent = 'Đã sao chép'; }
                catch(e){ copyBtn.textContent = 'Không thể sao chép'; }
                ta.remove();
                setTimeout(()=> copyBtn.textContent = 'Sao chép mã', 2000);
            }
        });

        function closeModal(){
            backdrop.remove();
        }

        closeBtn.addEventListener('click', closeModal);
        okBtn.addEventListener('click', closeModal);

        // close on backdrop click (outside box)
        backdrop.addEventListener('click', function(e){
            if (e.target === backdrop) closeModal();
        });

        // close on Escape
        function escHandler(e){
            if (e.key === 'Escape') closeModal();
        }
        document.addEventListener('keydown', escHandler);

        // cleanup listener on remove
        backdrop.addEventListener('remove', function(){
            document.removeEventListener('keydown', escHandler);
        });

        // focus first actionable button
        okBtn.focus();
    }

    // Set initial min attributes: check-in = today, check-out = tomorrow
    const todayISO = toLocalISO(new Date());
    const tomorrowISO = nextDayISOFromISO(todayISO);
    checkInEl.setAttribute('min', todayISO);
    checkOutEl.setAttribute('min', tomorrowISO);

    // When user changes check-in, update check-out min to the next day of check-in
    checkInEl.addEventListener('change', function(){
        bookingFeedback.textContent = '';
        availabilityResult.textContent = '';
        bookingForm.style.display = 'none';
        submitBtn.disabled = true;

        const ci = checkInEl.value;
        if (!ci) {
            checkOutEl.setAttribute('min', tomorrowISO);
            return;
        }
        const minCo = nextDayISOFromISO(ci);
        checkOutEl.setAttribute('min', minCo);

        // If current check-out is invalid (<= check-in), clear it
        if (checkOutEl.value && !(new Date(checkOutEl.value) > new Date(ci))) {
            checkOutEl.value = '';
        }
    });

    // When user changes check-out, validate immediately
    checkOutEl.addEventListener('change', function(){
        bookingFeedback.textContent = '';
        availabilityResult.textContent = '';
        bookingForm.style.display = 'none';
        submitBtn.disabled = true;

        const ci = checkInEl.value;
        const co = checkOutEl.value;
        if (ci && co && !validDates(ci, co)) {
            availabilityResult.className = 'availability-result unavailable';
            availabilityResult.textContent = 'Check-out phải lớn hơn check-in. Vui lòng chọn ngày khác.';
        } else {
            availabilityResult.textContent = '';
        }
    });

    // Fetch room details
    fetch(apiRoomUrl)
        .then(r => r.json())
        .then(res => {
            const payload = res.data || res;
            roomData = payload;
            renderRoom(payload);
        })
        .catch(err => {
            hero.innerHTML = '<div style="padding:18px;color:#b91c1c">Không thể lấy thông tin phòng</div>';
            console.error(err);
        });

    function renderRoom(room){
        // images
        const imgs = Array.isArray(room.images) ? room.images : [];
        let mainUrl = (imgs.find(i=>i.isPrimary) || imgs[0] || {}).imageUrl || '';
        if (!mainUrl) {
            hero.innerHTML = '<img src="https://dummyimage.com/900x600/ddd/999&text=No+Image" alt="no image">';
        } else {
            hero.innerHTML = `<img src="${mainUrl}" alt="room image">`;
        }

        thumbs.innerHTML = '';
        imgs.forEach((im, idx) => {
            const t = document.createElement('img');
            t.src = im.imageUrl;
            if (im.imageUrl === mainUrl) t.classList.add('selected');
            t.addEventListener('click', () => {
                hero.querySelector('img').src = im.imageUrl;
                thumbs.querySelectorAll('img').forEach(el => el.classList.remove('selected'));
                t.classList.add('selected');
            });
            thumbs.appendChild(t);
        });

        roomTitle.textContent = `Phòng ${room.roomNumber || room.roomId || ''}`;
        pricePerNightEl.textContent = room.pricePerNight ? viMoney(room.pricePerNight) : '-';
        roomTypeEl.textContent = room.roomType || '-';
        capacityEl.textContent = (room.capacity || '-');
        descEl.textContent = room.roomTypeDescription || room.description || '-';

        amenitiesList.innerHTML = '';
        const ams = Array.isArray(room.amenities) ? room.amenities : [];
        if (ams.length) {
            ams.forEach(a => {
                const li = document.createElement('li');
                li.textContent = a;
                amenitiesList.appendChild(li);
            });
        } else {
            amenitiesList.innerHTML = '<li>Không có thông tin tiện nghi</li>';
        }

        // prepare hidden fields
        roomIdInput.value = room.roomId || room.id || room.roomId;
    }

    checkBtn.addEventListener('click', function(){
        availabilityResult.textContent = '';
        bookingFeedback.textContent = '';
        bookingForm.style.display = 'none';
        submitBtn.disabled = true;

        const ci = checkInEl.value;
        const co = checkOutEl.value;

        if (!validDates(ci, co)){
            availabilityResult.className = 'availability-result unavailable';
            availabilityResult.textContent = 'Vui lòng chọn ngày hợp lệ (check-out phải sau check-in).';
            return;
        }

        // Call booking check API
        fetch(apiCheckUrl(roomId, ci, co))
            .then(r => r.json())
            .then(res => {
                let checkVal = null;
                if (res && typeof res === 'object') {
                    if (res.data && typeof res.data === 'object' && 'success' in res.data) checkVal = res.data.success;
                    else if ('success' in res) checkVal = res.success;
                    else if (res.data === true || res.data === false) checkVal = res.data;
                } else {
                    checkVal = Boolean(res);
                }

                if (checkVal === true) {
                    availabilityResult.className = 'availability-result unavailable';
                    availabilityResult.textContent = 'Không có lịch trống cho khoảng thời gian này.';
                } else {
                    const nights = nightsBetween(ci, co) || 1;
                    const pricePerNight = (roomData && roomData.pricePerNight) ? Number(roomData.pricePerNight) : 0;
                    const totalPrice = pricePerNight * nights;
                    availabilityResult.className = 'availability-result available';
                    availabilityResult.innerHTML = `
                        <div>Có sẵn: <strong>${ci}</strong> → <strong>${co}</strong></div>
                        <div>Số đêm: <strong>${nights}</strong> — Tổng: <strong style="color:#187700">${viMoney(totalPrice)}</strong></div>
                        <div style="margin-top:6px;color:#374151;font-size:0.95rem;">Vui lòng tiếp tục điền thông tin đặt phòng bên dưới.</div>
                    `;
                    // Fill booking form defaults and show
                    bookingForm.style.display = 'block';
                    roomIdInput.value = roomId;
                    bookingCodeInput.value = generateBookingCode(roomId);
                    nightsEl.textContent = nights;
                    totalPriceEl.textContent = viMoney(totalPrice);
                    bookingForm.dataset.checkIn = ci;
                    bookingForm.dataset.checkOut = co;
                    bookingForm.dataset.totalPrice = totalPrice;
                    // Synchronize min attributes
                    checkInEl.setAttribute('min', todayISO);
                    checkOutEl.setAttribute('min', nextDayISOFromISO(ci));
                    validateBookingForm();
                    setTimeout(() => bookingForm.scrollIntoView({behavior: 'smooth', block: 'center'}), 120);
                }
            })
            .catch(err => {
                availabilityResult.className = 'availability-result unavailable';
                availabilityResult.textContent = 'Lỗi kiểm tra lịch, vui lòng thử lại.';
                console.error(err);
            });
    });

    // form validation
    function validateBookingForm(){
        const ci = bookingForm.dataset.checkIn;
        const co = bookingForm.dataset.checkOut;
        const nameOk = (custName.value || '').trim().length > 1;
        const emailOk = (custEmail.value || '').trim().length > 3 && custEmail.value.includes('@');
        const phoneOk = (custPhone.value || '').trim().length > 6;
        const guestsOk = Number(numGuests.value) >= 1;
        const datesOk = validDates(ci, co);
        const ok = nameOk && emailOk && phoneOk && guestsOk && datesOk;
        submitBtn.disabled = !ok;
        return ok;
    }

    [custName, custEmail, custPhone, numGuests, specialRequests].forEach(el => {
        el.addEventListener('input', validateBookingForm);
    });

    bookingForm.addEventListener('submit', function(e){
        e.preventDefault();
        bookingFeedback.textContent = '';
        if (!validateBookingForm()) {
            bookingFeedback.style.color = '#b91c1c';
            bookingFeedback.textContent = 'Vui lòng điền đầy đủ và hợp lệ các trường bắt buộc.';
            return;
        }

        const payload = {
            booking_code: bookingCodeInput.value,
            room_id: Number(roomIdInput.value),
            customer_name: custName.value.trim(),
            customer_email: custEmail.value.trim(),
            customer_phone: custPhone.value.trim(),
            check_in_date: bookingForm.dataset.checkIn,
            check_out_date: bookingForm.dataset.checkOut,
            num_guests: Number(numGuests.value),
            total_price: Number(bookingForm.dataset.totalPrice) || 0,
            special_requests: specialRequests.value.trim() || null
        };

        submitBtn.disabled = true;
        submitBtn.textContent = 'Đang gửi...';

        fetch(apiBookingUrl(roomId), {
            method: 'POST',
            headers: {'Content-Type':'application/json'},
            body: JSON.stringify(payload)
        }).then(r => r.json())
            .then(res => {
                if (res && (res.success === true || (res.data && res.data.success === true))) {
                    // show modal popup with booking code
                    const code = payload.booking_code || bookingCodeInput.value || '';
                    showSuccessModal(code);
                    bookingFeedback.style.color = '#059669';
                    bookingFeedback.innerHTML = `🎉 <strong>Đặt phòng thành công!</strong> Mã booking: <strong>${code}</strong>`;

                    // Reset form fields & date pickers & availability summary
                    bookingForm.style.display = 'none';
                    custName.value = '';
                    custEmail.value = '';
                    custPhone.value = '';
                    numGuests.value = '1';
                    specialRequests.value = '';
                    nightsEl.textContent = '-';
                    totalPriceEl.textContent = '-';
                    // Reset date inputs to empty and reset min attributes
                    checkInEl.value = '';
                    checkOutEl.value = '';
                    checkInEl.setAttribute('min', todayISO);
                    checkOutEl.setAttribute('min', tomorrowISO);
                    availabilityResult.textContent = '';
                    // Clear booking code value
                    bookingCodeInput.value = '';
                } else if (res && res.errors) {
                    bookingFeedback.style.color = '#b91c1c';
                    bookingFeedback.textContent = 'Lỗi xác thực: ' + JSON.stringify(res.errors);
                } else {
                    bookingFeedback.style.color = '#b91c1c';
                    bookingFeedback.textContent = (res.message || 'Đặt phòng thất bại, vui lòng thử lại.');
                }
            })
            .catch(err => {
                bookingFeedback.style.color = '#b91c1c';
                bookingFeedback.textContent = 'Lỗi kết nối, vui lòng thử lại.';
                console.error(err);
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Gửi đặt phòng';
            });
    });

});