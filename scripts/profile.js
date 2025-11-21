document.addEventListener('DOMContentLoaded', function() {
    // Initialize form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        const inputs = form.querySelectorAll('input[required], select[required]');
        inputs.forEach(input => addInputValidation(input));
    });

    // Handle menu navigation
    const menuLinks = document.querySelectorAll('.profile-menu a');
    const sections = document.querySelectorAll('.profile-section');

    // Function to switch sections with animation
    function switchSection(targetSection) {
        // Hide all sections first
        sections.forEach(section => {
            if (section !== targetSection) {
                section.style.display = 'none';
                section.classList.remove('active', 'fade-in', 'fade-out');
            }
        });

        // Show and animate target section
        targetSection.style.display = 'block';
        targetSection.classList.add('active', 'fade-in');
        
        // Remove animation class after transition
        setTimeout(() => {
            targetSection.classList.remove('fade-in');
        }, 300);
    }

    // Hide all sections initially except the first one
    sections.forEach((section, index) => {
        if (index === 0) {
            section.style.display = 'block';
            section.classList.add('active');
        } else {
            section.style.display = 'none';
            section.classList.remove('active');
        }
    });

    // Activate first menu item
    if (menuLinks.length > 0) {
        menuLinks[0].classList.add('active');
    }

    // Handle menu item clicks
    menuLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Update active menu item
            menuLinks.forEach(l => l.classList.remove('active'));
            this.classList.add('active');
            
            // Find and switch to corresponding section
            const targetId = this.getAttribute('href').substring(1);
            const targetSection = Array.from(sections).find(section => {
                const sectionTitle = section.querySelector('h2').textContent.toLowerCase();
                return sectionTitle.includes(targetId.replace('-', ' '));
            });
            
            if (targetSection) {
                switchSection(targetSection);
            }
        });
    });

    // Handle form state preservation
    sections.forEach(section => {
        const form = section.querySelector('form');
        if (form) {
            // Save form state before switching
            form.addEventListener('input', function(e) {
                const input = e.target;
                sessionStorage.setItem(`${form.id}_${input.name}`, input.value);
            });

            // Restore form state when section becomes active
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.target.classList.contains('active')) {
                        form.querySelectorAll('input, select').forEach(input => {
                            const savedValue = sessionStorage.getItem(`${form.id}_${input.name}`);
                            if (savedValue) input.value = savedValue;
                        });
                    }
                });
            });

            observer.observe(section, { attributes: true, attributeFilter: ['class'] });
        }
    });

    // Handle profile form submission
    const profileForm = document.getElementById('profile-form');
    if (profileForm) {
        profileForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitForm(this, 'update_profile.php');
        });
    }

    // Handle address form submission
    const addressForm = document.getElementById('address-form');
    if (addressForm) {
        addressForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitForm(this, 'update_address.php');
        });
    }

    // Handle view order details
    const viewOrderButtons = document.querySelectorAll('.view-order-btn');
    viewOrderButtons.forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.dataset.orderId;
            viewOrderDetails(orderId);
        });
    });

    // Handle add payment method
    const addPaymentBtn = document.querySelector('.add-payment-btn');
    if (addPaymentBtn) {
        addPaymentBtn.addEventListener('click', function() {
            // Redirect to payment method form
            window.location.href = 'database/add_payment_method.php';
        });
    }
});

