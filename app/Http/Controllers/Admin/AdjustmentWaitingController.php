<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdjustmentWaiting;
use App\Models\Equipment;
use App\Models\Shift;
use App\Models\Section;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdjustmentWaitingController extends Controller
{
    public function index(Request $request)
    {
        $query = AdjustmentWaiting::with(['equipment', 'shift', 'equipment.section']);

        // Поиск и фильтрация
        if ($request->has('section_name') && $request->section_name != '') {
            $query->whereHas('equipment.section', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->section_name . '%');
            });
        }
        if ($request->has('machine_number') && $request->machine_number != '') {
            $query->whereHas('equipment', function ($q) use ($request) {
                $q->where('machine_number', 'like', '%' . $request->machine_number . '%');
            });
        }
        if ($request->has('shift_number') && $request->shift_number != '') {
            $query->whereHas('shift', function ($q) use ($request) {
                $q->where('shift_number', 'like', '%' . $request->shift_number . '%');
            });
        }
        if ($request->has('start_time') && $request->start_time != '') {
            $query->where('start_time', '>=', $request->start_time);
        }

        // Сортировка
        $sort = $request->get('sort', 'start_time'); // По умолчанию сортировка по времени начала
        $direction = $request->get('direction', 'asc'); // По умолчанию по возрастанию
        if ($sort === 'equipment.machine_number') {
            $query->join('equipment', 'adjustment_waitings.equipment_id', '=', 'equipment.id')
                ->orderBy('equipment.machine_number', $direction)
                ->select('adjustment_waitings.*');
        } elseif ($sort === 'section.name') {
            $query->join('equipment', 'adjustment_waitings.equipment_id', '=', 'equipment.id')
                ->join('sections', 'equipment.section_id', '=', 'sections.id')
                ->orderBy('sections.name', $direction)
                ->select('adjustment_waitings.*');
        } else {
            $query->orderBy($sort, $direction);
        }

        // Пагинация
        $adjustmentWaitings = $query->paginate(10);

        return view('admin.adjustment-waitings.index', compact('adjustmentWaitings'));
    }

    public function create()
    {
        $shifts = Shift::all();
        return view('admin.adjustment-waitings.create', compact('shifts'));
    }

    public function getSectionByShift(Shift $shift)
    {
        $section = $shift->section;
        return response()->json($section);
    }

    public function getEquipmentBySection(Section $section)
    {
        $equipment = $section->equipment;
        return response()->json($equipment);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'equipment_id' => 'required|exists:equipment,id',
            'shift_id' => 'required|exists:shifts,id',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after:start_time',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->all();
        if ($request->filled('end_time')) {
            $data['duration'] = Carbon::parse($request->start_time)->diffInMinutes($request->end_time);
        }

        AdjustmentWaiting::create($data);
        return redirect()->route('admin.adjustment-waitings.index')->with('success', 'Ожидание наладки создано успешно.');
    }

    public function show(AdjustmentWaiting $adjustmentWaiting)
    {
        return view('admin.adjustment-waitings.show', compact('adjustmentWaiting'));
    }

    public function edit(AdjustmentWaiting $adjustmentWaiting)
    {
        $shifts = Shift::all();
        return view('admin.adjustment-waitings.edit', compact('adjustmentWaiting', 'shifts'));
    }

    public function update(Request $request, AdjustmentWaiting $adjustmentWaiting)
    {
        $validator = Validator::make($request->all(), [
            'equipment_id' => 'required|exists:equipment,id',
            'shift_id' => 'required|exists:shifts,id',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after:start_time',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->all();
        if ($request->filled('end_time')) {
            $data['duration'] = Carbon::parse($request->start_time)->diffInMinutes($request->end_time);
        } else {
            $data['duration'] = null;
        }

        $adjustmentWaiting->update($data);
        return redirect()->route('admin.adjustment-waitings.index')->with('success', 'Ожидание наладки обновлено успешно.');
    }

    public function destroy(AdjustmentWaiting $adjustmentWaiting)
    {
        $adjustmentWaiting->delete();
        return redirect()->route('admin.adjustment-waitings.index')->with('success', 'Ожидание наладки удалено успешно.');
    }
}
