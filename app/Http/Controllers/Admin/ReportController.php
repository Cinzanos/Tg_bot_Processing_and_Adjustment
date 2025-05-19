<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Processing;
use App\Models\Section;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OperationsExport;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Processing::with([
            'user',
            'equipment.section',
            'equipment.adjustments' => fn($q) => $q->latest()->first(),
            'equipment.adjustmentWaitings' => fn($q) => $q->latest()->first(),
            'equipment.downtimes' => fn($q) => $q->latest()->first(),
            'equipment.remarks' => fn($q) => $q->latest()->first(),
            'shift.section'
        ]);

        // Фильтры
        if ($request->has('section_id') && $request->section_id) {
            $query->whereHas('equipment', function ($q) use ($request) {
                $q->where('section_id', $request->section_id);
            });
        }
        if ($request->has('date') && $request->date) {
            $query->whereHas('shift', function ($q) use ($request) {
                $q->where('date', $request->date);
            });
        }
        if ($request->has('shift_number') && $request->shift_number) {
            $query->whereHas('shift', function ($q) use ($request) {
                $q->where('shift_number', $request->shift_number);
            });
        }

        $operations = $query->paginate(10);
        $sections = Section::all();

        return view('admin.reports.index', compact('operations', 'sections'));
    }

    public function export(Request $request)
    {
        return Excel::download(new OperationsExport($request->all()), 'operations_report.xlsx');
    }
}
