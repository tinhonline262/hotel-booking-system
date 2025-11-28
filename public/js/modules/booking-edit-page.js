class BookingEditPageManager {
    constructor(apiBaseUrl) {
        this.apiBaseUrl = apiBaseUrl;
        this.tableBody = $('#bookingTableBody');
        this.idToDelete = null;
        this.init();
    }
    init() {
        this.loadBookings();
        $('#bookingFilterForm').on('submit', (e) => { e.preventDefault(); this.loadBookings(); });
        this.tableBody.on('click', '.btn-edit', (e) => this.openEditModal($(e.currentTarget).data('row')));
        this.tableBody.on('click', '.btn-delete', (e) => this.openDeleteModal($(e.currentTarget).data('id')));
        $('#editBookingForm').on('submit', (e) => this.submitEdit(e));
        $('#confirm-delete-btn').on('click', () => this.submitDelete());
        $('.modal__close').on('click', () => $('.modal').hide());
        $('.modal').on('click', function(e) { if ($(e.target).hasClass('modal')) $(this).hide(); });
    }
    buildFilterParams() {
        return {
            status: $('#filter_status').val(), check_in_date: $('#filter_check_in_date').val(),
            check_out_date: $('#filter_check_out_date').val(), booking_code: $('#filter_booking_code').val(),
            customer_email: $('#filter_customer_email').val(), customer_name: $('#filter_customer_name').val(),
            customer_phone: $('#filter_customer_phone').val(),
        };
    }
    getApiUrlByFilter(params) {
        if (params.booking_code) return `${this.apiBaseUrl}/bookings/filter/code?code=${encodeURIComponent(params.booking_code)}`;
        if (params.customer_email) return `${this.apiBaseUrl}/bookings/filter/email?email=${encodeURIComponent(params.customer_email)}`;
        if (params.customer_name) return `${this.apiBaseUrl}/bookings/filter/name?name=${encodeURIComponent(params.customer_name)}`;
        if (params.customer_phone) return `${this.apiBaseUrl}/bookings/filter/phone?phone=${encodeURIComponent(params.customer_phone)}`;
        if (params.check_in_date) return `${this.apiBaseUrl}/bookings/filter/checkin?checkin=${encodeURIComponent(params.check_in_date)}`;
        if (params.check_out_date) return `${this.apiBaseUrl}/bookings/filter/checkout?checkout=${encodeURIComponent(params.check_out_date)}`;
        if (params.status) return `${this.apiBaseUrl}/bookings/filter/status?status=${encodeURIComponent(params.status)}`;
        return `${this.apiBaseUrl}/bookings`;
    }
    loadBookings() {
        let params = this.buildFilterParams();
        let url = this.getApiUrlByFilter(params);
        $.getJSON(url, (res) => {
            let rows = res.data || [];
            if (!Array.isArray(rows)) rows = [rows];
            this.tableBody.html(
                rows.map((row, i) => `
                <tr class="table__row">
                    <td>${i+1}</td>
                    <td>${row.booking_code||''}</td>
                    <td>${row.room_id||''}</td>
                    <td>${row.customer_name||''}</td>
                    <td>${row.customer_email||''}</td>
                    <td>${row.customer_phone||''}</td>
                    <td>${row.check_in_date||''}</td>
                    <td>${row.check_out_date||''}</td>
                    <td>${row.num_guests||''}</td>
                    <td>${row.total_price||''}</td>
                    <td>${row.status||''}</td>
                    <td>${row.special_requests||''}</td>
                    <td class="action-buttons">
                        <button class="btn btn-edit" data-row='${JSON.stringify(row)}'>Sửa</button>
                        <button class="btn btn-danger btn-delete" data-id="${row.id}">Xóa</button>
                    </td>
                </tr>
                `).join('')
            );
        });
    }
    openEditModal(rowJson) {
        let row = (typeof rowJson === "string") ? JSON.parse(rowJson) : rowJson;
        $('#edit_id').val(row.id||'');
        $('#edit_booking_code').val(row.booking_code||'');
        $('#edit_room_id').val(row.room_id||'');
        $('#edit_customer_name').val(row.customer_name||'');
        $('#edit_customer_email').val(row.customer_email||'');
        $('#edit_customer_phone').val(row.customer_phone||'');
        $('#edit_check_in_date').val(row.check_in_date||'');
        $('#edit_check_out_date').val(row.check_out_date||'');
        $('#edit_num_guests').val(row.num_guests||'');
        $('#edit_total_price').val(row.total_price||'');
        $('#edit_status').val(row.status||'');
        $('#edit_special_requests').val(row.special_requests||'');
        $('#edit_booking_modal').show();
    }
    submitEdit(e) {
        e.preventDefault();
        let id = $('#edit_id').val();
        let body = {
            room_id: parseInt($('#edit_room_id').val()),
            customer_name: $('#edit_customer_name').val(),
            customer_email: $('#edit_customer_email').val(),
            customer_phone: $('#edit_customer_phone').val(),
            check_in_date: $('#edit_check_in_date').val(),
            check_out_date: $('#edit_check_out_date').val(),
            num_guests: parseInt($('#edit_num_guests').val()),
            total_price: parseFloat($('#edit_total_price').val()),
            status: $('#edit_status').val(),
            special_requests: $('#edit_special_requests').val()
        };
        $.ajax({
            url: `${this.apiBaseUrl}/bookings/${id}`,
            method: "PUT", contentType: "application/json", data: JSON.stringify(body),
            success: () => { $('#edit_booking_modal').hide(); this.loadBookings(); },
            error: (xhr) => alert('Có lỗi khi sửa: ' + (xhr.responseJSON?.message||''))
        });
    }
    openDeleteModal(id) { this.idToDelete = id; $('#delete_booking_modal').show(); }
    submitDelete() {
        let id = this.idToDelete;
        $.ajax({
            url: `${this.apiBaseUrl}/bookings/${id}`, method: "DELETE",
            success: () => { $('#delete_booking_modal').hide(); this.loadBookings(); },
            error: (xhr) => alert('Có lỗi khi xóa: ' + (xhr.responseJSON?.message||''))
        });
        this.idToDelete = null;
    }
}
$(function () { new BookingEditPageManager('http://localhost:8000/api'); });