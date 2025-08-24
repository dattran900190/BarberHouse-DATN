function initEchoListener() {
    if (typeof Echo !== "undefined" && Echo !== null) {
        // console.log('Echo is defined:', Echo);
        
        // Lấy thông tin user từ meta tag hoặc biến global
        const userRole = document.querySelector('meta[name="user-role"]')?.getAttribute('content');
        const userBranchId = document.querySelector('meta[name="user-branch-id"]')?.getAttribute('content');
        
        // Admin chính subscribe vào channel chung
        if (userRole === 'admin') {
            const channel = Echo.channel("appointments");
            channel
                .subscribed(() => {
                    // console.log('Admin subscribed to appointments channel');
                })
                .listen("NewAppointment", (event) => {
                    // console.log('New appointment event received:', event);
                    showToast(event);
                    updatePendingCount(1); // Tăng badge khi có lịch mới
                })
                .listen(".NewAppointment", (event) => {
                    // console.log('New appointment event received:', event);
                    showToast(event);
                    updatePendingCount(1); // Tăng badge khi có lịch mới
                })
                .listen("AppointmentConfirmed", (event) => {
                    // console.log('Appointment confirmed:', event);
                    updatePendingCount(-1); // Giảm badge khi lịch được xác nhận
                })
                .listen("App\\Events\\NewAppointment", (event) => {
                    showToast(event);
                    updatePendingCount(1); // Tăng badge khi có lịch mới
                })
                .listen(".App\\Events\\AppointmentConfirmed", (event) => {
                    updatePendingCount(-1); // Giảm badge khi lịch được xác nhận
                })
                .error((error) => {
                    console.error("Echo channel error:", error);
                });
        }
        
        // Admin chi nhánh subscribe vào channel riêng của chi nhánh
        if (userRole === 'admin_branch' && userBranchId) {
            const branchChannel = Echo.channel(`branch.${userBranchId}`);
            branchChannel
                .subscribed(() => {
                    console.log(`Admin branch subscribed to branch.${userBranchId} channel`);
                })
                .listen("App\\Events\\AppointmentCreated", (event) => {
                    // Chỉ hiển thị thông báo nếu lịch hẹn thuộc chi nhánh của admin
                    if (event.branch_id == userBranchId) {
                        showToast(event);
                        updatePendingCount(1); // Tăng badge khi có lịch mới
                    }
                })
                .error((error) => {
                    console.error("Echo branch channel error:", error);
                });
        }
    } else {
        console.error("Echo is not defined or null, retrying...");
        setTimeout(initEchoListener, 200);
    }
}

function updatePendingCount(change) {
    // Cập nhật badge trên dashboard
    const dashboardBadge = document.getElementById("pending-appointment-count");
    if (dashboardBadge) {
        let currentCount = parseInt(dashboardBadge.textContent) || 0;
        currentCount = Math.max(0, currentCount + change);
        dashboardBadge.textContent = currentCount;
        dashboardBadge.style.display = currentCount > 0 ? "inline" : "none";
        // console.log('Updated dashboard badge to:', currentCount);
    } else {
        console.error("Dashboard badge element not found");
    }

    // Cập nhật badge trong sidebar menu
    const sidebarBadges = document.getElementsByClassName(
        "pending-appointment-count"
    );
    if (sidebarBadges.length === 0) {
        console.error(
            'Sidebar badge element not found. Check HTML for class "pending-appointment-count"'
        );
    } else {
        Array.from(sidebarBadges).forEach((badge) => {
            let currentCount = parseInt(badge.textContent) || 0;
            currentCount = Math.max(0, currentCount + change);
            badge.textContent = currentCount;
            badge.classList.toggle("hidden", currentCount === 0);
            console.log("Updated sidebar badge to:", currentCount);
        });
    }
}

