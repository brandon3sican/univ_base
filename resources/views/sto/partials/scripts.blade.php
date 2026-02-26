<script>
    let deleteRecordId = null;
    let currentStep = 1;
    let originalAccomplishmentValues = {}; // Store original values for comparison
    let existingRemarks = {}; // Store existing remarks to preserve them

    // Define office order for consistent remarks arrangement
    const OFFICE_ORDER = ['RO', 'ABRA', 'APAYAO', 'BENGUET', 'IFUGAO', 'KALINGA', 'MT.PROVINCE', 'CAGAYAN', 'ISABELA', 'QUIRINO', 'AURORA', 'BATANES', 'PANGASINAN'];

    // Function to generate remarks inputs dynamically for offices with accomplishment > 0
    function generateRemarksInputs(officesWithAccomplishment, currentRemarksValues = {}) {
        const remarksContainer = document.getElementById('remarksInputs');

        if (officesWithAccomplishment.length === 0) {
            remarksContainer.innerHTML = '';
            return;
        }

        const remarksInputsHtml = officesWithAccomplishment.map(office => {
            const currentValue = currentRemarksValues[office] || '';
            return `
            <div class="space-y-1">
                <label class="block text-sm font-medium text-gray-700">
                    ${office} Remarks:
                </label>
                <textarea name="remarks[${office}]" rows="2"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-sm"
                    placeholder="Enter remarks for ${office}" required>${currentValue}</textarea>
            </div>
        `;
        }).join('');

        remarksContainer.innerHTML = remarksInputsHtml;
    }

    // Function to collect and combine remarks
    function collectRemarks() {
        const formData = new FormData(document.getElementById('stoForm'));
        const remarksValues = [];
        let hasAllRequiredRemarks = true;

        // First, add all existing remarks (preserve old ones)
        Object.keys(existingRemarks).forEach(office => {
            if (existingRemarks[office]) {
                remarksValues.push(`${office}: ${existingRemarks[office]}`);
            }
        });

        // Then, add new remarks from form (merge with existing)
        for (let [key, value] of formData.entries()) {
            if (key.startsWith('remarks[') && value.trim()) {
                const office = key.match(/remarks\[(.*?)\]/)[1];
                const newRemark = value.trim();

                // Check if this office already has an existing remark
                const existingIndex = remarksValues.findIndex(remark => remark.startsWith(`${office}:`));

                if (existingIndex !== -1) {
                    // Merge with existing remark
                    const existingRemark = remarksValues[existingIndex];
                    const existingText = existingRemark.substring(existingRemark.indexOf(':') + 1).trim();
                    const mergedRemark = `${existingText}; ${newRemark}`;
                    remarksValues[existingIndex] = `${office}: ${mergedRemark}`;
                } else {
                    // Add new remark
                    remarksValues.push(`${office}: ${newRemark}`);
                }
            }
        }

        // Sort remarks based on OFFICE_ORDER and ensure all selected offices are included
        const sortedRemarks = [];
        let selectedOffices = Array.from(document.querySelectorAll('input[name="office[]"]:checked')).map(cb => cb.value);

        OFFICE_ORDER.forEach(office => {
            // Only process offices that are actually selected
            if (selectedOffices.includes(office)) {
                const foundRemark = remarksValues.find(remark => remark.startsWith(`${office}:`));
                if (foundRemark) {
                    sortedRemarks.push(foundRemark);
                } else {
                    // Add "-" for offices without remarks to maintain alignment
                    sortedRemarks.push(`${office}: -`);
                }
            }
        });

        // Check if all required offices have remarks (only for newly updated accomplishments)
        selectedOffices.forEach(officeName => {
            const accomplishmentValue = formData.get(`accomplishment[${officeName}]`)?.trim() || '';
            const originalValue = originalAccomplishmentValues[officeName] || '';

            if (accomplishmentValue) {
                const accomplishmentNum = parseInt(accomplishmentValue);
                const originalNum = parseInt(originalValue) || 0;

                if (!isNaN(accomplishmentNum) && accomplishmentNum > 0) {
                    // Only check remarks if accomplishment is new or increased
                    if (!originalAccomplishmentValues[officeName] || accomplishmentNum > originalNum) {
                        // Check if office has either existing or new remarks
                        const hasExistingRemark = existingRemarks[officeName] && existingRemarks[officeName].trim();
                        const hasNewRemark = formData.get(`remarks[${officeName}]`)?.trim();

                        if (!hasExistingRemark && !hasNewRemark) {
                            hasAllRequiredRemarks = false;
                        }
                    }
                }
            }
        });

        return {
            combined: sortedRemarks.join('; '),
            hasAllRequired: hasAllRequiredRemarks
        };
    }

    // Function to dynamically generate universe, accomplishment, and target inputs for selected offices
    function updateOfficeInputs(currentUniverseValues = {}, currentAccomplishmentValues = {}, currentTargetValues = {}) {
        console.log('updateOfficeInputs called');
        officeCheckboxes = document.querySelectorAll('input[name="office[]"]:checked');
        selectedOffices = Array.from(officeCheckboxes).map(cb => cb.value);
        const universeInputsContainer = document.getElementById('universeInputs');
        const accomplishmentInputsContainer = document.getElementById('accomplishmentInputs');
        const targetInputsContainer = document.getElementById('targetInputs');

        console.log('Selected offices in updateOfficeInputs:', selectedOffices);
        console.log('Universe container:', universeInputsContainer);
        console.log('Accomplishment container:', accomplishmentInputsContainer);
        console.log('Target container:', targetInputsContainer);

        if (selectedOffices.length === 0) {
            universeInputsContainer.innerHTML = '<div class="text-xs text-gray-500">Select offices to see universe inputs</div>';
            accomplishmentInputsContainer.innerHTML = '<div class="text-xs text-gray-500">Select offices to see accomplishment inputs</div>';
            if (targetInputsContainer) {
                targetInputsContainer.innerHTML = '<div class="text-xs text-gray-500">Select offices to see target inputs</div>';
            }
            return;
        }

        // Generate universe inputs
        const universeInputsHtml = selectedOffices.map(office => {
            const currentValue = currentUniverseValues[office] || '';
            const placeholder = currentValue || '';
            return `
            <label class="flex items-center">
                <span class="text-sm w-16">${office}:</span>
                <input type="number" name="universe[${office}]" min="0" class="flex-1 px-3 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-sm" placeholder="${placeholder}">
            </label>
            `;
        }).join('');
        console.log('Generated universe HTML:', universeInputsHtml);
        universeInputsContainer.innerHTML = universeInputsHtml;

        // Generate accomplishment inputs
        const accomplishmentInputsHtml = selectedOffices.map(office => {
            const currentValue = currentAccomplishmentValues[office] || '';
            const placeholder = currentValue || '';
            return `
            <label class="flex items-center">
                <span class="text-sm w-16">${office}:</span>
                <input type="number" name="accomplishment[${office}]" min="0" class="flex-1 px-3 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-sm" placeholder="${placeholder}">
            </label>
            `;
        }).join('');
        console.log('Generated accomplishment HTML:', accomplishmentInputsHtml);
        accomplishmentInputsContainer.innerHTML = accomplishmentInputsHtml;

        // Generate target inputs for each year (2024-2028)
        if (targetInputsContainer) {
            const targetInputsHtml = selectedOffices.map(office => {
                return `
                <div class="border border-gray-200 rounded-lg p-3 mb-2">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">${office} Targets (2024-2028)</h4>
                    <div class="grid grid-cols-5 gap-2">
                        ${[2024, 2025, 2026, 2027, 2028].map(year => {
                    const currentValue = currentTargetValues[office] && currentTargetValues[office][year] ? currentTargetValues[office][year] : '';
                    return `
                            <div>
                                <label class="text-xs text-gray-600">${year}:</label>
                                <input type="number" name="target_${year}[${office}]" min="0" class="w-full px-2 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 text-sm" placeholder="0" value="${currentValue}">
                            </div>
                        `;
                }).join('')}
                    </div>
                </div>
                `;
            }).join('');
            console.log('Generated target HTML:', targetInputsHtml);
            targetInputsContainer.innerHTML = targetInputsHtml;
        }

        // Debug: Check if inputs exist after generation
        setTimeout(() => {
            const testUniverseInput = document.querySelector('input[name="universe[ABRA]"]');
            const testAccomplishmentInput = document.querySelector('input[name="accomplishment[ABRA]"]');
            console.log('After generation - ABRA universe input:', testUniverseInput);
            console.log('After generation - ABRA accomplishment input:', testAccomplishmentInput);
        }, 100);
    }

    // Add event listeners to office checkboxes
    document.addEventListener('DOMContentLoaded', function () {
        const officeCheckboxes = document.querySelectorAll('input[name="office[]"]');
        officeCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateOfficeInputs);
        });
    });

    function showNextStep() {
        const recordType = document.getElementById('record_type').value;
        const step1 = document.getElementById('step1');
        const step2 = document.getElementById('step2');
        const step3 = document.getElementById('step3');
        const backBtn = document.getElementById('backBtn');
        const nextBtn = document.getElementById('nextBtn');
        const submitBtn = document.getElementById('submitBtn');
        const projectIndicatorQuestion = document.getElementById('project_indicator_question');
        const activitySubactivityQuestion = document.getElementById('activity_subactivity_question');

        // Hide all conditional questions first
        projectIndicatorQuestion.classList.add('hidden');
        activitySubactivityQuestion.classList.add('hidden');

        if (recordType === 'program') {
            // Program: Go directly to final step
            step1.classList.add('hidden');
            step2.classList.add('hidden');
            step3.classList.remove('hidden');
            backBtn.classList.remove('hidden');
            nextBtn.classList.add('hidden');
            submitBtn.classList.remove('hidden');
            currentStep = 3;
        } else if (recordType === 'project') {
            // Project: Show indicator question
            step1.classList.add('hidden');
            step2.classList.remove('hidden');
            step3.classList.add('hidden');
            backBtn.classList.remove('hidden');
            nextBtn.classList.add('hidden');
            submitBtn.classList.add('hidden');
            projectIndicatorQuestion.classList.remove('hidden');
            currentStep = 2;
        } else if (recordType === 'activity') {
            // Activity: Show sub-activity question
            step1.classList.add('hidden');
            step2.classList.remove('hidden');
            step3.classList.add('hidden');
            backBtn.classList.remove('hidden');
            nextBtn.classList.add('hidden');
            submitBtn.classList.add('hidden');
            activitySubactivityQuestion.classList.remove('hidden');
            currentStep = 2;
        }
    }

    function showFinalStep() {
        const recordType = document.getElementById('record_type').value;
        const projectHasIndicators = document.querySelector('input[name="project_has_indicators"]:checked');
        const activityHasSubactivities = document.querySelector('input[name="activity_has_subactivities"]:checked');
        const step2 = document.getElementById('step2');
        const step3 = document.getElementById('step3');
        const nextBtn = document.getElementById('nextBtn');
        const submitBtn = document.getElementById('submitBtn');
        const additionalFields = document.getElementById('additional_fields');
        const parentSelection = document.getElementById('parent_selection');

        // Hide additional fields and parent selection first
        additionalFields.classList.add('hidden');
        parentSelection.classList.add('hidden');

        // Remove ALL required attributes when hiding fields
        removeRequiredAttributes();

        if (recordType === 'project' && projectHasIndicators) {
            if (projectHasIndicators.value === 'yes') {
                // Project with indicators: show all fields
                additionalFields.classList.remove('hidden');
                addRequiredAttributes();
            }
            // Go to final step
            step2.classList.add('hidden');
            step3.classList.remove('hidden');
            nextBtn.classList.add('hidden');
            submitBtn.classList.remove('hidden');
            currentStep = 3;
        } else if (recordType === 'activity' && activityHasSubactivities) {
            if (activityHasSubactivities.value === 'no') {
                // Activity with no sub-activities: show all fields and parent selection
                additionalFields.classList.remove('hidden');
                parentSelection.classList.remove('hidden');
                addRequiredAttributesForActivity();
            } else if (activityHasSubactivities.value === 'yes') {
                // Activity with sub-activities: show parent selection only
                parentSelection.classList.remove('hidden');
                addRequiredAttributesForActivity();
            }
            // Go to final step
            step2.classList.add('hidden');
            step3.classList.remove('hidden');
            nextBtn.classList.add('hidden');
            submitBtn.classList.remove('hidden');
            currentStep = 3;

            // Make fields focusable when additional fields are visible
            if (!additionalFields.classList.contains('hidden')) {
                officeCheckboxes = document.querySelectorAll('input[name="office[]"]');
                const universeInputs = document.querySelectorAll('input[name^="universe["]');
                const accomplishmentInputs = document.querySelectorAll('input[name^="accomplishment["]');
                const outputIndicators = document.getElementById('output_indicators');

                // Make office checkboxes focusable
                officeCheckboxes.forEach(checkbox => {
                    checkbox.setAttribute('tabindex', '0');
                });

                // Make universe inputs focusable
                universeInputs.forEach(input => {
                    input.setAttribute('tabindex', '0');
                });

                // Make accomplishment inputs focusable
                accomplishmentInputs.forEach(input => {
                    input.setAttribute('tabindex', '0');
                });

                if (outputIndicators) {
                    outputIndicators.setAttribute('tabindex', '0');
                    // output_indicators is not always required
                }
            }
        }
    }

    function goBack() {
        const step1 = document.getElementById('step1');
        const step2 = document.getElementById('step2');
        const step3 = document.getElementById('step3');
        const backBtn = document.getElementById('backBtn');
        const nextBtn = document.getElementById('nextBtn');
        const submitBtn = document.getElementById('submitBtn');

        if (currentStep === 3) {
            // Go back to step 2
            step1.classList.add('hidden');
            step2.classList.remove('hidden');
            step3.classList.add('hidden');
            backBtn.classList.remove('hidden');
            nextBtn.classList.add('hidden');
            submitBtn.classList.add('hidden');
            currentStep = 2;
        } else if (currentStep === 2) {
            // Go back to step 1
            step1.classList.remove('hidden');
            step2.classList.add('hidden');
            step3.classList.add('hidden');
            backBtn.classList.add('hidden');
            nextBtn.classList.add('hidden');
            submitBtn.classList.add('hidden');
            currentStep = 1;
        }
    }

    function goNext() {
        if (currentStep === 1) {
            showNextStep();
        } else if (currentStep === 2) {
            showFinalStep();
        }
    }

    function addRequiredAttributes() {
        const outputIndicators = document.getElementById('output_indicators');
        officeCheckboxes = document.querySelectorAll('input[name="office[]"]');
        const programProjectActivity = document.getElementById('program_project_activity');
        const indicatorsRequired = document.getElementById('indicatorsRequired');
        const additionalFields = document.getElementById('additional_fields');

        // Program/Project/Activity is always required
        programProjectActivity.setAttribute('required', 'required');

        // DO NOT add required attributes to additional fields - handle validation on submit
        // This prevents accessibility errors with hidden required fields

        if (additionalFields && !additionalFields.classList.contains('hidden')) {
            const recordType = document.getElementById('record_type').value;
            const projectHasIndicators = document.querySelector('input[name="project_has_indicators"]:checked');

            if (recordType === 'project' && projectHasIndicators && projectHasIndicators.value === 'yes') {
                // Project with indicators - show required asterisks but don't add required attribute
                indicatorsRequired.classList.remove('hidden');
            } else if (recordType === 'activity') {
                const activityHasSubactivities = document.querySelector('input[name="activity_has_subactivities"]:checked');
                if (activityHasSubactivities && activityHasSubactivities.value === 'no') {
                    // Activity with no sub-activities - hide indicators asterisk
                    indicatorsRequired.classList.add('hidden');
                } else {
                    // Activity with sub-activities - hide indicators asterisk
                    indicatorsRequired.classList.add('hidden');
                }
                indicatorsRequired.classList.add('hidden');
            } else {
                // All other cases - hide indicators asterisk
                indicatorsRequired.classList.add('hidden');
            }
        }
    }

    function addRequiredAttributesForActivity() {
        const outputIndicators = document.getElementById('output_indicators');
        officeCheckboxes = document.querySelectorAll('input[name="office[]"]');
        const programProjectActivity = document.getElementById('program_project_activity');
        const indicatorsRequired = document.getElementById('indicatorsRequired');
        const activityHasSubactivities = document.querySelector('input[name="activity_has_subactivities"]:checked');

        // Program/Project/Activity is always required
        programProjectActivity.setAttribute('required', 'required');

        // DO NOT add required attributes to additional fields - handle validation on submit
        // This prevents accessibility errors with hidden required fields

        if (activityHasSubactivities && activityHasSubactivities.value === 'no') {
            // Activity with no sub-activities - hide indicators asterisk
            indicatorsRequired.classList.add('hidden');
        } else {
            // Activity with sub-activities - hide indicators asterisk
            indicatorsRequired.classList.add('hidden');
        }
    }

    function removeRequiredAttributes() {
        const outputIndicators = document.getElementById('output_indicators');
        officeCheckboxes = document.querySelectorAll('input[name="office[]"]');
        const universeInputs = document.querySelectorAll('input[name^="universe["]');
        const accomplishmentInputs = document.querySelectorAll('input[name^="accomplishment["]');
        const programProjectActivity = document.getElementById('program_project_activity');
        const indicatorsRequired = document.getElementById('indicatorsRequired');

        // Remove ALL required attributes when fields are hidden
        outputIndicators.removeAttribute('required');
        programProjectActivity.removeAttribute('required');
        indicatorsRequired.classList.add('hidden');

        // Remove required from office checkboxes
        officeCheckboxes.forEach(checkbox => {
            checkbox.removeAttribute('required');
        });

        // Remove required and tabindex from universe inputs
        universeInputs.forEach(input => {
            input.removeAttribute('required');
            input.removeAttribute('tabindex');
        });

        // Remove required and tabindex from accomplishment inputs
        accomplishmentInputs.forEach(input => {
            input.removeAttribute('required');
            input.removeAttribute('tabindex');
        });

        // Also remove tabindex from other fields
        outputIndicators.removeAttribute('tabindex');
        officeCheckboxes.forEach(checkbox => {
            checkbox.removeAttribute('tabindex');
        });
    }

    function openCreateModal() {
        document.getElementById('modalTitle').textContent = 'Add New STO Record';
        document.getElementById('submitButtonText').textContent = 'Save Record';
        document.getElementById('stoForm').reset();
        document.getElementById('recordId').value = '';
        document.getElementById('stoModal').classList.remove('hidden');
        document.getElementById('stoModal').classList.add('flex');

        // Reset to step 1
        resetFormSteps();
    }

    function resetFormSteps() {
        const step1 = document.getElementById('step1');
        const step2 = document.getElementById('step2');
        const step3 = document.getElementById('step3');
        const backBtn = document.getElementById('backBtn');
        const nextBtn = document.getElementById('nextBtn');
        const submitBtn = document.getElementById('submitBtn');

        step1.classList.remove('hidden');
        step2.classList.add('hidden');
        step3.classList.add('hidden');
        backBtn.classList.add('hidden');
        nextBtn.classList.add('hidden');
        submitBtn.classList.add('hidden');

        // Clear original accomplishment values for new records
        originalAccomplishmentValues = {};
        existingRemarks = {}; // Clear existing remarks for new records

        removeRequiredAttributes();
        currentStep = 1;
    }

    function closeModal() {
        document.getElementById('stoModal').classList.add('hidden');
        document.getElementById('stoModal').classList.remove('flex');
        document.getElementById('stoForm').reset();
        resetFormSteps();
    }

    function editRecord(id) {
        fetch(`/sto/${id}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.text(); // Get response as text first
            })
            .then(text => {
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        const record = data.data;

                        // Set form values
                        document.getElementById('modalTitle').textContent = 'Edit STO Record';
                        document.getElementById('submitButtonText').textContent = 'Update Record';
                        document.getElementById('recordId').value = record.id;
                        document.getElementById('program_project_activity').value = record.program_project_activity;
                        document.getElementById('record_type').value = record.record_type;

                        // Reset form steps and show modal
                        resetFormSteps();
                        document.getElementById('stoModal').classList.remove('hidden');
                        document.getElementById('stoModal').classList.add('flex');

                        // Trigger record type change to show appropriate fields
                        document.getElementById('record_type').dispatchEvent(new Event('change'));

                        // Set conditional values
                        if (record.record_type === 'project' && record.project_has_indicators) {
                            const indicatorRadio = document.querySelector(`input[name="project_has_indicators"][value="${record.project_has_indicators}"]`);
                            if (indicatorRadio) {
                                indicatorRadio.checked = true;
                                indicatorRadio.dispatchEvent(new Event('change'));
                            }
                        } else if (record.record_type === 'activity' && record.activity_has_subactivities) {
                            const subactivityRadio = document.querySelector(`input[name="activity_has_subactivities"][value="${record.activity_has_subactivities}"]`);
                            if (subactivityRadio) {
                                subactivityRadio.checked = true;
                                subactivityRadio.dispatchEvent(new Event('change'));
                            }
                        }

                        // Set additional fields if they exist
                        if (record.output_indicators) {
                            document.getElementById('output_indicators').value = record.output_indicators;
                        }
                        if (record.office) {
                            // Handle office checkboxes - split by comma and check appropriate boxes
                            const offices = record.office.split(',');
                            officeCheckboxes = document.querySelectorAll('input[name="office[]"]');
                            officeCheckboxes.forEach(checkbox => {
                                checkbox.checked = offices.includes(checkbox.value);
                            });

                            // Parse current universe, accomplishment, and target values
                            const currentUniverseValues = {};
                            const currentAccomplishmentValues = {};
                            const currentTargetValues = {};
                            const currentRemarksValues = {};

                            if (record.universe && typeof record.universe === 'string') {
                                const universes = record.universe.split(',');
                                universes.forEach((universe, index) => {
                                    const office = offices[index];
                                    if (office) {
                                        currentUniverseValues[office] = universe.trim();
                                    }
                                });
                            }

                            if (record.accomplishment && typeof record.accomplishment === 'string') {
                                const accomplishments = record.accomplishment.split(',');
                                accomplishments.forEach((accomplishment, index) => {
                                    const office = offices[index];
                                    if (office) {
                                        currentAccomplishmentValues[office] = accomplishment.trim();
                                        // Store original values for comparison
                                        originalAccomplishmentValues[office] = accomplishment.trim();
                                    }
                                });
                            }

                            // Parse target values (2024-2028) - handle comma-separated strings like universe/accomplishment
                            for (let year = 2024; year <= 2028; year++) {
                                const targetField = `target_${year}`;
                                if (record[targetField]) {
                                    const targets = record[targetField].split(',');
                                    targets.forEach((target, index) => {
                                        const office = offices[index];
                                        if (office && !currentTargetValues[office]) {
                                            currentTargetValues[office] = {};
                                        }
                                        if (office) {
                                            currentTargetValues[office][year] = target.trim();
                                        }
                                    });
                                }
                            }

                            if (record.remarks && typeof record.remarks === 'string') {
                                // Parse remarks from "RO: remark1; ABRA: remark2" format
                                const remarksArray = record.remarks.split(';');
                                remarksArray.forEach(remark => {
                                    const parts = remark.trim().split(':');
                                    if (parts.length === 2) {
                                        const office = parts[0].trim();
                                        const remarkText = parts[1].trim();
                                        if (office && remarkText) {
                                            currentRemarksValues[office] = remarkText;
                                            // Store existing remarks to preserve them
                                            existingRemarks[office] = remarkText;
                                        }
                                    }
                                });
                            }

                            // Generate inputs for selected offices with current values as placeholders
                            updateOfficeInputs(currentUniverseValues, currentAccomplishmentValues, currentTargetValues);

                            // Set actual input values after a short delay to ensure inputs are generated
                            setTimeout(() => {
                                Object.keys(currentUniverseValues).forEach(office => {
                                    const universeInput = document.querySelector(`input[name="universe[${office}]"]`);
                                    if (universeInput && currentUniverseValues[office]) {
                                        universeInput.value = currentUniverseValues[office];
                                    }
                                });

                                Object.keys(currentAccomplishmentValues).forEach(office => {
                                    const accomplishmentInput = document.querySelector(`input[name="accomplishment[${office}]"]`);
                                    if (accomplishmentInput && currentAccomplishmentValues[office]) {
                                        accomplishmentInput.value = currentAccomplishmentValues[office];
                                    }
                                });

                                // Pre-populate remarks inputs if they exist
                                Object.keys(currentRemarksValues).forEach(office => {
                                    const remarksInput = document.querySelector(`textarea[name="remarks[${office}]"]`);
                                    if (remarksInput && currentRemarksValues[office]) {
                                        remarksInput.value = currentRemarksValues[office];
                                    }
                                });

                                // Pre-populate target inputs if they exist
                                Object.keys(currentTargetValues).forEach(office => {
                                    Object.keys(currentTargetValues[office]).forEach(year => {
                                        const targetInput = document.querySelector(`input[name="target_${year}[${office}]"]`);
                                        if (targetInput && currentTargetValues[office][year]) {
                                            targetInput.value = currentTargetValues[office][year];
                                        }
                                    });
                                });
                            }, 100);
                        }
                        if (record.parent_id) {
                            document.getElementById('parent_id').value = record.parent_id;
                        }
                    } else {
                        alert('Error loading record: ' + (data.message || 'Unknown error'));
                    }
                } catch (jsonError) {
                    console.error('JSON parsing error:', jsonError);
                    console.error('Response text:', text);
                    throw new Error('Invalid JSON response from server');
                }
            })
            .catch(error => {
                console.error('Error loading record:', error);
                alert('Error loading record: ' + error.message);
            });
    }

    function deleteRecord(id) {
        deleteRecordId = id;
        document.getElementById('deleteModal').classList.remove('hidden');
        document.getElementById('deleteModal').classList.add('flex');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        document.getElementById('deleteModal').classList.remove('flex');
        deleteRecordId = null;
    }

    function confirmDelete() {
        if (deleteRecordId) {
            fetch(`/sto/${deleteRecordId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        closeDeleteModal();
                        location.reload();
                    } else {
                        alert('Error deleting record: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error deleting record:', error);
                });
        }
    }

    let draggedRow = null;

    function handleDragStart(e) {
        draggedRow = this;
        this.style.opacity = '0.5';
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/html', this.innerHTML);
    }

    function handleDragOver(e) {
        if (e.preventDefault) {
            e.preventDefault();
        }
        e.dataTransfer.dropEffect = 'move';

        // Get the row being dragged over
        const targetRow = this.closest('tr');
        if (targetRow && targetRow !== draggedRow) {
            targetRow.style.backgroundColor = '#f3f4f6';
        }

        return false;
    }

    function handleDrop(e) {
        if (e.stopPropagation) {
            e.stopPropagation();
        }

        const targetRow = this.closest('tr');
        if (draggedRow !== targetRow) {
            const draggedId = draggedRow.getAttribute('data-id');
            const targetId = targetRow.getAttribute('data-id');

            // Get all rows in the table body
            const tbody = document.getElementById('sortableTableBody');
            const allRows = Array.from(tbody.querySelectorAll('tr'));

            // Find current positions
            const draggedIndex = allRows.indexOf(draggedRow);
            const targetIndex = allRows.indexOf(targetRow);

            // Remove dragged row and insert at target position
            const [draggedRowData] = allRows.splice(draggedIndex, 1);
            allRows.splice(targetIndex, 0, draggedRowData);

            // Re-append all rows in new order
            allRows.forEach(row => tbody.appendChild(row));

            // Update the order on the server
            updateOrder(draggedId, targetId);
        }

        return false;
    }

    function moveUp(id) {
        fetch(`/sto/move-up/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Record moved up successfully!');
                    location.reload();
                } else {
                    alert('Error moving record up: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error moving record up:', error);
                alert('Error moving record up');
            });
    }

    function moveDown(id) {
        fetch(`/sto/move-down/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Record moved down successfully!');
                    location.reload();
                } else {
                    alert('Error moving record down: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error moving record down:', error);
                alert('Error moving record down');
            });
    }

    function handleDragEnd(e) {
        // Reset styles
        const rows = document.querySelectorAll('#sortableTableBody tr');
        rows.forEach(row => {
            row.style.opacity = '';
            row.style.backgroundColor = '';
        });
    }

    function updateOrder(draggedId, targetId) {
        fetch('/sto/reorder', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                dragged_id: draggedId,
                target_id: targetId
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Order updated successfully');
                } else {
                    console.error('Error updating order:', data.message);
                    location.reload(); // Reload to restore correct order
                }
            })
            .catch(error => {
                console.error('Error updating order:', error);
                location.reload(); // Reload to restore correct order
            });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const stoForm = document.getElementById('stoForm');
        if (stoForm) {
            stoForm.addEventListener('submit', async function (e) {
                e.preventDefault();

                // Client-side validation before submission
                const programProjectActivity = document.getElementById('program_project_activity').value.trim();
                const recordType = document.getElementById('record_type').value;
                const projectHasIndicators = document.querySelector('input[name="project_has_indicators"]:checked');
                const activityHasSubactivities = document.querySelector('input[name="activity_has_subactivities"]:checked');
                const additionalFields = document.getElementById('additional_fields');
                const isAdditionalFieldsVisible = !additionalFields.classList.contains('hidden');

                // Validate program/project/activity (always required)
                if (!programProjectActivity) {
                    alert('Program/Project/Activity is required');
                    return;
                }

                // Validate additional fields based on record type and visibility
                if (isAdditionalFieldsVisible) {
                    // Collect form data first
                    const formData = new FormData(this);
                    const outputIndicators = formData.get('output_indicators')?.trim() || '';
                    officeCheckboxes = document.querySelectorAll('input[name="office[]"]:checked');
                    const office = Array.from(officeCheckboxes).map(cb => cb.value);

                    // Debug: Log selected offices
                    console.log('Selected offices:', office);

                    // Get values from form data for selected offices only
                    const universeValues = {};
                    const accomplishmentValues = {};
                    const remarksValues = {};
                    let hasAnyUniverseValue = false;
                    let hasAnyAccomplishmentValue = false;
                    let hasAccomplishmentGreaterThanZero = false;

                    office.forEach(officeName => {
                        const universeValue = formData.get(`universe[${officeName}]`)?.trim() || '';
                        const accomplishmentValue = formData.get(`accomplishment[${officeName}]`)?.trim() || '';
                        const remarksValue = formData.get(`remarks[${officeName}]`)?.trim() || '';

                        // Debug: Log form data values
                        console.log(`Office ${officeName}:`, {
                            universeValue: universeValue || 'NO VALUE',
                            accomplishmentValue: accomplishmentValue || 'NO VALUE',
                            remarksValue: remarksValue || 'NO VALUE'
                        });

                        if (universeValue !== '') {
                            universeValues[officeName] = universeValue;
                            hasAnyUniverseValue = true;
                            console.log(`Universe found for ${officeName}: ${universeValue}`);
                        }
                        if (accomplishmentValue !== '') {
                            accomplishmentValues[officeName] = accomplishmentValue;
                            hasAnyAccomplishmentValue = true;
                            console.log(`Accomplishment found for ${officeName}: ${accomplishmentValue}`);

                            // Check if accomplishment is greater than 0
                            const accomplishmentNum = parseInt(accomplishmentValue);
                            if (!isNaN(accomplishmentNum) && accomplishmentNum > 0) {
                                hasAccomplishmentGreaterThanZero = true;
                                console.log(`Accomplishment > 0 detected for ${officeName}: ${accomplishmentNum}`);
                            }
                        }
                        if (remarksValue !== '') {
                            remarksValues[officeName] = remarksValue;
                        }
                    });

                    // Debug: Log validation results
                    console.log('Validation results:', {
                        hasAnyUniverseValue,
                        hasAnyAccomplishmentValue,
                        hasAccomplishmentGreaterThanZero,
                        recordType,
                        projectHasIndicators: projectHasIndicators ? projectHasIndicators.value : null,
                        activityHasSubactivities: activityHasSubactivities ? activityHasSubactivities.value : null
                    });

                    if (recordType === 'project' && projectHasIndicators && projectHasIndicators.value === 'yes') {
                        // Project with indicators - all fields required except universe and accomplishment
                        if (!outputIndicators) {
                            alert('Output Indicators is required for projects with indicators');
                            return;
                        }
                        if (office.length === 0) {
                            alert('Office is required for projects with indicators');
                            return;
                        }

                        // Check if remarks are required when accomplishment > 0
                        if (hasAccomplishmentGreaterThanZero) {
                            // Get offices with newly updated accomplishment > 0
                            const officesWithNewAccomplishment = [];
                            office.forEach(officeName => {
                                const accomplishmentValue = accomplishmentValues[officeName];
                                const originalValue = originalAccomplishmentValues[officeName] || '';

                                if (accomplishmentValue) {
                                    const accomplishmentNum = parseInt(accomplishmentValue);
                                    const originalNum = parseInt(originalValue) || 0;

                                    if (!isNaN(accomplishmentNum) && accomplishmentNum > 0) {
                                        // Only include if accomplishment is new or increased
                                        if (!originalAccomplishmentValues[officeName] || accomplishmentNum > originalNum) {
                                            officesWithNewAccomplishment.push(officeName);
                                        }
                                    }
                                }
                            });

                            // Check if remarks already exist for these newly updated offices
                            const remarksData = collectRemarks();

                            if (!remarksData.hasAllRequired) {
                                // Collect current remarks values to pre-populate
                                const currentRemarksValues = {};
                                officesWithNewAccomplishment.forEach(office => {
                                    const remarksInput = document.querySelector(`textarea[name="remarks[${office}]"]`);
                                    if (remarksInput) {
                                        currentRemarksValues[office] = remarksInput.value;
                                    }
                                });

                                // Generate remarks inputs for offices with newly updated accomplishment > 0
                                generateRemarksInputs(officesWithNewAccomplishment, currentRemarksValues);

                                // Ask user to fill remarks for newly updated offices
                                alert(`Please enter remarks for the following offices with new accomplishments: ${officesWithNewAccomplishment.join(', ')}`);
                                return;
                            }
                        }

                        // Universe and accomplishment are now optional
                    } else if (recordType === 'activity' && activityHasSubactivities && activityHasSubactivities.value === 'no') {
                        // Activity with no sub-activities - office is required, universe and accomplishment are optional
                        if (office.length === 0) {
                            alert('Office is required for activities without sub-activities');
                            return;
                        }

                        // Check if remarks are required when accomplishment > 0
                        if (hasAccomplishmentGreaterThanZero) {
                            // Get offices with newly updated accomplishment > 0
                            const officesWithNewAccomplishment = [];
                            office.forEach(officeName => {
                                const accomplishmentValue = accomplishmentValues[officeName];
                                const originalValue = originalAccomplishmentValues[officeName] || '';

                                if (accomplishmentValue) {
                                    const accomplishmentNum = parseInt(accomplishmentValue);
                                    const originalNum = parseInt(originalValue) || 0;

                                    if (!isNaN(accomplishmentNum) && accomplishmentNum > 0) {
                                        // Only include if accomplishment is new or increased
                                        if (!originalAccomplishmentValues[officeName] || accomplishmentNum > originalNum) {
                                            officesWithNewAccomplishment.push(officeName);
                                        }
                                    }
                                }
                            });

                            // Check if remarks already exist for these newly updated offices
                            const remarksData = collectRemarks();

                            if (!remarksData.hasAllRequired) {
                                // Collect current remarks values to pre-populate
                                const currentRemarksValues = {};
                                officesWithNewAccomplishment.forEach(office => {
                                    const remarksInput = document.querySelector(`textarea[name="remarks[${office}]"]`);
                                    if (remarksInput) {
                                        currentRemarksValues[office] = remarksInput.value;
                                    }
                                });

                                // Generate remarks inputs for offices with newly updated accomplishment > 0
                                generateRemarksInputs(officesWithNewAccomplishment, currentRemarksValues);

                                // Ask user to fill remarks for newly updated offices
                                alert(`Please enter remarks for the following offices with new accomplishments: ${officesWithNewAccomplishment.join(', ')}`);
                                return;
                            }
                        }

                        // Universe and accomplishment are now optional
                    }
                }

                // Validate parent_id for activities with sub-activities (only for sub-activities, not main activities)
                if (recordType === 'activity' && activityHasSubactivities && activityHasSubactivities.value === 'yes') {
                    // This is a main activity with sub-activities - parent_id should be null
                    // No validation needed for parent_id
                }

                const formData = new FormData(this);
                const recordId = document.getElementById('recordId').value;
                const url = recordId ? `/sto/${recordId}/update` : '/sto/store';
                const method = 'POST';

                // Get values from form data
                const outputIndicators = formData.get('output_indicators')?.trim() || '';
                officeCheckboxes = document.querySelectorAll('input[name="office[]"]:checked');
                const office = Array.from(officeCheckboxes).map(cb => cb.value);

                // Collect universe, accomplishment, and target values
                const universeValues = {};
                const accomplishmentValues = {};
                const targetValues = {};

                // Initialize target values for each year
                for (let year = 2024; year <= 2028; year++) {
                    targetValues[`target_${year}`] = {};
                }

                office.forEach(officeName => {
                    const universeValue = formData.get(`universe[${officeName}]`)?.trim() || '';
                    const accomplishmentValue = formData.get(`accomplishment[${officeName}]`)?.trim() || '';

                    // Always include all selected offices, even with empty values
                    universeValues[officeName] = universeValue;
                    accomplishmentValues[officeName] = accomplishmentValue;

                    // Collect target values for each year
                    for (let year = 2024; year <= 2028; year++) {
                        const targetValue = formData.get(`target_${year}[${officeName}]`)?.trim() || '';
                        targetValues[`target_${year}`][officeName] = targetValue;
                    }
                });

                // Build data object based on visible fields
                const remarksData = collectRemarks();
                const data = {
                    program_project_activity: formData.get('program_project_activity'),
                    record_type: formData.get('record_type'),
                    output_indicators: outputIndicators,
                    universe: universeValues,
                    accomplishment: accomplishmentValues,
                    remarks: remarksData.combined, // Use combined remarks string
                    // Add target values for each year
                    ...targetValues, // Spread target values into data object
                };

                const isActivityWithSubactivities = activityHasSubactivities && activityHasSubactivities.value === 'yes';

                if (!isActivityWithSubactivities) {
                    data.office = Array.from(officeCheckboxes).map(cb => cb.value).join(',');
                }

                // Include parent_id if field exists and has value, or if activity has sub-activities
                const parentIdField = document.getElementById('parent_id');
                if (parentIdField && parentIdField.value) {
                    data.parent_id = parseInt(parentIdField.value);
                } else if (recordType === 'activity' && activityHasSubactivities && activityHasSubactivities.value === 'yes') {
                    // Main activity with sub-activities - explicitly set parent_id to null
                    data.parent_id = null;
                }

                // Include conditional answers
                if (projectHasIndicators) {
                    data.project_has_indicators = projectHasIndicators.value;
                }

                if (activityHasSubactivities) {
                    data.activity_has_subactivities = activityHasSubactivities.value;
                }

                console.log('Submitting data:', data);
                console.log('URL:', url);

                fetch(url, {
                    method: method,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                    .then(response => {
                        console.log('Response status:', response.status);
                        return response.json();
                    })
                    .then(data => {
                        console.log('Response data:', data);
                        if (data.success) {
                            alert(data.message);
                            closeModal();
                            location.reload();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error saving record:', error);
                        alert('Error saving record');
                    });
            });
        }
    });

</script>