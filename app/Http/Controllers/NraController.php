<?php

namespace App\Http\Controllers;

use App\Models\Indicator;
use App\Models\Nra;
use App\Models\Office;
use App\Models\Ppa;
use App\Models\RecordType;
use App\Models\Type;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $nras = Nra::with(['ppa.recordType', 'indicator', 'office'])->get();

        if (request()->expectsJson()) {
            return response()->json($nras);
        }

        $offices = Office::all();
        $ppas = Ppa::where('types_id', 7)->get();

        return view('nra.index', compact('nras', 'offices', 'ppas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $ppas = Ppa::where('types_id', 7)->get();
        $indicators = Indicator::all();
        $offices = Office::all();
        $recordTypes = RecordType::all();

        return response()->json([
            'ppas' => $ppas,
            'indicators' => $indicators,
            'offices' => $offices,
            'recordTypes' => $recordTypes,
            'type' => Type::where('code', 'NRA')->first(),
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
            \Log::info('NRA Store Request Data:', $request->all());

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
                    if ($typesId && ! \App\Models\Type::find($typesId)) {
                        Log::warning('Types ID '.$typesId.' not found, setting to null');
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
                        Log::info('Created default PPA details with ID: '.$ppaDetailsId);
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
                    Log::info('PPA created successfully with ID: '.$ppaId);
                } catch (\Exception $e) {
                    Log::error('PPA creation failed: '.$e->getMessage());
                    throw $e;
                }
            }

            // Handle Indicator creation if new indicator is provided
            $indicatorId = $request->indicator_id;
            if ($request->filled('indicator_text')) {
                $indicator = Indicator::create([
                    'name' => $request->indicator_text,
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

            // Create NRA record - ensure JSON fields are properly encoded
            $nraData = [
                'ppa_id' => $ppaId,
                'indicator_id' => $indicatorId,
                'office_id' => $officeId ? json_encode($officeId) : null,
                'universe' => $universeValue ? json_encode($universeValue) : null, // Store office->value structure as JSON
                'accomplishment' => $accomplishment ? json_encode($accomplishment) : null,
                'targets' => $targets ? json_encode($targets) : null,
                'years' => $years ? json_encode($years) : null,
                'remarks' => $request->remarks,
            ];

            // Debug: Log the data being saved
            \Log::info('NRA Data to be saved:', $nraData);
            \Log::info('Universe type: '.gettype($universe));
            \Log::info('Universe value: '.var_export($universe, true));

            $nra = Nra::create($nraData);

            return response()->json([
                'success' => true,
                'message' => 'NRA record created successfully',
                'data' => $nra->load(['ppa', 'indicator', 'office']),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation Error: '.$e->getMessage());
            \Log::error('Validation Errors:', $e->errors());

            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('GASS Store Error: '.$e->getMessage());
            Log::error('Stack trace: '.$e->getTraceAsString());
            Log::error('Request data: '.json_encode($request->all()));

            return response()->json([
                'success' => false,
                'message' => 'Error creating record: '.$e->getMessage(),
                'error_details' => $e->getMessage(),
                'request_data' => $request->all(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $nra = Nra::with(['ppa', 'indicator', 'office'])->findOrFail($id);

        return response()->json($nra);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): JsonResponse
    {
        $nra = Nra::with(['ppa', 'indicator', 'office'])->findOrFail($id);
        $ppas = Ppa::where('types_id', 7)->get();
        $indicators = Indicator::all();
        $offices = Office::all();

        return response()->json([
            'nra' => $nra,
            'ppas' => $ppas,
            'indicators' => $indicators,
            'offices' => $offices,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'ppa_id' => 'nullable|exists:ppa,id',
            'indicator_id' => 'nullable|exists:indicators,id',
            'office_id' => 'nullable|exists:offices,id',
            'universe' => 'nullable|array',
            'accomplishment' => 'nullable|array',
            'targets' => 'nullable|array',
            'years' => 'nullable|array',
        ]);

        $nra = Nra::findOrFail($id);
        $nra->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'NRA record updated successfully',
            'data' => $nra->load(['ppa', 'indicator', 'office']),
        ]);
    }

    /**
     * Get PPAs filtered by record type
     */
    public function getPpasByRecordType(Request $request)
    {
        $recordTypeId = $request->query('record_type_id');

        if (! $recordTypeId) {
            return response()->json([]);
        }

        $ppas = Ppa::where('record_type_id', $recordTypeId)
            ->where('types_id', 7) // Only NRA PPAs
            ->orderBy('created_at', 'desc')
            ->get(['id', 'name', 'types_id']);

        return response()->json($ppas);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $nra = Nra::findOrFail($id);
        $nra->delete();

        return response()->json([
            'success' => true,
            'message' => 'NRA record deleted successfully',
        ]);
    }
}
