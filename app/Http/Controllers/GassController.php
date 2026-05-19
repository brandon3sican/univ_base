<?php

namespace App\Http\Controllers;

use App\Models\Gass;
use App\Models\Ppa;
use App\Models\Indicator;
use App\Models\Office;
use App\Models\Type;
use App\Models\RecordType;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class GassController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $gasses = Gass::with(['ppa.recordType', 'indicator'])->get();
        
        if (request()->expectsJson()) {
            return response()->json($gasses);
        }
        
        $offices = Office::all();
        $ppas = Ppa::where('types_id', 1)->get();
        return view('gass.index', compact('gasses', 'offices', 'ppas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $ppas = Ppa::where('types_id', 1)->get();
        $indicators = Indicator::all();
        $offices = Office::all();
        $recordTypes = RecordType::all();
        
        return response()->json([
            'ppas' => $ppas,
            'indicators' => $indicators,
            'offices' => $offices,
            'recordTypes' => $recordTypes,
            'type' => Type::where('code', 'GASS')->first()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'ppa_id' => 'nullable|exists:ppa,id',
                'new_ppa_name' => 'nullable|string|required_without:ppa_id',
                'record_type_id' => 'nullable|required_with:new_ppa_name|exists:record_types,id',
                'types_id' => 'nullable|required_with:new_ppa_name|integer|min:1', // More permissive validation
                'ppa_details_id' => 'nullable', // Remove exists validation since parent_id is nullable
                'ppa_office_id' => 'nullable', // Allow any format, handle in controller
                'indicator_text' => 'nullable|string',
                'indicator_id' => 'nullable|exists:indicators,id',
                'office_id' => 'nullable', // Accept JSON string, handle in controller
                'universe' => 'nullable', // Accept JSON string, handle in controller
                'accomplishment' => 'nullable', // Accept JSON string, handle in controller
                'targets' => 'nullable', // Accept JSON string, handle in controller
                'years' => 'nullable', // Accept JSON string, handle in controller
                'remarks' => 'nullable|string',
            ]);

            // Debug: Log validation data
            \Log::info('GASS Store Request Data:', $request->all());

        // Handle PPA creation if new PPA is provided
        $ppaId = $request->ppa_id;
        if ($request->filled('new_ppa_name')) {
            // Handle ppa_office_id - ensure it's properly formatted
            $ppaOfficeId = $request->ppa_office_id;
            if (is_string($ppaOfficeId)) {
                $ppaOfficeId = json_decode($ppaOfficeId, true) ?? [];
            }
            if (is_array($ppaOfficeId) && empty($ppaOfficeId)) {
                $ppaOfficeId = null; // Set to null for database if empty array
            }

            // Debug: Log PPA creation data
            Log::info('Creating PPA with data:', [
                'name' => $request->new_ppa_name,
                'record_type_id' => $request->record_type_id,
                'types_id' => $request->types_id,
                'ppa_details_id' => $request->ppa_details_id,
                'office_id' => $ppaOfficeId,
            ]);

            try {
                // Check if types_id exists, if not, set to null
                $typesId = $request->types_id;
                if ($typesId && !\App\Models\Type::find($typesId)) {
                    Log::warning('Types ID ' . $typesId . ' not found, setting to null');
                    $typesId = null;
                }

                // Handle ppa_details_id - create default if not provided
                $ppaDetailsId = $request->ppa_details_id;
                if (empty($ppaDetailsId)) {
                    // Create default PPA details record since ALL PPA must have ppa_details
                    $ppaDetails = \App\Models\PpaDetails::create([
                        'parent_id' => null, // Root level record
                        'column_order' => 0, // Default order
                    ]);
                    $ppaDetailsId = $ppaDetails->id;
                    Log::info('Created default PPA details with ID: ' . $ppaDetailsId);
                }

                $ppa = Ppa::create([
                    'name' => $request->new_ppa_name,
                    'record_type_id' => $request->record_type_id,
                    'types_id' => $typesId,
                    'ppa_details_id' => $ppaDetailsId,
                    'office_id' => $ppaOfficeId,
                    'indicator_id' => null, // Will be set below if needed
                ]);
                $ppaId = $ppa->id;
                Log::info('PPA created successfully with ID: ' . $ppaId);
            } catch (\Exception $e) {
                Log::error('PPA creation failed: ' . $e->getMessage());
                throw $e;
            }
        }

        // Handle Indicator creation if new indicator is provided
        $indicatorId = $request->indicator_id;
        if ($request->filled('indicator_text')) {
            $indicator = Indicator::create([
                'name' => $request->indicator_text
            ]);
            $indicatorId = $indicator->id;
        }

        // Handle JSON fields - decode JSON strings to arrays
        $officeId = $request->office_id;
        if (is_string($officeId)) {
            $officeId = json_decode($officeId, true) ?? null;
        }
        
        $universe = $request->universe;
        if (is_string($universe)) {
            $universe = json_decode($universe, true) ?? null;
        }
        
        // Universe is 1 value per office - keep the structure as is
        $universeValue = $universe; // Keep the office->value structure
        
        $accomplishment = $request->accomplishment;
        if (is_string($accomplishment)) {
            $accomplishment = json_decode($accomplishment, true) ?? null;
        }
        
        $targets = $request->targets;
        if (is_string($targets)) {
            $targets = json_decode($targets, true) ?? null;
        }
        
        $years = $request->years;
        if (is_string($years)) {
            $years = json_decode($years, true) ?? null;
        }

        // Create GASS record - ensure JSON fields are properly encoded
        $gassData = [
            'ppa_id' => $ppaId,
            'indicator_id' => $indicatorId,
            // These are JSON columns; model casts handle encoding/decoding.
            'office_id' => $officeId ?: null,
            'universe' => $universeValue ?: null, // office->value structure
            'accomplishment' => $accomplishment ?: null,
            'targets' => $targets ?: null,
            'years' => $years ?: null,
            'remarks' => $request->remarks,
        ];

        // Debug: Log the data being saved
        \Log::info('GASS Data to be saved:', $gassData);
        \Log::info('Universe type: ' . gettype($universe));
        \Log::info('Universe value: ' . var_export($universe, true));

        $gass = Gass::create($gassData);
        
        return response()->json([
                'success' => true,
                'message' => 'GASS record created successfully',
                'data' => $gass->load(['ppa', 'indicator'])
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation Error: ' . $e->getMessage());
            \Log::error('Validation Errors:', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('GASS Store Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            Log::error('Request data: ' . json_encode($request->all()));
            
            return response()->json([
                'success' => false,
                'message' => 'Error creating record: ' . $e->getMessage(),
                'error_details' => $e->getMessage(),
                'request_data' => $request->all()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $gass = Gass::with(['ppa', 'indicator'])->findOrFail($id);
        return response()->json($gass);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): JsonResponse
    {
        $gass = Gass::with(['ppa', 'indicator'])->findOrFail($id);
        $ppas = Ppa::where('types_id', 1)->get();
        $indicators = Indicator::all();
        $offices = Office::all();
        
        // Get all related GASS records for the same PPA
        $relatedGassRecords = Gass::with(['ppa', 'indicator'])
            ->where('ppa_id', $gass->ppa_id)
            ->get();
        
        // Group data by office for easier frontend processing
        $officeData = [];
        foreach ($relatedGassRecords as $record) {
            // Handle JSON office_id field
            $officeIds = $record->office_id;
            if (is_string($officeIds)) {
                $officeIds = json_decode($officeIds, true);
            }
            $officeIds = $officeIds ?: [];
            
            foreach ($officeIds as $officeId) {
                $office = Office::find($officeId);
                if ($office) {
                    $officeData[$officeId] = [
                        'id' => $record->id,
                        'universe' => $record->universe,
                        'accomplishment' => $record->accomplishment,
                        'targets' => $record->targets,
                        'years' => $record->years,
                        'remarks' => $record->remarks,
                        'office_name' => $office->name
                    ];
                }
            }
        }
        
        return response()->json([
            'gass' => $gass,
            'related_records' => $relatedGassRecords,
            'office_data' => $officeData,
            'ppas' => $ppas,
            'indicators' => $indicators,
            'offices' => $offices
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $request->validate([
                'ppa_id' => 'nullable|exists:ppa,id',
                'new_ppa_name' => 'nullable|string',
                'record_type_id' => 'nullable|required_with:new_ppa_name|integer|min:1',
                'types_id' => 'nullable|required_with:new_ppa_name|integer|min:1',
                'ppa_details_id' => 'nullable',
                'ppa_office_id' => 'nullable',
                'indicator_id' => 'nullable|exists:indicators,id',
                'indicator_text' => 'nullable|string',
                // Frontend submits these as JSON strings via FormData (same as store()).
                'office_id' => 'nullable',
                'universe' => 'nullable',
                'accomplishment' => 'nullable',
                'targets' => 'nullable',
                'years' => 'nullable',
                'remarks' => 'nullable|string',
                'editSection' => 'nullable|in:ppa,data'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        try {
            // Find the existing record
            $existingGass = Gass::findOrFail($id);

            $editSection = $request->input('editSection');
            if (!$editSection) {
                // If the client didn't send editSection, infer it from the payload
                $hasDataPayload = $request->hasAny([
                    'office_id', 'universe', 'accomplishment', 'targets', 'years', 'remarks'
                ]);
                $editSection = $hasDataPayload ? 'data' : 'ppa';
            }
            
            // Get current data
            $currentData = [
                'ppa_id' => $existingGass->ppa_id,
                'indicator_id' => $existingGass->indicator_id,
                // These are JSON columns with casts; keep as arrays.
                'office_id' => $existingGass->office_id ?? [],
                'universe' => $existingGass->universe ?? [],
                'accomplishment' => $existingGass->accomplishment ?? [],
                'targets' => $existingGass->targets ?? [],
                'years' => $existingGass->years ?? [],
                'remarks' => $existingGass->remarks,
            ];

            // If indicator text is provided, treat it as an edit to the indicator itself.
            // Prefer updating the existing indicator row (if present) instead of creating duplicates.
            // Frontend should send indicator_text, but some forms may still send "indicator".
            $indicatorText = trim((string) ($request->input('indicator_text') ?? $request->input('indicator') ?? ''));
            if ($indicatorText !== '') {
                if ($existingGass->indicator_id) {
                    Indicator::whereKey($existingGass->indicator_id)->update(['name' => $indicatorText]);
                    $currentData['indicator_id'] = $existingGass->indicator_id;
                } else {
                    $indicator = Indicator::create(['name' => $indicatorText]);
                    $currentData['indicator_id'] = $indicator->id;
                }
            } elseif ($request->filled('indicator_id')) {
                // Allow switching to an existing indicator by id.
                $currentData['indicator_id'] = (int) $request->input('indicator_id');
            }

            // Handle PPA Details section update
            if ($editSection === 'ppa') {
                // Handle PPA creation if new PPA is provided
                $ppaId = $request->input('ppa_id');
                if ($request->filled('new_ppa_name')) {
                    // Handle ppa_office_id
                    $ppaOfficeId = $request->input('ppa_office_id');
                    if (is_string($ppaOfficeId)) {
                        $ppaOfficeId = json_decode($ppaOfficeId, true) ?? [];
                    }
                    if (is_array($ppaOfficeId) && empty($ppaOfficeId)) {
                        $ppaOfficeId = null;
                    }

                    // Handle ppa_details_id
                    $ppaDetailsId = $request->input('ppa_details_id');
                    if (empty($ppaDetailsId)) {
                        $ppaDetails = \App\Models\PpaDetails::create([
                            'parent_id' => null,
                            'column_order' => 0,
                        ]);
                        $ppaDetailsId = $ppaDetails->id;
                    }

                    $ppa = Ppa::create([
                        'name' => $request->new_ppa_name,
                        'record_type_id' => $request->record_type_id,
                        'types_id' => $request->types_id ?? 1,
                        'ppa_details_id' => $ppaDetailsId,
                        'office_id' => $ppaOfficeId,
                        'indicator_id' => null,
                    ]);
                    $ppaId = $ppa->id;
                }

                // Update only PPA-related fields
                $currentData['ppa_id'] = $ppaId ?? $currentData['ppa_id'];
            }

            // Handle Office Assignment & Data section update
            if ($editSection === 'data') {
                // Frontend submits these as JSON strings. Decode if needed.
                $officeIds = $request->input('office_id', $currentData['office_id']);
                if (is_string($officeIds)) {
                    $officeIds = json_decode($officeIds, true);
                }
                $officeIds = $officeIds ?: [];

                $universeData = $request->input('universe', $currentData['universe']);
                if (is_string($universeData)) {
                    $universeData = json_decode($universeData, true);
                }
                $universeData = $universeData ?: [];

                $accomplishmentData = $request->input('accomplishment', $currentData['accomplishment']);
                if (is_string($accomplishmentData)) {
                    $accomplishmentData = json_decode($accomplishmentData, true);
                }
                $accomplishmentData = $accomplishmentData ?: [];

                $targetsData = $request->input('targets', $currentData['targets']);
                if (is_string($targetsData)) {
                    $targetsData = json_decode($targetsData, true);
                }
                $targetsData = $targetsData ?: [];

                $yearsData = $request->input('years', $currentData['years']);
                if (is_string($yearsData)) {
                    $yearsData = json_decode($yearsData, true);
                }
                $yearsData = $yearsData ?: [];

                $remarks = $request->input('remarks', $currentData['remarks']);

                // Update only data-related fields
                $currentData['office_id'] = $officeIds;
                $currentData['universe'] = $universeData;
                $currentData['accomplishment'] = $accomplishmentData;
                $currentData['targets'] = $targetsData;
                $currentData['years'] = $yearsData;
                $currentData['remarks'] = $remarks;
            }

            // Update the existing record with merged data
            $updateData = [
                'ppa_id' => $currentData['ppa_id'],
                'indicator_id' => $currentData['indicator_id'],
                // JSON columns; model casts handle encoding/decoding.
                'office_id' => $currentData['office_id'],
                'universe' => $currentData['universe'],
                'accomplishment' => $currentData['accomplishment'],
                'targets' => $currentData['targets'],
                'years' => $currentData['years'],
                'remarks' => $currentData['remarks'],
            ];
            
            // Debug: Log what we're trying to update
            error_log("GASS UPDATE - ID: $id, Section: $editSection");
            error_log("GASS UPDATE - Data: " . json_encode($updateData));
            
            try {
                $result = $existingGass->update($updateData);
                if (!$result) {
                    throw new \Exception('Update returned false (no changes persisted)');
                }
                error_log("GASS UPDATE - Result: SUCCESS");
                error_log("GASS UPDATE - Updated record: " . json_encode($existingGass->fresh()->toArray()));
            } catch (\Exception $updateException) {
                error_log("GASS UPDATE - Update Exception: " . $updateException->getMessage());
                throw $updateException;
            }
            
            return response()->json([
                'success' => true,
                'message' => 'GASS record updated successfully',
                'data' => $existingGass->load(['ppa', 'indicator'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating record: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get PPAs filtered by record type
     */
    public function getPpasByRecordType(Request $request)
    {
        $recordTypeId = $request->query('record_type_id');
        
        if (!$recordTypeId) {
            return response()->json([]);
        }
        
        $ppas = Ppa::where('record_type_id', $recordTypeId)
            ->where('types_id', 1) // Only GASS PPAs
            ->orderBy('created_at', 'desc')
            ->get(['id', 'name', 'types_id']);
            
        return response()->json($ppas);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $gass = Gass::findOrFail($id);
        $gass->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'GASS record deleted successfully'
        ]);
    }
}
