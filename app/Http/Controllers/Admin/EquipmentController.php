<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EquipmentController extends Controller
{
    public function index()
    {
        $equipment = Equipment::with('section')->paginate(10);
        return view('admin.equipment.index', compact('equipment'));
    }

    public function create()
    {
        $sections = Section::all();
        return view('admin.equipment.create', compact('sections'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'section_id' => 'required|exists:sections,id',
            'machine_number' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Equipment::create($request->all());
        return redirect()->route('admin.equipment.index')->with('success', 'Оборудование создано успешно.');
    }

    public function show(Equipment $equipment)
    {
        $equipment->load('section');
        return view('admin.equipment.show', compact('equipment'));
    }

    public function edit(Equipment $equipment)
    {
        $sections = Section::all();
        return view('admin.equipment.edit', compact('equipment', 'sections'));
    }

    public function update(Request $request, Equipment $equipment)
    {
        $validator = Validator::make($request->all(), [
            'section_id' => 'required|exists:sections,id',
            'machine_number' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $equipment->update($request->all());
        return redirect()->route('admin.equipment.index')->with('success', 'Оборудование обновлено успешно.');
    }

    public function destroy(Equipment $equipment)
    {
        $equipment->delete();
        return redirect()->route('admin.equipment.index')->with('success', 'Оборудование удалено успешно.');
    }
}
