/**
 * Room Page Manager - jQuery version (routing: /api/rooms and filter endpoints)
 * Handles all UI interactions and business logic for Room Management page
 * Uses ApiService for backend communication
 * API routes: /api/rooms, /api/rooms/filter/status, /api/rooms/filter/room-number, /api/rooms/:id
 * DTO: id, room_number, room_type_id, status
 */
class RoomPageManager {
    constructor(apiBaseUrl) {
        this.api = new ApiService(apiBaseUrl);
        this.endpoint = "/rooms";
        this.currentRooms = [];
        this.roomTypes = [];
        this.deleteTargetId = null;
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.loadRoomTypes();
        this.loadRooms();
    }

    setupEventListeners() {
        $("#toggle_btn").on("click", () => $("#sidebar").toggleClass("opened"));
        $("#toggle_btn").on("click", () => $(".page").toggleClass("closed"));
        $("#toggle_btn").on("click", () => $(".header__left").toggleClass("expanded"));

        $('[data-action="open-add"]').on("click", () => this.openAddModal());
        $("#addRoomForm").on("submit", (e) => this.handleAdd(e));
        $("#editRoomForm").on("submit", (e) => this.handleEdit(e));
        $(".modal__close").on("click", function () {
            $(this).closest(".modal").fadeOut(200);
        });
        $(".modal").on("click", function (e) {
            if ($(e.target).is(".modal")) $(this).fadeOut(200);
        });
        $(document).on("keydown", (e) => {
            if (e.key === "Escape") $(".modal:visible").fadeOut(200);
        });
        $('[data-action="confirm-delete"]').on("click", () => this.confirmDelete());
        $('[data-action="cancel-delete"]').on("click", () => this.closeModal("delete_room"));
        $("#filterForm").on("submit", (e) => this.handleFilter(e));
    }

    async loadRooms(query = null) {
        try {
            let result;
            if (query && query.status) {
                result = await this.api.get(`/rooms/filter/status?status=${encodeURIComponent(query.status)}`);
            } else if (query && query.room_number) {
                result = await this.api.get(`/rooms/filter/room-number?room-number=${encodeURIComponent(query.room_number)}`);
                // API này luôn trả 1 phòng hoặc lỗi nếu không tìm thấy
            } else {
                result = await this.api.get(this.endpoint);
            }
            if (result.success) {
                let rooms;
                if (query && query.room_number) {
                    rooms = result.data ? [result.data] : [];
                } else {
                    rooms = Array.isArray(result.data) ? result.data : [];
                }
                this.currentRooms = rooms;
                this.renderTable(this.currentRooms);
            } else {
                this.showNotification(result.message || "Không tải được danh sách phòng!", "error");
            }
        } catch (error) {
            this.showNotification("Lỗi tải danh sách phòng: " + error.message, "error");
            console.error("Error loading rooms:", error);
        }
    }

    async loadRoomTypes() {
        // Room type endpoint is usually /room-types
        try {
            const res = await this.api.get("/room-types");
            if (res.success && Array.isArray(res.data)) {
                this.roomTypes = res.data;
                this.renderRoomTypeOptions("add_room_type_id");
                this.renderRoomTypeOptions("edit_room_type_id");
            }
        } catch (error) {
            this.roomTypes = [];
        }
    }

    renderRoomTypeOptions(selectId) {
        const $sel = $("#" + selectId);
        $sel.html("<option value=''>-- Chọn loại phòng --</option>");
        this.roomTypes.forEach(rt =>
            $sel.append(`<option value="${rt.id}">${this.escapeHtml(rt.name)}</option>`)
        );
    }

