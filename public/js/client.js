document.addEventListener("DOMContentLoaded", function () {
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

});

document.addEventListener('DOMContentLoaded', function () {
  document.getElementById('branch').addEventListener('change', function() {
        var branchId = this.value;
        var barberSelect = document.getElementById('barber');
        
        // Xóa các tùy chọn hiện tại và đặt lại mặc định
        barberSelect.innerHTML = '<option value="">-- Chọn thợ --</option>';

        if (branchId) {
            fetch('/get-barbers-by-branch/' + branchId)
                .then(response => response.json())
                .then(data => {
                    data.forEach(function(barber) {
                        var option = document.createElement('option');
                        option.value = barber.id;
                        option.text = barber.name;
                        barberSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Lỗi:', error));
        }
    });
    
  const serviceSelect = document.getElementById('service');
  if (!serviceSelect) {
    console.error('Không tìm thấy #service trong DOM');
    return;
  }

  const priceOutput = document.getElementById('totalPrice');
  const durationOutput = document.getElementById('totalDuration');

  serviceSelect.addEventListener('change', function () {
    const opt = this.options[this.selectedIndex];
    console.log('DEBUG sel.dataset =', opt.dataset);

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