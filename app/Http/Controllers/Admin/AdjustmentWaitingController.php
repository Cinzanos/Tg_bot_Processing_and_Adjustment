<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdjustmentWaiting;
use App\Models\Equipment;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdjustmentWaitingController extends Controller
{
    public function index()
    {
        $adjustmentWaitings = AdjustmentWaiting::with(['equipment', 'shift'])->paginate(10);
        return view('admin.adjustment-waitings.index', compact('adjustmentWaitings'));
    }

    public function create()
    {
        $equipment = Equipment::all();
        $shifts = Shift::all();
        return view('admin.adjustment-waitings.create', compact('equipment', 'shifts'));
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
        $equipment = Equipment::all();
        $shifts = Shift::all();
        return view('admin.adjustment-waitings.edit', compact('adjustmentWaiting', 'equipment', 'shifts'));
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
