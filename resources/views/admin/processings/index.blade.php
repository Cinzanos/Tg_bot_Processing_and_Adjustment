@extends('admin.layouts.app')

@section('content')
    <div class="bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Обработка</h1>

        <a href="{{ route('admin.processings.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 mb-4 inline-flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M12 4v16m8-8H4" />
            </svg>
            Добавить обработку
        </a>

        <table class="w-full datatable">
            <thead>
            <tr>
                <th>Оператор</th>
                <th>Станок</th>
                <th>Смена</th>
                <th>Время начала</th>
                <th>Время завершения</th>
                <th>Длительность (мин)</th>
                <th>Действия</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($processings as $processing)
                <tr>
                    <td>{{ $processing->user->full_name }}</td>
                    <td>{{ $processing->equipment->machine_number }}</td>
                    <td>{{ $processing->shift->shift_number }}</td>
                    <td>{{ $processing->start_time }}</td>
                    <td>{{ $processing->end_time ?? '-' }}</td>
                    <td>{{ $processing->duration ?? '-' }}</td>
                    <td class="space-x-2">
                        <a href="{{ route('admin.processings.show', $processing) }}" class="inline-flex items-center text-blue-500 hover:underline">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path d="M2.458 12C3.732 7.943 7.522 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7s-8.268-2.943-9.542-7z" />
                            </svg>
                            Просмотр
                        </a>

                        <a href="{{ route('admin.processings.edit', $processing) }}" class="inline-flex items-center text-green-500 hover:underline">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 113 3L12 15l-4 1 1-4 9.5-9.5z" />
                            </svg>
                            Редактировать
                        </a>

                        <form action="{{ route('admin.processings.destroy', $processing) }}" method="POST" class="inline">
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
    </div>
@endsection
