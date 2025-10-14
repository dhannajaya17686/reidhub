<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ReidHub | SignUp</title>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
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
                    <p>Let's Get Started!</p>
                </div>
                <form method="POST" enctype="multipart/form-data" class="form">
                    <p>One stop for all your university needs.</p>
                    <div class="form-name">
                        <input type="text" name="first_name" id="first_name" class="form-control" placeholder="First Name" autocomplete="off" required />
                        <input type="text" name="last_name" id="last_name" class="form-control" placeholder="Last Name" autocomplete="off" required />
                    </div>
                    <div class="form-name">
                        <input type="text" name="email" id="email" class="form-control" placeholder="Student Email" autocomplete="off" required />
                        <input type="text" name="reg_no" id="reg_no" class="form-control" placeholder="Registration No" autocomplete="off" required />
                    </div>
                    <div class="form-outline">
                        <div class="form-password">
                            <input type="password" name="password" id="password" class="form-control" placeholder="New Password" autocomplete="off" required />
                            <img id="togglePassword" src="assets/icons/hide.png" alt="Toggle Password">
                        </div>
                    </div>
                    <div class="form-outline">
                        <div class="form-password">
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirm Password" required />
                            <img id="togglePassword" src="assets/icons/hide.png" alt="Toggle Password">
                        </div>
                    </div>
                    <div class="signup_button">
                        <button name="signup" class="btn-primary" type="submit">Sign Up</button>
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>
