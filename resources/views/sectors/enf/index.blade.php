@extends('layouts.app')

@php
    $pageTitle = 'ENF';
@endphp

@section('content')
    <style>
        .table-container {
            height: 100%;
            overflow-y: auto;
            position: relative;
        }

        .thead-clone {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            z-index: 20;
            background: linear-gradient(to bottom, #f9fafb, #e5e7eb);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .thead-clone table {
            border-collapse: collapse;
            width: 100%;
        }

        .thead-clone th {
            background: linear-gradient(to bottom, #f9fafb, #e5e7eb);
        }
    </style>

    <!-- Data Table Container -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <!-- Table Header with Summary Statistics -->
        <div class="bg-gradient-to-r from-slate-700 to-slate-800 text-white p-3 shadow-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-table mr-2"></i>
                    <h2 class="text-sm font-bold">ENF Data Table</h2>
                </div>
                <div class="flex items-center space-x-2">
                    <!-- Search Input -->
                    <div class="relative flex items-center">
                        <input type="text" id="searchInput" placeholder="Search PPA name or indicators..."
                            class="bg-white text-slate-700 px-3 py-1.5 pl-10 rounded-lg transition-all duration-200 text-xs font-medium shadow hover:shadow-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 w-64"
                            onkeyup="handleSearch(event)">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <!-- Navigation Arrows -->
                        <div class="items-center ml-2 space-x-1 hidden" id="navigationArrows">
                            <button onclick="navigateToPrevMatch()" id="prevMatchBtn"
                                class="bg-gray-200 hover:bg-gray-300 text-slate-700 px-3 py-2 rounded-lg transition-all duration-200 flex items-center text-xs font-medium shadow hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
                                disabled>
                                <i class="fas fa-chevron-up"></i>
                            </button>
                            <button onclick="navigateToNextMatch()" id="nextMatchBtn"
                                class="bg-gray-200 hover:bg-gray-300 text-slate-700 px-3 py-2 rounded-lg transition-all duration-200 flex items-center text-xs font-medium shadow hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
                                disabled>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                        </div>
                    </div>
                    <!-- Toggle Actions Column Button -->
                    <button onclick="toggleActionsColumn()" id="toggleActionsBtn"
                        class="bg-white hover:bg-gray-100 text-slate-700 px-3 py-1.5 rounded-lg transition-all duration-200 flex items-center text-xs font-medium shadow hover:shadow-lg">
                        <i class="fas fa-eye mr-2"></i>
                        <span id="toggleActionsText">Show Actions</span>
                    </button>
                    <!-- Add New Record Button -->
                    <button onclick="openCreateModal()"
                        class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg transition-all duration-200 text-xs font-medium shadow hover:shadow-lg hover:border-2 hover:border-white flex items-center">
                        <i class="fas fa-plus mr-2"></i>
                        Add New Record
                    </button>
                </div>
            </div>
        </div>

        <!-- Table Container -->
        <div class="table-container overflow-x-auto">
            <table class="w-full border-collapse border-2 border-gray-400 shadow-lg rounded-lg overflow-hidden">
                <!-- Table Head -->
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100 text-gray-700 shadow-md border-b border-gray-300"
                    id="original-thead">
                    <!-- First row: main headers -->
                    <tr class="border-b border-gray-300">
                        <th rowspan="2"
                            class="px-2 py-0.5 text-center text-[12px] font-bold uppercase tracking-wider border-r border-gray-300 bg-gradient-to-b from-gray-100 to-gray-200">
                            <div class="flex items-center justify-center">
                                <i class="fas fa-list-ul mr-2 text-gray-500"></i>
                                PPA & Output Indicators
                            </div>
                        </th>
                        <th rowspan="2"
                            class="px-2 py-0.5 text-center text-[12px] font-bold uppercase tracking-wider border-r border-gray-300 bg-gradient-to-b from-gray-100 to-gray-200">
                            <div class="flex items-center justify-center">
                                <i class="fas fa-building mr-2 text-gray-500"></i>
                                OFFICE
                            </div>
                        </th>
                        <th rowspan="2"
                            class="px-2 py-0.5 text-center text-[12px] font-bold uppercase tracking-wider border-r border-gray-300 bg-gradient-to-b from-gray-100 to-gray-200">
                            <div class="flex items-center justify-center">
                                <i class="fas fa-globe mr-2 text-gray-500"></i>
                                UNIVERSE
                            </div>
                        </th>
                        <th rowspan="2"
                            class="px-2 py-0.5 text-center text-[12px] font-bold uppercase tracking-wider border-r border-gray-300 bg-gradient-to-b from-gray-100 to-gray-200">
                            <div class="flex items-center justify-center">
                                <i class="fas fa-chart-line mr-2 text-gray-500"></i>
                                BASELINE
                            </div>
                        </th>
                        <th colspan="2"
                            class="px-2 py-0.5 text-center text-[12px] font-bold uppercase tracking-wider border-r border-gray-300 bg-gradient-to-b from-gray-100 to-gray-200">
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
                            class="px-2 py-1 text-center text-[12px] font-bold uppercase tracking-wider border-r border-gray-300 bg-gradient-to-b from-gray-100 to-gray-200 hidden"
                            id="targetHeader">
                            <div class="flex items-center justify-center">
                                <i class="fas fa-bullseye mr-2 text-gray-500"></i>
                                TARGET
                            </div>
                        </th>

                        <th rowspan="2"
                            class="px-2 py-1 text-center text-[12px] font-bold uppercase tracking-wider border-r border-gray-300 bg-gradient-to-b from-gray-100 to-gray-200 hidden"
                            id="remarksHeader">
                            <div class="flex items-center justify-center">
                                <i class="fas fa-comment mr-2 text-gray-500"></i>
                                REMARKS
                            </div>
                        </th>
                        <th rowspan="2"
                            class="px-2 py-1 text-center text-[12px] font-bold uppercase tracking-wider border-r border-gray-300 bg-gradient-to-b from-gray-100 to-gray-200 hidden"
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
                            class="px-1 py-0.5 text-center text-[8px] font-bold uppercase tracking-wider border-r border-gray-300 bg-gradient-to-b from-gray-100 to-gray-200">
                            Past Years (2022-{{ date('Y') - 1 }})
                        </th>
                        <th
                            class="px-1 py-0.5 text-center text-[8px] font-bold uppercase tracking-wider border-r border-gray-300 bg-gradient-to-b from-gray-100 to-gray-200">
                            Current Year ({{ date('Y') }})
                        </th>
                        <!-- Target year sub-headers - use the $targetYears already calculated above -->
                        @if (count($targetYears) > 0)
                            @foreach ($targetYears as $year)
                                <th
                                    class="px-1 py-0.5 text-center text-[12px] font-bold uppercase tracking-wider border-r border-gray-300 bg-gradient-to-b from-gray-100 to-gray-200 hidden">
                                    {{ $year }}
                                </th>
                            @endforeach
                        @else
                            <th
                                class="px-2 py-2 text-center text-xs font-bold uppercase tracking-wider border-r border-gray-300 bg-gradient-to-b from-gray-100 to-gray-200 hidden">
                                No Targets
                            </th>
                        @endif
                    </tr>
                </thead>

                <!-- Table Body -->
                <tbody class="bg-gradient-to-b from-gray-50 to-gray-100 divide-y divide-gray-300" id="sortableTableBody">

                </tbody>

                <!-- Table Footer Stats -->
                <tfoot>
                    <tr>
                        <td colspan="100%" class="p-3 bg-gray-50 border-t border-gray-300">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                @php
                                    $totalRecords = $enfs->count();
                                    $recordsWithIndicator = $enfs->whereNotNull('indicator_id')->count();
                                    $currentYear = date('Y');
                                    $recordsWithNoCurrentYearAccomplishment = 0;

                                    foreach ($enfs as $enf) {
                                        // Only check records that have an indicator
                                        if ($enf->indicator_id === null) {
                                            continue;
                                        }

                                        $hasCurrentYearAccomplishment = false;
                                        $accomplishment = $enf->accomplishment ?? [];
                                        $officeIds = $enf->office_id ?? [];

                                        // Check if any office has a non-zero current year accomplishment
                                        if (is_array($accomplishment) && is_array($officeIds)) {
                                            foreach ($officeIds as $officeId) {
                                                if (
                                                    isset($accomplishment[$officeId]) &&
                                                    is_array($accomplishment[$officeId]) &&
                                                    isset($accomplishment[$officeId][$currentYear]) &&
                                                    $accomplishment[$officeId][$currentYear] > 0
                                                ) {
                                                    $hasCurrentYearAccomplishment = true;
                                                    break;
                                                }
                                            }
                                        }

                                        if (!$hasCurrentYearAccomplishment) {
                                            $recordsWithNoCurrentYearAccomplishment++;
                                        }
                                    }
                                @endphp

                                <!-- Total Records Card -->
                                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-md p-3 text-white">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-blue-100 text-xs font-medium uppercase tracking-wider">Total Records</p>
                                            <p class="text-2xl font-bold mt-1">{{ $totalRecords }}</p>
                                        </div>
                                        <div class="bg-white/20 rounded-full p-3">
                                            <i class="fas fa-database text-xl"></i>
                                        </div>
                                    </div>
                                </div>

                                <!-- Records with Indicator Card -->
                                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-md p-3 text-white">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-green-100 text-xs font-medium uppercase tracking-wider">Records with Indicator</p>
                                            <p class="text-2xl font-bold mt-1">{{ $recordsWithIndicator }}</p>
                                        </div>
                                        <div class="bg-white/20 rounded-full p-3">
                                            <i class="fas fa-check-circle text-xl"></i>
                                        </div>
                                    </div>
                                </div>

                                <!-- Records with No Current Year Accomplishment Card -->
                                <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-md p-3 text-white">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-orange-100 text-xs font-medium uppercase tracking-wider">No {{ $currentYear }}
                                                Accomplishment</p>
                                            <p class="text-2xl font-bold mt-1">{{ $recordsWithNoCurrentYearAccomplishment }}</p>
                                        </div>
                                        <div class="bg-white/20 rounded-full p-3">
                                            <i class="fas fa-exclamation-triangle text-xl"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

    </div>
@endsection

@include('partials.toggle')
@include('partials.modal', [
    'offices' => $offices ?? [],
    'ppas' => $ppas ?? [],
    'sectorType' => 3,
    'sectorName' => 'ENF',
    'saveButtonDisabled' => true,
])

<script>
    let currentEditId = null;
    let ppas = [];
    let indicators = [];
    let offices = @json($offices ?? []);
    let recordTypes = [];

    // Initialize the page
    document.addEventListener('DOMContentLoaded', function() {
        loadEnfData();
        loadFormData();
        initStickyHeader();
    });

    // Sticky header functionality
    function initStickyHeader() {
        const tableContainer = document.querySelector('.table-container');
        const originalThead = document.getElementById('original-thead');
        const originalTable = originalThead.closest('table');

        if (!tableContainer || !originalThead || !originalTable) return;

        // Clone the thead
        const clonedThead = originalThead.cloneNode(true);
        clonedThead.id = 'cloned-thead';

        // Sync actions column visibility with original header
        const originalActionsHeader = originalThead.querySelector('#actionsHeader');
        const clonedActionsHeader = clonedThead.querySelector('#actionsHeader');

        if (originalActionsHeader && clonedActionsHeader) {
            if (!originalActionsHeader.classList.contains('hidden')) {
                clonedActionsHeader.classList.remove('hidden');
            }
        }

        // Create container for cloned header
        const headerClone = document.createElement('div');
        headerClone.className = 'thead-clone';
        headerClone.style.display = 'none';

        // Create table for cloned header
        const clonedTable = document.createElement('table');
        clonedTable.className = 'w-full border-collapse border-2 border-gray-400 shadow-lg rounded-lg overflow-hidden';
        clonedTable.appendChild(clonedThead);

        headerClone.appendChild(clonedTable);
        tableContainer.appendChild(headerClone);

        // Function to sync column widths
        function syncColumnWidths() {
            const originalThs = originalThead.querySelectorAll('th');
            const clonedThs = clonedThead.querySelectorAll('th');

            originalThs.forEach((originalTh, index) => {
                if (clonedThs[index]) {
                    const computedStyle = window.getComputedStyle(originalTh);
                    clonedThs[index].style.width = computedStyle.width;
                    clonedThs[index].style.minWidth = computedStyle.minWidth;
                    clonedThs[index].style.maxWidth = computedStyle.maxWidth;
                    clonedThs[index].style.padding = computedStyle.padding;
                    clonedThs[index].style.margin = computedStyle.margin;
                }
            });

            // Sync table width
            const originalTableWidth = originalTable.offsetWidth;
            clonedTable.style.width = originalTableWidth + 'px';
        }

        // Initial sync
        setTimeout(syncColumnWidths, 100);

        // Sync on window resize
        window.addEventListener('resize', syncColumnWidths);

        // Handle scroll
        tableContainer.addEventListener('scroll', function() {
            const scrollTop = tableContainer.scrollTop;
            const theadHeight = originalThead.offsetHeight;

            if (scrollTop > theadHeight) {
                headerClone.style.display = 'block';
                headerClone.style.top = scrollTop + 'px';
                syncColumnWidths(); // Resync when showing
            } else {
                headerClone.style.display = 'none';
            }
        });

        // Handle horizontal scroll
        tableContainer.addEventListener('scroll', function() {
            const scrollLeft = tableContainer.scrollLeft;
            headerClone.style.transform = `translateX(${-scrollLeft}px)`;
        });
    }

    // Load ENF data for the table
    function loadEnfData() {
        fetch('/enf', {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                renderTable(data);
            })
            .catch(error => console.error('Error loading ENF data:', error));
    }

    // Load form data (PPAs and Indicators)
    function loadFormData() {
        return fetch('/enf/create', {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                ppas = data.ppas;
                indicators = data.indicators;
                offices = data.offices;
                recordTypes = data.recordTypes;
                populateSelects();
                return data; // Return data for promise chaining
            })
            .catch(error => {
                console.error('Error loading form data:', error);
                throw error; // Re-throw to be caught by caller
            });
    }

    // Populate select dropdowns
    function populateSelects() {
        const ppaSelect = document.getElementById('ppa_id');
        const recordTypeSelect = document.getElementById('record_type_id');
        const indicatorSelect = document.getElementById('indicator_id');
        const ppaList = document.getElementById('ppa-list');
        const indicatorList = document.getElementById('indicator-list');

        // Clear existing options (with null checks)
        if (ppaSelect) {
            ppaSelect.innerHTML =
                '<option value="">Select PPA</option><option value="new" class="bg-green-100 text-green-800 font-semibold">+ Register New PPA</option>';
        }
        if (recordTypeSelect) {
            recordTypeSelect.innerHTML = '<option value="">Select Record Type</option>';
        }

        // Add PPA options (only ENF PPAs - types_id = 3)
        if (ppaSelect) {
            ppas.forEach(ppa => {
                // Only show PPAs that belong to ENF (types_id = 3)
                if (ppa.types_id === 3) {
                    const option = document.createElement('option');
                    option.value = ppa.id;
                    option.textContent = ppa.name || `PPA ${ppa.id}`;
                    ppaSelect.appendChild(option);
                }
            });
        }

        // Add Record Type options
        if (recordTypeSelect) {
            recordTypes.forEach(recordType => {
                const option = document.createElement('option');
                option.value = recordType.id;
                option.textContent = recordType.name || `Record Type ${recordType.id}`;
                recordTypeSelect.appendChild(option);
            });
        }

        // Add event listener for indicator textarea
        const indicatorTextarea = document.getElementById('indicator_id');
        if (indicatorTextarea) {
            indicatorTextarea.addEventListener('input', function() {
                const officeSection = document.getElementById('officeSection');
                if (officeSection) {
                    if (this.value.trim() === '') {
                        officeSection.style.display = 'none';
                    } else {
                        officeSection.style.display = 'block';
                    }
                }

                // Update button visibility based on indicator
                updateButtonVisibility();
            });

            // Add event listeners for form validation
            const recordTypeSelect = document.getElementById('record_type_id');

            if (recordTypeSelect) {
                recordTypeSelect.addEventListener('change', updateButtonVisibility);
            }

            if (ppaSelect) {
                ppaSelect.addEventListener('change', function() {
                    handlePpaSelection();
                    updateButtonVisibility();
                });
            }

            // Add event listener for parent activity selection
            const parentActivitySelect = document.getElementById('parent_activity_id');
            if (parentActivitySelect) {
                parentActivitySelect.addEventListener('change', updateButtonVisibility);
            }

            // Add event listener for new PPA name textarea
            const newPpaTextarea = document.getElementById('new_ppa_name');
            if (newPpaTextarea) {
                newPpaTextarea.addEventListener('input', updateButtonVisibility);
            }
            // Initially hide office section and set button visibility
            const officeSection = document.getElementById('officeSection');
            if (officeSection) {
                officeSection.style.display = 'none';
            }
            updateButtonVisibility();
        }
    }

    // Handle PPA selection
    function handlePpaSelection() {
        const ppaSelect = document.getElementById('ppa_id');
        const newPpaSection = document.getElementById('newPpaSection');

        if (ppaSelect && newPpaSection) {
            if (ppaSelect.value === 'new') {
                newPpaSection.classList.remove('hidden');
            } else {
                newPpaSection.classList.add('hidden');
                // Clear the text area
                const newPpaTextarea = document.getElementById('new_ppa_name');
                if (newPpaTextarea) {
                    newPpaTextarea.value = '';
                }
            }
        }
    }

    // Update button visibility based on form validation
    function updateButtonVisibility() {
        const recordType = document.getElementById('record_type_id');
        const ppaSelect = document.getElementById('ppa_id');
        const newPpaName = document.getElementById('new_ppa_name');
        const parentActivitySelect = document.getElementById('parent_activity_id');
        const indicator = document.getElementById('indicator_id');
        const nextBtn = document.getElementById('nextBtn');
        const saveBtn = document.getElementById('saveBtn');
        const step2Section = document.getElementById('step2Section');

        // Force show save button in edit mode
        const isEditMode = currentEditId !== null;
        if (isEditMode && saveBtn) {
            saveBtn.disabled = false;
            saveBtn.classList.remove('opacity-50', 'cursor-not-allowed', 'bg-gray-400');
            saveBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
            saveBtn.classList.remove('hidden');

            // Hide Next button in edit mode
            if (nextBtn) {
                nextBtn.classList.add('hidden');
            }
            return; // Exit early for edit mode
        }

        if (recordType && ppaSelect && indicator && nextBtn && saveBtn) {
            const isRecordTypeValid = recordType.value.trim() !== '';
            const isPpaValid = (ppaSelect.value.trim() !== '' && ppaSelect.value !== 'new') ||
                (ppaSelect.value === 'new' && newPpaName && newPpaName.value.trim() !== '');

            // Check if parent activity is required and valid
            const selectedRecordType = recordType.value;
            let isParentActivityRequired = false;
            let isParentActivityValid = true;

            // Parent activity is required for record types 4, 5, and 6
            if (selectedRecordType == '4' || selectedRecordType == '5' || selectedRecordType == '6') {
                isParentActivityRequired = true;
                isParentActivityValid = parentActivitySelect && parentActivitySelect.value.trim() !== '';
            }

            const hasIndicator = indicator.value.trim() !== '';
            const selectedOffices = document.querySelectorAll('input[name="office_id[]"]:checked').length;
            const hasOfficeSelection = selectedOffices > 0;

            // Check if Record Type AND PPA are valid (Parent Activity is optional)
            if (!isRecordTypeValid || !isPpaValid || (isParentActivityRequired && !isParentActivityValid)) {
                // Basic requirements not met - show Save button (disabled)
                saveBtn.disabled = true;
                saveBtn.classList.add('opacity-50', 'cursor-not-allowed', 'bg-gray-400');
                saveBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                saveBtn.classList.remove('hidden');

                // Hide Step 2 if it's currently visible
                if (step2Section) {
                    step2Section.classList.add('hidden');
                }
            } else {
                // Basic requirements met (Record Type and PPA filled)
                if (hasIndicator) {
                    // Indicator is filled - hide Save button
                    saveBtn.classList.add('hidden');

                    // Show Next button only when both indicator AND office are selected
                    if (hasOfficeSelection) {
                        // Both indicator and office filled - show Next button (enabled)
                        nextBtn.disabled = false;
                        nextBtn.classList.remove('opacity-50', 'cursor-not-allowed', 'bg-gray-400');
                        nextBtn.classList.add('bg-green-600', 'hover:bg-green-700');
                        nextBtn.classList.remove('hidden');
                    } else {
                        // Indicator filled but no office - hide Next button
                        nextBtn.classList.add('hidden');
                    }
                } else {
                    // Indicator not filled - show Save button (enabled)
                    saveBtn.disabled = false;
                    saveBtn.classList.remove('opacity-50', 'cursor-not-allowed', 'bg-gray-400');
                    saveBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
                    saveBtn.classList.remove('hidden');

                    // Hide Next button
                    nextBtn.classList.add('hidden');
                }
            }
        }
    }

    // Go to Step 2
    function goToStep2() {
        const step2Section = document.getElementById('step2Section');
        const nextBtn = document.getElementById('nextBtn');
        const saveBtn = document.getElementById('saveBtn');

        if (step2Section && nextBtn && saveBtn) {
            // Generate universe table based on selected offices
            generateUniverseTable();

            // Show Step 2 section and update buttons
            step2Section.classList.remove('hidden');
            nextBtn.classList.add('hidden');
            saveBtn.classList.remove('hidden');
        }
    }

    // Generate combined data table for specific period
    function generateUniverseTable() {
        // Generate default 2022-2028 table
        generatePeriodTable('2022-2028', true);
    }

    // Generate table for specific period
    function generatePeriodTable(period, isDefault = false) {
        const checkedOffices = Array.from(document.querySelectorAll(
            'input[name="office_id[]"]:checked'));
        const years = getYearsForPeriod(period);

        if (isDefault) {
            const tbody = document.getElementById('dataTableBody_2022_2028');
            if (!tbody) return;
            tbody.innerHTML = '';
            generateTableRows(tbody, checkedOffices, period, years);
        } else {
            // Create new table for additional period
            const container = document.getElementById('additionalPeriodsContainer');
            const tableId = `table_${period.replace('-', '_')}`;

            // Check if table already exists
            if (document.getElementById(tableId)) return;

            const tableDiv = document.createElement('div');
            tableDiv.className = 'mb-6';
            tableDiv.id = tableId;
            tableDiv.innerHTML = `
                <div class="flex items-center justify-between mb-3">
                    <h5 class="text-md font-semibold text-gray-700">${period} Data</h5>
                    <button type="button" onclick="removePeriodTable('${period}')" class="text-red-500 hover:text-red-700 text-sm">
                        <i class="fas fa-times mr-1"></i>Remove
                    </button>
                </div>
                <div class="overflow-x-auto border border-gray-200 rounded-lg shadow-sm">
                    <table class="min-w-full">
                        <thead class="bg-gradient-to-r from-indigo-50 to-purple-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Office</th>
                                <th class="px-2 py-2 text-center text-xs font-semibold text-blue-600 uppercase tracking-wider">Universe</th>
                                ${years.map(year => `<th class="px-2 py-2 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">${year}</th>`).join('')}
                            </tr>
                        </thead>
                        <tbody id="dataTableBody_${period.replace('-', '_')}" class="bg-white divide-y divide-gray-100">
                            <!-- Dynamic rows will be inserted here -->
                        </tbody>
                    </table>
                </div>
            `;
            container.appendChild(tableDiv);

            const tbody = document.getElementById(`dataTableBody_${period.replace('-', '_')}`);
            generateTableRows(tbody, checkedOffices, period, years);
        }
    }

    // Get years for specific period
    function getYearsForPeriod(period) {
        switch (period) {
            case '2022-2028':
                return ['2022', '2023', '2024', '2025', '2026', '2027', '2028'];
            case '2028-2034':
                return ['2028', '2029', '2030', '2031', '2032', '2033', '2034'];
            case '2034-2040':
                return ['2034', '2035', '2036', '2037', '2038', '2039', '2040'];
            default:
                return [];
        }
    }

    // Generate table rows
    function generateTableRows(tbody, checkedOffices, period, years) {
        checkedOffices.forEach(checkbox => {
            const officeId = checkbox.value;
            const officeLabel = checkbox.nextElementSibling.textContent;

            // Single row with all data types for this office
            const officeRow = document.createElement('tr');
            officeRow.innerHTML = `
                <td class="px-4 py-3 text-[12px] font-medium text-gray-900">${officeLabel}</td>
                <td class="px-2 py-2 text-center">
                    <input type="number" name="universe[${officeId}]" class="w-full px-2 py-1 border border-gray-300 rounded text-center font-medium text-blue-600" placeholder="0" min="0" step="1" oninput="this.value = Math.floor(Math.max(0, this.value))">
                </td>
                ${years.map(year => `
                    <td class="px-2 py-3 text-center p-1" style="min-width: 80px;">
                        <div class="space-y-1">
                            <input type="number" name="accomplishment[${officeId}][${year}]" class="w-full px-2 py-1 border border-gray-300 rounded text-center text-xs text-green-600 font-medium" placeholder="A" min="0" step="1" oninput="this.value = Math.floor(Math.max(0, this.value))">
                            <input type="number" name="targets[${officeId}][${year}]" class="w-full px-2 py-1 border border-gray-300 rounded text-center text-xs text-purple-600 font-medium" placeholder="T" min="0" step="1" oninput="this.value = Math.floor(Math.max(0, this.value))">
                        </div>
                    </td>
                `).join('')}
            `;
            tbody.appendChild(officeRow);
        });
    }

    // Add period table (button click handler)
    function addPeriodTable(period) {
        generatePeriodTable(period, false);
    }

    // Remove period table
    function removePeriodTable(period) {
        const tableId = `table_${period.replace('-', '_')}`;
        const tableDiv = document.getElementById(tableId);
        if (tableDiv) {
            tableDiv.remove();
        }
    }

    // Refresh Step 2 content
    function refreshStep2() {
        // Get current selected offices
        const checkedOffices = Array.from(document.querySelectorAll(
            'input[name="office_id[]"]:checked'));

        // Regenerate all period tables
        generateUniverseTable();

        // Regenerate additional period tables if they exist
        const additionalContainer = document.getElementById('additionalPeriodsContainer');
        const existingTables = additionalContainer.querySelectorAll('[id^="table_"]');

        existingTables.forEach(tableDiv => {
            const periodId = tableDiv.id.replace('table_', '').replace('_', '-');
            generatePeriodTable(periodId, false);
        });
    }

    // Keep Step 2 tables in sync with office selections (preserve typed values)
    function captureStep2InputValues() {
        const values = {};
        const selectors = [
            '#step2Section input[name^="universe["]',
            '#step2Section input[name^="accomplishment["]',
            '#step2Section input[name^="targets["]'
        ];
        document.querySelectorAll(selectors.join(',')).forEach(input => {
            values[input.name] = input.value;
        });
        return values;
    }

    function restoreStep2InputValues(values) {
        Object.keys(values).forEach(name => {
            const safeName = name.replace(/"/g, '\\"');
            const input = document.querySelector(`#step2Section input[name="${safeName}"]`);
            if (input) input.value = values[name];
        });
    }

    function syncUniverseDataWithSelectedOffices() {
        const step2Section = document.getElementById('step2Section');
        if (!step2Section || step2Section.classList.contains('hidden')) return;

        const cachedValues = captureStep2InputValues();

        // Capture existing additional period tables
        const additionalContainer = document.getElementById('additionalPeriodsContainer');
        const existingPeriods = [];
        if (additionalContainer) {
            additionalContainer.querySelectorAll('[id^="table_"]').forEach(div => {
                const period = div.id.replace('table_', '').replace(/_/g, '-');
                existingPeriods.push(period);
            });
        }

        // Rebuild default table
        generateUniverseTable();

        // Rebuild additional period tables
        existingPeriods.forEach(period => {
            const tableId = `table_${period.replace('-', '_')}`;
            const existing = document.getElementById(tableId);
            if (existing) existing.remove();
            generatePeriodTable(period, false);
        });

        restoreStep2InputValues(cachedValues);
    }

    // Filter PPA dropdown based on selected record type
    function filterPpaByRecordType() {
        const recordTypeSelect = document.getElementById('record_type_id');
        const ppaSelect = document.getElementById('ppa_id');
        const loadingMessage = document.getElementById('ppaLoadingMessage');
        const selectedRecordTypeId = recordTypeSelect.value;

        // Show loading message
        loadingMessage.classList.remove('hidden');

        // Clear current PPA options (keep default and new option)
        ppaSelect.innerHTML = `
            <option value="">Select PPA</option>
            <option value="new" class="bg-green-100 text-green-800 font-semibold">+ Register New PPA</option>
        `;

        if (!selectedRecordTypeId) {
            loadingMessage.classList.add('hidden');
            return;
        }

        // Fetch PPAs for the selected record type
        fetch(`/api/enf/ppas?record_type_id=${selectedRecordTypeId}`)
            .then(response => response.json())
            .then(data => {
                // Handle both array and object data formats
                let ppaArray = [];

                if (Array.isArray(data)) {
                    // Data is already an array
                    ppaArray = data;
                } else if (typeof data === 'object' && data !== null) {
                    // Convert object to array (API returns object with numeric keys)
                    ppaArray = Object.values(data);
                } else {
                    console.error('Expected array or object but received:', typeof data, data);
                    loadingMessage.textContent = 'Invalid data format received';
                    loadingMessage.classList.add('text-red-500');
                    setTimeout(() => {
                        loadingMessage.classList.add('hidden');
                        loadingMessage.classList.remove('text-red-500');
                        loadingMessage.textContent =
                            'Loading PPAs for selected record type...';
                    }, 3000);
                    return;
                }

                // Add filtered PPAs to dropdown (only ENF PPAs - types_id = 3)
                ppaArray.forEach(ppa => {
                    // Only show PPAs that belong to ENF (types_id = 3)
                    if (ppa.types_id === 3) {
                        const option = document.createElement('option');
                        option.value = ppa.id;
                        option.title = ppa.name;
                        option.textContent = ppa.name.length > 100 ? ppa.name.substring(0, 100) + '...' :
                            ppa
                            .name;
                        ppaSelect.appendChild(option);
                    }
                });

                loadingMessage.classList.add('hidden');
            })
            .catch(error => {
                console.error('Error fetching PPAs:', error);
                loadingMessage.textContent = 'Error loading PPAs';
                loadingMessage.classList.add('text-red-500');
                setTimeout(() => {
                    loadingMessage.classList.add('hidden');
                    loadingMessage.classList.remove('text-red-500');
                    loadingMessage.textContent = 'Loading PPAs for selected record type...';
                }, 3000);
            });
    }

    // Toggle all offices selection
    function toggleAllOffices() {
        const selectAllCheckbox = document.getElementById('selectAllOffices');
        const officeCheckboxes = document.querySelectorAll('input[name="office_id[]"]');

        officeCheckboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
        });

        // Trigger validation to check office selection when indicator is not empty
        updateButtonVisibility();

        // Update button visibility if in Step 2
        const step2Section = document.getElementById('step2Section');
        if (step2Section && !step2Section.classList.contains('hidden')) {
            updateButtonVisibility();
            // If Step 2 is visible, regenerate tables for current office selection
            syncUniverseDataWithSelectedOffices();
        }
    }

    // Update select all checkbox state when individual checkboxes change
    document.addEventListener('change', function(e) {
        if (e.target && e.target.name === 'office_id[]' && e.target.id !== 'selectAllOffices') {
            const selectAllCheckbox = document.getElementById('selectAllOffices');
            const officeCheckboxes = document.querySelectorAll('input[name="office_id[]"]');
            const checkedCount = document.querySelectorAll('input[name="office_id[]"]:checked')
                .length;

            // Update select all checkbox based on individual selections
            if (checkedCount === 0) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            } else if (checkedCount === officeCheckboxes.length) {
                selectAllCheckbox.checked = true;
                selectAllCheckbox.indeterminate = false;
            } else {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = true;
            }

            // Update button visibility for office selection validation
            updateButtonVisibility();

            // If Step 2 is visible, update tables automatically
            syncUniverseDataWithSelectedOffices();
        }
    });

    // Add Data Tables button functionality
    function addDataTables() {
        const addDataTablesBtn = document.getElementById('addDataTablesBtn');

        // The table is already generated in generateUniverseTable(), so just hide the button
        addDataTablesBtn.classList.add('hidden');
    }

    // Render table data
    function renderTable(data) {
        const tbody = document.getElementById('sortableTableBody');
        tbody.innerHTML = '';

        // Reset counters before rendering to ensure numbering starts at 1
        resetCounters();

        // Track previous PPA name to handle consecutive duplicates only
        let previousPpaName = '';

        data.forEach(item => {
            const row = document.createElement('tr');
            row.setAttribute('data-record-id', item.id);

            // Get PPA name for consecutive duplicate checking
            const ppaName = item.ppa ? item.ppa.name : '';
            const isConsecutiveDuplicate = ppaName === previousPpaName && ppaName !== '';

            // Update previous PPA name for next iteration
            previousPpaName = ppaName;

            // Check if record has no indicator - merge row
            const hasNoIndicator = !item.indicator || item.indicator === '' || item
                .indicator === null || item
                .indicator === 'undefined';

            // Check if record type is PROGRAM for green background
            const isProgram = item.ppa && item.ppa.recordType && item.ppa.recordType.name ===
                'PROGRAM';

            if (hasNoIndicator) {
                // Merged row - calculate correct colspan
                const bgClass = isProgram ? 'bg-green-50' : 'bg-blue-50';
                row.className = `hover:bg-gray-50 transition-colors ${bgClass}`;

                // Calculate correct colspan: 6 base columns + target columns + remarks column (exclude actions)
                let colspan =
                    6; // PPA, Office, Universe, Baseline, 2 Accomplishment sub-columns

                // Add target columns if visible
                const targetHeader = document.getElementById('targetHeader');
                if (targetHeader && !targetHeader.classList.contains('hidden')) {
                    const targetColspan = parseInt(targetHeader.getAttribute('colspan') || '2');
                    colspan += targetColspan;
                }

                // Add remarks column if visible
                const remarksHeader = document.getElementById('remarksHeader');
                if (remarksHeader && !remarksHeader.classList.contains('hidden')) {
                    colspan += 1;
                }

                row.innerHTML = `
                    <td class="px-2 py-1 text-[8px] text-gray-900" colspan="${colspan}">
                        ${!isConsecutiveDuplicate ? getIndentedPPAName(item) : ''}
                        ${getIndentedIndicatorName(item)}
                        <div class="text-[12px] text-gray-600 mt-0.5 ${getIndentClass(item)}">
                            ${formatOfficeNames(item.office_id) !== 'N/A' ? '<span class="font-medium">Office:</span> ' + formatOfficeNames(item.office_id) : ''}
                            ${formatOfficeNames(item.office_id) !== 'N/A' && item.universe && Object.keys(item.universe).length > 0 ? ' | ' : ''}
                            ${item.universe && Object.keys(item.universe).length > 0 ? '<span class="font-medium">Universe:</span> ' + formatUniverseData(item.universe) : ''}
                            ${item.universe && Object.keys(item.universe).length > 0 && item.accomplishment && Object.keys(item.accomplishment).length > 0 ? ' | ' : ''}
                            ${item.accomplishment && Object.keys(item.accomplishment).length > 0 ? '<span class="font-medium">Accomplishment:</span> ' + formatJsonData(item.accomplishment) : ''}
                            ${item.accomplishment && Object.keys(item.accomplishment).length > 0 && item.targets && Object.keys(item.targets).length > 0 ? ' | ' : ''}
                            ${item.targets && Object.keys(item.targets).length > 0 ? '<span class="font-medium">Targets:</span> ' + formatJsonData(item.targets) : ''}
                            ${item.targets && Object.keys(item.targets).length > 0 && item.remarks ? ' | ' : ''}
                            ${item.remarks ? '<span class="font-medium">Remarks:</span> ' + item.remarks : ''}
                        </div>
                    </td>
                    <td class="px-2 py-1 text-xs text-gray-900 hidden actions-column">
                        <div class="flex justify-center space-x-2">
                            <button onclick="editRecord(${item.id})" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deleteRecord(${item.id})" class="text-red-600 hover:text-red-800">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                `;
            } else {
                // Normal row - show all data separately
                const bgClass = isProgram ? 'bg-green-50' : '';
                row.className = `hover:bg-gray-50 transition-colors ${bgClass}`;

                row.innerHTML = `
                    <td class="px-2 py-1 text-[8px] text-gray-900">
                        ${!isConsecutiveDuplicate ? getIndentedPPAName(item) : ''}
                        ${getIndentedIndicatorName(item) || '<div class="text-[12px] text-gray-400 mt-0.5">No indicator</div>'}
                    </td>
                    <td class="px-2 py-1 text-[12px] text-gray-900">
                        ${formatOfficeNames(item.office_id)}
                    </td>
                    <td class="px-2 py-1 text-[12px] text-gray-900 text-center">
                        ${item.universe && Object.keys(item.universe).length > 0 ? formatUniverseData(item.universe, item.targets) : ''}
                    </td>
                    <td class="px-2 py-1 text-[12px] text-gray-900 text-center">
                        ${calculateBaseline(item.universe, item.accomplishment, item.office_id)}
                    </td>
                    <td class="px-2 py-1 text-[12px] text-gray-900 text-center">
                        ${formatAccomplishmentPerOffice(item.accomplishment, item.office_id)}
                    </td>
                    <td class="px-2 py-1 text-[12px] text-gray-900 text-center">
                        ${getCurrentYearAccomplishment(item.accomplishment, item.office_id)}
                    </td>
                    <td class="px-2 py-1 text-[12px] text-gray-900 hidden remarks-column">${item.remarks || '-'}</td>
                    <td class="px-2 py-1 text-[12px] text-gray-900 hidden actions-column">
                        <div class="flex justify-center space-x-2">
                            <button onclick="editRecord(${item.id})" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deleteRecord(${item.id})" class="text-red-600 hover:text-red-800">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                `;
            }

            tbody.appendChild(row);
        });
    }

    // Format number with commas (e.g., 1000000 -> 1,000,000)
    function formatNumberWithCommas(num) {
        if (num === 0 || num === '0') return '0';
        if (num === null || num === undefined) return '0';
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    // Format JSON data for display
    function formatJsonData(data) {
        if (!data || Object.keys(data).length === 0) return '-';
        return Object.entries(data)
            .map(([year, value]) => `${year}: ${formatNumberWithCommas(value)}`)
            .join(', ');
    }

    // Update merged row colspan when target columns are toggled
    function updateMergedRowColspan() {
        const mergedRows = document.querySelectorAll('tr[data-merged-row="true"]');
        const targetHeader = document.getElementById('targetHeader');
        const isTargetVisible = !targetHeader.classList.contains('hidden');

        mergedRows.forEach(row => {
            const targetCell = row.querySelector('.target-column');
            if (targetCell) {
                if (isTargetVisible) {
                    targetCell.colSpan = "{{ count($targetYears) }}";
                } else {
                    targetCell.colSpan = "2"; // Merge with remarks column when targets hidden
                }
            }
        });
    }

    // Global counters for ordered list numbering
    let programCounter = 0;
    let projectCounter = {};
    let mainActivityCounter = {};
    let majorActivityCounter = {};
    let subActivityCounter = {};

    // Reset all counters before rendering table
    function resetCounters() {
        programCounter = 0;
        projectCounter = {};
        mainActivityCounter = {};
        majorActivityCounter = {};
        subActivityCounter = {};
    }

    // Get indented PPA name based on record type with ordered list numbering
    function getIndentedPPAName(item) {
        const ppaName = item.ppa?.name || item.ppa_name || 'N/A';
        const recordType = item.ppa?.record_type?.name || item.record_type_name || '';

        let numbering = '';
        let className = 'font-semibold text-sm';

        // Simplified approach: use current program counter and track hierarchy by order
        switch (recordType.toUpperCase()) {
            case 'PROGRAM':
                programCounter++;
                numbering = `${programCounter}. `;
                className = 'font-bold text-sm';
                // Reset all child counters for new program
                projectCounter[programCounter] = 0;
                mainActivityCounter[programCounter] = {};
                majorActivityCounter[programCounter] = {};
                subActivityCounter[programCounter] = {};
                break;
            case 'PROJECT':
                if (!projectCounter[programCounter]) {
                    projectCounter[programCounter] = 0;
                }
                projectCounter[programCounter]++;
                numbering = `${programCounter}.${projectCounter[programCounter]}. `;
                className = 'font-semibold text-xs ml-6';
                // Reset child counters for new project
                mainActivityCounter[programCounter][projectCounter[programCounter]] = 0;
                majorActivityCounter[programCounter][projectCounter[programCounter]] = {};
                subActivityCounter[programCounter][projectCounter[programCounter]] = {};
                break;
            case 'MAIN ACTIVITY':
                if (!mainActivityCounter[programCounter] || !mainActivityCounter[programCounter][
                        projectCounter[
                            programCounter]
                    ]) {
                    mainActivityCounter[programCounter][projectCounter[programCounter]] = 0;
                }
                mainActivityCounter[programCounter][projectCounter[programCounter]]++;
                numbering =
                    `${programCounter}.${projectCounter[programCounter]}.${mainActivityCounter[programCounter][projectCounter[programCounter]]}. `;
                className = 'font-semibold text-xs ml-12';
                // Reset child counters for new main activity
                majorActivityCounter[programCounter][projectCounter[programCounter]][
                    mainActivityCounter[programCounter]
                    [projectCounter[programCounter]]
                ] = 0;
                subActivityCounter[programCounter][projectCounter[programCounter]][mainActivityCounter[
                    programCounter][
                    projectCounter[programCounter]
                ]] = {};
                break;
            case 'MAJOR ACTIVITY':
                const currentMain = mainActivityCounter[programCounter][projectCounter[programCounter]];
                if (!majorActivityCounter[programCounter] || !majorActivityCounter[programCounter][
                        projectCounter[
                            programCounter]
                    ] || !majorActivityCounter[programCounter][projectCounter[programCounter]][
                        currentMain
                    ]) {
                    majorActivityCounter[programCounter][projectCounter[programCounter]][currentMain] =
                        0;
                }
                majorActivityCounter[programCounter][projectCounter[programCounter]][currentMain]++;
                numbering =
                    `${programCounter}.${projectCounter[programCounter]}.${currentMain}.${majorActivityCounter[programCounter][projectCounter[programCounter]][currentMain]}. `;
                className = 'font-medium text-xs ml-18';
                // Reset child counters for new major activity
                subActivityCounter[programCounter][projectCounter[programCounter]][currentMain][
                    majorActivityCounter[
                        programCounter][projectCounter[programCounter]][currentMain]
                ] = 0;
                break;
            case 'SUB ACTIVITY':
                const currentMainForSub = mainActivityCounter[programCounter][projectCounter[
                    programCounter]];
                const currentMajor = majorActivityCounter[programCounter][projectCounter[
                    programCounter]][
                    currentMainForSub
                ];
                if (!subActivityCounter[programCounter] || !subActivityCounter[programCounter][
                        projectCounter[
                            programCounter]
                    ] || !subActivityCounter[programCounter][projectCounter[programCounter]][
                        currentMainForSub
                    ] || !subActivityCounter[programCounter][projectCounter[programCounter]][
                        currentMainForSub
                    ][
                        currentMajor
                    ]) {
                    subActivityCounter[programCounter][projectCounter[programCounter]][
                        currentMainForSub
                    ][
                        currentMajor
                    ] = 0;
                }
                subActivityCounter[programCounter][projectCounter[programCounter]][currentMainForSub][
                    currentMajor
                ]++;
                numbering =
                    `${programCounter}.${projectCounter[programCounter]}.${currentMainForSub}.${currentMajor}.${subActivityCounter[programCounter][projectCounter[programCounter]][currentMainForSub][currentMajor]}. `;
                className = 'font-medium text-[12px] ml-24';
                break;
            case 'ACTIONABLE TASK':
                const currentMainForTask = mainActivityCounter[programCounter][projectCounter[
                    programCounter]];
                const currentMajorForTask = majorActivityCounter[programCounter][projectCounter[
                    programCounter]][
                    currentMainForTask
                ];
                const currentSubForTask = subActivityCounter[programCounter][projectCounter[
                    programCounter]][
                    currentMainForTask
                ][currentMajorForTask];
                numbering =
                    `${programCounter}.${projectCounter[programCounter]}.${currentMainForTask}.${currentMajorForTask}.${currentSubForTask}.1. `;
                className = 'font-normal text-[12px] ml-30';
                break;
            default:
                numbering = '';
                className = 'font-semibold text-sm';
        }

        return `<div class="${className}">${numbering}${ppaName}</div>`;
    }

    // Get indented indicator name based on record type (same as PPA)
    function getIndentedIndicatorName(item) {
        // Get indicator name from object or string
        const indicatorName = item.indicator?.name || item.indicator || '';
        const recordType = item.ppa?.record_type?.name || item.record_type_name || '';

        if (!indicatorName) return '';

        let indentation = '';
        let className = 'text-xs text-blue-600 mt-1';

        // Determine indentation based on record type (same as PPA)
        switch (recordType.toUpperCase()) {
            case 'PROGRAM':
                indentation = '';
                className = 'text-xs text-blue-600 mt-1';
                break;
            case 'PROJECT':
                indentation = '';
                className = 'text-xs text-blue-600 mt-1 ml-6';
                break;
            case 'MAIN ACTIVITY':
                indentation = '';
                className = 'text-xs text-blue-600 mt-1 ml-12';
                break;
            case 'MAJOR ACTIVITY':
                indentation = '';
                className = 'text-xs text-blue-600 mt-1 ml-18';
                break;
            case 'SUB ACTIVITY':
                indentation = '';
                className = 'text-xs text-blue-600 mt-1 ml-24';
                break;
            case 'ACTIONABLE TASK':
                indentation = '';
                className = 'text-xs text-blue-600 mt-1 ml-30';
                break;
            default:
                indentation = '';
                className = 'text-xs text-blue-600 mt-1';
        }

        return `<div class="${className}">${indentation} ${indicatorName}</div>`;
    }

    // Get indentation class for merged row details based on record type
    function getIndentClass(item) {
        const recordType = item.ppa?.record_type?.name || item.record_type_name || '';

        switch (recordType.toUpperCase()) {
            case 'PROGRAM':
                return '';
            case 'PROJECT':
                return 'ml-4';
            case 'MAIN ACTIVITY':
                return 'ml-8';
            case 'MAJOR ACTIVITY':
                return 'ml-12';
            case 'SUB ACTIVITY':
                return 'ml-16';
            case 'ACTIONABLE TASK':
                return 'ml-20';
            default:
                return '';
        }
    }

    // Format universe data for display with CAR total first (green font for CAR)
    function formatUniverseData(universe) {
        if (!universe) return '0';

        // If it's a string, try to parse it as JSON
        if (typeof universe === 'string') {
            try {
                universe = JSON.parse(universe);
            } catch (e) {
                return universe; // Return as-is if it's not valid JSON
            }
        }

        // If it's an object, calculate CAR total first, then individual offices
        if (typeof universe === 'object' && universe !== null) {
            const values = Object.values(universe);
            if (values.length > 0) {
                // Calculate CAR total (sum of all office values)
                const carTotal = values.reduce((sum, val) => sum + (parseFloat(val) || 0), 0);

                // Create display array: CAR total first (green), then individual values
                const displayValues = [
                    `<span class="text-green-600 font-semibold">${formatNumberWithCommas(carTotal)}</span>`
                ];

                // Add individual office values
                Object.values(universe).forEach(val => {
                    const numVal = parseFloat(val) || 0;
                    displayValues.push(formatNumberWithCommas(numVal));
                });

                return displayValues.join('<br>');
            }
        }

        // If we get here, return a safe string representation
        return String(universe);
    }

    // Format accomplishment data for display
    function formatAccomplishmentData(accomplishment, type) {
        if (!accomplishment) return '0';

        // If it's a string, try to parse it as JSON
        if (typeof accomplishment === 'string') {
            try {
                accomplishment = JSON.parse(accomplishment);
            } catch (e) {
                return '0'; // Return 0 if JSON parsing fails
            }
        }

        // If it's an object, get the specific type value
        if (typeof accomplishment === 'object' && accomplishment !== null) {
            const value = accomplishment[type];
            // Return the value if it exists and is not null/undefined, otherwise return 0
            return (value !== null && value !== undefined) ? value : '0';
        }
    }

    // Format universe data for display with CAR total first (green font for CAR) and hover tooltips for targets
    function formatUniverseData(universe, targets) {
        if (!universe) return '0';

        // If it's a string, try to parse it as JSON
        if (typeof universe === 'string') {
            try {
                universe = JSON.parse(universe);
            } catch (e) {
                return universe; // Return as-is if it's not valid JSON
            }
        }

        // Parse targets data
        if (targets && typeof targets === 'string') {
            try {
                targets = JSON.parse(targets);
            } catch (e) {
                targets = null;
            }
        }

        // If it's an object, calculate CAR total first, then individual offices
        if (typeof universe === 'object' && universe !== null) {
            const values = Object.values(universe);
            if (values.length > 0) {
                // Calculate CAR total (sum of all office values)
                const carTotal = values.reduce((sum, val) => sum + (parseFloat(val) || 0), 0);

                // Get all years from targets data
                const allTargetYears = new Set();
                if (targets && typeof targets === 'object') {
                    Object.values(targets).forEach(officeTargets => {
                        if (officeTargets && typeof officeTargets === 'object') {
                            Object.keys(officeTargets).forEach(year => {
                                allTargetYears.add(parseInt(year));
                            });
                        }
                    });
                }

                // Sort years in ascending order
                const targetYears = Array.from(allTargetYears).sort((a, b) => a - b);

                // Get current year for highlighting future targets
                const currentYear = new Date().getFullYear();

                // Create tooltips for each office
                const officeTooltips = [];
                const officeIds = Object.keys(universe);

                officeIds.forEach((officeId, index) => {
                    const officeTargets = targets && targets[officeId] ? targets[officeId] : null;
                    const officeName = offices.find(o => o.id == officeId)?.name || `Office ${officeId}`;

                    let tooltipContent =
                        `<div class="font-bold text-lg text-blue-600 mb-4">${officeName}</div>`;
                    tooltipContent += `<div class="font-semibold text-white mb-3">Targets per Year:</div>`;

                    if (officeTargets && typeof officeTargets === 'object' && Object.keys(officeTargets)
                        .length > 0) {
                        targetYears.forEach(year => {
                            const yearValue = officeTargets[year] !== null && officeTargets[year] !==
                                undefined ? officeTargets[year] : 0;
                            const isFuture = year > currentYear;
                            const isCurrent = year === currentYear;
                            let yearClass = 'font-medium';
                            let yearLabel = year;

                            if (isFuture) {
                                yearClass = 'text-yellow-400 font-bold';
                                yearLabel = `${year} (Future)`;
                            } else if (isCurrent) {
                                yearClass = 'text-green-400 font-bold';
                                yearLabel = `${year} (Current)`;
                            }

                            tooltipContent +=
                                `<div class="text-sm py-2">• ${yearLabel}: <span class="${yearClass}">${formatNumberWithCommas(yearValue)}</span></div>`;
                        });
                    } else {
                        tooltipContent += `<div class="text-sm py-2">No targets set</div>`;
                    }

                    officeTooltips.push(tooltipContent);
                });

                // Create display array: CAR total first (green) without tooltip, then individual values with tooltips
                const displayValues = [
                    `<span class="text-green-600 font-semibold">${carTotal === 0 ? '-' : formatNumberWithCommas(carTotal)}</span>`
                ];

                // Add individual office values with tooltips
                Object.values(universe).forEach((val, index) => {
                    const numVal = parseFloat(val) || 0;
                    displayValues.push(
                        `<span class="cursor-help hover:text-gray-700" data-tooltip="${encodeURIComponent(officeTooltips[index])}">${numVal === 0 ? '-' : formatNumberWithCommas(numVal)}</span>`
                    );
                });

                return displayValues.join('<br>');
            }
        }

        // If we get here, return a safe string representation
        return String(universe);
    }

    // Format accomplishment data per office with CAR total first (green font for CAR) and hover tooltips
    function formatAccomplishmentPerOffice(accomplishment, officeIds) {
        if (!accomplishment) return '0';

        // If it's a string, try to parse it as JSON
        if (typeof accomplishment === 'string') {
            try {
                accomplishment = JSON.parse(accomplishment);
            } catch (e) {
                return '0'; // Return 0 if JSON parsing fails
            }
        }

        // If it's an object, get accomplishment values for each office
        if (typeof accomplishment === 'object' && accomplishment !== null) {
            // Get office IDs array
            let officeIdArray = officeIds;
            if (typeof officeIds === 'string') {
                try {
                    officeIdArray = JSON.parse(officeIds);
                } catch (e) {
                    officeIdArray = [];
                }
            }

            if (!Array.isArray(officeIdArray)) {
                officeIdArray = [];
            }

            // Get accomplishment values for each office
            const officeAccomplishments = [];
            const officeTooltips = [];
            let carTotalAccomplishment = 0;

            // Get current year and past years
            const currentYear = new Date().getFullYear();
            const pastYears = [];
            for (let year = 2022; year < currentYear; year++) {
                pastYears.push(year);
            }

            officeIdArray.forEach((officeId, index) => {
                const officeAccomplishment = accomplishment[officeId];
                let value = 0;
                let tooltipContent = '';

                if (officeAccomplishment && typeof officeAccomplishment === 'object') {
                    // Sum past years only for the Past Years column
                    const pastYearsSum = pastYears.reduce((sum, year) => {
                        return sum + (officeAccomplishment[year] || 0);
                    }, 0);

                    value = pastYearsSum;

                    // Create tooltip content for past years
                    const officeName = offices.find(o => o.id == officeId)?.name ||
                        `Office ${officeId}`;
                    let yearBreakdown =
                        `<div class="font-bold text-lg text-blue-600 mb-4">${officeName}</div>`;
                    yearBreakdown +=
                        `<div class="font-semibold text-white mb-3">Past Years Accomplishment:</div>`;

                    pastYears.forEach(year => {
                        const yearValue = officeAccomplishment[year] || 0;
                        yearBreakdown +=
                            `<div class="text-sm py-2">• ${year}: <span class="font-medium">${yearValue}</span></div>`;
                    });

                    tooltipContent = yearBreakdown;
                } else {
                    value = 0;
                    const officeName = offices.find(o => o.id == officeId)?.name ||
                        `Office ${officeId}`;
                    tooltipContent =
                        `<div class="font-bold text-lg text-blue-600 mb-4">${officeName}</div>`;
                    tooltipContent +=
                        `<div class="font-semibold text-white mb-3">Past Years Accomplishment:</div>`;
                    tooltipContent += `<div class="text-sm py-2">No Data Available</div>`;
                }

                officeAccomplishments.push(value);
                officeTooltips.push(tooltipContent);
                carTotalAccomplishment += value;
            });

            // Create display array: CAR total first (green) without tooltip, then individual accomplishments with tooltips
            const displayValues = [
                `<span class="text-green-600 font-semibold">${carTotalAccomplishment === 0 ? '-' : formatNumberWithCommas(carTotalAccomplishment)}</span>`
            ];

            officeAccomplishments.forEach((val, index) => {
                displayValues.push(
                    `<span class="cursor-help hover:text-gray-700" data-tooltip="${encodeURIComponent(officeTooltips[index])}">${val === 0 ? '-' : formatNumberWithCommas(val)}</span>`
                );
            });

            return displayValues.join('<br>');
        }

        return '0';
    }

    // Calculate baseline with formula:
    // Baseline = Universe - (current accomplishment + past accomplishment)
    // If past/current aggregates are missing, fallback to summing year values.
    function calculateBaseline(universe, accomplishment, officeIds) {
        if (!universe || !accomplishment) return '0';

        // Parse universe data
        if (typeof universe === 'string') {
            try {
                universe = JSON.parse(universe);
            } catch (e) {
                return '0';
            }
        }

        // Parse accomplishment data
        if (typeof accomplishment === 'string') {
            try {
                accomplishment = JSON.parse(accomplishment);
            } catch (e) {
                return '0';
            }
        }

        // Get office IDs array
        let officeIdArray = officeIds;
        if (typeof officeIds === 'string') {
            try {
                officeIdArray = JSON.parse(officeIds);
            } catch (e) {
                officeIdArray = [];
            }
        }

        if (!Array.isArray(officeIdArray)) {
            officeIdArray = [];
        }

        // Calculate baseline for each office
        const baselineValues = [];
        let carTotalBaseline = 0;
        const hasUniverseAndAccomplishment = []; // Track if each office has non-null/0 universe and accomplishment

        officeIdArray.forEach((officeId, index) => {
            const universeValue = universe[officeId] || 0;
            const officeAccomplishment = accomplishment[officeId];

            if (officeAccomplishment && typeof officeAccomplishment === 'object') {
                // Prefer using the computed aggregates (to avoid double-counting years)
                let totalAccomplishment = 0;
                const hasAggregates =
                    typeof officeAccomplishment.past_years === 'number' ||
                    typeof officeAccomplishment.current_year === 'number';

                if (hasAggregates) {
                    totalAccomplishment =
                        (typeof officeAccomplishment.past_years === 'number' ?
                            officeAccomplishment.past_years :
                            0) +
                        (typeof officeAccomplishment.current_year === 'number' ?
                            officeAccomplishment
                            .current_year : 0);
                } else {
                    // Fallback: sum numeric year values only (ignore any non-year keys)
                    const yearValues = Object.entries(officeAccomplishment)
                        .filter(([key, val]) => /^\d{4}$/.test(String(key)) && typeof val ===
                            'number')
                        .map(([, val]) => val);
                    totalAccomplishment = yearValues.reduce((acc, val) => acc + val, 0);
                }

                const baseline = universeValue - totalAccomplishment;
                const adjustedBaseline = baseline < 0 ? 0 : baseline;
                baselineValues.push(adjustedBaseline);
                carTotalBaseline += adjustedBaseline;

                // Track if this office has non-null/0 universe and accomplishment
                hasUniverseAndAccomplishment[index] = (universeValue !== null && universeValue !== 0) &&
                    (totalAccomplishment !== null && totalAccomplishment !== 0);
            } else {
                const baseline = universeValue;
                const adjustedBaseline = baseline < 0 ? 0 : baseline;
                baselineValues.push(adjustedBaseline);
                carTotalBaseline += adjustedBaseline;

                // Track if this office has non-null/0 universe (no accomplishment)
                hasUniverseAndAccomplishment[index] = (universeValue !== null && universeValue !== 0);
            }
        });

        // Check if any office has non-null/0 universe and accomplishment for CAR total
        const carHasUniverseAndAccomplishment = hasUniverseAndAccomplishment.some(has => has);

        // Create display array: CAR total first (green/red based on value), then individual baselines
        const carColorClass = carTotalBaseline < 0 ? 'text-red-600' : 'text-green-600';
        const displayValues = [
            `<span class="${carColorClass} font-semibold">${carTotalBaseline === 0 ? (carHasUniverseAndAccomplishment ? '0' : '-') : formatNumberWithCommas(carTotalBaseline)}</span>`
        ];

        baselineValues.forEach((val, index) => {
            const colorClass = val < 0 ? 'text-red-600' : 'text-blue-600';
            displayValues.push(
                `<span class="${colorClass}">${val === 0 ? (hasUniverseAndAccomplishment[index] ? '0' : '-') : formatNumberWithCommas(val)}</span>`
            );
        });

        return displayValues.length > 0 ? displayValues.join('<br>') : '0';
    }

    // Get current year accomplishment per office with CAR total first (green font for CAR)
    function getCurrentYearAccomplishment(accomplishment, officeIds) {
        if (!accomplishment) return '0';

        // Parse accomplishment data
        if (typeof accomplishment === 'string') {
            try {
                accomplishment = JSON.parse(accomplishment);
            } catch (e) {
                return '0';
            }
        }

        // Get office IDs array
        let officeIdArray = officeIds;
        if (typeof officeIds === 'string') {
            try {
                officeIdArray = JSON.parse(officeIds);
            } catch (e) {
                officeIdArray = [];
            }
        }

        if (!Array.isArray(officeIdArray)) {
            officeIdArray = [];
        }

        // Get current year values for each office
        const currentYear = new Date().getFullYear();
        const currentYearValues = [];
        let carTotalCurrentYear = 0;

        officeIdArray.forEach(officeId => {
            const officeAccomplishment = accomplishment[officeId];
            if (officeAccomplishment && typeof officeAccomplishment === 'object') {
                const currentValue = officeAccomplishment[currentYear];
                const value = currentValue !== null && currentValue !== undefined ?
                    currentValue : 0;
                currentYearValues.push(value);
                carTotalCurrentYear += value;
            } else {
                currentYearValues.push(0);
                carTotalCurrentYear += 0;
            }
        });

        // Create display array: CAR total first (green), then individual current year values
        const displayValues = [
            `<span class="text-green-600 font-semibold">${carTotalCurrentYear === 0 ? '-' : formatNumberWithCommas(carTotalCurrentYear)}</span>`
        ];
        currentYearValues.forEach(val => displayValues.push(val === 0 ? '-' : formatNumberWithCommas(val)));

        return displayValues.length > 0 ? displayValues.join('<br>') : '0';
    }

    // Format office names for display with CAR as parent (green font for CAR)
    function formatOfficeNames(officeIds) {
        if (!officeIds || (Array.isArray(officeIds) && officeIds.length === 0)) return 'N/A';

        if (typeof officeIds === 'string') {
            try {
                officeIds = JSON.parse(officeIds);
            } catch (e) {
                return officeIds; // Return as-is if it's not valid JSON
            }
        }

        if (!Array.isArray(officeIds)) return 'N/A';

        // Always show CAR as parent office with green font
        let officeNames = ['<span class="text-green-600 font-semibold">CAR</span>'];

        // Get individual office names from the offices array
        const individualOffices = officeIds.map(id => {
            const office = offices.find(o => o.id == id);
            return office ? office.name : `Office ${id}`;
        });

        // Add individual offices after CAR
        officeNames = officeNames.concat(individualOffices);

        return officeNames.join('<br>');
    }

    function populateForm(data) {
        document.getElementById('recordId').value = data.id;
        document.getElementById('ppa_id').value = data.ppa ? data.ppa.id : '';
        document.getElementById('indicator_id').value = data.indicator ? data.indicator.name : '';

        // Handle office checkboxes
        const officeCheckboxes = document.querySelectorAll('input[name="office_id[]"]');
        officeCheckboxes.forEach(checkbox => {
            checkbox.checked = data.office_id && data.office_id.includes(parseInt(checkbox
                .value));
        });

        // Show/hide office section based on indicator
        const officeSection = document.getElementById('officeSection');
        if (officeSection) {
            if (data.indicator && data.indicator.name) {
                officeSection.style.display = 'block';
            } else {
                officeSection.style.display = 'none';
            }
        }
    }

    // Clear form fields
    function clearDynamicFields() {
        // Clear new PPA textarea
        const newPpaTextarea = document.getElementById('new_ppa_name');
        if (newPpaTextarea) newPpaTextarea.value = '';

        // Hide new PPA section
        const newPpaSection = document.getElementById('newPpaSection');
        if (newPpaSection) newPpaSection.classList.add('hidden');

        // Hide Step 2 section
        const step2Section = document.getElementById('step2Section');
        if (step2Section) step2Section.classList.add('hidden');

        // Reset button visibility
        updateButtonVisibility();

        // Clear Step 2 fields
        const remarks = document.getElementById('remarks');
        if (remarks) remarks.value = '';

        // Reset years coverage to default
        const year2022_2028 = document.getElementById('year_2022_2028');
        const year2028_2034 = document.getElementById('year_2028_2034');
        const year2034_2040 = document.getElementById('year_2034_2040');

        if (year2022_2028) year2022_2028.checked = true;
        if (year2028_2034) year2028_2034.checked = false;
        if (year2034_2040) year2034_2040.checked = false;

        // Clear Step 2 data table
        const dataTableBody = document.getElementById('dataTableBody');
        const addDataTablesBtn = document.getElementById('addDataTablesBtn');

        if (dataTableBody) dataTableBody.innerHTML = '';

        // Show the add data tables button
        if (addDataTablesBtn) addDataTablesBtn.classList.remove('hidden');
    }


    // Form submission
    document.getElementById('crudForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const data = Object.fromEntries(formData);

        // Handle PPA selection (dropdown)
        const ppaSelect = document.getElementById('ppa_id');
        if (ppaSelect && ppaSelect.value && ppaSelect.value !== 'new') {
            data.ppa_id = parseInt(ppaSelect.value);
        }

        // Handle new PPA name
        const newPpaTextarea = document.getElementById('new_ppa_name');
        if (newPpaTextarea && newPpaTextarea.value.trim()) {
            data.new_ppa_name = newPpaTextarea.value.trim();
        }

        // Handle indicator (textarea)
        const indicatorTextarea = document.getElementById('indicator_id');
        if (indicatorTextarea && indicatorTextarea.value.trim()) {
            data.indicator = indicatorTextarea.value.trim();
        }

        // Handle office checkboxes
        const checkedOffices = Array.from(document.querySelectorAll(
                'input[name="office_id[]"]:checked'))
            .map(checkbox => parseInt(checkbox.value));
        data.office_id = checkedOffices.length > 0 ? checkedOffices : null;

        // Handle universe data (only if data section is shown)
        const dataSection = document.getElementById('dataSection');
        if (dataSection && !dataSection.classList.contains('hidden')) {
            // Collect universe data (single value per office for entire duration)
            const universeInputs = document.querySelectorAll('input[name^="universe"]');
            const universeData = {};

            universeInputs.forEach(input => {
                const match = input.name.match(/universe\[(\d+)\]/);
                if (match) {
                    const officeId = match[1];
                    const value = parseFloat(input.value) || 0;

                    // Store the actual universe value (not the period)
                    universeData[officeId] = value;
                }
            });

            data.universe = universeData;

            // Handle accomplishment data (year-by-year per office)
            const accomplishmentInputs = document.querySelectorAll(
                'input[name^="accomplishment"]');
            const accomplishmentData = {};
            const currentYear = new Date().getFullYear(); // 2026

            accomplishmentInputs.forEach(input => {
                const match = input.name.match(/accomplishment\[(\d+)\]\[(\d+)\]/);
                if (match) {
                    const officeId = match[1];
                    const year = parseInt(match[2]);
                    const value = parseFloat(input.value) || 0;

                    if (!accomplishmentData[officeId]) {
                        accomplishmentData[officeId] = {};
                    }
                    accomplishmentData[officeId][year] = value;
                }
            });

            // Calculate past years sum and current year
            Object.keys(accomplishmentData).forEach(officeId => {
                const officeAccomplishments = accomplishmentData[officeId];
                let pastYearsSum = 0;
                let currentYearValue = 0;

                Object.keys(officeAccomplishments).forEach(year => {
                    const yearNum = parseInt(year);
                    if (yearNum < currentYear) {
                        // Past years (2022-2025)
                        pastYearsSum += officeAccomplishments[year];
                    } else if (yearNum === currentYear) {
                        // Current year (2026)
                        currentYearValue = officeAccomplishments[year];
                    }
                });

                // Store calculated values
                accomplishmentData[officeId]['past_years'] = pastYearsSum;
                accomplishmentData[officeId]['current_year'] = currentYearValue;
            });

            data.accomplishment = accomplishmentData;

            // Handle targets data (year-by-year per office)
            const targetsInputs = document.querySelectorAll('input[name^="targets"]');
            const targetsData = {};

            targetsInputs.forEach(input => {
                const match = input.name.match(/targets\[(\d+)\]\[(\d+)\]/);
                if (match) {
                    const officeId = match[1];
                    const year = match[2];
                    const value = parseFloat(input.value) || 0;

                    if (!targetsData[officeId]) {
                        targetsData[officeId] = {};
                    }
                    targetsData[officeId][year] = value;
                }
            });

            data.targets = targetsData;

            // Handle single remarks field
            const remarksTextarea = document.getElementById('remarks');
            if (remarksTextarea) {
                data.remarks = remarksTextarea.value.trim();
            }
        } else {
            // Step 2 not shown - set other fields to null
            data.universe = null;
            data.accomplishment = null;
            data.targets = null;
            data.remarks = null;
        }

        // Use FormData instead of JSON for more reliable submission
        const submitFormData = new FormData();

        // Add basic form fields (matching database schema) - handle all related tables
        // Handle PPA creation: if ppa_id is "new", don't send ppa_id, let controller handle creation
        if (data.ppa_id === 'new') {
            // Don't send ppa_id when creating new PPA, let controller handle it
            submitFormData.append('ppa_id', ''); // Empty for new PPA
            submitFormData.append('new_ppa_name', data.new_ppa_name || '');

            // Add required fields for new PPA creation
            const recordTypeSelect = document.getElementById('record_type_id');
            if (recordTypeSelect) {
                submitFormData.append('record_type_id', recordTypeSelect.value || '');
            }

            // Add types_id for new PPA creation (required field in PPA table)
            submitFormData.append('types_id', '3'); // ENF type

            // Add ppa_details_id (nullable, can be set by controller)
            submitFormData.append('ppa_details_id', ''); // Empty for now

            // Add office_id for PPA table (JSON array)
            if (data.office_id && Array.isArray(data.office_id)) {
                submitFormData.append('ppa_office_id', JSON.stringify(data.office_id));
            } else {
                submitFormData.append('ppa_office_id', JSON.stringify([]));
            }
        } else {
            submitFormData.append('ppa_id', data.ppa_id || '');
            submitFormData.append('new_ppa_name', ''); // Empty when using existing PPA
        }

        // Handle indicator creation - send indicator text for new indicator creation
        submitFormData.append('indicator_text', data.indicator || '');
        submitFormData.append('indicator_id',
            ''); // Let controller handle indicator lookup/creation

        submitFormData.append('remarks', data.remarks || '');

        // Only add Step 2 data if indicator has content, otherwise send null
        if (data.indicator && data.indicator.trim() !== '') {
            // Add years data (only when indicator exists)
            const yearsData = ['2022', '2023', '2024', '2025', '2026', '2027', '2028'];
            submitFormData.append('years', JSON.stringify(yearsData));

            // Add office IDs as JSON array (only when indicator exists)
            if (data.office_id && Array.isArray(data.office_id)) {
                submitFormData.append('office_id', JSON.stringify(data.office_id));
            } else {
                submitFormData.append('office_id', JSON.stringify([]));
            }

            // Add universe data as JSON string (with error handling)
            if (data.universe) {
                try {
                    submitFormData.append('universe', JSON.stringify(data.universe));
                } catch (e) {
                    console.error('Error stringifying universe data:', e);
                    submitFormData.append('universe', '{}');
                }
            } else {
                submitFormData.append('universe', '{}');
            }

            // Add accomplishment data as JSON string (with error handling)
            if (data.accomplishment) {
                try {
                    submitFormData.append('accomplishment', JSON.stringify(data
                        .accomplishment));
                } catch (e) {
                    console.error('Error stringifying accomplishment data:', e);
                    submitFormData.append('accomplishment', '{}');
                }
            } else {
                submitFormData.append('accomplishment', '{}');
            }

            // Add targets data as JSON string (with error handling)
            if (data.targets) {
                try {
                    submitFormData.append('targets', JSON.stringify(data.targets));
                } catch (e) {
                    console.error('Error stringifying targets data:', e);
                    submitFormData.append('targets', '{}');
                }
            } else {
                submitFormData.append('targets', '{}');
            }
        } else {
            // Send null values when no indicator (Step 2 not used)
            submitFormData.append('years', '');
            submitFormData.append('office_id', '');
            submitFormData.append('universe', '');
            submitFormData.append('accomplishment', '');
            submitFormData.append('targets', '');
        }

        // Add CSRF token
        submitFormData.append('_token', document.querySelector('meta[name="csrf-token"]')
            .getAttribute(
                'content'));

        // Add edit section for edit mode
        if (currentEditId) {
            // Prefer explicit selection, but auto-detect based on visible section
            const selectedSection = document.querySelector('input[name="editSection"]:checked');
            if (selectedSection) {
                submitFormData.append('editSection', selectedSection.value);
            } else {
                const ppaDetailsSection = document.getElementById('ppaDetailsSection');
                const dataSection = document.getElementById('dataSection');
                const isDataVisible = dataSection && !dataSection.classList.contains('hidden');
                const isPpaVisible = ppaDetailsSection && !ppaDetailsSection.classList.contains(
                    'hidden');

                // If user is viewing data section (or both), treat this as data edit.
                if (isDataVisible && !isPpaVisible) {
                    submitFormData.append('editSection', 'data');
                } else {
                    submitFormData.append('editSection', 'ppa');
                }
            }
        }

        const url = currentEditId ? `/enf/${currentEditId}` : '/enf';
        // IMPORTANT: Use POST + _method for updates so PHP parses multipart FormData.
        const method = 'POST';

        if (currentEditId) {
            submitFormData.append('_method', 'PUT');
        }

        fetch(url, {
                method: method,
                body: submitFormData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                        .getAttribute(
                            'content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                // Check if response is actually JSON
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                } else {
                    // If not JSON, throw an error with the response text
                    return response.text().then(text => {
                        throw new Error(
                            `Server returned non-JSON response: ${text.substring(0, 200)}...`
                        );
                    });
                }
            })
            .then(data => {
                if (data.success) {
                    closeModal();
                    showNotification(data.message, 'success');
                    // Ensure UI reflects saved DB state
                    setTimeout(() => {
                        window.location.reload();
                    }, 300);
                } else {
                    // Handle validation errors specifically
                    let errorMessage = data.message || 'Unknown error';

                    if (data.errors && Object.keys(data.errors).length > 0) {
                        // Show specific validation errors
                        const errorMessages = Object.values(data.errors).flat();
                        errorMessage = errorMessages.join(', ');
                    }

                    showNotification('Error: ' + errorMessage, 'error');
                    console.error('Server Response:', data);
                    console.error('Validation Errors:', data.errors);
                }
            })
            .catch(error => {
                console.error('Fetch Error:', error);
                showNotification('Error occurred: ' + error.message, 'error');
            });
    });


    // Edit record
    function editRecord(id) {
        currentEditId = id;

        // Fetch the record data
        fetch(`/enf/${id}/edit`, {
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                const contentType = response.headers.get('content-type') || '';
                if (!response.ok) {
                    return response.text().then(text => {
                        throw new Error(
                            `Server error (${response.status}). ${text.substring(0, 200)}...`
                        );
                    });
                }
                if (!contentType.includes('application/json')) {
                    return response.text().then(text => {
                        throw new Error(
                            `Server returned non-JSON response: ${text.substring(0, 200)}...`
                        );
                    });
                }
                return response.json();
            })
            .then(data => {
                // Store data globally for office-specific population
                window.editData = data;

                // Populate form fields with the record data
                const record = data.enf;
                const officeData = data.office_data || {};

                // Set basic fields (except dropdowns that need to be populated first)
                document.getElementById('modalTitle').textContent = 'Edit ENF Record';
                document.getElementById('indicator_id').value = record.indicator ? record.indicator
                    .name : '';

                // Show modal first
                document.getElementById('crudModal').classList.remove('hidden');
                document.getElementById('crudModal').classList.add('flex');
                document.getElementById('crudModal').classList.add('items-center');
                document.getElementById('crudModal').classList.add('justify-center');

                // Show edit section selector and hide sections initially
                document.getElementById('editSectionSelector').classList.remove('hidden');
                document.getElementById('editMode').value = 'true';
                document.getElementById('ppaDetailsSection').classList.add('hidden');
                document.getElementById('dataSection').classList.add('hidden');

                // Reset section selection
                document.querySelectorAll('input[name="editSection"]').forEach(radio => radio
                    .checked = false);

                // Load form data to populate dropdowns
                loadFormData().then(() => {
                    // After dropdowns are populated, set the values
                    setTimeout(() => {
                        // Set record type after dropdown is populated
                        document.getElementById('record_type_id').value = record
                            .ppa ? record.ppa
                            .record_type_id : '';

                        // Set PPA value
                        document.getElementById('ppa_id').value = record.ppa_id ||
                            '';

                        // Handle PPA selection
                        if (record.ppa_id) {
                            handlePpaSelection();
                        }

                        // Select offices that have data
                        const officeIds = Object.keys(officeData);
                        console.log('Office data:', officeData);
                        console.log('Office IDs:', officeIds);

                        // Get current record's office IDs - this is the primary source
                        let currentRecordOfficeIds = [];
                        if (record.office_id) {
                            try {
                                currentRecordOfficeIds = Array.isArray(record
                                        .office_id) ? record
                                    .office_id : JSON.parse(record.office_id);
                            } catch (e) {
                                console.log('Error parsing office_id:', e);
                            }
                        }
                        console.log('Current record office IDs:',
                            currentRecordOfficeIds);

                        // Use current record's office IDs as the primary source
                        // Only fall back to officeData if current record has no office IDs
                        const allOfficeIds = currentRecordOfficeIds.length > 0 ?
                            currentRecordOfficeIds : officeIds;
                        console.log('Final office IDs to select:', allOfficeIds);

                        allOfficeIds.forEach(officeId => {
                            const checkbox = document.getElementById(
                                `office_${officeId}`);
                            if (checkbox) {
                                checkbox.checked = true;
                            }
                        });

                        // Always show step 2 section if there are offices selected
                        const checkedOffices = Array.from(document.querySelectorAll(
                            'input[name="office_id[]"]:checked'));

                        if (checkedOffices.length > 0) {
                            const step2Section = document.getElementById(
                                'step2Section');
                            if (step2Section) {
                                step2Section.classList.remove('hidden');
                            }
                            generateUniverseTable();

                            // Populate office-specific data after tables are generated
                            setTimeout(() => {
                                populateOfficeSpecificData(officeData);
                                updateButtonVisibility();
                            }, 200);
                        }

                        // Update button visibility
                        updateButtonVisibility();
                    }, 100);
                });
            })
            .catch(error => {
                console.error('Error fetching record:', error);
                showNotification('Error loading record data', 'error');
            });
    }

    // Populate office-specific data in the form
    function populateOfficeSpecificData(officeData) {
        Object.keys(officeData).forEach(officeId => {
            const data = officeData[officeId];

            // Populate universe value
            const universeInput = document.querySelector(`input[name="universe[${officeId}]"]`);
            if (universeInput && data.universe) {
                let universeValue = data.universe;
                // Handle legacy JSON string or casted object
                if (typeof universeValue === 'string') {
                    try {
                        universeValue = JSON.parse(universeValue);
                    } catch (e) {
                        universeValue = {};
                    }
                }
                if (universeValue && typeof universeValue === 'object') {
                    universeInput.value = universeValue[officeId] ?? '';
                } else {
                    universeInput.value = universeValue ?? '';
                }
            }

            // Populate accomplishment and targets for each year
            let accomplishmentValue = data.accomplishment;
            console.log(`Raw accomplishment for office ${officeId}:`, accomplishmentValue);
            if (typeof accomplishmentValue === 'string') {
                try {
                    accomplishmentValue = JSON.parse(accomplishmentValue);
                } catch (e) {
                    console.error('Error parsing accomplishment:', e);
                    accomplishmentValue = null;
                }
            }
            console.log(`Parsed accomplishment for office ${officeId}:`, accomplishmentValue);
            if (accomplishmentValue && typeof accomplishmentValue === 'object') {
                // Structure is {officeId: {year: value}}
                const officeData = accomplishmentValue[officeId];
                if (officeData && typeof officeData === 'object') {
                    Object.keys(officeData).forEach(year => {
                        const yearValue = officeData[year];
                        const accompInput = document.querySelector(
                            `input[name="accomplishment[${officeId}][${year}]"]`);
                        if (accompInput) {
                            accompInput.value = yearValue ?? '';
                            console.log(
                                `Set accomplishment[${officeId}][${year}] = ${yearValue}`
                            );
                        }
                    });
                }
            }

            let targetsValue = data.targets;
            console.log(`Raw targets for office ${officeId}:`, targetsValue);
            if (typeof targetsValue === 'string') {
                try {
                    targetsValue = JSON.parse(targetsValue);
                } catch (e) {
                    console.error('Error parsing targets:', e);
                    targetsValue = null;
                }
            }
            console.log(`Parsed targets for office ${officeId}:`, targetsValue);
            if (targetsValue && typeof targetsValue === 'object') {
                // Structure is {officeId: {year: value}}
                const officeData = targetsValue[officeId];
                if (officeData && typeof officeData === 'object') {
                    Object.keys(officeData).forEach(year => {
                        const yearValue = officeData[year];
                        const targetInput = document.querySelector(
                            `input[name="targets[${officeId}][${year}]"]`);
                        if (targetInput) {
                            targetInput.value = yearValue ?? '';
                            console.log(
                                `Set targets[${officeId}][${year}] = ${yearValue}`);
                        }
                    });
                }
            }

            // Populate single remarks field (use remarks from first office with data)
            const remarksTextarea = document.getElementById('remarks');
            if (remarksTextarea && data.remarks && !remarksTextarea.value) {
                remarksTextarea.value = data.remarks || '';
            }
        });
    }

    // Delete record
    function deleteRecord(id) {
        currentEditId = id;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function confirmDelete() {
        fetch(`/enf/${currentEditId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeModal();
                    loadEnfData();
                    showNotification('Record deleted successfully!', 'success');
                    closeDeleteModal();
                    location.reload();
                } else {
                    showNotification(data.message || 'Error deleting record', 'error');
                }
            })
            .catch(error => {
                showNotification('Error occurred: ' + error.message, 'error');
            });
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        currentEditId = null;
    }

    // Show notification
    function showNotification(message, type) {
        // Simple notification - you can enhance this
        const notification = document.createElement('div');
        notification.className =
            `fixed top-3 right-4 px-3 py-1.5 rounded-lg text-white ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} z-50`;
        notification.textContent = message;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    // Custom tooltip functionality
    function initializeTooltips() {
        // Create tooltip element if it doesn't exist
        let tooltip = document.getElementById('custom-tooltip');
        if (!tooltip) {
            tooltip = document.createElement('div');
            tooltip.id = 'custom-tooltip';
            tooltip.className =
                'absolute z-50 bg-gray-800 text-white p-3 rounded-lg shadow-lg text-sm max-w-xs pointer-events-none opacity-0 transition-opacity duration-200';
            tooltip.style.display = 'none';
            document.body.appendChild(tooltip);
        }

        // Handle hover events for elements with data-tooltip
        document.addEventListener('mouseover', function(e) {
            const target = e.target.closest('[data-tooltip]');
            if (target) {
                const tooltipContent = decodeURIComponent(target.getAttribute('data-tooltip'));
                tooltip.innerHTML = tooltipContent;
                tooltip.style.display = 'block';

                // Position tooltip
                const rect = target.getBoundingClientRect();
                tooltip.style.left = rect.left + 'px';
                tooltip.style.top = (rect.bottom + 5) + 'px';

                // Show tooltip
                setTimeout(() => {
                    tooltip.style.opacity = '1';
                }, 10);
            }
        });

        document.addEventListener('mouseout', function(e) {
            const target = e.target.closest('[data-tooltip]');
            if (target) {
                tooltip.style.opacity = '0';
                setTimeout(() => {
                    tooltip.style.display = 'none';
                }, 200);
            }
        });
    }

    // Load offices for modal
    function loadOffices() {
        // Offices are already loaded globally from the initial page load
        // This function can be used to refresh office data if needed
        console.log('Offices already loaded:', offices);
    }

    // Load record types for modal
    function loadRecordTypes() {
        // Record types are already loaded globally from the initial page load
        // This function can be used to refresh record type data if needed
        console.log('Record types already loaded:', recordTypes);
    }

    // Handle record type change to show/hide parent activity selection
    function handleRecordTypeChange() {
        const recordTypeSelect = document.getElementById('record_type_id');
        const parentActivitySection = document.getElementById('parentActivitySection');
        const parentActivitySelect = document.getElementById('parent_activity_id');
        const parentActivityRequired = document.getElementById('parentActivityRequired');
        const selectedRecordType = recordTypeSelect.value;

        // Get selected record type name
        const selectedRecordTypeObj = recordTypes.find(rt => rt.id == selectedRecordType);
        const recordTypeName = selectedRecordTypeObj ? selectedRecordTypeObj.name.toUpperCase() : '';

        // Show parent activity section for hierarchical record types
        if (recordTypeName === 'MAJOR ACTIVITY' || recordTypeName === 'SUB ACTIVITY' ||
            recordTypeName ===
            'ACTIONABLE TASK') {
            parentActivitySection.classList.remove('hidden');
            loadParentActivities(recordTypeName);

            // Show required indicator for record types 2, 3, 4, 5, 6
            if (selectedRecordType == '2' || selectedRecordType == '3' || selectedRecordType == '4' ||
                selectedRecordType == '5' || selectedRecordType == '6') {
                parentActivityRequired.classList.remove('hidden');
            } else {
                parentActivityRequired.classList.add('hidden');
            }
        } else {
            parentActivitySection.classList.add('hidden');
            parentActivitySelect.innerHTML = '<option value="">Select Parent Activity</option>';
            parentActivityRequired.classList.add('hidden');
        }
    }

    // Load parent activities based on record type
    function loadParentActivities(recordType) {
        const parentActivitySelect = document.getElementById('parent_activity_id');
        const loadingMessage = document.getElementById('parentLoadingMessage');

        // Show loading message
        loadingMessage.classList.remove('hidden');
        parentActivitySelect.innerHTML = '<option value="">Loading...</option>';

        // Determine what type of parent activities to load
        let parentRecordType = '';
        switch (recordType) {
            case 'MAJOR ACTIVITY':
                parentRecordType = 'MAIN ACTIVITY';
                break;
            case 'SUB ACTIVITY':
                parentRecordType = 'MAJOR ACTIVITY';
                break;
            case 'ACTIONABLE TASK':
                parentRecordType = 'SUB ACTIVITY';
                break;
        }

        // Find the parent record type ID
        const parentRecordTypeObj = recordTypes.find(rt => rt.name === parentRecordType);
        const parentRecordTypeId = parentRecordTypeObj ? parentRecordTypeObj.id : null;

        console.log('Looking for parent activities of type:', parentRecordType, 'ID:',
            parentRecordTypeId);
        console.log('Available PPAs:', ppas);
        console.log('Available Record Types:', recordTypes);

        if (!parentRecordTypeId) {
            console.error('Parent record type not found:', parentRecordType);
            parentActivitySelect.innerHTML = '<option value="">Record type not found</option>';
            loadingMessage.classList.add('hidden');
            return;
        }

        // Filter PPAs based on parent record type ID (more reliable)
        const parentActivities = ppas.filter(ppa => {
            console.log('Checking PPA:', ppa.name, 'record_type_id:', ppa.record_type_id,
                'vs parent ID:',
                parentRecordTypeId);
            return ppa.record_type_id == parentRecordTypeId;
        });

        console.log('Filtered parent activities:', parentActivities);

        // If no activities found with record_type_id, try other methods
        let finalActivities = parentActivities;
        if (parentActivities.length === 0) {
            console.log('No activities found with record_type_id, trying other methods...');
            finalActivities = ppas.filter(ppa => {
                // Try different data structures
                if (ppa.record_type && ppa.record_type.name) {
                    return ppa.record_type.name === parentRecordType;
                } else if (ppa.record_type_name) {
                    return ppa.record_type_name === parentRecordType;
                }
                return false;
            });
            console.log('Activities found with alternative method:', finalActivities);
        }

        // Sort activities in descending order (by ID or name)
        finalActivities.sort((a, b) => {
            // First try to sort by ID (descending)
            if (a.id && b.id) {
                return b.id - a.id; // Descending order
            }
            // Fallback to sorting by name (descending)
            const nameA = (a.name || '').toLowerCase();
            const nameB = (b.name || '').toLowerCase();
            if (nameA < nameB) return 1;
            if (nameA > nameB) return -1;
            return 0;
        });

        console.log('Sorted parent activities (desc):', finalActivities);

        // Populate parent activity dropdown with numbered list
        setTimeout(() => {
            parentActivitySelect.innerHTML = '<option value="">Select Parent Activity</option>';

            if (finalActivities.length === 0) {
                const option = document.createElement('option');
                option.value = '';
                option.textContent = `No ${parentRecordType.toLowerCase()}s available`;
                option.disabled = true;
                parentActivitySelect.appendChild(option);
            } else {
                finalActivities.forEach((ppa, index) => {
                    const option = document.createElement('option');
                    option.value = ppa.id;
                    // Remove numbering - just show the name
                    option.textContent = ppa.name;
                    parentActivitySelect.appendChild(option);
                });
            }

            loadingMessage.classList.add('hidden');
        }, 300); // Shorter loading delay for better UX
    }

    // Load PPAs for modal
    function loadPpas() {
        // PPAs are already loaded globally from the initial page load
        // This function can be used to refresh PPA data if needed
    }

    // Load indicators for modal
    function loadIndicators() {
        // Indicators are already loaded globally from the initial page load
        // This function can be used to refresh indicator data if needed
    }

    // Load types for modal
    function loadTypes() {
        // Types data would be loaded here if needed
    }

    // Load PPA details for modal
    function loadPpaDetails() {
        // PPA details data would be loaded here if needed
    }

    // Open create modal
    function openCreateModal() {
        currentEditId = null;
        document.getElementById('modalTitle').textContent = 'Add New ENF Record';
        document.getElementById('crudForm').reset();
        clearDynamicFields();
        document.getElementById('crudModal').classList.remove('hidden');
        document.getElementById('crudModal').classList.add('flex');
        document.getElementById('crudModal').classList.add('items-center');
        document.getElementById('crudModal').classList.add('justify-center');

        // Hide edit section selector (create mode)
        document.getElementById('editSectionSelector').classList.add('hidden');
        document.getElementById('editMode').value = 'false';

        // Show all sections for create mode
        document.getElementById('ppaDetailsSection').classList.remove('hidden');
        document.getElementById('dataSection').classList.remove('hidden');

        // Reset parent activity section
        document.getElementById('parentActivitySection').classList.add('hidden');
        document.getElementById('parent_activity_id').innerHTML =
            '<option value="">Select Parent Activity</option>';

        updateButtonVisibility();
        loadOffices();
        loadRecordTypes();
        loadPpas();
        loadIndicators();
        loadTypes();
        loadPpaDetails();
    }

    // Refresh parent activities
    function refreshParentActivities() {
        const recordTypeSelect = document.getElementById('record_type_id');
        const selectedRecordType = recordTypeSelect.value;

        if (!selectedRecordType) {
            return;
        }

        // Get selected record type name
        const selectedRecordTypeObj = recordTypes.find(rt => rt.id == selectedRecordType);
        const recordTypeName = selectedRecordTypeObj ? selectedRecordTypeObj.name.toUpperCase() : '';

        // Only refresh if it's a hierarchical record type
        if (recordTypeName === 'MAJOR ACTIVITY' || recordTypeName === 'SUB ACTIVITY' ||
            recordTypeName ===
            'ACTIONABLE TASK') {
            console.log('Refreshing parent activities for:', recordTypeName);

            // Fetch fresh data from server using the same endpoint as initial load
            fetch('/enf/create', {
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    ppas = data.ppas; // Update the global ppas array with fresh data
                    indicators = data.indicators;
                    recordTypes = data.recordTypes;
                    loadParentActivities(recordTypeName);
                })
                .catch(error => {
                    // Show error message in the dropdown
                    const parentActivitySelect = document.getElementById('parent_activity_id');
                    const loadingMessage = document.getElementById('parentLoadingMessage');
                    parentActivitySelect.innerHTML = '<option value="">Error loading data</option>';
                    loadingMessage.classList.add('hidden');
                });
        }
    }

    // Close modal function
    function closeModal() {
        document.getElementById('crudModal').classList.add('hidden');
        document.getElementById('crudModal').classList.remove('flex');
        document.getElementById('crudModal').classList.remove('items-center');
        document.getElementById('crudModal').classList.remove('justify-center');
        currentEditId = null;
        clearDynamicFields();
        document.getElementById('crudForm').reset();
    }

    // Edit section selection functions
    function selectEditSection(section) {
        // Update radio button
        document.getElementById('editSection' + section.charAt(0).toUpperCase() + section.slice(1))
            .checked = true;

        // Hide all sections first
        document.getElementById('ppaDetailsSection').classList.add('hidden');
        document.getElementById('dataSection').classList.add('hidden');

        // Show selected section
        if (section === 'ppa') {
            document.getElementById('ppaDetailsSection').classList.remove('hidden');
        } else if (section === 'data') {
            document.getElementById('dataSection').classList.remove('hidden');
        }

        updateButtonVisibility();
        document.getElementById('saveBtn').disabled = false; // Enable the save button
    }

    // Initialize tooltips when page loads
    document.addEventListener('DOMContentLoaded', function() {
        initializeTooltips();

        // Add search input event listener
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('keypress', function(event) {
                if (event.key === 'Enter') {
                    performSearch();
                }
            });
        }
    });

    // Search functionality
    let currentSearchIndex = 0;
    let searchMatches = [];

    function handleSearch(event) {
        if (event.key === 'Enter') {
            performSearch();
        }
    }

    function performSearch() {
        const searchInput = document.getElementById('searchInput');
        const searchTerm = searchInput.value.trim().toLowerCase();

        // Clear previous highlights
        clearHighlights();

        if (!searchTerm) {
            return;
        }

        const tbody = document.getElementById('sortableTableBody');
        const rows = tbody.querySelectorAll('tr');
        searchMatches = [];
        currentSearchIndex = 0;

        // Search through all rows for PPA names and indicators
        rows.forEach((row, rowIndex) => {
            const cells = row.querySelectorAll('td');
            cells.forEach((cell, cellIndex) => {
                const text = cell.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    searchMatches.push({
                        element: cell,
                        row: row,
                        rowIndex: rowIndex,
                        cellIndex: cellIndex
                    });
                }
            });
        });

        if (searchMatches.length > 0) {
            // Highlight all matches
            highlightMatches(searchTerm);

            // Scroll to first match
            scrollToMatch(0);

            // Enable navigation buttons
            updateNavigationButtons();
        } else {
            alert('No matches found for: ' + searchInput.value);

            // Disable navigation buttons
            updateNavigationButtons();
        }
    }

    function highlightMatches(searchTerm) {
        searchMatches.forEach(match => {
            const cell = match.element;
            const originalText = cell.textContent;
            const regex = new RegExp(`(${escapeRegex(searchTerm)})`, 'gi');
            cell.innerHTML = originalText.replace(regex, '<mark class="bg-yellow-300 px-1 rounded">$1</mark>');
        });
    }

    function clearHighlights() {
        const marks = document.querySelectorAll('mark');
        marks.forEach(mark => {
            const parent = mark.parentNode;
            parent.replaceChild(document.createTextNode(mark.textContent), mark);
            parent.normalize();
        });
    }

    function escapeRegex(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    function scrollToMatch(index) {
        if (index >= 0 && index < searchMatches.length) {
            const match = searchMatches[index];
            const row = match.row;

            // Scroll the row into view
            row.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });

            // Add temporary highlight effect
            row.style.transition = 'background-color 0.3s';
            row.style.backgroundColor = '#fef08a';

            setTimeout(() => {
                row.style.backgroundColor = '';
            }, 1500);
        }
    }

    function navigateToNextMatch() {
        if (searchMatches.length === 0) return;

        currentSearchIndex = (currentSearchIndex + 1) % searchMatches.length;
        scrollToMatch(currentSearchIndex);
    }

    function navigateToPrevMatch() {
        if (searchMatches.length === 0) return;

        currentSearchIndex = (currentSearchIndex - 1 + searchMatches.length) % searchMatches.length;
        scrollToMatch(currentSearchIndex);
    }

    function updateNavigationButtons() {
        const prevBtn = document.getElementById('prevMatchBtn');
        const nextBtn = document.getElementById('nextMatchBtn');
        const navigationArrows = document.getElementById('navigationArrows');

        if (searchMatches.length > 0) {
            prevBtn.disabled = false;
            nextBtn.disabled = false;
            navigationArrows.classList.remove('hidden');
            navigationArrows.classList.add('flex');
        } else {
            prevBtn.disabled = true;
            nextBtn.disabled = true;
            navigationArrows.classList.add('hidden');
            navigationArrows.classList.remove('flex');
        }
    }

    // Find and highlight record when redirected from dashboard
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const recordId = urlParams.get('record_id');
        
        if (recordId) {
            const checkForRow = setInterval(() => {
                const row = document.querySelector(`tr[data-record-id="${recordId}"]`);
                if (row) {
                    clearInterval(checkForRow);
                    row.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    row.style.transition = 'background-color 0.3s';
                    row.style.backgroundColor = '#fef08a';
                    setTimeout(() => {
                        row.style.backgroundColor = '';
                    }, 2000);
                }
            }, 100);
            setTimeout(() => clearInterval(checkForRow), 5000);
        }
    });
</script>
