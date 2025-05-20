<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Downtime;
use App\Models\User;
use App\Models\Equipment;
use App\Models\Shift;
use App\Models\Section;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DowntimeController extends Controller
{
    public function index(Request $request)
    {
        $query = Downtime::with(['user', 'equipment', 'shift', 'equipment.section']);

        // Поиск и фильтрация
        if ($request->has('initiator_name') && $request->initiator_name != '') {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->initiator_name . '%');
            });
        }
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
        if ($sort === 'user.full_name') {
            $query->join('users', 'downtimes.user_id', '=', 'users.id')
                ->orderBy('users.full_name', $direction)
                ->select('downtimes.*');
        } elseif ($sort === 'equipment.section.name') {
            $query->join('equipment', 'downtimes.equipment_id', '=', 'equipment.id')
                ->join('sections', 'equipment.section_id', '=', 'sections.id')
                ->orderBy('sections.name', $direction)
                ->select('downtimes.*');
        } elseif ($sort === 'equipment.machine_number') {
            $query->join('equipment', 'downtimes.equipment_id', '=', 'equipment.id')
                ->orderBy('equipment.machine_number', $direction)
                ->select('downtimes.*');
        } elseif ($sort === 'shift.shift_number') {
            $query->join('shifts', 'downtimes.shift_id', '=', 'shifts.id')
                ->orderBy('shifts.shift_number', $direction)
                ->select('downtimes.*');
        } else {
            $query->orderBy($sort, $direction);
        }

        // Пагинация
        $downtimes = $query->paginate(10);

        return view('admin.downtimes.index', compact('downtimes'));
    }

    public function create()
    {
        $users = User::whereHas('role', function ($query) {
            $query->where('name', '!=', 'Администратор');
        })->get(); // Исключаем администраторов
        $shifts = Shift::all();
        $reasons = ['reason1', 'reason2', 'reason3', 'reason4']; // Заменить на реальные причины
        return view('admin.downtimes.create', compact('users', 'shifts', 'reasons'));
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
            'reason' => 'required|in:reason1,reason2,reason3,reason4', // Заменить на реальные причины
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->all();
        if ($request->filled('end_time')) {
            $data['duration'] = Carbon::parse($request->start_time)->diffInMinutes($request->end_time);
        }

        Downtime::create($data);
        return redirect()->route('admin.downtimes.index')->with('success', 'Простой создан успешно.');
    }

    public function show(Downtime $downtime)
    {
        return view('admin.downtimes.show', compact('downtime'));
    }

    public function edit(Downtime $downtime)
    {
        $users = User::whereHas('role', function ($query) {
            $query->where('name', '!=', 'Администратор');
        })->get(); // Исключаем администраторов
        $shifts = Shift::all();
        $reasons = ['reason1', 'reason2', 'reason3', 'reason4']; // Заменить на реальные причины
        return view('admin.downtimes.edit', compact('downtime', 'users', 'shifts', 'reasons'));
    }

    public function update(Request $request, Downtime $downtime)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'equipment_id' => 'required|exists:equipment,id',
            'shift_id' => 'required|exists:shifts,id',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after:start_time',
            'reason' => 'required|in:reason1,reason2,reason3,reason4', // Заменить на реальные причины
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

        $downtime->update($data);
        return redirect()->route('admin.downtimes.index')->with('success', 'Простой обновлен успешно.');
    }

    public function destroy(Downtime $downtime)
    {
        $downtime->delete();
        return redirect()->route('admin.downtimes.index')->with('success', 'Простой удален успешно.');
    }
}
