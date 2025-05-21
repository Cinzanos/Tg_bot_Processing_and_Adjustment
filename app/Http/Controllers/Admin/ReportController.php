<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Processing;
use App\Models\Adjustment;
use App\Models\AdjustmentWaiting;
use App\Models\Downtime;
use App\Models\Equipment;
use App\Models\Section;
use App\Models\Shift;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OperationsExport;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Processing::with([
            'user',
            'equipment.section',
            'equipment.adjustments',
            'equipment.adjustmentWaitings',
            'equipment.downtimes',
            'shift'
        ]);

        // Фильтрация
        if ($request->filled('date')) {
            $query->whereHas('shift', function ($q) use ($request) {
                $q->where('date', $request->date);
            });
        }

        if ($request->filled('shift_id')) {
            $query->where('shift_id', $request->shift_id);
        }

        if ($request->filled('section_id')) {
            $query->whereHas('equipment.section', function ($q) use ($request) {
                $q->where('id', $request->section_id);
            });
        }

        // Сортировка
        $sort = $request->get('sort', 'id');
        $direction = $request->get('direction', 'asc');

        switch ($sort) {
            case 'shift.date':
                $query->join('shifts', 'processings.shift_id', '=', 'shifts.id')
                    ->orderBy('shifts.date', $direction)
                    ->select('processings.*');
                break;
            case 'shift.shift_number':
                $query->join('shifts', 'processings.shift_id', '=', 'shifts.id')
                    ->orderBy('shifts.shift_number', $direction)
                    ->select('processings.*');
                break;
            case 'user.role_name':
                $query->join('users', 'processings.user_id', '=', 'users.id')
                    ->join('roles', 'users.role_id', '=', 'roles.id')
                    ->orderBy('roles.name', $direction)
                    ->select('processings.*');
                break;
            case 'user.full_name':
                $query->join('users', 'processings.user_id', '=', 'users.id')
                    ->orderBy('users.full_name', $direction)
                    ->select('processings.*');
                break;
            case 'equipment.section.name':
                $query->join('equipment', 'processings.equipment_id', '=', 'equipment.id')
                    ->join('sections', 'equipment.section_id', '=', 'sections.id')
                    ->orderBy('sections.name', $direction)
                    ->select('processings.*');
                break;
            case 'equipment.machine_number':
                $query->join('equipment', 'processings.equipment_id', '=', 'equipment.id')
                    ->orderBy('equipment.machine_number', $direction)
                    ->select('processings.*');
                break;
            default:
                $query->orderBy($sort, $direction);
                break;
        }

        $operations = $query->paginate(10);

        // Подгрузка фильтров
        $date = $request->date ?? date('Y-m-d');
        $shifts = Shift::where('date', $date)->get();
        $sections = collect(); // Изначально пустой массив
        if ($request->filled('shift_id')) {
            $sections = Section::whereHas('equipment.processings', function ($q) use ($request, $date) {
                $q->where('shift_id', $request->shift_id);
                if ($date) {
                    $q->whereHas('shift', function ($subQ) use ($date) {
                        $subQ->where('date', $date);
                    });
                }
            })->get();
        }

        // Добавление данных для других типов операций
        $allOperations = collect();
        if ($request->filled('section_id')) {
            $sectionId = $request->section_id;
            $shiftId = $request->filled('shift_id') ? $request->shift_id : null;
            $date = $request->filled('date') ? $request->date : null;

            // Обработка (Processing)
            $processings = Processing::with(['user', 'equipment.section', 'shift'])
                ->whereHas('equipment.section', function ($q) use ($sectionId) {
                    $q->where('id', $sectionId);
                })
                ->when($shiftId, function ($q) use ($shiftId) {
                    return $q->where('shift_id', $shiftId);
                })
                ->when($date, function ($q) use ($date) {
                    return $q->whereHas('shift', function ($q) use ($date) {
                        $q->where('date', $date);
                    });
                })
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'type' => 'processing',
                        'shift' => $item->shift,
                        'created_at' => $item->created_at,
                        'user' => $item->user,
                        'equipment' => $item->equipment,
                        'start_time' => $item->start_time,
                        'end_time' => $item->end_time,
                        'duration' => $item->duration,
                        'adjustment_waiting' => null,
                        'adjustment' => null,
                        'downtime' => null,
                    ];
                });

            // Ожидание наладки (AdjustmentWaiting)
            $adjustmentWaitings = AdjustmentWaiting::with(['user', 'equipment.section', 'shift'])
                ->whereHas('equipment.section', function ($q) use ($sectionId) {
                    $q->where('id', $sectionId);
                })
                ->when($shiftId, function ($q) use ($shiftId) {
                    return $q->where('shift_id', $shiftId);
                })
                ->when($date, function ($q) use ($date) {
                    return $q->whereHas('shift', function ($q) use ($date) {
                        $q->where('date', $date);
                    });
                })
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'type' => 'adjustment_waiting',
                        'shift' => $item->shift,
                        'created_at' => $item->created_at,
                        'user' => $item->user,
                        'equipment' => $item->equipment,
                        'start_time' => $item->start_time,
                        'end_time' => $item->end_time,
                        'duration' => $item->duration,
                        'adjustment_waiting' => $item,
                        'adjustment' => null,
                        'downtime' => null,
                    ];
                });

            // Наладка (Adjustment)
            $adjustments = Adjustment::with(['user', 'equipment.section', 'shift'])
                ->whereHas('equipment.section', function ($q) use ($sectionId) {
                    $q->where('id', $sectionId);
                })
                ->when($shiftId, function ($q) use ($shiftId) {
                    return $q->where('shift_id', $shiftId);
                })
                ->when($date, function ($q) use ($date) {
                    return $q->whereHas('shift', function ($q) use ($date) {
                        $q->where('date', $date);
                    });
                })
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'type' => 'adjustment',
                        'shift' => $item->shift,
                        'created_at' => $item->created_at,
                        'user' => $item->user,
                        'equipment' => $item->equipment,
                        'start_time' => $item->start_time,
                        'end_time' => $item->end_time,
                        'duration' => $item->duration,
                        'adjustment_waiting' => null,
                        'adjustment' => $item,
                        'downtime' => null,
                    ];
                });

            // Простой (Downtime)
            $downtimes = Downtime::with(['user', 'equipment.section', 'shift'])
                ->whereHas('equipment.section', function ($q) use ($sectionId) {
                    $q->where('id', $sectionId);
                })
                ->when($shiftId, function ($q) use ($shiftId) {
                    return $q->where('shift_id', $shiftId);
                })
                ->when($date, function ($q) use ($date) {
                    return $q->whereHas('shift', function ($q) use ($date) {
                        $q->where('date', $date);
                    });
                })
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'type' => 'downtime',
                        'shift' => $item->shift,
                        'created_at' => $item->created_at,
                        'user' => $item->user,
                        'equipment' => $item->equipment,
                        'start_time' => $item->start_time,
                        'end_time' => $item->end_time,
                        'duration' => $item->duration,
                        'adjustment_waiting' => null,
                        'adjustment' => null,
                        'downtime' => $item,
                    ];
                });

            // Объединяем все операции
            $allOperations = $processings
                ->merge($adjustmentWaitings)
                ->merge($adjustments)
                ->merge($downtimes);

            // Сортировка
            $allOperations = $allOperations->sortBy(function ($item) use ($sort) {
                switch ($sort) {
                    case 'shift.date': return $item['shift']->date ?? '';
                    case 'shift.shift_number': return $item['shift']->shift_number ?? 0;
                    case 'user.role_name': return $item['user']->role_name ?? '';
                    case 'user.full_name': return $item['user']->full_name ?? '';
                    case 'equipment.section.name': return $item['equipment']->section->name ?? '';
                    case 'equipment.machine_number': return $item['equipment']->machine_number ?? '';
                    default: return $item['id'];
                }
            }, SORT_REGULAR, $direction === 'desc');

            // Пагинация
            $perPage = 10;
            $page = $request->get('page', 1);
            $total = $allOperations->count();
            $allOperations = $allOperations->slice(($page - 1) * $perPage, $perPage);
            $allOperations = new \Illuminate\Pagination\LengthAwarePaginator(
                $allOperations,
                $total,
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        }

        return view('admin.reports.index', compact('operations', 'allOperations', 'shifts', 'sections'));
    }

    public function getShifts(Request $request)
    {
        $date = $request->date ?? date('Y-m-d');
        $shifts = Shift::where('date', $date)->get();
        return response()->json(['shifts' => $shifts]);
    }

    public function getSections(Request $request)
    {
        $shiftId = $request->shift_id;
        $date = $request->date;

        \Log::info("getSections called with shift_id: {$shiftId}, date: {$date}");

        $sections = collect();

        if ($shiftId) {
            $shift = Shift::find($shiftId);
            if ($shift && $shift->section) {
                $sections = collect([$shift->section]); // Возвращаем только участок, связанный со сменой
            }
        } elseif ($date) {
            // Если указана только дата, возвращаем участки, связанные со сменами на эту дату
            $sections = Section::whereHas('shifts', function ($q) use ($date) {
                $q->where('date', $date);
            })->get();
        }

        \Log::info("Found sections: " . $sections->toJson());

        return response()->json(['sections' => $sections]);
    }

    public function getEquipment(Request $request)
    {
        $sectionId = $request->section_id;
        $shiftId = $request->shift_id;

        $equipment = Equipment::where('section_id', $sectionId)
            ->whereHas('processings', function ($q) use ($shiftId) {
                if ($shiftId) {
                    $q->where('shift_id', $shiftId);
                }
            })
            ->get();

        return response()->json(['equipment' => $equipment]);
    }

    public function export(Request $request)
    {
        return Excel::download(new OperationsExport($request->all()), 'operations_report.xlsx');
    }
}