    renderTable(rooms) {
        const $tbody = $("#roomTableBody");
        if (!rooms || rooms.length === 0) {
            $tbody.html(`<tr>
        <td colspan="5" style="text-align:center;padding:28px;color:#999;">
          Không có phòng nào.
        </td>
      </tr>`);
            return;
        }
        const rows = rooms
            .map((room, idx) => {
                const typeName = this.roomTypes.find(rt => Number(rt.id) === Number(room.room_type_id))
                    ? this.roomTypes.find(rt => Number(rt.id) === Number(room.room_type_id)).name
                    : room.room_type_id || "-";
                return `<tr class="table__row">
          <td class="table__cell">${idx + 1}</td>
          <td class="table__cell">${this.escapeHtml(room.room_number)}</td>
          <td class="table__cell">${this.escapeHtml(typeName)}</td>
          <td class="table__cell">${this.formatStatus(room.status)}</td>
          <td class="table__cell table__cell--action">
            <div class="action-dropdown">
                <button class="action-dropdown__toggle">⋮</button>
                <div class="action-dropdown__menu">
                  <a href="#" class="action-dropdown__item" data-action="edit" data-id="${room.id}">Sửa</a>
                  <a href="#" class="action-dropdown__item" data-action="delete" data-id="${room.id}">Xóa</a>
                </div>
            </div>
          </td>
        </tr>`;
            }).join("");
        $tbody.html(rows);
        this.attachActionListeners();
    }

    attachActionListeners() {
        $('[data-action="edit"]').on("click", (e) => {
            e.preventDefault();
            const id = $(e.currentTarget).data("id");
            this.openEditModal(id);
        });
        $('[data-action="delete"]').on("click", (e) => {
            e.preventDefault();
            const id = $(e.currentTarget).data("id");
            this.openDeleteModal(id);
        });
        $(".action-dropdown__toggle").on("click", function (e) {
            e.stopPropagation();
            $(".action-dropdown__menu").hide();
            $(this).next(".action-dropdown__menu").toggle();
        });
        $(document).on("click", function () {
            $(".action-dropdown__menu").hide();
        });
    }

    openAddModal() {
        $("#addRoomForm")[0].reset();
        this.openModal("add_room");
    }

    async openEditModal(id) {
        try {
            const result = await this.api.get(`${this.endpoint}/${id}`);
            if (result.success) {
                const room = result.data;
                $("#edit_id").val(room.id);
                $("#edit_room_number").val(room.room_number || "");
                $("#edit_room_type_id").val(room.room_type_id || "");
                $("#edit_status").val(room.status || "available");
                this.openModal("edit_room");
            } else {
                this.showNotification(result.message || "Không tải được dữ liệu phòng!", "error");
            }
        } catch (error) {
            this.showNotification("Lỗi: " + error.message, "error");
        }
    }

    async handleAdd(e) {
        e.preventDefault();
        const formData = {
            room_number: $("#add_room_number").val() || "",
            room_type_id: Number($("#add_room_type_id").val()) || null,
            status: $("#add_status").val() || "available"
        };
        try {
            const result = await this.api.post(this.endpoint, formData);
            if (result.success) {
                this.showNotification(result.message || "Đã thêm phòng!", "success");
                this.closeModal("add_room");
                await this.loadRooms();
            } else {
                this.showNotification(result.message || "Thêm phòng thất bại!", "error");
            }
        } catch (error) {
            this.showNotification("Lỗi: " + error.message, "error");
        }
    }

    async handleEdit(e) {
        e.preventDefault();
        const id = $("#edit_id").val();
        const formData = {
            room_number: $("#edit_room_number").val() || "",
            room_type_id: Number($("#edit_room_type_id").val()) || null,
            status: $("#edit_status").val() || "available"
        };
        try {
            const result = await this.api.put(`${this.endpoint}/${id}`, formData);
            if (result.success) {
                this.showNotification(result.message || "Đã cập nhật phòng!", "success");
                this.closeModal("edit_room");
                await this.loadRooms();
            } else {
                this.showNotification(result.message || "Cập nhật phòng thất bại!", "error");
            }
        } catch (error) {
            this.showNotification("Lỗi: " + error.message, "error");
        }
    }