function showToast(event) {
    try {
        const toastContainer = document.getElementById("toastContainer");
        const toastTemplate = document.getElementById(
            "appointmentToastTemplate"
        );

        const newToast = toastTemplate.cloneNode(true);
        newToast.id = "appointmentToast-" + Date.now();
        newToast.style.display = "block";

        const toastMessage = newToast.querySelector("#toastMessage");
        const toastDetailLink = newToast.querySelector("#toastDetailLink");

        toastMessage.textContent =
            event?.message || "Không có thông tin chi tiết";
        toastDetailLink.href = `/admin/appointments/${
            event?.appointment_id || ""
        }`;

        toastContainer.appendChild(newToast);

        const toast = new bootstrap.Toast(newToast, {
            delay: 30000, // 3 phút
        });
        toast.show();
        // console.log('Toast shown successfully');

        newToast.addEventListener("hidden.bs.toast", () => {
            newToast.remove();
        });
    } catch (error) {
        console.error("Error showing toast:", error);
    }
}

function initOrderEchoListener() {
    if (typeof Echo !== 'undefined' && Echo !== null) {
        // Lấy thông tin user từ meta tag
        const userRole = document.querySelector('meta[name="user-role"]')?.getAttribute('content');
        
        // Chỉ admin chính mới nhận thông báo đặt hàng, admin chi nhánh không nhận
        if (userRole === 'admin') {
            const orderChannel = Echo.channel('orders');
            orderChannel
                .listen('NewOrderCreated', (event) => {
                    showOrderToast(event);
                    updatePendingOrderCount(1); // Tăng badge khi có đơn hàng mới
                })
                .error((error) => {
                    console.error('Echo order channel error:', error);
                });
        }
    } else {
        setTimeout(initOrderEchoListener, 200);
    }
}

function updatePendingOrderCount(change) {
    const orderBadge = document.getElementById('pending-order-count');
    if (orderBadge) {
        let currentCount = parseInt(orderBadge.textContent) || 0;
        currentCount = Math.max(0, currentCount + change);
        orderBadge.textContent = currentCount;
        orderBadge.style.display = currentCount > 0 ? 'inline' : 'none';
    }
}

function showOrderToast(event) {
    try {
        // Nếu có template riêng cho đơn hàng thì dùng, không thì dùng chung với appointment
        const toastContainer = document.getElementById('toastContainer');
        let toastTemplate = document.getElementById('orderToastTemplate');
        if (!toastTemplate) {
            toastTemplate = document.getElementById('appointmentToastTemplate');
        }
        const newToast = toastTemplate.cloneNode(true);
        newToast.id = 'orderToast-' + Date.now();
        newToast.style.display = 'block';
        const toastMessage = newToast.querySelector('#toastMessage');
        const toastDetailLink = newToast.querySelector('#toastDetailLink');
        toastMessage.textContent = event?.message || 'Có đơn hàng mới';
        toastDetailLink.href = `/admin/orders/${event?.order_id || ''}`;
        toastContainer.appendChild(newToast);
        const toast = new bootstrap.Toast(newToast, { delay: 180000 });
        toast.show();
        newToast.addEventListener('hidden.bs.toast', () => {
            newToast.remove();
        });
    } catch (error) {
        console.error('Error showing order toast:', error);
    }
}

function initRefundListener() {
    if (typeof Echo !== "undefined" && Echo) {
        Echo.channel("admin.refunds").listen(".refund.created", (data) => {
            console.log("Yêu cầu hoàn tiền mới:", data);
            showRefundToast(data);
            updateRefundBadge();
        });
    } else {
        console.error("Echo chưa được khởi tạo (refund)!");
    }
}

function showRefundToast(data) {
    const toast = document.getElementById("refund-toast");
    const msg = document.getElementById("toast-message-refund"); // ✅ Không dùng ID trùng
    if (toast && msg) {
        msg.textContent = `Yêu cầu hoàn tiền từ ${data.user_name}`;
        new bootstrap.Toast(toast).show();
    }
}

function updateRefundBadge() {
    const badge = document.getElementById("sidebar-pending-refund-count");
    if (badge) {
        let count = parseInt(badge.textContent) || 0;
        count++;
        badge.textContent = count;
        badge.style.display = "inline";
    }
}


document.addEventListener('DOMContentLoaded', function() {
    initEchoListener();
    initOrderEchoListener();
    initRefundListener();
});
