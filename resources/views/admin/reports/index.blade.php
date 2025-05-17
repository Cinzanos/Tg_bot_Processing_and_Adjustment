@extends('admin.layouts.app')

@section('content')
    <div class="bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Отчеты</h1>
        <form action="{{ route('admin.reports.index') }}" method="GET" class="mb-4">
            <div class="flex space-x-4">
                <div>
                    <label for="section" class="block text-sm font-medium text-gray-700">Участок</label>
                    <input type="text" name="section" id="section" class="mt-1 block w-full border rounded p-2" value="{{ request('section') }}">
                </div>
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700">Дата</label>
                    <input type="date" name="date" id="date" class="mt-1 block w-full border rounded p-2" value="{{ request('date') }}">
                </div>
                <div>
                    <label for="shift_number" class="block text-sm font-medium text-gray-700">Смена</label>
                    <input type="text" name="shift_number" id="shift_number" class="mt-1 block w-full border rounded p-2" value="{{ request('shift_number') }}">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Фильтровать</button>
                </div>
            </div>
        </form>
        <a href="{{ route('admin.reports.export') }}?{{ http_build_query(request()->query()) }}" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 mb-4 inline-block inline-flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1M12 12v9m0 0l-3-3m3 3l3-3M12 3v9" />
            </svg>
            Экспорт в Excel
        </a>
        <table class="w-full datatable">
            <thead>
            <tr>
                <th>№</th>
                <th>Дата</th>
                <th>Время</th>
                <th>Смена/Бригада</th>
                <th>Профессия</th>
                <th>Сообщение создал</th>
                <th>Участок</th>
                <th>Оборудование</th>
                <th>Обработка: Время начала</th>
                <th>Обработка: Время завершения</th>
                <th>Обработка: Время</th>
                <th>Наладка: Время начала</th>
                <th>Наладка: Время завершения</th>
                <th>Наладка: Время</th>
                <th>Ожидание наладки: Время начала</th>
                <th>Ожидание наладки: Время завершения</th>
                <th>Ожидание наладки: Время</th>
                <th>Простой: Время начала</th>
                <th>Простой: Время завершения</th>
                <th>Простой: Время</th>
                <th>Причина простоя</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($operations as $operation)
                <tr>
                    <td>{{ $operation->id }}</td>
                    <td>{{ $operation->shift->date }}</td>
                    <td>{{ $operation->shift->shift_number }}</td>
                    <td>{{ $operation->shift->shift_number }}</td>
                    <td>{{ $operation->user->role }}</td>
                    <td>{{ $operation->user->full_name }}</td>
                    <td>{{ $operation->equipment->section }}</td>
                    <td>{{ $operation->equipment->machine_number }}</td>
                    <td>{{ $operation->start_time }}</td>
                    <td>{{ $operation->end_time ?? '-' }}</td>
                    <td>{{ $operation->duration ?? '-' }}</td>
                    <td>{{ $operation->adjustments->start_time ?? '-' }}</td>
                    <td>{{ $operation->adjustments->end_time ?? '-' }}</td>
                    <td>{{ $operation->adjustments->duration ?? '-' }}</td>
                    <td>{{ $operation->adjustment_waitings->start_time ?? '-' }}</td>
                    <td>{{ $operation->adjustment_waitings->end_time ?? '-' }}</td>
                    <td>{{ $operation->adjustment_waitings->duration ?? '-' }}</td>
                    <td>{{ $operation->downtimes->start_time ?? '-' }}</td>
                    <td>{{ $operation->downtimes->end_time ?? '-' }}</td>
                    <td>{{ $operation->downtimes->duration ?? '-' }}</td>
                    <td>{{ $operation->downtimes->reason ?? '-' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <!-- Визуализация циклов состояния оборудования -->
        <div class="mt-6">
            <h2 class="text-xl font-bold mb-4">Циклы состояния оборудования</h2>
            <canvas id="equipmentCycleChart" width="400" height="200"></canvas>
        </div>
    </div>

    <script>
        // Инициализация Chart.js для визуализации циклов
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('equipmentCycleChart').getContext('2d');
            const chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: [
                        'Обработка',
                        'Наладка',
                        'Ожидание наладки',
                        'Простой'
                    ],
                    datasets: [{
                        label: 'Длительность (минуты)',
                        data: [
                            @php
                                $processingDuration = $operations->avg('duration') ?? 0;
                                $adjustmentDuration = $operations->avg('adjustments.duration') ?? 0;
                                $waitingDuration = $operations->avg('adjustment_waitings.duration') ?? 0;
                                $downtimeDuration = $operations->avg('downtimes.duration') ?? 0;
                                echo "$processingDuration, $adjustmentDuration, $waitingDuration, $downtimeDuration";
                            @endphp
                        ],
                        backgroundColor: [
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(255, 99, 132, 0.2)'
                        ],
                        borderColor: [
                            'rgba(75, 192, 192, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(255, 99, 132, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Длительность (минуты)'
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
