document.addEventListener("DOMContentLoaded", function () {
  // —— Search overlay ——
  const icon = document.getElementById("search-icon");
  const overlay = document.getElementById("search-overlay");
  const closeBtn = document.querySelector(".close-btn");
  if (icon && overlay) {
    icon.addEventListener("click", e => {
      e.preventDefault();
      overlay.style.display = "flex";
    });
    // đóng
    closeBtn?.addEventListener("click", () => overlay.style.display = "none");
    overlay.addEventListener("click", e => {
      if (!e.target.closest(".search-content")) overlay.style.display = "none";
    });
    document.addEventListener("keydown", e => {
      if (e.key === "Escape") overlay.style.display = "none";
    });
  }

  // —— Nav background on scroll ——
  const nav = document.getElementById("mainNav");
  function updateNavScrolled() {
    if (window.scrollY > 100) nav.classList.add("scrolled");
    else nav.classList.remove("scrolled");
  }
  window.addEventListener("scroll", updateNavScrolled);
  updateNavScrolled(); // Call once on page load

  // —— Hero slider ——
  // const slides = document.querySelectorAll(".hero-slider .slide");
  // let idx = 0;
  // function showSlide(i) {
  //   slides.forEach(s => s.classList.remove("active"));
  //   slides[i].classList.add("active");
  // }
  // document.querySelector(".hero-slider .next")
  //   .addEventListener("click", () => { idx = (idx + 1) % slides.length; showSlide(idx); });
  // document.querySelector(".hero-slider .prev")
  //   .addEventListener("click", () => { idx = (idx - 1 + slides.length) % slides.length; showSlide(idx); });
  // setInterval(() => { idx = (idx + 1) % slides.length; showSlide(idx); }, 4000);
  const slides = document.querySelectorAll(".hero-slider .slide");
  let idx = 0;
  function showSlide(i) {
    slides.forEach(s => s.classList.remove("active"));
    slides[i].classList.add("active");
  }
  const nextSlideBtn = document.querySelector(".hero-slider .next");
  const prevSlideBtn = document.querySelector(".hero-slider .prev");
  if (nextSlideBtn && prevSlideBtn && slides.length) {
    nextSlideBtn.addEventListener("click", () => { idx = (idx + 1) % slides.length; showSlide(idx); });
    prevSlideBtn.addEventListener("click", () => { idx = (idx - 1 + slides.length) % slides.length; showSlide(idx); });
    setInterval(() => { idx = (idx + 1) % slides.length; showSlide(idx); }, 4000);
  }

  // —— Posts slider ——
  const wrapper = document.querySelector(".posts-wrapper");
  const track = document.querySelector(".posts");
  const posts = document.querySelectorAll(".post");
  const prevBtn = document.querySelector(".prev-posts");
  const nextBtn = document.querySelector(".next-posts");
  let currentSlide = 0;
  const postsPerSlide = 3;
  const totalSlides = Math.ceil(posts.length / postsPerSlide);
  function updateSlide() {
    const slideWidth = wrapper.clientWidth;
    track.style.transform = `translateX(-${currentSlide * slideWidth}px)`;
  }
  // nextBtn.addEventListener("click", () => {
  //   if (currentSlide < totalSlides - 1) { currentSlide++; updateSlide(); }
  // });
  // prevBtn.addEventListener("click", () => {
  //   if (currentSlide > 0) { currentSlide--; updateSlide(); }
  // });
  if (nextBtn && prevBtn && wrapper && track && posts.length) {
    nextBtn.addEventListener("click", () => {
      if (currentSlide < totalSlides - 1) { currentSlide++; updateSlide(); }
    });
    prevBtn.addEventListener("click", () => {
      if (currentSlide > 0) { currentSlide--; updateSlide(); }
    });
    window.addEventListener("resize", updateSlide);
  }
  window.addEventListener("resize", updateSlide);

  // —— Time picker ——
  const timeSelect = document.getElementById('timeBooking');
  if (timeSelect) {
    const pad = n => n.toString().padStart(2, '0');
    for (let t = 10 * 60; t <= 19 * 60 + 30; t += 30) {
      const h = Math.floor(t / 60), m = t % 60;
      const label = `${pad(h)}:${pad(m)}`;
      const opt = document.createElement('option');
      opt.value = opt.text = label;
      timeSelect.appendChild(opt);
    }
  }

  // —— Chat box ——
  const chatToggle = document.getElementById('chatToggle');
  const chatBox = document.getElementById('chatBox');
  const chatClose = document.getElementById('chatClose');
  if (chatToggle && chatBox && chatClose) {
    chatToggle.addEventListener('click', () => chatBox.style.display = 'block');
    chatClose.addEventListener('click', () => chatBox.style.display = 'none');
  }



});

