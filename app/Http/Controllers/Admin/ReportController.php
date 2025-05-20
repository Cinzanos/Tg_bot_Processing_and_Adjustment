<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Processing;
use App\Models\Adjustment;
use App\Models\Downtime;
use App\Models\Equipment;
use App\Models\Section;
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
            'shift.section'
        ]);

        // Собираем equipment_id для всех событий
        $date = $request->date ?? '2025-05-20'; // По умолчанию текущая дата, если не указана
        $startOfDay = $date . ' 08:00:00';
        $endOfDay = $date . ' 22:00:00';

        // equipment_id из Processing
        $processingEquipmentIdsQuery = Processing::select('equipment_id')
            ->distinct()
            ->whereHas('shift', function ($q) use ($date) {
                $q->where('date', $date);
            });

        // equipment_id из Adjustments
        $adjustmentEquipmentIdsQuery = Adjustment::select('equipment_id')
            ->distinct()
            ->whereBetween('start_time', [$startOfDay, $endOfDay]);

        // equipment_id из Downtimes
        $downtimeEquipmentIdsQuery = Downtime::select('equipment_id')
            ->distinct()
            ->whereBetween('start_time', [$startOfDay, $endOfDay]);

        // Объединяем все equipment_id
        $equipmentIds = array_unique(
            array_merge(
                $processingEquipmentIdsQuery->pluck('equipment_id')->toArray(),
                $adjustmentEquipmentIdsQuery->pluck('equipment_id')->toArray(),
                $downtimeEquipmentIdsQuery->pluck('equipment_id')->toArray()
            )
        );

        // Загружаем все equipment с предварительной загрузкой
        $equipmentList = Equipment::with([
            'adjustments' => function ($query) use ($startOfDay, $endOfDay) {
                $query->whereBetween('start_time', [$startOfDay, $endOfDay]);
            },
            'downtimes' => function ($query) use ($startOfDay, $endOfDay) {
                $query->whereBetween('start_time', [$startOfDay, $endOfDay]);
            }
        ])->whereIn('id', $equipmentIds)->get()->sortBy('machine_number');

        // Применяем фильтры к Processing
        if ($request->has('section_id') && $request->section_id) {
            $query->whereHas('equipment.section', function ($q) use ($request) {
                $q->where('id', $request->section_id);
            });
        }
        if ($request->has('date') && $request->date) {
            $query->whereHas('shift', function ($q) use ($request) {
                $q->where('date', $request->date);
            });
        }
        if ($request->has('shift_number') && $request->shift_number) {
            $query->whereHas('shift', function ($q) use ($request) {
                $q->where('shift_number', 'like', '%' . $request->shift_number . '%');
            });
        }
        if ($request->has('machine_number') && $request->machine_number) {
            $query->whereHas('equipment', function ($q) use ($request) {
                $q->where('machine_number', 'like', '%' . $request->machine_number . '%');
            });
        }

        // Сортировка
        $sort = $request->get('sort', 'id');
        $direction = $request->get('direction', 'asc');
        if ($sort === 'shift.date') {
            $query->join('shifts', 'processings.shift_id', '=', 'shifts.id')
                ->orderBy('shifts.date', $direction)
                ->select('processings.*');
        } elseif ($sort === 'shift.shift_number') {
            $query->join('shifts', 'processings.shift_id', '=', 'shifts.id')
                ->orderBy('shifts.shift_number', $direction)
                ->select('processings.*');
        } elseif ($sort === 'user.role_name') {
            $query->join('users', 'processings.user_id', '=', 'users.id')
                ->join('roles', 'users.role_id', '=', 'roles.id')
                ->orderBy('roles.name', $direction)
                ->select('processings.*');
        } elseif ($sort === 'user.full_name') {
            $query->join('users', 'processings.user_id', '=', 'users.id')
                ->orderBy('users.full_name', $direction)
                ->select('processings.*');
        } elseif ($sort === 'equipment.section.name') {
            $query->join('equipment', 'processings.equipment_id', '=', 'equipment.id')
                ->join('sections', 'equipment.section_id', '=', 'sections.id')
                ->orderBy('sections.name', $direction)
                ->select('processings.*');
        } elseif ($sort === 'equipment.machine_number') {
            $query->join('equipment', 'processings.equipment_id', '=', 'equipment.id')
                ->orderBy('equipment.machine_number', $direction)
                ->select('processings.*');
        } else {
            $query->orderBy($sort, $direction);
        }

        $operations = $query->paginate(10);
        $sections = Section::all();

        return view('admin.reports.index', compact('operations', 'sections', 'equipmentList'));
    }

    public function export(Request $request)
    {
        return Excel::download(new OperationsExport($request->all()), 'operations_report.xlsx');
    }
}
