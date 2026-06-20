<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login - Hola Santana</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="login-page">
    <form class="login-card" method="post" action="{{ route('admin.login.store') }}">
        @csrf
        <p class="eyebrow">Hola Santana</p>
        <h1>Admin Login</h1>
        @error('email')<div class="error">{{ $message }}</div>@enderror
        <label>Email<input name="email" type="email" value="{{ old('email') }}" required autofocus></label>
        <label>Password<input name="password" type="password" required></label>
        <label class="checkbox"><input name="remember" type="checkbox" value="1"> Remember me</label>
        <button class="button full" type="submit">Sign in</button>
        <p class="hint">Seeded admin: admin@holasantana.com / Admin@12345</p>
    </form>
</body>
</html>
