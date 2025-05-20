<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Processing;
use App\Models\Section;
use App\Models\User;
use App\Models\Equipment;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProcessingController extends Controller
{
    public function index(Request $request)
    {
        $query = Processing::with(['user', 'equipment', 'shift', 'equipment.section']);

        // Поиск и фильтрация
        if ($request->has('operator_name') && $request->operator_name != '') {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->operator_name . '%');
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
        $sort = $request->get('sort', 'start_time'); // По умолчанию сортировка по времени начала
        $direction = $request->get('direction', 'asc'); // По умолчанию по возрастанию
        if ($sort === 'user.full_name') {
            $query->join('users', 'processings.user_id', '=', 'users.id')
                ->orderBy('users.full_name', $direction)
                ->select('processings.*');
        } elseif ($sort === 'equipment.machine_number') {
            $query->join('equipment', 'processings.equipment_id', '=', 'equipment.id')
                ->orderBy('equipment.machine_number', $direction)
                ->select('processings.*');
        } elseif ($sort === 'shift.shift_number') {
            $query->join('shifts', 'processings.shift_id', '=', 'shifts.id')
                ->orderBy('shifts.shift_number', $direction)
                ->select('processings.*');
        } elseif ($sort === 'section.name') {
            $query->join('equipment', 'processings.equipment_id', '=', 'equipment.id')
                ->join('sections', 'equipment.section_id', '=', 'sections.id')
                ->orderBy('sections.name', $direction)
                ->select('processings.*');
        } else {
            $query->orderBy($sort, $direction);
        }

        // Пагинация
        $processings = $query->paginate(10);

        return view('admin.processings.index', compact('processings'));
    }

    public function create()
    {
        $users = User::whereHas('role', function ($q) {
            $q->where('name', 'Оператор');
        })->get();
        $equipment = Equipment::all();
        $shifts = Shift::all();
        $sections = Section::all();
        return view('admin.processings.create', compact('users', 'equipment', 'shifts', 'sections'));
    }

    public function getSectionByShift(Shift $shift)
    {
        $section = $shift->section; // Предполагаем, что смена относится к одному участку
        return response()->json($section);
    }

    public function getEquipmentBySection(Section $section)
    {
        $equipment = $section->equipment; // Получить все станки на этом участке
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

        Processing::create($data);
        return redirect()->route('admin.processings.index')->with('success', 'Обработка создана успешно.');
    }

    public function show(Processing $processing)
    {
        return view('admin.processings.show', compact('processing'));
    }

    public function edit(Processing $processing)
    {
        $users = User::whereHas('role', function ($q) {
            $q->where('name', 'Оператор');
        })->get();
        $equipment = Equipment::all();
        $shifts = Shift::all();
        return view('admin.processings.edit', compact('processing', 'users', 'equipment', 'shifts'));
    }

    public function update(Request $request, Processing $processing)
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

        $processing->update($data);
        return redirect()->route('admin.processings.index')->with('success', 'Обработка обновлена успешно.');
    }

    public function destroy(Processing $processing)
    {
        $processing->delete();
        return redirect()->route('admin.processings.index')->with('success', 'Обработка удалена успешно.');
    }
}
