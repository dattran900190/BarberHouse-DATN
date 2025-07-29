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
    setInterval(() => { idx = (idx + 1) % slides.length; showSlide(idx); }, 6000);
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
