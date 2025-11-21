document.addEventListener('DOMContentLoaded', function() {
    // Handle payment method deletion
    const removeButtons = document.querySelectorAll('.remove-payment-btn');
    removeButtons.forEach(button => {
        button.addEventListener('click', async function(e) {
            e.preventDefault();
            const paymentId = this.dataset.paymentId;
            
            if (confirm('Are you sure you want to remove this payment method?')) {
                try {
                    const response = await fetch('database/delete_payment_method.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ payment_method_id: paymentId })
                    });

                    const data = await response.json();
                    
                    if (data.success) {
                        // Remove the payment method element from the DOM
                        this.closest('.payment-method').remove();
                        
                        // Show success message
                        const alert = document.createElement('div');
                        alert.className = 'alert success';
                        alert.textContent = 'Payment method removed successfully';
                        document.querySelector('.payment-methods').prepend(alert);
                        
                        // Remove alert after 3 seconds
                        setTimeout(() => alert.remove(), 3000);
                    } else {
                        throw new Error(data.message || 'Failed to remove payment method');
                    }
                } catch (error) {
                    // Show error message
                    const alert = document.createElement('div');
                    alert.className = 'alert error';
                    alert.textContent = error.message;
                    document.querySelector('.payment-methods').prepend(alert);
                    
                    // Remove alert after 3 seconds
                    setTimeout(() => alert.remove(), 3000);
                }
            }
        });
    });

    // Handle section navigation
    const menuLinks = document.querySelectorAll('.profile-menu a');
    const sections = document.querySelectorAll('.profile-section');

    menuLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all links and sections
            menuLinks.forEach(l => l.classList.remove('active'));
            sections.forEach(s => s.classList.remove('active'));
            
            // Add active class to clicked link
            this.classList.add('active');
            
            // Show corresponding section
            const targetId = this.getAttribute('href').substring(1);
            const targetSection = document.querySelector(`[data-section="${targetId}"]`);
            if (targetSection) {
                targetSection.classList.add('active');
            }
        });
    });
});