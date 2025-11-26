document.addEventListener("DOMContentLoaded", function(){
    const params = new URLSearchParams(window.location.search);
    const roomId = params.get('id');
    const apiUrl = '/api/room-details/' + roomId;

    const elRoomNumber = document.getElementById("room-number");
    const elRoomType = document.getElementById("room-type");
    const elPrice = document.getElementById("price-per-night");
    const elCapacity = document.getElementById("capacity");
    const elAmenities = document.getElementById("amenities");
    const elStatus = document.getElementById("room-status");
    const elImages = document.getElementById("room-images");
    const elBookBtn = document.getElementById("book-now-btn");

    if (!roomId) {
        elRoomNumber.innerHTML = "<span style='color:#e53935'>Không tìm thấy thông tin phòng!</span>";
        elRoomType.textContent = '';
        elPrice.textContent = '';
        elBookBtn.style.display = 'none';
        return;
    }

    fetch(apiUrl)
        .then(res => res.json())
        .then(res => {
            if (!res.success || !res.data) throw new Error();
            renderRoomDetails(res.data);
        }).catch(() => {
        elRoomNumber.innerHTML = "<span style='color:#e53935'>Không tìm thấy thông tin phòng!</span>";
        elRoomType.textContent = '';
        elPrice.textContent = '';
        elBookBtn.style.display = 'none';
    });

    function viMoney(x) { if (!x) return '-'; return Number(x).toLocaleString('vi-VN') + ' đ'; }

    function renderRoomDetails(room) {
        // Number & Type
        elRoomNumber.textContent = `Phòng ${room.roomNumber || room.roomId || '-'}`;

        elRoomType.textContent = room.roomType || '-';
        elPrice.textContent = viMoney(room.pricePerNight);
        elCapacity.textContent = room.capacity || '-';

        // Amenities
        if (Array.isArray(room.amenities)) {
            elAmenities.textContent = room.amenities.filter(x=>!!x).join(', ') || '-';
        } else {
            elAmenities.textContent = '-';
        }

        // Status & color
        const s = room.status || "";
        elStatus.textContent = statusText(s);
        elStatus.className = '';
        elStatus.classList.add(s);

        // Images section
        renderImages(room.images||[]);

        // Set button
        elBookBtn.disabled = (s !== "available");
        elBookBtn.onclick = () => {
            window.location.href = `room-booking.html?id=${room.roomId}`;
        };
        if (s !== "available") elBookBtn.textContent = "Không thể đặt phòng";
    }

    function renderImages(images) {
        elImages.innerHTML = '';

        if (!images.length) {
            elImages.innerHTML = `<img class="primary-img" src="https://dummyimage.com/400x300/eee/aaa&text=No+Image" alt="Không có ảnh">`;
            return;
        }

        // Find primary or first
        let primary = images.find(im => im.isPrimary) || images[0];

        // Main image
        const mainImg = document.createElement("img");
        mainImg.className = "primary-img";
        mainImg.src = primary.imageUrl || '';
        mainImg.alt = "Ảnh phòng";
        elImages.appendChild(mainImg);

        // List thumbnail if more than 1 image
        if (images.length > 1) {
            const thumbBox = document.createElement("div");
            thumbBox.className = "room-image-list";
            images.forEach(im => {
                const img = document.createElement("img");
                img.src = im.imageUrl;
                img.alt = "Ảnh phòng";
                if (im.imageId === primary.imageId) img.classList.add("selected");
                img.onclick = () => {
                    mainImg.src = im.imageUrl;
                    // Remove selection
                    Array.from(thumbBox.querySelectorAll("img")).forEach(im2=>im2.classList.remove("selected"));
                    img.classList.add("selected");
                };
                thumbBox.appendChild(img);
            });
            elImages.appendChild(thumbBox);
        }
    }

    function statusText(status) {
        switch (status) {
            case 'available': return 'Còn trống';
            case 'occupied': return 'Đã thuê';
            case 'cleaning': return 'Đang dọn';
            default: return status || '-';
        }
    }
});