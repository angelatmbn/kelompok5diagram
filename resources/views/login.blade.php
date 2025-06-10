<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login - Cafe Diagram</title>
  <link rel="shortcut icon" type="image/png" href="{{ asset('images/logos/favicon.png') }}" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * {
      font-family: 'Inter', sans-serif;
    }

    body {
      margin: 0;
      padding: 0;
      background: linear-gradient(to right, #f5f5f5, #e0d6c3); /* gradasi beige */
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .login-card {
      background-color: #ffffff;
      border-radius: 12px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
      padding: 40px;
      width: 100%;
      max-width: 400px;
    }

    .logo {
      display: flex;
      justify-content: center;
      margin-bottom: 24px;
    }

    .form-label {
      font-weight: 600;
      color: #4e342e; /* warna coklat gelap */
    }

    .form-control {
      border-radius: 8px;
      border: 1px solid #ccc;
      padding: 10px;
    }

    .btn-primary {
      background-color: #6d4c41; /* warna coklat */
      border: none;
      padding: 12px;
      border-radius: 8px;
      font-weight: 600;
    }

    .btn-primary:hover {
      background-color: #5d4037;
    }

    .alert {
      color: red;
      font-size: 14px;
      margin-bottom: 15px;
    }

    .text-center {
      text-align: center;
    }
  </style>
</head>

<body>

  <div class="login-card">
    <div class="logo">
      <img src="{{ asset('images/logos/mukena.PNG') }}" width="150" alt="Cafe Diagram Logo">
    </div>

    @if ($errors->any())
      <div class="alert">
        <ul>
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ url('/login') }}">
      @csrf
      <div class="mb-3">
        <label for="email" class="form-label">Username</label>
        <input type="email" class="form-control" id="email" name="email" required>
      </div>

      <div class="mb-4">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="password" required>
      </div>

      <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
  </div>

</body>
</html>
