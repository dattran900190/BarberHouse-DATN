<footer>
      <div class="main-footer">
        <div class="description">
          <div class="logo">
            <a href=""><img src="{{ asset('storage/' . ($imageSettings['white_logo'] ?? 'default-images/white_logo.png')) }}" alt=""></a>
          </div>
          <p>
            CÔNG TY TNHH BARBER HOURSE<br>
            MST 0315048848<br>
            Địa chỉ: Tòa nhà FPT Polytechnic, đường Trịnh Văn Bô, Phương Canh, Nam Từ Liêm, Hà Nội
          </p>
          <div class="social">
            <ul>
              @foreach ($social_links as $key => $url)
                    <li class="nav-item mx-2">
                        <a class="nav-link" href="{{ $url }}" target="_blank">
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
      
        <div class="information" data-title="Thông tin">
          <ul>
            <li><a href="#">Về chúng tôi</a></li>
            <li><a href="#">Dịch vụ</a></li>
            <li><a href="#">Liên hệ với chúng tôi</a></li>
          </ul>
        </div>
      
        <div class="faq" data-title="Chính sách">
          <ul>
            <li><a href="#">Bảo mật</a></li>
            <li><a href="#">Giao dịch</a></li>
            <li><a href="#">Vận chuyển</a></li>
            <li><a href="#">Bảo hành & Đổi trả</a></li>
          </ul>
        </div>
      
        <div class="follow-us" data-title="Kết nối">
          <ul>
            <li><a href="#">Facebook</a></li>
            <li><a href="#">Instagram</a></li>
            <li><a href="#">YouTube</a></li>
            <li><a href="#">TikTok</a></li>
          </ul>
        </div>
        <p>© 2025 <strong>Barber House</strong> | <em>FPT Polytechnic HaNoi</em> — Powered by <strong>DREAMTEAM</strong></p>
      </div>

    </footer>
    