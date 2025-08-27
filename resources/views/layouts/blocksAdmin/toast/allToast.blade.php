 <div aria-live="polite" aria-atomic="true" style="position: fixed; bottom: 20px; right: 20px; z-index: 1050;"
     id="toastContainer">
     <!-- Toast mẫu (sẽ được clone động) -->
     <div id="appointmentToastTemplate" class="toast" role="alert" data-bs-delay="30000" style="display: none;">
         <div class="toast-header bg-success text-white">
             <strong class="me-auto">Thông báo mới</strong>
             <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
         </div>
         <div class="toast-body">
             <p id="toastMessage"></p>
             <a id="toastDetailLink" href="#" class="btn btn-sm btn-primary mt-2">Xem chi tiết</a>
         </div>
     </div>
 </div>

 {{-- allToast.blade.php --}}
 <div id="toast-container" class="position-fixed" style="top: 20px; right: 20px; z-index: 9999;">
     <div id="refund-toast" class="toast align-items-center text-white bg-success border-0" role="alert"
         aria-live="assertive" aria-atomic="true">
         <div class="d-flex">
             <div class="toast-body">
                 <i class="fas fa-bell me-2"></i>
                 <span id="toast-message-refund"></span>
             </div>
             <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                 aria-label="Close"></button>
         </div>
     </div>
 </div>
