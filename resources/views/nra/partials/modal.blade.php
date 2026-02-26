<!-- Create/Edit Modal -->
<div id="nraModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white p-6 rounded-t-2xl">
            <div class="flex items-center justify-between">
                <h3 id="modalTitle" class="text-xl font-bold">Add New NRA Record</h3>
                <button onclick="closeModal()" class="text-white hover:text-gray-200 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>

        <form id="nraForm" class="p-6 space-y-4">
            <input type="hidden" id="recordId" name="id">

            <!-- Step 1: Record Type Selection -->
            <div id="step1" class="space-y-4">
                <div>
                    <label for="record_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Record Type <span class="text-red-500">*</span>
                    </label>
                    <select id="record_type" name="record_type"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                        onchange="showNextStep()">
                        <option value="">Select Type</option>
                        <option value="program">Program</option>
                        <option value="project">Project</option>
                        <option value="activity">Activity</option>
                    </select>
                </div>
            </div>

            <!-- Step 2: Conditional Questions -->
            <div id="step2" class="hidden space-y-4">
                <div id="project_indicator_question" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Does this project have indicators? <span class="text-red-500">*</span>
                    </label>
                    <div class="flex space-x-4">
                        <label class="flex items-center">
                            <input type="radio" name="project_has_indicators" value="yes" onchange="showFinalStep()"
                                class="mr-2">
                            <span>Yes</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="project_has_indicators" value="no" onchange="showFinalStep()"
                                class="mr-2">
                            <span>No</span>
                        </label>
                    </div>
                </div>

                <div id="activity_subactivity_question" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Does this activity have sub-activities? <span class="text-red-500">*</span>
                    </label>
                    <div class="flex space-x-4">
                        <label class="flex items-center">
                            <input type="radio" name="activity_has_subactivities" value="yes" onchange="showFinalStep()"
                                class="mr-2">
                            <span>Yes</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="activity_has_subactivities" value="no" onchange="showFinalStep()"
                                class="mr-2">
                            <span>No</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Step 3: Final Form Fields -->
            <div id="step3" class="hidden space-y-4">
                <div>
                    <label for="program_project_activity" class="block text-sm font-medium text-gray-700 mb-2">
                        Program/Project/Activity <span class="text-red-500">*</span>
                    </label>
                    <textarea id="program_project_activity" name="program_project_activity" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                        placeholder="Enter program/project/activity name"></textarea>
                </div>

                @php
                    function getHierarchicalNumberForRecord($record)
                    {
                        $activityCounter = 1;
                        $subActivityCounters = [];

                        // Get all records ordered by order to calculate numbering
                        $allRecords = \App\Models\Nra::orderBy('order_column')->get();

                        foreach ($allRecords as $r) {
                            // Process main activities (no parent_id)
                            if ($r->record_type === 'activity' && is_null($r->parent_id)) {
                                if ($r->id == $record->id) {
                                    return $activityCounter;
                                }
                                $activityCounter++;
                            }
                            // Process sub-activities (level 2)
                            elseif ($r->record_type === 'activity' && !is_null($r->parent_id)) {
                                $parentRecord = $r->parent;
                                if ($parentRecord && $parentRecord->record_type === 'activity') {
                                    // Find parent number
                                    $parentNumber = 1;
                                    $tempCounter = 1;
                                    foreach ($allRecords as $pr) {
                                        if ($pr->record_type === 'activity' && is_null($pr->parent_id)) {
                                            if ($pr->id == $parentRecord->id) {
                                                $parentNumber = $tempCounter;
                                                break;
                                            }
                                            $tempCounter++;
                                        }
                                    }

                                    if (!isset($subActivityCounters[$parentNumber])) {
                                        $subActivityCounters[$parentNumber] = 1;
                                    }

                                    if ($r->id == $record->id) {
                                        return $parentNumber . '.' . $subActivityCounters[$parentNumber];
                                    }

                                    $subActivityCounters[$parentNumber]++;
                                }
                            }
                        }

                        return $record->order_column;
                    }
                @endphp

                <!-- Parent Selection for Activities -->
                <div id="parent_selection" class="hidden">
                    <label for="parent_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Parent Activity <span class="text-gray-400">(optional)</span>
                    </label>
                    <select id="parent_id" name="parent_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                        <option value="">Select Parent Activity (Optional)</option>
                        @foreach($processedActivities as $potentialParent)
                            @if($potentialParent->record_type === 'activity' && ($potentialParent->indentation_level <= 1))
                                <option value="{{ $potentialParent->id }}">
                                    {{ str_repeat('-', $potentialParent->indentation_level ?? 0) }}{{ getHierarchicalNumberForRecord($potentialParent) }}
                                    {{ $potentialParent->display_name }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div id="additional_fields" class="hidden space-y-4">
                    <div>
                        <label for="output_indicators" class="block text-sm font-medium text-gray-700 mb-2">
                            Output Indicators <span id="indicatorsRequired" class="text-red-500">*</span>
                        </label>
                        <textarea id="output_indicators" name="output_indicators" rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                            placeholder="Describe the output indicators"></textarea>
                    </div>

                    <div>
                        <label for="office" class="block text-sm font-medium text-gray-700 mb-2">
                            Office <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="office" name="office"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                            placeholder="Enter office name">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="universe" class="block text-sm font-medium text-gray-700 mb-2">
                                Universe
                            </label>
                            <input type="number" id="universe" name="universe"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                placeholder="0" oninput="calculateBaseline()" onchange="calculateBaseline()">
                        </div>

                        <div>
                            <label for="accomplishment" class="block text-sm font-medium text-gray-700 mb-2">
                                Accomplishment
                            </label>
                            <input type="number" id="accomplishment" name="accomplishment"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                placeholder="0" oninput="calculateBaseline()" onchange="calculateBaseline()">
                        </div>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Baseline (Universe - Accomplishment):</span>
                            <div class="flex items-center space-x-2">
                                <span id="baselineDisplay" class="text-lg font-bold text-blue-600"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Buttons -->
            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                <button type="button" id="backBtn" onclick="goBack()"
                    class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all duration-200 font-medium hidden">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back
                </button>
                <div class="flex-1"></div>
                <button type="button" id="nextBtn" onclick="goNext()"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 font-medium shadow hover:shadow-lg hidden">
                    Next
                    <i class="fas fa-arrow-right ml-2"></i>
                </button>
                <button type="submit" id="submitBtn"
                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-200 font-medium shadow hover:shadow-lg hidden">
                    <i class="fas fa-save mr-2"></i>
                    <span id="submitButtonText">Save Record</span>
                </button>
                <button type="button" onclick="closeModal()"
                    class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all duration-200 font-medium">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full">
        <div class="bg-red-600 text-white p-6 rounded-t-2xl">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle text-2xl mr-3"></i>
                <h3 class="text-xl font-bold">Confirm Delete</h3>
            </div>
        </div>

        <div class="p-6">
            <p class="text-gray-700 mb-6">Are you sure you want to delete this NRA record? This action cannot be undone.
            </p>

            <div class="flex items-center justify-end space-x-3">
                <button onclick="closeDeleteModal()"
                    class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all duration-200 font-medium">
                    Cancel
                </button>
                <button onclick="confirmDelete()"
                    class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all duration-200 font-medium shadow hover:shadow-lg">
                    <i class="fas fa-trash mr-2"></i>
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>