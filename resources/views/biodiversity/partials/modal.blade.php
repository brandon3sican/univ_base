<!-- Create/Edit Modal -->
<div id="crudModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border shadow-lg rounded-lg bg-white" style="width: 95vw; max-width: 1600px;">
        <div class="bg-white rounded-lg shadow-xl transform transition-all">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900" id="modalTitle">Add New Record</h3>
                <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="crudForm" class="px-6 py-4 space-y-4">
                @csrf
                <input type="hidden" id="recordId" name="id">
                <input type="hidden" id="type" name="type" value="4">

                <!-- Record Type Selection -->
                <div>
                    <label for="record_type_id" class="block text-sm font-medium text-gray-700 mb-1">Record Type</label>
                    <select id="record_type_id" name="record_type_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        onchange="filterPpaByRecordType(); handleRecordTypeChange()">
                        <option value="">Select Record Type</option>
                    </select>
                </div>

                <!-- PPA Selection -->
                <div>
                    <label for="ppa_id" class="block text-sm font-medium text-gray-700 mb-1">PPA Name</label>
                    <select id="ppa_id" name="ppa_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        onchange="handlePpaSelection()">
                        <option value="">Select PPA</option>
                        @foreach ($ppas as $ppa)
                            @if ($ppa->types_id == 4)
                                <option value="{{ $ppa->id }}">{{ $ppa->name }}</option>
                            @endif
                        @endforeach
                        <option value="new" class="bg-green-100 text-green-800 font-semibold">+ Register New PPA
                        </option>
                    </select>
                    <div id="ppaLoadingMessage" class="mt-1 text-xs text-gray-500 hidden">
                        Loading PPAs for selected record type...
                    </div>
                    <!-- New PPA Text Area (hidden by default) -->
                    <div id="newPpaSection" class="mt-2 hidden">
                        <label for="new_ppa_name" class="block text-sm font-medium text-gray-700 mb-1">New PPA
                            Name</label>
                        <textarea id="new_ppa_name" name="new_ppa_name" rows="2"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Enter new PPA name"></textarea>
                    </div>
                </div>

                <!-- Parent Activity Selection (hidden by default, shown for hierarchical record types) -->
                <div id="parentActivitySection" class="hidden">
                    <label for="parent_activity_id" class="block text-sm font-medium text-gray-700 mb-1">Parent
                        Activity <span id="parentActivityRequired" class="text-red-500 hidden">*</span></label>
                    <div class="flex gap-2 w-full">
                        <select id="parent_activity_id" name="parent_activity_id"
                            class="flex-1 min-w-0 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Parent Activity</option>
                        </select>
                        <button type="button" onclick="refreshParentActivities()"
                            class="flex-shrink-0 px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200 flex items-center"
                            title="Refresh Parent Activities">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                    <div id="parentLoadingMessage" class="mt-1 text-xs text-gray-500 hidden">
                        Loading parent activities...
                    </div>
                </div>

                <!-- Indicator Text Area -->
                <div>
                    <label for="indicator_id" class="block text-sm font-medium text-gray-700 mb-1">Indicator</label>
                    <textarea id="indicator_id" name="indicator" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Enter indicator description (optional)"></textarea>
                </div>

                <!-- Office Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Office</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                        <!-- Select/Unselect All as first checkbox -->
                        <div class="flex items-center">
                            <input type="checkbox" id="selectAllOffices" onchange="toggleAllOffices()"
                                class="mr-2 rounded border-gray-300 text-blue-600 focus:ring-2 focus:ring-blue-500">
                            <label for="selectAllOffices"
                                class="text-sm text-gray-700 cursor-pointer font-semibold">Select/Unselect All</label>
                        </div>
                        @foreach ($offices as $office)
                            <div class="flex items-center">
                                <input type="checkbox" id="office_{{ $office->id }}" name="office_id[]"
                                    value="{{ $office->id }}"
                                    class="mr-2 rounded border-gray-300 text-blue-600 focus:ring-2 focus:ring-blue-500">
                                <label for="office_{{ $office->id }}"
                                    class="text-sm text-gray-700 cursor-pointer">{{ $office->name }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Universe Data (conditional) -->
                <div id="step2Section" class="space-y-4 hidden">
                    <div class="border-t pt-4">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4">Universe Data</h4>

                        <!-- Combined Data Table -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Data Table</label>
                            <div
                                class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-4 mb-4 shadow-sm">
                                <div class="flex items-center justify-between mb-3">
                                    <h6 class="text-sm font-semibold text-blue-800">Legend:</h6>
                                    <button type="button" onclick="refreshStep2()"
                                        class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition-colors">
                                        <i class="fas fa-sync-alt mr-1"></i>Refresh
                                    </button>
                                </div>
                                <div class="grid grid-cols-3 gap-4 text-sm">
                                    <div
                                        class="flex items-center space-x-2 bg-white rounded-lg p-3 shadow-sm border border-gray-200">
                                        <div class="w-4 h-4 bg-blue-500 rounded-full flex items-center justify-center">
                                            <span class="text-white text-xs font-bold">U</span>
                                        </div>
                                        <div>
                                            <div class="text-blue-700 font-semibold">Universe</div>
                                            <div class="text-xs text-gray-500">Single value per office</div>
                                        </div>
                                    </div>
                                    <div
                                        class="flex items-center space-x-2 bg-white rounded-lg p-3 shadow-sm border border-gray-200">
                                        <div
                                            class="w-4 h-4 bg-green-500 rounded-full flex items-center justify-center">
                                            <span class="text-white text-xs font-bold">A</span>
                                        </div>
                                        <div>
                                            <div class="text-green-700 font-semibold">Accomplishment</div>
                                            <div class="text-xs text-gray-500">Year-by-year values</div>
                                        </div>
                                    </div>
                                    <div
                                        class="flex items-center space-x-2 bg-white rounded-lg p-3 shadow-sm border border-gray-200">
                                        <div
                                            class="w-4 h-4 bg-purple-500 rounded-full flex items-center justify-center">
                                            <span class="text-white text-xs font-bold">T</span>
                                        </div>
                                        <div>
                                            <div class="text-purple-700 font-semibold">Targets</div>
                                            <div class="text-xs text-gray-500">Year-by-year values</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="overflow-x-auto border border-gray-200 rounded-lg shadow-sm">
                                <table class="min-w-full">
                                    <thead class="bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
                                        <tr>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                                Office</th>
                                            <th
                                                class="px-3 py-3 text-center text-xs font-semibold text-blue-600 uppercase tracking-wider">
                                                Universe Value</th>
                                            <th
                                                class="px-3 py-3 text-center text-xs font-semibold text-green-600 uppercase tracking-wider">
                                                2022</th>
                                            <th
                                                class="px-3 py-3 text-center text-xs font-semibold text-green-600 uppercase tracking-wider">
                                                2023</th>
                                            <th
                                                class="px-3 py-3 text-center text-xs font-semibold text-green-600 uppercase tracking-wider">
                                                2024</th>
                                            <th
                                                class="px-3 py-3 text-center text-xs font-semibold text-green-600 uppercase tracking-wider">
                                                2025</th>
                                            <th
                                                class="px-3 py-3 text-center text-xs font-semibold text-green-600 uppercase tracking-wider">
                                                2026</th>
                                            <th
                                                class="px-3 py-3 text-center text-xs font-semibold text-green-600 uppercase tracking-wider">
                                                2027</th>
                                            <th
                                                class="px-3 py-3 text-center text-xs font-semibold text-green-600 uppercase tracking-wider">
                                                2028</th>
                                        </tr>
                                    </thead>
                                    <tbody id="dataTableBody_2022_2028" class="bg-white divide-y divide-gray-100">
                                        <!-- Dynamic rows will be inserted here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Period Tables Container -->
                    <div id="additionalPeriodsContainer"></div>

                    <!-- Remarks -->
                    <div>
                        <label for="remarks" class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                        <textarea id="remarks" name="remarks" rows="4"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Enter any additional remarks or notes"></textarea>
                    </div>

                    <!-- Period Buttons -->
                    <div class="flex flex-wrap gap-3 mb-6">
                        <button type="button" onclick="addPeriodTable('2028-2034')"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
                            <i class="fas fa-plus mr-2"></i>Add 2028-2034 Period
                        </button>
                        <button type="button" onclick="addPeriodTable('2034-2040')"
                            class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors shadow-sm">
                            <i class="fas fa-plus mr-2"></i>Add 2034-2040 Period
                        </button>
                    </div>
                </div>
        </div>

        <!-- Modal Footer -->
        <div class="flex justify-end space-x-3 mt-6">
            <button type="button" onclick="closeModal()"
                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                Cancel
            </button>
            <button type="button" id="nextBtn" onclick="goToStep2()" disabled
                class="px-4 py-2 bg-gray-400 text-white rounded-lg opacity-50 cursor-not-allowed transition-colors hidden">
                Next <i class="fas fa-arrow-right ml-2"></i>
            </button>
            <button type="submit" id="saveBtn" disabled
                class="px-4 py-2 bg-gray-400 text-white rounded-lg opacity-50 cursor-not-allowed transition-colors hidden">
                <i class="fas fa-save mr-2"></i>Save
            </button>
        </div>
        </form>
    </div>
</div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                <i class="fas fa-exclamation-triangle text-red-600"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Delete Record</h3>
            <p class="text-sm text-gray-500 mb-4">Are you sure you want to delete this BIODIVERSITY record? This action
                cannot
                be undone.</p>
            <div class="flex justify-center space-x-3">
                <button onclick="closeDeleteModal()"
                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                    Cancel
                </button>
                <button onclick="confirmDelete()"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>
