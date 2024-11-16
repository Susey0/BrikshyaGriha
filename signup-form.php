<form id="signupForm" action="signup.php" method="POST">
    <div class="form-group">
        <label for="registerFirstName">First Name</label>
        <input type="text" class="form-control" id="registerFirstName" name="first_name" required>
    </div>
    <div class="form-group">
        <label for="registerLastName">Last Name</label>
        <input type="text" class="form-control" id="registerLastName" name="last_name" required>
    </div>
    <div class="form-group">
        <label for="registerUsername">Username</label>
        <input type="text" class="form-control" id="registerUsername" name="username" required>
    </div>
    <div class="form-group">
        <label for="registerEmail">Email address</label>
        <input type="email" class="form-control" id="registerEmail" name="email" required>
    </div>
    <div class="form-group">
        <label for="registerPassword">Password</label>
        <div class="input-group">
            <input type="password" class="form-control" id="registerPassword" name="password" required>
            <div class="input-group-append">
                <span class="input-group-text" id="toggleSignupPassword">
                    <i class="fas fa-eye "></i>
                </span>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label for="registerConfirmPassword">Confirm Password</label>
        <div class="input-group">
            <input type="password" class="form-control" id="registerConfirmPassword" name="confirm_password" required>
            <div class="input-group-append">
                <span class="input-group-text" id="toggleConfirmPassword">
                    <i class="fas fa-eye"></i>
                </span>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label for="registerPhone">Phone Number</label>
        <input type="tel" class="form-control" id="registerPhone" name="phone" required>
    </div>
    <div class="form-group">
        <label for="registerAddress">Address</label>
        <input type="text" class="form-control" id="registerAddress" name="address" required>
    </div>
    <div class="form-group">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="termsCheck" name="terms" required>
            <label class="form-check-label" for="termsCheck">
                I agree to the <a href="terms.php">terms and conditions</a>.
            </label>
        </div>
    </div>
    
    <button type="submit" class="btn btn-primary">Sign Up</button>
</form>
<script>
    document.getElementById('toggleSignupPassword').addEventListener('click', function () {
        const signupPasswordInput = document.getElementById('registerPassword');
        const signupPasswordIcon = this.querySelector('i');

        if (signupPasswordInput.type === 'password') {
            signupPasswordInput.type = 'text';
            signupPasswordIcon.classList.remove('fa-eye');
            signupPasswordIcon.classList.add('fa-eye-slash');
        } else {
            signupPasswordInput.type = 'password';
            signupPasswordIcon.classList.remove('fa-eye-slash');
            signupPasswordIcon.classList.add('fa-eye');
        }
    });

    document.getElementById('toggleConfirmPassword').addEventListener('click', function () {
        const confirmPasswordInput = document.getElementById('registerConfirmPassword');
        const confirmPasswordIcon = this.querySelector('i');

        if (confirmPasswordInput.type === 'password') {
            confirmPasswordInput.type = 'text';
            confirmPasswordIcon.classList.remove('fa-eye');
            confirmPasswordIcon.classList.add('fa-eye-slash');
        } else {
            confirmPasswordInput.type = 'password';
            confirmPasswordIcon.classList.remove('fa-eye-slash');
            confirmPasswordIcon.classList.add('fa-eye');
        }
    });
</script>


<script>
// Password validation function for individual rules
function validatePassword(password) {
    const errors = [];
    if (password.length < 8) {
        errors.push("Password must be at least 8 characters long.");
    }
    if (!/[A-Z]/.test(password)) {
        errors.push("Password must contain at least one uppercase letter.");
    }
    if (!/[a-z]/.test(password)) {
        errors.push("Password must contain at least one lowercase letter.");
    }
    if (!/\d/.test(password)) {
        errors.push("Password must contain at least one number.");
    }
    if (!/[@$!%*?&]/.test(password)) {
        errors.push("Password must contain at least one special character.");
    }
    return errors;
}

