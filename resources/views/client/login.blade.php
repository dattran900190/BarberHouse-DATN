<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Đăng nhập Barber House</title>
    <link rel="stylesheet" href="{{ asset('css/client.css') }}" />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
      crossorigin="anonymous"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
  </head>
  <body>


    <main class="">
      <div class="login">
        <div class="image-login">
          <img src="https://file.hstatic.net/200000503583/file/barber-la-nghe-gi__4__54d8d1f24560403b9b5929af8c54f9c1.jpg" alt="">
        </div>

        <div class="form-login">
          <div class="image">
            <img src="{{ asset('images/black_logo.png') }}" alt="">
          </div>

         <div class="form">
          <h3>Đăng nhập</h3>
          <form>
            <div class="mb-3">
              <label for="" class="form-label">Email</label>
              <input type="email" class="form-control" id="">
            </div>
            <div class="mb-3">
              <label for="" class="form-label">Mật khẩu</label>
              <input type="password" class="form-control" id="">
            </div>
            <div class="mb-3 form-check">
              <input type="checkbox" class="form-check-input" id="exampleCheck1">
              <label class="form-check-label" for="exampleCheck1">Ghi nhớ</label>
            </div>
            <button type="submit">Đăng nhập</button>

            <p>Bạn chưa có tài khoản? <a href="{{ asset('dang-ky') }}">Đăng ký</a></p>
          </form>
         </div>
        </div>

        <a href="{{ asset('/') }}" class="back-button">
          &#8592;
        </a>
      </div>
    </main>

    
  </body>

  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
    crossorigin="anonymous"
  ></script>
</html>
