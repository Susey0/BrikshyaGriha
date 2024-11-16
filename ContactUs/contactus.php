<?php
session_start();
include '../config.php';
if (isset($_SESSION['login_error'])) {
    $loginError = $_SESSION['login_error'];
    unset($_SESSION['login_error']); // Clear the error after displaying it
} else {
    $loginError = '';
}
?>

<?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
    <div class="alert alert-success" role="alert">
        Your message has been sent successfully!
    </div>
<?php endif; ?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Brikshya Griha</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel = "stylesheet" href="../css/contactus.css">
    <link rel="stylesheet" href="fontawesome-free-6.6.0-web/css/all.min.css">

</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">
            <img src="../images/logo.jpg" alt="Brikshya Griha Logo" width="30" height="30"> Brikshya Griha
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link active" href="../index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="../aboutus.php">About Us</a></li>
                <li class="nav-item"><a class="nav-link" href="../products.php">Products</a></li>
                <li class="nav-item"><a class="nav-link" href="ContactUs/contactus.php">Contact Us</a></li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="#" id="loginLink" data-toggle="modal"
                        data-target="#loginModal">Login</a></li>
                <li class="nav-item"><a class="nav-link" href="#" id="signupLink" data-toggle="modal"
                        data-target="#signupModal">Signup</a></li>
            </ul>
        </div>
    </nav>
     <!-- Login Modal -->
     <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalLabel">Login</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <!-- Display error message if present -->
                <?php if ($loginError): ?>
                    <div class="alert alert-danger">
                        <?php echo $loginError; ?>
                    </div>
                <?php endif; ?>
                <div class="modal-body">
                    <div id="loginSuccessMessage" class="alert alert-success" style="display: none;"></div>

                    <form action="../login.php" method="POST">
                        <div class="form-group">
                            <label for="username">Email:</label>
                            <input type="text" name="email" id="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password:</label>
                            <div class="input-group">
                                <input type="password" name="password" id="password" class="form-control" required>
                                <div class="input-group-append">
                                    <span class="input-group-text" id="toggleLoginPassword">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                            </div>

                        </div>

                        <button type="submit" class="btn btn-primary">Login</button>
                        <div class="form-group text-center">
                            <p>Don't have an account? <a href="#" data-dismiss="modal" data-toggle="modal"
                                    data-target="#signupModal">Signup</a></p>
                            <p><a href="#" data-toggle="modal" data-target="#forgotPasswordModal">Forgot Password?</a>
                            </p>

                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Automatically open the modal if there's an error -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        window.onload = function () {
            if ("<?php echo $loginError; ?>") {
                $('#loginModal').modal('show');
            }
        };
        // When clicking on the "Signup" link, show the signup modal
        $(document).on('click', '[data-toggle="modal"]', function (event) {
            event.preventDefault(); // Prevent default anchor click behavior
            var targetModal = $(this).data('target');
            $(targetModal).modal('show'); // Show the target modal
        });
    </script>

    <!-- Forgot Password Modal -->
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="forgotPasswordForm" action="forgot_password.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="forgotPasswordModalLabel">Forgot Password</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="fpSuccessMessage" class="alert alert-success" style="display: none;"></div>
                        <div id="fpErrorMessage" class="alert alert-danger" style="display: none;"></div>

                        <div class="form-group">
                            <label for="fpEmail">Email address</label>
                            <input type="email" class="form-control" id="fpEmail" name="email" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Send Reset Link</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        function handleShopClick(categoryIndex) {
            if (!isLoggedIn) {
                // Show the login modal if not logged in
                $('#loginModal').modal('show');
                // Add a message to the login modal to inform the user that they need to login to shop
                document.getElementById('loginSuccessMessage').textContent = 'You must login first to shop.';
                document.getElementById('loginSuccessMessage').style.display = 'block';
                // Clear any existing error messages
                document.getElementById('loginError').innerHTML = '';
            } else {
                // Redirect to the shopping page or category page
                window.location.href = 'shop.php?category=' + categoryIndex;
            }
        }
    </script>


    <!-- Register Modal -->
    <div class="modal fade" id="signupModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="signupForm" action="signup.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="registerModalLabel">Sign Up</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="successMessage" class="alert alert-success" style="display: none;"></div>

                        <div class="form-group">
                            <label for="registerFirstName">First Name</label>
                            <input type="text" class="form-control" id="registerFirstName" name="first_name" required>
                            <div class="error" id="errorFirstName"></div>
                        </div>
                        <div class="form-group">
                            <label for="registerLastName">Last Name</label>
                            <input type="text" class="form-control" id="registerLastName" name="last_name" required>
                            <div class="error" id="errorLastName"></div>
                        </div>
                        <div class="form-group">
                            <label for="registerUsername">Username</label>
                            <input type="text" class="form-control" id="registerUsername" name="username" required>
                            <div class="error" id="errorUsername"></div>
                        </div>
                        <div class="form-group">
                            <label for="registerEmail">Email address</label>
                            <input type="email" class="form-control" id="registerEmail" name="email" required>
                            <div class="error" id="errorEmail"></div>
                        </div>
                        <div class="form-group">
                            <label for="registerPassword">Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="registerPassword" name="password"
                                    required>
                                <div class="input-group-append">
                                    <span class="input-group-text" id="toggleSignupPassword">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="error" id="errorPassword"></div>
                            <small class="form-text text-muted">
                                Password must be at least 8 characters long, contain one uppercase letter, one lowercase
                                letter, one number, and one special character.
                            </small>
                        </div>
                        <div class="form-group">
                            <label for="registerConfirmPassword">Confirm Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="registerConfirmPassword"
                                    name="confirm_password" required>
                                <div class="input-group-append">
                                    <span class="input-group-text" id="toggleConfirmPassword">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="error" id="errorConfirmPassword"></div>
                        </div>
                        <div class="form-group">
                            <label for="registerPhone">Phone Number</label>
                            <input type="tel" class="form-control" id="registerPhone" name="phone" required>
                            <div class="error" id="errorPhone"></div>
                        </div>
                        <div class="form-group">
                            <label for="registerAddress">Address</label>
                            <input type="text" class="form-control" id="registerAddress" name="address" required>
                            <div class="error" id="errorAddress"></div>
                        </div>
                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="termsCheck" name="terms" required>
                                <label class="form-check-label" for="termsCheck">
                                    I agree to the <a href="terms.php">terms and conditions</a>.
                                </label>
                                <div class="error" id="errorTerms"></div>
                            </div>
                        </div>
                        <div class="form-group text-center">
                            <p>Already have an account? <a href="#" data-dismiss="modal" data-toggle="modal"
                                    data-target="#loginModal">Login</a></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Sign Up</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('toggleLoginPassword').addEventListener('click', function () {
            const passwordInput = document.getElementById('password');
            const passwordIcon = this.querySelector('i');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('fa-eye');
                passwordIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('fa-eye-slash');
                passwordIcon.classList.add('fa-eye');
            }
        });

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
        document.getElementById('signupForm').addEventListener('submit', function (e) {
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

<div class="container mt-5">
    <h2>Contact Us</h2>
    <form action="submit_contact.php" method="post">
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="subject">Subject</label>
            <input type="text" class="form-control" id="subject" name="subject" required>
        </div>
        <div class="form-group">
            <label for="message">Message</label>
            <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>

<!-- Include your footer here -->

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
