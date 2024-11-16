<?php
session_start();
include 'config.php';

$_SESSION['success_message'] = 'You have successfully registered. Now you can log in.';


$_SESSION['errors'] = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data
    $first_name = $_POST['first_name'] ?? null;
    $last_name = $_POST['last_name'] ?? null;
    $username = $_POST['username'] ?? null;
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;
    $phone = $_POST['phone'] ?? null;
    $address = $_POST['address'] ?? null;

    // Input validation
    if (empty($first_name) || empty($last_name) || empty($username) || empty($email) || empty($password) || empty($phone) || empty($address)) {
        $_SESSION['errors'][] = "All fields are required.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['errors'][] = "Invalid email format.";
    }

    if (strlen($password) < 8) {
        $_SESSION['errors'][] = "Password must be at least 8 characters long.";
    }

    if (!preg_match("/^[0-9]{10}$/", $phone)) {
        $_SESSION['errors'][] = "Invalid phone number format. It should be 10 digits.";
    }

    // Check if username or email already exists
    $sql = "SELECT * FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        $_SESSION['errors'][] = "Error preparing statement: " . $conn->error;
        header('Location: index.php');
        exit;
    }

    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['errors'][] = "Username or email already exists.";
        $stmt->close();
        header('Location: index.php');
        exit;
    }

    $stmt->close();

    // Proceed if no validation errors
    if (empty($_SESSION['errors'])) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT); 

        // Insert new user into the database
        $sql = "INSERT INTO users (first_name, last_name, username, email, password, phone, address) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            $_SESSION['errors'][] = "Error preparing statement: " . $conn->error;
            header('Location: index.php');
            exit;
        }

        $stmt->bind_param("sssssss", $first_name, $last_name, $username, $email, $hashed_password, $phone, $address);

        if ($stmt->execute()) {
            header('Location: index.php?success=true');
            exit;
        } else {
            $_SESSION['errors'][] = "Error: " . $stmt->error;
        }

        $stmt->close();
    }

    // Close connection and redirect in case of errors
    $conn->close();
    header('Location: index.php');
    exit;
}
?>
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
                            <input type="password" class="form-control" id="registerPassword" name="password" required>
                            <div class="input-group-append">
                                <span class="input-group-text" id="toggleSignupPassword">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                  </div>
                        <div class="error" id="errorPassword"></div>
                        <small class="form-text text-muted">
                            Password must be at least 8 characters long, contain one uppercase letter, one lowercase letter, one number, and one special character.
                        </small>
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
                        <p>Already have an account? <a href="#" data-dismiss="modal" data-toggle="modal" data-target="#loginModal">Login</a></p>
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

        // Hide the success message after 3 seconds
        setTimeout(function() {
            document.getElementById('loginSuccessMessage').style.display = 'none';
        }, 3000);
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