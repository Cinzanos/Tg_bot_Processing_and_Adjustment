<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Adjustment;
use App\Models\User;
use App\Models\Equipment;
use App\Models\Shift;
use App\Models\Section;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdjustmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Adjustment::with(['user', 'equipment', 'shift', 'equipment.section']);

        // Фильтры
        if ($request->has('adjuster_name') && $request->adjuster_name != '') {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->adjuster_name . '%');
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
        if ($request->has('section_name') && $request->section_name != '') {
            $query->whereHas('equipment.section', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->section_name . '%');
            });
        }

        // Сортировка
        $sort = $request->get('sort', 'start_time');
        $direction = $request->get('direction', 'asc');

        if ($sort === 'user.full_name') {
            $query->join('users', 'adjustments.user_id', '=', 'users.id')
                ->orderBy('users.full_name', $direction)
                ->select('adjustments.*');
        } elseif ($sort === 'equipment.machine_number') {
            $query->join('equipment', 'adjustments.equipment_id', '=', 'equipment.id')
                ->orderBy('equipment.machine_number', $direction)
                ->select('adjustments.*');
        } elseif ($sort === 'shift.shift_number') {
            $query->join('shifts', 'adjustments.shift_id', '=', 'shifts.id')
                ->orderBy('shifts.shift_number', $direction)
                ->select('adjustments.*');
        } elseif ($sort === 'section.name') {
            $query->join('equipment', 'adjustments.equipment_id', '=', 'equipment.id')
                ->join('sections', 'equipment.section_id', '=', 'sections.id')
                ->orderBy('sections.name', $direction)
                ->select('adjustments.*');
        } else {
            $query->orderBy($sort, $direction);
        }

        $adjustments = $query->paginate(10);

        return view('admin.adjustments.index', compact('adjustments'));
    }

    public function create()
    {
        $users = User::whereHas('role', function ($q) {
            $q->where('name', 'Наладчик');
        })->get();

        $equipment = Equipment::all();
        $shifts = Shift::all();
        $sections = Section::all();

        return view('admin.adjustments.create', compact('users', 'equipment', 'shifts', 'sections'));
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
            'user_id' => 'required|exists:users,id',
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

        Adjustment::create($data);

        return redirect()->route('admin.adjustments.index')->with('success', 'Наладка создана успешно.');
    }

    public function show(Adjustment $adjustment)
    {
        return view('admin.adjustments.show', compact('adjustment'));
    }

    public function edit(Adjustment $adjustment)
    {
        $users = User::whereHas('role', function ($q) {
            $q->where('name', 'Наладчик');
        })->get();

        $equipment = Equipment::all();
        $shifts = Shift::all();
        $sections = Section::all();

        return view('admin.adjustments.edit', compact('adjustment', 'users', 'equipment', 'shifts', 'sections'));
    }

    public function update(Request $request, Adjustment $adjustment)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
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

        $adjustment->update($data);

        return redirect()->route('admin.adjustments.index')->with('success', 'Наладка обновлена успешно.');
    }

    public function destroy(Adjustment $adjustment)
    {
        $adjustment->delete();

        return redirect()->route('admin.adjustments.index')->with('success', 'Наладка удалена успешно.');
    }
}
