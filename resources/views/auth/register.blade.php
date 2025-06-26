<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register - Cafe Diagram</title>
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
      background-color: rgb(46, 37, 28);
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
      color: rgb(255, 255, 255);
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
    }
  </style>
</head>

<body>
  <div class="login-card">
    <div class="logo">
      <img src="{{ asset('images/logos/diagram.png') }}" alt="Cafe Diagram Logo">
    </div>

    @if ($errors->any())
      <div class="alert">
        <ul style="margin: 0; padding-left: 20px;">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
      @csrf

      <label class="form-label" for="name">Nama</label>
      <input type="text" class="form-control" name="name" id="name" required>

      <label class="form-label" for="email">Email</label>
      <input type="email" class="form-control" name="email" id="email" required>

      <label class="form-label" for="password">Password</label>
      <input type="password" class="form-control" name="password" id="password" required>

      <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
      <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" required>

      <button type="submit" class="btn-primary">Register</button>

      <div style="text-align: center; margin-top: 16px;">
        <span style="color: white;">Already have an account?</span><br>
        <a href="{{ route('login') }}" style="color: #e0d6c3; font-weight: 600; text-decoration: none;">
          Login now
        </a>
      </div>
    </form>
  </div>
</body>

</html>