    openDeleteModal(id) {
        this.deleteTargetId = id;
        this.openModal("delete_room");
    }

    async confirmDelete() {
        if (!this.deleteTargetId) return;
        try {
            const result = await this.api.delete(`${this.endpoint}/${this.deleteTargetId}`);
            if (result.success) {
                this.showNotification(result.message || "Đã xóa phòng!", "success");
                this.closeModal("delete_room");
                this.deleteTargetId = null;
                await this.loadRooms();
            } else {
                this.showNotification(result.message || "Không xóa được phòng!", "error");
            }
        } catch (error) {
            this.showNotification("Lỗi: " + error.message, "error");
        }
    }

    handleFilter(e) {
        e.preventDefault();
        const status = $("#filterStatus").val();
        const room_number = $("#filterRoomNumber").val();
        if (status) {
            this.loadRooms({ status });
        } else if (room_number) {
            this.loadRooms({ room_number });
        } else {
            this.loadRooms();
        }
    }

    openModal(modalId) {
        $(`#${modalId}`).fadeIn(200);
        $("body").css("overflow", "hidden");
    }
    closeModal(modalId) {
        $(`#${modalId}`).fadeOut(200);
        $("body").css("overflow", "");
    }
    showNotification(message, type = "info") {
        if ($("#notification-container").length === 0) {
            $("body").append(`
        <div id="notification-container" style="
          position: fixed;
          top: 20px;
          right: 20px;
          z-index: 9999;
          display: flex;
          flex-direction: column;
          gap: 10px;
          max-width: 420px;
        "></div>
      `);
        }
        const colors = {
            success: { bg: "#d4edda", border: "#c3e6cb", text: "#155724" },
            error: { bg: "#f8d7da", border: "#f5c6cb", text: "#721c24" },
            warning: { bg: "#fff3cd", border: "#ffeaa7", text: "#856404" },
            info: { bg: "#d1ecf1", border: "#bee5eb", text: "#0c5460" }
        };
        const color = colors[type] || colors.info;
        const $notification = $(`
      <div class="notification notification--${type}" style="
        background-color: ${color.bg};
        border: 1px solid ${color.border};
        color: ${color.text};
        padding: 15px 20px;
        border-radius: 4px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.09);
        display: flex; align-items: center; justify-content: space-between; gap: 15px;
        animation: slideIn 0.3s;
        min-width: 300px;
      ">
        <span style="flex: 1;">${this.escapeHtml(message)}</span>
        <button class="notification-close" style="
          background: none; border: none; color: ${color.text}; cursor: pointer; font-size: 19px; line-height: 1; padding: 0;
        ">&times;</button>
      </div>
    `);
        $notification.find(".notification-close").on("click", function () {
            $(this).closest(".notification").fadeOut(250, function () { $(this).remove(); });
        });
        $("#notification-container").append($notification);
        setTimeout(() => {
            $notification.fadeOut(350, function () { $(this).remove(); });
        }, 4400);
    }

    escapeHtml(text) {
        if (!text) return "";
        return String(text).replace(/[&<>"']/g, (m) => ({
            "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#039;"
        }[m]));
    }
    formatStatus(status) {
        switch (status) {
            case "available": return `<span style="color:#059669">Còn trống</span>`;
            case "occupied": return `<span style="color:#b91c1c">Đã thuê</span>`;
            case "cleaning": return `<span style="color:#c2410c">Đang dọn</span>`;
            case "maintenance": return `<span style="color:#6d28d9">Bảo trì</span>`;
            default: return this.escapeHtml(status || "-");
        }
    }
}

if ($("#notification-animations").length === 0) {
    $("head").append(`
    <style id="notification-animations">
      @keyframes slideIn {
        from {transform: translateX(100%);opacity:0;}
        to {transform: translateX(0);opacity:1;}
      }
    </style>
  `);
}