<script>
    function toggleActionsColumn() {
        const actionsHeader = document.getElementById('actionsHeader');
        const toggleBtn = document.getElementById('toggleActionsBtn');
        const toggleText = document.getElementById('toggleActionsText');
        const toggleIcon = toggleBtn.querySelector('i');

        // Toggle header
        actionsHeader.classList.toggle('hidden');

        // Toggle action cells - simple approach for all rows
        const allDataRows = document.querySelectorAll('tbody tr');
        allDataRows.forEach(row => {
            const cells = row.querySelectorAll('td');
            // Actions should be in the last cell for all rows
            if (cells.length > 0) {
                const lastCell = cells[cells.length - 1];
                lastCell.classList.toggle('hidden');
            }
        });

        // Update button text and icon
        if (actionsHeader.classList.contains('hidden')) {
            toggleText.textContent = 'Show Actions';
            toggleIcon.className = 'fas fa-eye mr-2';
        } else {
            toggleText.textContent = 'Hide Actions';
            toggleIcon.className = 'fas fa-eye-slash mr-2';
        }
    }

    function updateMergedRowColspan() {
        const mergedRows = document.querySelectorAll('[data-merged-row]');
        const targetHeader = document.getElementById('targetHeader');
        const remarksHeader = document.getElementById('remarksHeader');
        const actionsHeader = document.getElementById('actionsHeader');

        // Calculate base colspan (always visible columns: PPA, Office, Universe, Baseline = 4)
        // Plus 2 accomplishment sub-columns = 6
        let colspan = 6;

        // Add target columns if visible (dynamic count based on current year)
        if (targetHeader && !targetHeader.classList.contains('hidden')) {
            const targetColspan = parseInt(targetHeader.getAttribute('colspan') || '2');
            colspan += targetColspan;
        }

        // Add remarks column if visible
        if (remarksHeader && !remarksHeader.classList.contains('hidden')) {
            colspan += 1;
        }

        // Note: Actions column is NOT included in merged row colspan
        // Merged rows should have a separate actions cell

        // Update all merged rows
        mergedRows.forEach(row => {
            const mergedCell = row.querySelector('td:first-child');
            if (mergedCell) {
                mergedCell.colSpan = colspan;
            }
        });
    }

    // Initialize merged row colspan on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateMergedRowColspan();
    });
</script>
