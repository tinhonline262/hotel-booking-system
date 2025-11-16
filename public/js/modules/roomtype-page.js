/**
 * Room Type Page Manager - jQuery version
 * Handles all UI interactions and business logic for Room Type page
 * Uses ApiService for backend communication
 */
class RoomTypePageManager {
  constructor(apiBaseUrl) {
    // Initialize API service
    this.api = new ApiService(apiBaseUrl);
    this.endpoint = "/room-types";

    // State
    this.currentRoomTypes = [];
    this.deleteTargetId = null;

    // Initialize when DOM is ready
    this.init();
  }

  /**
   * Initialize the page manager
   */
  init() {
    this.setActiveSidebar();
    this.setupEventListeners();
    this.loadRoomTypes();
    this.loadRooms();
  }
  setActiveSidebar() {
    const currentPage = window.location.pathname.split('/').pop() || 'room-type.html';
    $('.sidebar__link').removeClass('sidebar__link--active');
    $(`.sidebar__link[href="${currentPage}"]`).addClass('sidebar__link--active');
  }

  /**
   * Setup all event listeners
   */
  setupEventListeners() {
    // Mobile sidebar toggle
    $("#toggle_btn").on("click", () => {
      $("#sidebar").toggleClass("opened");
    });

    $("#toggle_btn").on("click", () => {
      $(".page").toggleClass("closed");
    });

    $("#toggle_btn").on("click", function () {
      $(".header__left").toggleClass("expanded");
    });

    // Add button
    $('[data-action="open-add"]').on("click", () => this.openAddModal());

    // Form submissions
    $("#addForm").on("submit", (e) => this.handleAdd(e));
    $("#editForm").on("submit", (e) => this.handleEdit(e));

    // Modal close buttons
    $(".modal__close").on("click", function () {
      $(this).closest(".modal").fadeOut(200);
    });

    // Close modal on outside click
    $(".modal").on("click", function (e) {
      if ($(e.target).is(".modal")) {
        $(this).fadeOut(200);
      }
    });

    // ESC key to close modal
    $(document).on("keydown", (e) => {
      if (e.key === "Escape") {
        $(".modal:visible").fadeOut(200);
      }
    });

    // Delete confirmation
    $('[data-action="confirm-delete"]').on("click", () => this.confirmDelete());
    $('[data-action="cancel-delete"]').on("click", () =>
      this.closeModal("delete_type")
    );
  }

  /**
   * Load all room types from API
   */
  async loadRoomTypes() {
    try {
      const result = await this.api.get(this.endpoint);

      if (result.success) {
        this.currentRoomTypes = result.data;
        this.renderTable(result.data);
      } else {
        this.showNotification(
          result.message || "Failed to load room types",
          "error"
        );
      }
    } catch (error) {
      console.error("Error loading room types:", error);
      this.showNotification(
        "Error loading room types: " + error.message,
        "error"
      );
    }
  }

  /**
   * Render room types table
   * @param {Array} roomTypes - Array of room type objects
   */
  renderTable(roomTypes) {
    const $tbody = $("#roomTypeTableBody");

    if (!roomTypes || roomTypes.length === 0) {
      $tbody.html(`
                <tr>
                    <td colspan="7" style="text-align: center; padding: 30px; color: #999;">
                        No room types found
                    </td>
                </tr>
            `);
      return;
    }

    const rows = roomTypes
      .map((room, index) => {
        const amenities = this.parseAmenities(room.amenities);

        return `
                <tr class="table__row">
                    <td class="table__cell">${index + 1}</td>
                    <td class="table__cell">${this.escapeHtml(room.name)}</td>
                    <td class="table__cell">${this.escapeHtml(
          room.description || "-"
        )}</td>
                    <td class="table__cell">${room.capacity}</td>
                    <td class="table__cell">${this.formatPrice(
          room.pricePerNight
        )}</td>
                    <td class="table__cell">${this.escapeHtml(amenities)}</td>
                    <td class="table__cell table__cell--action">
                        <div class="action-dropdown">
                            <button class="action-dropdown__toggle">⋮</button>
                            <div class="action-dropdown__menu">
                                <a href="#" class="action-dropdown__item" data-action="edit" data-id="${room.id
          }">Edit</a>
                                <a href="#" class="action-dropdown__item" data-action="delete" data-id="${room.id
          }">Delete</a>
                            </div>
                        </div>
                    </td>
                </tr>
            `;
      })
      .join("");

    $tbody.html(rows);

    // Attach action button listeners
    this.attachActionListeners();
  }

