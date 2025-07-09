
function initEchoListener() {
    if (typeof Echo !== 'undefined' && Echo !== null) {
        // console.log('Echo is defined:', Echo);
        const channel = Echo.channel('appointments');
        channel.subscribed(() => {
            // console.log('Subscribed to appointments channel');
        })
            .listen('NewAppointment', (event) => {
                // console.log('New appointment event received:', event);
                showToast(event);
                updatePendingCount(1); // Tăng badge khi có lịch mới
            })
            .listen('.NewAppointment', (event) => {
                // console.log('New appointment event received:', event);
                showToast(event);
                updatePendingCount(1); // Tăng badge khi có lịch mới
            })
            .listen('AppointmentConfirmed', (event) => {
                // console.log('Appointment confirmed:', event);
                updatePendingCount(-1); // Giảm badge khi lịch được xác nhận
            })
            .listen('App\\Events\\NewAppointment', (event) => {
                showToast(event);
                updatePendingCount(1); // Tăng badge khi có lịch mới
            })
            .listen('.App\\Events\\AppointmentConfirmed', (event) => {
                updatePendingCount(-1); // Giảm badge khi lịch được xác nhận
            })
            .error((error) => {
                console.error('Echo channel error:', error);
            });
    } else {
        console.error('Echo is not defined or null, retrying...');
        setTimeout(initEchoListener, 200);
    }
}

function updatePendingCount(change) {
    // Cập nhật badge trên dashboard
    const dashboardBadge = document.getElementById('pending-appointment-count');
    if (dashboardBadge) {
        let currentCount = parseInt(dashboardBadge.textContent) || 0;
        currentCount = Math.max(0, currentCount + change);
        dashboardBadge.textContent = currentCount;
        dashboardBadge.style.display = currentCount > 0 ? 'inline' : 'none';
        // console.log('Updated dashboard badge to:', currentCount);
    } else {
        console.error('Dashboard badge element not found');
    }

    // Cập nhật badge trong sidebar menu
    const sidebarBadges = document.getElementsByClassName('pending-appointment-count');
    if (sidebarBadges.length === 0) {
        console.error('Sidebar badge element not found. Check HTML for class "pending-appointment-count"');
    } else {
        Array.from(sidebarBadges).forEach(badge => {
            let currentCount = parseInt(badge.textContent) || 0;
            currentCount = Math.max(0, currentCount + change);
            badge.textContent = currentCount;
            badge.classList.toggle('hidden', currentCount === 0);
            console.log('Updated sidebar badge to:', currentCount);
        });
    }
}

function showToast(event) {
    try {
        const toastContainer = document.getElementById('toastContainer');
        const toastTemplate = document.getElementById('appointmentToastTemplate');

        const newToast = toastTemplate.cloneNode(true);
        newToast.id = 'appointmentToast-' + Date.now();
        newToast.style.display = 'block';

        const toastMessage = newToast.querySelector('#toastMessage');
        const toastDetailLink = newToast.querySelector('#toastDetailLink');

        toastMessage.textContent = event?.message || 'Không có thông tin chi tiết';
        toastDetailLink.href = `/admin/appointments/${event?.appointment_id || ''}`;

        toastContainer.appendChild(newToast);

        const toast = new bootstrap.Toast(newToast, {
            delay: 180000 // 3 phút
        });
        toast.show();
        // console.log('Toast shown successfully');

        newToast.addEventListener('hidden.bs.toast', () => {
            newToast.remove();
        });

    } catch (error) {
        console.error('Error showing toast:', error);
    }
}

document.addEventListener('DOMContentLoaded', initEchoListener);
