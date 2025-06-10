<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
<<<<<<< HEAD
  <title>Login - Cafe Diagram</title>
  <link rel="shortcut icon" type="image/png" href="{{ asset('images/logos/favicon.png') }}" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * {
      font-family: 'Inter', sans-serif;
      box-sizing: border-box;
    }

    body {
      margin: 0;
      padding: 0;
      background: linear-gradient(to right, #f5f5f5, #e0d6c3);
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .login-card {
      background-color: #ffffff;
      border-radius: 16px;
      box-shadow: 0 12px 35px rgba(0, 0, 0, 0.15);
      padding: 40px 30px;
      width: 100%;
      max-width: 420px;
      transition: 0.3s;
    }

    .logo {
      display: flex;
      justify-content: center;
      margin-bottom: 30px;
    }

    .logo img {
      width: 160px;
      height: auto;
      object-fit: contain;
    }

    .form-label {
      font-weight: 600;
      color: #4e342e;
      margin-bottom: 6px;
      display: block;
    }

    .form-control {
      width: 100%;
      padding: 12px 14px;
      border-radius: 10px;
      border: 1px solid #ccc;
      margin-bottom: 20px;
      font-size: 15px;
    }

    .btn-primary {
      width: 100%;
      background-color: #6d4c41;
      border: none;
      padding: 14px;
      border-radius: 10px;
      font-weight: 600;
      color: white;
      font-size: 16px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .btn-primary:hover {
      background-color: #5d4037;
    }

    .alert {
      background-color: #ffe5e5;
      color: #d32f2f;
      padding: 12px;
      border-radius: 8px;
      margin-bottom: 20px;
      font-size: 14px;
    }

    @media (max-width: 480px) {
      .login-card {
        padding: 30px 20px;
      }

      .logo img {
        width: 120px;
      }
=======
  <title>Cafe Diagram</title>

  <link rel="shortcut icon" type="image/png" href="{{asset('images/logos/favicon.png')}}" />
  <link rel="stylesheet" href="{{asset('css/styles.min.css')}}" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body {
      background: linear-gradient(135deg, #fffacd, #fefcbf, #fff3a0);
      /* Gradasi kuning halus */
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      margin: 0;
    }

    .card {
      border: none;
      border-radius: 12px;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
      background: #fffde9;
    }

    .card-body {
      padding: 2.5rem;
    }

    .form-label {
      color: #333;
      text-align: left;
      display: block;
      margin-bottom: 0.5rem;
      font-weight: bold;
      /* TEKS BOLD */
    }

    .form-control {
      margin-bottom: 1.2rem;
    }

    .btn-primary {
      background-color: #28a745;
      border: none;
      width: 100%;
      /* LEBAR PENUH */
      padding: 0.6rem;
      font-size: 1rem;
      font-weight: bold;
      transition: background-color 0.3s;
    }

    .btn-primary:hover {
      background-color: #218838;
    }

    .register {
      display: block;
      text-align: center;
      margin-top: 1rem;
      color: #555;
      font-size: 0.95rem;
>>>>>>> 519817204e2598416ec72975b2a8e9cff4710d33
    }
  </style>
</head>

<body>
<<<<<<< HEAD

  <div class="login-card">
    <div class="logo">
      <img src="{{ asset('images/logos/mukena.PNG') }}" alt="Cafe Diagram Logo">
    </div>
=======
  <!-- Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper">
    <div class="d-flex align-items-center justify-content-center w-100">
      <div class="row justify-content-center w-100">
        <div class="col-md-8 col-lg-6 col-xxl-3">
          <div class="card mb-0">
            <div class="card-body text-center">
              <h2 class="mb-4">Login Customer</h2>

              <!-- Alert for errors -->
              @if ($errors->any())
              <div style="color: red;">
                <ul>
                  @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
              @endif

              <form method="POST" action="{{ url('/login') }}">
                @csrf
                <div class="mb-3 text-start">
                  <label for="email" class="form-label">Username</label>
                  <input type="email" class="form-control" id="email" name="email" required autofocus>
                </div>
                <div class="mb-3 text-start">
                  <label for="password" class="form-label">Password</label>
                  <div class="input-group">
                    <input type="password" class="form-control" id="password" name="password" required>
                    <span class="input-group-text" id="togglePassword" style="cursor:pointer">
                      <i class="bi bi-eye"></i>
                    </span>
                  </div>
                </div>

                <button type="submit" class="btn btn-primary mb-2">Login</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="{{asset('libs/jquery/dist/jquery.min.js')}}"></script>
  <script src="{{asset('libs/bootstrap/dist/js/bootstrap.bundle.min.js')}}"></script>
  <script>
    document.getElementById('togglePassword').addEventListener('click', function() {
      const passwordField = document.getElementById('password');
      const passwordIcon = this.querySelector('i');

      if (passwordField.type === 'password') {
        passwordField.type = 'text';
        passwordIcon.classList.remove('bi-eye');
        passwordIcon.classList.add('bi-eye-slash');
      } else {
        passwordField.type = 'password';
        passwordIcon.classList.remove('bi-eye-slash');
        passwordIcon.classList.add('bi-eye');
      }
    });
  </script>
</body>
>>>>>>> 519817204e2598416ec72975b2a8e9cff4710d33

    @if ($errors->any())
      <div class="alert">
        <ul style="margin: 0; padding-left: 20px;">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ url('/login') }}">
      @csrf
      <label for="email" class="form-label">Username</label>
      <input type="email" class="form-control" id="email" name="email" required>

      <label for="password" class="form-label">Password</label>
      <input type="password" class="form-control" id="password" name="password" required>

      <button type="submit" class="btn-primary">Login</button>
    </form>
  </div>

</body>
</html>
