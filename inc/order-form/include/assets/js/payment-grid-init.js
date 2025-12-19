document.addEventListener('DOMContentLoaded', function () {
    const paymentMethods = document.querySelector('.codplugin-payment-methods');
    if (paymentMethods) {
        // Get settings from PHP-passed payment_grid_settings object
        const columns = (typeof payment_grid_settings !== 'undefined' && payment_grid_settings && payment_grid_settings.columns) ? payment_grid_settings.columns : 4;
        
        console.log('Initializing payment grid with columns:', columns);

        // Always use grid layout - no carousel mode
        paymentMethods.classList.add('payment-grid-layout');
        
        // Calculate and apply responsive CSS custom properties for grid
        const gridColumns = columns;
        const gapSize = 15; // Consistent gap size
        
        paymentMethods.style.display = 'grid';
        paymentMethods.style.gridTemplateColumns = `repeat(${gridColumns}, 1fr)`;
        paymentMethods.style.gap = `${gapSize}px`;
        
        // Add responsive breakpoints for grid
        function updateGridColumns() {
            if (window.innerWidth <= 600) {
                paymentMethods.style.gridTemplateColumns = `repeat(${Math.max(1, gridColumns - 1)}, 1fr)`;
                paymentMethods.style.gap = `${gapSize}px`;
            } else if (window.innerWidth <= 900) {
                paymentMethods.style.gridTemplateColumns = `repeat(${Math.min(gridColumns, 2)}, 1fr)`;
                paymentMethods.style.gap = `${gapSize}px`;
            } else {
                paymentMethods.style.gridTemplateColumns = `repeat(${gridColumns}, 1fr)`;
                paymentMethods.style.gap = `${gapSize}px`;
            }
        }
        
        // Initial responsive setup
        updateGridColumns();
        
        // Add resize listener for responsive grid
        window.addEventListener('resize', function() {
            updateGridColumns();
        });
        
        // Add click handler to payment options for better UX
        const paymentOptions = paymentMethods.querySelectorAll('.payment-option');
        paymentOptions.forEach(option => {
            option.addEventListener('click', function(e) {
                // Prevent clicking on the radio input from triggering the label twice
                if (e.target.tagName !== 'INPUT') {
                    const radioInput = option.querySelector('.payment-option-input');
                    if (radioInput) {
                        radioInput.checked = true;
                        radioInput.dispatchEvent(new Event('change'));
                    }
                }
            });
        });
    }
});
