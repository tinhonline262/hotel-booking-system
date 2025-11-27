document.addEventListener('DOMContentLoaded', function(){
    const params = new URLSearchParams(window.location.search);
    const roomId = params.get('id');

    // Use controller route you provided
    const apiRoomUrl = `/api/room-details/${roomId}`; // GET -> RoomDetailController::getDetailRooms
    const apiCheckUrl = (id, ci, co) => `/api/booking/rooms/${id}?checkInDate=${encodeURIComponent(ci)}&checkOutDate=${encodeURIComponent(co)}`;
    const apiBookingUrl = (id) => `/api/booking/rooms/${id}`;

    // Elements
    const hero = document.getElementById('vr-hero');
    const thumbs = document.getElementById('vr-thumbs');
    const titleEl = document.getElementById('vr-title');
    const roomTypeEl = document.getElementById('vr-roomtype');
    const capacityEl = document.getElementById('vr-capacity');
    const priceBig = document.getElementById('vr-price');        // large card price
    const priceSmall = document.getElementById('vr-price-inline'); // inline meta price
    const descEl = document.getElementById('vr-description');
    const amList = document.getElementById('vr-amenities-list');

    const checkIn = document.getElementById('vr-checkin');
    const checkOut = document.getElementById('vr-checkout');
    const checkBtn = document.getElementById('vr-check-btn');
    const availEl = document.getElementById('vr-availability');

    const bookForm = document.getElementById('vr-bookform');
    const roomIdInput = document.getElementById('vr-room-id');
    const bookingCodeInput = document.getElementById('vr-booking-code');
    const cname = document.getElementById('vr-cname');
    const cemail = document.getElementById('vr-cemail');
    const cphone = document.getElementById('vr-cphone');
    const cguests = document.getElementById('vr-guests');
    const crequests = document.getElementById('vr-requests');
    const nightsEl = document.getElementById('vr-nights');
    const totalEl = document.getElementById('vr-total');
    const submitBtn = document.getElementById('vr-submit');
    const feedback = document.getElementById('vr-feedback');

    let roomData = null;

    if (!roomId) {
        hero.innerHTML = '<div style="padding:18px;color:#b91c1c">ID phòng không có trong URL</div>'; return;
    }

    // helpers
    function toLocalISO(d){ const y=d.getFullYear(), m=String(d.getMonth()+1).padStart(2,'0'), day=String(d.getDate()).padStart(2,'0'); return `${y}-${m}-${day}`; }
    function nextDayISO(iso){ const p=iso.split('-').map(Number); const dt=new Date(p[0],p[1]-1,p[2]); dt.setDate(dt.getDate()+1); return toLocalISO(dt); }
    function validDates(ci,co){ if(!ci||!co) return false; return new Date(ci) < new Date(co); }
    function nightsBetween(ci,co){ return Math.ceil((new Date(co)-new Date(ci))/(1000*60*60*24)); }
    function viMoney(v){ return Number(v).toLocaleString('vi-VN') + ' đ'; }

    // booking code generator matching PHP: BK-{roomId}-{YmdHis}-{uniqid}
    function pad(n){ return String(n).padStart(2,'0'); }
    function ymdHis(d){ return d.getFullYear()+ pad(d.getMonth()+1)+ pad(d.getDate()) + pad(d.getHours()) + pad(d.getMinutes()) + pad(d.getSeconds()); }
    function genBookingCode(id){
        const random10 = Math.random().toString(16).slice(2, 12);
        return `BK-${id}-${random10}`;}

    // modal popup success
    function showModal(code){
        const existing = document.getElementById('vr-success-modal'); if(existing) existing.remove();
        const overlay = document.createElement('div'); overlay.id='vr-success-modal';
        Object.assign(overlay.style,{position:'fixed',left:0,top:0,right:0,bottom:0,background:'rgba(0,0,0,0.5)',display:'flex',alignItems:'center',justifyContent:'center',zIndex:12000});
        const box = document.createElement('div'); Object.assign(box.style,{width:'min(520px,92%)',background:'#fff',borderRadius:'10px',padding:'20px',textAlign:'left'});
        box.innerHTML = `
      <div style="display:flex;justify-content:space-between;align-items:center">
        <h3 style="margin:0;color:#059669">Đặt phòng thành công</h3>
        <button id="vr-modal-close" style="background:none;border:0;font-size:18px;cursor:pointer">✕</button>
      </div>
      <p style="color:#333">Mã booking của bạn:</p>
      <div style="background:#f1f9f3;padding:12px;border-radius:8px;font-weight:700;color:#0b5132;margin-bottom:12px" id="vr-modal-code">${code}</div>
      <div style="text-align:right"><button id="vr-modal-ok" style="background:#059669;color:#fff;border:0;padding:8px 12px;border-radius:8px;cursor:pointer">Đóng</button></div>
    `;
        overlay.appendChild(box); document.body.appendChild(overlay);
        document.getElementById('vr-modal-close').addEventListener('click', ()=>overlay.remove());
        document.getElementById('vr-modal-ok').addEventListener('click', ()=>overlay.remove());
        document.getElementById('vr-modal-code').addEventListener('click', function(){ navigator.clipboard?.writeText(code).catch(()=>{}); });
        document.getElementById('vr-modal-ok').focus();
    }

    // initial date mins
    const today = toLocalISO(new Date()), tomorrow = nextDayISO(today);
    checkIn.setAttribute('min', today); checkOut.setAttribute('min', tomorrow);

    checkIn.addEventListener('change', ()=> {
        feedback.textContent=''; availEl.textContent=''; bookForm.style.display='none'; submitBtn.disabled=true;
        const ci = checkIn.value;
        if(!ci){ checkOut.setAttribute('min', tomorrow); return; }
        checkOut.setAttribute('min', nextDayISO(ci));
        if(checkOut.value && !(new Date(checkOut.value) > new Date(ci))) checkOut.value='';
    });

    checkOut.addEventListener('change', ()=> {
        feedback.textContent=''; availEl.textContent=''; bookForm.style.display='none'; submitBtn.disabled=true;
        if(checkIn.value && checkOut.value && !validDates(checkIn.value, checkOut.value)){
            availEl.className='availability unavailable'; availEl.textContent='Check-out phải lớn hơn check-in.';
        } else { availEl.textContent=''; }
    });

    // fetch room details
    fetch(apiRoomUrl).then(r=>{
        if (!r.ok) throw new Error('HTTP ' + r.status);
        return r.json();
    }).then(res=>{
        const data = res.data || res;
        // If data is list, pick first; if single use as-is
        const payload = Array.isArray(data) && data.length ? data[0] : data;
        roomData = payload;
        renderRoom(payload);
    }).catch(err=>{
        hero.innerHTML = '<div style="padding:18px;color:#b91c1c">Không thể tải thông tin phòng</div>'; console.error(err);
    });

    function renderRoom(r){
        if(!r) return;
        const imgs = Array.isArray(r.images)? r.images : [];
        const main = (imgs.find(i=>i.isPrimary) || imgs[0] || {}).imageUrl || '';
        hero.innerHTML = main ? `<img src="${main}" alt="img">` : `<div style="color:#666">Không có ảnh</div>`;
        thumbs.innerHTML = '';
        imgs.forEach(im=>{
            const t=document.createElement('img'); t.src=im.imageUrl; t.alt='thumb';
            t.addEventListener('click', ()=>{ const img=hero.querySelector('img'); if(img) img.src = im.imageUrl; thumbs.querySelectorAll('img').forEach(x=>x.classList.remove('selected')); t.classList.add('selected'); });
            if(im.imageUrl === main) t.classList.add('selected'); thumbs.appendChild(t);
        });

        titleEl.textContent = `Phòng ${r.roomNumber || r.roomId || ''}`;
        roomTypeEl.textContent = r.roomType || '-';
        // capacity: show just number (or '-' if missing)
        capacityEl.textContent = (typeof r.capacity !== 'undefined' && r.capacity !== null) ? String(r.capacity) : '-';
        // priceBig (card) and priceSmall (inline meta)
        const priceText = r.pricePerNight ? (Number(r.pricePerNight).toLocaleString('vi-VN') + ' đ') : '-';
        if (priceBig) priceBig.textContent = priceText;
        if (priceSmall) priceSmall.textContent = priceText;
        descEl.textContent = r.description || '-';
        amList.innerHTML = '';
        (Array.isArray(r.amenities)? r.amenities : []).forEach(a=>{ const li=document.createElement('li'); li.textContent=a; amList.appendChild(li); });
        roomIdInput.value = r.roomId || r.id || roomId;
    }

    // availability check
    checkBtn.addEventListener('click', ()=>{
        availEl.textContent=''; feedback.textContent=''; bookForm.style.display='none'; submitBtn.disabled=true;
        const ci = checkIn.value, co = checkOut.value;
        if(!validDates(ci,co)){ availEl.className='availability unavailable'; availEl.textContent='Vui lòng chọn ngày hợp lệ (checkout phải sau checkin)'; return; }
        fetch(apiCheckUrl(roomId,ci,co)).then(r=>r.json()).then(res=>{
            let check = null;
            if(res && typeof res === 'object'){
                if(res.data && typeof res.data === 'object' && 'success' in res.data) check = res.data.success;
                else if('success' in res) check = res.success;
                else check = (res.data === true || res.data === false) ? res.data : null;
            } else check = Boolean(res);
            if(check === true){ availEl.className='availability unavailable'; availEl.textContent='Không có lịch trống cho khoảng thời gian này.'; return; }
            const nights = nightsBetween(ci,co) || 1; const p = (roomData && roomData.pricePerNight)? Number(roomData.pricePerNight):0; const total = p * nights;
            availEl.className = 'availability available';
            availEl.innerHTML = `Có sẵn: <strong>${ci}</strong> → <strong>${co}</strong><div>Số đêm: <strong>${nights}</strong> — Tổng: <strong style="color:#187700">${viMoney(total)}</strong></div>`;
            bookForm.style.display='block'; roomIdInput.value = roomId; bookingCodeInput.value = genBookingCode(roomId);
            nightsEl.textContent = nights; totalEl.textContent = viMoney(total);
            bookForm.dataset.checkin = ci; bookForm.dataset.checkout = co; bookForm.dataset.total = total;
            validateForm(); setTimeout(()=> bookForm.scrollIntoView({behavior:'smooth', block:'center'}),120);
        }).catch(err=>{ availEl.className='availability unavailable'; availEl.textContent='Lỗi kiểm tra lịch'; console.error(err); });
    });

    // validation & submit
    function validateForm(){
        const ci = bookForm.dataset.checkin, co = bookForm.dataset.checkout;
        const nameOk = (cname.value||'').trim().length > 1;
        const emailOk = (cemail.value||'').includes('@');
        const phoneOk = (cphone.value||'').trim().length > 6;
        const guestsOk = Number(cguests.value) >= 1;
        const datesOk = validDates(ci,co);
        const ok = nameOk && emailOk && phoneOk && guestsOk && datesOk;
        submitBtn.disabled = !ok; return ok;
    }
    [cname,cemail,cphone,cguests,crequests].forEach(el=>el.addEventListener('input', validateForm));

    bookForm.addEventListener('submit', function(e){
        e.preventDefault(); feedback.textContent='';
        if(!validateForm()){ feedback.style.color='#b91c1c'; feedback.textContent='Vui lòng điền đầy đủ'; return; }
        const payload = {
            booking_code: bookingCodeInput.value,
            room_id: Number(roomIdInput.value),
            customer_name: cname.value.trim(),
            customer_email: cemail.value.trim(),
            customer_phone: cphone.value.trim(),
            check_in_date: bookForm.dataset.checkin,
            check_out_date: bookForm.dataset.checkout,
            num_guests: Number(cguests.value),
            total_price: Number(bookForm.dataset.total) || 0,
            special_requests: (crequests.value||'').trim() || null
        };
        submitBtn.disabled=true; submitBtn.textContent='Đang gửi...';
        fetch(apiBookingUrl(roomId), { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(payload) })
            .then(r=>r.json()).then(res=>{
            if(res && (res.success === true || (res.data && res.data.success === true))){
                showModal(payload.booking_code);
                feedback.style.color='#059669';
                feedback.innerHTML = `🎉 Đặt phòng thành công. Mã: <strong>${payload.booking_code}</strong>`;
                bookForm.style.display='none'; cname.value=''; cemail.value=''; cphone.value=''; cguests.value='1'; crequests.value='';
                nightsEl.textContent='-'; totalEl.textContent='-'; checkIn.value=''; checkOut.value=''; checkIn.setAttribute('min', today); checkOut.setAttribute('min', tomorrow);
                availEl.textContent=''; bookingCodeInput.value=''; setTimeout(()=>{ feedback.textContent=''; },8000);
            } else if(res && res.errors){ feedback.style.color='#b91c1c'; feedback.textContent = 'Lỗi xác thực: ' + JSON.stringify(res.errors); }
            else { feedback.style.color='#b91c1c'; feedback.textContent = (res.message || 'Đặt phòng thất bại'); }
        }).catch(err=>{ feedback.style.color='#b91c1c'; feedback.textContent='Lỗi kết nối'; console.error(err); })
            .finally(()=>{ submitBtn.disabled=false; submitBtn.textContent='Gửi đặt phòng'; });
    });

});