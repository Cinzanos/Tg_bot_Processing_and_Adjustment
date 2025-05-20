@extends('admin.layouts.app')

@section('content')
    <div class="bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Отчеты</h1>

        <!-- Форма поиска и фильтрации -->
        <form action="{{ route('admin.reports.index') }}" method="GET" class="mb-4">
            <div class="flex space-x-4">
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700">Дата</label>
                    <input type="date" name="date" id="date" class="mt-1 block w-full border rounded p-2" value="{{ request('date') }}">
                </div>
                <div>
                    <label for="shift_number" class="block text-sm font-medium text-gray-700">Смена</label>
                    <input type="text" name="shift_number" id="shift_number" class="mt-1 block w-full border rounded p-2" value="{{ request('shift_number') }}" placeholder="Введите номер смены">
                </div>
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
                    <label for="machine_number" class="block text-sm font-medium text-gray-700">Оборудование</label>
                    <input type="text" name="machine_number" id="machine_number" class="mt-1 block w-full border rounded p-2" value="{{ request('machine_number') }}" placeholder="Введите номер оборудования">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Фильтровать</button>
                </div>
            </div>
        </form>

        <!-- Кнопка экспорта -->
        <a href="{{ route('admin.reports.export') }}?{{ http_build_query(request()->query()) }}" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 mb-4 inline-block inline-flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1M12 12v9m0 0l-3-3m3 3l3-3M12 3v9" />
            </svg>
            Экспорт в Excel
        </a>

        <!-- Таблица -->
        <table class="w-full border-collapse">
            <thead>
            <tr class="bg-gray-200">
                <th class="border p-2">
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'id', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center">
                        №
                        <span class="ml-1">
                            @if (request('sort') === 'id' && request('direction') === 'asc')
                                ▼
                            @elseif (request('sort') === 'id' && request('direction') === 'desc')
                                ▲
                            @else
                                ↕
                            @endif
                        </span>
                    </a>
                </th>
                <th class="border p-2">
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'shift.date', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center">
                        Дата
                        <span class="ml-1">
                            @if (request('sort') === 'shift.date' && request('direction') === 'asc')
                                ▼
                            @elseif (request('sort') === 'shift.date' && request('direction') === 'desc')
                                ▲
                            @else
                                ↕
                            @endif
                        </span>
                    </a>
                </th>
                <th class="border p-2">
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center">
                        Время
                        <span class="ml-1">
                            @if (request('sort') === 'created_at' && request('direction') === 'asc')
                                ▼
                            @elseif (request('sort') === 'created_at' && request('direction') === 'desc')
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
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'user.role_name', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center">
                        Профессия
                        <span class="ml-1">
                            @if (request('sort') === 'user.role_name' && request('direction') === 'asc')
                                ▼
                            @elseif (request('sort') === 'user.role_name' && request('direction') === 'desc')
                                ▲
                            @else
                                ↕
                            @endif
                        </span>
                    </a>
                </th>
                <th class="border p-2">
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'user.full_name', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center">
                        Сообщение создал
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
                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'equipment.section.name', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc']) }}" class="flex items-center">
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
                        Оборудование
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
                <th class="border p-2">Обработка (Время начала, Время завершения, Длительность)</th>
                <th class="border p-2">Ожидание наладки (Время начала, Время завершения, Длительность)</th>
                <th class="border p-2">Наладка (Время начала, Время завершения, Длительность)</th>
                <th class="border p-2">Простой (Причина простоя, Время начала, Время завершения, Длительность)</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($operations as $operation)
                <tr class="border-t hover:bg-gray-100">
                    <td class="border p-2">{{ $operation->id }}</td>
                    <td class="border p-2">{{ $operation->shift->date ?? '-' }}</td>
                    <td class="border p-2">{{ $operation->created_at ? $operation->created_at->format('H:i') : '-' }}</td>
                    <td class="border p-2">{{ $operation->shift->shift_number ?? '-' }}</td>
                    <td class="border p-2">{{ $operation->user->role_name ?? '-' }}</td>
                    <td class="border p-2">{{ $operation->user->full_name ?? '-' }}</td>
                    <td class="border p-2">{{ $operation->equipment->section->name ?? '-' }}</td>
                    <td class="border p-2">{{ $operation->equipment->machine_number ?? '-' }}</td>
                    <td class="border p-2">
                        @if ($operation->start_time && $operation->end_time && $operation->duration)
                            {{ \Carbon\Carbon::parse($operation->start_time)->format('H:i') }} -
                            {{ \Carbon\Carbon::parse($operation->end_time)->format('H:i') }} -
                            {{ $operation->duration }} мин
                        @else
                            -
                        @endif
                    </td>
                    <td class="border p-2">
                        @if ($operation->adjustment_waiting && $operation->adjustment_waiting->start_time && $operation->adjustment_waiting->end_time && $operation->adjustment_waiting->duration)
                            {{ \Carbon\Carbon::parse($operation->adjustment_waiting->start_time)->format('H:i') }} -
                            {{ \Carbon\Carbon::parse($operation->adjustment_waiting->end_time)->format('H:i') }} -
                            {{ $operation->adjustment_waiting->duration }} мин
                        @else
                            -
                        @endif
                    </td>
                    <td class="border p-2">
                        @if ($operation->adjustment && $operation->adjustment->start_time && $operation->adjustment->end_time && $operation->adjustment->duration)
                            {{ \Carbon\Carbon::parse($operation->adjustment->start_time)->format('H:i') }} -
                            {{ \Carbon\Carbon::parse($operation->adjustment->end_time)->format('H:i') }} -
                            {{ $operation->adjustment->duration }} мин
                        @else
                            -
                        @endif
                    </td>
                    <td class="border p-2">
                        @if ($operation->downtime && $operation->downtime->start_time && $operation->downtime->end_time && $operation->downtime->duration)
                            {{ $operation->downtime->reason ?? '-' }}:
                            {{ \Carbon\Carbon::parse($operation->downtime->start_time)->format('H:i') }} -
                            {{ \Carbon\Carbon::parse($operation->downtime->end_time)->format('H:i') }} -
                            {{ $operation->downtime->duration }} мин
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <!-- Пагинация -->
        <div class="mt-4 flex justify-center">
            {{ $operations->appends(request()->query())->links('vendor.pagination.tailwind') }}
        </div>

        <!-- Gantt-диаграммы по дням -->
        <div class="mt-6">
            <h2 class="text-xl font-bold mb-4">График работы оборудования</h2>
            @php
                $operationsByDate = $operations->groupBy(function ($operation) {
                    return $operation->shift->date ?? 'Unknown';
                })->filter(function ($group, $date) {
                    return $date !== 'Unknown';
                });

                foreach ($operationsByDate as $date => $dateOperations) {
                    $equipmentGroups = $dateOperations->groupBy('equipment_id')->map(function ($group) {
                        return $group->first()->equipment;
                    })->sortBy('machine_number');

                    $labels = $equipmentGroups->map(function ($equipment) {
                        return $equipment->machine_number . ' (' . $equipment->machine_type . ')';
                    })->values()->toArray();

                    $datasets = [];
                    $index = 0;
                    foreach ($equipmentGroups as $equipment) {
                        $equipmentOperations = $dateOperations->where('equipment_id', $equipment->id);

                        // Обработка (Processing) - зеленый
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
                                        'backgroundColor' => 'rgba(34, 197, 94, 0.5)',
                                        'borderColor' => 'rgba(34, 197, 94, 1)',
                                        'borderWidth' => 1,
                                        'yAxisID' => 'y',
                                        'index' => $index
                                    ];
                                }
                            }
                        }

                        // Наладка (Adjustments) - голубой
                        $adjustments = $equipment->adjustments->filter(function ($adjustment) use ($date) {
                            try {
                                $startDateTime = new DateTime($adjustment->start_time);
                                $startOfDay = new DateTime($date . ' 08:00:00');
                                $endOfDay = new DateTime($date . ' 22:00:00');
                                return $startDateTime >= $startOfDay && $startDateTime <= $endOfDay;
                            } catch (\Exception $e) {
                                \Log::error("Error processing adjustment for equipment {$equipment->id}: {$e->getMessage()}");
                                return false;
                            }
                        });
                        echo "<div>Adjustments for equipment {$equipment->id} on {$date}: {$adjustments->count()}</div>";
                        foreach ($adjustments as $adjustment) {
                            try {
                                $startDateTime = new DateTime($adjustment->start_time);
                                $endDateTime = new DateTime($adjustment->end_time);
                                $startOfDay = new DateTime($date . ' 08:00:00');
                                $startMinutes = max(0, ($startDateTime->getTimestamp() - $startOfDay->getTimestamp()) / 60);
                                $endMinutes = min(840, ($endDateTime->getTimestamp() - $startOfDay->getTimestamp()) / 60);
                                $datasets[] = [
                                    'label' => 'Наладка',
                                    'data' => [[$startMinutes, $endMinutes]],
                                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                                    'borderColor' => 'rgba(59, 130, 246, 1)',
                                    'borderWidth' => 1,
                                    'yAxisID' => 'y',
                                    'index' => $index
                                ];
                            } catch (\Exception $e) {
                                \Log::error("Error rendering adjustment for equipment {$equipment->id}: {$e->getMessage()}");
                            }
                        }

                        // Простои (Downtimes) - желтый
                        echo "<div>Total downtimes loaded for equipment {$equipment->id}: " . $equipment->downtimes->count() . "</div>";
                        $downtimes = $equipment->downtimes->filter(function ($downtime) use ($date) {
                            try {
                                $startDateTime = new DateTime($downtime->start_time);
                                $startOfDay = new DateTime($date . ' 08:00:00');
                                $endOfDay = new DateTime($date . ' 22:00:00');
                                echo "<div>Downtime check: Start: {$downtime->start_time}, Date: {$date}, Result: " . ($startDateTime >= $startOfDay && $startDateTime <= $endOfDay ? 'true' : 'false') . "</div>";
                                return $startDateTime >= $startOfDay && $startDateTime <= $endOfDay;
                            } catch (\Exception $e) {
                                \Log::error("Error processing downtime for equipment {$equipment->id}: {$e->getMessage()}");
                                return false;
                            }
                        });
                        echo "<div>Downtimes for equipment {$equipment->id} on {$date}: {$downtimes->count()}</div>";
                        foreach ($downtimes as $downtime) {
                            try {
                                $startDateTime = new DateTime($downtime->start_time);
                                $endDateTime = new DateTime($downtime->end_time);
                                $startOfDay = new DateTime($date . ' 08:00:00');
                                $startMinutes = max(0, ($startDateTime->getTimestamp() - $startOfDay->getTimestamp()) / 60);
                                $endMinutes = min(840, ($endDateTime->getTimestamp() - $startOfDay->getTimestamp()) / 60);
                                $datasets[] = [
                                    'label' => 'Простой',
                                    'data' => [[$startMinutes, $endMinutes]],
                                    'backgroundColor' => 'rgba(234, 179, 8, 0.5)',
                                    'borderColor' => 'rgba(234, 179, 8, 1)',
                                    'borderWidth' => 1,
                                    'yAxisID' => 'y',
                                    'index' => $index
                                ];
                            } catch (\Exception $e) {
                                \Log::error("Error rendering downtime for equipment {$equipment->id}: {$e->getMessage()}");
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
                                    max: 840,
                                    ticks: {
                                        stepSize: 60,
                                        callback: function(value) {
                                            const hours = Math.floor(value / 60) + 8;
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
                            barThickness: 40
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
    <!-- Скрипты уже добавлены inline для каждого графика -->
@endsection
