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
  window.addEventListener("scroll", () => {
    if (window.scrollY > 100) nav.classList.add("scrolled");
    else nav.classList.remove("scrolled");
  });

  // —— Hero slider ——
  const slides = document.querySelectorAll(".hero-slider .slide");
  let idx = 0;
  function showSlide(i) {
    slides.forEach(s => s.classList.remove("active"));
    slides[i].classList.add("active");
  }
  document.querySelector(".hero-slider .next")
    .addEventListener("click", () => { idx = (idx + 1) % slides.length; showSlide(idx); });
  document.querySelector(".hero-slider .prev")
    .addEventListener("click", () => { idx = (idx - 1 + slides.length) % slides.length; showSlide(idx); });
  setInterval(() => { idx = (idx + 1) % slides.length; showSlide(idx); }, 4000);

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
  nextBtn.addEventListener("click", () => {
    if (currentSlide < totalSlides - 1) { currentSlide++; updateSlide(); }
  });
  prevBtn.addEventListener("click", () => {
    if (currentSlide > 0) { currentSlide--; updateSlide(); }
  });
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

document.addEventListener('DOMContentLoaded', function () {
  // const appointmentDate = document.getElementById('appointment_date');
  // const appointmentTime = document.getElementById('appointment_time');
  // const branchSelect = document.getElementById('branch');
  // const barberSelect = document.getElementById('barber');
  // const serviceSelect = document.getElementById('service');

  // let lastRequest = null; // Track last request to avoid duplicates

  // function updateBarbers(branchId, date, time = null, serviceId = null) {
  //   const requestKey = `${branchId}-${date}-${time}-${serviceId}`;
  //   if (lastRequest === requestKey) {
  //     return;
  //   }
  //   lastRequest = requestKey;

  //   barberSelect.innerHTML = '<option value="">-- Chọn thợ --</option>';

  //   if (!branchId || !date) {
  //     barberSelect.innerHTML = '<option value="">Vui lòng chọn chi nhánh và ngày</option>';
  //     return;
  //   }

  //   let url = `/get-available-barbers-by-date/${branchId}/${date}`;
  //   if (time) {
  //     url += `/${encodeURIComponent(time)}`;
  //   } else {
  //     url += '/null'; // Gửi $time = null khi không chọn thời gian
  //   }
  //   if (serviceId) {
  //     url += `/${serviceId}`;
  //   }

  //   // console.log('Fetching URL:', url); // Debug URL

  //   fetch(url)
  //     .then(response => {
  //       if (!response.ok) {
  //         throw new Error(`HTTP error! Status: ${response.status}`);
  //       }
  //       return response.json();
  //     })
  //     .then(data => {
  //       if (data.error) {
  //         barberSelect.innerHTML = `<option value="">${data.error}</option>`;
  //       } else if (!data.length) {
  //         barberSelect.innerHTML = `<option value="">Không có thợ khả dụng</option>`;
  //       } else {
  //         const addedBarbers = new Set();
  //         data.forEach(barber => {
  //           if (!addedBarbers.has(barber.id)) {
  //             const option = document.createElement('option');
  //             option.value = barber.id;
  //             option.text = barber.name;
  //             barberSelect.appendChild(option);
  //             addedBarbers.add(barber.id);
  //           }
  //         });
  //       }
  //     })
  //     .catch(error => {
  //       // console.error('Fetch error:', error);
  //       barberSelect.innerHTML = '<option value="">Lỗi khi tải danh sách thợ</option>';
  //     });
  // }

  // appointmentDate.addEventListener('change', function () {
  //   updateBarbers(branchSelect.value, this.value, appointmentTime.value);
  // });

  // appointmentTime.addEventListener('change', function () {
  //   updateBarbers(branchSelect.value, appointmentDate.value, this.value);
  // });

  // branchSelect.addEventListener('change', function () {
  //   updateBarbers(this.value, appointmentDate.value, appointmentTime.value);
  // });

  // serviceSelect.addEventListener('change', function () {
  //   updateBarbers(branchSelect.value, appointmentDate.value, appointmentTime.value, this.value);
  // });

  // const appointmentDate = document.getElementById('appointment_date');
  // const appointmentTime = document.getElementById('appointment_time');
  // const branchSelect = document.getElementById('branch');
  // const barberSelect = document.getElementById('barber');
  // const serviceSelect = document.getElementById('service');

  // let lastRequest = null; // Track last request key

  // function updateBarbers(branchId, date, time = null, serviceId = null) {
  //   // Chỉ gọi fetch khi combo branch-date-time-service thay đổi
  //   const requestKey = `${branchId}|${date}|${time}|${serviceId}`;
  //   if (lastRequest === requestKey) return;
  //   lastRequest = requestKey;

  //   // Giữ lại barber đã chọn (nếu có)
  //   const prevBarber = barberSelect.value;

  //   // Reset UI
  //   barberSelect.innerHTML = '<option value="">-- Chọn thợ --</option>';

  //   if (!branchId || !date) {
  //     barberSelect.innerHTML = '<option value="">Vui lòng chọn chi nhánh và ngày</option>';
  //     return;
  //   }

  //   // Build URL
  //   let url = `/get-available-barbers-by-date/${branchId}/${date}/${time ? encodeURIComponent(time) : 'null'}`;
  //   if (serviceId) url += `/${serviceId}`;

  //   fetch(url)
  //     .then(r => {
  //       if (!r.ok) throw new Error(`Status ${r.status}`);
  //       return r.json();
  //     })
  //     .then(data => {
  //       if (data.error) {
  //         barberSelect.innerHTML = `<option value="">${data.error}</option>`;
  //         return;
  //       }
  //       if (!data.length) {
  //         barberSelect.innerHTML = '<option value="">Không có thợ khả dụng</option>';
  //         return;
  //       }

  //       const added = new Set();
  //       data.forEach(b => {
  //         if (!added.has(b.id)) {
  //           const opt = document.createElement('option');
  //           opt.value = b.id;
  //           opt.text = b.name;
  //           barberSelect.appendChild(opt);
  //           added.add(b.id);
  //         }
  //       });

  //       // Nếu prevBarber vẫn có trong added, chọn lại
  //       if (prevBarber && added.has(prevBarber)) {
  //         barberSelect.value = prevBarber;
  //       }
  //     })
  //     .catch(err => {
  //       console.error(err);
  //       barberSelect.innerHTML = '<option value="">Lỗi khi tải danh sách thợ</option>';
  //     });
  // }

  // // Chỉ gắn listener vào 4 trường liên quan
  // appointmentDate.addEventListener('change', () => updateBarbers(branchSelect.value, appointmentDate.value, appointmentTime.value, serviceSelect.value));
  // appointmentTime.addEventListener('change', () => updateBarbers(branchSelect.value, appointmentDate.value, appointmentTime.value, serviceSelect.value));
  // branchSelect.addEventListener('change', () => updateBarbers(branchSelect.value, appointmentDate.value, appointmentTime.value, serviceSelect.value));
  // serviceSelect.addEventListener('change', () => updateBarbers(branchSelect.value, appointmentDate.value, appointmentTime.value, serviceSelect.value));

  // tiền dịch vụ và thời gian 


  // document.addEventListener('DOMContentLoaded', function () {
  // const appointmentDate = document.getElementById('appointment_date');
  // const appointmentTime = document.getElementById('appointment_time');
  // const branchContainer = document.getElementById('branch');
  // const branchInput = document.getElementById('branch_input');
  // const appointmentTimeInput     = document.getElementById('appointment_time_input');
  // const barberSelect = document.getElementById('barber');
  // const serviceSelect = document.getElementById('service');

  // let lastRequest = null;

  // function updateBarbers(branchId, date, time = null, serviceId = null) {
  //   const requestKey = `${branchId}|${date}|${time}|${serviceId}`;
  //   if (lastRequest === requestKey) return;
  //   lastRequest = requestKey;

  //   const prevBarber = barberSelect.value;
  //   barberSelect.innerHTML = '<option value="">Chọn kĩ thuật viên</option>';

  //   if (!branchId || !date) {
  //     barberSelect.innerHTML = '<option value="">Vui lòng chọn chi nhánh và ngày</option>';
  //     return;
  //   }

  //   let url = `/get-available-barbers-by-date/${branchId}/${date}/${time ? encodeURIComponent(time) : 'null'}`;
  //   if (serviceId) url += `/${serviceId}`;

  //   fetch(url)
  //     .then(r => { if (!r.ok) throw new Error(`Status ${r.status}`); return r.json(); })
  //     .then(data => {
  //       if (data.error) {
  //         barberSelect.innerHTML = `<option value="">${data.error}</option>`;
  //         return;
  //       }
  //       if (!data.length) {
  //         barberSelect.innerHTML = '<option value="">Không có thợ khả dụng</option>';
  //         return;
  //       }
  //       const added = new Set();
  //       data.forEach(b => {
  //         if (!added.has(b.id)) {
  //           const opt = document.createElement('option');
  //           opt.value = b.id;
  //           opt.text = b.name;
  //           barberSelect.appendChild(opt);
  //           added.add(b.id);
  //         }
  //       });
  //       if (prevBarber && added.has(prevBarber)) {
  //         barberSelect.value = prevBarber;
  //       }
  //     })
  //     .catch(err => {
  //       console.error(err);
  //       barberSelect.innerHTML = '<option value="">Lỗi khi tải danh sách thợ</option>';
  //     });
  // }

  // // Bắt sự kiện khi click vào chi nhánh
  // branchContainer.querySelectorAll('.branch-item').forEach(item => {
  //   item.addEventListener('click', () => {
  //     // Reset tất cả active
  //     branchContainer.querySelectorAll('.branch-item')
  //       .forEach(el => el.classList.remove('active'));

  //     // Active item này
  //     item.classList.add('active');

  //     // Lấy id và gán vào input ẩn
  //     const branchId = item.getAttribute('data-id');
  //     branchInput.value = branchId;

  //     // Thêm branchContainer.value cho compatibility với code cũ
  //     branchContainer.value = branchId;

  //     // Gọi updateBarbers ngay sau khi chọn chi nhánh
  //     updateBarbers(
  //       branchId,
  //       appointmentDate.value,
  //       appointmentTime.value,
  //       serviceSelect.value
  //     );
  //   });
  // });

  // // Các listener khác giữ nguyên
  // appointmentDate.addEventListener('change', () =>
  //   updateBarbers(branchContainer.value, appointmentDate.value, appointmentTime.value, serviceSelect.value)
  // );
  // appointmentTime.addEventListener('change', () =>
  //   updateBarbers(branchContainer.value, appointmentDate.value, appointmentTime.value, serviceSelect.value)
  // );
  // serviceSelect.addEventListener('change', () =>
  //   updateBarbers(branchContainer.value, appointmentDate.value, appointmentTime.value, serviceSelect.value)
  // );


  const appointmentDate = document.getElementById('appointment_date');
  const appointmentTime = document.getElementById('appointment_time');
  const branchContainer = document.getElementById('branch');
  const branchInput = document.getElementById('branch_input');
  const barberSelect = document.getElementById('barber');
  const serviceSelect = document.getElementById('service');


  const timeGrid = document.getElementById('timeGrid');

  // Xử lý sự kiện nhấp chuột trên các ô giờ
  timeGrid.querySelectorAll('.time-slot').forEach(slot => {
    slot.addEventListener('click', function () {
      // Xóa lớp 'selected' khỏi tất cả các ô giờ
      timeGrid.querySelectorAll('.time-slot').forEach(s => s.classList.remove('selected'));
      // Thêm lớp 'selected' cho ô giờ được nhấp
      this.classList.add('selected');
      // Cập nhật giá trị input ẩn
      appointmentTime.value = this.getAttribute('data-value');
      updateBarbers(branchContainer.value, appointmentDate.value, appointmentTime.value, serviceSelect.value);
    });
  });

  // Đánh dấu ô giờ nếu có giá trị cũ (từ {{ old('appointment_time') }})
  const oldTime = appointmentTime.value;
  if (oldTime) {
    const slot = timeGrid.querySelector(`.time-slot[data-value="${oldTime}"]`);
    if (slot) slot.classList.add('selected');
  }

  let lastRequest = null;

  function updateBarbers(branchId, date, time = null, serviceId = null) {
    const requestKey = `${branchId}|${date}|${time}|${serviceId}`;
    if (lastRequest === requestKey) return;
    lastRequest = requestKey;

    const prevBarber = barberSelect.value;
    barberSelect.innerHTML = '<option value="">Chọn kĩ thuật viên</option>';

    if (!branchId || !date) {
      barberSelect.innerHTML = '<option value="">Vui lòng chọn chi nhánh và ngày</option>';
      return;
    }

    let url = `/get-available-barbers-by-date/${branchId}/${date}/${time ? encodeURIComponent(time) : 'null'}`;
    if (serviceId) url += `/${serviceId}`;

    fetch(url)
      .then(r => { if (!r.ok) throw new Error(`Status ${r.status}`); return r.json(); })
      .then(data => {
        if (data.error) {
          barberSelect.innerHTML = `<option value="">${data.error}</option>`;
          return;
        }
        if (!data.length) {
          barberSelect.innerHTML = '<option value="">Không có thợ khả dụng</option>';
          return;
        }
        const added = new Set();
        data.forEach(b => {
          if (!added.has(b.id)) {
            const opt = document.createElement('option');
            opt.value = b.id;
            opt.text = b.name;
            barberSelect.appendChild(opt);
            added.add(b.id);
          }
        });
        if (prevBarber && added.has(prevBarber)) {
          barberSelect.value = prevBarber;
        }
      })
      .catch(err => {
        console.error(err);
        barberSelect.innerHTML = '<option value="">Lỗi khi tải danh sách thợ</option>';
      });
  }

  // Bắt sự kiện khi click vào chi nhánh
  branchContainer.querySelectorAll('.branch-item').forEach(item => {
    item.addEventListener('click', () => {
      // Reset tất cả active
      branchContainer.querySelectorAll('.branch-item')
        .forEach(el => el.classList.remove('active'));

      // Active item này
      item.classList.add('active');

      // Lấy id và gán vào input ẩn
      const branchId = item.getAttribute('data-id');
      branchInput.value = branchId;

      // Thêm branchContainer.value cho compatibility với code cũ
      branchContainer.value = branchId;

      // Gọi updateBarbers ngay sau khi chọn chi nhánh
      updateBarbers(
        branchId,
        appointmentDate.value,
        appointmentTime.value,
        serviceSelect.value
      );
    });
  });

  // Các listener khác giữ nguyên
  appointmentDate.addEventListener('change', () =>
    updateBarbers(branchContainer.value, appointmentDate.value, appointmentTime.value, serviceSelect.value)
  );

  serviceSelect.addEventListener('change', () =>
    updateBarbers(branchContainer.value, appointmentDate.value, appointmentTime.value, serviceSelect.value)
  );

  flatpickr(appointmentDate, {
    // defaultDate: "today",
    minDate: "today",
    maxDate: new Date().fp_incr(90),
    dateFormat: "Y-m-d", // Thay đổi sang định dạng Laravel chấp nhận
    disableMobile: true,
    onChange: function (selectedDates, dateStr, instance) {
      appointmentDate.value = dateStr; // Cập nhật trực tiếp
      updateBarbers(branchContainer.value, dateStr, appointmentTime.value, serviceSelect.value);
    }
  });

  // tính tổng tiền và thời gian dịch vụ 
  const priceOutput = document.getElementById('totalPrice');
  const durationOutput = document.getElementById('totalDuration');

  serviceSelect.addEventListener('change', function () {
    const opt = this.options[this.selectedIndex];
    // console.log('DEBUG sel.dataset =', opt.dataset);

    const price = parseFloat(opt.dataset.price);
    const duration = parseInt(opt.dataset.duration, 10);

    priceOutput.textContent = isNaN(price) ? '0 vnđ' : price.toLocaleString('vi-VN') + ' vnđ';
    durationOutput.textContent = isNaN(duration) ? '0 Phút' : duration + ' Phút';
  });

  // Trigger nếu old() đã chọn
  if (serviceSelect.value) {
    serviceSelect.dispatchEvent(new Event('change'));
  }

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

  // checkbox hiển thị khi đặt lịch hộ
  const checkbox = document.getElementById('other_person');
  const otherInfo = document.getElementById('other-info');
  checkbox.addEventListener('change', function () {
    otherInfo.style.display = this.checked ? 'block' : 'none';
  });

  document.addEventListener('DOMContentLoaded', function () {
    const checkbox = document.getElementById('other_person');
    const otherInfo = document.getElementById('other-info');

    checkbox.addEventListener('change', function () {
      otherInfo.style.display = this.checked ? 'block' : 'none';
    });
  });


});



