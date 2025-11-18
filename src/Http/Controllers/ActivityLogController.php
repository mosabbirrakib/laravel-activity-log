<?php

namespace AlMosabbirRakib\ActivityLog\Http\Controllers;

use AlMosabbirRakib\ActivityLog\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;

class ActivityLogController extends Controller
{
    /**
     * Display the activity logs page.
     *
     * @return View
     */
    public function index(): View
    {
        return view('activity-log::index');
    }

    /**
     * Get activity logs with pagination and filtering.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getLogs(Request $request): JsonResponse
    {
        $query = ActivityLog::query()
            ->with(['causer', 'subject'])
            ->orderBy('created_at', 'desc');

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by causer (user)
        if ($request->filled('causer_id') && $request->filled('causer_type')) {
            $query->where('causer_id', $request->causer_id)
                ->where('causer_type', $request->causer_type);
        }

        // Filter by subject
        if ($request->filled('subject_id') && $request->filled('subject_type')) {
            $query->where('subject_id', $request->subject_id)
                ->where('subject_type', $request->subject_type);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search in description
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        // Pagination
        $perPage = $request->input('per_page', config('activity-log.per_page', 15));
        $logs = $query->paginate($perPage);

        // Transform the data
        $logs->getCollection()->transform(function ($log) {
            return [
                'id' => $log->id,
                'type' => $log->type,
                'description' => $log->description,
                'properties' => $log->properties,
                'causer' => $log->causer ? [
                    'id' => $log->causer->id,
                    'name' => $log->causer_name,
                    'type' => $log->causer_type,
                ] : null,
                'subject' => $log->subject ? [
                    'id' => $log->subject->id,
                    'name' => $log->subject_name,
                    'type' => $log->subject_type,
                ] : null,
                'ip_address' => $log->ip_address,
                'user_agent' => $log->user_agent,
                'created_at' => $log->created_at->toISOString(),
                'formatted_created_at' => $log->formatted_created_at,
                'type_badge_color' => $log->type_badge_color,
            ];
        });

        return response()->json($logs);
    }

    /**
     * Get a single activity log.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $log = ActivityLog::with(['causer', 'subject'])->findOrFail($id);

        return response()->json([
            'id' => $log->id,
            'type' => $log->type,
            'description' => $log->description,
            'properties' => $log->properties,
            'causer' => $log->causer ? [
                'id' => $log->causer->id,
                'name' => $log->causer_name,
                'type' => $log->causer_type,
            ] : null,
            'subject' => $log->subject ? [
                'id' => $log->subject->id,
                'name' => $log->subject_name,
                'type' => $log->subject_type,
            ] : null,
            'ip_address' => $log->ip_address,
            'user_agent' => $log->user_agent,
            'created_at' => $log->created_at->toISOString(),
            'formatted_created_at' => $log->formatted_created_at,
            'type_badge_color' => $log->type_badge_color,
        ]);
    }

    /**
     * Get activity log types.
     *
     * @return JsonResponse
     */
    public function getTypes(): JsonResponse
    {
        $types = ActivityLog::select('type')
            ->distinct()
            ->whereNotNull('type')
            ->pluck('type');

        return response()->json($types);
    }

    /**
     * Get activity log statistics.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getStats(Request $request): JsonResponse
    {
        $query = ActivityLog::query();

        // Apply date range filter if provided
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $stats = [
            'total' => (clone $query)->count(),
            'by_type' => (clone $query)->selectRaw('type, count(*) as count')
                ->groupBy('type')
                ->pluck('count', 'type'),
            'today' => (clone $query)->whereDate('created_at', today())->count(),
            'this_week' => (clone $query)->whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
            'this_month' => (clone $query)->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Delete old activity logs.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function cleanup(Request $request): JsonResponse
    {
        $request->validate([
            'days' => 'required|integer|min:1',
        ]);

        $date = now()->subDays($request->days);
        $deleted = ActivityLog::where('created_at', '<', $date)->delete();

        return response()->json([
            'message' => "Deleted {$deleted} activity logs older than {$request->days} days.",
            'deleted' => $deleted,
        ]);
    }
}

