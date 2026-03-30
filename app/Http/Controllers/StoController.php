<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sto;
use App\Models\StoUniverse;
use App\Models\StoAccomplishment;
use App\Models\StoTarget;
use App\Models\Ppa;
use App\Models\PpaDetails;
use App\Models\Indicator;
use App\Models\Office;
use App\Models\RecordType;
use App\Models\Type;
use Illuminate\Support\Facades\DB;

class StoController extends Controller
{
    public function index()
    {
        $stos = Sto::with(['ppa', 'indicator'])->get();
        $offices = Office::all();
        $recordTypes = RecordType::all();
        $ppas = Ppa::with('recordType')->get();
        return view('sto.index', compact('stos', 'offices', 'recordTypes', 'ppas'));
    }

    public function create()
    {
        $ppas = Ppa::all();
        $indicators = Indicator::all();
        $universes = StoUniverse::all();
        $accomplishments = StoAccomplishment::all();
        $targets = StoTarget::all();
        
        return view('sto.create', compact('ppas', 'indicators', 'universes', 'accomplishments', 'targets'));
    }

    public function store(Request $request)
    {
        // Debug: Log all request data
        \Log::info('STO Store Request Data:', $request->all());
        
        DB::beginTransaction();
        
        try {
            // Step 1: Create PPA Details record first (always create if we have offices or indicator)
            $ppaDetails = null;
            $ppa = null;
            
            if ($request->filled('ppa') || $request->filled('indicator') || $request->has('offices')) {
                $ppaDetails = PpaDetails::create([
                    'parent_id' => $request->parent_activity ?: null,
                    'column_order' => PpaDetails::max('column_order') + 1,
                ]);

                // Create PPA record to match ppa_details
                $ppa = Ppa::create([
                    'name' => $request->ppa ?: 'Auto-generated PPA',
                    'types_id' => $request->types_id,
                    'record_type_id' => $request->record_type,
                    'ppa_details_id' => $ppaDetails->id,
                    'indicator_id' => null, // Will be set later
                    'office_id' => $request->offices ?? [],
                ]);
            }

            // Step 1: Create or find Indicator record
            $indicator = null;
            if ($request->filled('indicator')) {
                $indicator = Indicator::create([
                    'name' => $request->indicator,
                ]);
                
                // Update PPA with indicator_id if both exist
                if ($ppa && $indicator) {
                    $ppa->indicator_id = $indicator->id;
                    $ppa->save();
                }
            }

            // Step 2: Create STO record and office data using existing structure
            $sto = null;
            
            if ($request->filled('ppa') || $request->filled('indicator') || $request->has('offices')) {
                // Create the main STO record
                $sto = Sto::create([
                    'ppa_id' => $ppa ? $ppa->id : null,
                    'indicator_id' => $indicator ? $indicator->id : null,
                    'universe_id' => null, // Will use sto_accomplishments instead
                    'accomplishment_id' => null, // Will use sto_accomplishments instead
                    'targets_id' => null, // Will handle separately if needed
                ]);
                
                // Process office data if provided
                if ($request->has('office_data') && !empty($request->office_data)) {
                    $officeDataArray = json_decode($request->office_data, true);
                    
                    // Debug: log what we received
                    \Log::info('Office data received:', ['office_data' => $officeDataArray]);
                    
                    $officeIds = [];
                    $universeValues = [];
                    $accomplishmentValues = [];
                    $remarksArray = [];
                    $yearsArray = [];
                    
                    // Collect data for each office
                    foreach ($officeDataArray as $officeData) {
                        $officeIds[] = (int) $officeData['office_id'];
                        $universeValues[] = isset($officeData['universe']) && is_numeric($officeData['universe']) ? (int) $officeData['universe'] : null;
                        $remarksArray[] = isset($officeData['remarks']) ? $officeData['remarks'] : null;
                        
                        // Create accomplishment values array for all years
                        $yearlyAccomplishments = [];
                        $years = [2022, 2023, 2024, 2025, 2026, 2027, 2028];
                        
                        foreach ($years as $year) {
                            $key = "accomplishment_{$year}";
                            $yearlyAccomplishments[$year] = isset($officeData[$key]) && is_numeric($officeData[$key]) ? (int) $officeData[$key] : null;
                        }
                        
                        $accomplishmentValues[] = $yearlyAccomplishments;
                        $yearsArray[] = $years; // Store the years array for each office
                    }
                    
                    // Debug: log the arrays we're about to save
                    \Log::info('Arrays to save:', [
                        'office_ids' => $officeIds,
                        'universe_values' => $universeValues,
                        'accomplishment_values' => $accomplishmentValues,
                        'remarks' => $remarksArray,
                        'years' => $yearsArray
                    ]);
                    
                    // Create universe record if we have universe data
                    if (!empty($officeIds) && !empty(array_filter($universeValues, function($value) { return $value !== null; }))) {
                        $stoUniverse = StoUniverse::create([
                            'office_ids' => $officeIds,
                            'values' => $universeValues,
                        ]);
                        
                        // Update STO with universe_id
                        $sto->universe_id = $stoUniverse->id;
                        $sto->save();
                        
                        \Log::info('STO Universe created with ID: ' . $stoUniverse->id);
                    }
                    
                    // Create accomplishment record with arrays
                    if (!empty($officeIds)) {
                        $stoAccomplishment = StoAccomplishment::create([
                            'office_ids' => $officeIds,
                            'values' => $accomplishmentValues, // Array of yearly accomplishment arrays
                            'remarks' => $remarksArray,
                            'years' => $yearsArray, // Array of years arrays
                        ]);
                        
                        // Update STO with accomplishment_id
                        $sto->accomplishment_id = $stoAccomplishment->id;
                        $sto->save();
                        
                        \Log::info('STO Accomplishment created with ID: ' . $stoAccomplishment->id);
                    }
                } else {
                    \Log::info('No office data found in request');
                }
            }

            // Step 3: Create Target records for each period and office
            $targetRecords = [];
            
            // Extract target periods from form data
            $targetData = [];
            foreach ($request->all() as $key => $value) {
                if (strpos($key, 'target_') === 0) {
                    // Parse key format: target_{officeId}_{year}
                    $parts = explode('_', $key);
                    if (count($parts) === 3) {
                        $officeId = $parts[1];
                        $year = $parts[2];
                        
                        if (!isset($targetData[$officeId])) {
                            $targetData[$officeId] = [];
                        }
                        // Save target even if blank (will store NULL)
                        $targetData[$officeId][$year] = is_numeric($value) ? (int) $value : null;
                    }
                }
            }

            // Create target records if we have target data
            if (!empty($targetData)) {
                foreach ($targetData as $officeId => $yearlyTargets) {
                    $values = [];
                    $yearsArray = [];
                    
                    foreach ($yearlyTargets as $year => $value) {
                        $values[] = is_numeric($value) ? (int) $value : null;
                        $yearsArray[] = is_numeric($year) ? (int) $year : null;
                    }
                    
                    $targetRecords[] = StoTarget::create([
                        'values' => $values,
                        'years' => $yearsArray,
                    ]);
                }
                
                // Update STO with targets_id if targets were created
                if ($sto && !empty($targetRecords)) {
                    $sto->targets_id = array_column($targetRecords, 'id');
                    $sto->save();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'STO record created successfully.',
                'sto_id' => $sto->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error creating STO record: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Sto $sto)
    {
        $sto->load(['ppa', 'indicator']);
        
        // Return JSON for AJAX requests
        if (request()->expectsJson()) {
            return response()->json($sto);
        }
        
        return view('sto.show', compact('sto'));
    }

    public function edit(Sto $sto)
    {
        $ppas = Ppa::all();
        $indicators = Indicator::all();
        $universes = StoUniverse::all();
        $accomplishments = StoAccomplishment::all();
        $targets = StoTarget::all();
        
        return view('sto.edit', compact('sto', 'ppas', 'indicators', 'universes', 'accomplishments', 'targets'));
    }

    public function update(Request $request, Sto $sto)
    {
        $request->validate([
            'ppa_id' => 'nullable|exists:ppa,id',
            'indicator_id' => 'nullable|exists:indicators,id',
            'universe_id' => 'nullable|exists:sto_universe,id',
            'accomplishment_id' => 'nullable|exists:sto_accomplishments,id',
            'target_id' => 'nullable|exists:sto_target,id',
        ]);

        $sto->update($request->all());

        return redirect()->route('sto.index')
            ->with('success', 'STO record updated successfully.');
    }

    public function destroy($id)
    {
        try {
            // Debug: Log the incoming ID
            \Log::info('STO Delete Request - ID: ' . $id);
            
            // Find STO record by ID
            $sto = Sto::find($id);
            
            if (!$sto) {
                \Log::error('STO Delete Failed - Record not found for ID: ' . $id);
                return response()->json([
                    'success' => false,
                    'message' => 'STO record not found'
                ], 404);
            }
            
            \Log::info('STO Delete - Found record: ' . $sto->id);
            \Log::info('STO Delete - STO basic data: ID=' . $sto->id . ', ppa_id=' . $sto->ppa_id . ', indicator_id=' . $sto->indicator_id);
            
            // Add back cascade deletion step by step
            \Log::info('STO Delete - Starting cascade deletion');
            
            // Delete PPA record and its details first (most important)
            if ($sto->ppa_id) {
                \Log::info('STO Delete - Deleting PPA record: ' . $sto->ppa_id);
                try {
                    $ppa = Ppa::find($sto->ppa_id);
                    if ($ppa) {
                        // Delete PPA details first
                        if ($ppa->ppa_details_id) {
                            \Log::info('STO Delete - Checking PPA details: ' . $ppa->ppa_details_id);
                            $ppaDetails = PpaDetails::find($ppa->ppa_details_id);
                            if ($ppaDetails) {
                                $ppaDetails->delete();
                                \Log::info('STO Delete - PPA details deleted');
                            }
                        }
                        
                        // Delete PPA record
                        $ppa->delete();
                        \Log::info('STO Delete - PPA record deleted');
                    }
                } catch (\Exception $e) {
                    \Log::warning('STO Delete - Could not delete PPA: ' . $e->getMessage());
                }
            }
            
            // Delete indicator record if it exists
            if ($sto->indicator_id) {
                \Log::info('STO Delete - Deleting indicator: ' . $sto->indicator_id);
                try {
                    $indicator = Indicator::find($sto->indicator_id);
                    if ($indicator) {
                        $indicator->delete();
                        \Log::info('STO Delete - Indicator deleted');
                    }
                } catch (\Exception $e) {
                    \Log::warning('STO Delete - Could not delete indicator: ' . $e->getMessage());
                }
            }
            
            // Delete accomplishment records if they exist (JSON array)
            if ($sto->accomplishment_id && is_array($sto->accomplishment_id)) {
                \Log::info('STO Delete - Deleting accomplishment records: ' . json_encode($sto->accomplishment_id));
                try {
                    foreach ($sto->accomplishment_id as $accomplishmentId) {
                        if ($accomplishmentId) {
                            $accomplishment = StoAccomplishment::find($accomplishmentId);
                            if ($accomplishment) {
                                $accomplishment->delete();
                                \Log::info('STO Delete - Accomplishment deleted: ' . $accomplishmentId);
                            }
                        }
                    }
                } catch (\Exception $e) {
                    \Log::warning('STO Delete - Could not delete accomplishment: ' . $e->getMessage());
                }
            }
            
            // Delete target records if they exist (JSON array)
            if ($sto->targets_id && is_array($sto->targets_id)) {
                \Log::info('STO Delete - Deleting target records: ' . json_encode($sto->targets_id));
                try {
                    foreach ($sto->targets_id as $targetId) {
                        if ($targetId) {
                            $target = StoTarget::find($targetId);
                            if ($target) {
                                $target->delete();
                                \Log::info('STO Delete - Target deleted: ' . $targetId);
                            }
                        }
                    }
                } catch (\Exception $e) {
                    \Log::warning('STO Delete - Could not delete target: ' . $e->getMessage());
                }
            }
            
            // Delete universe records if they exist (JSON array)
            if ($sto->universe_id && is_array($sto->universe_id)) {
                \Log::info('STO Delete - Deleting universe records: ' . json_encode($sto->universe_id));
                try {
                    foreach ($sto->universe_id as $universeId) {
                        if ($universeId) {
                            $universe = StoUniverse::find($universeId);
                            if ($universe) {
                                $universe->delete();
                                \Log::info('STO Delete - Universe deleted: ' . $universeId);
                            }
                        }
                    }
                } catch (\Exception $e) {
                    \Log::warning('STO Delete - Could not delete universe: ' . $e->getMessage());
                }
            }
            
            // Finally delete the STO record
            \Log::info('STO Delete - Deleting STO record: ' . $sto->id);
            $sto->delete();
            \Log::info('STO Delete - STO record deleted');
            
            \Log::info('STO Delete - Cascade deletion completed');
            
            return response()->json([
                'success' => true,
                'message' => 'STO record and all related data deleted successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('STO Delete Exception: ' . $e->getMessage());
            \Log::error('STO Delete Exception Trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting STO record: ' . $e->getMessage()
            ], 500);
        }
    }
}
