<!-- resources/views/admin/auth/login.blade.php -->
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход в админку</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
<div class="bg-white p-8 rounded shadow w-full max-w-md">
    <h1 class="text-2xl font-bold mb-6 text-center">Вход в админ-панель</h1>
    @if ($errors->any())
        <div class="bg-red-500 text-white p-4 rounded mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('admin.login') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label for="login" class="block text-sm font-medium text-gray-700">Логин</label>
            <input type="text" name="login" id="login" class="mt-1 block w-full border rounded p-2" value="{{ old('login') }}">
        </div>
        <div class="mb-6">
            <label for="password" class="block text-sm font-medium text-gray-700">Пароль</label>
            <input type="password" name="password" id="password" class="mt-1 block w-full border rounded p-2">
        </div>
        <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 flex items-center justify-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M5 12h14M12 5l7 7-7 7" />
            </svg>
            Войти
        </button>
    </form>
</div>
</body>
</html>