// Function to show alert messages
function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert ${type}`;
    alertDiv.textContent = message;
    document.body.appendChild(alertDiv);

    // Remove alert after 3 seconds
    setTimeout(() => alertDiv.remove(), 3000);
}

// Function to validate form inputs
function validateForm(form) {
    const inputs = form.querySelectorAll('input[required], select[required]');
    let isValid = true;
    let firstInvalidInput = null;
    let errors = [];

    inputs.forEach(input => {
        const value = input.value.trim();
        input.classList.remove('invalid');

        if (!value) {
            isValid = false;
            input.classList.add('invalid');
            if (!firstInvalidInput) firstInvalidInput = input;
            errors.push(`${input.previousElementSibling.textContent} is required`);
        } else {
            // Specific validation rules
            switch(input.id) {
                case 'email':
                    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                        isValid = false;
                        input.classList.add('invalid');
                        errors.push('Please enter a valid email address');
                    }
                    break;
                case 'phone':
                    if (!/^\+?[0-9]{10,}$/.test(value.replace(/[\s-]/g, ''))) {
                        isValid = false;
                        input.classList.add('invalid');
                        errors.push('Please enter a valid phone number');
                    }
                    break;
                case 'postcode':
                    if (!/^[0-9]{4}\s?[A-Za-z]{2}$/.test(value)) {
                        isValid = false;
                        input.classList.add('invalid');
                        errors.push('Please enter a valid postcode (e.g., 1234 AB)');
                    }
                    break;
                case 'huisnummer':
                    if (!/^[0-9]+[A-Za-z]?$/.test(value)) {
                        isValid = false;
                        input.classList.add('invalid');
                        errors.push('Please enter a valid house number');
                    }
                    break;
            }
        }
    });

    if (!isValid) {
        if (firstInvalidInput) firstInvalidInput.focus();
        showAlert('error', errors.join('\n'));
    }

    return isValid;
}

// Add real-time validation feedback
function addInputValidation(input) {
    input.addEventListener('input', function() {
        validateInput(this);
    });

    input.addEventListener('blur', function() {
        validateInput(this, true);
    });
}

// Function to validate individual input
function validateInput(input, showError = false) {
    const value = input.value.trim();
    let isValid = true;
    let errorMessage = '';

    // Remove existing error message
    const existingError = input.parentElement.querySelector('.error-message');
    if (existingError) existingError.remove();

    if (!value && input.hasAttribute('required')) {
        isValid = false;
        errorMessage = `${input.previousElementSibling.textContent} is required`;
    } else if (value) {
        switch(input.id) {
            case 'email':
                if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                    isValid = false;
                    errorMessage = 'Please enter a valid email address';
                }
                break;
            case 'phone':
                if (!/^\+?[0-9]{10,}$/.test(value.replace(/[\s-]/g, ''))) {
                    isValid = false;
                    errorMessage = 'Please enter a valid phone number';
                }
                break;
            case 'postcode':
                if (!/^[0-9]{4}\s?[A-Za-z]{2}$/.test(value)) {
                    isValid = false;
                    errorMessage = 'Please enter a valid postcode (e.g., 1234 AB)';
                }
                break;
            case 'huisnummer':
                if (!/^[0-9]+[A-Za-z]?$/.test(value)) {
                    isValid = false;
                    errorMessage = 'Please enter a valid house number';
                }
                break;
        }
    }

    input.classList.toggle('invalid', !isValid);
    
    if (!isValid && showError) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.textContent = errorMessage;
        input.parentElement.appendChild(errorDiv);
    }

    return isValid;
}

// Function to handle form submissions
async function submitForm(form, endpoint) {
    try {
        const formData = new FormData(form);
        const submitButton = form.querySelector('button[type="submit"]');
        const inputs = form.querySelectorAll('input[required], select[required]');
        let isValid = true;

        // Clear previous error messages
        form.querySelectorAll('.error-message').forEach(error => error.remove());

        // Validate all inputs before submission
        inputs.forEach(input => {
            if (!validateInput(input, true)) {
                isValid = false;
            }
        });

        if (!isValid) {
            showAlert('error', 'Please correct the errors before submitting');
            return;
        }

        // Show loading state
        submitButton.disabled = true;
        const originalText = submitButton.textContent;
        submitButton.innerHTML = '<span class="loading-spinner"></span> Updating...';
        form.classList.add('submitting');

        // Send form data
        const response = await fetch(`database/${endpoint}`, {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();
        
        // Show success/error message
        if (result.success) {
            showAlert('success', result.message);
            // Reload page after successful update
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showAlert('error', result.message || 'An error occurred');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('error', 'An error occurred while processing your request');
    } finally {
        const submitButton = form.querySelector('button[type="submit"]');
        submitButton.disabled = false;
        submitButton.textContent = 'Update Information';
    }
}

// Function to view order details
async function viewOrderDetails(orderId) {
    try {
        const response = await fetch(`database/get_order_details.php?order_id=${orderId}`);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const orderDetails = await response.json();
        // Display order details in a modal or new page
        window.location.href = `order_details.php?order_id=${orderId}`;
    } catch (error) {
        console.error('Error:', error);
        showAlert('error', 'Failed to load order details');
    }
}

// Function to show alerts
function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.textContent = message;

    // Insert alert at the top of the main content
    const mainContent = document.querySelector('.profile-content');
    mainContent.insertBefore(alertDiv, mainContent.firstChild);

    // Remove alert after 3 seconds
    setTimeout(() => alertDiv.remove(), 3000);
}