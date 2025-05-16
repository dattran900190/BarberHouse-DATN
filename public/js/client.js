    // ——— Nav background on scroll ———
    const nav = document.getElementById("mainNav");

    window.addEventListener("scroll", () => {
      if (window.scrollY > 100) {
        nav.classList.add("scrolled");
      } else {
        nav.classList.remove("scrolled");
      }
    });

    // ——— Hero slider ———
    const slides = document.querySelectorAll(".hero-slider .slide");
    let idx = 0;

    function showSlide(i) {
      slides.forEach((s) => s.classList.remove("active"));
      slides[i].classList.add("active");
    }
    function next() {
      idx = (idx + 1) % slides.length;
      showSlide(idx);
    }
    function prev() {
      idx = (idx - 1 + slides.length) % slides.length;
      showSlide(idx);
    }

    document
      .querySelector(".hero-slider .next")
      .addEventListener("click", next);
    document
      .querySelector(".hero-slider .prev")
      .addEventListener("click", prev);
    setInterval(next, 4000); // autoplay every 4s

    // posts
    const wrapper = document.querySelector(".posts-wrapper");
    const track = document.querySelector(".posts");
    const posts = document.querySelectorAll(".post");
    const prevBtn = document.querySelector(".prev-posts");
    const nextBtn = document.querySelector(".next-posts");

    let currentSlide = 0;
    const postsPerSlide = 3;
    const totalSlides = Math.ceil(posts.length / postsPerSlide);

    function updateSlide() {
      const slideWidth = wrapper.clientWidth; // chính xác width mà 3 post chiếm
      track.style.transform = `translateX(-${currentSlide * slideWidth}px)`;
    }

    // next/prev
    nextBtn.addEventListener("click", () => {
      if (currentSlide < totalSlides - 1) {
        currentSlide++;
        updateSlide();
      }
    });
    prevBtn.addEventListener("click", () => {
      if (currentSlide > 0) {
        currentSlide--;
        updateSlide();
      }
    });

    // đảm bảo đúng khi resize
    window.addEventListener("resize", updateSlide);


    // thời gian đặt lịch
  const timeSelect = document.getElementById('time');
  const pad = n => n.toString().padStart(2, '0');

  // generate từ 10:00 đến 19:30, step = 30 phút
  let start = 10 * 60;    // 10:00 in minutes
  const end   = 19 * 60 + 30; // 19:30 in minutes
  for (let t = start; t <= end; t += 30) {
    const h = Math.floor(t / 60);
    const m = t % 60;
    const label = `${pad(h)}:${pad(m)}`;
    const opt   = document.createElement('option');
    opt.value   = label;
    opt.text    = label;
    timeSelect.appendChild(opt);
  }

  // document.querySelectorAll('#mainNav a').forEach(a => {
  //   if (a.href === window.location.href) {
  //     a.classList.add('active');
  //   }
  // });

  
  const chatToggle = document.getElementById('chatToggle');
  const chatBox = document.getElementById('chatBox');
  const chatClose = document.getElementById('chatClose');

  chatToggle.addEventListener('click', () => {
    chatBox.style.display = 'block';
  });

  chatClose.addEventListener('click', () => {
    chatBox.style.display = 'none';
  });

  