@extends('admin.layouts.app')

@section('content')
    <div class="bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Роли</h1>

        <a href="{{ route('admin.roles.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 mb-4 inline-flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M12 4v16m8-8H4" />
            </svg>
            Добавить роль
        </a>

        <table class="w-full border-collapse">
            <thead>
            <tr class="bg-gray-200">
                <th class="border p-2">
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center">
                        Название
                        <span class="ml-1">
                            @if (request('sort') === 'name' && request('direction') === 'asc')
                                ▼
                            @elseif (request('sort') === 'name' && request('direction') === 'desc')
                                ▲
                            @else
                                ↕
                            @endif
                        </span>
                    </a>
                </th>
                <th class="border p-2">Действия</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($roles as $role)
                <tr class="border-t hover:bg-gray-100">
                    <td class="border p-2">{{ $role->name }}</td>
                    <td class="border p-2 space-x-2">
                        <a href="{{ route('admin.roles.edit', $role) }}" class="inline-flex items-center text-green-500 hover:underline">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 113 3L12 15l-4 1 1-4 9.5-9.5z" />
                            </svg>
                            Редактировать
                        </a>
                        <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="inline">
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
            {{ $roles->links('vendor.pagination.tailwind') }}
        </div>
    </div>
@endsection
