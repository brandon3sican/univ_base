<script>
    function toggleTargetColumns() {
        const targetHeader = document.getElementById('targetHeader');
        const toggleBtn = document.getElementById('toggleTargetBtn');
        const toggleText = document.getElementById('toggleTargetText');
        const toggleIcon = toggleBtn.querySelector('i');

        // Toggle parent header
        targetHeader.classList.toggle('hidden');

        // Toggle all year sub-headers (second row)
        const yearHeaders = document.querySelectorAll('tr:nth-child(2) th');
        yearHeaders.forEach(header => {
            header.classList.toggle('hidden');
        });

        // Toggle all target data cells (columns 6-10)
        const targetCells = document.querySelectorAll('td:nth-child(n+6):nth-child(-n+10)');
        targetCells.forEach(cell => {
            cell.classList.toggle('hidden');
        });

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
        const toggleIcon = toggleBtn.querySelector('i');

        // Toggle header
        remarksHeader.classList.toggle('hidden');

        // Toggle all remarks cells
        const remarksCells = document.querySelectorAll('[id^="remarksCell-"]');
        remarksCells.forEach(cell => {
            cell.classList.toggle('hidden');
        });

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

        // Toggle all action cells
        const actionCells = document.querySelectorAll('[id^="actionsCell-"]');
        actionCells.forEach(cell => {
            cell.classList.toggle('hidden');
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
</script>