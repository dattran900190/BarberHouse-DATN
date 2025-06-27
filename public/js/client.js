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

  const appointmentDate  = document.getElementById('appointment_date');
  const appointmentTime  = document.getElementById('appointment_time');
  const branchSelect     = document.getElementById('branch');
  const barberSelect     = document.getElementById('barber');
  const serviceSelect    = document.getElementById('service');

  let lastRequest = null; // Track last request key

  function updateBarbers(branchId, date, time = null, serviceId = null) {
    // Chỉ gọi fetch khi combo branch-date-time-service thay đổi
    const requestKey = `${branchId}|${date}|${time}|${serviceId}`;
    if (lastRequest === requestKey) return;
    lastRequest = requestKey;

    // Giữ lại barber đã chọn (nếu có)
    const prevBarber = barberSelect.value;

    // Reset UI
    barberSelect.innerHTML = '<option value="">-- Chọn thợ --</option>';

    if (!branchId || !date) {
      barberSelect.innerHTML = '<option value="">Vui lòng chọn chi nhánh và ngày</option>';
      return;
    }

    // Build URL
    let url = `/get-available-barbers-by-date/${branchId}/${date}/${time ? encodeURIComponent(time) : 'null'}`;
    if (serviceId) url += `/${serviceId}`;

    fetch(url)
      .then(r => {
        if (!r.ok) throw new Error(`Status ${r.status}`);
        return r.json();
      })
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
            opt.text  = b.name;
            barberSelect.appendChild(opt);
            added.add(b.id);
          }
        });

        // Nếu prevBarber vẫn có trong added, chọn lại
        if (prevBarber && added.has(prevBarber)) {
          barberSelect.value = prevBarber;
        }
      })
      .catch(err => {
        console.error(err);
        barberSelect.innerHTML = '<option value="">Lỗi khi tải danh sách thợ</option>';
      });
  }

  // Chỉ gắn listener vào 4 trường liên quan
  appointmentDate .addEventListener('change', () => updateBarbers(branchSelect.value, appointmentDate.value, appointmentTime.value, serviceSelect.value));
  appointmentTime .addEventListener('change', () => updateBarbers(branchSelect.value, appointmentDate.value, appointmentTime.value, serviceSelect.value));
  branchSelect    .addEventListener('change', () => updateBarbers(branchSelect.value, appointmentDate.value, appointmentTime.value, serviceSelect.value));
  serviceSelect   .addEventListener('change', () => updateBarbers(branchSelect.value, appointmentDate.value, appointmentTime.value, serviceSelect.value));

  // tiền dịch vụ và thời gian 
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