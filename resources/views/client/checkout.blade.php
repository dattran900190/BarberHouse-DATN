@extends('layouts.ClientLayout')

@section('title-page')
    {{-- {{ $titlePage }} --}}
    Thanh toán Baber House
@endsection

@section('content')
<main class="container">
      <section class="h-100 h-custom">
        <div class="mainCheckout">
          <div class="informationUser">
            <h3>Thông tin nhận hàng</h3>

            <form>
              <div class="mb-3">
                <label for="" class="form-label">Tên người nhận</label>
                <input type="text" class="form-control" id="" />
              </div>
              <div class="mb-3">
                <label for="" class="form-label">Địa chỉ Email</label>
                <input type="text" class="form-control" id="" />
              </div>
              <div class="mb-3">
                <label for="" class="form-label">Số điện thoại</label>
                <input type="text" class="form-control" id="" />
              </div>
              <div class="mb-3">
                <label for="" class="form-label">Địa chỉ</label>
                <input type="text" class="form-control" id="" />
              </div>
              <div class="mb-3">
                <label for="" class="form-label">Ghi chú</label>
                <textarea
                  name=""
                  class="form-control"
                  id=""
                  rows="5"
                ></textarea>
              </div>
              <button type="submit" class="btn btn-primary">Submit</button>
            </form>

            
          </div>

          <div class="informationProduct">
            <h3>Vận chuyển</h3>
            <div
              class="form-check"
              style="display: flex; justify-content: space-between"
            >
              <div class="chon">
                <input
                  class="form-check-input"
                  type="radio"
                  name="deliveryMethod"
                  checked
                  disabled
                />
                <label class="form-check-label" for="deliveryMethod1">
                  Giao hàng tận nơi
                </label>
              </div>
              <div class="ship">30.000 VNĐ</div>
            </div>

            <h3>Thanh toán</h3>

            <div
              class="form-check"
              style="display: flex; justify-content: space-between"
            >
              <div class="chon">
                <input
                  class="form-check-input"
                  value="1"
                  type="radio"
                  name="phuong_thuc_thanh_toan_id"
                  checked
                />
                <label class="form-check-label" for="paymentMethodCOD">
                  Thanh toán khi giao hàng (COD)
                </label>
              </div>
              <div class="icon-bank">
                <i class="fa-regular fa-money-bill-1"></i>
              </div>
            </div>
            <div
              class="form-check"
              style="display: flex; justify-content: space-between"
            >
              <div class="chon">
                <input
                  class="form-check-input"
                  value="2"
                  type="radio"
                  name="phuong_thuc_thanh_toan_id"
                />
                <label class="form-check-label" for="paymentMethodVNPAY">
                  Thanh toán qua VNPAY-QR
                </label>
              </div>
              <div class="icon-bank">
                <i class="fa-solid fa-qrcode"></i>
              </div>
            </div>
          </div>

          <div class="informationProduct">
            <h3>Thông tin sản phẩm</h3>

            <table>
              <tbody>
                <tr style="border-bottom: 1px solid #ccc; padding: 10px 0">
                  <td style="padding: 10px">
                    <img src="/img/post.png" alt="" width="80px" />
                  </td>
                  <td style="padding: 20px; line-height: 20px; font-size: 12px">
                    Tên sản phẩm: Sáp xịn<br />
                    Số lượng: 1 <br />
                    Giá: 111 VNĐ
                  </td>
                </tr>
                <tr style="border-bottom: 1px solid #ccc; padding: 10px 0">
                  <td style="padding: 10px">
                    <img src="/img/post.png" alt="" width="80px" />
                  </td>
                  <td style="padding: 20px; line-height: 20px; font-size: 12px">
                    Tên sản phẩm: Sáp xịn<br />
                    Số lượng: 1 <br />
                    Giá: 111 VNĐ
                  </td>
                </tr>
              </tbody>
            </table>

            <div class="phi-tam-thoi">
              <div class="tam-tinh">
                <p>Tạm tính</p>
                2344 VNĐ
              </div>
              <div class="phi-van-chuyen">
                <p>Phí vận chuyển</p>
                30.000 VNĐ
              </div>
            </div>

            <div class="tong-cong">
              <div class="tong-thanh-toan">
                <p>Tổng tiền thanh toán</p>
                <input
                  type="hidden"
                  name="tong_tien"
                  value=""
                />
                <h5>54667 VNĐ</h5>
              </div>
            </div>

            <div class="dat-hang">
              <a href=""
                ><i class="fa-solid fa-angle-left"></i> Quay về giỏ hàng</a
              >
              <button
                class="btn btn-info"
                type="submit"
                style="padding: 10px 20px"
              >
                ĐẶT HÀNG
              </button>
            </div>
          </div>
        </div>
      </section>
    </main>
    <style>
      #mainNav {
        background-color: #000;
      }
    </style>
@endsection

<script>
      document.addEventListener("DOMContentLoaded", function () {
        // Lấy các phần tử bằng ID
        const minusBtn = document.getElementById("quantity-minus");
        const plusBtn = document.getElementById("quantity-plus");
        const input = document.getElementById("quantity-input");

        // Xử lý sự kiện nút trừ
        minusBtn.addEventListener("click", function () {
          let value = parseInt(input.value);
          if (value > 1) {
            // Đảm bảo giá trị không nhỏ hơn 1
            input.value = value - 1;
          }
        });

        // Xử lý sự kiện nút cộng
        plusBtn.addEventListener("click", function () {
          let value = parseInt(input.value);
          input.value = value + 1;
        });

        // Xử lý khi người dùng nhập trực tiếp vào input
        input.addEventListener("change", function () {
          let value = parseInt(input.value);
          if (isNaN(value) || value < 1) {
            // Đảm bảo giá trị hợp lệ
            input.value = 1;
          }
        });
      });

      const nav = document.getElementById("mainNav");

    window.addEventListener("scroll", () => {
      if (window.scrollY = 100) {
        nav.classList.add("scrolled");
      } else {
        nav.classList.remove("scrolled");
      }
    });
    </script>