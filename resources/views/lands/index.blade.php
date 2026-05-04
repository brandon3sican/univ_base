@extends('layouts.app')

@php
    $pageTitle = 'LANDS';
@endphp

@section('content')
    <!-- Data Table Container -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <!-- Table Header with Summary Statistics -->
        <div class="bg-gradient-to-r from-slate-700 to-slate-800 text-white p-6 shadow-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas fa-table mr-2"></i>
                    <h2 class="text-lg font-bold">LANDS Data Table</h2>
                </div>
                <div class="flex items-center space-x-3">
                    <!-- Toggle Actions Column Button -->
                    <button onclick="toggleActionsColumn()" id="toggleActionsBtn"
                        class="bg-white hover:bg-gray-100 text-slate-700 px-4 py-2 rounded-lg transition-all duration-200 flex items-center text-sm font-medium shadow hover:shadow-lg">
                        <i class="fas fa-eye mr-2"></i>
                        <span id="toggleActionsText">Show Actions</span>
                    </button>
                    <!-- Add New Record Button -->
                    <button onclick="openCreateModal()"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-all duration-200 text-sm font-medium shadow hover:shadow-lg hover:border-2 hover:border-white flex items-center">
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
                <thead
                    class="sticky top-0 z-10 bg-gradient-to-r from-gray-50 to-gray-100 text-gray-700 shadow-md border-b border-gray-300">
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
                        @if (count($targetYears) > 0)
                            @foreach ($targetYears as $year)
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

                </tbody>
            </table>
        </div>

        <!-- Table Footer -->
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <label class="text-sm font-medium text-gray-700">Filter by Period:</label>
                    <select id="yearFilter"
                        class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="2022-2028" selected>2022-2028</option>
                        <option value="2028-2034">2028-2034</option>
                        <option value="2034-2040">2034-2040</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
@endsection

@include('lands.partials.toggle')
@include('lands.partials.modal', ['offices' => $offices ?? [], 'ppas' => $ppas ?? []])

