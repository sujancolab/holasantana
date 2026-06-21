<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Owner Login - Hola Santana</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="login-page">
    <form method="post" action="{{ route('owner.login.store') }}" class="login-card">
        @csrf
        <p class="eyebrow">Owner portal</p>
        <h1>Owner Login</h1>
        @if ($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif
        <label>Owner User ID<input name="owner_user_id" value="{{ old('owner_user_id') }}" required autofocus></label>
        <label>Owner Password<input type="password" name="owner_password" required></label>
        <button class="button full" type="submit">Login</button>
        <p class="hint"><a href="{{ route('home') }}">Back to website</a></p>
    </form>
</body>
</html>
