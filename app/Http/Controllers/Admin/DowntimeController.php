<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Downtime;
use App\Models\User;
use App\Models\Equipment;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DowntimeController extends Controller
{
    public function index()
    {
        $downtimes = Downtime::with(['user', 'equipment', 'shift'])->paginate(10);
        return view('admin.downtimes.index', compact('downtimes'));
    }

    public function create()
    {
        $users = User::whereIn('role', ['master', 'brigadier', 'operator', 'adjuster'])->get();
        $equipment = Equipment::all();
        $shifts = Shift::all();
        $reasons = ['reason1', 'reason2', 'reason3', 'reason4']; // Заменить на реальные причины из ТЗ
        return view('admin.downtimes.create', compact('users', 'equipment', 'shifts', 'reasons'));
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
        $users = User::whereIn('role', ['master', 'brigadier', 'operator', 'adjuster'])->get();
        $equipment = Equipment::all();
        $shifts = Shift::all();
        $reasons = ['reason1', 'reason2', 'reason3', 'reason4']; // Заменить на реальные причины
        return view('admin.downtimes.edit', compact('downtime', 'users', 'equipment', 'shifts', 'reasons'));
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
