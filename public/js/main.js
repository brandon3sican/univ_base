// Ripple Effect for Buttons
document.addEventListener('DOMContentLoaded', function() {
    // Ripple effect for buttons
    const buttons = document.querySelectorAll('.btn-ripple');
    
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            // Create ripple element
            const ripple = document.createElement('span');
            ripple.classList.add('ripple');
            
            // Get position of the button
            const rect = button.getBoundingClientRect();
            
            // Set position of ripple
            const size = Math.max(rect.width, rect.height);
            ripple.style.width = ripple.style.height = `${size}px`;
            
            // Calculate position to center the ripple effect
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.left = `${x}px`;
            ripple.style.top = `${y}px`;
            
            // Add ripple to button
            this.appendChild(ripple);
            
            // Remove ripple after animation completes
            ripple.addEventListener('animationend', () => {
                ripple.remove();
            });
        });
    });

    // Toggle FY Columns functionality
    const toggleButton = document.getElementById('toggleFyColumns');
    if (toggleButton) {
        const fyColumns = document.querySelectorAll('.fy-column');
        
        toggleButton.addEventListener('click', function() {
            fyColumns.forEach(column => {
                column.classList.toggle('hidden');
            });
            
            // Toggle button text based on visibility
            const isHidden = fyColumns[0]?.classList.contains('hidden');
            if (isHidden) {
                toggleButton.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                Show FY Columns`;
            } else {
                toggleButton.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                Hide FY Columns`;
            }
        });
    }
});
