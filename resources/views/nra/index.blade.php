@extends('layouts.app')

@php
    $pageTitle = 'NRA';
    
    // Get all activities (both main and sub) for parent dropdown
    $allActivities = \App\Models\Nra::where('record_type', 'activity')
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

    <!-- Data Table Container -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <!-- Table Header with Summary Statistics -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-table mr-2"></i>
                    <h2 class="text-lg font-bold">NRA Data Table</h2>
                </div>
                <div class="flex items-center space-x-3">
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
                        <th rowspan="2"
                            class="px-4 py-3 text-center text-sm font-bold text-gray-700 uppercase tracking-wider bg-gray-50 hidden" id="actionsHeader">
                            ACTIONS
                        </th>
                    </tr>
                </thead>

                <!-- Table Body -->
                <tbody class="bg-white divide-y divide-gray-200" id="sortableTableBody">
                    @php
                        function displayHierarchicalRecords($records, $level = 0, $parentNumber = '', &$activityCounter = null, &$subActivityCounters = [], &$subSubActivityCounters = [], &$projectCounter = null)
                        {
                            if ($activityCounter === null) {
                                $activityCounter = 1;
                            }
                            if ($projectCounter === null) {
                                $projectCounter = 0;
                            }
                            foreach ($records as $index => $record) {
                                $indentation = str_repeat('-', $level);
                                $hasChildren = $record->children->isNotEmpty();
                                $isSimple = !$record->output_indicators && !$record->office && !$record->universe && !$record->accomplishment;
                                $isActivity = $record->record_type === 'activity';
                                $isProject = $record->record_type === 'project';

                                // For projects, use alphabetical numbering starting from A
                                if ($isProject) {
                                    $number = chr(65 + $projectCounter); // 65 = ASCII for 'A'
                                    $projectCounter++;
                                }
                                // For activities, use sequential numbering starting from 1
                                elseif ($isActivity) {
                                    if ($level === 0) {
                                        // Level 1: Main activity (1, 2, 3...)
                                        $number = $activityCounter;
                                        $activityCounter++;
                                    } elseif ($level === 1) {
                                        // Level 2: Sub-activity (1.1, 1.2, 2.1...)
                                        $parentOnlyNumber = explode('.', $parentNumber)[0];
                                        if (!isset($subActivityCounters[$parentOnlyNumber])) {
                                            $subActivityCounters[$parentOnlyNumber] = 1;
                                        }
                                        $number = $parentOnlyNumber . '.' . $subActivityCounters[$parentOnlyNumber];
                                        $subActivityCounters[$parentOnlyNumber]++;
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
                                        $subSubActivityCounters[$parentKey]++;
                                    }
                                }
                    @endphp
                    <tr class="hover:bg-blue-50 transition-colors duration-150" data-id="{{ $record->id }}">
                        @if($isSimple && !$hasChildren)
                            <!-- Simple record without children - merge cells -->
                            <td class="px-6 py-4 text-sm text-gray-900" colspan="5">
                                <div class="space-y-2">
                                    <!-- Main Content Row -->
                                    <div class="flex items-start">
                                        @if($level > 0)
                                            <div class="flex items-center mr-3">
                                                @for($i = 0; $i < $level; $i++)
                                                    <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                @endfor
                                            </div>
                                        @endif
                                        
                                        <div class="flex-1">
                                            @if($isActivity)
                                                <div class="flex items-center ml-6">
                                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-xs font-bold mr-3 bg-blue-100 text-blue-800 border-2 border-blue-300">{{ $number }}</span>
                                                    <span class="font-medium text-blue-700 leading-relaxed bg-blue-50 px-3 py-1 rounded-lg">{{ $record->program_project_activity }}</span>
                                                </div>
                                            @elseif($record->record_type === 'program')
                                                <div class="flex items-center">
                                                    <span class="font-bold text-gray-900 text-lg leading-relaxed">{{ Str::upper($record->program_project_activity) }}</span>
                                                </div>
                                            @elseif($record->record_type === 'project')
                                                <div class="flex items-center">
                                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-xs font-bold mr-3 bg-orange-100 text-orange-800 border-4 border-orange-300">{{ $number }}</span>
                                                    <span class="font-bold text-orange-900 text-lg leading-relaxed bg-orange-50 px-3 py-1 rounded-lg border-l-8 border-orange-500">{{ $record->program_project_activity }}</span>
                                                </div>
                                            @else
                                                <span class="text-gray-700 leading-relaxed bg-gray-50 px-3 py-1 rounded-lg">{{ $record->program_project_activity }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Output Indicators Section -->
                                    @if($record->output_indicators)
                                        <div class="ml-{{ $level == 0 ? '6' : ($level == 1 ? '22' : '70') }} mt-3 p-4 bg-gradient-to-r from-teal-50 to-cyan-50 rounded-xl border-l-4 border-teal-400 shadow-sm">
                                            <div class="flex items-start">
                                                <div class="w-5 h-5 bg-teal-500 rounded-full mr-3 mt-0.5 flex items-center justify-center">
                                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2v8a2 2 0 01-2 2H2a2 2 0 01-2-2V5a2 2 0 012-2h2zm2-1a1 1 0 00-1 1v8a1 1 0 001 1h2a1 1 0 001-1V4a1 1 0 00-1-1H6z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                                <div class="flex-1">
                                                    <span class="text-xs font-bold text-teal-700 uppercase tracking-wider mb-2 block">📊 Output Indicators</span>
                                                    <span class="text-sm text-gray-600 leading-relaxed font-medium">{{ $record->output_indicators }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        @elseif($isActivity && $hasChildren)
                            <!-- Activity with children - merge cells -->
                            <td class="px-6 py-4 text-sm text-gray-900" colspan="5">
                                <div class="space-y-2">
                                    <!-- Main Content Row -->
                                    <div class="flex items-start">
                                        @if($level > 0)
                                            <div class="flex items-center mr-3">
                                                @for($i = 0; $i < $level; $i++)
                                                    <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                @endfor
                                            </div>
                                        @endif
                                        
                                        <div class="flex-1">
                                            <div class="flex items-center ml-6">
                                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-xs font-bold mr-3 bg-blue-100 text-blue-800 border-2 border-blue-300">{{ $number }}</span>
                                                <span class="font-medium text-blue-700 leading-relaxed bg-blue-50 px-3 py-1 rounded-lg">{{ $record->program_project_activity }}</span>
                                            </div>
                                            
                                            <!-- Output Indicators Section -->
                                            @if($record->output_indicators)
                                                <div class="ml-3 mt-3 p-4 bg-gradient-to-r from-teal-50 to-cyan-50 rounded-xl border-l-4 border-teal-400 shadow-sm">
                                                    <div class="flex items-start">
                                                        <div class="w-5 h-5 bg-teal-500 rounded-full mr-3 mt-0.5 flex items-center justify-center">
                                                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                                                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2v8a2 2 0 01-2 2H2a2 2 0 01-2-2V5a2 2 0 012-2h2zm2-1a1 1 0 00-1 1v8a1 1 0 001 1h2a1 1 0 001-1V4a1 1 0 00-1-1H6z" clip-rule="evenodd"/>
                                                            </svg>
                                                        </div>
                                                        <div class="flex-1">
                                                            <span class="text-xs font-bold text-teal-700 uppercase tracking-wider mb-2 block">📊 Output Indicators</span>
                                                            <span class="text-sm text-gray-600 leading-relaxed font-medium">{{ $record->output_indicators }}</span>
                                                        </div>
                                                    </div>
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
                                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                @endfor
                                            </div>
                                        @endif
                                        
                                        <div class="flex-1">
                                            @if($isActivity)
                                                <div class="flex items-center ml-6">
                                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-xs font-bold mr-3 bg-blue-100 text-blue-800 border-2 border-blue-300">{{ $number }}</span>
                                                    <span class="font-medium text-blue-700 leading-relaxed bg-blue-50 px-3 py-1 rounded-lg">{{ $record->program_project_activity }}</span>
                                                </div>
                                            @elseif($record->record_type === 'program')
                                                <div class="flex items-center">
                                                    <span class="font-bold text-gray-900 text-lg leading-relaxed">{{ Str::upper($record->program_project_activity) }}</span>
                                                </div>
                                            @elseif($record->record_type === 'project')
                                                <div class="flex items-center">
                                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-xs font-bold mr-3 bg-orange-100 text-orange-800 border-4 border-orange-300">{{ $number }}</span>
                                                    <span class="font-bold text-orange-900 text-lg leading-relaxed bg-orange-50 px-3 py-1 rounded-lg border-l-8 border-orange-500">{{ $record->program_project_activity }}</span>
                                                </div>
                                            @else
                                                <span class="text-gray-700 leading-relaxed bg-gray-50 px-3 py-1 rounded-lg">{{ $record->program_project_activity }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Output Indicators Section -->
                                    @if($record->output_indicators)
                                        <div class="ml-{{ $level == 0 ? '6' : ($level == 1 ? '22' : '70') }} mt-3 p-4 bg-gradient-to-r from-teal-50 to-cyan-50 rounded-xl border-l-4 border-teal-400 shadow-sm">
                                            <div class="flex items-start">
                                                <div class="w-5 h-5 bg-teal-500 rounded-full mr-3 mt-0.5 flex items-center justify-center">
                                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2v8a2 2 0 01-2 2H2a2 2 0 01-2-2V5a2 2 0 012-2h2zm2-1a1 1 0 00-1 1v8a1 1 0 001 1h2a1 1 0 001-1V4a1 1 0 00-1-1H6z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                                <div class="flex-1">
                                                    <span class="text-xs font-bold text-teal-700 uppercase tracking-wider mb-2 block">📊 Output Indicators</span>
                                                    <span class="text-sm text-gray-600 leading-relaxed font-medium">{{ $record->output_indicators }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            
                            <!-- Column 2: OFFICE - Displays which offices are assigned to this record (comma-separated) -->
                            <td class="px-4 py-3 text-sm text-gray-900 border-r border-gray-200 text-center font-medium">
                                {{ $record->office ?? '' }}
                            </td>
                            
                            <!-- Column 3: UNIVERSE - Shows target/baseline values for each office (comma-separated) -->
                            <td class="px-4 py-3 text-sm text-gray-900 border-r border-gray-200 text-center font-medium">
                                @if($record->universe !== null && $record->universe !== '')
                                    @php
                                        $trimmedUniverse = trim($record->universe);
                                        if (is_numeric($trimmedUniverse)) {
                                            echo number_format($trimmedUniverse);
                                        } else {
                                            echo $trimmedUniverse;
                                        }
                                    @endphp
                                @endif
                            </td>
                            
                            <!-- Column 4: BASELINE - Calculated as Universe - Accomplishment -->
                            <td class="px-4 py-3 text-sm text-gray-900 border-r border-gray-200 text-center font-medium">
                                @if($record->universe !== null && $record->universe !== '' && $record->accomplishment !== null && $record->accomplishment !== '')
                                    @php
                                        $trimmedUniverse = trim($record->universe);
                                        $trimmedAccomplishment = trim($record->accomplishment);
                                        
                                        if (is_numeric($trimmedUniverse) && is_numeric($trimmedAccomplishment)) {
                                            $baseline = intval($trimmedUniverse) - intval($trimmedAccomplishment);
                                            echo number_format($baseline);
                                        } else {
                                            echo '';
                                        }
                                    @endphp
                                @endif
                            </td>
                            
                            <!-- Column 5: ACCOMPLISHMENT - Shows accomplishment values for each office (comma-separated) -->
                            <td class="px-4 py-3 text-sm text-gray-900 border-r border-gray-200 text-center font-medium">
                                @if($record->accomplishment !== null && $record->accomplishment !== '')
                                    @php
                                        $trimmedAccomplishment = trim($record->accomplishment);
                                        if (is_numeric($trimmedAccomplishment)) {
                                            echo number_format($trimmedAccomplishment);
                                        } else {
                                            echo $trimmedAccomplishment;
                                        }
                                    @endphp
                                @endif
                            </td>
                            
                            <!-- Column 6: ACTIONS - Edit and delete buttons for record management -->
                            <td class="px-4 py-3 text-sm text-gray-900 text-center hidden" id="actionsCell-{{ $record->id }}">
                                <div class="flex items-center justify-center space-x-2">
                                    <button onclick="editRecord({{ $record->id }})"
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg transition-all duration-200 text-xs font-medium shadow hover:shadow-lg"
                                        title="Edit Record">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deleteRecord({{ $record->id }})"
                                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-lg transition-all duration-200 text-xs font-medium shadow hover:shadow-lg"
                                        title="Delete Record">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        @endif
                    </tr>
                    @if($hasChildren)
                        @php displayHierarchicalRecords($record->children, $level + 1, $number, $activityCounter, $subActivityCounters, $subSubActivityCounters, $projectCounter); @endphp
                    @endif
                    @php
                            }
                        }
                    @endphp

                    @if(count($nraRecords) > 0)
                        @php displayHierarchicalRecords($nraRecords); @endphp
                    @else
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
                                    <p class="text-lg font-medium">No records found</p>
                                    <p class="text-sm text-gray-400">Start by adding your first NRA record</p>
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
                        <span class="font-medium">{{ $nraRecords->count() }}</span> 
                        {{ $nraRecords->count() == 1 ? 'record' : 'records' }} total
                        <span class="text-gray-400">|</span>
                        <span class="text-blue-600 font-medium">
                            {{ $nraRecords->where('record_type', 'program')->count() }} program{{ $nraRecords->where('record_type', 'program')->count() == 1 ? '' : 's' }}
                        </span>
                        <span class="text-gray-400">|</span>
                        <span class="text-green-600 font-medium">
                            {{ $nraRecords->where('record_type', 'project')->count() }} project{{ $nraRecords->where('record_type', 'project')->count() == 1 ? '' : 's' }}
                        </span>
                        <span class="text-gray-400">|</span>
                        <span class="text-purple-600 font-medium">
                            {{ $nraRecords->where('record_type', 'activity')->count() }} activit{{ $nraRecords->where('record_type', 'activity')->count() == 1 ? 'y' : 'ies' }}
                        </span>
                        @if($nraRecords->where('record_type', 'activity')->whereNotNull('parent_id')->count() > 0)
                            <span class="text-gray-400">|</span>
                            <span class="text-orange-600 font-medium">
                                {{ $nraRecords->where('record_type', 'activity')->whereNotNull('parent_id')->count() }} sub-activit{{ $nraRecords->where('record_type', 'activity')->whereNotNull('parent_id')->count() == 1 ? 'y' : 'ies' }}
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

@include('nra.partials.scripts')
@include('nra.partials.toggle')
@include('nra.partials.modal')