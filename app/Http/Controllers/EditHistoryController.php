<?php

namespace App\Http\Controllers;

use App\Models\EditHistory;
use Illuminate\Http\Request;

class EditHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = EditHistory::with('user')->orderBy('created_at', 'desc');

        // Filter by model type if provided
        if ($request->has('model_type') && $request->model_type) {
            $query->where('model_type', 'like', '%' . $request->model_type . '%');
        }

        // Filter by action if provided
        if ($request->has('action') && $request->action) {
            $query->where('action', $request->action);
        }

        // Filter by user if provided
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date range if provided
        if ($request->has('date_from') && $request->date_from) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $editHistories = $query->paginate(50);

        // Get unique model types for filter dropdown
        $modelTypes = EditHistory::select('model_type')
            ->distinct()
            ->pluck('model_type')
            ->map(fn($type) => class_basename($type))
            ->unique()
            ->sort()
            ->values();

        // Get unique users for filter dropdown
        $users = \App\Models\User::select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('edit-history.index', compact('editHistories', 'modelTypes', 'users'));
    }

    public function show($id)
    {
        $editHistory = EditHistory::with('user')->findOrFail($id);

        return view('edit-history.show', compact('editHistory'));
    }
}
