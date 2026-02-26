<script>
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