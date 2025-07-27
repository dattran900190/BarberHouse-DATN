<footer class="footer">
  <div class="footer-container">
      <div class="footer-top">
          <h4 class="company-name">CÔNG TY TNHH MTV BARBER HOUSE</h4>
          <p class="tax-code">MST 0315048848</p>
          <p class="address">Địa chỉ: Tòa nhà FPT Polytechnic, P. Trịnh Văn Bô, Xuân Phương, Nam Từ Liêm, Hà Nội</p>
          <div class="social d-flex justify-content-center" >
            <ul class="d-flex list-unstyled mb-0">
                @foreach ($social_links as $key => $url)
                    <li class="nav-item mx-2" style="font-size: 20px;">
                        <a class="nav-link p-0" href="{{ $url }}" target="_blank">
                            @if (str_contains($key, 'facebook'))
                                <i class="fa-brands fa-facebook"></i>
                            @elseif (str_contains($key, 'instagram'))
                                <i class="fa-brands fa-instagram"></i>
                            @elseif (str_contains($key, 'youtube'))
                                <i class="fa-brands fa-youtube"></i>
                            @elseif (str_contains($key, 'tiktok'))
                                <i class="fa-brands fa-tiktok"></i>
                            @else
                                <i class="fa-solid fa-link"></i>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
          </div>
      </div>

      <div class="footer-links">
          <div class="footer-column">
              <h5 class="text-uppercase">Về chúng tôi</h5>
              <ul>
                  <li><a href="#">Home</a></li>
                  <li><a href="#">Video</a></li>
                  <li><a href="#">Contact Us</a></li>
                  <li><a href="#">Chi nhánh 4Rau gần bạn</a></li>
              </ul>
          </div>
          <div class="footer-column">
              <h5>FAQ</h5>
              <ul>
                  <li><a href="#">Chính sách bảo mật</a></li>
                  <li><a href="#">Chính sách giao dịch</a></li>
                  <li><a href="#">Chính sách vận chuyển</a></li>
                  <li><a href="#">Chính sách bảo hành - đổi trả</a></li>
              </ul>
          </div>
          
        </div>
        
      </div>

      <div class="footer-logo-cert">
          <img src="{{ asset('storage/' . ($imageSettings['white_logo'] ?? 'default-images/white_logo.png')) }}" alt="4Rau Logo" class="logo-footer">
          <img src="{{ asset('images/Logo-thong-bao-mau-xanh.png') }}" alt="Bộ Công Thương" class="cert-footer">
      </div>

      <div class="footer-bottom">
        © 2025 <strong>Barber House</strong> | <em>FPT Polytechnic HaNoi</em> — Powered by <strong>DREAMTEAM</strong>
    </div>
  </div>
</footer>
