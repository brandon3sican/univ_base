<!-- Create Modal -->
<div id="stoModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-7xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-4 rounded-t-lg">
            <h3 class="text-xl font-semibold" id="modalTitle">Add New STO Record</h3>
            <button onclick="closeModal()" class="absolute top-4 right-4 text-white hover:text-gray-200">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="stoForm" class="p-6">
            <!-- Hidden field for STO type -->
            <input type="hidden" name="types_id" value="2">
            
            <!-- Step 1: Basic Information -->
            <div id="step1" class="space-y-4">
                <!-- Record Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Record Type</label>
                    <select name="record_type" id="recordType"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Record Type</option>
                        @if(isset($recordTypes))
                            @foreach($recordTypes as $recordType)
                                @php
                                    $prefix = '';
                                    // Debug: log the actual record type name
                                    \Log::info('Record type name: ' . $recordType->name);
                                    
                                    switch(strtolower($recordType->name)) {
                                        case 'program':
                                            $prefix = 'I. ';
                                            break;
                                        case 'project':
                                            $prefix = 'A. ';
                                            break;
                                        case 'main activity':
                                        case 'main':
                                            $prefix = '1. ';
                                            break;
                                        case 'sub-activity':
                                            $prefix = '1.1. ';
                                            break;
                                        case 'sub-sub-activity':
                                            $prefix = '1.1.1. ';
                                            break;
                                        default:
                                            // Check if it contains 'sub' twice for sub-sub activity
                                            if(strpos(strtolower($recordType->name), 'sub') !== false && substr_count(strtolower($recordType->name), 'sub') >= 2) {
                                                $prefix = '1.1.1. ';
                                            }
                                            break;
                                    }
                                @endphp
                                <option value="{{ $recordType->id }}">{{ $prefix }}{{ $recordType->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <!-- PPA and other fields (Hidden until record type is selected) -->
                <div id="ppaSection" class="hidden">
                    <!-- PPA -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">PPA</label>
                        <div class="flex space-x-2">
                            <select name="ppa" id="ppaSelect"
                                class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Select PPA</option>
                                @if(isset($ppas))
                                    @php
                                        $uniquePpas = [];
                                        $seenNames = [];
                                        foreach($ppas as $ppa) {
                                            if($ppa->types_id == 2 && !in_array($ppa->name, $seenNames)) {
                                                $uniquePpas[] = $ppa;
                                                $seenNames[] = $ppa->name;
                                            }
                                        }
                                    @endphp
                                    @foreach($uniquePpas as $ppa)
                                        <option value="{{ $ppa->name }}" data-record-type="{{ $ppa->record_type_id }}">{{ $ppa->name }}</option>
                                    @endforeach
                                @endif
                                <option value="new">+ Register New PPA</option>
                            </select>
                        </div>
                        
                        <!-- New PPA Input (Hidden by default) -->
                        <div id="newPpaDiv" class="hidden mt-2">
                            <input type="text" name="new_ppa" id="newPpaInput"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Enter new PPA name...">
                        </div>
                    </div>

                    <!-- Parent Activity (Conditional) -->
                    <div id="parentActivityDiv" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Parent Activity (Optional)</label>
                        <select name="parent_activity" id="parentActivity"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Parent Activity</option>
                            <!-- Options will be populated dynamically -->
                        </select>
                    </div>

                    <!-- Indicator -->
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Indicator (Optional)</label>
                        <textarea name="indicator" id="indicatorField" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Enter indicator details..."
                            oninput="handleIndicatorChange()"></textarea>
                    </div>

                    <!-- Offices -->
                    <div id="officesSection">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Offices</label>
                        <div class="border border-gray-300 rounded-lg p-3">
                            <div class="grid grid-cols-3 gap-4 max-h-40 overflow-y-auto">
                                <div class="flex items-center">
                                    <span class="text-sm text-gray-600">Select offices:</span>
                                </div>
                                <div class="flex items-center">
                                    <label class="flex items-center">
                                        <input type="checkbox" id="selectAllOffices" 
                                            class="mr-2 text-blue-600 focus:ring-blue-500">
                                        <span class="text-sm text-blue-600">Select/Unselect All</span>
                                    </label>
                                </div>
                                <div></div>
                                @if(isset($offices))
                                    @foreach($offices as $office)
                                        <label class="flex items-center">
                                            <input type="checkbox" name="offices[]" value="{{ $office->id }}"
                                                class="mr-2 text-blue-600 focus:ring-blue-500 office-checkbox">
                                            <span>{{ $office->name }}</span>
                                        </label>
                                    @endforeach
                                @else
                                    <p class="text-gray-500 text-sm col-span-3">No offices available</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-2 pt-4">
                    <button type="button" onclick="closeModal()"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg transition-all duration-200">
                        Cancel
                    </button>
                    <!-- Next/Submit Button -->
                    <button type="button" onclick="goToStep2()" id="nextButton"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-all duration-200">
                        Next <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </div>

            <!-- Step 2: Values (appears after Next button) -->
            <div id="step2" class="space-y-3 hidden">
                <!-- Step Header -->
                <div class="mb-3">
                    <h3 class="text-base font-semibold text-gray-800 mb-1">Office Performance Data</h3>
                    <p class="text-xs text-gray-600 mb-3">Enter universe for each selected office</p>
                    
                    <!-- Year Input - Hidden -->
                    <div class="flex items-center space-x-2 hidden">
                        <label class="text-sm font-medium text-gray-700">Year:</label>
                        <select id="globalYear" 
                            class="px-3 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 w-32">
                            @php
                                $currentYear = date('Y');
                                for($year = 2022; $year <= 2040; $year++) {
                                    echo "<option value='$year'" . ($year == $currentYear ? " selected" : "") . ">$year</option>";
                                }
                            @endphp
                        </select>
                    </div>
                </div>
                
                <!-- Office Values Table -->
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse bg-white border border-gray-300">
                        <thead class="bg-gray-50 border-b border-gray-300">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-700 border-r border-gray-300 w-25" rowspan="2">Office</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-700 border-r border-gray-300 w-25" rowspan="2">Universe</th>
                                <th class="px-3 py-2 text-center text-xs font-medium text-gray-700 border-r border-gray-300" colspan="7">Accomplishments (2022-2028)</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-700 w-50" rowspan="2">Remarks</th>
                            </tr>
                            <tr>
                                <th class="px-3 py-2 text-center text-xs font-medium text-gray-700">2022</th>
                                <th class="px-3 py-2 text-center text-xs font-medium text-gray-700">2023</th>
                                <th class="px-3 py-2 text-center text-xs font-medium text-gray-700">2024</th>
                                <th class="px-3 py-2 text-center text-xs font-medium text-gray-700">2025</th>
                                <th class="px-3 py-2 text-center text-xs font-medium text-gray-700">2026</th>
                                <th class="px-3 py-2 text-center text-xs font-medium text-gray-700">2027</th>
                                <th class="px-3 py-2 text-center text-xs font-medium text-gray-700 border-r border-gray-300">2028</th>
                            </tr>
                        </thead>
                        <tbody id="officeValuesTableBody">
                            <!-- Office rows will be dynamically generated here -->
                        </tbody>
                    </table>
                </div>
                
                <!-- Hidden template for row generation -->
                <div id="officeRowTemplate" class="hidden">
                    <tr>
                        <td class="px-3 py-2 border-r border-gray-300">
                            <span class="text-sm text-gray-900 office-name"></span>
                        </td>
                        <td class="px-3 py-2 border-r border-gray-300">
                            <input type="number" 
                                class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                placeholder="0" data-field="universe">
                        </td>
                        <td class="px-3 py-2 border-r border-gray-300 text-center">
                            <input type="number" 
                                class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-center" 
                                placeholder="0" data-year="2022" data-field="accomplishment">
                        </td>
                        <td class="px-3 py-2 border-r border-gray-300 text-center">
                            <input type="number" 
                                class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-center" 
                                placeholder="0" data-year="2023" data-field="accomplishment">
                        </td>
                        <td class="px-3 py-2 border-r border-gray-300 text-center">
                            <input type="number" 
                                class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-center" 
                                placeholder="0" data-year="2024" data-field="accomplishment">
                        </td>
                        <td class="px-3 py-2 border-r border-gray-300 text-center">
                            <input type="number" 
                                class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-center" 
                                placeholder="0" data-year="2025" data-field="accomplishment">
                        </td>
                        <td class="px-3 py-2 border-r border-gray-300 text-center">
                            <input type="number" 
                                class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-center" 
                                placeholder="0" data-year="2026" data-field="accomplishment">
                        </td>
                        <td class="px-3 py-2 border-r border-gray-300 text-center">
                            <input type="number" 
                                class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-center" 
                                placeholder="0" data-year="2027" data-field="accomplishment">
                        </td>
                        <td class="px-3 py-2 border-r border-gray-300 text-center">
                            <input type="number" 
                                class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-center" 
                                placeholder="0" data-year="2028" data-field="accomplishment">
                        </td>
                        <td class="px-3 py-2 border-r border-gray-300">
                            <textarea class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none" 
                                rows="1" placeholder="Enter remarks..." data-field="remarks"></textarea>
                        </td>
                    </tr>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-between items-center pt-2">
                    <button type="button" onclick="goToStep1()"
                        class="flex items-center px-3 py-1.5 bg-gray-500 hover:bg-gray-600 text-white rounded text-sm transition-all duration-200">
                        <i class="fas fa-arrow-left mr-1"></i> Back
                    </button>
                    <div class="flex items-center space-x-2">
                        <button type="button" onclick="closeModal()"
                            class="px-3 py-1.5 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded text-sm transition-all duration-200">
                            Cancel
                        </button>
                        <button type="button" onclick="goToStep3()"
                            class="flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm transition-all duration-200">
                            Next <i class="fas fa-arrow-right mr-1"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Step 3: Target Values (7-year periods) -->
            <div id="step3" class="space-y-3 hidden">
                <!-- Step Header -->
                <div class="mb-3">
                    <h3 class="text-base font-semibold text-gray-800 mb-1">7-Year Target Values</h3>
                    <p class="text-xs text-gray-600 mb-3">Set target values for each office in 7-year periods: 2022-2028, 2028-2034, 2034-2040</p>
                </div>
                
                <!-- Target Periods Container -->
                <div id="targetPeriodsContainer" class="space-y-4">
                    <!-- Initial target periods will be generated by JavaScript -->
                </div>
                
                <!-- Add Period Buttons -->
                <div class="flex justify-center space-x-2">
                    <button type="button" onclick="addTargetPeriod(2028)" 
                        class="flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded text-sm transition-all duration-200">
                        <i class="fas fa-plus mr-2"></i> Add 2028-2034
                    </button>
                    <button type="button" onclick="addTargetPeriod(2034)" 
                        class="flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded text-sm transition-all duration-200">
                        <i class="fas fa-plus mr-2"></i> Add 2034-2040
                    </button>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-between items-center pt-2 border-t border-gray-200">
                    <button type="button" onclick="backToStep2()"
                        class="flex items-center px-3 py-1.5 bg-gray-500 hover:bg-gray-600 text-white rounded text-sm transition-all duration-200">
                        <i class="fas fa-arrow-left mr-1"></i> Back
                    </button>
                    <div class="flex items-center space-x-2">
                        <button type="button" onclick="closeModal()"
                            class="px-3 py-1.5 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded text-sm transition-all duration-200">
                            Cancel
                        </button>
                        <button type="submit"
                            class="flex items-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded text-sm transition-all duration-200">
                            <i class="fas fa-save mr-1"></i> Save Record
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="stoEditModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-7xl w-full mx-4" style="max-height: 90vh; overflow-y: auto;">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-orange-600 to-orange-700 text-white px-6 py-4 rounded-t-lg">
            <h3 class="text-xl font-semibold">Edit STO Record</h3>
            <button onclick="closeEditModal()" class="absolute top-4 right-4 text-white hover:text-gray-200">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6">
            <!-- Step Selection -->
            <div id="stepSelection" class="space-y-4">
                <h4 class="text-lg font-semibold text-gray-800 mb-4">What would you like to edit?</h4>
                
                <div class="space-y-3">
                    <button onclick="editStep1()" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-all duration-200 text-left">
                        <i class="fas fa-edit mr-2"></i>
                        <div>
                            <div class="font-semibold">Step 1: Basic Information</div>
                            <div class="text-sm opacity-90">Record Type, PPA, Parent Activity, Indicator, Offices</div>
                        </div>
                    </button>
                    
                    <button onclick="editStep2()" 
                        class="w-full bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg transition-all duration-200 text-left">
                        <i class="fas fa-chart-bar mr-2"></i>
                        <div>
                            <div class="font-semibold">Step 2: Performance Data</div>
                            <div class="text-sm opacity-90">Universe, Accomplishments, and Remarks for all years</div>
                        </div>
                    </button>
                    
                    <button onclick="editStep3()" 
                        class="w-full bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg transition-all duration-200 text-left">
                        <i class="fas fa-bullseye mr-2"></i>
                        <div>
                            <div class="font-semibold">Step 3: Target Values</div>
                            <div class="text-sm opacity-90">Target values for all years</div>
                        </div>
                    </button>
                </div>
                
                <!-- Current STO Info -->
                <div id="currentStoInfo" class="mt-6 p-4 bg-gray-50 rounded-lg">
                    <!-- Will be populated dynamically -->
                </div>
            </div>
            
            <!-- Step 1: Basic Information (appears after clicking Step 1) -->
            <div id="step1" class="space-y-4 hidden">
                <!-- Will be populated dynamically -->
            </div>
            
            <!-- Step 2: Performance Data (appears after clicking Step 2) -->
            <div id="step2" class="space-y-4 hidden">
                <!-- Will be populated dynamically -->
            </div>
            
            <!-- Step 3: Target Values (appears after clicking Step 3) -->
            <div id="step3" class="space-y-4 hidden">
                <!-- Will be populated dynamically -->
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="stoDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <!-- Modal Header -->
        <div class="bg-red-600 text-white px-6 py-4 rounded-t-lg">
            <h3 class="text-xl font-semibold">Delete STO Record</h3>
        </div>
        
        <!-- Modal Body -->
        <div class="p-6">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0 mr-4">
                    <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                </div>
                <div>
                    <p class="text-gray-700 font-medium">Are you sure you want to delete this STO record?</p>
                    <p class="text-gray-500 text-sm mt-1">This action cannot be undone.</p>
                </div>
            </div>
            
            <div class="text-sm text-gray-600 mb-4">
                <strong>Record ID:</strong> <span id="deleteStoId">-</span>
            </div>
        </div>
        
        <!-- Modal Footer -->
        <div class="bg-gray-100 px-6 py-4 rounded-b-lg flex justify-end space-x-2">
            <button onclick="closeDeleteModal()" 
                class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg transition-colors duration-200">
                Cancel
            </button>
            <button onclick="confirmDelete()" 
                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                <i class="fas fa-trash mr-2"></i>Delete Record
            </button>
        </div>
    </div>
</div>

<script>
    // Modal functionality
    function openCreateModal() {
        document.getElementById('stoModal').classList.remove('hidden');
        document.getElementById('modalTitle').textContent = 'Add New STO Record';
        resetForm();
        goToStep1();
    }

    function closeModal() {
        document.getElementById('stoModal').classList.add('hidden');
        document.getElementById('stoForm').reset();
        currentStoId = null;
        // Refresh the page when modal is closed
        location.reload();
        
        // Reset step visibility
        document.getElementById('step1').classList.remove('hidden');
        document.getElementById('step2').classList.add('empty-state');
        document.getElementById('step2').classList.add('hidden');
        document.getElementById('step3').classList.add('hidden');
        
        // Clear dynamic content
        const officeValuesTableBody = document.getElementById('officeValuesTableBody');
        if (officeValuesTableBody) {
            officeValuesTableBody.innerHTML = '';
        }
    }

    function resetForm() {
        document.getElementById('stoForm').reset();
        document.getElementById('parentActivityDiv').classList.add('hidden');
        updateButtonText();
    }

    function populateStep2EditTable(sto, allYears, currentYear) {
        // Use generateOfficeValueFields instead of template since Step 2 uses different structure
        generateOfficeValueFields();
    }

    function handleIndicatorChange() {
        const indicatorField = document.getElementById('indicatorField');
        const officeCheckboxes = document.querySelectorAll('input[name="offices[]"]');
        const selectAllCheckbox = document.getElementById('selectAllOffices');
        
        // If indicator field is empty, unselect all offices
        if (indicatorField.value.trim() === '') {
            officeCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = false;
            }
        }
    }

    // Step navigation
    function goToStep1() {
        document.getElementById('step1').classList.remove('hidden');
        document.getElementById('step2').classList.add('hidden');
        document.getElementById('step3').classList.add('hidden');
    }

    function validateStep1() {
        const indicator = document.querySelector('textarea[name="indicator"]').value.trim();
        const checkedOffices = document.querySelectorAll('input[name="offices[]"]:checked');

        // Both empty - can save directly
        if (!indicator && checkedOffices.length === 0) {
            return { valid: true, canSave: true };
        }

        // Both filled - can proceed to step 2
        if (indicator && checkedOffices.length > 0) {
            return { valid: true, canSave: false };
        }

        // One filled, other empty - invalid
        return { valid: false, canSave: false, error: 'If indicator is provided, at least one office must be selected. If offices are selected, indicator must be provided.' };
    }

    function goToStep2() {
        const validation = validateStep1();

        if (!validation.valid) {
            alert(validation.error);
            return;
        }

        if (validation.canSave) {
            // Submit form directly
            submitForm();
        } else {
            // Generate dynamic office value fields
            generateOfficeValueFields();
            
            // Hide all steps and show only Step 2
            document.getElementById('step1').classList.add('hidden');
            document.getElementById('step3').classList.add('hidden');
            document.getElementById('step2').classList.remove('hidden');
        }
    }

    function goToStep3() {
        // Get offices from selected checkboxes if no STO data
        let stoData = window.currentSto;
        
        if (!stoData) {
            console.info('No STO data in window.currentSto, using selected offices (normal for new records)');
            // Get offices from checked checkboxes
            const checkedOffices = document.querySelectorAll('input[name="offices[]"]:checked');
            const offices = @json($offices);
            
            const selectedOffices = [];
            checkedOffices.forEach(checkbox => {
                const officeId = checkbox.value;
                const office = offices.find(o => o.id == officeId);
                if (office) {
                    selectedOffices.push(office);
                }
            });
            
            stoData = {
                offices: selectedOffices,
                universe_data: {},
                accomplishment_data: {},
                remarks_data: {}
            };
        }
        
        // Generate the Step 2 table first if it doesn't exist
        const tbody = document.getElementById('officeValuesTableBody');
        if (tbody.querySelectorAll('tr').length === 0) {
            generateOfficeValueFields();
            
            // Wait a moment for the table to be generated, then save data
            setTimeout(() => {
                saveStep2Data();
                
                // Populate Step 2 table with office data
                populateStep2EditTable(stoData, [2022, 2023, 2024, 2025, 2026, 2027, 2028], 2026);
                
                // Initialize target periods if not already done
                if (document.getElementById('targetPeriodsContainer').children.length === 0) {
                    initializeTargetPeriods();
                }
                
                document.getElementById('step2').classList.add('hidden');
                document.getElementById('step3').classList.remove('hidden');
            }, 100);
        } else {
            // Table already exists, save current data and proceed
            saveStep2Data();
            
            // Populate Step 2 table with office data
            populateStep2EditTable(stoData, [2022, 2023, 2024, 2025, 2026, 2027, 2028], 2026);
            
            // Initialize target periods if not already done
            if (document.getElementById('targetPeriodsContainer').children.length === 0) {
                initializeTargetPeriods();
            }
            
            document.getElementById('step2').classList.add('hidden');
            document.getElementById('step3').classList.remove('hidden');
        }
    }

    function backToStep2() {
        // Save current Step 3 data before going back
        saveStep3Data();
        
        // Restore Step 2 data
        restoreStep2Data();
        
        document.getElementById('step3').classList.add('hidden');
        document.getElementById('step2').classList.remove('hidden');
    }

    function saveStep2Data() {
        // Save all Step 2 input values to a global variable
        window.step2Data = {
            offices: [],
            universe: {},
            accomplishments: {},
            remarks: {}
        };
        
        const tbody = document.getElementById('officeValuesTableBody');
        const rows = tbody.querySelectorAll('tr');
        
        rows.forEach(row => {
            const officeNameCell = row.querySelector('.office-name');
            if (officeNameCell) {
                const officeName = officeNameCell.textContent.trim();
                window.step2Data.offices.push(officeName);
                
                // Save universe
                const universeInput = row.querySelector('input[data-field="universe"]');
                if (universeInput) {
                    window.step2Data.universe[officeName] = universeInput.value;
                }
                
                // Save accomplishments for each year
                const years = [2022, 2023, 2024, 2025, 2026, 2027, 2028];
                window.step2Data.accomplishments[officeName] = {};
                
                years.forEach(year => {
                    const accomplishmentInput = row.querySelector(`input[data-year="${year}"][data-field="accomplishment"]`);
                    if (accomplishmentInput) {
                        window.step2Data.accomplishments[officeName][year] = accomplishmentInput.value;
                    }
                });
                
                // Save remarks
                const remarksInput = row.querySelector('textarea[data-field="remarks"]');
                if (remarksInput) {
                    window.step2Data.remarks[officeName] = remarksInput.value;
                }
            }
        });
        
        console.log('Step 2 data saved:', window.step2Data);
    }

    function restoreStep2Data() {
        if (!window.step2Data) {
            console.log('No Step 2 data to restore');
            return;
        }
        
        console.log('Restoring Step 2 data:', window.step2Data);
        
        const tbody = document.getElementById('officeValuesTableBody');
        const rows = tbody.querySelectorAll('tr');
        
        rows.forEach(row => {
            const officeNameCell = row.querySelector('.office-name');
            if (officeNameCell) {
                const officeName = officeNameCell.textContent.trim();
                
                // Restore universe
                if (window.step2Data.universe[officeName]) {
                    const universeInput = row.querySelector('input[data-field="universe"]');
                    if (universeInput) {
                        universeInput.value = window.step2Data.universe[officeName];
                    }
                }
                
                // Restore accomplishments for each year
                if (window.step2Data.accomplishments[officeName]) {
                    const years = [2022, 2023, 2024, 2025, 2026, 2027, 2028];
                    
                    years.forEach(year => {
                        if (window.step2Data.accomplishments[officeName][year]) {
                            const accomplishmentInput = row.querySelector(`input[data-year="${year}"][data-field="accomplishment"]`);
                            if (accomplishmentInput) {
                                accomplishmentInput.value = window.step2Data.accomplishments[officeName][year];
                            }
                        }
                    });
                }
                
                // Restore remarks
                if (window.step2Data.remarks[officeName]) {
                    const remarksInput = row.querySelector('textarea[data-field="remarks"]');
                    if (remarksInput) {
                        remarksInput.value = window.step2Data.remarks[officeName];
                    }
                }
            }
        });
    }

    function saveStep3Data() {
        // Save Step 3 data if needed (for future implementation)
        console.log('Step 3 data saved (placeholder)');
    }

    function initializeTargetPeriods() {
        // Add the first period (2022-2027)
        addTargetPeriod();
    }

    let targetPeriodCount = 0;

    function addTargetPeriod(startYearOverride = null) {
        const container = document.getElementById('targetPeriodsContainer');
        const checkedOffices = Array.from(document.querySelectorAll('input[name="offices[]"]:checked'));
        const offices = @json($offices);
        
        targetPeriodCount++;
        const startYear = startYearOverride || (2022 + ((targetPeriodCount - 1) * 7));
        const endYear = startYear + 6;
        
        const periodDiv = document.createElement('div');
        periodDiv.className = 'border border-gray-300 rounded-lg p-4 bg-gray-50';
        periodDiv.innerHTML = `
            <div class="flex justify-between items-center mb-3">
                <h4 class="font-semibold text-gray-800">${startYear}-${endYear}</h4>
                ${targetPeriodCount > 1 ? `<button type="button" onclick="removeTargetPeriod(this)" class="text-red-600 hover:text-red-700 text-sm">
                    <i class="fas fa-trash"></i> Remove
                </button>` : ''}
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full border-collapse bg-white border border-gray-300">
                    <thead class="bg-gray-50 border-b border-gray-300">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-700 border-r border-gray-300">Office</th>
                            ${Array.from({length: 7}, (_, i) => startYear + i).map(year => 
                                `<th class="px-3 py-2 text-center text-xs font-medium text-gray-700 border-r border-gray-300">${year}</th>`
                            ).join('')}
                        </tr>
                    </thead>
                    <tbody>
                        ${checkedOffices.length > 0 ? checkedOffices.map(checkbox => {
                            const officeId = checkbox.value;
                            const office = offices.find(o => o.id == officeId);
                            return `
                                <tr class="border-b border-gray-200">
                                    <td class="px-3 py-2 border-r border-gray-300">
                                        <div class="font-medium text-gray-900 text-sm">${office.name}</div>
                                    </td>
                                    ${Array.from({length: 7}, (_, i) => {
                                        const year = startYear + i;
                                        return `
                                            <td class="px-3 py-2 border-r border-gray-300">
                                                <input type="number" name="target_${officeId}_${year}" 
                                                    class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                    placeholder="0"
                                                    min="0">
                                            </td>
                                        `;
                                    }).join('')}
                                </tr>
                            `;
                        }).join('') : `<tr><td colspan="8" class="px-3 py-2 text-center text-gray-500 text-sm">No offices selected</td></tr>`}
                    </tbody>
                </table>
            </div>
        `;
        
        container.appendChild(periodDiv);
    }

    function removeTargetPeriod(button) {
        button.closest('.border').remove();
    }

    function generateOfficeValueFields() {
        const tbody = document.getElementById('officeValuesTableBody');
        const checkedOffices = document.querySelectorAll('input[name="offices[]"]:checked');
        const offices = @json($offices);
        
        console.log('generateOfficeValueFields called');
        console.log('Checked offices:', checkedOffices.length);
        console.log('Tbody found:', !!tbody);
        
        // Clear existing content
        tbody.innerHTML = '';
        
        // Set current year in global year input
        document.getElementById('globalYear').value = new Date().getFullYear();
        
        if (checkedOffices.length === 0) {
            tbody.innerHTML = '<tr><td colspan="11" class="px-3 py-4 text-center text-gray-500 text-sm">No offices selected</td></tr>';
            console.log('No offices selected, showing message');
            return;
        }
        
        console.log('Generating rows for', checkedOffices.length, 'offices');
        
        checkedOffices.forEach((checkbox, index) => {
            const officeId = checkbox.value;
            const office = offices.find(o => o.id == officeId);
            
            console.log('Processing office:', officeId, office?.name);
            
            if (office) {
                const row = document.createElement('tr');
                row.className = 'border-b border-gray-200 hover:bg-gray-50';
                row.innerHTML = `
                    <td class="px-3 py-2 border-r border-gray-300 align-top">
                        <div class="font-medium text-gray-900 text-sm office-name">${office.name}</div>
                    </td>
                    <td class="px-3 py-2 border-r border-gray-300 align-top">
                        <input type="number" name="universe_${officeId}" 
                            class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="0"
                            min="0"
                            data-field="universe">
                    </td>
                    <td class="px-3 py-2 border-r border-gray-300 align-top">
                        <input type="number" name="accomplishment_2022_${officeId}"
                            class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="0"
                            min="0"
                            data-year="2022"
                            data-field="accomplishment">
                    </td>
                    <td class="px-3 py-2 border-r border-gray-300 align-top">
                        <input type="number" name="accomplishment_2023_${officeId}"
                            class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="0"
                            min="0"
                            data-year="2023"
                            data-field="accomplishment">
                    </td>
                    <td class="px-3 py-2 border-r border-gray-300 align-top">
                        <input type="number" name="accomplishment_2024_${officeId}"
                            class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="0"
                            min="0"
                            data-year="2024"
                            data-field="accomplishment">
                    </td>
                    <td class="px-3 py-2 border-r border-gray-300 align-top">
                        <input type="number" name="accomplishment_2025_${officeId}"
                            class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="0"
                            min="0"
                            data-year="2025"
                            data-field="accomplishment">
                    </td>
                    <td class="px-3 py-2 border-r border-gray-300 align-top">
                        <input type="number" name="accomplishment_2026_${officeId}"
                            class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="0"
                            min="0"
                            data-year="2026"
                            data-field="accomplishment">
                    </td>
                    <td class="px-3 py-2 border-r border-gray-300 align-top">
                        <input type="number" name="accomplishment_2027_${officeId}"
                            class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="0"
                            min="0"
                            data-year="2027"
                            data-field="accomplishment">
                    </td>
                    <td class="px-3 py-2 border-r border-gray-300 align-top">
                        <input type="number" name="accomplishment_2028_${officeId}"
                            class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="0"
                            min="0"
                            data-year="2028"
                            data-field="accomplishment">
                    </td>
                    <td class="px-3 py-2 align-top">
                        <textarea name="remarks_${officeId}" rows="2"
                            class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 resize-y"
                            placeholder="Enter remarks..."
                            data-field="remarks"></textarea>
                    </td>
                `;
                tbody.appendChild(row);
                console.log('Added row for office:', office.name);
            }
        });
        
        console.log('Table generation complete. Total rows:', tbody.querySelectorAll('tr').length);
    }

    function validateStep2() {
        const checkedOffices = document.querySelectorAll('input[name="offices[]"]:checked');
        
        // Validate global year
        const globalYear = document.getElementById('globalYear').value;
        const yearNum = parseFloat(globalYear) || 0;
        
        if (yearNum < 2000 || yearNum > 2100) {
            alert('Year must be between 2000 and 2100');
            return false;
        }
        
        for (let checkbox of checkedOffices) {
            const officeId = checkbox.value;
            const universe = document.querySelector(`input[name="universe_${officeId}"]`).value;
            const accomplishment = document.querySelector(`input[name="accomplishment_${officeId}"]`).value;
            const remarks = document.querySelector(`textarea[name="remarks_${officeId}"]`).value.trim();
            
            // Convert to numbers for comparison
            const universeNum = parseFloat(universe) || 0;
            const accomplishmentNum = parseFloat(accomplishment) || 0;
            
            // Accomplishment cannot exceed universe
            if (accomplishmentNum > universeNum && universeNum > 0) {
                alert(`Accomplishment value (${accomplishmentNum}) cannot exceed universe value (${universeNum})`);
                return false;
            }
            
            // If accomplishment has value, remarks is required
            if (accomplishmentNum > 0 && !remarks) {
                alert('Remarks are required when accomplishment value is provided');
                return false;
            }
        }
        
        return true;
    }

    // Update button text and state based on form state
    function updateButtonText() {
        const validation = validateStep1();
        const nextButton = document.getElementById('nextButton');

        if (!validation.valid) {
            // Invalid state - disable button
            nextButton.innerHTML = 'Fill Required Fields';
            nextButton.disabled = true;
            nextButton.classList.remove('bg-blue-600', 'hover:bg-blue-700', 'bg-green-600', 'hover:bg-green-700');
            nextButton.classList.add('bg-gray-400', 'cursor-not-allowed');
        } else if (validation.canSave) {
            // Both empty - save record
            nextButton.innerHTML = '<i class="fas fa-save mr-2"></i> Save Record';
            nextButton.disabled = false;
            nextButton.classList.remove('bg-blue-600', 'hover:bg-blue-700', 'bg-gray-400', 'cursor-not-allowed');
            nextButton.classList.add('bg-green-600', 'hover:bg-green-700');
        } else {
            // Both filled - next step
            nextButton.innerHTML = 'Next <i class="fas fa-arrow-right ml-2"></i>';
            nextButton.disabled = false;
            nextButton.classList.remove('bg-green-600', 'hover:bg-green-700', 'bg-gray-400', 'cursor-not-allowed');
            nextButton.classList.add('bg-blue-600', 'hover:bg-blue-700');
        }
    }

    // Record type change handler
    document.getElementById('recordType').addEventListener('change', function () {
        const parentDiv = document.getElementById('parentActivityDiv');
        const parentSelect = document.getElementById('parentActivity');
        const recordTypeName = this.options[this.selectedIndex].text;

        // Show parent activity for sub and sub-sub activities
        if (recordTypeName.includes('SUB') || recordTypeName.toLowerCase().includes('sub')) {
            parentDiv.classList.remove('hidden');

            // Clear existing options
            parentSelect.innerHTML = '<option value="">Select Parent Activity</option>';

            // Add PPAs with main and sub activity record types
            const ppas = @json($ppas);
            ppas.forEach(function (ppa) {
                if (ppa.record_type) {
                    const recordTypeName = ppa.record_type.name.toUpperCase();
                    // Add main activities and sub activities as parent options
                    if (recordTypeName.includes('MAIN') || recordTypeName.includes('SUB') && !recordTypeName.includes('SUB-SUB')) {
                        const option = document.createElement('option');
                        option.value = ppa.id;
                        option.textContent = ppa.name + ' (' + ppa.record_type.name + ')';
                        parentSelect.appendChild(option);
                    }
                }
            });
        } else {
            parentDiv.classList.add('hidden');
        }
    });

    // Add event listeners for button text updates
    document.addEventListener('DOMContentLoaded', function () {
        // Store original PPA data once when page loads
        const ppaSelect = document.getElementById('ppaSelect');
        const originalPpas = [];
        
        // Store all PPAs from the original HTML (excluding empty and "new" options)
        for (let i = 1; i < ppaSelect.options.length - 1; i++) {
            originalPpas.push({
                value: ppaSelect.options[i].value,
                text: ppaSelect.options[i].text,
                recordType: ppaSelect.options[i].getAttribute('data-record-type')
            });
        }
        
        // Store original PPAs in a global variable
        window.originalPpas = originalPpas;
        
        // Update button text when indicator changes
        const indicatorTextarea = document.querySelector('textarea[name="indicator"]');
        if (indicatorTextarea) {
            indicatorTextarea.addEventListener('input', updateButtonText);
        }

        // Update button text when offices are checked/unchecked
        document.addEventListener('change', function (e) {
            if (e.target.type === 'checkbox' && e.target.name === 'offices[]') {
                updateButtonText();
            }
        });

        // Handle Record Type dropdown change
        document.getElementById('recordType').addEventListener('change', function (e) {
            const selectedRecordType = e.target.value;
            const ppaSection = document.getElementById('ppaSection');
            const ppaSelect = document.getElementById('ppaSelect');
            
            // Show/hide PPA section based on record type selection
            if (selectedRecordType) {
                ppaSection.classList.remove('hidden');
            } else {
                ppaSection.classList.add('hidden');
                return; // Exit early if no record type selected
            }
            
            // Clear current options (keep first empty option)
            ppaSelect.innerHTML = '<option value="">Select PPA</option>';
            
            // Add filtered PPAs based on selected record type using original data
            window.originalPpas.forEach(ppa => {
                if (!selectedRecordType || ppa.recordType === selectedRecordType) {
                    const option = document.createElement('option');
                    option.value = ppa.value;
                    option.text = ppa.text;
                    option.setAttribute('data-record-type', ppa.recordType);
                    ppaSelect.appendChild(option);
                }
            });
            
            // Add the "new" option back
            const newOption = document.createElement('option');
            newOption.value = 'new';
            newOption.text = '+ Register New PPA';
            ppaSelect.appendChild(newOption);
        });

        // Handle PPA dropdown change
        document.getElementById('ppaSelect').addEventListener('change', function (e) {
            const newPpaDiv = document.getElementById('newPpaDiv');
            const newPpaInput = document.getElementById('newPpaInput');
            
            if (e.target.value === 'new') {
                newPpaDiv.classList.remove('hidden');
                newPpaInput.focus();
            } else {
                newPpaDiv.classList.add('hidden');
                newPpaInput.value = '';
            }
        });

        // Initialize button text
        updateButtonText();
    });

    // Form submission
    function submitForm() {
        const form = document.getElementById('stoForm');
        const formData = new FormData(form);
        
        // Handle new PPA input
        const ppaSelect = document.getElementById('ppaSelect');
        const newPpaInput = document.getElementById('newPpaInput');
        
        if (ppaSelect.value === 'new' && newPpaInput.value.trim()) {
            // Replace 'new' with the actual inputted PPA name
            formData.set('ppa', newPpaInput.value.trim());
            formData.delete('new_ppa'); // Remove the new_ppa field
        }
        
        // Collect office data from Step 2 if visible, or from Step 1 if submitting directly
        const isStep2Visible = !document.getElementById('step2').classList.contains('hidden');
        console.log('Step 2 visible:', isStep2Visible);
        
        // Check if we have offices selected and should collect data
        const checkedOffices = document.querySelectorAll('input[name="offices[]"]:checked');
        const hasOffices = checkedOffices.length > 0;
        
        if ((isStep2Visible && window.step2Data) || (!isStep2Visible && hasOffices)) {
            console.log('Collecting office data for submission');
            
            // If Step 2 is not visible but we have offices, generate table first to collect data
            if (!isStep2Visible && hasOffices) {
                generateOfficeValueFields();
                
                // Wait a moment for table generation, then collect data
                setTimeout(() => {
                    collectAndSubmitOfficeData(formData, checkedOffices);
                }, 100);
                return; // Don't proceed with submission yet
            } else {
                collectAndSubmitOfficeData(formData, checkedOffices);
            }
        } else {
            console.log('No offices selected or no data to collect, skipping office data collection');
        }
        
        // Convert FormData to URL-encoded string for AJAX submission
        const data = new URLSearchParams(formData);
        
        // Send AJAX request
        fetch('/sto', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: data
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert(result.message);
                closeModal();
                // Optionally refresh the page to show the new record
                location.reload();
            } else {
                alert('Error: ' + result.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while saving the record.');
        });
    }
    
    function collectAndSubmitOfficeData(formData, checkedOffices) {
        console.log('Using saved Step 2 data:', window.step2Data);
        
        // Convert saved data back to office data format
        const officeData = [];
        const offices = @json($offices);
        
        checkedOffices.forEach(checkbox => {
            const officeId = checkbox.value;
            const office = offices.find(o => o.id == officeId);
            
            if (office) {
                const officeName = office.name;
                const universe = window.step2Data?.universe[officeName] || '';
                const remarks = window.step2Data?.remarks[officeName] || '';
                
                const accomplishments = {};
                const years = [2022, 2023, 2024, 2025, 2026, 2027, 2028];
                
                years.forEach(year => {
                    const value = window.step2Data?.accomplishments[officeName]?.[year] || '';
                    accomplishments[`accomplishment_${year}`] = value;
                });
                
                officeData.push({
                    office_id: officeId,
                    universe: universe,
                    remarks: remarks,
                    ...accomplishments
                });
            }
        });
        
        // Add office data as JSON
        formData.set('office_data', JSON.stringify(officeData));
        
        // Debug: log the office data
        console.log('Office data being sent:', officeData);
        
        // Convert FormData to URL-encoded string for AJAX submission
        const data = new URLSearchParams(formData);
        
        // Send AJAX request
        fetch('/sto', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: data
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert(result.message);
                closeModal();
                // Optionally refresh the page to show the new record
                location.reload();
            } else {
                alert('Error: ' + result.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while saving the record.');
        });
    }

    document.getElementById('stoForm').addEventListener('submit', function (e) {
        e.preventDefault();
        
        // Validate Step 2 if it's visible (meaning offices were selected)
        if (!document.getElementById('step2').classList.contains('hidden')) {
            if (!validateStep2()) {
                return; // Stop submission if validation fails
            }
        }
        
        submitForm();
    });

    // Check All functionality for offices
    document.getElementById('selectAllOffices').addEventListener('change', function (e) {
        const officeCheckboxes = document.querySelectorAll('.office-checkbox');
        const isChecked = e.target.checked;
        
        officeCheckboxes.forEach(checkbox => {
            checkbox.checked = isChecked;
        });
        
        // Trigger the button text update to reflect selection state
        updateButtonText();
    });

    // Hide/Show Offices section based on indicator field
    const indicatorTextarea = document.querySelector('textarea[name="indicator"]');
    const officesSection = document.getElementById('officesSection');
    
    indicatorTextarea.addEventListener('input', function () {
        if (this.value.trim() === '') {
            officesSection.classList.add('hidden');
        } else {
            officesSection.classList.remove('hidden');
        }
    });
    
    // Initial check on page load
    if (indicatorTextarea.value.trim() === '') {
        officesSection.classList.add('hidden');
    }

    // Edit Modal Functions
    function openEditModal(stoId) {
        console.log('Opening edit modal for STO ID:', stoId);
        
        // Fetch STO data
        fetch(`/sto/${stoId}`)
            .then(response => response.json())
            .then(sto => {
                console.log('Populating edit form with STO data:', sto);
                populateEditForm(sto);
                
                // Show the edit modal
                document.getElementById('stoEditModal').classList.remove('hidden');
            })
            .catch(error => {
                console.error('Error loading STO record:', error);
                alert('Error loading STO record: ' + error.message);
            });
    }

    function editSto(stoId) {
        openEditModal(stoId);
    }

    // Delete Modal Functions
    let deleteStoId = null;

    function deleteSto(stoId) {
        deleteStoId = stoId;
        document.getElementById('deleteStoId').textContent = stoId;
        document.getElementById('stoDeleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('stoDeleteModal').classList.add('hidden');
        deleteStoId = null;
    }

    function confirmDelete() {
        if (!deleteStoId) return;
        
        console.log('Attempting to delete STO ID:', deleteStoId); // Debug log
        
        if (confirm('Are you absolutely sure you want to delete this STO record? This action cannot be undone.')) {
            // Send delete request
            fetch(`/sto/${deleteStoId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                console.log('Delete response status:', response.status); // Debug log
                console.log('Delete response headers:', response.headers); // Debug log
                return response.json();
            })
            .then(result => {
                if (result.success) {
                    alert(result.message);
                    closeDeleteModal();
                    // Reload page to show updated list
                    window.location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while deleting the record.');
            });
        }
    }
</script>