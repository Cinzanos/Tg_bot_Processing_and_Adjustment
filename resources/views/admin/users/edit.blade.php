@extends('admin.layouts.app')

@section('content')
    <div class="bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Редактировать сотрудника</h1>
        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="full_name" class="block text-sm font-medium text-gray-700">ФИО</label>
                <input type="text" name="full_name" id="full_name" class="mt-1 block w-full border rounded p-2" value="{{ old('full_name', $user->full_name) }}">
                @error('full_name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label for="telegram_id" class="block text-sm font-medium text-gray-700">Telegram ID</label>
                <input type="text" name="telegram_id" id="telegram_id" class="mt-1 block w-full border rounded p-2" value="{{ old('telegram_id', $user->telegram_id) }}">
                @error('telegram_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label for="role_id" class="block text-sm font-medium text-gray-700">Профессия</label>
                <select name="role_id" id="role_id" class="mt-1 block w-full border rounded p-2" onchange="toggleAdminFields()">
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
                @error('role_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div id="admin-fields" class="{{ $user->role && $user->role->name !== 'Администратор' ? 'hidden' : '' }}">
                <div class="mb-4">
                    <label for="login" class="block text-sm font-medium text-gray-700">Логин</label>
                    <input type="text" name="login" id="login" class="mt-1 block w-full border rounded p-2" value="{{ old('login', $user->login) }}">
                    @error('login')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700">Пароль (оставьте пустым, чтобы не менять)</label>
                    <input type="password" name="password" id="password" class="mt-1 block w-full border rounded p-2">
                    @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
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
            const roleId = document.getElementById('role_id').value;
            const adminRoleId = @json($adminRoleId);
            const adminFields = document.getElementById('admin-fields');
            adminFields.classList.toggle('hidden', roleId != adminRoleId);
        }
    </script>
@endsection
