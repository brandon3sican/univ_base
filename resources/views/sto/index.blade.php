@extends('layouts.app')

@php
    $pageTitle = 'STO';

    // Get all activities (both main and sub) for parent dropdown
    $allActivities = \App\Models\Sto::where('record_type', 'activity')
        ->orderBy('order_column')
        ->get();

    // Group activities by name and add letter suffixes for duplicates
    $groupedActivities = [];
    foreach ($allActivities as $activity) {
        $name = strtolower(trim($activity->program_project_activity));
        if (!isset($groupedActivities[$name])) {
            $groupedActivities[$name] = [];
        }
        $groupedActivities[$name][] = $activity;
    }

    // Add letter suffixes to activities with duplicate names
    $processedActivities = [];
    foreach ($groupedActivities as $name => $activities) {
        if (count($activities) > 1) {
            // Multiple activities with same name - add letter suffixes
            foreach ($activities as $index => $activity) {
                $suffix = chr(97 + $index); // a, b, c, etc.
                $activity->display_name = $activity->program_project_activity . ' (' . $suffix . ')';
                $processedActivities[] = $activity;
            }
        } else {
            // Single activity with this name - no suffix needed
            $activities[0]->display_name = $activities[0]->program_project_activity;
            $processedActivities[] = $activities[0];
        }
    }
@endphp

