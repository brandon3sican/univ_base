<?php

namespace App\Http\Controllers;

use App\Models\Nra;
use Illuminate\Http\Request;

class NraController extends Controller
{
    public function index()
    {
        // Get all root records (no parent) ordered by order_column
        $nraRecords = Nra::whereNull('parent_id')
            ->with(['children' => function ($query) {
                $query->with(['children' => function ($query) {
                    $query->with(['children'])->orderBy('order_column');
                }])->orderBy('order_column');
            }])
            ->orderBy('order_column')
            ->get();

        return view('nra.index', compact('nraRecords'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'program_project_activity' => 'required|string',
                'record_type' => 'required|in:program,project,activity',
                'output_indicators' => 'nullable|string',
                'office' => 'nullable|string',
                'universe' => 'nullable|integer|min:0',
                'accomplishment' => 'nullable|integer|min:0',
                'parent_id' => 'nullable|integer|exists:nra,id',
            ]);

            // Handle conditional validation based on record type
            if ($validated['record_type'] === 'project') {
                $request->validate([
                    'project_has_indicators' => 'required|in:yes,no',
                ]);

                if ($request->project_has_indicators === 'yes') {
                    $request->validate([
                        'output_indicators' => 'required|string',
                        'office' => 'required|string',
                        'universe' => 'nullable|integer|min:0',
                        'accomplishment' => 'nullable|integer|min:0',
                    ]);
                }
            } elseif ($validated['record_type'] === 'activity') {

                $request->validate([
                    'activity_has_subactivities' => 'required|in:yes,no',
                ]);

                // Activity WITHOUT subactivities → requires indicators
                if ($request->activity_has_subactivities === 'no') {

                    $request->validate([
                        'office' => 'required|string',
                        'universe' => 'nullable|integer|min:0',
                        'accomplishment' => 'nullable|integer|min:0',
                    ]);

                } elseif ($request->activity_has_subactivities === 'yes') {
                    // Activity with sub-activities - parent_id can be null (no validation needed)
                    // No validation required for parent_id
                }
            }

            // Check accomplishment vs universe if both are provided
            if (isset($validated['universe']) && isset($validated['accomplishment'])) {
                if ($validated['accomplishment'] > $validated['universe']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Accomplishment cannot be greater than universe.',
                    ], 422);
                }
            }

            // Only include fields that have data
            $data = [
                'program_project_activity' => $validated['program_project_activity'],
                'record_type' => $validated['record_type'],
                'order_column' => Nra::max('order_column') + 1,
            ];

            // Add parent_id for activities if provided
            if (isset($validated['parent_id'])) {
                $data['parent_id'] = $validated['parent_id'];
            }

            // Add optional fields if they exist
            if ($validated['record_type'] === 'activity' && isset($validated['activity_has_subactivities']) && $validated['activity_has_subactivities'] === 'yes') {
                // Activity with sub-activities - set other fields to null
                $data['output_indicators'] = null;
                $data['office'] = null;
                $data['universe'] = null;
                $data['accomplishment'] = null;
            } else {
                // Regular record - include fields if they have data
                if (! empty($validated['output_indicators'])) {
                    $data['output_indicators'] = $validated['output_indicators'];
                }
                if (! empty($validated['office'])) {
                    $data['office'] = $validated['office'];
                }
                
                // Always include universe and accomplishment if they're in the validated data
                // This handles both setting values and clearing them (null)
                if (array_key_exists('universe', $validated)) {
                    $data['universe'] = $validated['universe'];
                }
                if (array_key_exists('accomplishment', $validated)) {
                    $data['accomplishment'] = $validated['accomplishment'];
                }
            }

            $nra = Nra::create($data);

            return response()->json([
                'success' => true,
                'message' => 'NRA record created successfully.',
                'data' => $nra,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating record: '.$e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $nra = Nra::findOrFail($id);

            $validated = $request->validate([
                'program_project_activity' => 'required|string',
                'record_type' => 'required|in:program,project,activity',
                'output_indicators' => 'nullable|string',
                'office' => 'nullable|string',
                'universe' => 'nullable|integer|min:0',
                'accomplishment' => 'nullable|integer|min:0',
            ]);

            // Handle conditional validation based on record type
            if ($validated['record_type'] === 'project') {
                $request->validate([
                    'project_has_indicators' => 'required|in:yes,no',
                ]);

                if ($request->project_has_indicators === 'yes') {
                    $request->validate([
                        'output_indicators' => 'required|string',
                        'office' => 'required|string',
                        'universe' => 'nullable|integer|min:0',
                        'accomplishment' => 'nullable|integer|min:0',
                    ]);
                }
            } elseif ($validated['record_type'] === 'activity') {
                $request->validate([
                    'activity_has_subactivities' => 'required|in:yes,no',
                ]);

                if ($request->activity_has_subactivities === 'no') {
                    $request->validate([
                        'output_indicators' => 'nullable|string',
                        'office' => 'required|string',
                        'universe' => 'nullable|integer|min:0',
                        'accomplishment' => 'nullable|integer|min:0',
                    ]);
                } elseif ($request->activity_has_subactivities === 'yes') {
                    // Activity with sub-activities - parent_id can be null (no validation needed)
                    // No validation required for parent_id
                }
            }

            // Check accomplishment vs universe if both are provided
            if (isset($validated['universe']) && isset($validated['accomplishment'])) {
                if ($validated['accomplishment'] > $validated['universe']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Accomplishment cannot be greater than universe.',
                    ], 422);
                }
            }

            // Only include fields that have data
            $data = [
                'program_project_activity' => $validated['program_project_activity'],
            ];

            // Add optional fields if they exist
            if ($validated['record_type'] === 'activity' && isset($validated['activity_has_subactivities']) && $validated['activity_has_subactivities'] === 'yes') {
                // Activity with sub-activities - set other fields to null
                $data['output_indicators'] = null;
                $data['office'] = null;
                $data['universe'] = null;
                $data['accomplishment'] = null;
            } else {
                // Regular record - include fields if they have data
                if (! empty($validated['output_indicators'])) {
                    $data['output_indicators'] = $validated['output_indicators'];
                }
                if (! empty($validated['office'])) {
                    $data['office'] = $validated['office'];
                }
                
                // Always include universe and accomplishment if they're in the validated data
                // This handles both setting values and clearing them (null)
                if (array_key_exists('universe', $validated)) {
                    $data['universe'] = $validated['universe'];
                }
                if (array_key_exists('accomplishment', $validated)) {
                    $data['accomplishment'] = $validated['accomplishment'];
                }
            }

            $nra->update($data);

            return response()->json([
                'success' => true,
                'message' => 'NRA record updated successfully.',
                'data' => $nra,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating record: '.$e->getMessage(),
            ], 500);
        }
    }

    public function moveUp($id)
    {
        try {
            $record = Nra::findOrFail($id);

            // Get the record with the next higher order (lower order_column value)
            $previousRecord = Nra::where('order_column', '<', $record->order_column)
                ->orderBy('order_column', 'desc')
                ->first();

            if ($previousRecord) {
                // Swap order values
                $currentOrder = $record->order_column;
                $previousOrder = $previousRecord->order_column;

                $record->order_column = $previousOrder;
                $record->save();

                $previousRecord->order_column = $currentOrder;
                $previousRecord->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Record moved up successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error moving record up: '.$e->getMessage(),
            ], 500);
        }
    }

    public function moveDown($id)
    {
        try {
            $record = Nra::findOrFail($id);

            // Get the record with the next lower order (higher order_column value)
            $nextRecord = Nra::where('order_column', '>', $record->order_column)
                ->orderBy('order_column', 'asc')
                ->first();

            if ($nextRecord) {
                // Swap order values
                $currentOrder = $record->order_column;
                $nextOrder = $nextRecord->order_column;

                $record->order_column = $nextOrder;
                $record->save();

                $nextRecord->order_column = $currentOrder;
                $nextRecord->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Record moved down successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error moving record down: '.$e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        $nra = Nra::findOrFail($id);
        $nra->delete();

        return response()->json([
            'success' => true,
            'message' => 'NRA record deleted successfully.',
        ]);
    }

    public function show($id)
    {
        $nra = Nra::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $nra,
        ]);
    }

    public function reorder(Request $request)
    {
        try {
            $draggedId = $request->input('dragged_id');
            $targetId = $request->input('target_id');

            $draggedRecord = Nra::findOrFail($draggedId);
            $targetRecord = Nra::findOrFail($targetId);

            // Get all records ordered by current order
            $allRecords = Nra::orderBy('order_column')->get();

            // Find positions in the current order
            $draggedPosition = $allRecords->search(function ($record) use ($draggedId) {
                return $record->id == $draggedId;
            });
            $targetPosition = $allRecords->search(function ($record) use ($targetId) {
                return $record->id == $targetId;
            });

            // Remove dragged record from array
            $draggedRecordData = $allRecords->splice($draggedPosition, 1)[0];

            // Insert dragged record at target position
            array_splice($allRecords, $targetPosition, 0, [$draggedRecordData]);

            // Update order_column for all records
            foreach ($allRecords as $index => $record) {
                $record->order_column = $index + 1;
                $record->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Records reordered successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error reordering records: '.$e->getMessage(),
            ], 500);
        }
    }
}
