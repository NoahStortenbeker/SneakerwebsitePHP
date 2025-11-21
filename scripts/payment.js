document.addEventListener('DOMContentLoaded', function() {
    const paymentOptions = document.querySelectorAll('.payment-option');
    const paymentTypeInput = document.getElementById('payment_type');
    const creditCardFields = document.getElementById('credit-card-fields');
    const idealFields = document.getElementById('ideal-fields');

    // Format expiry date input
    const expiryInput = document.getElementById('card_expiry');
    if (expiryInput) {
        expiryInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\/D/g, '');
            if (value.length >= 2) {
                value = value.slice(0,2) + '/' + value.slice(2);
            }
            e.target.value = value;
        });
    }

    // Format card number input
    const cardInput = document.getElementById('card_number');
    if (cardInput) {
        cardInput.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/\D/g, '');
        });
    }

    // Format CVV input
    const cvvInput = document.getElementById('card_cvv');
    if (cvvInput) {
        cvvInput.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/\D/g, '');
        });
    }

    // Handle payment method selection
    paymentOptions.forEach(option => {
        option.addEventListener('click', function() {
            // Remove active class from all options
            paymentOptions.forEach(opt => opt.classList.remove('active'));
            
            // Add active class to selected option
            this.classList.add('active');
            
            // Set payment type
            const paymentType = this.dataset.type;
            paymentTypeInput.value = paymentType;
            
            // Show/hide appropriate fields
            creditCardFields.classList.remove('active');
            idealFields.classList.remove('active');
            
            if (paymentType === 'credit_card') {
                creditCardFields.classList.add('active');
                enableRequiredFields(creditCardFields);
                disableRequiredFields(idealFields);
            } else if (paymentType === 'ideal') {
                idealFields.classList.add('active');
                enableRequiredFields(idealFields);
                disableRequiredFields(creditCardFields);
            }
        });
    });

    function enableRequiredFields(container) {
        container.querySelectorAll('input, select').forEach(field => {
            field.required = true;
        });
    }

    function disableRequiredFields(container) {
        container.querySelectorAll('input, select').forEach(field => {
            field.required = false;
        });
    }
});