 <div aria-live="polite" aria-atomic="true" style="position: fixed; bottom: 20px; right: 20px; z-index: 1050;"
            id="toastContainer">
            <!-- Toast mẫu (sẽ được clone động) -->
            <div id="appointmentToastTemplate" class="toast" role="alert" data-bs-delay="180000"
                style="display: none;">
                <div class="toast-header bg-success text-white">
                    <strong class="me-auto">Thông báo lịch hẹn</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    <p id="toastMessage"></p>
                    <a id="toastDetailLink" href="#" class="btn btn-sm btn-primary mt-2">Xem chi tiết</a>
                </div>
            </div>
        </div>