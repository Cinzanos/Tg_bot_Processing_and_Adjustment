<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Adjustment;
use App\Models\User;
use App\Models\Equipment;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdjustmentController extends Controller
{
    public function index()
    {
        $adjustments = Adjustment::with(['user', 'equipment', 'shift'])->paginate(10);
        return view('admin.adjustments.index', compact('adjustments'));
    }

    public function create()
    {
        $users = User::whereIn('role', ['adjuster'])->get();
        $equipment = Equipment::all();
        $shifts = Shift::all();
        return view('admin.adjustments.create', compact('users', 'equipment', 'shifts'));
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
        $users = User::whereIn('role', ['adjuster'])->get();
        $equipment = Equipment::all();
        $shifts = Shift::all();
        return view('admin.adjustments.edit', compact('adjustment', 'users', 'equipment', 'shifts'));
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
