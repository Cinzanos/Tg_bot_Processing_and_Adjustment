<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ShiftController extends Controller
{
    public function index()
    {
        $shifts = Shift::with('section')->paginate(10);
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
