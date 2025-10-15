
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/home/globals.css">
        <title>ReidHub | SignIn</title>
        <link rel="stylesheet" href="/css/auth/globals.css">
        <link rel="stylesheet" href="/css/home/globals.css">
    </head>

    <body>
        <div class="main-container">
            <!-- Left Image Section -->
            <div class="image-section">            </div>

            <!-- Right Form Section -->
            <div class="form-section">
                <div class="header">
                    <img src="assets/images/logo-no-text.png" alt="ReidHub Logo">
                    <p>Welcome Back!</p>
                </div>
                <form method="POST" enctype="multipart/form-data" class="form">
                    <div class="form-username">
                            <input type="username" name="username" id="username" class="form-control" placeholder="Username" autocomplete="off" required />
                    </div>
                    
                    <div class="form-outline">
                        <div class="form-password">
                        <input type="password" name="password" id="password" class="form-control" placeholder="Password" required />
                        
                    </div>
                    
                    
                    <div class="signin_button">
                        <button name="signin" class="btn-primary" type="submit">Sign In</button>
                    </div>

                    <div class="forgot">
                        <p class="mb-0 me-2">Forgot Password? <a href="/recoverPassword">Recover Password</a></p>
                    </div>

                    <div class="create">
                        <p class="mb-0 me-2">Don't have an account? <a href="./register.html">Sign Up</a></p>
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>



