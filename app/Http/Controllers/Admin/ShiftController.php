<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShiftController extends Controller
{
    public function index(Request $request)
    {
        $query = Shift::with('section');

        // Поиск
        if ($request->has('shift_number') && $request->shift_number != '') {
            $query->where('shift_number', 'like', '%' . $request->shift_number . '%');
        }
        if ($request->has('date') && $request->date != '') {
            $query->whereDate('date', $request->date);
        }
        if ($request->has('section_name') && $request->section_name != '') {
            $query->whereHas('section', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->section_name . '%');
            });
        }

        // Сортировка
        $sort = $request->get('sort', 'shift_number'); // По умолчанию сортировка по номеру смены
        $direction = $request->get('direction', 'asc'); // По умолчанию по возрастанию
        if ($sort === 'section.name') {
            $query->join('sections', 'shifts.section_id', '=', 'sections.id')
                ->orderBy('sections.name', $direction)
                ->select('shifts.*');
        } else {
            $query->orderBy($sort, $direction);
        }

        // Пагинация
        $shifts = $query->paginate(10);

        return view('admin.shifts.index', compact('shifts'));
    }

    public function create()
    {
        $sections = Section::all();
        return view('admin.shifts.create', compact('sections'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shift_number' => 'required|string|max:50',
            'date' => 'required|date',
            'section_id' => 'required|exists:sections,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Shift::create($request->all());
        return redirect()->route('admin.shifts.index')->with('success', 'Смена создана успешно.');
    }

    public function show(Shift $shift)
    {
        $shift->load('section');
        return view('admin.shifts.show', compact('shift'));
    }

    public function edit(Shift $shift)
    {
        $sections = Section::all();
        return view('admin.shifts.edit', compact('shift', 'sections'));
    }

    public function update(Request $request, Shift $shift)
    {
        $validator = Validator::make($request->all(), [
            'shift_number' => 'required|string|max:50',
            'date' => 'required|date',
            'section_id' => 'required|exists:sections,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $shift->update($request->all());
        return redirect()->route('admin.shifts.index')->with('success', 'Смена обновлена успешно.');
    }

    public function destroy(Shift $shift)
    {
        $shift->delete();
        return redirect()->route('admin.shifts.index')->with('success', 'Смена удалена успешно.');
    }
}
