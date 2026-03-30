<script>
    function toggleTargetColumns() {
        const targetHeader = document.getElementById('targetHeader');
        const toggleBtn = document.getElementById('toggleTargetBtn');
        const toggleText = document.getElementById('toggleTargetText');
        const toggleIcon = toggleBtn.querySelector('i');

        // Toggle parent header
        targetHeader.classList.toggle('hidden');

        // Toggle all year sub-headers (second row) - specifically target target headers
        const allTargetYearHeaders = document.querySelectorAll('tr:nth-child(2) th');
        allTargetYearHeaders.forEach(header => {
            // Only toggle if it's a target year header (contains a number or "No Targets")
            const headerText = header.textContent.trim();
            if (/^\d{4}$/.test(headerText) || headerText === 'No Targets') {
                header.classList.toggle('hidden');
            }
        });

        // Toggle all target data cells
        const allDataRows = document.querySelectorAll('tbody tr');
        allDataRows.forEach((row, rowIndex) => {
            const cells = row.querySelectorAll('td');
            const isMergedRow = row.hasAttribute('data-merged-row');
            
            if (!isMergedRow) {
                // Find target columns by looking for target year content
                const targetColspan = parseInt(targetHeader.getAttribute('colspan') || '2');
                let targetColumnsToggled = 0;
                
                for (let i = 0; i < cells.length; i++) {
                    const cell = cells[i];
                    const cellContent = cell.textContent.trim();
                    
                    // Skip first 6 columns (base columns)
                    if (i < 6) continue;
                    
                    // Skip last column (Actions column) - explicit check
                    if (i === cells.length - 1) continue;
                    
                    // Check if this cell looks like a target cell (has year content or is empty target cell)
                    // More specific check: target cells should be in the target column range
                    const targetColStart = 6;
                    const targetColEnd = targetColStart + targetColspan - 1;
                    const isInTargetRange = i >= targetColStart && i <= targetColEnd;
                    
                    // More inclusive target cell detection
                    const looksLikeTargetCell = isInTargetRange && (
                        cellContent === '-' || 
                        cellContent === '0' ||
                        cellContent === '' || 
                        /^202[7-9]/.test(cellContent) ||
                        /^\d+$/.test(cellContent) ||  // Any number
                        !cellContent.match(/edit|delete|view/i)  // Not an action button
                    );
                    
                    if (looksLikeTargetCell) {
                        cell.classList.toggle('hidden');
                        targetColumnsToggled++;
                    }
                }
            }
        });

        // Update merged row colspan
        updateMergedRowColspan();

        // Update button text and icon
        if (targetHeader.classList.contains('hidden')) {
            toggleText.textContent = 'Show Targets';
            toggleIcon.className = 'fas fa-bullseye mr-2';
        } else {
            toggleText.textContent = 'Hide Targets';
            toggleIcon.className = 'fas fa-bullseye-slash mr-2';
        }
    }

    function toggleRemarksColumn() {
        const remarksHeader = document.getElementById('remarksHeader');
        const toggleBtn = document.getElementById('toggleRemarksBtn');
        const toggleText = document.getElementById('toggleRemarksText');
        const toggleIcon = document.getElementById('toggleRemarksBtn').querySelector('i');

        // Toggle header
        remarksHeader.classList.toggle('hidden');

        // Toggle remarks cells - use fixed position approach
        const allDataRows = document.querySelectorAll('tbody tr');
        allDataRows.forEach((row, rowIndex) => {
            const cells = row.querySelectorAll('td');
            const isMergedRow = row.hasAttribute('data-merged-row');
            
            if (!isMergedRow) {
                // Remarks column is ALWAYS at position 8, regardless of target visibility
                // Column structure: 0-5 (base), 6-7 (targets), 8 (remarks), 9 (actions)
                const remarksColumnIndex = 8;
                
                if (cells[remarksColumnIndex]) {
                    const cellContent = cells[remarksColumnIndex].textContent.trim();
                    cells[remarksColumnIndex].classList.toggle('hidden');
                }
            }
        });

        // Update merged row colspan
        updateMergedRowColspan();

        // Update button text and icon
        if (remarksHeader.classList.contains('hidden')) {
            toggleText.textContent = 'Show Remarks';
            toggleIcon.className = 'fas fa-comment mr-2';
        } else {
            toggleText.textContent = 'Hide Remarks';
            toggleIcon.className = 'fas fa-comment-slash mr-2';
        }
    }

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