document.addEventListener('DOMContentLoaded', function () {
  const serviceSelect = document.getElementById('service');
  const voucherSelect = document.getElementById('voucher_id');
  const priceOutput = document.querySelectorAll('#totalPrice');
  const totalAfterDiscount = document.getElementById('total_after_discount');

  function getServicePrice() {
    const opt = serviceSelect.options[serviceSelect.selectedIndex];
    // Nếu có data-price thì lấy, không thì lấy số trong text
    let price = opt.dataset.price ? parseFloat(opt.dataset.price) : 0;
    if (!price) {
      // fallback: lấy số trong text
      const match = opt.textContent.match(/(\d[\d\.]*)đ/);
      if (match) price = parseInt(match[1].replace(/\./g, ''));
    }
    return price || 0;
  }

  function updateTotal() {
    const price = getServicePrice();
    let discount = 0;
    let discountText = '';
    const voucherOpt = voucherSelect.options[voucherSelect.selectedIndex];
    const discountType = voucherOpt.getAttribute('data-discount-type');
    const discountValue = parseFloat(voucherOpt.getAttribute('data-discount-value')) || 0;

    if (voucherSelect.value && price > 0) {
      if (discountType === 'fixed') {
        discount = discountValue;
        discountText = `- ${discount.toLocaleString('vi-VN')} vnđ`;
      } else if (discountType === 'percent') {
        discount = price * discountValue / 100;
        discountText = `- ${discountValue}% (${discount.toLocaleString('vi-VN')} vnđ)`;
      }
    }

    let total = price - discount;
    if (total < 0) total = 0;

    // Cập nhật tất cả chỗ hiển thị tổng tiền
    priceOutput.forEach(el => el.textContent = total.toLocaleString('vi-VN') + ' vnđ');
    totalAfterDiscount.innerHTML = discount > 0 ?
      `<span class="text-success">Đã giảm: ${discountText}</span>` :
      '';
  }

  serviceSelect.addEventListener('change', updateTotal);
  voucherSelect.addEventListener('change', updateTotal);

  // Gọi khi load trang
  updateTotal();
});
