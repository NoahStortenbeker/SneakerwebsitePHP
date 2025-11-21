document.addEventListener("DOMContentLoaded", function () {
  // DOM Elements
  const signupForm = document.getElementById("signup-form");
  const loginLink = document.querySelector(".login-link a");
  const signupContainer = document.querySelector(".signup-form-container");
  const formTitle = document.getElementById("form-title");

  // Create login form container and elements if they don't exist
  if (!document.querySelector(".login-form-container")) {
    // Create login form container
    const loginContainer = document.createElement("div");
    loginContainer.className = "login-form-container";
    loginContainer.style.display = "none";

    // Create login form HTML
    loginContainer.innerHTML = `
            <form id="login-form" class="login-form">
                <div class="form-group">
                    <label for="login-email">Email Address *</label>
                    <input type="email" id="login-email" name="login-email" required>
                </div>
                <div class="form-group">
                    <label for="login-password">Password *</label>
                    <input type="password" id="login-password" name="login-password" required>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn login-btn">Log In</button>
                    <p class="signup-link">Don't have an account? <a href="#" class="show-signup">Sign up</a></p>
                    <p class="forgot-password"><a href="#">Forgot your password?</a></p>
                </div>
            </form>
        `;

    // Insert login container after signup container
    signupContainer.parentNode.insertBefore(
      loginContainer,
      signupContainer.nextSibling
    );
  }

  // Get the login container and show signup link
  const loginContainer = document.querySelector(".login-form-container");
  const showSignupLink = document.querySelector(".show-signup");
  const loginForm = document.getElementById("login-form");

  // Function to switch to login form
  function showLoginForm() {
    // Change the title to Welcome Back
    if (formTitle) {
      formTitle.textContent = "Welcome Back";
    }

    // Add fade out class to signup form
    signupContainer.classList.add("form-fade");

    // After animation completes, hide signup and show login
    setTimeout(() => {
      signupContainer.style.display = "none";
      loginContainer.style.display = "block";
      loginContainer.classList.add("form-fade");

      // Trigger reflow
      void loginContainer.offsetWidth;

      // Add fade in class
      loginContainer.classList.add("form-fade-in");
    }, 500);
  }

  // Function to switch to signup form
  function showSignupForm() {
    // Change the title back to Create Account
    if (formTitle) {
      formTitle.textContent = "Create Account";
    }

    // Add fade out class to login form
    loginContainer.classList.remove("form-fade-in");

    // After animation completes, hide login and show signup
    setTimeout(() => {
      loginContainer.style.display = "none";
      signupContainer.style.display = "block";
      signupContainer.classList.add("form-fade");

      // Trigger reflow
      void signupContainer.offsetWidth;

      // Add fade in class
      signupContainer.classList.remove("form-fade");
    }, 500);
  }

  // Event Listeners
  if (loginLink) {
    loginLink.addEventListener("click", function (e) {
      e.preventDefault();
      showLoginForm();
    });
  }

  if (showSignupLink) {
    showSignupLink.addEventListener("click", function (e) {
      e.preventDefault();
      showSignupForm();
    });
  }

  // Function to show alert message
  function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.textContent = message;
    document.body.insertBefore(alertDiv, document.body.firstChild);
    
    setTimeout(() => {
      alertDiv.remove();
    }, 3000);
  }

  // Form Validation
  if (signupForm) {
    signupForm.addEventListener("submit", async function (e) {
      e.preventDefault();

      try {
        const formData = new FormData(this);
        const response = await fetch('database/register.php', {
          method: 'POST',
          body: formData
        });

        const result = await response.json();
        
        if (result.success) {
          showAlert(result.message, 'success');
          setTimeout(() => {
            showLoginForm();
          }, 1500);
        } else {
          showAlert(result.message || 'Registration failed', 'error');
        }
      } catch (error) {
        showAlert('An error occurred during registration', 'error');
      }
    });
  }

  // Login form validation
  if (loginForm) {
    loginForm.addEventListener("submit", async function (e) {
      e.preventDefault();

      try {
        const formData = new FormData(this);
        const response = await fetch('database/login.php', {
          method: 'POST',
          body: formData
        });

        const result = await response.json();
        
        if (result.success) {
          showAlert(result.message, 'success');
          setTimeout(() => {
            window.location.href = result.redirect;
          }, 1500);
        } else {
          showAlert(result.message || 'Login failed', 'error');
        }
      } catch (error) {
        showAlert('An error occurred during login', 'error');
      }
    });
  }
});
