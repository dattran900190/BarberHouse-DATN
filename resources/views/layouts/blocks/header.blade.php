<nav id="mainNav">
      <div class="nav-left">
        <ul>
          <li><a href="{{ asset('/') }}">Trang chủ</a></li>
          <li><a href="{{ asset('san-pham') }}">Sản phẩm</a></li>
          <li><a href="{{ asset('bai-viet') }}">Tin tức</a></li>
        </ul>
      </div>

      <div class="nav-mid">
        <a href="#">
          <img
            src="{{ asset('images/white_logo.png') }}"
            alt=""
          />
        </a>
      </div>

      <div class="nav-right">
        <ul>
          <li><a href="{{ asset('dat-lich') }}">Đặt lịch</a></li>
          <li><a href="{{ asset('chi-nhanh') }}">Chi nhánh</a></li>
        </ul>
      </div>

      <div class="nav-info">
        <ul>
          <!-- <li>
            <a href="#"><i class="fa-brands fa-square-facebook"></i></a>
          </li>
          <li>
            <a href="#"><i class="fa-brands fa-square-instagram"></i></a>
          </li>
          <li>
            <a href="#"><i class="fa-brands fa-square-youtube"></i></a>
          </li> -->
          <li>
            <a href="{{ asset('gio-hang') }}"><i class="fa-solid fa-cart-shopping"></i></a>
          </li>
          <li>
            <a href="{{ asset('dang-nhap') }}"><i class="fa-solid fa-user"></i></a>
          </li>
          <li>
            <a href="#"><i class="fa-solid fa-magnifying-glass"></i></a>
          </li>
        </ul>
      </div>
    </nav>

   