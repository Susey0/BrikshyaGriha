<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/form.css">
    <link rel="stylesheet" href="fontawesome-free-6.6.0-web/css/all.min.css">


</head>
<body>
<form id="loginForm" action="login.php" method="POST">
    <h5>Login</h5>
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
   
</form>
</body>
</html>
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
    </script>