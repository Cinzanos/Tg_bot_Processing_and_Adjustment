@extends('admin.layouts.app')

@section('content')
    <div class="bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Простои</h1>

        <!-- Форма поиска и фильтрации -->
        <div class="mb-4">
            <form method="GET" action="{{ route('admin.downtimes.index') }}" class="flex space-x-4">
                <!-- Поиск по ФИО инициатора -->
                <div>
                    <label for="initiator_name" class="block text-sm font-medium text-gray-700">Инициатор</label>
                    <input type="text" name="initiator_name" id="initiator_name" class="mt-1 block w-full border rounded p-2" value="{{ request('initiator_name') }}" placeholder="Введите ФИО">
                </div>

                <!-- Поиск по участку -->
                <div>
                    <label for="section_name" class="block text-sm font-medium text-gray-700">Участок</label>
                    <input type="text" name="section_name" id="section_name" class="mt-1 block w-full border rounded p-2" value="{{ request('section_name') }}" placeholder="Введите название участка">
                </div>

                <!-- Поиск по номеру станка -->
                <div>
                    <label for="machine_number" class="block text-sm font-medium text-gray-700">Станок</label>
                    <input type="text" name="machine_number" id="machine_number" class="mt-1 block w-full border rounded p-2" value="{{ request('machine_number') }}" placeholder="Введите номер станка">
                </div>

                <!-- Поиск по номеру смены -->
                <div>
                    <label for="shift_number" class="block text-sm font-medium text-gray-700">Смена</label>
                    <input type="text" name="shift_number" id="shift_number" class="mt-1 block w-full border rounded p-2" value="{{ request('shift_number') }}" placeholder="Введите номер смены">
                </div>

                <!-- Поиск по времени начала -->
                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700">Время начала</label>
                    <input type="datetime-local" name="start_time" id="start_time" class="mt-1 block w-full border rounded p-2" value="{{ request('start_time') }}">
                </div>

                <!-- Кнопка поиска -->
                <div class="mt-6">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        Искать
                    </button>
                </div>
            </form>
        </div>

        <a href="{{ route('admin.downtimes.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 mb-4 inline-flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M12 4v16m8-8H4" />
            </svg>
            Добавить простой
        </a>

        <table class="w-full border-collapse">
            <thead>
            <tr class="bg-gray-200">
                <th class="border p-2">
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'user.full_name', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center">
                        Инициатор
                        <span class="ml-1">
                            @if (request('sort') === 'user.full_name' && request('direction') === 'asc')
                                ▼
                            @elseif (request('sort') === 'user.full_name' && request('direction') === 'desc')
                                ▲
                            @else
                                ↕
                            @endif
                        </span>
                    </a>
                </th>
                <th class="border p-2">
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'equipment.machine_number', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center">
                        Участок
                        <span class="ml-1">
                            @if (request('sort') === 'equipment.section.name' && request('direction') === 'asc')
                                ▼
                            @elseif (request('sort') === 'equipment.section.name' && request('direction') === 'desc')
                                ▲
                            @else
                                ↕
                            @endif
                        </span>
                    </a>
                </th>
                <th class="border p-2">
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'equipment.machine_number', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center">
                        Станок
                        <span class="ml-1">
                            @if (request('sort') === 'equipment.machine_number' && request('direction') === 'asc')
                                ▼
                            @elseif (request('sort') === 'equipment.machine_number' && request('direction') === 'desc')
                                ▲
                            @else
                                ↕
                            @endif
                        </span>
                    </a>
                </th>
                <th class="border p-2">
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'shift.shift_number', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center">
                        Смена
                        <span class="ml-1">
                            @if (request('sort') === 'shift.shift_number' && request('direction') === 'asc')
                                ▼
                            @elseif (request('sort') === 'shift.shift_number' && request('direction') === 'desc')
                                ▲
                            @else
                                ↕
                            @endif
                        </span>
                    </a>
                </th>
                <th class="border p-2">
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'start_time', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center">
                        Время начала
                        <span class="ml-1">
                            @if (request('sort') === 'start_time' && request('direction') === 'asc')
                                ▼
                            @elseif (request('sort') === 'start_time' && request('direction') === 'desc')
                                ▲
                            @else
                                ↕
                            @endif
                        </span>
                    </a>
                </th>
                <th class="border p-2">Время завершения</th>
                <th class="border p-2">Длительность (мин)</th>
                <th class="border p-2">Причина</th>
                <th class="border p-2">Действия</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($downtimes as $downtime)
                <tr class="border-t hover:bg-gray-100">
                    <td class="border p-2">{{ $downtime->user->full_name }}</td>
                    <td class="border p-2">{{ $downtime->equipment->section->name ?? '-' }}</td>
                    <td class="border p-2">{{ $downtime->equipment->machine_number }}</td>
                    <td class="border p-2">{{ $downtime->shift->shift_number }}</td>
                    <td class="border p-2">{{ $downtime->start_time }}</td>
                    <td class="border p-2">{{ $downtime->end_time ?? '-' }}</td>
                    <td class="border p-2">{{ $downtime->duration ?? '-' }}</td>
                    <td class="border p-2">{{ $downtime->reason }}</td>
                    <td class="border p-2 space-x-2">
                        <a href="{{ route('admin.downtimes.show', $downtime) }}" class="inline-flex items-center text-blue-500 hover:underline">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path d="M2.458 12C3.732 7.943 7.522 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7s-8.268-2.943-9.542-7z" />
                            </svg>
                            Просмотр
                        </a>
                        <a href="{{ route('admin.downtimes.edit', $downtime) }}" class="inline-flex items-center text-green-500 hover:underline">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 113 3L12 15l-4 1 1-4 9.5-9.5z" />
                            </svg>
                            Редактировать
                        </a>
                        <form action="{{ route('admin.downtimes.destroy', $downtime) }}" method="POST" class="inline">
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
            {{ $downtimes->appends(request()->query())->links('vendor.pagination.tailwind') }}
        </div>
    </div>
@endsection
