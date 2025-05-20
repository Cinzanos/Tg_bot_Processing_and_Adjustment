@extends('admin.layouts.app')

@section('content')
    <div class="bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Смены</h1>

        <!-- Форма поиска и фильтрации -->
        <div class="mb-4">
            <form method="GET" action="{{ route('admin.shifts.index') }}" class="flex space-x-4">
                <!-- Поиск по номеру смены -->
                <div>
                    <label for="shift_number" class="block text-sm font-medium text-gray-700">Номер смены</label>
                    <input type="text" name="shift_number" id="shift_number" class="mt-1 block w-full border rounded p-2" value="{{ request('shift_number') }}" placeholder="Введите номер смены">
                </div>

                <!-- Поиск по дате -->
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700">Дата</label>
                    <input type="date" name="date" id="date" class="mt-1 block w-full border rounded p-2" value="{{ request('date') }}">
                </div>

                <!-- Поиск по участку -->
                <div>
                    <label for="section_name" class="block text-sm font-medium text-gray-700">Участок</label>
                    <input type="text" name="section_name" id="section_name" class="mt-1 block w-full border rounded p-2" value="{{ request('section_name') }}" placeholder="Введите название участка">
                </div>

                <!-- Кнопка поиска -->
                <div class="mt-6">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        Искать
                    </button>
                </div>
            </form>
        </div>

        <a href="{{ route('admin.shifts.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 mb-4 inline-flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M12 4v16m8-8H4" />
            </svg>
            Добавить смену
        </a>

        <table class="w-full border-collapse">
            <thead>
            <tr class="bg-gray-200">
                <th class="border p-2">
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'shift_number', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center">
                        Номер смены
                        <span class="ml-1">
                            @if (request('sort') === 'shift_number' && request('direction') === 'asc')
                                ▼
                            @elseif (request('sort') === 'shift_number' && request('direction') === 'desc')
                                ▲
                            @else
                                ↕
                            @endif
                        </span>
                    </a>
                </th>
                <th class="border p-2">
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'date', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center">
                        Дата
                        <span class="ml-1">
                            @if (request('sort') === 'date' && request('direction') === 'asc')
                                ▼
                            @elseif (request('sort') === 'date' && request('direction') === 'desc')
                                ▲
                            @else
                                ↕
                            @endif
                        </span>
                    </a>
                </th>
                <th class="border p-2">
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'section.name', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center">
                        Участок
                        <span class="ml-1">
                            @if (request('sort') === 'section.name' && request('direction') === 'asc')
                                ▼
                            @elseif (request('sort') === 'section.name' && request('direction') === 'desc')
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
            @foreach ($shifts as $shift)
                <tr class="border-t hover:bg-gray-100">
                    <td class="border p-2">{{ $shift->shift_number }}</td>
                    <td class="border p-2">{{ $shift->date }}</td>
                    <td class="border p-2">{{ $shift->section->name ?? '-' }}</td>
                    <td class="border p-2 space-x-2">
                        <a href="{{ route('admin.shifts.show', $shift) }}" class="inline-flex items-center text-blue-500 hover:underline">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path d="M2.458 12C3.732 7.943 7.522 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7s-8.268-2.943-9.542-7z" />
                            </svg>
                            Просмотр
                        </a>
                        <a href="{{ route('admin.shifts.edit', $shift) }}" class="inline-flex items-center text-green-500 hover:underline">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 113 3L12 15l-4 1 1-4 9.5-9.5z" />
                            </svg>
                            Редактировать
                        </a>
                        <form action="{{ route('admin.shifts.destroy', $shift) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Вы уверены?')" class="inline-flex items-center text-red-500 hover:underline">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M19 7l-.867 12.142A2 2 0 0116.162 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m2 0a2 2 0 01-2-2H9a2 2 0 01-2 2h10z" />
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
            {{ $shifts->appends(request()->query())->links('vendor.pagination.tailwind') }}
        </div>
    </div>
@endsection
