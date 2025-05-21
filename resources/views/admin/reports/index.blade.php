@extends('admin.layouts.app')

@section('content')
    <div class="bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Отчеты</h1>

        <!-- Форма поиска и фильтрации -->
        <form action="{{ route('admin.reports.index') }}" method="GET" class="mb-4" id="reportFilterForm">
            <div class="flex space-x-4">
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700">Дата</label>
                    <input type="date" name="date" id="date" class="mt-1 block w-full border rounded p-2" value="{{ request('date') ?? date('Y-m-d') }}">
                </div>
                <div>
                    <label for="shift_id" class="block text-sm font-medium text-gray-700">Смена</label>
                    <select name="shift_id" id="shift_id" class="mt-1 block w-full border rounded p-2" required>
                        <option value="">Выберите смену</option>
                        @foreach ($shifts as $shift)
                            <option value="{{ $shift->id }}" {{ request('shift_id') == $shift->id ? 'selected' : '' }}>
                                {{ $shift->shift_number }} ({{ $shift->date }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="section_id" class="block text-sm font-medium text-gray-700">Участок</label>
                    <select name="section_id" id="section_id" class="mt-1 block w-full border rounded p-2" required>
                        <option value="">Выберите участок</option>
                        @if ($sections->isNotEmpty())
                            @foreach ($sections as $section)
                                <option value="{{ $section->id }}" {{ request('section_id') == $section->id ? 'selected' : '' }}>{{ $section->name }}</option>
                            @endforeach
                        @endif
                    </select>
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
                            @if (request('sort') === 'shift.shift_number' && request('direction') == 'asc')
                                ▼
                            @elseif (request('sort') === 'shift.shift_number' && request('direction') == 'desc')
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
                            @if (request('sort') === 'equipment.machine_number' && request('direction') == 'asc')
                                ▼
                            @elseif (request('sort') === 'equipment.machine_number' && request('direction') == 'desc')
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
            @if($allOperations->isNotEmpty())
                @foreach ($allOperations as $operation)
                    <tr class="border-t hover:bg-gray-100">
                        <td class="border p-2">{{ $operation['id'] }}</td>
                        <td class="border p-2">{{ $operation['shift']->date ?? '-' }}</td>
                        <td class="border p-2">{{ $operation['created_at'] ? \Carbon\Carbon::parse($operation['created_at'])->format('H:i') : '-' }}</td>
                        <td class="border p-2">{{ $operation['shift']->shift_number ?? '-' }}</td>
                        <td class="border p-2">{{ $operation['user']->role_name ?? '-' }}</td>
                        <td class="border p-2">{{ $operation['user']->full_name ?? '-' }}</td>
                        <td class="border p-2">{{ $operation['equipment']->section->name ?? '-' }}</td>
                        <td class="border p-2">{{ $operation['equipment']->machine_number ?? '-' }}</td>
                        <td class="border p-2">
                            @if ($operation['type'] == 'processing' && $operation['start_time'] && $operation['end_time'] && $operation['duration'])
                                {{ \Carbon\Carbon::parse($operation['start_time'])->format('H:i') }} -
                                {{ \Carbon\Carbon::parse($operation['end_time'])->format('H:i') }} -
                                {{ $operation['duration'] }} мин
                            @else
                                -
                            @endif
                        </td>
                        <td class="border p-2">
                            @if ($operation['type'] == 'adjustment_waiting' && $operation['adjustment_waiting'] && $operation['adjustment_waiting']->start_time && $operation['adjustment_waiting']->end_time && $operation['adjustment_waiting']->duration)
                                {{ \Carbon\Carbon::parse($operation['adjustment_waiting']->start_time)->format('H:i') }} -
                                {{ \Carbon\Carbon::parse($operation['adjustment_waiting']->end_time)->format('H:i') }} -
                                {{ $operation['adjustment_waiting']->duration }} мин
                            @else
                                -
                            @endif
                        </td>
                        <td class="border p-2">
                            @if ($operation['type'] == 'adjustment' && $operation['adjustment'] && $operation['adjustment']->start_time && $operation['adjustment']->end_time && $operation['adjustment']->duration)
                                {{ \Carbon\Carbon::parse($operation['adjustment']->start_time)->format('H:i') }} -
                                {{ \Carbon\Carbon::parse($operation['adjustment']->end_time)->format('H:i') }} -
                                {{ $operation['adjustment']->duration }} мин
                            @else
                                -
                            @endif
                        </td>
                        <td class="border p-2">
                            @if ($operation['type'] == 'downtime' && $operation['downtime'] && $operation['downtime']->start_time && $operation['downtime']->end_time && $operation['downtime']->duration)
                                {{ $operation['downtime']->reason ?? '-' }}:
                                {{ \Carbon\Carbon::parse($operation['downtime']->start_time)->format('H:i') }} -
                                {{ \Carbon\Carbon::parse($operation['downtime']->end_time)->format('H:i') }} -
                                {{ $operation['downtime']->duration }} мин
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="12" class="border p-2 text-center text-gray-500">Выберите участок и нажмите "Фильтровать" для отображения данных.</td>
                </tr>
            @endif
            </tbody>
        </table>

        <!-- Пагинация -->
        @if($allOperations->isNotEmpty())
            <div class="mt-4 flex justify-center">
                {{ $allOperations->appends(request()->query())->links('vendor.pagination.tailwind') }}
            </div>
        @endif

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
                    $selectedSectionId = request('section_id');
                    if ($selectedSectionId) {
                        $allEquipments = \App\Models\Equipment::where('section_id', $selectedSectionId)->get();
                    } else {
                        $allEquipments = collect();
                    }

                    $labels = $allEquipments->map(function ($equipment) {
                        return $equipment->machine_number;
                    })->values()->toArray();

                    $datasets = [];
                    foreach ($allEquipments as $equipment) {
                        $equipmentId = $equipment->id;
                        $equipmentOperations = $dateOperations->where('equipment_id', $equipmentId);

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
                                        'label' => "Обработка ({$equipment->machine_number}, {$startMinutes}-{$endMinutes})",
                                        'data' => [
                                            [
                                                'x' => [$startMinutes, $endMinutes],
                                                'y' => $equipment->machine_number
                                            ]
                                        ],
                                        'backgroundColor' => 'rgba(34, 197, 94, 0.5)',
                                        'borderColor' => 'rgba(34, 197, 94, 1)',
                                        'borderWidth' => 2
                                    ];
                                }
                            }
                        }

                        $adjustments = $equipment->adjustments->filter(function ($adjustment) use ($date) {
                            try {
                                $startDateTime = new DateTime($adjustment->start_time);
                                $startOfDay = new DateTime($date . ' 08:00:00');
                                $endOfDay = new DateTime($date . ' 22:00:00');
                                return $startDateTime >= $startOfDay && $startDateTime <= $endOfDay;
                            } catch (\Exception $e) {
                                return false;
                            }
                        });
                        foreach ($adjustments as $adjustment) {
                            try {
                                $startDateTime = new DateTime($adjustment->start_time);
                                $endDateTime = new DateTime($adjustment->end_time);
                                $startOfDay = new DateTime($date . ' 08:00:00');
                                $startMinutes = max(0, ($startDateTime->getTimestamp() - $startOfDay->getTimestamp()) / 60);
                                $endMinutes = min(840, ($endDateTime->getTimestamp() - $startOfDay->getTimestamp()) / 60);
                                $datasets[] = [
                                    'label' => "Наладка ({$equipment->machine_number}, {$startMinutes}-{$endMinutes})",
                                    'data' => [
                                        [
                                            'x' => [$startMinutes, $endMinutes],
                                            'y' => $equipment->machine_number
                                        ]
                                    ],
                                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                                    'borderColor' => 'rgba(59, 130, 246, 1)',
                                    'borderWidth' => 2
                                ];
                            } catch (\Exception $e) {
                            }
                        }

                        $downtimes = $equipment->downtimes->filter(function ($downtime) use ($date) {
                            try {
                                $startDateTime = new DateTime($downtime->start_time);
                                $startOfDay = new DateTime($date . ' 08:00:00');
                                $endOfDay = new DateTime($date . ' 22:00:00');
                                return $startDateTime >= $startOfDay && $startDateTime <= $endOfDay;
                            } catch (\Exception $e) {
                                return false;
                            }
                        });
                        foreach ($downtimes as $downtime) {
                            try {
                                $startDateTime = new DateTime($downtime->start_time);
                                $endDateTime = new DateTime($downtime->end_time);
                                $startOfDay = new DateTime($date . ' 08:00:00');
                                $startMinutes = max(0, ($startDateTime->getTimestamp() - $startOfDay->getTimestamp()) / 60);
                                $endMinutes = min(840, ($endDateTime->getTimestamp() - $startOfDay->getTimestamp()) / 60);
                                $datasets[] = [
                                    'label' => "Простой ({$equipment->machine_number}, {$startMinutes}-{$endMinutes})",
                                    'data' => [
                                        [
                                            'x' => [$startMinutes, $endMinutes],
                                            'y' => $equipment->machine_number
                                        ]
                                    ],
                                    'backgroundColor' => 'rgba(234, 179, 8, 0.5)',
                                    'borderColor' => 'rgba(234, 179, 8, 1)',
                                    'borderWidth' => 2
                                ];
                            } catch (\Exception $e) {
                            }
                        }
                    }
            @endphp
            <div class="mb-8">
                <h3 class="text-lg font-semibold mb-2">Дата: {{ \Carbon\Carbon::parse($date)->format('d.m.Y') }}</h3>
                <canvas id="ganttChart_{{ \Carbon\Carbon::parse($date)->format('Ymd') }}" width="800" height="{{ max(200, count($labels) * 50) }}" style="display: block; width: 800px; height: {{ max(200, count($labels) * 50) }}px; max-height: 600px; overflow-y: auto;"></canvas>
                <script>
                    const ctx_{{ \Carbon\Carbon::parse($date)->format('Ymd') }} = document.getElementById('ganttChart_{{ \Carbon\Carbon::parse($date)->format('Ymd') }}').getContext('2d');
                    const chartLabels = @json($labels);
                    const chartData = @json($datasets);
                    const chart_{{ \Carbon\Carbon::parse($date)->format('Ymd') }} = new Chart(ctx_{{ \Carbon\Carbon::parse($date)->format('Ymd') }}, {
                        type: 'bar',
                        data: {
                            labels: chartLabels,
                            datasets: chartData
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
                                    },
                                    type: 'category',
                                    labels: chartLabels
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
                                            if (!data) return `${dataset.label}: Нет данных`;
                                            const startMinutes = data.x[0];
                                            const endMinutes = data.x[1];
                                            const startHours = Math.floor(startMinutes / 60) + 8;
                                            const startMins = startMinutes % 60;
                                            const endHours = Math.floor(endMinutes / 60) + 8;
                                            const endMins = endMinutes % 60;
                                            return `${dataset.label}: ${startHours.toString().padStart(2, '0')}:${startMins.toString().padStart(2, '0')} - ${endHours.toString().padStart(2, '0')}:${endMins.toString().padStart(2, '0')}`;
                                        }
                                    }
                                }
                            },
                            barThickness: 15,
                            categoryPercentage: 0.6,
                            barPercentage: 0.8,
                            stacked: false
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
        document.getElementById('date').addEventListener('change', updateShiftsAndSections);
        document.getElementById('shift_id').addEventListener('change', updateSections);

        function updateShiftsAndSections() {
            const date = document.getElementById('date').value;
            const shiftSelect = document.getElementById('shift_id');
            const sectionSelect = document.getElementById('section_id');

            console.log('updateShiftsAndSections called with date:', date);

            if (date) {
                fetch(`/admin/reports/shifts?date=${date}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                    .then(response => {
                        console.log('Shifts response status:', response.status);
                        if (!response.ok) throw new Error(`Failed to fetch shifts: ${response.status}`);
                        return response.json();
                    })
                    .then(data => {
                        console.log('Shifts data:', data);
                        shiftSelect.innerHTML = '<option value="">Выберите смену</option>';
                        if (data.shifts && data.shifts.length > 0) {
                            data.shifts.forEach(shift => {
                                const option = document.createElement('option');
                                option.value = shift.id;
                                option.text = `${shift.shift_number} (${shift.date})`;
                                if (shift.id == {{ request('shift_id') ?? 'null' }}) {
                                    option.selected = true;
                                }
                                shiftSelect.appendChild(option);
                            });
                        } else {
                            shiftSelect.innerHTML = '<option value="">Смены не найдены</option>';
                        }
                        updateSections();
                    })
                    .catch(error => {
                        console.error('Ошибка загрузки смен:', error);
                        shiftSelect.innerHTML = '<option value="">Ошибка загрузки смен</option>';
                        sectionSelect.innerHTML = '<option value="">Выберите участок</option>';
                    });
            } else {
                shiftSelect.innerHTML = '<option value="">Выберите смену</option>';
                sectionSelect.innerHTML = '<option value="">Выберите участок</option>';
            }
        }

        function updateSections() {
            const shiftId = document.getElementById('shift_id').value;
            const date = document.getElementById('date').value;
            const sectionSelect = document.getElementById('section_id');

            console.log('updateSections called with shiftId:', shiftId, 'date:', date);

            if (shiftId || date) {
                fetch(`/admin/reports/sections?shift_id=${shiftId}&date=${date}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                    .then(response => {
                        console.log('Sections response status:', response.status);
                        if (!response.ok) throw new Error(`Failed to fetch sections: ${response.status}`);
                        return response.json();
                    })
                    .then(data => {
                        console.log('Sections data:', data);
                        sectionSelect.innerHTML = '<option value="">Выберите участок</option>';
                        if (data.sections && data.sections.length > 0) {
                            data.sections.forEach(section => {
                                const option = document.createElement('option');
                                option.value = section.id;
                                option.text = section.name;
                                if (section.id == {{ request('section_id') ?? 'null' }}) {
                                    option.selected = true;
                                }
                                sectionSelect.appendChild(option);
                            });
                        } else {
                            sectionSelect.innerHTML = '<option value="">Участки не найдены</option>';
                        }
                    })
                    .catch(error => {
                        console.error('Ошибка загрузки участков:', error);
                        sectionSelect.innerHTML = '<option value="">Ошибка загрузки участков</option>';
                    });
            } else {
                sectionSelect.innerHTML = '<option value="">Выберите участок</option>';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM fully loaded, calling updateShiftsAndSections');
            updateShiftsAndSections();
        });
@endsection