  /**
   * Attach event listeners to table action buttons
   */
  attachActionListeners() {
    // Edit buttons
    $('[data-action="edit"]').on("click", (e) => {
      e.preventDefault();
      const id = $(e.currentTarget).data("id");
      this.openEditModal(id);
    });

    // Delete buttons
    $('[data-action="delete"]').on("click", (e) => {
      e.preventDefault();
      const id = $(e.currentTarget).data("id");
      this.openDeleteModal(id);
    });
  }

  /**
   * Open add modal
   */
  openAddModal() {
    $("#addForm")[0].reset();
    this.openModal("add_type");
  }

  /**
   * Handle add form submission
   * @param {Event} e - Submit event
   */
  async handleAdd(e) {
    e.preventDefault();

    const formData = {
      name: $("#add_name").val(),
      description: $("#add_description").val(),
      capacity: parseInt($("#add_capacity").val()),
      pricePerNight: parseFloat($("#add_price").val()),
      amenities: this.formatAmenitiesForApi($("#add_amenities").val()),
    };

    try {
      const result = await this.api.post(this.endpoint, formData);

      if (result.success) {
        this.showNotification(
          result.message || "Room type created successfully",
          "success"
        );
        this.closeModal("add_type");
        await this.loadRoomTypes();
      } else {
        if (result.errors) {
          const errorMsg = Object.values(result.errors).join("\n");
          this.showNotification(errorMsg, "error");
        } else {
          this.showNotification(
            result.message || "Failed to create room type",
            "error"
          );
        }
      }
    } catch (error) {
      console.error("Error creating room type:", error);
      this.showNotification("Error: " + error.message, "error");
    }
  }

  /**
   * Open edit modal with data
   * @param {number} id - Room type ID
   */
  async openEditModal(id) {
    try {
      const result = await this.api.get(`${this.endpoint}/${id}`);

      if (result.success) {
        const room = result.data;

        $("#edit_id").val(room.id);
        $("#edit_name").val(room.name);
        $("#edit_description").val(room.description || "");
        $("#edit_capacity").val(room.capacity);
        $("#edit_price").val(room.pricePerNight);

        // Handle amenities
        const amenitiesValue = Array.isArray(room.amenities)
          ? room.amenities.join(", ")
          : room.amenities || "";
        $("#edit_amenities").val(amenitiesValue);

        this.openModal("edit_type");
      } else {
        this.showNotification(
          result.message || "Failed to load room type",
          "error"
        );
      }
    } catch (error) {
      console.error("Error loading room type:", error);
      this.showNotification("Error: " + error.message, "error");
    }
  }

  /**
   * Handle edit form submission
   * @param {Event} e - Submit event
   */
  async handleEdit(e) {
    e.preventDefault();

    const id = $("#edit_id").val();
    const formData = {
      name: $("#edit_name").val(),
      description: $("#edit_description").val(),
      capacity: parseInt($("#edit_capacity").val()),
      pricePerNight: parseFloat($("#edit_price").val()),
      amenities: this.formatAmenitiesForApi($("#edit_amenities").val()),
    };

    try {
      const result = await this.api.put(`${this.endpoint}/${id}`, formData);

      if (result.success) {
        this.showNotification(
          result.message || "Room type updated successfully",
          "success"
        );
        this.closeModal("edit_type");
        await this.loadRoomTypes();
      } else {
        if (result.errors) {
          const errorMsg = Object.values(result.errors).join("\n");
          this.showNotification(errorMsg, "error");
        } else {
          this.showNotification(
            result.message || "Failed to update room type",
            "error"
          );
        }
      }
    } catch (error) {
      console.error("Error updating room type:", error);
      this.showNotification("Error: " + error.message, "error");
    }
  }

  /**
   * Open delete confirmation modal
   * @param {number} id - Room type ID
   */
  openDeleteModal(id) {
    this.deleteTargetId = id;
    this.openModal("delete_type");
  }

