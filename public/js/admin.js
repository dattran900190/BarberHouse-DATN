 // Xử lý nút "Đánh dấu No-show"
    document.querySelectorAll('.no-show-btn').forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            const appointmentId = this.getAttribute('data-id');

            // Cửa sổ nhập lý do no-show (tùy chọn)
            Swal.fire({
                title: 'Đánh dấu lịch hẹn là No-show',
                text: 'Vui lòng nhập lý do (tùy chọn)',
                input: 'textarea',
                inputPlaceholder: 'Nhập lý do no-show (tối đa 255 ký tự)...',
                inputAttributes: {
                    'rows': 4
                },
                icon: 'warning',
                width: '400px',
                customClass: {
                    popup: 'custom-swal-popup'
                },
                showCancelButton: true,
                confirmButtonText: 'Xác nhận',
                cancelButtonText: 'Hủy',
                inputValidator: (value) => {
                    if (value && value.length > 255) {
                        return 'Lý do không được vượt quá 255 ký tự!';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Cửa sổ loading
                    Swal.fire({
                        title: 'Đang xử lý...',
                        text: 'Vui lòng chờ trong giây lát.',
                        allowOutsideClick: false,
                        width: '400px',
                        customClass: {
                            popup: 'custom-swal-popup'
                        },
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Gửi yêu cầu AJAX
                    fetch('{{ route('appointments.no-show', ':id') }}'.replace(':id', appointmentId), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            no_show_reason: result.value || 'Khách hàng không đến'
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! Status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Đóng cửa sổ loading
                        Swal.close();

                        if (data.success) {
                            Swal.fire({
                                title: 'Thành công!',
                                text: data.message,
                                icon: 'success',
                                width: '400px',
                                customClass: {
                                    popup: 'custom-swal-popup'
                                }
                            }).then(() => {
                                window.location.href = '{{ route('appointments.index') }}';
                            });
                        } else {
                            Swal.fire({
                                title: 'Lỗi!',
                                text: data.message,
                                icon: 'error',
                                width: '400px',
                                customClass: {
                                    popup: 'custom-swal-popup'
                                }
                            });
                        }
                    })
                    .catch(error => {
                        // Đóng cửa sổ loading
                        Swal.close();
                        console.error('Lỗi AJAX:', error);
                        Swal.fire({
                            title: 'Lỗi!',
                            text: 'Đã có lỗi xảy ra: ' + error.message,
                            icon: 'error',
                            width: '400px',
                            customClass: {
                                popup: 'custom-swal-popup'
                            }
                        });
                    });
                }
            });
        });
    });