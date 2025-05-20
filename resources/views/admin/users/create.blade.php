@extends('admin.layouts.app')

@section('content')
    <div class="bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Добавить сотрудника</h1>
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="full_name" class="block text-sm font-medium text-gray-700">ФИО</label>
                <input type="text" name="full_name" id="full_name" class="mt-1 block w-full border rounded p-2" value="{{ old('full_name') }}">
                @error('full_name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label for="telegram_id" class="block text-sm font-medium text-gray-700">Telegram ID</label>
                <input type="text" name="telegram_id" id="telegram_id" class="mt-1 block w-full border rounded p-2" value="{{ old('telegram_id') }}">
                @error('telegram_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label for="role_id" class="block text-sm font-medium text-gray-700">Профессия</label>
                <select name="role_id" id="role_id" class="mt-1 block w-full border rounded p-2" onchange="toggleAdminFields()">
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
                @error('role_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div id="admin-fields" class="hidden">
                <div class="mb-4">
                    <label for="login" class="block text-sm font-medium text-gray-700">Логин</label>
                    <input type="text" name="login" id="login" class="mt-1 block w-full border rounded p-2" value="{{ old('login') }}">
                    @error('login')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700">Пароль</label>
                    <input type="password" name="password" id="password" class="mt-1 block w-full border rounded p-2">
                    @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                    Сохранить
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
        toggleAdminFields();
    </script>
@endsection
