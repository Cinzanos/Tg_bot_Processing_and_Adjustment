@extends('admin.layouts.app')

@section('content')
    <div class="bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Редактировать пользователя</h1>
        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="full_name" class="block text-sm font-medium text-gray-700">ФИО</label>
                <input type="text" name="full_name" id="full_name" class="mt-1 block w-full border rounded p-2" value="{{ old('full_name', $user->full_name) }}">
            </div>
            <div class="mb-4">
                <label for="telegram_id" class="block text-sm font-medium text-gray-700">Telegram ID</label>
                <input type="text" name="telegram_id" id="telegram_id" class="mt-1 block w-full border rounded p-2" value="{{ old('telegram_id', $user->telegram_id) }}">
            </div>
            <div class="mb-4">
                <label for="role" class="block text-sm font-medium text-gray-700">Профессия</label>
                <select name="role" id="role" class="mt-1 block w-full border rounded p-2" onchange="toggleAdminFields()">
                    <option value="master" {{ old('role', $user->role) == 'master' ? 'selected' : '' }}>Мастер</option>
                    <option value="brigadier" {{ old('role', $user->role) == 'brigadier' ? 'selected' : '' }}>Бригадир</option>
                    <option value="operator" {{ old('role', $user->role) == 'operator' ? 'selected' : '' }}>Оператор</option>
                    <option value="adjuster" {{ old('role', $user->role) == 'adjuster' ? 'selected' : '' }}>Наладчик</option>
                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Админ</option>
                </select>
            </div>
            <div id="admin-fields" class="{{ $user->role !== 'admin' ? 'hidden' : '' }}">
                <div class="mb-4">
                    <label for="login" class="block text-sm font-medium text-gray-700">Логин</label>
                    <input type="text" name="login" id="login" class="mt-1 block w-full border rounded p-2" value="{{ old('login', $user->login) }}">
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700">Пароль (оставьте пустым, чтобы не менять)</label>
                    <input type="password" name="password" id="password" class="mt-1 block w-full border rounded p-2">
                </div>
                <div class="mb-4">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Подтверждение пароля</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="mt-1 block w-full border rounded p-2">
                </div>
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    Обновить
                </button>
                <a href="{{ route('admin.users.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Назад
                </a>
            </div>
        </form>
    </div>

    <script>
        function toggleAdminFields() {
            const role = document.getElementById('role').value;
            const adminFields = document.getElementById('admin-fields');
            adminFields.classList.toggle('hidden', role !== 'admin');
        }
    </script>
@endsection
