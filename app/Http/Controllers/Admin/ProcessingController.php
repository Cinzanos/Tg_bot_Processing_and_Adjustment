<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Processing;
use App\Models\User;
use App\Models\Equipment;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProcessingController extends Controller
{
    public function index()
    {
        $processings = Processing::with(['user', 'equipment', 'shift'])->paginate(10);
        return view('admin.processings.index', compact('processings'));
    }

    public function create()
    {
        $users = User::whereIn('role', ['operator'])->get();
        $equipment = Equipment::all();
        $shifts = Shift::all();
        return view('admin.processings.create', compact('users', 'equipment', 'shifts'));
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
        $users = User::whereIn('role', ['operator'])->get();
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
