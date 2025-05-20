<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Remark;
use App\Models\User;
use App\Models\Equipment;
use App\Models\Shift;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class RemarkController extends Controller
{
    public function index(Request $request)
    {
        $query = Remark::with(['user', 'equipment', 'shift', 'equipment.section']);

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
        if ($request->has('text') && $request->text != '') {
            $query->where('text', 'like', '%' . $request->text . '%');
        }
        if ($request->has('type') && $request->type != '') {
            $query->where('type', $request->type);
        }

        // Сортировка
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'asc');
        if ($sort === 'user.full_name') {
            $query->join('users', 'remarks.user_id', '=', 'users.id')
                ->orderBy('users.full_name', $direction)
                ->select('remarks.*');
        } elseif ($sort === 'user.role_name') {
            $query->join('users', 'remarks.user_id', '=', 'users.id')
                ->join('roles', 'users.role_id', '=', 'roles.id')
                ->orderBy('roles.name', $direction)
                ->select('remarks.*');
        } elseif ($sort === 'equipment.section.name') {
            $query->join('equipment', 'remarks.equipment_id', '=', 'equipment.id')
                ->join('sections', 'equipment.section_id', '=', 'sections.id')
                ->orderBy('sections.name', $direction)
                ->select('remarks.*');
        } elseif ($sort === 'equipment.machine_number') {
            $query->join('equipment', 'remarks.equipment_id', '=', 'equipment.id')
                ->orderBy('equipment.machine_number', $direction)
                ->select('remarks.*');
        } elseif ($sort === 'shift.shift_number') {
            $query->join('shifts', 'remarks.shift_id', '=', 'shifts.id')
                ->orderBy('shifts.shift_number', $direction)
                ->select('remarks.*');
        } else {
            $query->orderBy($sort, $direction);
        }

        $remarks = $query->paginate(10);
        return view('admin.remarks.index', compact('remarks'));
    }

    public function create()
    {
        $users = User::whereHas('role', function ($query) {
            $query->where('name', 'Оператор');
        })->get();
        $shifts = Shift::all();
        $types = Remark::types();
        return view('admin.remarks.create', compact('users', 'shifts', 'types'));
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
            'text' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'type' => 'required|in:' . implode(',', array_keys(Remark::types())),
            'created_at' => 'required|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->all();

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('remarks', 'public');
            $data['photo'] = '/storage/' . $path;
        }

        Remark::create($data);

        return redirect()->route('admin.remarks.index')->with('success', 'Замечание создано успешно.');
    }

    public function show(Remark $remark)
    {
        return view('admin.remarks.show', compact('remark'));
    }

    public function edit(Remark $remark)
    {
        $users = User::whereHas('role', function ($query) {
            $query->where('name', 'Оператор');
        })->get();
        $shifts = Shift::all();
        $types = Remark::types();
        return view('admin.remarks.edit', compact('remark', 'users', 'shifts', 'types'));
    }

    public function update(Request $request, Remark $remark)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'equipment_id' => 'required|exists:equipment,id',
            'shift_id' => 'required|exists:shifts,id',
            'text' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'type' => 'required|in:' . implode(',', array_keys(Remark::types())),
            'created_at' => 'required|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->all();

        if ($request->hasFile('photo')) {
            if ($remark->photo && Storage::disk('public')->exists(str_replace('/storage/', '', $remark->photo))) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $remark->photo));
            }
            $path = $request->file('photo')->store('remarks', 'public');
            $data['photo'] = '/storage/' . $path;
        }

        $remark->update($data);

        return redirect()->route('admin.remarks.index')->with('success', 'Замечание обновлено успешно.');
    }

    public function destroy(Remark $remark)
    {
        if ($remark->photo && Storage::disk('public')->exists(str_replace('/storage/', '', $remark->photo))) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $remark->photo));
        }

        $remark->delete();
        return redirect()->route('admin.remarks.index')->with('success', 'Замечание удалено успешно.');
    }
}