// Form validation on submit
document.getElementById('signupForm').addEventListener('submit', function(e) {
    // Get form elements
    const password = document.getElementById('registerPassword').value;
    const confirmPassword = document.getElementById('registerConfirmPassword').value;
    const errorPassword = document.getElementById('errorPassword');
    const errorConfirmPassword = document.getElementById('errorConfirmPassword');

    // Clear previous error messages
    errorPassword.innerHTML = '';
    errorConfirmPassword.textContent = '';

    let valid = true;

    // Validate password
    const passwordErrors = validatePassword(password);
    if (passwordErrors.length > 0) {
        valid = false;
        // Display password errors
        errorPassword.innerHTML = passwordErrors.map(err => `<p>${err}</p>`).join('');
    }

    // Check if passwords match
    if (password !== confirmPassword) {
        errorConfirmPassword.textContent = 'Passwords do not match.';
        valid = false;
    }

    // Prevent form submission if validation fails
    if (!valid) {
        e.preventDefault();
    }
});
</script>


<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('signupForm');

    // Check for success message
    const successMessage = '<?php echo isset($_SESSION["success_message"]) ? $_SESSION["success_message"] : ""; ?>';
    if (successMessage) {
        // Display the success message in the login modal
        document.getElementById('loginSuccessMessage').textContent = successMessage;
        document.getElementById('loginSuccessMessage').style.display = 'block';
        
        // Show the login modal
        $('#loginModal').modal('show');

        // Clear the session variable
        <?php unset($_SESSION['success_message']); ?>
    }

    form.addEventListener('submit', function (event) {
        // Clear previous error messages
        document.querySelectorAll('.error').forEach(el => el.textContent = '');

        const errors = validateForm(); // Get validation errors

        if (errors.length > 0) {
            // Display errors
            errors.forEach(error => {
                const errorElement = document.getElementById(`error${error.field}`);
                if (errorElement) {
                    errorElement.textContent = error.message;
                }
            });

            // Prevent form submission and keep the modal open
            event.preventDefault();
            $('#signupModal').modal('show'); // Show the modal if it closes
        } else {
            // If no errors, submit the form
            form.submit();
        }
    });

    function validateForm() {
        const errors = [];

        // First Name
        const firstName = document.getElementById('registerFirstName').value.trim();
        if (!firstName) {
            errors.push({ field: 'FirstName', message: 'First Name is required.' });
        }

        // Last Name
        const lastName = document.getElementById('registerLastName').value.trim();
        if (!lastName) {
            errors.push({ field: 'LastName', message: 'Last Name is required.' });
        }

        // Username
        const username = document.getElementById('registerUsername').value.trim();
        if (!username) {
            errors.push({ field: 'Username', message: 'Username is required.' });
        }

        // Email
        const email = document.getElementById('registerEmail').value.trim();
        if (!email || !/\S+@\S+\.\S+/.test(email)) {
            errors.push({ field: 'Email', message: 'Valid email is required.' });
        }

        // Password
    const password = document.getElementById('registerPassword').value;
    const passwordErrors = validatePassword(password);
    if (passwordErrors.length > 0) {
        passwordErrors.forEach(err => {
            errors.push({ field: 'Password', message: err });
        });
    }


        // Confirm Password
        const confirmPassword = document.getElementById('registerConfirmPassword').value;
        if (password !== confirmPassword) {
            errors.push({ field: 'ConfirmPassword', message: 'Passwords do not match.' });
        }

        // Phone
        const phone = document.getElementById('registerPhone').value.trim();
        if (!phone) {
            errors.push({ field: 'Phone', message: 'Phone Number is required.' });
        }

        // Address
        const address = document.getElementById('registerAddress').value.trim();
        if (!address) {
            errors.push({ field: 'Address', message: 'Address is required.' });
        }

        // Terms
        const terms = document.getElementById('termsCheck').checked;
        if (!terms) {
            errors.push({ field: 'Terms', message: 'You must agree to the terms and conditions.' });
        }

        return errors;
    }
});

</script>