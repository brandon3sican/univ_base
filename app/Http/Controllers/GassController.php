<?php

namespace App\Http\Controllers;

use App\Models\Gass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GassController extends Controller
{
    public function index()
    {
        // Get all root records (no parent) ordered by order_column
        $gassRecords = Gass::whereNull('parent_id')
            ->with(['children' => function ($query) {
                $query->with(['children' => function ($query) {
                    $query->with(['children'])->orderBy('order_column');
                }])->orderBy('order_column');
            }])
            ->orderBy('order_column')
            ->get();

        return view('gass.index', compact('gassRecords'));
    }

    public function store(Request $request)
    {
        try {
            // Basic validation
            $validated = $request->validate([
                'program_project_activity' => 'required|string',
                'record_type' => 'required|in:program,project,activity',
                'output_indicators' => 'nullable|string',
                'office' => 'nullable|string',
                'universe' => 'nullable|array',
                'accomplishment' => 'nullable|array',
                'remarks' => 'nullable|string',
                'parent_id' => 'nullable|integer|exists:gass,id',
                'project_has_indicators' => 'nullable|in:yes,no',
                'activity_has_subactivities' => 'nullable|in:yes,no',
                'target_2024' => 'nullable|array',
                'target_2025' => 'nullable|array',
                'target_2026' => 'nullable|array',
                'target_2027' => 'nullable|array',
                'target_2028' => 'nullable|array',
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
                        'universe' => 'required|array',
                        'accomplishment' => 'required|array',
                    ]);
                }
            } elseif ($validated['record_type'] === 'activity') {
                $request->validate([
                    'activity_has_subactivities' => 'required|in:yes,no',
                ]);

                if ($request->activity_has_subactivities === 'no') {
                    $request->validate([
                        'office' => 'required|string',
                        'universe' => 'required|array',
                        'accomplishment' => 'required|array',
                    ]);
                }
            }

            // Check accomplishment vs universe if both are provided
            if (isset($validated['universe']) && isset($validated['accomplishment'])) {
                // Compare per office, not total arrays
                $universeArray = $validated['universe'];
                $accomplishmentArray = $validated['accomplishment'];
                
                foreach ($universeArray as $office => $universeValue) {
                    $accomplishmentValue = $accomplishmentArray[$office] ?? 0;
                    if ($accomplishmentValue > $universeValue) {
                        return response()->json([
                            'success' => false,
                            'message' => "Accomplishment cannot be greater than universe for office: {$office}.",
                        ], 422);
                    }
                }
            }

            // Only include fields that have data
            $data = [
                'program_project_activity' => $validated['program_project_activity'],
                'record_type' => $validated['record_type'],
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
                if (!empty($validated['output_indicators'])) {
                    $data['output_indicators'] = $validated['output_indicators'];
                }
                if (!empty($validated['office'])) {
                    $data['office'] = $validated['office'];
                }
                if (isset($validated['universe'])) {
                    // Convert array to comma-separated string for storage, preserving empty values as "-"
                    if (is_array($validated['universe'])) {
                        $universeValues = array_map(function($value) {
                            return ($value === '' || $value === null) ? '-' : $value;
                        }, $validated['universe']);
                        $data['universe'] = implode(',', $universeValues);
                    } else {
                        $data['universe'] = $validated['universe'];
                    }
                }
                if (isset($validated['accomplishment'])) {
                    // Convert array to comma-separated string for storage, preserving empty values as "-"
                    if (is_array($validated['accomplishment'])) {
                        $accomplishmentValues = array_map(function($value) {
                            return ($value === '' || $value === null) ? '-' : $value;
                        }, $validated['accomplishment']);
                        $data['accomplishment'] = implode(',', $accomplishmentValues);
                    } else {
                        $data['accomplishment'] = $validated['accomplishment'];
                    }
                }
                if (isset($validated['remarks'])) {
                    // Remarks is already a semicolon-separated string from frontend
                    $data['remarks'] = $validated['remarks'];
                }
                
                // Process target fields (2024-2028) - store as comma-separated strings per office
                for ($year = 2024; $year <= 2028; $year++) {
                    $targetField = "target_{$year}";
                    if (isset($validated[$targetField]) && is_array($validated[$targetField])) {
                        // Convert empty values to "-" and keep all values for proper office alignment
                        $targetValues = array_map(function($value) {
                            return ($value === '' || $value === null) ? '-' : $value;
                        }, $validated[$targetField]);
                        $data[$targetField] = implode(',', $targetValues);
                    }
                }
            }

            // Set order_column
            $maxOrder = Gass::max('order_column') ?? 0;
            $data['order_column'] = $maxOrder + 1;

            // Include parent_id if provided
            if (isset($validated['parent_id']) && $validated['parent_id']) {
                $data['parent_id'] = $validated['parent_id'];
            }

            // Create the record
            $gass = Gass::create($data);

            return response()->json([
                'success' => true,
                'message' => 'GASS record created successfully.',
                'data' => $gass,
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
                'message' => 'Error creating record: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        $gass = Gass::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $gass,
        ]);
    }

    public function update(Request $request, $id)
    {
        try {
            $gass = Gass::findOrFail($id);

            // Basic validation
            $validated = $request->validate([
                'program_project_activity' => 'required|string',
                'record_type' => 'required|in:program,project,activity',
                'output_indicators' => 'nullable|string',
                'office' => 'nullable|string',
                'universe' => 'nullable|array',
                'accomplishment' => 'nullable|array',
                'remarks' => 'nullable|string',
                'parent_id' => 'nullable|integer|exists:gass,id',
                'project_has_indicators' => 'nullable|in:yes,no',
                'activity_has_subactivities' => 'nullable|in:yes,no',
                'target_2024' => 'nullable|array',
                'target_2025' => 'nullable|array',
                'target_2026' => 'nullable|array',
                'target_2027' => 'nullable|array',
                'target_2028' => 'nullable|array',
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
                        'universe' => 'required|array',
                        'accomplishment' => 'required|array',
                    ]);
                }
            } elseif ($validated['record_type'] === 'activity') {
                $request->validate([
                    'activity_has_subactivities' => 'required|in:yes,no',
                ]);

                if ($request->activity_has_subactivities === 'no') {
                    $request->validate([
                        'office' => 'required|string',
                        'universe' => 'required|array',
                        'accomplishment' => 'required|array',
                    ]);
                }
            }

            // Check accomplishment vs universe if both are provided
            if (isset($validated['universe']) && isset($validated['accomplishment'])) {
                // Compare per office, not total arrays
                $universeArray = $validated['universe'];
                $accomplishmentArray = $validated['accomplishment'];
                
                foreach ($universeArray as $office => $universeValue) {
                    $accomplishmentValue = $accomplishmentArray[$office] ?? 0;
                    if ($accomplishmentValue > $universeValue) {
                        return response()->json([
                            'success' => false,
                            'message' => "Accomplishment cannot be greater than universe for office: {$office}.",
                        ], 422);
                    }
                }
            }

            // Only include fields that have data
            $data = [
                'program_project_activity' => $validated['program_project_activity'],
                'record_type' => $validated['record_type'],
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
                if (!empty($validated['output_indicators'])) {
                    $data['output_indicators'] = $validated['output_indicators'];
                }
                if (!empty($validated['office'])) {
                    $data['office'] = $validated['office'];
                }
                if (isset($validated['universe'])) {
                    // Convert array to comma-separated string for storage, preserving empty values as "-"
                    if (is_array($validated['universe'])) {
                        $universeValues = array_map(function($value) {
                            return ($value === '' || $value === null) ? '-' : $value;
                        }, $validated['universe']);
                        $data['universe'] = implode(',', $universeValues);
                    } else {
                        $data['universe'] = $validated['universe'];
                    }
                }
                if (isset($validated['accomplishment'])) {
                    // Convert array to comma-separated string for storage, preserving empty values as "-"
                    if (is_array($validated['accomplishment'])) {
                        $accomplishmentValues = array_map(function($value) {
                            return ($value === '' || $value === null) ? '-' : $value;
                        }, $validated['accomplishment']);
                        $data['accomplishment'] = implode(',', $accomplishmentValues);
                    } else {
                        $data['accomplishment'] = $validated['accomplishment'];
                    }
                }
                if (isset($validated['remarks'])) {
                    // Remarks is already a semicolon-separated string from frontend
                    $data['remarks'] = $validated['remarks'];
                }
                
                // Process target fields (2024-2028) - store as comma-separated strings per office
                for ($year = 2024; $year <= 2028; $year++) {
                    $targetField = "target_{$year}";
                    if (isset($validated[$targetField]) && is_array($validated[$targetField])) {
                        // Convert empty values to "-" and keep all values for proper office alignment
                        $targetValues = array_map(function($value) {
                            return ($value === '' || $value === null) ? '-' : $value;
                        }, $validated[$targetField]);
                        $data[$targetField] = implode(',', $targetValues);
                    }
                }
            }

            // Include parent_id if provided
            if (isset($validated['parent_id']) && $validated['parent_id']) {
                $data['parent_id'] = $validated['parent_id'];
            }

            // Update the record
            $gass->update($data);

            return response()->json([
                'success' => true,
                'message' => 'GASS record updated successfully.',
                'data' => $gass,
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
                'message' => 'Error updating record: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $gass = Gass::findOrFail($id);
            $gass->delete();

            return response()->json([
                'success' => true,
                'message' => 'GASS record deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting record: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function moveUp($id)
    {
        try {
            $record = Gass::findOrFail($id);

            // Get the record with the next higher order (lower order_column value)
            $previousRecord = Gass::where('order_column', '<', $record->order_column)
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
                'message' => 'Error moving record up: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function moveDown($id)
    {
        try {
            $record = Gass::findOrFail($id);

            // Get the record with the next lower order (higher order_column value)
            $nextRecord = Gass::where('order_column', '>', $record->order_column)
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
                'message' => 'Error moving record down: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function reorder(Request $request)
    {
        try {
            $draggedId = $request->input('dragged_id');
            $targetId = $request->input('target_id');

            $draggedRecord = Gass::findOrFail($draggedId);
            $targetRecord = Gass::findOrFail($targetId);

            // Get all records ordered by current order
            $allRecords = Gass::orderBy('order_column')->get();

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
                'message' => 'Error reordering records: ' . $e->getMessage(),
            ], 500);
        }
    }
}
