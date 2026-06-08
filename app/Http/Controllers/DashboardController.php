<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $locations = ['GASS', 'STO', 'ENF', 'BIODIVERSITY', 'LANDS', 'SOILCON', 'NRA'];
        $ubCounts = [];

        foreach ($locations as $location) {
            $modelClass = 'App\\Models\\' . ucfirst(strtolower($location));
            if (class_exists($modelClass)) {
                $ubCounts[$location] = $modelClass::count();
            } else {
                $ubCounts[$location] = 0;
            }
        }

        return view('dashboard.index', compact('ubCounts'));
    }

    public function search(Request $request)
    {
        $keyword = $request->input('keyword');
        $searchLocation = $request->input('search_location');
        $office = $request->input('office');

        // Calculate ubCounts for the dashboard statistics
        $locations = ['GASS', 'STO', 'ENF', 'BIODIVERSITY', 'LANDS', 'SOILCON', 'NRA'];
        $ubCounts = [];

        foreach ($locations as $location) {
            $modelClass = 'App\\Models\\' . ucfirst(strtolower($location));
            if (class_exists($modelClass)) {
                $ubCounts[$location] = $modelClass::count();
            } else {
                $ubCounts[$location] = 0;
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

        return view('dashboard.index', compact('results', 'ubCounts'));
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
