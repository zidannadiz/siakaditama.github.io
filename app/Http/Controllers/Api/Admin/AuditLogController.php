<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AuditLogController extends Controller
{
    /**
     * Get audit logs with filters
     */
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by model type
        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $query->where('created_at', '>=', $startDate);
        }

        if ($request->filled('end_date')) {
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->where('created_at', '<=', $endDate);
        }

        // Search in description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhere('model_type', 'like', "%{$search}%");
            });
        }

        $perPage = $request->per_page ?? 50;
        $logs = $query->paginate($perPage);

        // Get filter options
        $actions = AuditLog::distinct()->pluck('action')->sort()->values();
        $modelTypes = AuditLog::distinct()->whereNotNull('model_type')->pluck('model_type')->sort()->values();
        $users = User::whereIn('id', AuditLog::distinct()->whereNotNull('user_id')->pluck('user_id'))
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'logs' => $logs->items(),
                'pagination' => [
                    'current_page' => $logs->currentPage(),
                    'last_page' => $logs->lastPage(),
                    'per_page' => $logs->perPage(),
                    'total' => $logs->total(),
                ],
                'filters' => [
                    'actions' => $actions,
                    'model_types' => $modelTypes,
                    'users' => $users->map(function($user) {
                        return [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                        ];
                    }),
                ],
            ],
        ]);
    }

    /**
     * Get audit log detail
     */
    public function show(AuditLog $auditLog)
    {
        $auditLog->load('user');
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $auditLog->id,
                'action' => $auditLog->action,
                'model_type' => $auditLog->model_type,
                'model_id' => $auditLog->model_id,
                'description' => $auditLog->description,
                'old_values' => $auditLog->old_values,
                'new_values' => $auditLog->new_values,
                'user' => $auditLog->user ? [
                    'id' => $auditLog->user->id,
                    'name' => $auditLog->user->name,
                    'email' => $auditLog->user->email,
                ] : null,
                'created_at' => $auditLog->created_at->toDateTimeString(),
            ],
        ]);
    }
}