<script>
    let currentEditId = null;
    let ppas = [];
    let indicators = [];
    let offices = @json($offices ?? []);
    let recordTypes = [];

    // Initialize the page
    document.addEventListener('DOMContentLoaded', function() {
        loadLandsData();
        loadFormData();
    });

    // Load LANDS data for the table
    function loadLandsData() {
        fetch('/lands', {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                renderTable(data);
            })
            .catch(error => console.error('Error loading LANDS data:', error));
    }

    // Load form data (PPAs and Indicators)
    function loadFormData() {
        fetch('/lands/create', {
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
            })
            .catch(error => console.error('Error loading form data:', error));
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

        // Add PPA options (only LANDS PPAs - types_id = 5)
        if (ppaSelect) {
            ppas.forEach(ppa => {
                // Only show PPAs that belong to LANDS (types_id = 5)
                if (ppa.types_id === 5) {
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

        if (recordType && ppaSelect && indicator && nextBtn && saveBtn) {
            const isRecordTypeValid = recordType.value.trim() !== '';
            const isPpaValid = (ppaSelect.value.trim() !== '' && ppaSelect.value !== 'new') ||
                (ppaSelect.value === 'new' && newPpaName && newPpaName.value.trim() !== '');

            // Check if parent activity is required and valid
            const selectedRecordType = recordType.value;
            let isParentActivityRequired = false;
            let isParentActivityValid = true;

            if (selectedRecordType == '4' || selectedRecordType == '5' || selectedRecordType == '6') {
                isParentActivityRequired = true;
                isParentActivityValid = parentActivitySelect && parentActivitySelect.value.trim() !== '';
            }

            const hasIndicator = indicator.value.trim() !== '';
            const selectedOffices = document.querySelectorAll('input[name="office_id[]"]:checked').length;
            const hasOfficeSelection = selectedOffices > 0;


            // Check if Record Type AND PPA are valid, AND Parent Activity if required
            if (!isRecordTypeValid || !isPpaValid || (isParentActivityRequired && !isParentActivityValid)) {
                // Lock BOTH Save and Next buttons - disable them
                saveBtn.disabled = true;
                saveBtn.classList.add('opacity-50', 'cursor-not-allowed');
                saveBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                saveBtn.classList.add('bg-gray-400');

                nextBtn.disabled = true;
                nextBtn.classList.add('opacity-50', 'cursor-not-allowed');
                nextBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
                nextBtn.classList.add('bg-gray-400');

                // Hide both buttons
                saveBtn.classList.add('hidden');
                nextBtn.classList.add('hidden');

                // Hide Step 2 if it's currently visible
                if (step2Section) {
                    step2Section.classList.add('hidden');
                }
            } else {
                // Record Type AND PPA are valid - enable Save button by default
                saveBtn.disabled = false;
                saveBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                saveBtn.classList.remove('bg-gray-400');
                saveBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');

                if (hasIndicator) {
                    // Has indicator content - check office selection
                    if (hasOfficeSelection) {
                        // Has offices - enable Next button, hide Save
                        nextBtn.disabled = false;
                        nextBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                        nextBtn.classList.remove('bg-gray-400');
                        nextBtn.classList.add('bg-green-600', 'hover:bg-green-700');
                        nextBtn.classList.remove('hidden');
                        saveBtn.classList.add('hidden');
                    } else {
                        // No offices selected - disable Next button, hide Save
                        nextBtn.disabled = true;
                        nextBtn.classList.add('opacity-50', 'cursor-not-allowed');
                        nextBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
                        nextBtn.classList.add('bg-gray-400');
                        nextBtn.classList.remove('hidden');
                        saveBtn.classList.add('hidden');
                    }
                } else {
                    // No indicator content - show Save button, hide Next (everything else is optional)
                    nextBtn.classList.add('hidden');
                    saveBtn.classList.remove('hidden');

                    // Hide Step 2 if it's currently visible
                    if (step2Section) {
                        step2Section.classList.add('hidden');
                    }
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
        const checkedOffices = Array.from(document.querySelectorAll('input[name="office_id[]"]:checked'));
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
                                <th class="px-3 py-3 text-center text-xs font-semibold text-blue-600 uppercase tracking-wider">Universe</th>
                                ${years.map(year => `<th class="px-3 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">${year}</th>`).join('')}
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
                <td class="px-4 py-3 text-sm font-medium text-gray-900">${officeLabel}</td>
                <td class="px-3 py-3 text-center">
                    <input type="number" name="universe[${officeId}]" class="w-full px-2 py-1 border border-gray-300 rounded text-center font-medium text-blue-600" placeholder="0" min="0" step="1" oninput="this.value = Math.floor(Math.max(0, this.value))">
                </td>
                ${years.map(year => `
                    <td class="px-2 py-3 text-center p-1" style="min-width: 80px;">
                        <div class="space-y-1">
                            <input type="number" name="accomplishment[${officeId}][${year}]" class="w-full px-2 py-1 border border-gray-300 rounded text-center text-sm text-green-600 font-medium" placeholder="A" min="0" step="1" oninput="this.value = Math.floor(Math.max(0, this.value))">
                            <input type="number" name="targets[${officeId}][${year}]" class="w-full px-2 py-1 border border-gray-300 rounded text-center text-sm text-purple-600 font-medium" placeholder="T" min="0" step="1" oninput="this.value = Math.floor(Math.max(0, this.value))">
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
        const checkedOffices = Array.from(document.querySelectorAll('input[name="office_id[]"]:checked'));

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
        fetch(`/api/lands/ppas?record_type_id=${selectedRecordTypeId}`)
            .then(response => response.json())
            .then(data => {
                // Add filtered PPAs to dropdown (only LANDS PPAs - types_id = 5)
                data.forEach(ppa => {
                    // Only show PPAs that belong to LANDS (types_id = 5)
                    if (ppa.types_id === 5) {
                        const option = document.createElement('option');
                        option.value = ppa.id;
                        option.textContent = ppa.name;
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
            // If Step 2 is visible, regenerate tables
            if (selectAllCheckbox.checked) {
                generateUniverseTable();
            }
        }
    }

    // Update select all checkbox state when individual checkboxes change
    document.addEventListener('change', function(e) {
        if (e.target && e.target.name === 'office_id[]' && e.target.id !== 'selectAllOffices') {
            const selectAllCheckbox = document.getElementById('selectAllOffices');
            const officeCheckboxes = document.querySelectorAll('input[name="office_id[]"]');
            const checkedCount = document.querySelectorAll('input[name="office_id[]"]:checked').length;

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

            // Get PPA name for consecutive duplicate checking
            const ppaName = item.ppa ? item.ppa.name : '';
            const isConsecutiveDuplicate = ppaName === previousPpaName && ppaName !== '';

            // Update previous PPA name for next iteration
            previousPpaName = ppaName;

            // Check if record has no indicator - merge row
            const hasNoIndicator = !item.indicator || item.indicator === '' || item.indicator === null || item
                .indicator === 'undefined';

            // Check if record type is PROGRAM for green background
            const isProgram = item.ppa && item.ppa.recordType && item.ppa.recordType.name === 'PROGRAM';

            if (hasNoIndicator) {
                // Merged row - calculate correct colspan
                const bgClass = isProgram ? 'bg-green-50' : 'bg-blue-50';
                row.className = `hover:bg-gray-50 transition-colors ${bgClass}`;

                // Calculate correct colspan: 6 base columns + target columns + remarks column (exclude actions)
                let colspan = 6; // PPA, Office, Universe, Baseline, 2 Accomplishment sub-columns

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
                    <td class="px-3 py-2 text-sm text-gray-900" colspan="${colspan}">
                        ${!isConsecutiveDuplicate ? getIndentedPPAName(item) : ''}
                        ${getIndentedIndicatorName(item)}
                        <div class="text-sm text-gray-600 mt-1 ${getIndentClass(item)}">
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
                    <td class="px-3 py-2 text-sm text-gray-900 hidden actions-column">
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
                    <td class="px-3 py-2 text-sm text-gray-900">
                        ${!isConsecutiveDuplicate ? getIndentedPPAName(item) : ''}
                        ${getIndentedIndicatorName(item) || '<div class="text-xs text-gray-400 mt-1">No indicator</div>'}
                    </td>
                    <td class="px-3 py-2 text-sm text-gray-900">
                        ${formatOfficeNames(item.office_id)}
                    </td>
                    <td class="px-3 py-2 text-sm text-gray-900 text-center">
                        ${item.universe && Object.keys(item.universe).length > 0 ? formatUniverseData(item.universe) : ''}
                    </td>
                    <td class="px-3 py-2 text-sm text-gray-900 text-center">
                        ${calculateBaseline(item.universe, item.accomplishment, item.office_id)}
                    </td>
                    <td class="px-3 py-2 text-sm text-gray-900 text-center">
                        ${formatAccomplishmentPerOffice(item.accomplishment, item.office_id)}
                    </td>
                    <td class="px-3 py-2 text-sm text-gray-900 text-center">
                        ${getCurrentYearAccomplishment(item.accomplishment, item.office_id)}
                    </td>
                    <td class="px-3 py-2 text-sm text-gray-900 hidden remarks-column">${item.remarks || '-'}</td>
                    <td class="px-3 py-2 text-sm text-gray-900 hidden actions-column">
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

    // Format JSON data for display
    function formatJsonData(data) {
        if (!data || Object.keys(data).length === 0) return '-';
        return Object.entries(data)
            .map(([year, value]) => `${year}: ${value}`)
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
        let className = 'font-semibold text-lg';

        // Simplified approach: use current program counter and track hierarchy by order
        switch (recordType.toUpperCase()) {
            case 'PROGRAM':
                programCounter++;
                numbering = `${programCounter}. `;
                className = 'font-bold text-lg';
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
                className = 'font-semibold text-base ml-6';
                // Reset child counters for new project
                mainActivityCounter[programCounter][projectCounter[programCounter]] = 0;
                majorActivityCounter[programCounter][projectCounter[programCounter]] = {};
                subActivityCounter[programCounter][projectCounter[programCounter]] = {};
                break;
            case 'MAIN ACTIVITY':
                if (!mainActivityCounter[programCounter] || !mainActivityCounter[programCounter][projectCounter[
                        programCounter]]) {
                    mainActivityCounter[programCounter][projectCounter[programCounter]] = 0;
                }
                mainActivityCounter[programCounter][projectCounter[programCounter]]++;
                numbering =
                    `${programCounter}.${projectCounter[programCounter]}.${mainActivityCounter[programCounter][projectCounter[programCounter]]}. `;
                className = 'font-semibold text-base ml-12';
                // Reset child counters for new main activity
                majorActivityCounter[programCounter][projectCounter[programCounter]][mainActivityCounter[programCounter]
                    [projectCounter[programCounter]]
                ] = 0;
                subActivityCounter[programCounter][projectCounter[programCounter]][mainActivityCounter[programCounter][
                    projectCounter[programCounter]
                ]] = {};
                break;
            case 'MAJOR ACTIVITY':
                const currentMain = mainActivityCounter[programCounter][projectCounter[programCounter]];
                if (!majorActivityCounter[programCounter] || !majorActivityCounter[programCounter][projectCounter[
                        programCounter]] || !majorActivityCounter[programCounter][projectCounter[programCounter]][
                        currentMain
                    ]) {
                    majorActivityCounter[programCounter][projectCounter[programCounter]][currentMain] = 0;
                }
                majorActivityCounter[programCounter][projectCounter[programCounter]][currentMain]++;
                numbering =
                    `${programCounter}.${projectCounter[programCounter]}.${currentMain}.${majorActivityCounter[programCounter][projectCounter[programCounter]][currentMain]}. `;
                className = 'font-medium text-base ml-18';
                // Reset child counters for new major activity
                subActivityCounter[programCounter][projectCounter[programCounter]][currentMain][majorActivityCounter[
                    programCounter][projectCounter[programCounter]][currentMain]] = 0;
                break;
            case 'SUB ACTIVITY':
                const currentMainForSub = mainActivityCounter[programCounter][projectCounter[programCounter]];
                const currentMajor = majorActivityCounter[programCounter][projectCounter[programCounter]][
                    currentMainForSub
                ];
                if (!subActivityCounter[programCounter] || !subActivityCounter[programCounter][projectCounter[
                        programCounter]] || !subActivityCounter[programCounter][projectCounter[programCounter]][
                        currentMainForSub
                    ] || !subActivityCounter[programCounter][projectCounter[programCounter]][currentMainForSub][
                        currentMajor
                    ]) {
                    subActivityCounter[programCounter][projectCounter[programCounter]][currentMainForSub][
                        currentMajor
                    ] = 0;
                }
                subActivityCounter[programCounter][projectCounter[programCounter]][currentMainForSub][currentMajor]++;
                numbering =
                    `${programCounter}.${projectCounter[programCounter]}.${currentMainForSub}.${currentMajor}.${subActivityCounter[programCounter][projectCounter[programCounter]][currentMainForSub][currentMajor]}. `;
                className = 'font-medium text-sm ml-24';
                break;
            case 'ACTIONABLE TASK':
                const currentMainForTask = mainActivityCounter[programCounter][projectCounter[programCounter]];
                const currentMajorForTask = majorActivityCounter[programCounter][projectCounter[programCounter]][
                    currentMainForTask
                ];
                const currentSubForTask = subActivityCounter[programCounter][projectCounter[programCounter]][
                    currentMainForTask
                ][currentMajorForTask];
                numbering =
                    `${programCounter}.${projectCounter[programCounter]}.${currentMainForTask}.${currentMajorForTask}.${currentSubForTask}.1. `;
                className = 'font-normal text-sm ml-30';
                break;
            default:
                numbering = '';
                className = 'font-semibold text-lg';
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
                const displayValues = [`<span class="text-green-600 font-semibold">${carTotal}</span>`];

                // Add individual office values
                Object.values(universe).forEach(val => {
                    displayValues.push(val.toString());
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
                const displayValues = [`<span class="text-green-600 font-semibold">${carTotal}</span>`];

                // Add individual office values
                Object.values(universe).forEach(val => {
                    displayValues.push(val.toString());
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
                    // Get current year value or sum past years only
                    const currentValue = officeAccomplishment[currentYear];
                    const pastYearsSum = pastYears.reduce((sum, year) => {
                        return sum + (officeAccomplishment[year] || 0);
                    }, 0);

                    value = index === 0 ? pastYearsSum : (currentValue !== null && currentValue !== undefined ?
                        currentValue : pastYearsSum);

                    // Create tooltip content for past years
                    const officeName = offices.find(o => o.id == officeId)?.name || `Office ${officeId}`;
                    let yearBreakdown = `<div class="font-bold text-lg text-blue-600 mb-4">${officeName}</div>`;
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
                    const officeName = offices.find(o => o.id == officeId)?.name || `Office ${officeId}`;
                    tooltipContent = `<div class="font-bold text-lg text-blue-600 mb-4">${officeName}</div>`;
                    tooltipContent +=
                        `<div class="font-semibold text-white mb-3">Past Years Accomplishment:</div>`;
                    tooltipContent += `<div class="text-sm py-2">No Data Available</div>`;
                }

                officeAccomplishments.push(value);
                officeTooltips.push(tooltipContent);
                carTotalAccomplishment += value;
            });

            // Create CAR tooltip (sum of all office tooltips)
            let carTooltip = `<div class="font-bold text-lg text-blue-600 mb-4">CAR</div>`;
            carTooltip += `<div class="font-semibold text-white mb-3">Total Past Years Accomplishment:</div>`;
            carTooltip += pastYears.map(year => {
                const yearTotal = officeIdArray.reduce((sum, officeId) => {
                    const officeAccomplishment = accomplishment[officeId];
                    return sum + (officeAccomplishment && officeAccomplishment[year] ?
                        officeAccomplishment[year] : 0);
                }, 0);
                return `<div class="text-sm py-2">• ${year}: <span class="font-medium">${yearTotal}</span></div>`;
            }).join('');

            // Create display array: CAR total first (green), then individual accomplishments with tooltips
            const displayValues = [
                `<span class="text-green-600 font-semibold cursor-help hover:text-green-700" data-tooltip="${encodeURIComponent(carTooltip)}">${carTotalAccomplishment}</span>`
            ];

            officeAccomplishments.forEach((val, index) => {
                displayValues.push(
                    `<span class="cursor-help hover:text-gray-700" data-tooltip="${encodeURIComponent(officeTooltips[index])}">${val}</span>`
                );
            });

            return displayValues.join('<br>');
        }

        return '0';
    }

    // Calculate baseline (universe value - total accomplishment value) with CAR total first
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

        officeIdArray.forEach(officeId => {
            const universeValue = universe[officeId] || 0;
            const officeAccomplishment = accomplishment[officeId];

            if (officeAccomplishment && typeof officeAccomplishment === 'object') {
                // Sum all accomplishment years for this office
                const allYears = Object.values(officeAccomplishment).filter(val => typeof val === 'number');
                const totalAccomplishment = allYears.reduce((acc, val) => acc + val, 0);

                const baseline = universeValue - totalAccomplishment;
                baselineValues.push(baseline);
                carTotalBaseline += baseline;
            } else {
                const baseline = universeValue;
                baselineValues.push(baseline);
                carTotalBaseline += baseline;
            }
        });

        // Create display array: CAR total first (green/red based on value), then individual baselines
        const carColorClass = carTotalBaseline < 0 ? 'text-red-600' : 'text-green-600';
        const displayValues = [`<span class="${carColorClass} font-semibold">${carTotalBaseline}</span>`];

        baselineValues.forEach(val => {
            const colorClass = val < 0 ? 'text-red-600' : 'text-blue-600';
            displayValues.push(`<span class="${colorClass}">${val}</span>`);
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
                const value = currentValue !== null && currentValue !== undefined ? currentValue : 0;
                currentYearValues.push(value);
                carTotalCurrentYear += value;
            } else {
                currentYearValues.push(0);
                carTotalCurrentYear += 0;
            }
        });

        // Create display array: CAR total first (green), then individual current year values
        const displayValues = [`<span class="text-green-600 font-semibold">${carTotalCurrentYear}</span>`];
        currentYearValues.forEach(val => displayValues.push(val.toString()));

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
            checkbox.checked = data.office_id && data.office_id.includes(parseInt(checkbox.value));
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
        const checkedOffices = Array.from(document.querySelectorAll('input[name="office_id[]"]:checked'))
            .map(checkbox => parseInt(checkbox.value));
        data.office_id = checkedOffices.length > 0 ? checkedOffices : null;

        // Handle universe data (only if Step 2 is shown)
        const step2Section = document.getElementById('step2Section');
        if (step2Section && !step2Section.classList.contains('hidden')) {
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
            const accomplishmentInputs = document.querySelectorAll('input[name^="accomplishment"]');
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

            // Handle remarks
            const remarks = document.getElementById('remarks');
            if (remarks) {
                data.remarks = remarks.value.trim();
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
            submitFormData.append('types_id', '5'); // LANDS type

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
        submitFormData.append('indicator_id', ''); // Let controller handle indicator lookup/creation

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
                    submitFormData.append('accomplishment', JSON.stringify(data.accomplishment));
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
        submitFormData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute(
            'content'));

        const url = currentEditId ? `/lands/${currentEditId}` : '/lands';
        const method = currentEditId ? 'POST' :
            'POST'; // Use POST for both, Laravel will handle method spoofing

        // Add method spoofing for PUT requests
        if (currentEditId) {
            submitFormData.append('_method', 'PUT');
        }

        // Debug: Log all FormData entries
        console.log('=== FormData Debug ===');
        for (let [key, value] of submitFormData.entries()) {
            console.log(`${key}: ${value}`);
        }
        console.log('=== End FormData Debug ===');

        fetch(url, {
                method: method,
                body: submitFormData
            })
            .then(response => {
                // Check if response is actually JSON
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                } else {
                    // If not JSON, throw an error with the response text
                    return response.text().then(text => {
                        console.error('Non-JSON Response:', text);
                        throw new Error(
                            `Server returned non-JSON response: ${text.substring(0, 200)}...`
                        );
                    });
                }
            })
            .then(data => {
                if (data.success) {
                    closeModal();
                    loadLandsData();
                    showNotification(data.message, 'success');
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
        fetch(`/lands/${id}/edit`)
            .then(response => response.json())
            .then(data => {
                // Populate form fields with the record data
                const record = data.lands;
                const ppas = data.ppas || [];

                // Set basic fields
                document.getElementById('modalTitle').textContent = 'Edit LANDS Record';
                document.getElementById('record_type_id').value = record.record_type_id || '';
                document.getElementById('indicator_id').value = record.indicator_id || '';

                // Show modal first
                document.getElementById('crudModal').classList.remove('hidden');
                document.getElementById('crudModal').classList.add('flex');
                document.getElementById('crudModal').classList.add('items-center');
                document.getElementById('crudModal').classList.add('justify-center');

                // Load form data to populate dropdowns
                loadFormData().then(() => {
                    // After dropdowns are populated, set the PPA value
                    setTimeout(() => {
                        document.getElementById('ppa_id').value = record.ppa_id || '';

                        // Handle PPA selection
                        if (record.ppa_id) {
                            handlePpaSelection();
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

    // Delete record
    function deleteRecord(id) {
        currentEditId = id;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function confirmDelete() {
        fetch(`/lands/${currentEditId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeDeleteModal();
                    loadLandsData();
                    showNotification(data.message, 'success');
                } else {
                    showNotification('Error occurred', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error occurred', 'error');
            });
    }

    // Modal functions
    function closeModal() {
        document.getElementById('crudModal').classList.add('hidden');
        document.getElementById('crudForm').reset();
        currentEditId = null;
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
            `fixed top-4 right-4 px-4 py-2 rounded-lg text-white ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} z-50`;
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
        if (recordTypeName === 'MAJOR ACTIVITY' || recordTypeName === 'SUB ACTIVITY' || recordTypeName ===
            'ACTIONABLE TASK') {
            parentActivitySection.classList.remove('hidden');
            loadParentActivities(recordTypeName);

            // Show required indicator for record types 4, 5, 6
            if (selectedRecordType == '4' || selectedRecordType == '5' || selectedRecordType == '6') {
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

        console.log('Looking for parent activities of type:', parentRecordType, 'ID:', parentRecordTypeId);
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
            console.log('Checking PPA:', ppa.name, 'record_type_id:', ppa.record_type_id, 'vs parent ID:',
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
        console.log('PPAs already loaded:', ppas);
    }

    // Load indicators for modal
    function loadIndicators() {
        // Indicators are already loaded globally from the initial page load
        // This function can be used to refresh indicator data if needed
        console.log('Indicators already loaded:', indicators);
    }

    // Load types for modal
    function loadTypes() {
        // Types data would be loaded here if needed
        console.log('Types loading placeholder');
    }

    // Load PPA details for modal
    function loadPpaDetails() {
        // PPA details data would be loaded here if needed
        console.log('PPA details loading placeholder');
    }

    // Open create modal
    function openCreateModal() {
        currentEditId = null;
        document.getElementById('modalTitle').textContent = 'Add New LANDS Record';
        document.getElementById('crudForm').reset();
        clearDynamicFields();
        document.getElementById('crudModal').classList.remove('hidden');
        document.getElementById('crudModal').classList.add('flex');
        document.getElementById('crudModal').classList.add('items-center');
        document.getElementById('crudModal').classList.add('justify-center');

        // Reset parent activity section
        document.getElementById('parentActivitySection').classList.add('hidden');
        document.getElementById('parent_activity_id').innerHTML = '<option value="">Select Parent Activity</option>';

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
            console.log('No record type selected for refresh');
            return;
        }

        // Get selected record type name
        const selectedRecordTypeObj = recordTypes.find(rt => rt.id == selectedRecordType);
        const recordTypeName = selectedRecordTypeObj ? selectedRecordTypeObj.name.toUpperCase() : '';

        // Only refresh if it's a hierarchical record type
        if (recordTypeName === 'MAJOR ACTIVITY' || recordTypeName === 'SUB ACTIVITY' || recordTypeName ===
            'ACTIONABLE TASK') {
            console.log('Refreshing parent activities for:', recordTypeName);

            // Fetch fresh data from server using the same endpoint as initial load
            fetch('/lands/create', {
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
                    console.log('Fresh PPAs loaded:', ppas);
                    loadParentActivities(recordTypeName);
                })
                .catch(error => {
                    console.error('Error refreshing PPAs:', error);
                    // Show error message in the dropdown
                    const parentActivitySelect = document.getElementById('parent_activity_id');
                    const loadingMessage = document.getElementById('parentLoadingMessage');
                    parentActivitySelect.innerHTML = '<option value="">Error loading data</option>';
                    loadingMessage.classList.add('hidden');
                });
        }
    }

    // Initialize tooltips when page loads
    document.addEventListener('DOMContentLoaded', function() {
        initializeTooltips();
    });
</script>