// document.addEventListener('DOMContentLoaded', function () {
//   const appointmentDate = document.getElementById('appointment_date');
//   const appointmentTime = document.getElementById('appointment_time');
//   const branchContainer = document.getElementById('branch');
//   const branchInput = document.getElementById('branch_input');
//   const barberSelect = document.getElementById('barber');
//   const serviceSelect = document.getElementById('service');

//   const timeGrid = document.getElementById('timeGrid');

//   // Xử lý sự kiện nhấp chuột trên các ô giờ
//   if (timeGrid) {
//     timeGrid.querySelectorAll('.time-slot').forEach(slot => {
//       slot.addEventListener('click', function () {
//         // Xóa lớp 'selected' khỏi tất cả các ô giờ
//         timeGrid.querySelectorAll('.time-slot').forEach(s => s.classList.remove('selected'));
//         // Thêm lớp 'selected' cho ô giờ được nhấp
//         this.classList.add('selected');
//         // Cập nhật giá trị input ẩn
//         appointmentTime.value = this.getAttribute('data-value');
//         updateBarbers(branchContainer.value, appointmentDate.value, appointmentTime.value, serviceSelect.value);
//       });
//     });

//     // Đánh dấu ô giờ nếu có giá trị cũ (từ {{ old('appointment_time') }})
//     const oldTime = appointmentTime.value;
//     if (oldTime) {
//       const slot = timeGrid.querySelector(`.time-slot[data-value="${oldTime}"]`);
//       if (slot) slot.classList.add('selected');
//     }
//   }

//   let lastRequest = null;

//   function updateBarbers(branchId, date, time = null, serviceId = null) {
//     const requestKey = `${branchId}|${date}|${time}|${serviceId}`;
//     if (lastRequest === requestKey) return;
//     lastRequest = requestKey;

//     const prevBarber = barberSelect.value;
//     barberSelect.innerHTML = '<option value="">Chọn kĩ thuật viên</option>';

//     if (!branchId || !date) {
//       barberSelect.innerHTML = '<option value="">Vui lòng chọn chi nhánh và ngày</option>';
//       return;
//     }

//     let additional = [];
//     try {
//       additional = JSON.parse(additionalServicesInput.value);
//     } catch (e) { /* ignore nếu null */ }

//     // let url = `/get-available-barbers-by-date/${branchId}/${date}/${time ? encodeURIComponent(time) : 'null'}`;
//     // if (serviceId) url += `/${serviceId}`;
//     let url = `/get-available-barbers-by-date/${branchId}/${date}/${time ? encodeURIComponent(time) : 'null'}`;
//     if (serviceId) {
//       url += `/${serviceId}`;
//     }

//     // Đính kèm thêm services bổ sung
//     if (additional.length) {
//       additional.forEach(id => {
//         url += `?additional_services[]=${encodeURIComponent(id)}`;
//       });
//     }

