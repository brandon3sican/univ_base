@extends('layouts.app')

@php
    $pageTitle = 'STO';
    
    // Initialize global counters at the start of the file
    $GLOBALS['programCounter'] = 1;
    $GLOBALS['projectCounter'] = 1;
    $GLOBALS['mainCounter'] = 1;
    $GLOBALS['subCounter'] = 1;
    $GLOBALS['subSubCounter'] = 1;
@endphp

@section('content')
    <!-- Data Table Container -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <!-- Table Header with Summary Statistics -->
        <div class="bg-gradient-to-r from-slate-700 to-slate-800 text-white p-6 shadow-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-table mr-2"></i>
                    <h2 class="text-lg font-bold">STO Data Table</h2>
                </div>
                <div class="flex items-center space-x-3">
                    <!-- Toggle Target Columns Button -->
                    <button onclick="toggleTargetColumns()" id="toggleTargetBtn"
                        class="bg-white hover:bg-gray-100 text-slate-700 px-4 py-2 rounded-lg transition-all duration-200 flex items-center text-sm font-medium shadow hover:shadow-lg">
                        <i class="fas fa-bullseye mr-2"></i>
                        <span id="toggleTargetText">Show Targets</span>
                    </button>
                    <!-- Toggle Remarks Column Button -->
                    <button onclick="toggleRemarksColumn()" id="toggleRemarksBtn"
                        class="bg-white hover:bg-gray-100 text-slate-700 px-4 py-2 rounded-lg transition-all duration-200 flex items-center text-sm font-medium shadow hover:shadow-lg">
                        <i class="fas fa-comment mr-2"></i>
                        <span id="toggleRemarksText">Show Remarks</span>
                    </button>
                    <!-- Toggle Actions Column Button -->
                    <button onclick="toggleActionsColumn()" id="toggleActionsBtn"
                        class="bg-white hover:bg-gray-100 text-slate-700 px-4 py-2 rounded-lg transition-all duration-200 flex items-center text-sm font-medium shadow hover:shadow-lg">
                        <i class="fas fa-eye mr-2"></i>
                        <span id="toggleActionsText">Show Actions</span>
                    </button>
                    <!-- Add New Record Button -->
                    <button onclick="openCreateModal()"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-all duration-200 text-sm font-medium shadow hover:shadow-lg flex items-center">
                        <i class="fas fa-plus mr-2"></i>
                        Add New Record
                    </button>
                </div>
            </div>
        </div>

        <!-- Table Container -->
        <div class="overflow-x-auto" style="max-height: 70vh; overflow-y: auto;">
            <table class="w-full border-collapse border-2 border-gray-400 shadow-lg rounded-lg overflow-hidden">
                <!-- Table Head -->
                <thead class="sticky top-0 z-10 bg-gradient-to-r from-gray-50 to-gray-100 text-gray-700 shadow-md border-b border-gray-300">
                    <!-- First row: main headers -->
                    <tr class="border-b border-gray-300">
                        <th rowspan="2"
                            class="px-6 py-2 text-center text-sm font-bold uppercase tracking-wider border-r border-gray-300 bg-gradient-to-b from-gray-100 to-gray-200">
                            <div class="flex items-center justify-center">
                                <i class="fas fa-list-ul mr-2 text-gray-500"></i>
                                PPA & Output Indicators
                            </div>
                        </th>
                        <th rowspan="2"
                            class="px-6 py-2 text-center text-sm font-bold uppercase tracking-wider border-r border-gray-300 bg-gradient-to-b from-gray-100 to-gray-200">
                            <div class="flex items-center justify-center">
                                <i class="fas fa-building mr-2 text-gray-500"></i>
                                OFFICE
                            </div>
                        </th>
                        <th rowspan="2"
                            class="px-6 py-2 text-center text-sm font-bold uppercase tracking-wider border-r border-gray-300 bg-gradient-to-b from-gray-100 to-gray-200">
                            <div class="flex items-center justify-center">
                                <i class="fas fa-globe mr-2 text-gray-500"></i>
                                UNIVERSE
                            </div>
                        </th>
                        <th rowspan="2"
                            class="px-6 py-2 text-center text-sm font-bold uppercase tracking-wider border-r border-gray-300 bg-gradient-to-b from-gray-100 to-gray-200">
                            <div class="flex items-center justify-center">
                                <i class="fas fa-chart-line mr-2 text-gray-500"></i>
                                BASELINE
                            </div>
                        </th>
                        <th colspan="2"
                            class="px-6 py-2 text-center text-sm font-bold uppercase tracking-wider border-r border-gray-300 bg-gradient-to-b from-gray-100 to-gray-200">
                            <div class="flex items-center justify-center">
                                <i class="fas fa-tasks mr-2 text-gray-500"></i>
                                ACCOMPLISHMENT
                            </div>
                        </th>

                        @php
                            $currentYear = date('Y'); // 2026
                            $targetYears = [];
                            
                            // Determine the current period and calculate remaining years
                            if ($currentYear >= 2022 && $currentYear < 2028) {
                                // 2022-2028 period
                                $periodEnd = 2028;
                            } elseif ($currentYear >= 2028 && $currentYear < 2034) {
                                // 2028-2034 period  
                                $periodEnd = 2034;
                            } elseif ($currentYear >= 2034 && $currentYear <= 2040) {
                                // 2034-2040 period
                                $periodEnd = 2040;
                            } else {
                                $periodEnd = $currentYear; // Fallback
                            }
                            
                            // Generate remaining years in current period
                            for ($year = $currentYear + 1; $year <= $periodEnd; $year++) {
                                $targetYears[] = $year;
                            }
                        @endphp
                        
                        <!-- This is the new parent column for target years -->
                        <th colspan="{{ count($targetYears) }}"
                            class="px-6 py-4 text-center text-sm font-bold uppercase tracking-wider border-r border-gray-300 bg-gradient-to-b from-gray-100 to-gray-200 hidden"
                            id="targetHeader">
                            <div class="flex items-center justify-center">
                                <i class="fas fa-bullseye mr-2 text-gray-500"></i>
                                TARGET
                            </div>
                        </th>

                        <th rowspan="2"
                            class="px-6 py-4 text-center text-sm font-bold uppercase tracking-wider border-r border-gray-300 bg-gradient-to-b from-gray-100 to-gray-200 hidden"
                            id="remarksHeader">
                            <div class="flex items-center justify-center">
                                <i class="fas fa-comment mr-2 text-gray-500"></i>
                                REMARKS
                            </div>
                        </th>
                        <th rowspan="2"
                            class="px-6 py-4 text-center text-sm font-bold uppercase tracking-wider border-r border-gray-300 bg-gradient-to-b from-gray-100 to-gray-200 hidden"
                            id="actionsHeader">
                            <div class="flex items-center justify-center">
                                <i class="fas fa-cogs mr-2 text-gray-500"></i>
                                ACTIONS
                            </div>
                        </th>
                    </tr>

                    <!-- Second row: year sub-headers -->
                    <tr class="border-b border-gray-300">
                        <!-- Accomplishment sub-headers -->
                        <th
                            class="px-3 py-3 text-center text-sm font-bold uppercase tracking-wider border-r border-gray-300 bg-gradient-to-b from-gray-100 to-gray-200">
                            Past Years (2022-{{ date('Y') - 1 }})
                        </th>
                        <th
                            class="px-3 py-3 text-center text-sm font-bold uppercase tracking-wider border-r border-gray-300 bg-gradient-to-b from-gray-100 to-gray-200">
                            Current Year ({{ date('Y') }})
                        </th>
                        <!-- Target year sub-headers - use the $targetYears already calculated above -->
                        @if(count($targetYears) > 0)
                            @foreach($targetYears as $year)
                                <th
                                    class="px-3 py-3 text-center text-sm font-bold uppercase tracking-wider border-r border-gray-300 bg-gradient-to-b from-gray-100 to-gray-200 hidden">
                                    {{ $year }}
                                </th>
                            @endforeach
                        @else
                            <th
                                class="px-3 py-3 text-center text-sm font-bold uppercase tracking-wider border-r border-gray-300 bg-gradient-to-b from-gray-100 to-gray-200 hidden">
                                No Targets
                            </th>
                        @endif
                    </tr>
                </thead>

                <!-- Table Body -->
                <tbody class="bg-gradient-to-b from-gray-50 to-gray-100 divide-y divide-gray-300" id="sortableTableBody">
                    @php
                        $previousPpaName = null;
                    @endphp
                    @forelse ($stos as $sto)
                        @if(!$sto->indicator)
                            @php
                                $currentPpaName = $sto->ppa?->name ?? 'N/A';
                                $shouldDisplayPpaName = $previousPpaName !== $currentPpaName;
                                if ($shouldDisplayPpaName) {
                                    $previousPpaName = $currentPpaName;
                                }
                            @endphp
                            <!-- PPA row without indicator - merge columns from PPA to Remarks -->
                            <tr class="hover:bg-gray-100 transition-colors duration-150" data-merged-row>
                                <!-- PPA & Output Indicators (merged) -->
                                <td colspan="13" class="px-6 py-2">
                                    <div class="text-sm">
                                        @if($shouldDisplayPpaName)
                                            @php
                                                // Determine numbering based on record type for merged row
                                                $ppaNumbering = '';
                                                if($sto->ppa) {
                                                    $recordType = $sto->ppa->record_type_id ?? null;
                                                    
                                                    // Initialize counters if not set
                                                    if(!isset($GLOBALS['programCounter'])) $GLOBALS['programCounter'] = 1;
                                                    if(!isset($GLOBALS['projectCounter'])) $GLOBALS['projectCounter'] = 1;
                                                    
                                                    if($recordType == 1) {
                                                        // Record Type 1: PROGRAM - Roman numerals
                                                        $romanNumerals = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X'];
                                                        $ppaNumbering = $romanNumerals[$GLOBALS['programCounter'] - 1] . '. ';
                                                        $GLOBALS['programCounter']++;
                                                    } elseif($recordType == 2) {
                                                        // Record Type 2: PROJECT - Letters
                                                        $ppaNumbering = chr(65 + $GLOBALS['projectCounter'] - 1) . '. '; // A, B, C, etc.
                                                        $GLOBALS['projectCounter']++;
                                                        // Reset main, sub, and sub-sub counters for new project
                                                        $GLOBALS['mainCounter'] = 1;
                                                        $GLOBALS['subCounter'] = 1;
                                                        $GLOBALS['subSubCounter'] = 1;
                                                    } elseif($recordType == 3) {
                                                        // Record Type 3: MAIN ACTIVITY - Numbers
                                                        $ppaNumbering = $GLOBALS['mainCounter'] . '. ';
                                                        $GLOBALS['mainCounter']++;
                                                    } elseif($recordType == 4) {
                                                        // Record Type 4: SUB ACTIVITY - Decimal
                                                        $ppaNumbering = ($GLOBALS['mainCounter'] - 1) . '.' . $GLOBALS['subCounter'] . '. ';
                                                        $GLOBALS['subCounter']++;
                                                    } elseif($recordType == 5) {
                                                        // Record Type 5: SUB-SUB ACTIVITY - Double decimal
                                                        $ppaNumbering = ($GLOBALS['mainCounter'] - 1) . '.' . ($GLOBALS['subCounter'] - 1) . '.' . $GLOBALS['subSubCounter'] . '. ';
                                                        $GLOBALS['subSubCounter']++;
                                                    } else {
                                                        // Default fallback
                                                        if(!isset($GLOBALS['defaultCounter'])) $GLOBALS['defaultCounter'] = 1;
                                                        $ppaNumbering = $GLOBALS['defaultCounter'] . '. ';
                                                        $GLOBALS['defaultCounter']++;
                                                    }
                                                } else {
                                                    // No PPA, use default
                                                    $ppaNumbering = '1. ';
                                                }
                                            @endphp
                                            @php
                                                    // Add indentation based on record type for hierarchy
                                                    $indentation = '';
                                                    if($recordType == 1) {
                                                        $indentation = ''; // PROGRAM - no indentation
                                                    } elseif($recordType == 2) {
                                                        $indentation = '&nbsp;&nbsp;&nbsp;&nbsp;'; // PROJECT - 4 spaces
                                                    } elseif($recordType == 3) {
                                                        $indentation = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'; // MAIN ACTIVITY - 8 spaces
                                                    } elseif($recordType == 4) {
                                                        $indentation = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'; // SUB ACTIVITY - 12 spaces
                                                    } elseif($recordType == 5) {
                                                        $indentation = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'; // SUB-SUB ACTIVITY - 16 spaces
                                                    } else {
                                                        $indentation = ''; // Default - no indentation
                                                    }
                                                @endphp
                                                @php
                                                    // Add color based on record type
                                                    $textColor = '';
                                                    if($recordType == 1) {
                                                        $textColor = '#14423f'; // PROGRAM - dark green
                                                    } elseif($recordType == 2) {
                                                        $textColor = '#306b40'; // PROJECT - medium green
                                                    } elseif($recordType == 3) {
                                                        $textColor = '#66a558'; // MAIN ACTIVITY - light green
                                                    } elseif($recordType == 4) {
                                                        $textColor = '#5c463e'; // SUB-ACTIVITY - brown
                                                    } elseif($recordType == 5) {
                                                        $textColor = '#3a272b'; // SUB-SUB-ACTIVITY - dark brown
                                                    } else {
                                                        $textColor = '#111827'; // Default - gray
                                                    }
                                                @endphp
                                                <div class="font-medium" style="color: {!! $textColor !!}">{!! $indentation . '<strong>' . $ppaNumbering . '</strong>' . $currentPpaName !!}</div>
                                        @endif
                                    </div>
                                </td>
                                
                                <!-- Actions -->
                                <td class="px-3 py-2 border-r border-gray-300 hidden">
                                    <div class="flex items-center space-x-2">
                                        <button onclick="editSto({{ $sto->id }})" 
                                            class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deleteSto({{ $sto->id }})" 
                                            class="text-red-600 hover:text-red-900 text-sm font-medium">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @else
                            @php
                                $currentPpaNameWithIndicator = $sto->ppa?->name ?? 'N/A';
                                $shouldDisplayPpaNameWithIndicator = $previousPpaName !== $currentPpaNameWithIndicator;
                                if ($shouldDisplayPpaNameWithIndicator) {
                                    $previousPpaName = $currentPpaNameWithIndicator;
                                }
                            @endphp
                            <!-- Regular row with indicator -->
                            <tr class="hover:bg-gray-100 transition-colors duration-150">
                                <!-- PPA & Output Indicators -->
                                <td class="px-6 py-2 border-r border-gray-300">
                                    <div class="text-sm">
                                        @if($shouldDisplayPpaNameWithIndicator)
                                            @php
                                                // Determine numbering based on record type
                                                $ppaNumbering = '';
                                                if($sto->ppa) {
                                                    $recordType = $sto->ppa->record_type_id ?? null;
                                                    
                                                    // Initialize counters if not set
                                                    if(!isset($GLOBALS['programCounter'])) $GLOBALS['programCounter'] = 1;
                                                    if(!isset($GLOBALS['projectCounter'])) $GLOBALS['projectCounter'] = 1;
                                                    
                                                    if($recordType == 1) {
                                                        // Record Type 1: PROGRAM - Roman numerals
                                                        $romanNumerals = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X'];
                                                        $ppaNumbering = $romanNumerals[$GLOBALS['programCounter'] - 1] . '. ';
                                                        $GLOBALS['programCounter']++;
                                                    } elseif($recordType == 2) {
                                                        // Record Type 2: PROJECT - Letters
                                                        $ppaNumbering = chr(65 + $GLOBALS['projectCounter'] - 1) . '. '; // A, B, C, etc.
                                                        $GLOBALS['projectCounter']++;
                                                        // Reset main, sub, and sub-sub counters for new project
                                                        $GLOBALS['mainCounter'] = 1;
                                                        $GLOBALS['subCounter'] = 1;
                                                        $GLOBALS['subSubCounter'] = 1;
                                                    } elseif($recordType == 3) {
                                                        // Record Type 3: MAIN ACTIVITY - Numbers
                                                        $ppaNumbering = $GLOBALS['mainCounter'] . '. ';
                                                        $GLOBALS['mainCounter']++;
                                                    } elseif($recordType == 4) {
                                                        // Record Type 4: SUB ACTIVITY - Decimal
                                                        $ppaNumbering = ($GLOBALS['mainCounter'] - 1) . '.' . $GLOBALS['subCounter'] . '. ';
                                                        $GLOBALS['subCounter']++;
                                                    } elseif($recordType == 5) {
                                                        // Record Type 5: SUB-SUB ACTIVITY - Double decimal
                                                        $ppaNumbering = ($GLOBALS['mainCounter'] - 1) . '.' . ($GLOBALS['subCounter'] - 1) . '.' . $GLOBALS['subSubCounter'] . '. ';
                                                        $GLOBALS['subSubCounter']++;
                                                    } else {
                                                        // Default fallback
                                                        if(!isset($GLOBALS['defaultCounter'])) $GLOBALS['defaultCounter'] = 1;
                                                        $ppaNumbering = $GLOBALS['defaultCounter'] . '. ';
                                                        $GLOBALS['defaultCounter']++;
                                                    }
                                                } else {
                                                    // No PPA, use default
                                                    $ppaNumbering = '1. ';
                                                }
                                            @endphp
                                            @php
                                                    // Add indentation based on record type for hierarchy
                                                    $indentation = '';
                                                    if($recordType == 1) {
                                                        $indentation = ''; // PROGRAM - no indentation
                                                    } elseif($recordType == 2) {
                                                        $indentation = '&nbsp;&nbsp;&nbsp;&nbsp;'; // PROJECT - 4 spaces
                                                    } elseif($recordType == 3) {
                                                        $indentation = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'; // MAIN ACTIVITY - 8 spaces
                                                    } elseif($recordType == 4) {
                                                        $indentation = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'; // SUB ACTIVITY - 12 spaces
                                                    } elseif($recordType == 5) {
                                                        $indentation = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'; // SUB-SUB ACTIVITY - 16 spaces
                                                    } else {
                                                        $indentation = ''; // Default - no indentation
                                                    }
                                                @endphp
                                                @php
                                                    // Add color based on record type
                                                    $textColor = '';
                                                    if($recordType == 1) {
                                                        $textColor = '#14423f'; // PROGRAM - dark green
                                                    } elseif($recordType == 2) {
                                                        $textColor = '#306b40'; // PROJECT - medium green
                                                    } elseif($recordType == 3) {
                                                        $textColor = '#66a558'; // MAIN ACTIVITY - light green
                                                    } elseif($recordType == 4) {
                                                        $textColor = '#5c463e'; // SUB-ACTIVITY - brown
                                                    } elseif($recordType == 5) {
                                                        $textColor = '#3a272b'; // SUB-SUB-ACTIVITY - dark brown
                                                    } else {
                                                        $textColor = '#111827'; // Default - gray
                                                    }
                                                @endphp
                                                <div class="font-medium" style="color: {!! $textColor !!}">{!! $indentation . '<strong>' . $ppaNumbering . '</strong>' . $currentPpaNameWithIndicator !!}</div>
                                        @endif
                                        @if($sto->indicator)
                                            @php
                                                // Add icon based on record type
                                                $indicatorIcon = '';
                                                if($recordType == 1) {
                                                    $indicatorIcon = '<i class="fas fa-bullseye mr-1 text-blue-600"></i>'; // PROGRAM - bullseye/target
                                                } elseif($recordType == 2) {
                                                    $indicatorIcon = '<i class="fas fa-rocket mr-1 text-blue-600"></i>'; // PROJECT - rocket
                                                } elseif($recordType == 3) {
                                                    $indicatorIcon = '<i class="fas fa-cogs mr-1 text-blue-600"></i>'; // MAIN ACTIVITY - gears
                                                } elseif($recordType == 4) {
                                                    $indicatorIcon = '<i class="fas fa-sitemap mr-1 text-blue-600"></i>'; // SUB ACTIVITY - sitemap
                                                } elseif($recordType == 5) {
                                                    $indicatorIcon = '<i class="fas fa-microchip mr-1 text-blue-600"></i>'; // SUB-SUB-ACTIVITY - microchip
                                                } else {
                                                    $indicatorIcon = '<i class="fas fa-dot-circle mr-1 text-blue-600"></i>'; // Default - dot circle
                                                }
                                            @endphp
                                            <div class="text-xs mt-1 text-blue-600">{!! $indentation . '&nbsp;&nbsp;&nbsp;&nbsp;' . $indicatorIcon . $sto->indicator->name !!}</div>
                                        @endif
                                    </div>
                                </td>
                                
                                <!-- Office -->
                                <td class="px-6 py-2 border-r border-gray-300">
                                    <div class="text-sm text-gray-900">
                                        @php
                                            // Get office IDs from this STO's PPA
                                            $ppaOfficeIds = [];
                                            if($sto->ppa && isset($sto->ppa->office_id)) {
                                                $ppaOfficeIds = $sto->ppa->office_id;
                                            }
                                        @endphp
                                        
                                        <!-- CAR Row -->
                                        <div class="font-bold text-blue-600">CAR</div>
                                        
                                        @foreach($ppaOfficeIds as $officeId)
                                            @php
                                                $office = \App\Models\Office::find($officeId);
                                            @endphp
                                            @if($office)
                                                <div>{{ $office->name }}</div>
                                            @else
                                                <div>Office {{ $officeId }}</div>
                                            @endif
                                        @endforeach
                                        @if(empty($ppaOfficeIds))
                                            <div>-</div>
                                        @endif
                                    </div>
                                </td>
                                
                                <!-- Universe -->
                                <td class="px-6 py-2 border-r border-gray-300">
                                    <div class="text-sm">
                                        @php
                                            // Get office IDs from this STO's PPA
                                            $ppaOfficeIds = [];
                                            if($sto->ppa && isset($sto->ppa->office_id)) {
                                                $ppaOfficeIds = $sto->ppa->office_id;
                                            }
                                            
                                            // Create office universe mapping
                                            $officeUniverseMap = [];
                                            if($sto->universe_id) {
                                                $universeRecords = \App\Models\StoUniverse::whereIn('id', is_array($sto->universe_id) ? $sto->universe_id : [$sto->universe_id])->get();
                                                foreach($universeRecords as $universe) {
                                                    foreach($universe->office_ids ?? [] as $index => $officeId) {
                                                        $officeUniverseMap[$officeId] = $universe->values[$index] ?? null;
                                                    }
                                                }
                                            }
                                            
                                            // Calculate CAR total for universe
                                            $carTotalUniverse = 0;
                                            foreach($ppaOfficeIds as $officeId) {
                                                $universeValue = $officeUniverseMap[$officeId] ?? null;
                                                if($universeValue !== null) {
                                                    $carTotalUniverse += (float)$universeValue;
                                                }
                                            }
                                            
                                            // Display CAR total first
                                            echo '<div class="font-bold text-blue-600">' . $carTotalUniverse . '</div>';
                                            
                                            // Then display individual offices
                                            foreach($ppaOfficeIds as $officeId) {
                                                $universeValue = $officeUniverseMap[$officeId] ?? null;
                                                echo $universeValue !== null && $universeValue !== '' ? $universeValue : '-';
                                                echo '<br>';
                                            }
                                        @endphp
                                    </div>
                                </td>
                                
                                <!-- Baseline -->
                                <td class="px-6 py-2 border-r border-gray-300">
                                    <div class="text-sm">
                                        @php
                                            // Get office IDs from this STO's PPA
                                            $ppaOfficeIds = [];
                                            if($sto->ppa && isset($sto->ppa->office_id)) {
                                                $ppaOfficeIds = $sto->ppa->office_id;
                                            }
                                            
                                            // Calculate CAR total for baseline
                                            $carTotalBaseline = 0;
                                            foreach($ppaOfficeIds as $officeId) {
                                                // Get universe for this office
                                                $universeValue = null;
                                                if($sto->universe_id) {
                                                    $universeRecord = \App\Models\StoUniverse::find($sto->universe_id);
                                                    if($universeRecord) {
                                                        $officeIndex = array_search($officeId, $universeRecord->office_ids ?? []);
                                                        if($officeIndex !== false) {
                                                            $universeValue = $universeRecord->values[$officeIndex] ?? null;
                                                        }
                                                    }
                                                }
                                                
                                                // Get accomplishments for this office (2022-2026)
                                                $totalAccomplishments = 0;
                                                if($sto->accomplishment_id) {
                                                    $accomplishmentRecord = \App\Models\StoAccomplishment::find($sto->accomplishment_id);
                                                    if($accomplishmentRecord) {
                                                        $officeIndex = array_search($officeId, $accomplishmentRecord->office_ids ?? []);
                                                        if($officeIndex !== false) {
                                                            $yearlyData = $accomplishmentRecord->values[$officeIndex] ?? [];
                                                            if(is_array($yearlyData)) {
                                                                foreach($yearlyData as $year => $value) {
                                                                    if($year >= 2022 && $year <= 2026) {
                                                                        $totalAccomplishments += (float)$value;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                                
                                                // Calculate baseline for this office
                                                $baseline = null;
                                                if($universeValue !== null) {
                                                    $baseline = (float)$universeValue - $totalAccomplishments;
                                                    $carTotalBaseline += $baseline;
                                                }
                                            }
                                            
                                            // Display CAR total first
                                            echo '<div class="font-bold text-blue-600">' . $carTotalBaseline . '</div>';
                                            
                                            // Then display individual offices
                                            foreach($ppaOfficeIds as $officeId) {
                                                // Get universe for this office
                                                $universeValue = null;
                                                if($sto->universe_id) {
                                                    $universeRecord = \App\Models\StoUniverse::find($sto->universe_id);
                                                    if($universeRecord) {
                                                        $officeIndex = array_search($officeId, $universeRecord->office_ids ?? []);
                                                        if($officeIndex !== false) {
                                                            $universeValue = $universeRecord->values[$officeIndex] ?? null;
                                                        }
                                                    }
                                                }
                                                
                                                // Get accomplishments for this office (2022-2026)
                                                $totalAccomplishments = 0;
                                                if($sto->accomplishment_id) {
                                                    $accomplishmentRecord = \App\Models\StoAccomplishment::find($sto->accomplishment_id);
                                                    if($accomplishmentRecord) {
                                                        $officeIndex = array_search($officeId, $accomplishmentRecord->office_ids ?? []);
                                                        if($officeIndex !== false) {
                                                            $yearlyData = $accomplishmentRecord->values[$officeIndex] ?? [];
                                                            if(is_array($yearlyData)) {
                                                                foreach($yearlyData as $year => $value) {
                                                                    if($year >= 2022 && $year <= 2026) {
                                                                        $totalAccomplishments += (float)$value;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                                
                                                // Calculate baseline
                                                $baseline = null;
                                                if($universeValue !== null) {
                                                    $baseline = (float)$universeValue - $totalAccomplishments;
                                                }
                                                
                                                echo $baseline !== null ? $baseline : '-';
                                                echo '<br>';
                                            }
                                        @endphp
                                    </div>
                                </td>
                                
                                <!-- Accomplishment - Past Years -->
                                <td class="px-6 py-2 border-r border-gray-300">
                                    <div class="text-sm">
                                        @php
                                            // Load accomplishment records specifically for this STO
                                            $stoAccomplishmentRecords = $sto->accomplishment_id ? \App\Models\StoAccomplishment::whereIn('id', is_array($sto->accomplishment_id) ? $sto->accomplishment_id : [$sto->accomplishment_id])->get() : collect();
                                            
                                            // Get office IDs from this STO's PPA to determine how many offices
                                            $ppaOfficeIds = [];
                                            if($sto->ppa && isset($sto->ppa->office_id)) {
                                                $ppaOfficeIds = $sto->ppa->office_id;
                                            }
                                            
                                            // Create office accomplishment mapping for past years (2022-2025) only
                                            $officeAccomplishmentMap = [];
                                            
                                            foreach($stoAccomplishmentRecords as $accomplishment) {
                                                foreach($accomplishment->office_ids ?? [] as $index => $officeId) {
                                                    // The correct structure: values[office_index] is an array of yearly accomplishments
                                                    $yearlyAccomplishments = $accomplishment->values[$index] ?? [];
                                                    
                                                    if(is_array($yearlyAccomplishments)) {
                                                        // Only include past years (2022-2025), exclude current year (2026)
                                                        foreach($yearlyAccomplishments as $year => $value) {
                                                            if($year >= 2022 && $year <= 2025) {
                                                                if(!isset($officeAccomplishmentMap[$officeId])) {
                                                                    $officeAccomplishmentMap[$officeId] = 0;
                                                                }
                                                                $officeAccomplishmentMap[$officeId] += (float)$value;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        @endphp
                                        
                                        @php
                                            // Calculate CAR total for past years accomplishments
                                            $carTotalPastAccomplishments = 0;
                                            foreach($ppaOfficeIds as $officeId) {
                                                $accomplishmentValue = $officeAccomplishmentMap[$officeId] ?? 0;
                                                $carTotalPastAccomplishments += $accomplishmentValue;
                                            }
                                            
                                            // Display CAR total first
                                            echo '<div class="font-bold text-blue-600">' . $carTotalPastAccomplishments . '</div>';
                                        @endphp
                                        @foreach($ppaOfficeIds as $officeId)
                                            @php
                                                $accomplishmentValue = $officeAccomplishmentMap[$officeId] ?? 0;
                                                
                                                // Get detailed breakdown for tooltip
                                                $yearlyBreakdown = [];
                                                foreach($stoAccomplishmentRecords as $accomplishment) {
                                                    foreach($accomplishment->office_ids ?? [] as $index => $accOfficeId) {
                                                        if($accOfficeId == $officeId) {
                                                            $yearlyAccomplishments = $accomplishment->values[$index] ?? [];
                                                            if(is_array($yearlyAccomplishments)) {
                                                                foreach($yearlyAccomplishments as $year => $value) {
                                                                    if($year >= 2022 && $year <= 2025) {
                                                                        $yearlyBreakdown[$year] = (float)$value;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                                
                                                // Get office name
                                                $office = \App\Models\Office::find($officeId);
                                                $officeName = $office ? $office->name : 'Office ' . $officeId;
                                                
                                                // Build tooltip content
                                                $tooltipContent = $officeName . '<br>';
                                                foreach([2022, 2023, 2024, 2025] as $year) {
                                                    $value = $yearlyBreakdown[$year] ?? 0;
                                                    $tooltipContent .= $year . ': ' . $value . '<br>';
                                                }
                                            @endphp
                                            
                                            <div class="font-medium text-gray-900 relative group cursor-help"
                                                 title="{{ $officeName }} Accomplishments (2022-2025)">
                                                @if($accomplishmentValue > 0)
                                                    {{ number_format($accomplishmentValue) }}
                                                @else
                                                    0
                                                @endif
                                                
                                                <!-- Tooltip -->
                                                <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 bg-gray-800 text-white text-sm rounded-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 whitespace-nowrap z-10">
                                                    <div class="text-xs">{!! $tooltipContent !!}</div>
                                                    <div class="absolute top-full left-1/2 transform -translate-x-1/2 -mt-1">
                                                        <div class="border-4 border-transparent border-t-gray-800"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                        @if(empty($ppaOfficeIds))
                                            <div class="font-medium text-gray-900">0</div>
                                        @endif
                                    </div>
                                </td>
                                
                                <!-- Accomplishment - Current Year -->
                                <td class="px-6 py-2 border-r border-gray-300">
                                    <div class="text-sm">
                                        @php
                                            // Create office accomplishment mapping for current year (2026)
                                            $officeCurrentAccomplishmentMap = [];
                                            foreach($stoAccomplishmentRecords as $accomplishment) {
                                                foreach($accomplishment->office_ids ?? [] as $index => $officeId) {
                                                    // The correct structure: values[office_index] is an array of yearly accomplishments
                                                    $yearlyAccomplishments = $accomplishment->values[$index] ?? [];
                                                    
                                                    if(is_array($yearlyAccomplishments)) {
                                                        // Only include current year (2026)
                                                        $currentYearValue = $yearlyAccomplishments[2026] ?? 0;
                                                        if(!isset($officeCurrentAccomplishmentMap[$officeId])) {
                                                            $officeCurrentAccomplishmentMap[$officeId] = 0;
                                                        }
                                                        $officeCurrentAccomplishmentMap[$officeId] += (float)$currentYearValue;
                                                    }
                                                }
                                            }
                                        @endphp
                                        
                                        @php
                                            // Calculate CAR total for current year accomplishments
                                            $carTotalCurrentAccomplishments = 0;
                                            foreach($ppaOfficeIds as $officeId) {
                                                $accomplishmentValue = $officeCurrentAccomplishmentMap[$officeId] ?? 0;
                                                $carTotalCurrentAccomplishments += $accomplishmentValue;
                                            }
                                            
                                            // Display CAR total first
                                            echo '<div class="font-bold text-blue-600">' . $carTotalCurrentAccomplishments . '</div>';
                                        @endphp
                                        @foreach($ppaOfficeIds as $officeId)
                                            @php
                                                $accomplishmentValue = $officeCurrentAccomplishmentMap[$officeId] ?? 0;
                                            @endphp
                                            @if($accomplishmentValue > 0)
                                                <div class="font-medium text-gray-900">{{ number_format($accomplishmentValue) }}</div>
                                            @else
                                                <div class="font-medium text-gray-900">0</div>
                                            @endif
                                        @endforeach
                                        @if(empty($ppaOfficeIds))
                                            <div class="font-medium text-gray-900">0</div>
                                        @endif
                                    </div>
                                </td>
                                
                                <!-- Targets -->
                                @php
                                    $targetRecords = \App\Models\StoTarget::whereIn('id', $sto->targets_id ?? [])->get();
                                    
                                    // Get office IDs from this STO's PPA
                                    $ppaOfficeIds = [];
                                    if($sto->ppa && isset($sto->ppa->office_id)) {
                                        $ppaOfficeIds = $sto->ppa->office_id;
                                    }
                                    
                                    // Filter target years to only show future years (after current year 2026)
                                    $currentYear = 2026;
                                    $allFutureYears = [];
                                    foreach($targetRecords as $target) {
                                        foreach($target->years ?? [] as $year) {
                                            if($year > $currentYear && !in_array($year, $allFutureYears)) {
                                                $allFutureYears[] = $year;
                                            }
                                        }
                                    }
                                    sort($allFutureYears);
                                @endphp
                                @foreach($allFutureYears as $year)
                                    <td class="border-r border-gray-300 hidden">
                                        <div class="text-sm text-gray-900 text-center">
                                            @php
                                                // Calculate CAR total for this year's targets
                                                $carTotalTarget = 0;
                                                foreach($ppaOfficeIds as $index => $officeId) {
                                                    $targetValue = 0;
                                                    $foundTarget = false;
                                                    
                                                    // Get the target record for this office (by index)
                                                    if(isset($targetRecords[$index])) {
                                                        $target = $targetRecords[$index];
                                                        // Find the year index in this target's years array
                                                        $yearIndex = array_search($year, $target->years ?? []);
                                                        if($yearIndex !== false) {
                                                            $foundTarget = true;
                                                            $targetValue = $target->values[$yearIndex] ?? 0;
                                                            $carTotalTarget += $targetValue;
                                                        }
                                                    }
                                                }
                                                
                                                // Display CAR total first
                                                echo '<div class="font-bold text-blue-600">' . $carTotalTarget . '</div>';
                                            @endphp
                                            
                                            @foreach($ppaOfficeIds as $index => $officeId)
                                                @php
                                                    $targetValue = 0;
                                                    $foundTarget = false;
                                                    
                                                    // Get office name for display
                                                    $office = \App\Models\Office::find($officeId);
                                                    $officeName = $office ? $office->name : 'Office ' . $officeId;
                                                    
                                                    // Get the target record for this office (by index)
                                                    if(isset($targetRecords[$index])) {
                                                        $target = $targetRecords[$index];
                                                        // Find the year index in this target's years array
                                                        $yearIndex = array_search($year, $target->years ?? []);
                                                        if($yearIndex !== false) {
                                                            $foundTarget = true;
                                                            $targetValue = $target->values[$yearIndex] ?? 0;
                                                        }
                                                    }
                                                @endphp
                                                
                                                <div class="">
                                                    @if($foundTarget && $targetValue !== null && $targetValue !== '')
                                                        <span class="font-medium">{{ $targetValue }}</span>
                                                    @else
                                                        <span class="text-gray-500">0</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                @endforeach
                                
                                <!-- Remarks -->
                                <td class="px-3 py-2 border-r border-gray-300 hidden">
                                    <div class="text-sm">
                                        @php
                                            // Load accomplishment records specifically for this STO
                                            $stoAccomplishmentRecords = $sto->accomplishment_id ? \App\Models\StoAccomplishment::whereIn('id', is_array($sto->accomplishment_id) ? $sto->accomplishment_id : [$sto->accomplishment_id])->get() : collect();
                                            
                                            // Get office IDs from this STO's PPA to determine how many offices
                                            $ppaOfficeIds = [];
                                            if($sto->ppa && isset($sto->ppa->office_id)) {
                                                $ppaOfficeIds = $sto->ppa->office_id;
                                            }
                                            
                                            // Create office remarks mapping
                                            $officeRemarksMap = [];
                                            foreach($stoAccomplishmentRecords as $accomplishment) {
                                                foreach($accomplishment->office_ids ?? [] as $index => $officeId) {
                                                    $officeRemarksMap[$officeId] = $accomplishment->remarks[$index] ?? null;
                                                }
                                            }
                                        @endphp
                                        @foreach($ppaOfficeIds as $officeId)
                                            @php
                                                $remark = $officeRemarksMap[$officeId] ?? null;
                                            @endphp
                                            @if($remark !== null && $remark !== '')
                                                <div class="font-medium text-gray-900">{{ $remark }}</div>
                                            @else
                                                <div class="font-medium text-gray-900">-</div>
                                            @endif
                                        @endforeach
                                        @if(empty($ppaOfficeIds))
                                            <div class="font-medium text-gray-900">-</div>
                                        @endif
                                    </div>
                                </td>
                                
                                <!-- Actions -->
                                <td class="px-3 py-2 border-r border-gray-300 hidden">
                                    <div class="flex items-center space-x-2">
                                        <button onclick="editSto({{ $sto->id }})" 
                                            class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deleteSto({{ $sto->id }})" 
                                            class="text-red-600 hover:text-red-900 text-sm font-medium">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="12" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-inbox text-4xl text-gray-300 mb-4"></i>
                                    <p class="text-lg font-medium">No STO records found</p>
                                    <p class="text-sm mt-2">Click the "Add New STO Record" button to create your first record.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Table Footer -->
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <label class="text-sm font-medium text-gray-700">Filter by Period:</label>
                    <select id="yearFilter" class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="2022-2028" selected>2022-2028</option>
                        <option value="2028-2034">2028-2034</option>
                        <option value="2034-2040">2034-2040</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
@endsection

@include('sto.partials.toggle')
@include('sto.partials.modal')
