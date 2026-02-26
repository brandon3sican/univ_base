<script>
    let deleteRecordId = null;
    let currentStep = 1;

    // ────────────────────────────────────────────────
    // Step Navigation
    // ────────────────────────────────────────────────

    function showNextStep() {
        const type = document.getElementById('record_type')?.value;
        if (!type) return;

        document.getElementById('step1').classList.add('hidden');
        document.getElementById('step2').classList.add('hidden');
        document.getElementById('step3').classList.add('hidden');

        document.getElementById('project_indicator_question')?.classList.add('hidden');
        document.getElementById('activity_subactivity_question')?.classList.add('hidden');

        if (type === 'program') {
            document.getElementById('step3').classList.remove('hidden');
            currentStep = 3;
        } else if (type === 'project') {
            document.getElementById('step2').classList.remove('hidden');
            document.getElementById('project_indicator_question')?.classList.remove('hidden');
            currentStep = 2;
        } else if (type === 'activity') {
            document.getElementById('step2').classList.remove('hidden');
            document.getElementById('activity_subactivity_question')?.classList.remove('hidden');
            currentStep = 2;
        }

        updateNavigationButtons();
    }

    function showFinalStep() {
        const type = document.getElementById('record_type')?.value;
        const projIndicators = document.querySelector('input[name="project_has_indicators"]:checked')?.value;
        const actSub = document.querySelector('input[name="activity_has_subactivities"]:checked')?.value;

        document.getElementById('step2').classList.add('hidden');
        document.getElementById('step3').classList.remove('hidden');

        const addFields = document.getElementById('additional_fields');
        const parentSel = document.getElementById('parent_selection');

        addFields?.classList.add('hidden');
        parentSel?.classList.add('hidden');

        if (type === 'project' && projIndicators === 'yes') {
            addFields?.classList.remove('hidden');
        } else if (type === 'activity') {
            if (actSub === 'no') {
                addFields?.classList.remove('hidden');
                parentSel?.classList.remove('hidden');
            } else if (actSub === 'yes') {
                parentSel?.classList.remove('hidden');
            }
        }

        currentStep = 3;
        updateNavigationButtons();
    }

    function updateNavigationButtons() {
        const back  = document.getElementById('backBtn');
        const next  = document.getElementById('nextBtn');
        const sub   = document.getElementById('submitBtn');

        back.classList.toggle('hidden', currentStep === 1);
        next.classList.toggle('hidden', currentStep !== 1 && currentStep !== 2);
        sub.classList.toggle('hidden', currentStep !== 3);
    }

    function goBack() {
        if (currentStep === 3) {
            document.getElementById('step3').classList.add('hidden');
            document.getElementById('step2').classList.remove('hidden');
            currentStep = 2;
        } else if (currentStep === 2) {
            document.getElementById('step2').classList.add('hidden');
            document.getElementById('step1').classList.remove('hidden');
            currentStep = 1;
        }
        updateNavigationButtons();
    }

    function goNext() {
        if (currentStep === 1) showNextStep();
        else if (currentStep === 2) showFinalStep();
    }

    // ────────────────────────────────────────────────
    // Modal Controls
    // ────────────────────────────────────────────────

    function openCreateModal() {
        document.getElementById('modalTitle').textContent = 'Add New Record';
        document.getElementById('submitButtonText').textContent = 'Save Record';
        document.getElementById('recordId').value = '';
        document.getElementById('nraForm')?.reset();
        document.getElementById('nraModal').classList.remove('hidden');
        document.getElementById('nraModal').classList.add('flex');
        resetFormSteps();
    }

    function resetFormSteps() {
        document.getElementById('step1').classList.remove('hidden');
        document.getElementById('step2').classList.add('hidden');
        document.getElementById('step3').classList.add('hidden');
        currentStep = 1;
        updateNavigationButtons();
    }

    function closeModal() {
        document.getElementById('nraModal').classList.add('hidden');
        document.getElementById('nraModal').classList.remove('flex');
        document.getElementById('nraForm')?.reset();
        resetFormSteps();
    }

    // ────────────────────────────────────────────────
    // CRUD Operations
    // ────────────────────────────────────────────────

    async function editRecord(id) {
        try {
            const res = await fetch(`/nra/${id}`);
            const { success, data, message } = await res.json();

            if (!success) throw new Error(message || 'Failed to load record');

            document.getElementById('modalTitle').textContent = 'Edit Record';
            document.getElementById('submitButtonText').textContent = 'Update Record';
            document.getElementById('recordId').value = data.id || '';
            document.getElementById('program_project_activity').value = data.program_project_activity || '';
            document.getElementById('record_type').value = data.record_type || '';

            // Reset & open modal
            resetFormSteps();
            document.getElementById('nraModal').classList.remove('hidden');
            document.getElementById('nraModal').classList.add('flex');

            // Trigger change event to show correct conditional fields
            document.getElementById('record_type').dispatchEvent(new Event('change'));

            // Set conditional radio buttons
            if (data.record_type === 'project' && data.project_has_indicators) {
                const radio = document.querySelector(`input[name="project_has_indicators"][value="${data.project_has_indicators}"]`);
                if (radio) {
                    radio.checked = true;
                    radio.dispatchEvent(new Event('change'));
                }
            }
            if (data.record_type === 'activity' && data.activity_has_subactivities) {
                const radio = document.querySelector(`input[name="activity_has_subactivities"][value="${data.activity_has_subactivities}"]`);
                if (radio) {
                    radio.checked = true;
                    radio.dispatchEvent(new Event('change'));
                }
            }

            // Fill optional fields
            ['output_indicators', 'office', 'universe', 'accomplishment', 'parent_id'].forEach(field => {
                const el = document.getElementById(field);
                if (el) el.value = data[field] ?? '';
            });

        } catch (err) {
            console.error('Edit error:', err);
            alert('Error loading record: ' + err.message);
        }
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

    async function confirmDelete() {
        if (!deleteRecordId) return;

        try {
            const res = await fetch(`/nra/${deleteRecordId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Accept': 'application/json'
                }
            });

            const data = await res.json();
            if (data.success) {
                alert(data.message || 'Record deleted successfully');
                closeDeleteModal();
                location.reload();
            } else {
                alert('Delete failed: ' + (data.message || 'Unknown error'));
            }
        } catch (err) {
            console.error('Delete error:', err);
            alert('Network error during delete');
        }
    }

    // ────────────────────────────────────────────────
    // Reordering (Move Up / Move Down)
    // ────────────────────────────────────────────────

    async function moveUp(id) {
        try {
            const res = await fetch(`/nra/move-up/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

            const data = await res.json();
            if (data.success) {
                // Optional: alert('Moved up successfully');
                location.reload();
            } else {
                alert(data.message || 'Could not move record up');
            }
        } catch (err) {
            console.error('Move up error:', err);
            alert('Error moving record up');
        }
    }

    async function moveDown(id) {
        try {
            const res = await fetch(`/nra/move-down/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

            const data = await res.json();
            if (data.success) {
                // Optional: alert('Moved down successfully');
                location.reload();
            } else {
                alert(data.message || 'Could not move record down');
            }
        } catch (err) {
            console.error('Move down error:', err);
            alert('Error moving record down');
        }
    }

    // ────────────────────────────────────────────────
    // Form Submit
    // ────────────────────────────────────────────────

    document.getElementById('nraForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();

        const ppa = document.getElementById('program_project_activity')?.value?.trim();
        if (!ppa) {
            alert('Program / Project / Activity name is required.');
            return;
        }

        const formData   = new FormData(this);
        const recordId   = document.getElementById('recordId')?.value;
        const url        = recordId ? `/nra/${recordId}/update` : '/nra/store';

        const payload = {
            program_project_activity: ppa,
            record_type:             formData.get('record_type'),
        };

        const addFieldsVisible = !document.getElementById('additional_fields')?.classList.contains('hidden');

        if (addFieldsVisible) {
            const type    = payload.record_type;
            const projInd = document.querySelector('input[name="project_has_indicators"]:checked')?.value;
            const actSub  = document.querySelector('input[name="activity_has_subactivities"]:checked')?.value;

            if ((type === 'project' && projInd === 'yes') || (type === 'activity' && actSub === 'no')) {
                payload.output_indicators = formData.get('output_indicators')?.trim() || null;
                payload.office            = formData.get('office')?.trim() || null;

                const uniVal = formData.get('universe')?.trim();
                const accVal = formData.get('accomplishment')?.trim();

                payload.universe       = uniVal ? Number(uniVal) : null;
                payload.accomplishment = accVal ? Number(accVal) : null;
            }
        }

        // Parent ID handling
        const parentField = document.getElementById('parent_id');
        if (parentField?.value) {
            payload.parent_id = Number(parentField.value);
        } else if (payload.record_type === 'activity' &&
                   document.querySelector('input[name="activity_has_subactivities"]:checked')?.value === 'yes') {
            payload.parent_id = null;
        }

        // Include conditional flags if they exist
        const projInd = document.querySelector('input[name="project_has_indicators"]:checked')?.value;
        const actSub  = document.querySelector('input[name="activity_has_subactivities"]:checked')?.value;
        if (projInd) payload.project_has_indicators    = projInd;
        if (actSub)  payload.activity_has_subactivities = actSub;

        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            const result = await res.json();

            if (result.success) {
                alert(result.message || 'Record saved successfully');
                closeModal();
                location.reload();
            } else {
                alert('Save failed: ' + (result.message || 'Unknown error'));
            }
        } catch (err) {
            console.error('Submit error:', err);
            alert('Error communicating with server');
        }
    });
</script>