//     fetch(url)
//       .then(r => { if (!r.ok) throw new Error(`Status ${r.status}`); return r.json(); })
//       .then(data => {
//         if (data.error) {
//           barberSelect.innerHTML = `<option value="">${data.error}</option>`;
//           return;
//         }
//         if (!data.length) {
//           barberSelect.innerHTML = '<option value="">Không có thợ khả dụng</option>';
//           return;
//         }
//         const added = new Set();
//         data.forEach(b => {
//           if (!added.has(b.id)) {
//             const opt = document.createElement('option');
//             opt.value = b.id;
//             opt.text = b.name;
//             barberSelect.appendChild(opt);
//             added.add(b.id);
//           }
//         });
//         if (prevBarber && added.has(prevBarber)) {
//           barberSelect.value = prevBarber;
//         }
//       })
//       .catch(err => {
//         console.error(err);
//         barberSelect.innerHTML = '<option value="">Lỗi khi tải danh sách thợ</option>';
//       });
//   }

//   // Bắt sự kiện khi click vào chi nhánh
//   branchContainer.querySelectorAll('.branch-item').forEach(item => {
//     item.addEventListener('click', () => {
//       // Reset tất cả active
//       branchContainer.querySelectorAll('.branch-item')
//         .forEach(el => el.classList.remove('active'));

//       // Active item này
//       item.classList.add('active');

//       // Lấy id và gán vào input ẩn
//       const branchId = item.getAttribute('data-id');
//       branchInput.value = branchId;

//       // Thêm branchContainer.value cho compatibility với code cũ
//       branchContainer.value = branchId;

//       // Gọi updateBarbers ngay sau khi chọn chi nhánh
//       updateBarbers(
//         branchId,
//         appointmentDate.value,
//         appointmentTime.value,
//         serviceSelect.value
//       );
//     });
//   });

//   // Các listener khác giữ nguyên
//   appointmentDate.addEventListener('change', () =>
//     updateBarbers(branchContainer.value, appointmentDate.value, appointmentTime.value, serviceSelect.value)
//   );

//   serviceSelect.addEventListener('change', () =>
//     updateBarbers(branchContainer.value, appointmentDate.value, appointmentTime.value, serviceSelect.value)
//   );

//   flatpickr(appointmentDate, {
//     // defaultDate: "today",
//     locale: 'vn',
//     minDate: "today",
//     maxDate: new Date().fp_incr(90),
//     dateFormat: "Y-m-d", // Thay đổi sang định dạng Laravel chấp nhận
//     disableMobile: true,
//     onChange: function (selectedDates, dateStr, instance) {
//       appointmentDate.value = dateStr; // Cập nhật trực tiếp
//       updateBarbers(branchContainer.value, dateStr, appointmentTime.value, serviceSelect.value);
//     }
//   });

//   // Trigger nếu old() đã chọn
//   if (serviceSelect.value) {
//     serviceSelect.dispatchEvent(new Event('change'));
//   }

//   const icon = document.getElementById("search-icon");
//   const overlay = document.getElementById("search-overlay");
//   const closeBtn = document.querySelector(".close-btn");
//   if (icon && overlay) {
//     icon.addEventListener("click", e => {
//       e.preventDefault();
//       overlay.style.display = "flex";
//     });
//     // đóng
//     closeBtn?.addEventListener("click", () => overlay.style.display = "none");
//     overlay.addEventListener("click", e => {
//       if (!e.target.closest(".search-content")) overlay.style.display = "none";
//     });
//     document.addEventListener("keydown", e => {
//       if (e.key === "Escape") overlay.style.display = "none";
//     });
//   }

//   // checkbox hiển thị khi đặt lịch hộ
//   const checkbox = document.getElementById('other_person');
//   const otherInfo = document.getElementById('other-info');
//   checkbox.addEventListener('change', function () {
//     otherInfo.style.display = this.checked ? 'block' : 'none';
//   });

// });

// document.addEventListener('DOMContentLoaded', function () {

