<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Remark;
use App\Models\User;
use App\Models\Equipment;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RemarkController extends Controller
{
    public function index()
    {
        $remarks = Remark::with(['user', 'equipment', 'shift'])->paginate(10);
        return view('admin.remarks.index', compact('remarks'));
    }

    public function create()
    {
        $users = User::whereIn('role', ['master', 'brigadier', 'operator', 'adjuster'])->get();
        $equipment = Equipment::all();
        $shifts = Shift::all();
        $types = ['acceptance', 'handover'];
        return view('admin.remarks.create', compact('users', 'equipment', 'shifts', 'types'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'equipment_id' => 'required|exists:equipment,id',
            'shift_id' => 'required|exists:shifts,id',
            'text' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'type' => 'required|in:acceptance,handover',
        ]);

        $data = $request->all();

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('remarks', 'public');
            $data['photo'] = '/storage/' . $path;
        }

        Remark::create($data);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Remark::create($request->all());
        return redirect()->route('admin.remarks.index')->with('success', 'Замечание создано успешно.');
    }

    public function show(Remark $remark)
    {
        return view('admin.remarks.show', compact('remark'));
    }

    public function edit(Remark $remark)
    {
        $users = User::whereIn('role', ['master', 'brigadier', 'operator', 'adjuster'])->get();
        $equipment = Equipment::all();
        $shifts = Shift::all();
        $types = ['acceptance', 'handover'];
        return view('admin.remarks.edit', compact('remark', 'users', 'equipment', 'shifts', 'types'));
    }

    public function update(Request $request, Remark $remark)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'equipment_id' => 'required|exists:equipment,id',
            'shift_id' => 'required|exists:shifts,id',
            'text' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'type' => 'required|in:acceptance,handover',
        ]);

        $data = $request->all();

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('remarks', 'public');
            $data['photo'] = '/storage/' . $path;
        }

        $remark->update($data);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $remark->update($request->all());
        return redirect()->route('admin.remarks.index')->with('success', 'Замечание обновлено успешно.');
    }

    public function destroy(Remark $remark)
    {
        $remark->delete();
        return redirect()->route('admin.remarks.index')->with('success', 'Замечание удалено успешно.');
    }
}
