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
                <form method="POST" enctype="multipart/form-data" class="form" action="/signup">
                    <p>One stop for all your university needs.</p>

                    <div class="form-name">
                        <div class="field">
                            <input type="text" name="first_name" id="first_name" class="form-control" placeholder="First Name" autocomplete="off" required />
                            <small class="field-error" data-error-for="first_name"></small>
                        </div>
                        <div class="field">
                            <input type="text" name="last_name" id="last_name" class="form-control" placeholder="Last Name" autocomplete="off" required />
                            <small class="field-error" data-error-for="last_name"></small>
                        </div>
                    </div>

                    <div class="form-name">
                        <div class="field">
                            <input type="text" name="email" id="email" class="form-control" placeholder="Student Email" autocomplete="off" required />
                            <small class="field-error" data-error-for="email"></small>
                        </div>
                        <div class="field">
                            <input type="text" name="reg_no" id="reg_no" class="form-control" placeholder="Registration No" autocomplete="off" required />
                            <small class="field-error" data-error-for="reg_no"></small>
                        </div>
                    </div>
                    <div class="form-outline">
                        <div class="form-password">
                            <input type="password" name="password" id="password" class="form-control" placeholder="New Password" autocomplete="off" required />
<<<<<<< HEAD
                            <small class="field-error" data-error-for="password"></small>
=======
>>>>>>> ed92e142f73848822dd9709bbfaa561680b1af8c
                        </div>
                    </div>
                    <div class="form-outline">
                        <div class="form-password">
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirm Password" required />
<<<<<<< HEAD
                            <small class="field-error" data-error-for="confirm_password"></small>
=======

>>>>>>> ed92e142f73848822dd9709bbfaa561680b1af8c
                        </div>
                    </div>
                    <div class="signup_button">
                        <button name="signup" class="btn-primary" type="submit">Sign Up</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="toast" class="toast" role="alert" aria-live="polite"></div>

        <script src="/js/home/auth/response-handle.js"></script>
    </body>
</html>