//   const voucherSelect = document.getElementById('voucher_id');
//   const priceOutput = document.getElementById('totalPrice');
//   const totalAfterDiscount = document.getElementById('total_after_discount');
//   const durationOutput = document.getElementById('totalDuration');
//   const additionalServicesContainer = document.getElementById('additionalServicesContainer');

//   function getServiceInfo(option) {
//     if (!option) return { price: 0, duration: 0 };
//     return {
//       price: parseFloat(option.getAttribute('data-price')) || 0,
//       duration: parseInt(option.getAttribute('data-duration'), 10) || 0
//     };
//   }

//   function getAdditionalServicesInfo() {
//     let totalPrice = 0;
//     let totalDuration = 0;
//     if (!additionalServicesContainer) return { totalPrice, totalDuration };
//     const selects = additionalServicesContainer.querySelectorAll('.additional-service-select');
//     selects.forEach(select => {
//       const opt = select.options[select.selectedIndex];
//       if (opt && opt.value) {
//         const info = getServiceInfo(opt);
//         totalPrice += info.price;
//         totalDuration += info.duration;
//       }
//     });
//     return { totalPrice, totalDuration };
//   }

//   // function updateTotal() {
//   //   // Lấy thông tin dịch vụ
//   //   const mainOpt = serviceSelect.options[serviceSelect.selectedIndex];
//   //   const mainInfo = getServiceInfo(mainOpt);

//   //   // Lấy thông tin các dịch vụ phụ
//   //   const addInfo = getAdditionalServicesInfo();
//   //   // Tính tổng giá và tổng thời gian trước khi giảm giá
//   //   const totalPrice = mainInfo.price + addInfo.totalPrice;
//   //   const totalDuration = mainInfo.duration + addInfo.totalDuration;

//   //   // Lấy thông tin voucher
//   //   const voucherOpt = voucherSelect.options[voucherSelect.selectedIndex];
//   //   const discountType = voucherOpt?.getAttribute('data-discount-type') || '';
//   //   const discountValue = parseFloat(voucherOpt?.getAttribute('data-discount-value')) || 0;

//   //   // Tính giảm giá
//   //   let discount = 0;
//   //   let discountText = '';
//   //   if (voucherSelect.value && totalPrice > 0 && discountType) {
//   //     if (discountType === 'fixed') {
//   //       discount = discountValue;
//   //       discountText = `Đã giảm: <span>${discount.toLocaleString('vi-VN')} VNĐ</span>`;
//   //     } else if (discountType === 'percent') {
//   //       discount = Math.round(totalPrice * discountValue / 100);
//   //       discountText = `Đã giảm: <span>${discountValue}%</span> (<span>${discount.toLocaleString('vi-VN')} VNĐ</span>)`;
//   //     }
//   //   }

//   //   // Tính tổng sau giảm giá
//   //   let totalAfter = totalPrice - discount;
//   //   if (totalAfter < 0) totalAfter = 0;

//   //   // Cập nhật giao diện người dùng
//   //   priceOutput.textContent = totalAfter.toLocaleString('vi-VN') + ' vnđ';
//   //   durationOutput.textContent = totalDuration + ' Phút';
//   //   totalAfterDiscount.innerHTML = discount > 0
//   //     ? `<span class="text-success">${discountText}</span>`
//   //     : '';
//   // }

//   // // Listen for changes
//   // serviceSelect.addEventListener('change', updateTotal);
//   // voucherSelect.addEventListener('change', updateTotal);

//   // // Listen for changes in additional services
//   // if (additionalServicesContainer) {
//   //   additionalServicesContainer.addEventListener('change', function (e) {
//   //     if (e.target.classList.contains('additional-service-select')) {
//   //       updateTotal();
//   //     }
//   //   });
//   //   // Also update when add/remove additional service
//   //   new MutationObserver(updateTotal).observe(additionalServicesContainer, { childList: true, subtree: true });
//   // }

// });