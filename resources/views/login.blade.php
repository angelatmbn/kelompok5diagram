<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
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
    }
  </style>
</head>

<body>
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

</html>