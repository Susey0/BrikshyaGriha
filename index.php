<?php
session_start();
include 'config.php';
if (isset($_SESSION['login_error'])) {
    $loginError = $_SESSION['login_error'];
    unset($_SESSION['login_error']); // Clear the error after displaying it
} else {
    $loginError = '';
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brikshya Griha</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="fontawesome-free-6.6.0-web/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans&display=swap" rel="stylesheet">
    <link rel="icon" href="images/logo.jpg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

</head>

<body>
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
                <?php if ($loginError): ?>
                    <div class="alert alert-danger">
                        <?php echo $loginError; ?>
                    </div>
                <?php endif; ?>
                <div class="modal-body">
                    <div id="loginSuccessMessage" class="alert alert-success" style="display: none;"></div>

                    <form action="login.php" method="POST">
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
            event.preventDefault(); 
            var targetModal = $(this).data('target');
            $(targetModal).modal('show'); 
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
    <!-- <script>
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
    </script> -->


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

    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">
            <img src="images/logo.jpg" alt="Brikshya Griha Logo" width="30" height="30"> Brikshya Griha
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link active" href="#">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="aboutus.php">About Us</a></li>
                <li class="nav-item"><a class="nav-link" href="products.php">Products</a></li>
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

    <div class="hero text-center text-white" style="padding: 50px 0;">

    </div>


    <section class="my-5">
        <div class="container">
            <h2 class="text-center">About Us</h2>
            <div class="row">
                <div class="col-md-6">
                    <img src="images/about.jpg" class="img-fluid" alt="About Us">
                </div>
                <div class="col-md-6">
                    <h3>We are Brikshya Griha.</h3>
                    <p>We are an Brikshya Griha dedicated to offering a wide range of indoor and outdoor plants, tools,
                        and accessories. Discover the joy of a greener world with our motto: <em>Growing Green, Growing
                            Together</em></p>
                    <a href="aboutus.php" class="btn btn-custom">More about us</a>
                </div>
            </div>
        </div>
    </section>

    <section class="my-5">
        <div class="container">
            <h2 class="text-center">Product Categories</h2>
            <div class="row">

                <?php
                // Array of categories
                $categories = [
                    ["image" => "images/plant1.png", "title" => "Indoor Plants", "description" => "Decorate your home with our wide selection of plants."],
                    ["image" => "images/Outdoor Plants.png", "title" => "Outdoor Plants", "description" => "Enhance your space with vibrant outdoor plants!"],
                    ["image" => "images/flower.png", "title" => "Flowers", "description" => "Brighten your garden with stunning flowers!"],
                    ["image" => "images/vegetable.png", "title" => "Vegetables", "description" => "Harvest your own fresh vegetables today!"],
                    ["image" => "images/fruits.png", "title" => "Fruits", "description" => "Enjoy fresh, delicious fruits from your garden!"],
                    ["image" => "images/fertilizers.png", "title" => "Fertilizers", "description" => "Boost your plants with premium fertilizers!"],
                    ["image" => "images/pot1.png", "title" => "Pots", "description" => "Elevate your garden with stylish pots!"],
                    ["image" => "images/accessories.jpg", "title" => "Accessories", "description" => "Discover handy accessories for easy plant care."]
                ];

                // Loop through categories and create cards
                foreach ($categories as $key => $category): ?>
                    <div class="col-md-4 col-sm-6 col-12 mb-4">
                        <div class="card">
                            <img src="<?php echo $category['image']; ?>" class="card-img-top img-fluid" alt="<?php echo $category['title']; ?>">
                            <div class="card-body">
                                <h4 class="card-title"><?php echo $category['title']; ?></h4>
                                <p class="card-text"><?php echo $category['description']; ?></p>
                                <a href="#" class="btn btn-custom" onclick="handleShopClick(<?php echo $key; ?>)">Let's Shop</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>      

    <script>
        function handleShopClick(categoryIndex) {
            <?php if (!isset($_SESSION['user_logged_in'])): // Check if user is logged in ?>
                <?php $_SESSION['errors']['general'] = "To shop, you must log in first."; ?>

                $('#loginModal').modal('show');

                $('#categoryIndex').val(categoryIndex);
            <?php else: ?>
                // Redirect to the shop page with the selected category
                window.location.href = "shop.php?category=" + categoryIndex;
            <?php endif; ?>
        }

    </script>

    <section class="my-5">
        <div class="container">
            <h2 class="text-center">Our Gallery</h2>
            <div class="row gallery">
                <div class="col-md-4">
                    <img src="images/gallery1.png" class="img-fluid" alt="Gallery">
                </div>
                <div class="col-md-4">
                    <img src="images/gallery2.jpg" class="img-fluid" alt="Gallery">
                </div>
                <div class="col-md-4">
                    <img src="images/gallery3.png" class="img-fluid" alt="Gallery">
                </div>
                <div class="col-md-4">
                    <img src="images/gallery4.jpg" class="img-fluid" alt="Gallery">
                </div>
            </div>
        </div>
    </section>

    <section class="my-5">
        <div class="container">
            <h2 class="text-center">From Our Blog</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <img src="images/blog1.jpg" class="card-img-top" alt="Blog Post">
                        <div class="card-body">
                            <h4 class="card-title">6 Stylish Plants That Help Clean Air</h4>
                            <a href="Blogs/blog1.html" class="btn btn-custom">Read Now ></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <img src="images/blog2.jpg" class="card-img-top" alt="Blog Post">
                        <div class="card-body">
                            <h4 class="card-title">People And Plants</h4>
                            <a href="Blogs/blog2.html" class="btn btn-custom">Visit</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <img src="images/blog4.png" class="card-img-top" alt="Blog Post">
                        <div class="card-body">
                            <h4 class="card-title">The Benefits of Indoor Plants</h4>
                            <a href="Blogs/blog3.html" class="btn btn-custom">Visit</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <img src="images/blog3.jpg" class="card-img-top" alt="Blog Post">
                        <div class="card-body">
                            <h4 class="card-title">Tree & Forest Activities For Kids</h4>
                            <a href="Blogs/blog3.html" class="btn btn-custom">Read Now ></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="my-5">
        <div class="container">
            <h2 class="text-center">Frequently Asked Questions</h2>
            <div>
                <h4>Which indoor plants are easiest to care for?</h4>
                <p>Pothos, Aglaonema, Sansevieria, Dracaena, Spider Plants, Cacti, Succulents.</p>
                <h4>Should I mist my plants?</h4>
                <p>Plants like Bird of Paradise, Philodendron, Fern, Iron plant, and Bella Palm enjoy misting. Avoid
                    misting plants like Snake, ZZ, Yucca, Jade.</p>
            </div>
            <div class="text-center">
                <a href="Blogs/faq.html" class="btn btn-custom">FAQs</a>
            </div>
        </div>
    </section>




    <footer style="background-color: #121212; color: #E0E0E0; padding: 20px 0;">
        <div class="container text-center">
            <img src="images/logo.jpg" alt="Brikshya Griha Logo" style="height: 50px; margin-bottom: 10px;">
            <h3 style="color: #5CD63A;">Brikshya Griha</h3>
            <p>Brikshya Griha is your go-to online store for a wide variety of plants and plant accessories. We are
                committed to providing high-quality products that nurture your green thumb.</p>

            <div class="quick-links" style="margin: 20px 0;">
                <h4 style="color: #FFC107;">Quick Links</h4>
                <a href="aboutus.php" class="footer-link">About Us</a>
                <a href="ContactUs/contactus.php" class="footer-link">Contact Us</a>
                <a href="terms.php" class="footer-link">Terms and Condition</a>
            </div>

            <div class="social-media" style="margin: 20px 0;">
                <h4 style="color: #FFC107;">Follow Us</h4>
                <a href="https://facebook.com" target="_blank" class="social-icon">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="https://instagram.com" target="_blank" class="social-icon">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="https://twitter.com" target="_blank" class="social-icon">
                    <i class="fab fa-twitter"></i>
                </a>
            </div>

            <p style="margin-top: 20px;">&copy; 2024 Brikshya Griha. All rights reserved.</p>
        </div>


    </footer>




</body>

</html>