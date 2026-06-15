<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $locations = ['GASS', 'STO', 'ENF', 'BIODIVERSITY', 'LANDS', 'SOILCON', 'NRA'];
        $ubCounts = [];
        $universeTotals = [];
        $baselineTotals = [];
        $accomplishmentTotals = [];
        $accomplishmentWithoutUniverseTotals = [];

        foreach ($locations as $location) {
            $modelClass = 'App\\Models\\' . ucfirst(strtolower($location));
            if (class_exists($modelClass)) {
                $ubCounts[$location] = $modelClass::count();

                // Calculate total universe, total baselines, and total accomplishments
                $records = $modelClass::all();
                $totalUniverse = 0;
                $totalBaseline = 0;
                $totalAccomplishment = 0;
                $totalAccomplishmentWithoutUniverse = 0;

                foreach ($records as $record) {
                    $universeData = $record->universe;
                    $accomplishmentData = $record->accomplishment;
                    $officeIds = $record->office_id;

                    if (is_string($universeData)) {
                        $universeData = json_decode($universeData, true);
                    }
                    if (is_string($accomplishmentData)) {
                        $accomplishmentData = json_decode($accomplishmentData, true);
                    }
                    if (is_string($officeIds)) {
                        $officeIds = json_decode($officeIds, true);
                    }

                    // Sum all universe values for this record
                    $recordUniverse = 0;
                    if (is_array($universeData)) {
                        foreach ($universeData as $universeValue) {
                            if (is_numeric($universeValue) && $universeValue > 0) {
                                $recordUniverse += (float)$universeValue;
                            }
                        }
                    }

                    // Sum accomplishments only for years 2022-2026 for this record
                    $recordAccomplishment = 0;
                    if (is_array($accomplishmentData) && is_array($officeIds)) {
                        foreach ($officeIds as $officeId) {
                            if (isset($accomplishmentData[$officeId]) && is_array($accomplishmentData[$officeId])) {
                                foreach ($accomplishmentData[$officeId] as $year => $accomplishmentValue) {
                                    if ($year && is_numeric($year) && $year >= 2022 && $year <= 2026 && is_numeric($accomplishmentValue)) {
                                        $recordAccomplishment += (float)$accomplishmentValue;
                                    }
                                }
                            }
                        }
                    }

                    // Calculate baseline for this record
                    $recordBaseline = max(0, $recordUniverse - $recordAccomplishment);

                    // Track accomplishments without universe
                    if ($recordAccomplishment > 0 && $recordUniverse === 0) {
                        $totalAccomplishmentWithoutUniverse += $recordAccomplishment;
                    }

                    // Add to totals
                    $totalUniverse += $recordUniverse;
                    $totalBaseline += $recordBaseline;
                    $totalAccomplishment += $recordAccomplishment;
                }

                $universeTotals[$location] = $totalUniverse;
                $baselineTotals[$location] = $totalBaseline;
                $accomplishmentTotals[$location] = $totalAccomplishment;
                $accomplishmentWithoutUniverseTotals[$location] = $totalAccomplishmentWithoutUniverse;
            } else {
                $ubCounts[$location] = 0;
                $universeTotals[$location] = 0;
                $baselineTotals[$location] = 0;
                $accomplishmentTotals[$location] = 0;
                $accomplishmentWithoutUniverseTotals[$location] = 0;
            }
        }

        // Calculate total across all sectors
        $totalAccomplishmentWithoutUniverse = array_sum($accomplishmentWithoutUniverseTotals);

        return view('dashboard.index', compact('ubCounts', 'universeTotals', 'baselineTotals', 'accomplishmentTotals', 'accomplishmentWithoutUniverseTotals', 'totalAccomplishmentWithoutUniverse'));
    }

    public function showSector($sector)
    {
        // Validate sector
        $validSectors = ['GASS', 'STO', 'ENF', 'BIODIVERSITY', 'LANDS', 'SOILCON', 'NRA'];
        if (!in_array(strtoupper($sector), $validSectors)) {
            return redirect()->route('dashboard')->with('error', 'Invalid sector');
        }

        $sector = strtoupper($sector);
        $selectedSector = $sector;
        $modelClass = 'App\\Models\\' . ucfirst(strtolower($sector));

        if (!class_exists($modelClass)) {
            return redirect()->route('dashboard')->with('error', 'Sector model not found');
        }

        // Get records for the sector
        $records = $modelClass::with(['ppa', 'indicator'])->get();
        
        $sectorRecords = collect();

        foreach ($records as $record) {
            $universeData = $record->universe;
            $accomplishmentData = $record->accomplishment;
            $officeIds = $record->office_id;

            if (is_string($universeData)) {
                $universeData = json_decode($universeData, true);
            }
            if (is_string($accomplishmentData)) {
                $accomplishmentData = json_decode($accomplishmentData, true);
            }
            if (is_string($officeIds)) {
                $officeIds = json_decode($officeIds, true);
            }

            // Calculate universe and baseline for this record
            $recordUniverse = 0;
            $recordAccomplishment = 0;

            // Sum all universe values (regardless of structure)
            if (is_array($universeData)) {
                foreach ($universeData as $universeValue) {
                    if (is_numeric($universeValue) && $universeValue > 0) {
                        $recordUniverse += (float)$universeValue;
                    }
                }
            }
            
            // Sum accomplishments only for years 2022-2026
            if (is_array($accomplishmentData) && is_array($officeIds)) {
                foreach ($officeIds as $officeId) {
                    if (isset($accomplishmentData[$officeId]) && is_array($accomplishmentData[$officeId])) {
                        foreach ($accomplishmentData[$officeId] as $year => $accomplishmentValue) {
                            if ($year && is_numeric($year) && $year >= 2022 && $year <= 2026 && is_numeric($accomplishmentValue)) {
                                $recordAccomplishment += (float)$accomplishmentValue;
                            }
                        }
                    }
                }
            }

            $recordBaseline = max(0, $recordUniverse - $recordAccomplishment);

            // Only include records that have universe
            if ($recordUniverse > 0) {
                $sectorRecords->push((object)[
                    'id' => $record->id,
                    'ppa_name' => $record->ppa->name ?? '-',
                    'indicator' => $record->indicator->name ?? '-',
                    'sector' => $sector,
                    'universe' => $recordUniverse,
                    'baseline' => $recordBaseline,
                ]);
            }
        }

        // Also calculate dashboard statistics
        $locations = ['GASS', 'STO', 'ENF', 'BIODIVERSITY', 'LANDS', 'SOILCON', 'NRA'];
        $ubCounts = [];
        $universeTotals = [];
        $baselineTotals = [];
        $accomplishmentTotals = [];

        foreach ($locations as $location) {
            $modelClass = 'App\\Models\\' . ucfirst(strtolower($location));
            if (class_exists($modelClass)) {
                $ubCounts[$location] = $modelClass::count();
                
                // Calculate total universe, total baselines, and total accomplishments
                $records = $modelClass::all();
                $totalUniverse = 0;
                $totalBaseline = 0;
                $totalAccomplishment = 0;
                
                foreach ($records as $record) {
                    $universeData = $record->universe;
                    $accomplishmentData = $record->accomplishment;
                    $officeIds = $record->office_id;
                    
                    if (is_string($universeData)) {
                        $universeData = json_decode($universeData, true);
                    }
                    if (is_string($accomplishmentData)) {
                        $accomplishmentData = json_decode($accomplishmentData, true);
                    }
                    if (is_string($officeIds)) {
                        $officeIds = json_decode($officeIds, true);
                    }
                    
                    // Sum all universe values for this record
                    $recordUniverse = 0;
                    if (is_array($universeData)) {
                        foreach ($universeData as $universeValue) {
                            if (is_numeric($universeValue) && $universeValue > 0) {
                                $recordUniverse += (float)$universeValue;
                            }
                        }
                    }
                    
                    // Sum accomplishments only for years 2022-2026 for this record
                    $recordAccomplishment = 0;
                    if (is_array($accomplishmentData) && is_array($officeIds)) {
                        foreach ($officeIds as $officeId) {
                            if (isset($accomplishmentData[$officeId]) && is_array($accomplishmentData[$officeId])) {
                                foreach ($accomplishmentData[$officeId] as $year => $accomplishmentValue) {
                                    if ($year && is_numeric($year) && $year >= 2022 && $year <= 2026 && is_numeric($accomplishmentValue)) {
                                        $recordAccomplishment += (float)$accomplishmentValue;
                                    }
                                }
                            }
                        }
                    }
                    
                    // Calculate baseline for this record
                    $recordBaseline = max(0, $recordUniverse - $recordAccomplishment);
                    
                    // Add to totals
                    $totalUniverse += $recordUniverse;
                    $totalBaseline += $recordBaseline;
                    $totalAccomplishment += $recordAccomplishment;
                }
                
                $universeTotals[$location] = $totalUniverse;
                $baselineTotals[$location] = $totalBaseline;
                $accomplishmentTotals[$location] = $totalAccomplishment;
            } else {
                $ubCounts[$location] = 0;
                $universeTotals[$location] = 0;
                $baselineTotals[$location] = 0;
                $accomplishmentTotals[$location] = 0;
            }
        }

        return view('dashboard.index', compact('sectorRecords', 'selectedSector', 'ubCounts', 'universeTotals', 'baselineTotals', 'accomplishmentTotals'));
    }

    public function search(Request $request)
    {
        $keyword = $request->input('keyword');
        $searchLocation = $request->input('search_location');
        $office = $request->input('office');

        // Calculate ubCounts, universe totals, and baseline totals for the dashboard statistics
        $locations = ['GASS', 'STO', 'ENF', 'BIODIVERSITY', 'LANDS', 'SOILCON', 'NRA'];
        $ubCounts = [];
        $universeTotals = [];
        $baselineTotals = [];

        foreach ($locations as $location) {
            $modelClass = 'App\\Models\\' . ucfirst(strtolower($location));
            if (class_exists($modelClass)) {
                $ubCounts[$location] = $modelClass::count();
                
                // Calculate total universe and total accomplishments (only those with universe)
                $records = $modelClass::all();
                $totalUniverse = 0;
                $totalAccomplishment = 0;
                
                foreach ($records as $record) {
                    $universeData = $record->universe;
                    $accomplishmentData = $record->accomplishment;
                    
                    if (is_string($universeData)) {
                        $universeData = json_decode($universeData, true);
                    }
                    if (is_string($accomplishmentData)) {
                        $accomplishmentData = json_decode($accomplishmentData, true);
                    }
                    
                    // Both universe and accomplishments are keyed by office_id
                    if (is_array($universeData) && is_array($accomplishmentData)) {
                        foreach ($universeData as $officeId => $universeValue) {
                            // Skip if universe is 0 or null
                            if (!is_numeric($universeValue) || $universeValue == 0) {
                                continue;
                            }
                            
                            $totalUniverse += (float)$universeValue;
                            
                            // Get accomplishment for this office_id
                            $accomplishment = $accomplishmentData[$officeId] ?? null;
                            
                            // Sum accomplishments that have a universe
                            if (is_numeric($accomplishment)) {
                                $totalAccomplishment += (float)$accomplishment;
                            }
                        }
                    }
                }
                
                // Baseline = total universe - total accomplishments
                $totalBaseline = max(0, $totalUniverse - $totalAccomplishment);
                
                $universeTotals[$location] = $totalUniverse;
                $baselineTotals[$location] = $totalBaseline;
            } else {
                $ubCounts[$location] = 0;
                $universeTotals[$location] = 0;
                $baselineTotals[$location] = 0;
            }
        }

        $results = collect();

        // If a specific location is selected (and not ALL), only search that location
        if ($searchLocation && $searchLocation !== 'ALL') {
            $locations = [$searchLocation];
        }

        foreach ($locations as $location) {
            $modelClass = 'App\\Models\\' . ucfirst(strtolower($location));

            if (class_exists($modelClass)) {
                $query = $modelClass::with(['ppa', 'indicator']);

                // Filter by keyword (PPA name or indicator)
                if ($keyword && trim($keyword) !== '') {
                    $query->whereHas('ppa', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    })->orWhereHas('indicator', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                }

                // Filter by office only if a specific office is selected (and not ALL)
                // If no office selected or ALL is selected, all offices will be displayed
                if ($office && $office !== 'ALL') {
                    // Get the office ID from the office name
                    $officeModel = \App\Models\Office::where('name', $office)->first();
                    if ($officeModel) {
                        $query->whereHas('ppa', function ($q) use ($officeModel) {
                            $q->whereJsonContains('office_id', $officeModel->id);
                        });
                    }
                }

                $locationResults = $query->get()->flatMap(function ($item) use ($location, $office) {
                    // Skip if no PPA relationship
                    if (!$item->ppa) {
                        return collect();
                    }

                    // Get office names from office_id array
                    $officeIds = is_array($item->ppa->office_id) ? $item->ppa->office_id : [$item->ppa->office_id];
                    $offices = \App\Models\Office::whereIn('id', $officeIds)->pluck('name')->toArray();

                    // Decode JSON data if they're strings
                    $universeData = $item->universe;
                    if (is_string($universeData)) {
                        $universeData = json_decode($universeData, true);
                    }

                    $baselineData = $item->targets;
                    if (is_string($baselineData)) {
                        $baselineData = json_decode($baselineData, true);
                    }

                    $accomplishmentData = $item->accomplishment;
                    if (is_string($accomplishmentData)) {
                        $accomplishmentData = json_decode($accomplishmentData, true);
                    }

                    // Create one row per office (GASS approach)
                    $rows = collect();

                    foreach ($offices as $index => $officeName) {
                        // If office filter is set (and not ALL), only show that office
                        if ($office && $office !== 'ALL' && $officeName !== $office) {
                            continue;
                        }

                        $officeId = $officeIds[$index] ?? null;

                        // Get universe value for this office
                        $universeValue = '-';
                        if (is_array($universeData) && isset($universeData[$officeId])) {
                            $universeValue = $universeData[$officeId];
                        } elseif (is_array($universeData)) {
                            $keys = array_keys($universeData);
                            $universeValue = $universeData[$keys[$index] ?? 0] ?? '-';
                        }

                        // Get baseline value for this office
                        $baselineValue = '-';
                        if (is_array($baselineData) && isset($baselineData[$officeId])) {
                            $baselineValue = $baselineData[$officeId];
                        } elseif (is_array($baselineData)) {
                            $keys = array_keys($baselineData);
                            $baselineValue = $baselineData[$keys[$index] ?? 0] ?? '-';
                        }

                        // Structure accomplishments by year
                        $accomplishmentsByYear = [];
                        $years = ['2022', '2023', '2024', '2025', '2026', '2027', '2028'];

                        // Get the accomplishment data for this office
                        $officeAccomplishments = null;
                        if (is_array($accomplishmentData) && isset($accomplishmentData[$officeId])) {
                            $officeAccomplishments = $accomplishmentData[$officeId];
                        } elseif (is_array($accomplishmentData)) {
                            $keys = array_keys($accomplishmentData);
                            $officeAccomplishments = $accomplishmentData[$keys[$index] ?? 0] ?? null;
                        }

                        // Extract year values
                        if (is_array($officeAccomplishments)) {
                            foreach ($years as $year) {
                                $accomplishmentsByYear[$year] = $officeAccomplishments[$year] ?? '-';
                            }
                        } else {
                            foreach ($years as $year) {
                                $accomplishmentsByYear[$year] = '-';
                            }
                        }

                        $rows->push((object)[
                            'ppa_name' => $item->ppa->name ?? '-',
                            'indicator' => $item->indicator->name ?? '-',
                            'location' => $location,
                            'office' => $officeName,
                            'universe' => $this->formatArrayField($universeValue),
                            'baseline' => $this->formatArrayField($baselineValue),
                            'accomplishments' => $accomplishmentsByYear,
                            'id' => $item->id,
                        ]);
                    }

                    return $rows;
                });

                $results = $results->merge($locationResults);
            }
        }

        return view('dashboard.index', compact('results', 'ubCounts', 'universeTotals', 'baselineTotals'));
    }

    private function formatArrayField($value)
    {
        if ($value === null || $value === '') {
            return '-';
        }

        if (is_array($value)) {
            // Convert all array elements to strings and implode with line breaks
            $stringValues = array_map(function($item) {
                if (is_array($item) || is_object($item)) {
                    return json_encode($item);
                }
                return (string)$item;
            }, $value);

            return implode('<br>', $stringValues);
        }

        return (string)$value;
    }
}
