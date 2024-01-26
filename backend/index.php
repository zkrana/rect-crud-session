<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styling/style.css">
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <h2>Admin Login</h2>
            <p>Welcome back comrade!</p>

            <!-- <?php 
            // Include the PHP logic file
            // include '/backend/auth/backend-assets/login.php';

            // if(!empty($login_err)){
            //     echo '<div class="alert alert-danger">' . $login_err . '</div>';
            // }        
            ?> -->
            <form action="auth/backend-assets/login.php" method="post">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>">
                    <span class="invalid-feedback"><?php echo $username_err; ?></span>
                </div>    
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                    <span class="invalid-feedback"><?php echo $password_err; ?></span>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Login">
                </div>
                <p>Don't have an account? <a href="./files/register.php">Sign up now</a>.</p>
            </form>
        </div>
    </div>
</body>
</html>
