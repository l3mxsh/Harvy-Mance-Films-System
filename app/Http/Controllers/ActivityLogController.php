<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::query();

        $search = trim((string) $request->input('search', ''));

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('actor_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('action')) {
            $query->where('action', $request->input('action'));
        }

        if ($request->filled('user_type')) {
            $query->where('user_type', $request->input('user_type'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        $logs = $query->latest()->paginate(20)->withQueryString();

        if ($request->ajax()) {
            $rowsHtml = view('dashboard.partials.activity-log-rows', compact('logs'))->render();
            $mobileRowsHtml = view('dashboard.partials.activity-log-mobile-rows', compact('logs'))->render();
            $paginationHtml = $logs->hasPages() ? $logs->links('vendor.pagination.bootstrap-5')->render() : '';

            return response()->json([
                'rows' => $rowsHtml,
                'mobileRows' => $mobileRowsHtml,
                'pagination' => $paginationHtml,
                'total' => $logs->total(),
            ]);
        }

        $distinctActions = ActivityLog::select('action')->distinct()->orderBy('action')->pluck('action');

        $summary = [
            'total' => ActivityLog::count(),
            'today' => ActivityLog::whereDate('created_at', today())->count(),
            'week' => ActivityLog::where('created_at', '>=', now()->subDays(7))->count(),
            'actions' => $distinctActions->count(),
        ];

        return view('dashboard.activity-logs', compact(
            'logs',
            'distinctActions',
            'summary'
        ));
    }
}
