document.addEventListener('DOMContentLoaded', function() {
    // Form elements
    const loginForm = document.querySelector('form');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const rememberMe = document.getElementById('remember-me');
    const loginBtn = document.querySelector('.btn-login');

    // Check if we're on the login page before running login-specific code
    if (!loginBtn || !emailInput || !passwordInput) {
        return; // Exit if not on login page
    }

    // Add ripple effect to the login button
    function createRipple(event) {
        const button = event.currentTarget;
        const circle = document.createElement('span');
        const diameter = Math.max(button.clientWidth, button.clientHeight);
        const radius = diameter / 2;

        circle.style.width = circle.style.height = `${diameter}px`;
        circle.style.left = `${event.clientX - button.getBoundingClientRect().left - radius}px`;
        circle.style.top = `${event.clientY - button.getBoundingClientRect().top - radius}px`;
        circle.classList.add('ripple');

        const ripple = button.getElementsByClassName('ripple')[0];
        if (ripple) {
            ripple.remove();
        }

        button.appendChild(circle);
    }

    // Add ripple effect to the login button
    loginBtn.addEventListener('click', createRipple);

    // Form validation
    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(String(email).toLowerCase());
    }

    function validateForm() {
        let isValid = true;

        // Email validation
        if (!validateEmail(emailInput.value)) {
            emailInput.classList.add('border-red-500');
            isValid = false;
        } else {
            emailInput.classList.remove('border-red-500');
        }

        // Password validation
        if (passwordInput.value.length < 6) {
            passwordInput.classList.add('border-red-500');
            isValid = false;
        } else {
            passwordInput.classList.remove('border-red-500');
        }

        return isValid;
    }

    // Form submission
    loginForm.addEventListener('submit', function(e) {
        if (!validateForm()) {
            e.preventDefault();
            return false;
        }

        // Show loading state
        loginBtn.disabled = true;
        loginBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Signing in...';

        // Allow form to submit naturally
        // The browser will handle the redirect based on the server response
    });

    // Input field focus effects
    const inputs = document.querySelectorAll('input');
    inputs.forEach(input => {
        // Add focus class
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('ring-2', 'ring-green-400', 'rounded-lg');
        });

        // Remove focus class
        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('ring-2', 'ring-green-400', 'rounded-lg');
        });
    });

    // Toggle password visibility
    const togglePassword = document.createElement('span');
    togglePassword.className = 'absolute right-3 top-1/2 transform -translate-y-1/2 cursor-pointer text-gray-400 hover:text-gray-600';
    togglePassword.innerHTML = '<i class="far fa-eye"></i>';
    passwordInput.parentNode.appendChild(togglePassword);

    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.innerHTML = type === 'password' ? '<i class="far fa-eye"></i>' : '<i class="far fa-eye-slash"></i>';
    });

    // Check for saved login
    if (localStorage.getItem('rememberMe') === 'true') {
        rememberMe.checked = true;
        emailInput.value = localStorage.getItem('email') || '';
        passwordInput.value = localStorage.getItem('password') || '';
    }
});