@section('content')
    <!-- Modern Page Header 
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 mb-6 relative overflow-hidden">
                <div class="absolute inset-0 bg-blue-50 opacity-50"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="flex items-center space-x-3 mb-2">
                                <div class="bg-blue-600 rounded-xl p-2 shadow-lg">
                                    <i class="fas fa-sitemap text-white text-xl"></i>
                                </div>
                                <div>
                                    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">STO</h1>
                                    <p class="text-gray-600 text-sm font-medium">Support To Operations</p>
                                </div>
                            </div>
                            <p class="text-gray-500 text-xs ml-14">Comprehensive management of programs, projects, and activities in
                                hierarchical structure</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            Add New Record Button
                            <button onclick="openCreateModal()"
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-all duration-200 flex items-center text-sm font-medium shadow hover:shadow-lg"
                                title="Add New Record">
                                <i class="fas fa-plus mr-2"></i>
                                <span>Add New Record</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        -->
    <!-- Data Table Container -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <!-- Table Header with Summary Statistics -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-table mr-2"></i>
                    <h2 class="text-lg font-bold">STO Data Table</h2>
                </div>
                <div class="flex items-center space-x-3">
                    <!-- Toggle Target Columns Button -->
                    <button onclick="toggleTargetColumns()" id="toggleTargetBtn"
                        class="bg-white hover:bg-gray-100 text-blue-600 px-4 py-2 rounded-lg transition-all duration-200 flex items-center text-sm font-medium shadow hover:shadow-lg">
                        <i class="fas fa-bullseye mr-2"></i>
                        <span id="toggleTargetText">Show Targets</span>
                    </button>
                    <!-- Toggle Remarks Column Button -->
                    <button onclick="toggleRemarksColumn()" id="toggleRemarksBtn"
                        class="bg-white hover:bg-gray-100 text-blue-600 px-4 py-2 rounded-lg transition-all duration-200 flex items-center text-sm font-medium shadow hover:shadow-lg">
                        <i class="fas fa-comment mr-2"></i>
                        <span id="toggleRemarksText">Show Remarks</span>
                    </button>
                    <!-- Toggle Actions Column Button -->
                    <button onclick="toggleActionsColumn()" id="toggleActionsBtn"
                        class="bg-white hover:bg-gray-100 text-blue-600 px-4 py-2 rounded-lg transition-all duration-200 flex items-center text-sm font-medium shadow hover:shadow-lg">
                        <i class="fas fa-eye mr-2"></i>
                        <span id="toggleActionsText">Show Actions</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Table Container -->
        <div class="overflow-x-auto" style="max-height: 70vh; overflow-y: auto;">
            <table class="w-full border-collapse">
                <!-- Table Head -->
                <thead class="sticky top-0 z-10 bg-gray-50">
                    <!-- First row: main headers -->
                    <tr class="border-b-2 border-gray-200">
                        <th rowspan="2"
                            class="px-4 py-3 text-center text-sm font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200 bg-gray-50">
                            PROGRAM/PROJECT/ACTIVITY & OUTPUT INDICATORS
                        </th>
                        <th rowspan="2"
                            class="px-4 py-3 text-center text-sm font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200 bg-gray-50">
                            OFFICE
                        </th>
                        <th rowspan="2"
                            class="px-4 py-3 text-center text-sm font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200 bg-gray-50">
                            UNIVERSE
                        </th>
                        <th rowspan="2"
                            class="px-4 py-3 text-center text-sm font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200 bg-gray-50">
                            BASELINE
                        </th>
                        <th rowspan="2"
                            class="px-4 py-3 text-center text-sm font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200 bg-gray-50">
                            ACCOMPLISHMENT
                        </th>

                        <!-- This is the new parent column for all years -->
                        <th colspan="5"
                            class="px-4 py-3 text-center text-sm font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200 bg-gray-50 hidden"
                            id="targetHeader">
                            TARGET
                        </th>

                        <th rowspan="2"
                            class="px-4 py-3 text-center text-sm font-bold text-gray-700 uppercase tracking-wider bg-gray-50 hidden"
                            id="remarksHeader">
                            REMARKS
                        </th>
                        <th rowspan="2"
                            class="px-4 py-3 text-center text-sm font-bold text-gray-700 uppercase tracking-wider bg-gray-50 hidden"
                            id="actionsHeader">
                            ACTIONS
                        </th>
                    </tr>

                    <!-- Second row: year sub-headers -->
                    <tr class="border-b-2 border-gray-200">
                        <th
                            class="px-2 py-3 text-center text-sm font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200 bg-gray-50 hidden">
                            2024
                        </th>
                        <th
                            class="px-2 py-3 text-center text-sm font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200 bg-gray-50 hidden">
                            2025
                        </th>
                        <th
                            class="px-2 py-3 text-center text-sm font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200 bg-gray-50 hidden">
                            2026
                        </th>
                        <th
                            class="px-2 py-3 text-center text-sm font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200 bg-gray-50 hidden">
                            2027
                        </th>
                        <th
                            class="px-2 py-3 text-center text-sm font-bold text-gray-700 uppercase tracking-wider border-r border-gray-200 bg-gray-50 hidden">
                            2028
                        </th>
                    </tr>
                </thead>

                <!-- Table Body -->
                <tbody class="bg-white divide-y divide-gray-200" id="sortableTableBody">
                    @php
                        function displayHierarchicalRecords($records, $level = 0, $parentNumber = '', &$activityCounter = null, &$subActivityCounters = [], &$subSubActivityCounters = [], &$lastProgramName = '', &$projectCounter = 1)
                        {
                            if ($activityCounter === null) {
                                $activityCounter = 1;
                            }
                            foreach ($records as $index => $record) {
                                $indentation = str_repeat('-', $level);
                                $hasChildren = $record->children->isNotEmpty();
                                $isSimple = !$record->output_indicators && !$record->office && !$record->universe && !$record->accomplishment;
                                $isActivity = $record->record_type === 'activity';
                                $isProject = $record->record_type === 'project';

                                // Reset activity counter when we encounter a new project at root level
                                if ($isProject && $level === 0) {
                                    $activityCounter = 1;
                                }

                                // For projects, use alphabetical numbering starting from A
                                if ($isProject && $level === 0) {
                                    $projectLetter = chr(65 + ($projectCounter - 1)); // A=65, B=66, etc.
                                    $number = $projectLetter;
                                }

                                // Check if current program name is the same as the last one
                                $currentProgramName = $record->program_project_activity;
                                $showProgramName = $currentProgramName !== $lastProgramName;
                                if ($showProgramName) {
                                    $lastProgramName = $currentProgramName;
                                    if ($isProject && $level === 0) {
                                        $projectCounter++;
                                    }
                                }

                                // For activities, use sequential numbering starting from 1
                                if ($isActivity) {
                                    if ($level === 0) {
                                        // Level 1: Main activity (1, 2, 3...)
                                        $number = $activityCounter;
                                        if ($showProgramName) {
                                            $activityCounter++;
                                        }
                                    } elseif ($level === 1) {
                                        // Level 2: Sub-activity (1.1, 1.2, 2.1...)
                                        $parentOnlyNumber = explode('.', $parentNumber)[0];
                                        if (!isset($subActivityCounters[$parentOnlyNumber])) {
                                            $subActivityCounters[$parentOnlyNumber] = 1;
                                        }
                                        $number = $parentOnlyNumber . '.' . $subActivityCounters[$parentOnlyNumber];
                                        if ($showProgramName) {
                                            $subActivityCounters[$parentOnlyNumber]++;
                                        }
                                    } elseif ($level === 2) {
                                        // Level 3: Sub-sub-activity (1.1.1, 1.1.2, 1.2.1...)
                                        $parentParts = explode('.', $parentNumber);
                                        $mainParentNumber = $parentParts[0];
                                        $subParentNumber = $parentParts[1];

                                        $parentKey = $mainParentNumber . '.' . $subParentNumber;
                                        if (!isset($subSubActivityCounters[$parentKey])) {
                                            $subSubActivityCounters[$parentKey] = 1;
                                        }
                                        $number = $parentKey . '.' . $subSubActivityCounters[$parentKey];
                                        if ($showProgramName) {
                                            $subSubActivityCounters[$parentKey]++;
                                        }
                                    }
                                }
                    @endphp
                    <tr class="hover:bg-blue-50 transition-colors duration-150" data-id="{{ $record->id }}">
                        @if($isSimple && !$hasChildren)
                            <!-- Simple record without children - merge cells -->
                            <td class="px-6 py-4 text-sm text-gray-900" colspan="6">
                                <div class="space-y-2">
                                    <!-- Main Content Row -->
                                    <div class="flex items-start">
                                        @if($level > 0)
                                            <div class="flex items-center mr-3">
                                                @for($i = 0; $i < $level; $i++)
                                                    <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                @endfor
                                            </div>
                                        @endif

                                        <div class="flex-1">
                                            @if($showProgramName)
                                                @if($isActivity)
                                                    <div class="flex items-center ml-6">
                                                        <span
                                                            class="inline-flex items-center justify-center w-8 h-8 rounded-full text-xs font-bold mr-3 bg-blue-100 text-blue-800 border-2 border-blue-300">{{ $number }}</span>
                                                        <span
                                                            class="font-medium text-blue-700 leading-relaxed bg-blue-50 px-3 py-1 rounded-lg">{{ $record->program_project_activity }}</span>
                                                    </div>
                                                @elseif($record->record_type === 'program')
                                                    <div class="flex items-center">
                                                        <span
                                                            class="font-bold text-gray-900 text-lg leading-relaxed">{{ Str::upper($record->program_project_activity) }}</span>
                                                    </div>
                                                @elseif($record->record_type === 'project')
                                                    <div class="flex items-center">
                                                        <span
                                                            class="inline-flex items-center justify-center w-8 h-8 rounded-full text-xs font-bold mr-3 bg-orange-100 text-orange-800 border-4 border-orange-300">{{ $number }}</span>
                                                        <span
                                                            class="font-bold text-orange-900 text-lg leading-relaxed bg-orange-50 px-3 py-1 rounded-lg border-l-8 border-orange-500">{{ $record->program_project_activity }}</span>
                                                    </div>
                                                @else
                                                    <span
                                                        class="text-gray-700 leading-relaxed bg-gray-50 px-3 py-1 rounded-lg">{{ $record->program_project_activity }}</span>
                                                @endif
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Output Indicators Section -->
                                    @if($record->output_indicators)
                                        <div
                                            class="ml-{{ $level == 0 ? '6' : ($level == 1 ? '22' : '70') }} mt-3 p-4 bg-gradient-to-r from-teal-50 to-cyan-50 rounded-xl border-l-4 border-teal-400 shadow-sm">
                                            <div class="flex items-start">
                                                <div
                                                    class="w-5 h-5 bg-teal-500 rounded-full mr-3 mt-0.5 flex items-center justify-center">
                                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                                        <path fill-rule="evenodd"
                                                            d="M4 5a2 2 0 012-2v8a2 2 0 01-2 2H2a2 2 0 01-2-2V5a2 2 0 012-2h2zm2-1a1 1 0 00-1 1v8a1 1 0 001 1h2a1 1 0 001-1V4a1 1 0 00-1-1H6z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                                <div class="flex-1">
                                                    <span
                                                        class="text-xs font-bold text-teal-700 uppercase tracking-wider mb-2 block">📊
                                                        Output Indicators</span>
                                                    <span
                                                        class="text-sm text-gray-600 leading-relaxed font-medium">{{ $record->output_indicators }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        @elseif($isActivity && $hasChildren)
                            <!-- Activity with children - merge cells -->
                            <td class="px-6 py-4 text-sm text-gray-900" colspan="6">
                                <div class="space-y-2">
                                    <!-- Main Content Row -->
                                    <div class="flex items-start">
                                        @if($level > 0)
                                            <div class="flex items-center mr-3">
                                                @for($i = 0; $i < $level; $i++)
                                                    <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                @endfor
                                            </div>
                                        @endif

                                        <div class="flex-1">
                                            @if($showProgramName)
                                                <div class="flex items-center ml-6">
                                                    <span
                                                        class="inline-flex items-center justify-center w-8 h-8 rounded-full text-xs font-bold mr-3 bg-blue-100 text-blue-800 border-2 border-blue-300">{{ $number }}</span>
                                                    <span
                                                        class="font-medium text-blue-700 leading-relaxed bg-blue-50 px-3 py-1 rounded-lg">{{ $record->program_project_activity }}</span>

                                                    <!-- Output Indicators Section -->
                                                    @if($record->output_indicators)
                                                        <div
                                                            class="ml-3 mt-3 p-4 bg-gradient-to-r from-teal-50 to-cyan-50 rounded-xl border-l-4 border-teal-400 shadow-sm">
                                                            <div class="flex items-start">
                                                                <div
                                                                    class="w-5 h-5 bg-teal-500 rounded-full mr-3 mt-0.5 flex items-center justify-center">
                                                                    <svg class="w-3 h-3 text-white" fill="currentColor"
                                                                        viewBox="0 0 20 20">
                                                                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                                                        <path fill-rule="evenodd"
                                                                            d="M4 5a2 2 0 012-2v8a2 2 0 01-2 2H2a2 2 0 01-2-2V5a2 2 0 012-2h2zm2-1a1 1 0 00-1 1v8a1 1 0 001 1h2a1 1 0 001-1V4a1 1 0 00-1-1H6z"
                                                                            clip-rule="evenodd" />
                                                                    </svg>
                                                                </div>
                                                                <div class="flex-1">
                                                                    <span
                                                                        class="text-xs font-bold text-teal-700 uppercase tracking-wider mb-2 block">📊
                                                                        Output Indicators</span>
                                                                    <span
                                                                        class="text-sm text-gray-600 leading-relaxed font-medium">{{ $record->output_indicators }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                        @else
                            <!-- Full record or record with children - show all columns -->
                            <!-- Column 1: PROGRAM/PROJECT/ACTIVITY & OUTPUT INDICATORS - Shows hierarchical display with indicators -->
                            <td class="px-6 py-4 text-sm text-gray-900 border-r border-gray-200 bg-white">
                                <div class="space-y-2">
                                    <!-- Main Content Row -->
                                    <div class="flex items-start">
                                        @if($level > 0)
                                            <div class="flex items-center mr-3">
                                                @for($i = 0; $i < $level; $i++)
                                                    <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                @endfor
                                            </div>
                                        @endif

                                        <div class="flex-1">
                                            @if($showProgramName)
                                                @if($isActivity)
                                                    <div class="flex items-center ml-6">
                                                        <span
                                                            class="inline-flex items-center justify-center w-8 h-8 rounded-full text-xs font-bold mr-3 bg-blue-100 text-blue-800 border-2 border-blue-300">{{ $number }}</span>
                                                        <span
                                                            class="font-medium text-blue-700 leading-relaxed bg-blue-50 px-3 py-1 rounded-lg">{{ $record->program_project_activity }}</span>
                                                    </div>
                                                @elseif($record->record_type === 'program')
                                                    <div class="flex items-center">
                                                        <span
                                                            class="font-bold text-gray-900 text-lg leading-relaxed">{{ Str::upper($record->program_project_activity) }}</span>
                                                    </div>
                                                @elseif($record->record_type === 'project')
                                                    <div class="flex items-center">
                                                        <span
                                                            class="inline-flex items-center justify-center w-8 h-8 rounded-full text-xs font-bold mr-3 bg-orange-100 text-orange-800 border-4 border-orange-300">{{ $number }}</span>
                                                        <span
                                                            class="font-bold text-orange-900 text-lg leading-relaxed bg-orange-50 px-3 py-1 rounded-lg border-l-8 border-orange-500">{{ $record->program_project_activity }}</span>
                                                    </div>
                                                @else
                                                    <span
                                                        class="text-gray-700 leading-relaxed bg-gray-50 px-3 py-1 rounded-lg">{{ $record->program_project_activity }}</span>
                                                @endif
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Output Indicators Section -->
                                    @if($record->output_indicators)
                                        <div
                                            class="ml-{{ $level == 0 ? '6' : ($level == 1 ? '22' : '70') }} mt-3 p-4 bg-gradient-to-r from-teal-50 to-cyan-50 rounded-xl border-l-4 border-teal-400 shadow-sm">
                                            <div class="flex items-start">
                                                <div
                                                    class="w-5 h-5 bg-teal-500 rounded-full mr-3 mt-0.5 flex items-center justify-center">
                                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                                        <path fill-rule="evenodd"
                                                            d="M4 5a2 2 0 012-2v8a2 2 0 01-2 2H2a2 2 0 01-2-2V5a2 2 0 012-2h2zm2-1a1 1 0 00-1 1v8a1 1 0 001 1h2a1 1 0 001-1V4a1 1 0 00-1-1H6z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                                <div class="flex-1">
                                                    <span
                                                        class="text-xs font-bold text-teal-700 uppercase tracking-wider mb-2 block">📊
                                                        Output Indicators</span>
                                                    <span
                                                        class="text-sm text-gray-600 leading-relaxed font-medium">{{ $record->output_indicators }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <!-- Column 3: OFFICE - Displays which offices are assigned to this record (comma-separated) -->
                            <td class="px-4 py-3 text-sm text-gray-900 border-r border-gray-200 text-center font-medium">
                                @if($record->office)
                                    @php
                                        $offices = explode(',', $record->office);
                                        foreach ($offices as $office) {
                                            echo trim($office) . '<br>';
                                        }
                                    @endphp
                                @else
                                    <span class="text-gray-400">-RO</span><br>
                                    <span class="text-gray-400">-ABRA</span><br>
                                    <span class="text-gray-400">-APAYAO</span><br>
                                    <span class="text-gray-400">-BENGUET</span><br>
                                    <span class="text-gray-400">-IFUGAO</span><br>
                                    <span class="text-gray-400">-KALINGA</span><br>
                                    <span class="text-gray-400">-MT.PROVINCE</span><br>
                                    <span class="text-gray-400">-CAGAYAN</span><br>
                                    <span class="text-gray-400">-ISABELA</span><br>
                                    <span class="text-gray-400">-QUIRINO</span><br>
                                    <span class="text-gray-400">-AURORA</span><br>
                                    <span class="text-gray-400">-BATANES</span><br>
                                    <span class="text-gray-400">-PANGASINAN</span>
                                @endif
                            </td>

                            <!-- Column 4: UNIVERSE - Shows target/baseline values for each office (comma-separated) -->
                            <td class="px-4 py-3 text-sm text-gray-900 border-r border-gray-200 text-center font-medium">
                                @if($record->universe && $record->office)
                                    @php
                                        $universes = explode(',', $record->universe);
                                        // Get office count to ensure consistent display
                                        $offices = $record->office ? explode(',', $record->office) : [];
                                        $office_count = count($offices);

                                        // Ensure universe array matches office count
                                        if (count($universes) < $office_count) {
                                            $universes = array_pad($universes, $office_count, '');
                                        } elseif (count($universes) > $office_count) {
                                            $universes = array_slice($universes, 0, $office_count);
                                        }

                                        foreach ($universes as $universe) {
                                            $trimmedUniverse = trim($universe);
                                            if ($trimmedUniverse !== '' && $trimmedUniverse !== '-' && is_numeric($trimmedUniverse)) {
                                                echo number_format($trimmedUniverse) . '<br>';
                                            } else {
                                                echo $trimmedUniverse !== '' && $trimmedUniverse !== '-' ? $trimmedUniverse : '-' . '<br>';
                                            }
                                        }
                                    @endphp
                                @elseif($record->office)
                                    @php
                                        $offices = explode(',', $record->office);
                                        $office_count = count($offices);
                                        echo implode('<br>', array_fill(0, $office_count, '-'));
                                    @endphp
                                @else
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>

                            <!-- Column 5: BASELINE - Calculates remaining work needed (universe - accomplishment) -->
                            <td class="px-4 py-3 text-sm text-gray-900 border-r border-gray-200 text-center font-medium">
                                @if($record->universe && $record->accomplishment)
                                    @php
                                        $universes = explode(',', $record->universe);
                                        $accomplishments = explode(',', $record->accomplishment);
                                        // Get office count to ensure consistent display
                                        $offices = $record->office ? explode(',', $record->office) : [];
                                        $office_count = count($offices);

                                        // Ensure arrays match office count
                                        if (count($universes) < $office_count) {
                                            $universes = array_pad($universes, $office_count, '');
                                        } elseif (count($universes) > $office_count) {
                                            $universes = array_slice($universes, 0, $office_count);
                                        }

                                        if (count($accomplishments) < $office_count) {
                                            $accomplishments = array_pad($accomplishments, $office_count, '');
                                        } elseif (count($accomplishments) > $office_count) {
                                            $accomplishments = array_slice($accomplishments, 0, $office_count);
                                        }

                                        foreach ($universes as $index => $universe) {
                                            $accomplishment = isset($accomplishments[$index]) ? $accomplishments[$index] : 0;
                                            $trimmedUniverse = trim($universe);
                                            $trimmedAccomplishment = trim($accomplishment);

                                            if (
                                                is_numeric($trimmedUniverse) && is_numeric($trimmedAccomplishment) &&
                                                $trimmedUniverse !== '' && $trimmedAccomplishment !== ''
                                            ) {
                                                $baseline = intval($trimmedUniverse) - intval($trimmedAccomplishment);
                                                echo number_format($baseline) . '<br>';
                                            } else {
                                                echo '-' . '<br>';
                                            }
                                        }
                                    @endphp
                                @elseif($record->office)
                                    @php
                                        $offices = explode(',', $record->office);
                                        $office_count = count($offices);
                                        echo implode('<br>', array_fill(0, $office_count, '-'));
                                    @endphp
                                @else
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>

                            <!-- Column 6: ACCOMPLISHMENT - Shows all accomplishment values for backward compatibility (comma-separated) -->
                            <td class="px-4 py-3 text-sm text-gray-900 text-center font-medium">
                                @if($record->accomplishment && $record->office)
                                    @php
                                        $accomplishments = explode(',', $record->accomplishment);
                                        // Get office count to ensure consistent display
                                        $offices = $record->office ? explode(',', $record->office) : [];
                                        $office_count = count($offices);

                                        // Ensure accomplishment array matches office count
                                        if (count($accomplishments) < $office_count) {
                                            $accomplishments = array_pad($accomplishments, $office_count, '');
                                        } elseif (count($accomplishments) > $office_count) {
                                            $accomplishments = array_slice($accomplishments, 0, $office_count);
                                        }

                                        foreach ($accomplishments as $accomplishment) {
                                            $trimmedAccomplishment = trim($accomplishment);
                                            if ($trimmedAccomplishment !== '' && $trimmedAccomplishment !== '-' && is_numeric($trimmedAccomplishment)) {
                                                echo number_format($trimmedAccomplishment) . '<br>';
                                            } else {
                                                echo $trimmedAccomplishment !== '' && $trimmedAccomplishment !== '-' ? $trimmedAccomplishment : '-' . '<br>';
                                            }
                                        }
                                    @endphp
                                @elseif($record->office)
                                    @php
                                        $offices = explode(',', $record->office);
                                        $office_count = count($offices);
                                        echo implode('<br>', array_fill(0, $office_count, '-'));
                                    @endphp
                                @else
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>

                            <!-- Columns 7-11: YEARLY TARGETS (2024-2028) - Shows yearly target values from database -->
                            <!-- Column 7: 2024 Target -->
                            <td class="px-2 py-3 text-sm text-gray-900 text-center font-medium hidden">
                                @if($record->target_2024 && $record->office)
                                    @php
                                        $targets_2024 = explode(',', $record->target_2024);
                                        // Get office count to ensure consistent display
                                        $offices = $record->office ? explode(',', $record->office) : [];
                                        $office_count = count($offices);

                                        // Ensure target array matches office count
                                        if (count($targets_2024) < $office_count) {
                                            // Pad with empty values if target array is shorter
                                            $targets_2024 = array_pad($targets_2024, $office_count, '');
                                        } elseif (count($targets_2024) > $office_count) {
                                            // Truncate if target array is longer
                                            $targets_2024 = array_slice($targets_2024, 0, $office_count);
                                        }

                                        // Display individual office targets separated by line breaks
                                        $display_2024 = implode('<br>', array_map(function ($target) {
                                            return $target && $target !== '' && $target !== '-' ? number_format($target) : '-';
                                        }, $targets_2024));
                                    @endphp
                                    {!! $display_2024 !!}
                                @else
                                    @if($record->office)
                                        @php
                                            $offices = explode(',', $record->office);
                                            $office_count = count($offices);
                                            // Display "-" for each office when no targets exist
                                            echo implode('<br>', array_fill(0, $office_count, '-'));
                                        @endphp
                                    @else
                                        -
                                    @endif
                                @endif
                            </td>

                            <!-- Column 8: 2025 Target -->
                            <td class="px-2 py-3 text-sm text-gray-900 text-center font-medium hidden">
                                @if($record->target_2025)
                                    @php
                                        $targets_2025 = explode(',', $record->target_2025);
                                        // Get office count to ensure consistent display
                                        $offices = $record->office ? explode(',', $record->office) : [];
                                        $office_count = count($offices);

                                        // Ensure target array matches office count
                                        if (count($targets_2025) < $office_count) {
                                            $targets_2025 = array_pad($targets_2025, $office_count, '');
                                        } elseif (count($targets_2025) > $office_count) {
                                            $targets_2025 = array_slice($targets_2025, 0, $office_count);
                                        }

                                        $display_2025 = implode('<br>', array_map(function ($target) {
                                            return $target && $target !== '' && $target !== '-' ? number_format($target) : '-';
                                        }, $targets_2025));
                                    @endphp
                                    {!! $display_2025 !!}
                                @elseif($record->office)
                                    @php
                                        $offices = explode(',', $record->office);
                                        $office_count = count($offices);
                                        echo implode('<br>', array_fill(0, $office_count, '-'));
                                    @endphp
                                @else
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>

                            <!-- Column 9: 2026 Target -->
                            <td class="px-2 py-3 text-sm text-gray-900 text-center font-medium hidden">
                                @if($record->target_2026)
                                    @php
                                        $targets_2026 = explode(',', $record->target_2026);
                                        // Get office count to ensure consistent display
                                        $offices = $record->office ? explode(',', $record->office) : [];
                                        $office_count = count($offices);

                                        // Ensure target array matches office count
                                        if (count($targets_2026) < $office_count) {
                                            $targets_2026 = array_pad($targets_2026, $office_count, '');
                                        } elseif (count($targets_2026) > $office_count) {
                                            $targets_2026 = array_slice($targets_2026, 0, $office_count);
                                        }

                                        $display_2026 = implode('<br>', array_map(function ($target) {
                                            return $target && $target !== '' && $target !== '-' ? number_format($target) : '-';
                                        }, $targets_2026));
                                    @endphp
                                    {!! $display_2026 !!}
                                @elseif($record->office)
                                    @php
                                        $offices = explode(',', $record->office);
                                        $office_count = count($offices);
                                        echo implode('<br>', array_fill(0, $office_count, '-'));
                                    @endphp
                                @else
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>

                            <!-- Column 10: 2027 Target -->
                            <td class="px-2 py-3 text-sm text-gray-900 text-center font-medium hidden">
                                @if($record->target_2027)
                                    @php
                                        $targets_2027 = explode(',', $record->target_2027);
                                        // Get office count to ensure consistent display
                                        $offices = $record->office ? explode(',', $record->office) : [];
                                        $office_count = count($offices);

                                        // Ensure target array matches office count
                                        if (count($targets_2027) < $office_count) {
                                            $targets_2027 = array_pad($targets_2027, $office_count, '');
                                        } elseif (count($targets_2027) > $office_count) {
                                            $targets_2027 = array_slice($targets_2027, 0, $office_count);
                                        }

                                        $display_2027 = implode('<br>', array_map(function ($target) {
                                            return $target && $target !== '' && $target !== '-' ? number_format($target) : '-';
                                        }, $targets_2027));
                                    @endphp
                                    {!! $display_2027 !!}
                                @elseif($record->office)
                                    @php
                                        $offices = explode(',', $record->office);
                                        $office_count = count($offices);
                                        echo implode('<br>', array_fill(0, $office_count, '-'));
                                    @endphp
                                @else
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>

                            <!-- Column 11: 2028 Target -->
                            <td class="px-2 py-3 text-sm text-gray-900 text-center font-medium hidden">
                                @if($record->target_2028)
                                    @php
                                        $targets_2028 = explode(',', $record->target_2028);
                                        // Get office count to ensure consistent display
                                        $offices = $record->office ? explode(',', $record->office) : [];
                                        $office_count = count($offices);

                                        // Ensure target array matches office count
                                        if (count($targets_2028) < $office_count) {
                                            $targets_2028 = array_pad($targets_2028, $office_count, '');
                                        } elseif (count($targets_2028) > $office_count) {
                                            $targets_2028 = array_slice($targets_2028, 0, $office_count);
                                        }

                                        $display_2028 = implode('<br>', array_map(function ($target) {
                                            return $target && $target !== '' && $target !== '-' ? number_format($target) : '-';
                                        }, $targets_2028));
                                    @endphp
                                    {!! $display_2028 !!}
                                @elseif($record->office)
                                    @php
                                        $offices = explode(',', $record->office);
                                        $office_count = count($offices);
                                        echo implode('<br>', array_fill(0, $office_count, '-'));
                                    @endphp
                                @else
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span><br>
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>

                            <!-- Column 12: REMARKS - Shows office-specific remarks sorted by office order -->
                            <td class="px-4 py-3 text-sm text-gray-900 text-left font-medium hidden"
                                id="remarksCell-{{ $record->id }}">
                                @if($record->remarks && $record->office)
                                    @php
                                        // Define office order for consistent remarks arrangement
                                        $OFFICE_ORDER = ['RO', 'ABRA', 'APAYAO', 'BENGUET', 'IFUGAO', 'KALINGA', 'MT.PROVINCE', 'CAGAYAN', 'ISABELA', 'QUIRINO', 'AURORA', 'BATANES', 'PANGASINAN'];

                                        // Split remarks by semicolon and sort by office order
                                        $remarksArray = explode(';', $record->remarks);
                                        $sortedRemarks = [];

                                        // Get selected offices to ensure alignment
                                        $selectedOffices = $record->office ? explode(',', $record->office) : [];

                                        foreach ($OFFICE_ORDER as $office) {
                                            // Only process offices that are actually selected
                                            if (in_array($office, $selectedOffices)) {
                                                $foundRemark = null;
                                                foreach ($remarksArray as $remark) {
                                                    $trimmedRemark = trim($remark);
                                                    if ($trimmedRemark !== '' && str_starts_with($trimmedRemark, $office . ':')) {
                                                        $foundRemark = $trimmedRemark;
                                                        break;
                                                    }
                                                }

                                                if ($foundRemark) {
                                                    $sortedRemarks[] = $foundRemark;
                                                } else {
                                                    // Add "-" for offices without remarks to maintain alignment
                                                    $sortedRemarks[] = $office . ': -';
                                                }
                                            }
                                        }

                                        // Display sorted remarks vertically with line breaks
                                        foreach ($sortedRemarks as $remark) {
                                            $trimmedRemark = trim($remark);
                                            if ($trimmedRemark !== '') {
                                                // Check if remark is just "-" (empty remark)
                                                $remarkText = substr($trimmedRemark, strpos($trimmedRemark, ':') + 2);
                                                if ($remarkText === '-') {
                                                    echo '<span class="text-gray-400">' . htmlspecialchars($trimmedRemark) . '</span><br>';
                                                } else {
                                                    echo '<span>' . htmlspecialchars($trimmedRemark) . '</span><br>';
                                                }
                                            }
                                        }
                                    @endphp
                                @elseif($record->office)
                                    @php
                                        $offices = explode(',', $record->office);
                                        foreach ($offices as $office) {
                                            echo '<span class="text-gray-400">' . htmlspecialchars(trim($office) . ': -') . '</span><br>';
                                        }
                                    @endphp
                                @else
                                    <span class="text-gray-400">RO: -</span><br>
                                    <span class="text-gray-400">ABRA: -</span><br>
                                    <span class="text-gray-400">APAYAO: -</span><br>
                                    <span class="text-gray-400">BENGUET: -</span><br>
                                    <span class="text-gray-400">IFUGAO: -</span><br>
                                    <span class="text-gray-400">KALINGA: -</span><br>
                                    <span class="text-gray-400">MT.PROVINCE: -</span><br>
                                    <span class="text-gray-400">CAGAYAN: -</span><br>
                                    <span class="text-gray-400">ISABELA: -</span><br>
                                    <span class="text-gray-400">QUIRINO: -</span><br>
                                    <span class="text-gray-400">AURORA: -</span><br>
                                    <span class="text-gray-400">BATANES: -</span><br>
                                    <span class="text-gray-400">PANGASINAN: -</span>
                                @endif
                            </td>

                            <!-- Column 13: ACTIONS - Edit and delete buttons for record management -->
                            <td class="px-4 py-3 text-sm text-gray-900 border-r border-gray-200 text-center hidden"
                                id="actionsCell-{{ $record->id }}">
                                <div class="flex items-center justify-center space-x-2">
                                    <button onclick="editRecord({{ $record->id }})"
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg transition-all duration-200 text-xs font-medium shadow hover:shadow-lg">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deleteRecord({{ $record->id }})"
                                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-lg transition-all duration-200 text-xs font-medium shadow hover:shadow-lg">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <div class="px-2">
                                        <button onclick="moveUp({{ $record->id }})"
                                            class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-1 rounded-lg transition-all duration-200 text-xs font-medium shadow hover:shadow-lg"
                                            title="Move Up">
                                            <i class="fas fa-arrow-up"></i>
                                        </button>
                                        <button onclick="moveDown({{ $record->id }})"
                                            class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-1 rounded-lg transition-all duration-200 text-xs font-medium shadow hover:shadow-lg"
                                            title="Move Down">
                                            <i class="fas fa-arrow-down"></i>
                                        </button>
                                    </div>
                                </div>
                            </td>
                        @endif
                    </tr>
                    @if($hasChildren)
                        @php displayHierarchicalRecords($record->children, $level + 1, $number, $activityCounter, $subActivityCounters, $subSubActivityCounters, $lastProgramName); @endphp
                    @endif
                    @php
                            }
                        }
                    @endphp

                    @if(count($stoRecords) > 0)
                        @php displayHierarchicalRecords($stoRecords); @endphp
                    @else
                        <tr>
                            <td colspan="13" class="px-4 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
                                    <p class="text-lg font-medium">No records found</p>
                                    <p class="text-sm text-gray-400">Start by adding your first STO record</p>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Table Footer -->
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    <div class="flex items-center space-x-4">
                        <span class="font-medium">{{ $stoRecords->count() }}</span>
                        {{ $stoRecords->count() == 1 ? 'record' : 'records' }} total
                        <span class="text-gray-400">|</span>
                        <span class="text-blue-600 font-medium">
                            {{ $stoRecords->where('record_type', 'program')->count() }}
                            program{{ $stoRecords->where('record_type', 'program')->count() == 1 ? '' : 's' }}
                        </span>
                        <span class="text-gray-400">|</span>
                        <span class="text-green-600 font-medium">
                            {{ $stoRecords->where('record_type', 'project')->count() }}
                            project{{ $stoRecords->where('record_type', 'project')->count() == 1 ? '' : 's' }}
                        </span>
                        <span class="text-gray-400">|</span>
                        <span class="text-purple-600 font-medium">
                            {{ $stoRecords->where('record_type', 'activity')->count() }}
                            activit{{ $stoRecords->where('record_type', 'activity')->count() == 1 ? 'y' : 'ies' }}
                        </span>
                        @if($stoRecords->where('record_type', 'activity')->whereNotNull('parent_id')->count() > 0)
                            <span class="text-gray-400">|</span>
                            <span class="text-orange-600 font-medium">
                                {{ $stoRecords->where('record_type', 'activity')->whereNotNull('parent_id')->count() }}
                                sub-activit{{ $stoRecords->where('record_type', 'activity')->whereNotNull('parent_id')->count() == 1 ? 'y' : 'ies' }}
                            </span>
                        @endif
                    </div>
                </div>
                <button onclick="openCreateModal()"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-all duration-200 text-sm font-medium shadow hover:shadow-lg flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Add New Record
                </button>
            </div>
        </div>
    </div>
@endsection

@include('sto.partials.scripts')
@include('sto.partials.toggle')
@include('sto.partials.modal')