  /**
   * Confirm and execute delete
   */
  async confirmDelete() {
    if (!this.deleteTargetId) return;

    try {
      const result = await this.api.delete(
        `${this.endpoint}/${this.deleteTargetId}`
      );

      if (result.success) {
        this.showNotification(
          result.message || "Room type deleted successfully",
          "success"
        );
        this.closeModal("delete_type");
        this.deleteTargetId = null;
        await this.loadRoomTypes();
      } else {
        this.showNotification(
          result.message || "Failed to delete room type",
          "error"
        );
      }
    } catch (error) {
      console.error("Error deleting room type:", error);
      this.showNotification("Error: " + error.message, "error");
    }
  }

  /**
   * Open modal with fade animation
   * @param {string} modalId - Modal element ID
   */
  openModal(modalId) {
    $(`#${modalId}`).fadeIn(200);
    $("body").css("overflow", "hidden");
  }

  /**
   * Close modal with fade animation
   * @param {string} modalId - Modal element ID
   */
  closeModal(modalId) {
    $(`#${modalId}`).fadeOut(200);
    $("body").css("overflow", "");
  }

  /**
   * Show notification
   * @param {string} message - Notification message
   * @param {string} type - Notification type (success, error, warning, info)
   */
  showNotification(message, type = "info") {
    // Create notification container if not exists
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
                    max-width: 400px;
                "></div>
            `);
    }

    const colors = {
      success: { bg: "#d4edda", border: "#c3e6cb", text: "#155724" },
      error: { bg: "#f8d7da", border: "#f5c6cb", text: "#721c24" },
      warning: { bg: "#fff3cd", border: "#ffeaa7", text: "#856404" },
      info: { bg: "#d1ecf1", border: "#bee5eb", text: "#0c5460" },
    };

    const color = colors[type] || colors.info;

    const $notification = $(`
            <div class="notification notification--${type}" style="
                background-color: ${color.bg};
                border: 1px solid ${color.border};
                color: ${color.text};
                padding: 15px 20px;
                border-radius: 4px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 15px;
                animation: slideIn 0.3s ease-out;
                min-width: 300px;
            ">
                <span style="flex: 1;">${this.escapeHtml(message)}</span>
                <button class="notification-close" style="
                    background: none;
                    border: none;
                    color: ${color.text};
                    cursor: pointer;
                    font-size: 20px;
                    line-height: 1;
                    padding: 0;
                ">&times;</button>
            </div>
        `);

    // Close button
    $notification.find(".notification-close").on("click", function () {
      $(this)
        .closest(".notification")
        .fadeOut(300, function () {
          $(this).remove();
        });
    });

    // Add to container
    $("#notification-container").append($notification);

    // Auto remove after 5 seconds
    setTimeout(() => {
      $notification.fadeOut(300, function () {
        $(this).remove();
      });
    }, 5000);
  }

  /**
   * Utility: Escape HTML to prevent XSS
   * @param {string} text - Text to escape
   * @returns {string}
   */
  escapeHtml(text) {
    if (!text) return "";
    const map = {
      "&": "&amp;",
      "<": "&lt;",
      ">": "&gt;",
      '"': "&quot;",
      "'": "&#039;",
    };
    return String(text).replace(/[&<>"']/g, (m) => map[m]);
  }

  /**
   * Utility: Format price
   * @param {number} price - Price value
   * @returns {string}
   */
  formatPrice(price) {
    const numPrice = parseFloat(price);
    if (isNaN(numPrice)) return "-";

    if (numPrice >= 1000) {
      return new Intl.NumberFormat("vi-VN", {
        style: "currency",
        currency: "VND",
      }).format(numPrice);
    }
    return new Intl.NumberFormat("en-US", {
      style: "currency",
      currency: "USD",
    }).format(numPrice);
  }

  /**
   * Utility: Parse amenities
   * @param {Array|string} amenities - Amenities data
   * @returns {string}
   */
  parseAmenities(amenities) {
    if (!amenities) return "-";
    if (Array.isArray(amenities)) return amenities.join(", ");
    if (typeof amenities === "string") return amenities;
    return "-";
  }

  /**
   * Utility: Format amenities for API
   * @param {string} input - Comma-separated input
   * @returns {string}
   */
  formatAmenitiesForApi(input) {
    if (!input) return "";
    return input
      .split(",")
      .map((a) => a.trim())
      .filter((a) => a)
      .join(",");
  }
}

// Add CSS animations
if ($("#notification-animations").length === 0) {
  $("head").append(`
        <style id="notification-animations">
            @keyframes slideIn {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
        </style>
    `);
}
