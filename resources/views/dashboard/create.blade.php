@extends('layouts.app')

@php
    $pageTitle = 'Create PPA Entry';
@endphp

@section('content')
    <!-- Simple Page Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Create PPA Entry</h1>
            <p class="text-gray-600 mt-1">Create a new PPA entry/entries for the universe baseline</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6">
            <form id="ppaForm" class="space-y-6">
                @csrf

                <!-- Entry Type Field -->
                <div>
                    <label for="entry_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Entry <span class="text-red-500">*</span>
                    </label>
                    <select id="entry_type" name="entry_type" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                        <option value="">Select entry type</option>
                        <option value="GASS">General Administration and Support Services (GASS)</option>
                        <option value="STO">Special Technical Operations (STO)</option>
                        <option value="ENF">Environment and Natural Resources (ENF)</option>
                        <option value="Biodiversity">Biodiversity</option>
                        <option value="Lands">Lands</option>
                        <option value="NRA">National Resources Authority (NRA)</option>
                    </select>
                    @error('entry_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Dynamic Entry Forms Container -->
                <div id="dynamic_entries_container" class="space-y-6 hidden">
                    <!-- Entry forms will be generated here dynamically -->
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('gass.index') }}"
                        class="px-6 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors flex items-center">
                        <i class="fas fa-save mr-2"></i>
                        Save PPA
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div
            class="fixed top-4 right-4 bg-green-50 border border-green-200 text-green-800 px-6 py-4 rounded-lg shadow-lg z-50 flex items-center">
            <i class="fas fa-check-circle mr-3"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div
            class="fixed top-4 right-4 bg-red-50 border border-red-200 text-red-800 px-6 py-4 rounded-lg shadow-lg z-50 flex items-center">
            <i class="fas fa-exclamation-circle mr-3"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- JavaScript for Form Handling -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('ppaForm');
            const entryTypeSelect = document.getElementById('entry_type');
            const dynamicContainer = document.getElementById('dynamic_entries_container');

            // Make updateProjectOptions globally accessible
            window.updateProjectOptions = function(programValue, entryType) {
                console.log('updateProjectOptions called with:', programValue, entryType);
                
                // Wait a moment for the DOM to be ready
                setTimeout(() => {
                    const projectSelect = document.querySelector('select[name="entries[2]"]');
                    console.log('Project select found:', projectSelect);
                    
                    if (entryType === 'GASS' && programValue === 'General Management and Supervision') {
                        console.log('Updating project options for GASS - General Management and Supervision');
                        
                        // Clear existing options
                        projectSelect.innerHTML = '<option value="">Select project</option>';
                        
                        // Add specific project options for General Management and Supervision
                        const projectOptions = [
                            'CONSTRUCTION, REPAIR AND MAINTENANCE AND INSURANCE OF PROPERTY INCLUDING MOTOR VEHICLES',
                            'ADMINISTRATIVE SERVICES',
                            'SERVICE CONTRACTS',
                            'FINANCE SERVICES',
                            'AUDITING SERVICES',
                            'TOP LEVEL MANAGEMENT/PROJECT MANAGEMENT SUPERVISION',
                            'MANDATORIES AND IMPOSITIONS',
                            'PS Requirements'
                        ];
                        
                        projectOptions.forEach(option => {
                            const optionElement = document.createElement('option');
                            optionElement.value = option;
                            optionElement.textContent = option;
                            projectSelect.appendChild(optionElement);
                        });
                        
                        console.log('Project options updated successfully');
                    } else {
                        console.log('Resetting to default project options');
                        // Reset to default options
                        projectSelect.innerHTML = `
                            <option value="">Select project</option>
                            <option value="Project 1">Project 1</option>
                            <option value="Project 2">Project 2</option>
                            <option value="Project 3">Project 3</option>
                        `;
                    }
                }, 100);
            };

            // Make updateActivityOptions globally accessible
            window.updateActivityOptions = function(projectValue, entryType) {
                console.log('updateActivityOptions called with:', projectValue, entryType);
                
                // Store the current selection for use in generateActivityFields
                window.currentProjectSelection = projectValue;
                window.currentEntryType = entryType;
            };

            // Make updateActivity2Options globally accessible
            window.updateActivity2Options = function(activity1Value) {
                console.log('updateActivity2Options called with:', activity1Value);
                
                // Store the Activity 1 selection
                window.currentActivity1Selection = activity1Value;
                
                // Update Activity 2 options if it exists
                const activity2Select = document.getElementById('activity_2');
                if (activity2Select && window.currentEntryType === 'GASS' && 
                    window.currentProjectSelection === 'CONSTRUCTION, REPAIR AND MAINTENANCE AND INSURANCE OF PROPERTY INCLUDING MOTOR VEHICLES') {
                    
                    if (activity1Value === 'Repair, Maintenance, Insurance of DENR Properties') {
                        // Special options for Activity 2 when Repair, Maintenance is selected
                        activity2Select.innerHTML = `
                            <option value="">Select activity 2</option>
                            <option value="Repair and Maintenance of Buildings and Other Structures Below PhP 50,000.00">Repair and Maintenance of Buildings and Other Structures Below PhP 50,000.00</option>
                            <option value="Procurement of Motor Vehicles">Procurement of Motor Vehicles</option>
                            <option value="Hiring of Civil Engineer @ P 33,000/month">Hiring of Civil Engineer @ P 33,000/month</option>
                            <option value="Repair and Maintenance of Motor Vehicles">Repair and Maintenance of Motor Vehicles</option>
                            <option value="Submission of Updated "Annex D: Inventory and Refleeting Program for the Budget Proposal" (Annual-1st Qtr)">Submission of Updated "Annex D: Inventory and Refleeting Program for the Budget Proposal" (Annual-1st Qtr)</option>
                            <option value="Property Plant and Equipment (PPE) Accountability Reports">Property Plant and Equipment (PPE) Accountability Reports</option>
                            <option value="Insurance of Property">Insurance of Property</option>
                            <option value="Report on the Physical Count of Inventories and Semi-Expendable Properties">Report on the Physical Count of Inventories and Semi-Expendable Properties</option>
                        `;
                    } else if (activity1Value === 'Construction of Buildings and Other Structures') {
                        // Default construction options for Activity 2
                        activity2Select.innerHTML = `
                            <option value="">Select activity 2</option>
                            <option value="Construction of Buildings">Construction of Buildings</option>
                            <option value="Construction of Other Structures">Construction of Other Structures</option>
                            <option value="Repair/Completion/Expansion of office building">Repair/Completion/Expansion of office building</option>
                        `;
                    } else {
                        // Default options for other Activity 1 selections
                        activity2Select.innerHTML = `
                            <option value="">Select activity 2</option>
                            <option value="Activity 2 - Option 1">Activity 2 - Option 1</option>
                            <option value="Activity 2 - Option 2">Activity 2 - Option 2</option>
                            <option value="Activity 2 - Option 3">Activity 2 - Option 3</option>
                        `;
                    }
                } else if (activity2Select && window.currentEntryType === 'GASS' && 
                           window.currentProjectSelection === 'ADMINISTRATIVE SERVICES') {
                    
                    if (activity1Value === 'Procurement Reports') {
                        // Special options for Activity 2 when Procurement Reports is selected
                        activity2Select.innerHTML = `
                            <option value="">Select activity 2</option>
                            <option value="Submission of Updated Annual Procurement Plan non-Common Use Supplies and Equipment (APP non-CSE) based on approved GAA FY 2026 to the GPPB-TSO">Submission of Updated Annual Procurement Plan non-Common Use Supplies and Equipment (APP non-CSE) based on approved GAA FY 2026 to the GPPB-TSO</option>
                            <option value="Posting of Indicative Annual Procurement Plan non-Common Use Supplies and Equipment (APP non-CSE) for FY 2027 in the Transparency Seal">Posting of Indicative Annual Procurement Plan non-Common Use Supplies and Equipment (APP non-CSE) for FY 2027 in the Transparency Seal</option>
                            <option value="Submission of Certificate of Undertaking or Non Undertaking of Early Procurement Activities (EPA)">Submission of Certificate of Undertaking or Non Undertaking of Early Procurement Activities (EPA)</option>
                        `;
                    } else if (activity1Value === 'Procurement Activities for the Succeeding Year') {
                        // Special options for Activity 2 when Procurement Activities for the Succeeding Year is selected
                        activity2Select.innerHTML = `
                            <option value="">Select activity 2</option>
                            <option value="Uploading of FY 2027 Annual Procurement Plan - Common Use Supplies and Equipment (APP-CSE) to the PS-PHILGEPS virtual store">Uploading of FY 2027 Annual Procurement Plan - Common Use Supplies and Equipment (APP-CSE) to the PS-PHILGEPS virtual store</option>
                            <option value="Conduct of Early Procurement Activity (EPA)">Conduct of Early Procurement Activity (EPA)</option>
                            <option value="Compliance to PHILGEPS Conditions">Compliance to PHILGEPS Conditions</option>
                        `;
                    } else if (activity1Value === 'Human Resource Development Service -Personnel Division Statement of Assets and Liabilities Networth (SALN)') {
                        // Special options for Activity 2 when HR Development Service SALN is selected
                        activity2Select.innerHTML = `
                            <option value="">Select activity 2</option>
                            <option value="Appointments">Appointments</option>
                            <option value="Maintenance and Updating of Information System">Maintenance and Updating of Information System</option>
                            <option value="HR Statistical Profile">HR Statistical Profile</option>
                            <option value="Resolution of Complaints lodged at the Contact Center ng Bayan">Resolution of Complaints lodged at the Contact Center ng Bayan</option>
                        `;
                    } else if (activity1Value === 'Records Management') {
                        // Special options for Activity 2 when Records Management is selected
                        activity2Select.innerHTML = `
                            <option value="">Select activity 2</option>
                            <option value="Conduct disposal of inventoried valueless records (Annual)">Conduct disposal of inventoried valueless records (Annual)</option>
                            <option value="Submission of FOI report to the Presidential Communications Office (PCO)">Submission of FOI report to the Presidential Communications Office (PCO)</option>
                        `;
                    } else {
                        // Default options for other Activity 1 selections in Administrative Services
                        activity2Select.innerHTML = `
                            <option value="">Select activity 2</option>
                            <option value="Activity 2 - Option 1">Activity 2 - Option 1</option>
                            <option value="Activity 2 - Option 2">Activity 2 - Option 2</option>
                            <option value="Activity 2 - Option 3">Activity 2 - Option 3</option>
                        `;
                    }
                } else if (activity2Select && window.currentEntryType === 'GASS' && 
                           window.currentProjectSelection === 'FINANCE SERVICES') {
                    
                    if (activity1Value === 'Budget Execution') {
                        // Special options for Activity 2 when Budget Execution is selected
                        activity2Select.innerHTML = `
                            <option value="">Select activity 2</option>
                            <option value="Preparation of FY 2027 BED forms">Preparation of FY 2027 BED forms</option>
                            <option value="Evaluation/processing/encoding of Obligation Requests and Status (ORS) / Budget Utilization Request (BUR) on the ORS System">Evaluation/processing/encoding of Obligation Requests and Status (ORS) / Budget Utilization Request (BUR) on the ORS System</option>
                        `;
                    } else if (activity1Value === 'Budget Accountability') {
                        // Special options for Activity 2 when Budget Accountability is selected
                        activity2Select.innerHTML = `
                            <option value="">Select activity 2</option>
                            <option value="Preparation of Budget Accountability Reports (BFARs)">Preparation of Budget Accountability Reports (BFARs)</option>
                            <option value="Maintain/Preparation of RAPAL/RAOD/RBUD">Maintain/Preparation of RAPAL/RAOD/RBUD</option>
                        `;
                    } else if (activity1Value === 'Accounting') {
                        // Special options for Activity 2 when Accounting is selected
                        activity2Select.innerHTML = `
                            <option value="">Select activity 2</option>
                            <option value="Disbursement">Disbursement</option>
                            <option value="Remittance">Remittance</option>
                            <option value="Bookkeeping">Bookkeeping</option>
                            <option value="Compliance">Compliance</option>
                        `;
                    } else {
                        // Default options for other Activity 1 selections in Finance Services
                        activity2Select.innerHTML = `
                            <option value="">Select activity 2</option>
                            <option value="Activity 2 - Option 1">Activity 2 - Option 1</option>
                            <option value="Activity 2 - Option 2">Activity 2 - Option 2</option>
                            <option value="Activity 2 - Option 3">Activity 2 - Option 3</option>
                        `;
                    }
                } else if (activity2Select && window.currentEntryType === 'GASS' && 
                           window.currentProjectSelection === 'TOP LEVEL MANAGEMENT/PROJECT MANAGEMENT SUPERVISION') {
                    
                    if (activity1Value === 'Implementation of QMS 100% of QMS non-comformities/observations with corrective actions taken and closed by the Auditor') {
                        // Special options for Activity 2 when QMS Implementation is selected
                        activity2Select.innerHTML = `
                            <option value="">Select activity 2</option>
                            <option value="Conduct of Internal Audit on QMS">Conduct of Internal Audit on QMS</option>
                            <option value="Support to the implementation of occupational safety and Health Law">Support to the implementation of occupational safety and Health Law</option>
                        `;
                    } else {
                        // Default options for other Activity 1 selections in Top Level Management
                        activity2Select.innerHTML = `
                            <option value="">Select activity 2</option>
                            <option value="Activity 2 - Option 1">Activity 2 - Option 1</option>
                            <option value="Activity 2 - Option 2">Activity 2 - Option 2</option>
                            <option value="Activity 2 - Option 3">Activity 2 - Option 3</option>
                        `;
                    }
                }
            };

            // Make updateActivity3Options globally accessible
            window.updateActivity3Options = function(activity2Value) {
                console.log('updateActivity3Options called with:', activity2Value);
                
                // Store the Activity 2 selection
                window.currentActivity2Selection = activity2Value;
                
                // Update Activity 3 options if it exists
                const activity3Select = document.getElementById('activity_3');
                if (activity3Select && 
                    window.currentEntryType === 'GASS' && 
                    window.currentProjectSelection === 'CONSTRUCTION, REPAIR AND MAINTENANCE AND INSURANCE OF PROPERTY INCLUDING MOTOR VEHICLES' &&
                    window.currentActivity1Selection === 'Repair, Maintenance, Insurance of DENR Properties') {
                    
                    if (activity2Value === 'Repair and Maintenance of Motor Vehicles') {
                        // Special options for Activity 3 when Repair and Maintenance of Motor Vehicles is selected
                        activity3Select.innerHTML = `
                            <option value="">Select activity 3</option>
                            <option value="Repair of Motor Vehicles (including change of tires, change oil, replacement of brake pads, and other repairs)">Repair of Motor Vehicles (including change of tires, change oil, replacement of brake pads, and other repairs)</option>
                            <option value="Maintenance of Motor Vehicles">Maintenance of Motor Vehicles</option>
                        `;
                    } else if (activity2Value === 'Property Plant and Equipment (PPE) Accountability Reports') {
                        // Special options for Activity 3 when PPE Accountability Reports is selected
                        activity3Select.innerHTML = `
                            <option value="">Select activity 3</option>
                            <option value="Conduct of Physical Count of Property Plant and Equipment (RPCPPE) as of Dec 31, 2026 (previous year) (Php5,000-Php20,000/office)">Conduct of Physical Count of Property Plant and Equipment (RPCPPE) as of Dec 31, 2026 (previous year) (Php5,000-Php20,000/office)</option>
                        `;
                    } else if (activity2Value === 'Insurance of Property') {
                        // Special options for Activity 3 when Insurance of Property is selected
                        activity3Select.innerHTML = `
                            <option value="">Select activity 3</option>
                            <option value="Insurance of Property (Buildings and its Content) including Other structures and Watercrafts">Insurance of Property (Buildings and its Content) including Other structures and Watercrafts</option>
                            <option value="Insurance of Motor Vehicles">Insurance of Motor Vehicles</option>
                        `;
                    } else if (activity2Value === 'Report on the Physical Count of Inventories and Semi-Expendable Properties') {
                        // Special options for Activity 3 when Report on Physical Count is selected
                        activity3Select.innerHTML = `
                            <option value="">Select activity 3</option>
                            <option value="Conduct of Physical Count of Inventories (RPCI) as of 31 Dec. 2026 (previous year) and 30 June 2027 (current year) (Php5,000 to Php20,000/per office)">Conduct of Physical Count of Inventories (RPCI) as of 31 Dec. 2026 (previous year) and 30 June 2027 (current year) (Php5,000 to Php20,000/per office)</option>
                            <option value="Conduct of Physical Count of Semi-Expendable Property (RPCSP) as of 31 Dec. 2026 (previous year) and as of 30 June 2027 (current year) (Php5,000 to Php20,000/per office)">Conduct of Physical Count of Semi-Expendable Property (RPCSP) as of 31 Dec. 2026 (previous year) and as of 30 June 2027 (current year) (Php5,000 to Php20,000/per office)</option>
                        `;
                    } else {
                        // Default options for other Activity 2 selections
                        activity3Select.innerHTML = `
                            <option value="">Select activity 3</option>
                            <option value="Activity 3 - Option 1">Activity 3 - Option 1</option>
                            <option value="Activity 3 - Option 2">Activity 3 - Option 2</option>
                            <option value="Activity 3 - Option 3">Activity 3 - Option 3</option>
                        `;
                    }
                } else if (activity3Select && 
                           window.currentEntryType === 'GASS' && 
                           window.currentProjectSelection === 'FINANCE SERVICES' &&
                           window.currentActivity1Selection === 'Accounting') {
                    
                    if (activity2Value === 'Disbursement') {
                        // Special options for Activity 3 when Disbursement is selected
                        activity3Select.innerHTML = `
                            <option value="">Select activity 3</option>
                            <option value="Processing of vouchers, payrolls, NTAs and others">Processing of vouchers, payrolls, NTAs and others</option>
                            <option value="Preparation of Advice to Debit Account (ADA)">Preparation of Advice to Debit Account (ADA)</option>
                        `;
                    } else if (activity2Value === 'Remittance') {
                        // Special options for Activity 3 when Remittance is selected
                        activity3Select.innerHTML = `
                            <option value="">Select activity 3</option>
                            <option value="Preparation of remittances to various offices/unit">Preparation of remittances to various offices/unit</option>
                            <option value="Preparation of year-end reports for submission to BIR">Preparation of year-end reports for submission to BIR</option>
                        `;
                    } else if (activity2Value === 'Bookkeeping') {
                        // Special options for Activity 3 when Bookkeeping is selected
                        activity3Select.innerHTML = `
                            <option value="">Select activity 3</option>
                            <option value="Preparation of Journal Entry Vouchers for all Funds to record the financial transactions">Preparation of Journal Entry Vouchers for all Funds to record the financial transactions</option>
                            <option value="Preparation of Financial Reports/ Statements for all Funds (Fund Cluster 01, 02, 04, 05, 06, 07 and Projects)">Preparation of Financial Reports/ Statements for all Funds (Fund Cluster 01, 02, 04, 05, 06, 07 and Projects)</option>
                            <option value="Preparation and submission of Budget and Financial Accountability Reports (BFARs) based on DBM-COA Joint Circular No. 2019-1 dated January 1, 2019 or the Updated Guidelines Relative to Budget and Financial Accountability Reports Starting FY2019">Preparation and submission of Budget and Financial Accountability Reports (BFARs) based on DBM-COA Joint Circular No. 2019-1 dated January 1, 2019 or the Updated Guidelines Relative to Budget and Financial Accountability Reports Starting FY2019</option>
                        `;
                    } else if (activity2Value === 'Compliance') {
                        // Special options for Activity 3 when Compliance is selected
                        activity3Select.innerHTML = `
                            <option value="">Select activity 3</option>
                            <option value="Preparation of Agency Action Plan and Status of Implementation (AAPSI) for Management Letter">Preparation of Agency Action Plan and Status of Implementation (AAPSI) for Management Letter</option>
                            <option value="Preparation of Agency Action Plan and Status of Implementation (AAPSI) for Consolidated Annual Audit Report">Preparation of Agency Action Plan and Status of Implementation (AAPSI) for Consolidated Annual Audit Report</option>
                        `;
                    } else {
                        // Default options for other Activity 2 selections in Finance Services Accounting
                        activity3Select.innerHTML = `
                            <option value="">Select activity 3</option>
                            <option value="Activity 3 - Option 1">Activity 3 - Option 1</option>
                            <option value="Activity 3 - Option 2">Activity 3 - Option 2</option>
                            <option value="Activity 3 - Option 3">Activity 3 - Option 3</option>
                        `;
                    }
                }
            };

            // Make updateActivityCount globally accessible
            window.updateActivityCount = function(programValue, entryType) {
                console.log('updateActivityCount called with:', programValue, entryType);
                // This function can be used to trigger activity-related updates when program changes
                // For now, it's a placeholder for future functionality
            };

            // Make generateActivityFields globally accessible
            window.generateActivityFields = function(count) {
                console.log('generateActivityFields called with:', count);
                const container = document.getElementById('activity_fields_container');
                
                if (!container) {
                    console.log('Activity container not found');
                    return;
                }
                
                // Clear existing activity fields
                container.innerHTML = '';
                
                if (count === '0') {
                    container.innerHTML = '<p class="text-gray-500 text-sm">No activities selected</p>';
                    return;
                }
                
                let html = '';
                for (let i = 1; i <= parseInt(count); i++) {
                    // Check if we should use specific activity options for construction project
                    let activityOptions = '';
                    if (window.currentEntryType === 'GASS' && 
                        window.currentProjectSelection === 'CONSTRUCTION, REPAIR AND MAINTENANCE AND INSURANCE OF PROPERTY INCLUDING MOTOR VEHICLES' && 
                        i === 1) {
                        // Special options for Activity 1 when construction project is selected
                        activityOptions = `
                            <option value="">Select activity ${i}</option>
                            <option value="Construction of Buildings and Other Structures">Construction of Buildings and Other Structures</option>
                            <option value="Repair, Maintenance, Insurance of DENR Properties">Repair, Maintenance, Insurance of DENR Properties</option>
                        `;
                    } else if (window.currentEntryType === 'GASS' && 
                               window.currentProjectSelection === 'ADMINISTRATIVE SERVICES' && 
                               i === 1) {
                        // Special options for Activity 1 when Administrative Services is selected
                        activityOptions = `
                            <option value="">Select activity ${i}</option>
                            <option value="Procurement Reports">Procurement Reports</option>
                            <option value="Procurement Activities for the Succeeding Year">Procurement Activities for the Succeeding Year</option>
                            <option value="Human Resource Development Service -Personnel Division Statement of Assets and Liabilities Networth (SALN)">Human Resource Development Service -Personnel Division Statement of Assets and Liabilities Networth (SALN)</option>
                            <option value="Cashier">Cashier</option>
                            <option value="Records Management">Records Management</option>
                        `;
                    } else if (window.currentEntryType === 'GASS' && 
                               window.currentProjectSelection === 'FINANCE SERVICES' && 
                               i === 1) {
                        // Special options for Activity 1 when Finance Services is selected
                        activityOptions = `
                            <option value="">Select activity ${i}</option>
                            <option value="FY 2027 Budget Proposal">FY 2027 Budget Proposal</option>
                            <option value="Budget Execution">Budget Execution</option>
                            <option value="Budget Accountability">Budget Accountability</option>
                            <option value="Accounting">Accounting</option>
                        `;
                    } else if (window.currentEntryType === 'GASS' && 
                               window.currentProjectSelection === 'TOP LEVEL MANAGEMENT/PROJECT MANAGEMENT SUPERVISION' && 
                               i === 1) {
                        // Special options for Activity 1 when Top Level Management/Project Management Supervision is selected
                        activityOptions = `
                            <option value="">Select activity ${i}</option>
                            <option value="Attendance/participation to meetings/conferences">Attendance/participation to meetings/conferences</option>
                            <option value="Conduct of Regional, PENRO Management Conference/Meetings (RMC, PMC)">Conduct of Regional, PENRO Management Conference/Meetings (RMC, PMC)</option>
                            <option value="Implementation of QMS 100% of QMS non-comformities/observations with corrective actions taken and closed by the Auditor">Implementation of QMS 100% of QMS non-comformities/observations with corrective actions taken and closed by the Auditor</option>
                        `;
                    } else if (window.currentEntryType === 'GASS' && 
                               window.currentProjectSelection === 'CONSTRUCTION, REPAIR AND MAINTENANCE AND INSURANCE OF PROPERTY INCLUDING MOTOR VEHICLES' && 
                               i === 2) {
                        // Special options for Activity 2 when construction project is selected
                        activityOptions = `
                            <option value="">Select activity ${i}</option>
                            <option value="Construction of Buildings">Construction of Buildings</option>
                            <option value="Construction of Other Structures">Construction of Other Structures</option>
                            <option value="Repair/Completion/Expansion of office building">Repair/Completion/Expansion of office building</option>
                        `;
                    } else {
                        // Default options for other activities
                        activityOptions = `
                            <option value="">Select activity ${i}</option>
                            <option value="Activity ${i} - Option 1">Activity ${i} - Option 1</option>
                            <option value="Activity ${i} - Option 2">Activity ${i} - Option 2</option>
                            <option value="Activity ${i} - Option 3">Activity ${i} - Option 3</option>
                        `;
                    }
                    
                    html += `
                        <div class="mb-4">
                            <label for="activity_${i}" class="block text-sm font-medium text-gray-700 mb-2">
                                Activity ${i} <span class="text-red-500">*</span>
                            </label>
                            <select id="activity_${i}" name="activities[${i}]" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                                ${i === 1 ? 'onchange="updateActivity2Options(this.value)"' : ''}
                                ${i === 2 ? 'onchange="updateActivity3Options(this.value)"' : ''}>
                                ${activityOptions}
                            </select>
                        </div>`;
                }
                
                container.innerHTML = html;
                console.log(`Generated ${count} activity fields`);
            };

            // Generate entry forms when entry type changes
            entryTypeSelect.addEventListener('change', function () {
                const entryType = this.value;
                
                if (entryType) {
                    // Always generate 5 entries (full set)
                    generateEntryForms(5, entryType);
                    dynamicContainer.classList.remove('hidden');
                } else {
                    dynamicContainer.classList.add('hidden');
                    dynamicContainer.innerHTML = '';
                }
            });

            function generateEntryForms(numEntries, entryType) {
                let html = '<div class="space-y-6">';
                
                html += `
                    <div class="border border-gray-200 rounded-lg p-6 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Entry ${entryType}</h3>
                `;
                
                const entryNames = ['Program', 'Project', 'Activity a1', 'Activity a2', 'Activity a3'];
                
                for (let i = 1; i <= numEntries; i++) {
                    const entryName = entryNames[i - 1] || `Entry ${i}`;
                    
                    if (entryName === 'Program' && entryType === 'GASS') {
                        // Special dropdown for GASS Program
                        html += `
                            <div class="mb-4">
                                <label for="entry_${i}" class="block text-sm font-medium text-gray-700 mb-2">
                                    ${entryName} <span class="text-red-500">*</span>
                                </label>
                                <select id="entry_${i}" name="entries[${i}]" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                                    onchange="updateProjectOptions(this.value, '${entryType}')">
                                    <option value="">Select ${entryName.toLowerCase()}</option>
                                    <option value="General Management and Supervision">General Management and Supervision</option>
                                    <option value="Human Resource Development">Human Resource Development</option>
                                    <option value="Administration of Personnel Benefits">Administration of Personnel Benefits</option>
                                </select>
                            </div>`;
                    } else if (entryName === 'Program') {
                        // Regular dropdown for other entry types' Program
                        html += `
                            <div class="mb-4">
                                <label for="entry_${i}" class="block text-sm font-medium text-gray-700 mb-2">
                                    ${entryName} <span class="text-red-500">*</span>
                                </label>
                                <select id="entry_${i}" name="entries[${i}]" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                                    onchange="updateActivityCount(this.value, '${entryType}')">
                                    <option value="">Select ${entryName.toLowerCase()}</option>
                                    <option value="Program 1">Program 1</option>
                                    <option value="Program 2">Program 2</option>
                                    <option value="Program 3">Program 3</option>
                                </select>
                            </div>`;
                    } else if (entryName === 'Project') {
                        // Dropdown for Project with dynamic options
                        html += `
                            <div class="mb-4">
                                <label for="entry_${i}" class="block text-sm font-medium text-gray-700 mb-2">
                                    ${entryName} <span class="text-red-500">*</span>
                                </label>
                                <select id="entry_${i}" name="entries[${i}]" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                                    onchange="updateActivityOptions(this.value, '${entryType}')">
                                    <option value="">Select ${entryName.toLowerCase()}</option>
                                    <option value="Project 1">Project 1</option>
                                    <option value="Project 2">Project 2</option>
                                    <option value="Project 3">Project 3</option>
                                </select>
                            </div>`;
                    } else if (entryName === 'Activity a1') {
                        // Activity count selector
                        html += `
                            <div class="mb-4">
                                <label for="activity_count" class="block text-sm font-medium text-gray-700 mb-2">
                                    Number of Activities <span class="text-red-500">*</span>
                                </label>
                                <select id="activity_count" name="activity_count" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                                    onchange="generateActivityFields(this.value)">
                                    <option value="">Select number of activities</option>
                                    <option value="0">0 entry</option>
                                    <option value="1">1 entry</option>
                                    <option value="2">2 entries</option>
                                    <option value="3">3 entries</option>
                                </select>
                            </div>
                            <div id="activity_fields_container" class="space-y-4">
                                <!-- Activity fields will be generated here dynamically -->
                            </div>`;
                    } else {
                        // Don't generate Activity a2 and a3 initially - they'll be created dynamically
                        continue;
                    }
                }
                
                html += `
                        <!-- Hidden fields for entry type and number -->
                        <input type="hidden" name="entry_type" value="${entryType}">
                        <input type="hidden" name="num_entries" value="${numEntries}">
                    </div>
                `;
                
                html += '</div>';
                dynamicContainer.innerHTML = html;
            }

            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const submitBtn = form.querySelector('button[type="submit"]');
                const entryType = entryTypeSelect.value.trim();

                // Validate dynamic fields
                const dynamicForms = dynamicContainer.querySelectorAll('select[required]');
                let isValid = true;
                
                dynamicForms.forEach(function(field) {
                    if (!field.value.trim()) {
                        isValid = false;
                        field.classList.add('border-red-500');
                    } else {
                        field.classList.remove('border-red-500');
                    }
                });

                if (!isValid) {
                    alert('Please fill in all required fields for all entries.');
                    return;
                }

                // Show loading state
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
                submitBtn.disabled = true;

                // Simulate form submission (replace with actual AJAX call)
                setTimeout(function () {
                    // Reset button state
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;

                    // Show success message and redirect
                    const successDiv = document.createElement('div');
                    successDiv.className = 'fixed top-4 right-4 bg-green-50 border border-green-200 text-green-800 px-6 py-4 rounded-lg shadow-lg z-50 flex items-center';
                    successDiv.innerHTML = `<i class="fas fa-check-circle mr-3"></i>5 ${entryType} entries created successfully!`;
                    document.body.appendChild(successDiv);

                    setTimeout(function () {
                        window.location.href = '{{ route("dashboard") }}';
                    }, 1500);
                }, 1000);
            });

            // Auto-hide messages after 5 seconds
            setTimeout(function () {
                const messages = document.querySelectorAll('.fixed.top-4.right-4');
                messages.forEach(function (msg) {
                    msg.style.transition = 'opacity 0.3s';
                    msg.style.opacity = '0';
                    setTimeout(function () {
                        msg.remove();
                    }, 300);
                });
            }, 5000);
        });
    </script>
@endsection