@extends('admin.layouts.app')

@section('content')
    <div class="bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Сотрудники</h1>

        <!-- Форма поиска -->
        <div class="mb-4">
            <form method="GET" action="{{ route('admin.users.index') }}" class="flex space-x-4">
                <div>
                    <label for="full_name" class="block text-sm font-medium text-gray-700">ФИО</label>
                    <input type="text" name="full_name" id="full_name" class="mt-1 block w-full border rounded p-2" value="{{ request('full_name') }}" placeholder="Введите ФИО">
                </div>
                <div>
                    <label for="role_id" class="block text-sm font-medium text-gray-700">Профессия</label>
                    <select name="role_id" id="role_id" class="mt-1 block w-full border rounded p-2">
                        <option value="">Все профессии</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mt-6">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        Искать
                    </button>
                </div>
            </form>
        </div>

        <a href="{{ route('admin.users.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 mb-4 inline-flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M12 4v16m8-8H4" />
            </svg>
            Добавить сотрудника
        </a>

        <table class="w-full border-collapse">
            <thead>
            <tr class="bg-gray-200">
                <th class="border p-2">
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'full_name', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center">
                        ФИО
                        <span class="ml-1">
                            @if (request('sort') === 'full_name' && request('direction') === 'asc')
                                ▼
                            @elseif (request('sort') === 'full_name' && request('direction') === 'desc')
                                ▲
                            @else
                                ↕
                            @endif
                        </span>
                    </a>
                </th>
                <th class="border p-2">Telegram ID</th>
                <th class="border p-2">Профессия</th>
                <th class="border p-2">Действия</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($users as $user)
                <tr class="border-t hover:bg-gray-100">
                    <td class="border p-2">{{ $user->full_name }}</td>
                    <td class="border p-2">{{ $user->telegram_id }}</td>
                    <td class="border p-2">{{ $user->role_name }}</td>
                    <td class="border p-2 space-x-2">
                        <a href="{{ route('admin.users.show', $user) }}" class="inline-flex items-center text-blue-500 hover:underline">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path d="M2.458 12C3.732 7.943 7.522 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7s-8.268-2.943-9.542-7z" />
                            </svg>
                            Просмотр
                        </a>

                        <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center text-green-500 hover:underline">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 113 3L12 15l-4 1 1-4 9.5-9.5z" />
                            </svg>
                            Редактировать
                        </a>

                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Вы уверены?')" class="inline-flex items-center text-red-500 hover:underline">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m2 0a2 2 0 01-2-2H9a2 2 0 01-2 2h10z" />
                                </svg>
                                Удалить
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <!-- Пагинация внизу по центру с Tailwind -->
        <div class="mt-4 flex justify-center">
            {{ $users->appends(request()->query())->links('vendor.pagination.tailwind') }}
        </div>
    </div>
@endsection
