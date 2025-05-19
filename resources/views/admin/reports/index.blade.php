@extends('admin.layouts.app')

@section('content')
    <div class="bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Отчеты</h1>
        <form action="{{ route('admin.reports.index') }}" method="GET" class="mb-4">
            <div class="flex space-x-4">
                <div>
                    <label for="section_id" class="block text-sm font-medium text-gray-700">Участок</label>
                    <select name="section_id" id="section_id" class="mt-1 block w-full border rounded p-2">
                        <option value="">Все участки</option>
                        @foreach ($sections as $section)
                            <option value="{{ $section->id }}" {{ request('section_id') == $section->id ? 'selected' : '' }}>{{ $section->name }}</option>
                        @endforeach
                    </select>
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
                <th>Смена/Бригада</th>
                <th>Участок (смена)</th>
                <th>Сотрудник</th>
                <th>Участок (оборудование)</th>
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
                    <td>{{ $operation->shift->date ?? '-' }}</td>
                    <td>{{ $operation->shift->shift_number ?? '-' }}</td>
                    <td>{{ $operation->shift->section->name ?? '-' }}</td>
                    <td>{{ $operation->user->full_name ?? '-' }}</td>
                    <td>{{ $operation->equipment->section->name ?? '-' }}</td>
                    <td>{{ $operation->equipment->machine_number }}</td>
                    <td>{{ $operation->start_time ?? '-' }}</td>
                    <td>{{ $operation->end_time ?? '-' }}</td>
                    <td>{{ $operation->duration ?? '-' }}</td>
                    <td>{{ $operation->equipment->adjustments->first()->start_time ?? '-' }}</td>
                    <td>{{ $operation->equipment->adjustments->first()->end_time ?? '-' }}</td>
                    <td>{{ $operation->equipment->adjustments->first()->duration ?? '-' }}</td>
                    <td>{{ $operation->equipment->adjustmentWaitings->first()->start_time ?? '-' }}</td>
                    <td>{{ $operation->equipment->adjustmentWaitings->first()->end_time ?? '-' }}</td>
                    <td>{{ $operation->equipment->adjustmentWaitings->first()->duration ?? '-' }}</td>
                    <td>{{ $operation->equipment->downtimes->first()->start_time ?? '-' }}</td>
                    <td>{{ $operation->equipment->downtimes->first()->end_time ?? '-' }}</td>
                    <td>{{ $operation->equipment->downtimes->first()->duration ?? '-' }}</td>
                    <td>{{ $operation->equipment->downtimes->first()->reason ?? '-' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <!-- Gantt-диаграммы по дням -->
        <div class="mt-6">
            <h2 class="text-xl font-bold mb-4">График работы оборудования</h2>
            @php
                // Группировка операций по дням
                $operationsByDate = $operations->groupBy(function ($operation) {
                    return $operation->shift->date ?? 'Unknown';
                })->filter(function ($group, $date) {
                    return $date !== 'Unknown';
                });

                foreach ($operationsByDate as $date => $dateOperations) {
                    // Группировка по оборудованию
                    $equipmentGroups = $dateOperations->groupBy('equipment_id')->map(function ($group) {
                        return $group->first()->equipment;
                    })->sortBy('machine_number');

                    // Подготовка данных для Gantt-диаграммы
                    $labels = $equipmentGroups->map(function ($equipment) {
                        return $equipment->machine_number . ' (' . $equipment->machine_type . ')';
                    })->values()->toArray();

                    // Отладка данных
                    echo "<div class='mt-2 text-sm'>Debug for $date: " . count($labels) . " станков, operations count: " . $dateOperations->count() . "</div>";

                    $datasets = [];
                    $index = 0;
                    foreach ($equipmentGroups as $equipment) {
                        $equipmentOperations = $dateOperations->where('equipment_id', $equipment->id);

                        // Обработка (Processing)
                        foreach ($equipmentOperations as $operation) {
                            if ($operation->start_time && $operation->end_time) {
                                $startDateTime = new DateTime($operation->start_time);
                                $endDateTime = new DateTime($operation->end_time);
                                $startOfDay = new DateTime($startDateTime->format('Y-m-d') . ' 08:00:00');
                                $endOfDay = new DateTime($startDateTime->format('Y-m-d') . ' 22:00:00');

                                if ($startDateTime >= $startOfDay && $startDateTime <= $endOfDay) {
                                    $startMinutes = max(0, ($startDateTime->getTimestamp() - $startOfDay->getTimestamp()) / 60);
                                    $endMinutes = min(840, ($endDateTime->getTimestamp() - $startOfDay->getTimestamp()) / 60);
                                    $datasets[] = [
                                        'label' => 'Обработка',
                                        'data' => [[$startMinutes, $endMinutes]],
                                        'backgroundColor' => 'rgba(34, 197, 94, 0.5)', // Зеленый
                                        'borderColor' => 'rgba(34, 197, 94, 1)',
                                        'borderWidth' => 1,
                                        'yAxisID' => 'y',
                                        'index' => $index
                                    ];
                                }
                            }
                        }

                        // Наладка (Adjustments)
                        $adjustments = $equipment->adjustments()->whereBetween('start_time', [
                            $date . ' 08:00:00',
                            $date . ' 22:00:00'
                        ])->get();
                        foreach ($adjustments as $adjustment) {
                            if ($adjustment->start_time && $adjustment->end_time) {
                                $startDateTime = new DateTime($adjustment->start_time);
                                $endDateTime = new DateTime($adjustment->end_time);
                                $startOfDay = new DateTime($date . ' 08:00:00');
                                $startMinutes = ($startDateTime->getTimestamp() - $startOfDay->getTimestamp()) / 60;
                                $endMinutes = ($endDateTime->getTimestamp() - $startOfDay->getTimestamp()) / 60;
                                $datasets[] = [
                                    'label' => 'Наладка',
                                    'data' => [[$startMinutes, $endMinutes]],
                                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)', // Голубой
                                    'borderColor' => 'rgba(59, 130, 246, 1)',
                                    'borderWidth' => 1,
                                    'yAxisID' => 'y',
                                    'index' => $index
                                ];
                            }
                        }

                        // Простои (Downtimes)
                        $downtimes = $equipment->downtimes()->whereBetween('start_time', [
                            $date . ' 08:00:00',
                            $date . ' 22:00:00'
                        ])->get();
                        foreach ($downtimes as $downtime) {
                            if ($downtime->start_time && $downtime->end_time) {
                                $startDateTime = new DateTime($downtime->start_time);
                                $endDateTime = new DateTime($downtime->end_time);
                                $startOfDay = new DateTime($date . ' 08:00:00');
                                $startMinutes = ($startDateTime->getTimestamp() - $startOfDay->getTimestamp()) / 60;
                                $endMinutes = ($endDateTime->getTimestamp() - $startOfDay->getTimestamp()) / 60;
                                $datasets[] = [
                                    'label' => 'Простой',
                                    'data' => [[$startMinutes, $endMinutes]],
                                    'backgroundColor' => 'rgba(234, 179, 8, 0.5)', // Желтый
                                    'borderColor' => 'rgba(234, 179, 8, 1)',
                                    'borderWidth' => 1,
                                    'yAxisID' => 'y',
                                    'index' => $index
                                ];
                            }
                        }

                        $index++;
                    }
            @endphp
            <div class="mb-8">
                <h3 class="text-lg font-semibold mb-2">Дата: {{ \Carbon\Carbon::parse($date)->format('d.m.Y') }}</h3>
                <canvas id="ganttChart_{{ \Carbon\Carbon::parse($date)->format('Ymd') }}" width="800" height="{{ max(200, count($labels) * 50) }}" style="display: block; width: 800px; height: {{ max(200, count($labels) * 50) }}px; max-height: 600px; overflow-y: auto;"></canvas>
                <script>
                    const ctx_{{ \Carbon\Carbon::parse($date)->format('Ymd') }} = document.getElementById('ganttChart_{{ \Carbon\Carbon::parse($date)->format('Ymd') }}').getContext('2d');
                    const chart_{{ \Carbon\Carbon::parse($date)->format('Ymd') }} = new Chart(ctx_{{ \Carbon\Carbon::parse($date)->format('Ymd') }}, {
                        type: 'bar',
                        data: {
                            labels: @json($labels),
                            datasets: @json($datasets)
                        },
                        options: {
                            indexAxis: 'y',
                            scales: {
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Время'
                                    },
                                    min: 0,
                                    max: 840, // 14 часов * 60 минут = 840 минут
                                    ticks: {
                                        stepSize: 60, // Шаг в 1 час
                                        callback: function(value) {
                                            const hours = Math.floor(value / 60) + 8;
                                            const minutes = value % 60;
                                            return `${hours.toString().padStart(2, '0')}:00`;
                                        }
                                    }
                                },
                                y: {
                                    title: {
                                        display: true,
                                        text: 'Наименование/Номер Станка'
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'top'
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const dataset = context.dataset;
                                            const data = dataset.data[context.dataIndex];
                                            const startMinutes = data[0];
                                            const endMinutes = data[1];
                                            const startHours = Math.floor(startMinutes / 60) + 8;
                                            const startMins = startMinutes % 60;
                                            const endHours = Math.floor(endMinutes / 60) + 8;
                                            const endMins = endMinutes % 60;
                                            return `${dataset.label}: ${startHours.toString().padStart(2, '0')}:${startMins.toString().padStart(2, '0')} - ${endHours.toString().padStart(2, '0')}:${endMins.toString().padStart(2, '0')}`;
                                        }
                                    }
                                }
                            },
                            parsing: {
                                xAxisKey: 'data'
                            },
                            barThickness: 55 // Увеличен размер полос для лучшей видимости
                        }
                    });
                </script>
            </div>
            @php
                }
            @endphp
        </div>
    </div>
@endsection

@section('scripts')
    // Скрипты уже добавлены inline для каждого графика
@endsection
