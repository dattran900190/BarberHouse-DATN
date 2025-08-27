<!--   Core JS Files   -->
<script src="{{ asset('kaiadmin-lite-1.2.0/assets/js/core/jquery-3.7.1.min.js') }} "></script>
<script src="{{ asset('kaiadmin-lite-1.2.0/assets/js/core/popper.min.js') }} "></script>
<script src="{{ asset('kaiadmin-lite-1.2.0/assets/js/core/bootstrap.min.js') }} "></script>

<!-- jQuery Scrollbar -->
<script src="{{ asset('kaiadmin-lite-1.2.0/assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }} "></script>

<!-- Chart JS -->
<script src="{{ asset('kaiadmin-lite-1.2.0/assets/js/plugin/chart.js/chart.min.js') }} "></script>

<!-- jQuery Sparkline -->
<script src="{{ asset('kaiadmin-lite-1.2.0/assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js') }} "></script>

<!-- Chart Circle -->
<script src="{{ asset('kaiadmin-lite-1.2.0/assets/js/plugin/chart-circle/circles.min.js') }} "></script>

<!-- Datatables -->
<script src="{{ asset('kaiadmin-lite-1.2.0/assets/js/plugin/datatables/datatables.min.js') }} "></script>

<!-- Bootstrap Notify -->
<script src="{{ asset('kaiadmin-lite-1.2.0/assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js') }} "></script>

<!-- jQuery Vector Maps -->
<script src="{{ asset('kaiadmin-lite-1.2.0/assets/js/plugin/jsvectormap/jsvectormap.min.js') }}"></script>
<script src="{{ asset('kaiadmin-lite-1.2.0/assets/js/plugin/jsvectormap/world.js') }} "></script>

<!-- Sweet Alert -->
<script src="{{ asset('kaiadmin-lite-1.2.0/assets/js/plugin/sweetalert/sweetalert.min.js') }}"></script>

<!-- Kaiadmin JS -->
<script src="{{ asset('kaiadmin-lite-1.2.0/assets/js/kaiadmin.min.js') }} "></script>

<!-- Kaiadmin DEMO methods, don't include it in your project! -->
<script src="{{ asset('kaiadmin-lite-1.2.0/assets/js/setting-demo.js') }} "></script>
<script src="{{ asset('kaiadmin-lite-1.2.0/assets/js/demo.js') }} "></script>
<script>
    $("#lineChart").sparkline([102, 109, 120, 99, 110, 105, 115], {
        type: "line",
        height: "70",
        width: "100%",
        lineWidth: "2",
        lineColor: "#177dff",
        fillColor: "rgba(23, 125, 255, 0.14)",
    });

    $("#lineChart2").sparkline([99, 125, 122, 105, 110, 124, 115], {
        type: "line",
        height: "70",
        width: "100%",
        lineWidth: "2",
        lineColor: "#f3545d",
        fillColor: "rgba(243, 84, 93, .14)",
    });

    $("#lineChart3").sparkline([105, 103, 123, 100, 95, 105, 115], {
        type: "line",
        height: "70",
        width: "100%",
        lineWidth: "2",
        lineColor: "#ffa534",
        fillColor: "rgba(255, 165, 52, .14)",
    });
</script>

