<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Processing;
use App\Models\Adjustment;
use App\Models\AdjustmentWaiting;
use App\Models\Downtime;
use App\Models\Remark;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OperationsExport;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Processing::with(['user', 'equipment', 'shift'])
            ->join('adjustments', 'processings.equipment_id', '=', 'adjustments.equipment_id')
            ->join('adjustment_waitings', 'processings.equipment_id', '=', 'adjustment_waitings.equipment_id')
            ->join('downtimes', 'processings.equipment_id', '=', 'downtimes.equipment_id')
            ->join('remarks', 'processings.equipment_id', '=', 'remarks.equipment_id')
            ->select('processings.*', 'adjustments.*', 'adjustment_waitings.*', 'downtimes.*', 'remarks.*');

        // Фильтры
        if ($request->has('section')) {
            $query->whereHas('equipment', function ($q) use ($request) {
                $q->where('section', $request->section);
            });
        }
        if ($request->has('date')) {
            $query->whereHas('shift', function ($q) use ($request) {
                $q->where('date', $request->date);
            });
        }
        // Другие фильтры по аналогии

        $operations = $query->paginate(10);
        return view('admin.reports.index', compact('operations'));
    }

    public function export(Request $request)
    {
        return Excel::download(new OperationsExport($request->all()), 'operations_report.xlsx');
    }
}