<script>
    // 2. Lắng nghe sự kiện từ Pusher
    const pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
        cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
        encrypted: true
    });

    // Lấy thông tin user từ meta tag
    const userRole = document.querySelector('meta[name="user-role"]')?.getAttribute('content');
    const userBranchId = document.querySelector('meta[name="user-branch-id"]')?.getAttribute('content');

    // Admin chính subscribe vào channel chung
    if (userRole === 'admin') {
        const channel = pusher.subscribe('appointments');
        channel.bind('App\\Events\\AppointmentCreated', function(data) {
            // console.log('Admin nhận được lịch hẹn mới:', data);

            // Thêm lịch hẹn mới vào bảng
            showAppointmentNotification(data);

            // Hiển thị toast thông báo sử dụng template có sẵn
            // showToastFromTemplate(data);
        });
    }

    // Admin chi nhánh subscribe vào channel riêng của chi nhánh
    if (userRole === 'admin_branch' && userBranchId) {
        const branchChannel = pusher.subscribe('branch.' + userBranchId);
        branchChannel.bind('App\\Events\\AppointmentCreated', function(data) {
            // console.log(`Admin chi nhánh ${userBranchId} nhận được lịch hẹn mới:`, data);

            // Chỉ hiển thị thông báo nếu lịch hẹn thuộc chi nhánh của admin
            if (data.branch_id == userBranchId) {
                // Thêm lịch hẹn mới vào bảng
                showAppointmentNotification(data);

                // Hiển thị toast thông báo sử dụng template có sẵn
                showToastFromTemplate(data);
            }
        });
    }

    // ===== REALTIME PRODUCT UPDATES =====
    // Lắng nghe Event ProductUpdated để cập nhật realtime
    const productChannel = pusher.subscribe('products');
    productChannel.bind('ProductUpdated', function(data) {
        console.log('🎯 ProductUpdated event received:', data);
        
        // Hiển thị thông báo realtime
        showProductRealtimeNotification();
        
        // Cập nhật UI nếu đang ở trang products
        updateProductUI();
    });

    // Function hiển thị thông báo realtime cho sản phẩm
    function showProductRealtimeNotification() {
        // Kiểm tra xem có SweetAlert2 không
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: '🔄 Cập nhật Realtime!',
                text: 'Sản phẩm đã được cập nhật!',
                icon: 'info',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        } else {
            // Fallback: Sử dụng alert thông thường
            alert('🔄 Sản phẩm đã được cập nhật realtime!');
        }
    }

    // Function cập nhật UI sản phẩm
    function updateProductUI() {
        // Kiểm tra xem có đang ở trang products không
        const currentPath = window.location.pathname;
        
        if (currentPath.includes('/admin/products')) {
            // Nếu đang ở trang danh sách sản phẩm
            if (currentPath.endsWith('/admin/products') || currentPath.includes('/admin/products?')) {
                console.log('🔄 Đang ở trang danh sách sản phẩm, cập nhật UI...');
                
                // Hiển thị loading
                const tableBody = document.querySelector('#products-table tbody');
                if (tableBody) {
                    tableBody.style.opacity = '0.6';
                }
                
                // Reload trang sau 1 giây để cập nhật dữ liệu
                setTimeout(() => {
                    location.reload();
                }, 1000);
            }
            // Nếu đang ở trang chi tiết sản phẩm
            else if (currentPath.includes('/admin/products/') && !currentPath.includes('/edit')) {
                console.log('🔄 Đang ở trang chi tiết sản phẩm, hỏi user có muốn refresh...');
                
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: '🔄 Cập nhật Realtime!',
                        text: 'Sản phẩm đã được cập nhật! Bạn có muốn refresh trang không?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Có, Refresh!',
                        cancelButtonText: 'Không, để nguyên'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                } else {
                    if (confirm('🔄 Sản phẩm đã được cập nhật! Bạn có muốn refresh trang không?')) {
                        location.reload();
                    }
                }
            }
        }
    }

    function showAppointmentNotification(data) {
        // Tạo row mới cho bảng appointments
        const newRow = createAppointmentRow(data);

        // Thêm vào đầu bảng pending (vì lịch hẹn mới thường có status 'pending')
        const pendingTableBody = document.querySelector('#pending tbody');
        if (pendingTableBody) {
            // Kiểm tra xem có row "Không có lịch hẹn nào" không
            const noDataRow = pendingTableBody.querySelector('td[colspan]');
            if (noDataRow) {
                noDataRow.parentElement.remove(); // Xóa row "Không có dữ liệu"
            }

            // Thêm row mới vào đầu bảng
            pendingTableBody.insertBefore(newRow, pendingTableBody.firstChild);

            // Cập nhật số thứ tự
            updateRowNumbers('#pending tbody');

            // Cập nhật badge count nếu có
            updatePendingBadge();

            // Chuyển đến tab pending nếu đang ở tab khác
            const pendingTab = document.querySelector('#pending-tab');
            if (pendingTab && !pendingTab.classList.contains('active')) {
                // Kích hoạt tab pending
                const tab = new bootstrap.Tab(pendingTab);
                tab.show();
            }
        }
    }

    function createAppointmentRow(data) {
        const row = document.createElement('tr');
        row.setAttribute('data-appointment-id', data.id); // Thêm attribute để dễ dàng tìm kiếm

        // Xác định payment method text
        const paymentMethodText = data.payment_method === 'vnpay' ? 'Thanh toán VNPAY' : 'Thanh toán tại tiệm';

        // Xác định payment status
        let paymentStatus = 'unpaid';
        let paymentStatusText = 'Chưa thanh toán';
        let paymentStatusClass = 'warning';

        if (data.payment_method === 'vnpay') {
            paymentStatus = 'paid';
            paymentStatusText = 'Đã thanh toán';
            paymentStatusClass = 'success';
        }

        // Tạo HTML cho dịch vụ bổ sung
        let additionalServicesHtml = '';
        if (data.additional_services && data.additional_services.length > 0) {
            additionalServicesHtml = `
                    <div class="mt-2">
                        <strong class="text-muted">Dịch vụ bổ xung:</strong>
                        <ul class="mb-0 mt-1 ps-3 text-muted">
                            ${data.additional_services.map(service => `<li>${service}</li>`).join('')}
                        </ul>
                    </div>
                `;
        }

        // Tạo HTML cho nút hành động
        const actionsHtml = `
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary" type="button" id="actionMenu${data.id}"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="actionMenu${data.id}">
                        <li>
                            <a href="/admin/appointments/${data.id}" class="dropdown-item">
                                <i class="fas fa-eye me-2"></i> Xem
                            </a>
                        </li>
                        <li>
                            <a href="/admin/appointments/${data.id}/edit" class="dropdown-item">
                                <i class="fas fa-edit me-2"></i> Sửa
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <button type="button" class="dropdown-item text-success confirm-btn" data-id="${data.id}">
                                <i class="fas fa-check me-2"></i> Xác nhận
                            </button>
                        </li>
                        <li>
                            <button type="button" class="dropdown-item text-danger cancel-btn" data-id="${data.id}">
                                <i class="fas fa-times-circle me-2"></i> Hủy
                            </button>
                        </li>
                    </ul>
                </div>
            `;

        row.innerHTML = `
                <td>Mới</td>
                <td>${data.appointment_code}</td>
                <td>${data.user_name}</td>
                <td>${data.phone}</td>
                <td>${data.barber_name}</td>
                <td>
                    ${data.service_name}
                    ${additionalServicesHtml}
                </td>
                <td>${paymentMethodText}</td>
                <td>${data.appointment_time}</td>
                <td><span class="badge bg-warning">Chờ xác nhận</span></td>
                <td><span class="badge bg-${paymentStatusClass}">${paymentStatusText}</span></td>
                <td class="text-center">${actionsHtml}</td>
            `;

        // Thêm event listener cho các nút sau khi row được thêm vào DOM
        setTimeout(() => {
            // Event listener cho nút xác nhận
            const confirmBtn = row.querySelector('.confirm-btn');
            if (confirmBtn) {
                confirmBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const appointmentId = this.getAttribute('data-id');
                    confirmAppointment(appointmentId);
                });
            }

            // Event listener cho nút hủy
            const cancelBtn = row.querySelector('.cancel-btn');
            if (cancelBtn) {
                cancelBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const appointmentId = this.getAttribute('data-id');
                    cancelAppointment(appointmentId);
                });
            }

            // Khởi tạo Bootstrap dropdown
            const dropdownToggle = row.querySelector('[data-bs-toggle="dropdown"]');
            if (dropdownToggle) {
                new bootstrap.Dropdown(dropdownToggle);
            }
        }, 100);

        return row;
    }

    function updateRowNumbers(tableSelector) {
        const rows = document.querySelectorAll(`${tableSelector} tr:not([style*="display: none"])`);
        rows.forEach((row, index) => {
            const firstCell = row.querySelector('td:first-child');
            if (firstCell) {
                firstCell.textContent = index + 1;
            }
        });
    }

    function showToastFromTemplate(data) {
        // Sử dụng toast template có sẵn từ allToast.blade.php
        const toastContainer = document.getElementById('toastContainer');
        const toastTemplate = document.getElementById('appointmentToastTemplate');

        if (toastContainer && toastTemplate) {
            // Clone toast template
            const newToast = toastTemplate.cloneNode(true);
            newToast.id = 'appointmentToast-' + Date.now();
            newToast.style.display = 'block';

            // Cập nhật nội dung toast
            const toastMessage = newToast.querySelector('#toastMessage');
            const toastDetailLink = newToast.querySelector('#toastDetailLink');

            if (toastMessage) {
                // Sử dụng mẫu đơn giản như yêu cầu
                toastMessage.textContent = `Có lịch hẹn mới từ ${data.user_name}`;
            }

            if (toastDetailLink) {
                toastDetailLink.href = `/admin/appointments/${data.id}`;
                toastDetailLink.innerHTML = 'Xem chi tiết';
            }

            // Thêm toast vào container
            toastContainer.appendChild(newToast);

            // Khởi tạo Bootstrap toast
            const toast = new bootstrap.Toast(newToast, {
                delay: 30000, // 30 giây
                autohide: true
            });

            // Hiển thị toast
            toast.show();

            // Xóa toast sau khi ẩn
            newToast.addEventListener('hidden.bs.toast', function() {
                if (newToast.parentElement) {
                    newToast.remove();
                }
            });

            // Thêm hiệu ứng highlight cho row mới trong bảng
            highlightNewAppointmentRow(data.id);
        }
    }

    // Hàm xác nhận lịch hẹn
    function confirmAppointment(appointmentId) {
        Swal.fire({
            title: 'Xác nhận lịch hẹn?',
            text: 'Bạn có chắc chắn muốn xác nhận lịch hẹn này?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Xác nhận',
            cancelButtonText: 'Hủy',
            customClass: {
                popup: 'custom-swal-popup'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Hiển thị loading
                Swal.fire({
                    title: 'Đang xử lý...',
                    text: 'Vui lòng chờ trong giây lát.',
                    icon: 'info',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    customClass: {
                        popup: 'custom-swal-popup'
                    },
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Gửi request xác nhận
                fetch(`/admin/appointments/${appointmentId}/confirm`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        Swal.close();
                        if (data.success) {
                            // Cập nhật badge realtime
                            const sidebarBadge = document.getElementById('pending-appointment-count');
                            if (sidebarBadge) {
                                let currentCount = parseInt(sidebarBadge.textContent) || 0;
                                currentCount = Math.max(0, currentCount - 1);
                                sidebarBadge.textContent = currentCount;
                                sidebarBadge.style.display = currentCount > 0 ? 'inline' : 'none';
                                console.log('Updated pending count after confirmation:', currentCount);
                            }

                            // Xóa row khỏi bảng pending
                            const row = document.querySelector(`tr[data-appointment-id="${appointmentId}"]`);
                            if (row) {
                                row.remove();
                                updateRowNumbers('#pending tbody');
                            }

                            Swal.fire({
                                title: 'Thành công!',
                                text: data.message,
                                icon: 'success',
                                customClass: {
                                    popup: 'custom-swal-popup'
                                }
                            });
                        } else {
                            Swal.fire({
                                title: 'Thất bại!',
                                text: data.message || 'Có lỗi xảy ra khi xác nhận lịch hẹn.',
                                icon: 'error',
                                customClass: {
                                    popup: 'custom-swal-popup'
                                }
                            });
                        }
                    })
                    .catch(err => {
                        Swal.close();
                        Swal.fire({
                            title: 'Lỗi!',
                            text: 'Không thể xác nhận lịch hẹn. Vui lòng thử lại.',
                            icon: 'error',
                            customClass: {
                                popup: 'custom-swal-popup'
                            }
                        });
                        console.error(err);
                    });
            }
        });
    }

    // Hàm hủy lịch hẹn
    function cancelAppointment(appointmentId) {
        Swal.fire({
            title: 'Hủy lịch hẹn?',
            text: 'Bạn có chắc chắn muốn hủy lịch hẹn này?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Hủy lịch',
            cancelButtonText: 'Đóng',
            input: 'text',
            inputPlaceholder: 'Nhập lý do hủy (tùy chọn)...',
            customClass: {
                popup: 'custom-swal-popup'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Hiển thị loading
                Swal.fire({
                    title: 'Đang xử lý...',
                    text: 'Vui lòng chờ trong giây lát.',
                    icon: 'info',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    customClass: {
                        popup: 'custom-swal-popup'
                    },
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Gửi request hủy lịch
                fetch(`/admin/appointments/${appointmentId}/cancel`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            cancellation_reason: result.value || 'Không có lý do cụ thể'
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        Swal.close();
                        if (data.success) {
                            // Cập nhật badge realtime
                            const sidebarBadge = document.getElementById('pending-appointment-count');
                            if (sidebarBadge) {
                                let currentCount = parseInt(sidebarBadge.textContent) || 0;
                                currentCount = Math.max(0, currentCount - 1);
                                sidebarBadge.textContent = currentCount;
                                sidebarBadge.style.display = currentCount > 0 ? 'inline' : 'none';
                                console.log('Updated pending count after cancellation:', currentCount);
                            }

                            // Xóa row khỏi bảng pending
                            const row = document.querySelector(`tr[data-appointment-id="${appointmentId}"]`);
                            if (row) {
                                row.remove();
                                updateRowNumbers('#pending tbody');
                            }

                            Swal.fire({
                                title: 'Thành công!',
                                text: data.message,
                                icon: 'success',
                                customClass: {
                                    popup: 'custom-swal-popup'
                                }
                            });
                        } else {
                            Swal.fire({
                                title: 'Thất bại!',
                                text: data.message || 'Có lỗi xảy ra khi hủy lịch hẹn.',
                                icon: 'error',
                                customClass: {
                                    popup: 'custom-swal-popup'
                                }
                            });
                        }
                    })
                    .catch(err => {
                        Swal.close();
                        Swal.fire({
                            title: 'Lỗi!',
                            text: 'Không thể hủy lịch hẹn. Vui lòng thử lại.',
                            icon: 'error',
                            customClass: {
                                popup: 'custom-swal-popup'
                            }
                        });
                        console.error(err);
                    });
            }
        });
    }

    // Hàm cập nhật badge pending count
    function updatePendingBadge() {
        const pendingTableBody = document.querySelector('#pending tbody');
        if (pendingTableBody) {
            const rowCount = pendingTableBody.querySelectorAll('tr').length;

            // Cập nhật badge trong sidebar
            const sidebarBadge = document.getElementById('pending-appointment-count');
            if (sidebarBadge) {
                sidebarBadge.textContent = rowCount;
                sidebarBadge.style.display = rowCount > 0 ? 'inline' : 'none';
                console.log('Updated sidebar badge to:', rowCount);
            }

            // Cập nhật badge trong tab
            const pendingTab = document.querySelector('#pending-tab');
            if (pendingTab) {
                let badge = pendingTab.querySelector('.position-relative .position-absolute');
                if (!badge) {
                    // Tạo badge mới nếu chưa có
                    const span = document.createElement('span');
                    span.className = 'position-relative';
                    span.innerHTML = `
                            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                                <span class="visually-hidden">New alerts</span>
                            </span>
                        `;
                    pendingTab.appendChild(span);
                    badge = span.querySelector('.position-absolute');
                }

                // Cập nhật số lượng
                if (rowCount > 0) {
                    badge.style.display = 'block';
                } else {
                    badge.style.display = 'none';
                }
            }
        }
    }

    function showToastFromTemplate(data) {
        // Sử dụng toast template có sẵn từ allToast.blade.php
        const toastContainer = document.getElementById('toastContainer');
        const toastTemplate = document.getElementById('appointmentToastTemplate');

        if (toastContainer && toastTemplate) {
            // Clone toast template
            const newToast = toastTemplate.cloneNode(true);
            newToast.id = 'appointmentToast-' + Date.now();
            newToast.style.display = 'block';

            // Cập nhật nội dung toast
            const toastMessage = newToast.querySelector('#toastMessage');
            const toastDetailLink = newToast.querySelector('#toastDetailLink');

            if (toastMessage) {
                // Sử dụng mẫu đơn giản như yêu cầu
                toastMessage.textContent = `Có lịch hẹn mới từ ${data.user_name}`;
            }

            if (toastDetailLink) {
                toastDetailLink.href = `/admin/appointments/${data.id}`;
                toastDetailLink.innerHTML = 'Xem chi tiết';
            }

            // Thêm toast vào container
            toastContainer.appendChild(newToast);

            // Khởi tạo Bootstrap toast
            const toast = new bootstrap.Toast(newToast, {
                delay: 30000, // 30 giây
                autohide: true
            });

            // Hiển thị toast
            toast.show();

            // Xóa toast sau khi ẩn
            newToast.addEventListener('hidden.bs.toast', function() {
                if (newToast.parentElement) {
                    newToast.remove();
                }
            });

            // Thêm hiệu ứng highlight cho row mới trong bảng
            highlightNewAppointmentRow(data.id);
        }
    }
</script>
