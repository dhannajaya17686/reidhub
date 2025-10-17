<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ReidHub | SignIn</title>
        <link rel="stylesheet" href="/css/auth/globals.css">
        <link rel="stylesheet" href="/css/home/globals.css">
<<<<<<< HEAD
        <style>
            .field-error{color:#c0392b;font-size:.9rem;margin-top:6px;display:block;text-align:left}
            .input-invalid{border-color:#c0392b !important}
        </style>
=======
>>>>>>> ed92e142f73848822dd9709bbfaa561680b1af8c
    </head>
    <body>
        <div class="main-container">
<<<<<<< HEAD
            <div class="image-section"></div>

=======
            <!-- Left Image Section -->
            <div class="image-section">            </div>

            <!-- Right Form Section -->
>>>>>>> ed92e142f73848822dd9709bbfaa561680b1af8c
            <div class="form-section">
                <div class="header">
                    <img src="/assets/images/logo-no-text.png" alt="ReidHub Logo" />
                    <p>Welcome Back!</p>
                </div>
<<<<<<< HEAD

                <?php
                  $errors = $errors ?? [];
                  $old    = $old ?? [];
                ?>

                <form method="POST" class="form" action="/login" novalidate>
                    <div class="form-username">
                        <input
                            type="text"
                            name="username"
                            id="username"
                            class="form-control<?php echo isset($errors['username']) ? ' input-invalid' : '';?>"
                            placeholder="Email or Registration No"
                            autocomplete="username"
                            value="<?php echo htmlspecialchars($old['username'] ?? ''); ?>"
                            required
                        />
                        <small class="field-error" data-error-for="username">
                            <?php echo isset($errors['username']) ? htmlspecialchars($errors['username']) : ''; ?>
                        </small>
=======
                <form method="POST" enctype="multipart/form-data" class="form">
                    <div class="form-username">
                            <input type="username" name="username" id="username" class="form-control" placeholder="Username" autocomplete="off" required />
>>>>>>> ed92e142f73848822dd9709bbfaa561680b1af8c
                    </div>
                    
                    <div class="form-outline">
                        <div class="form-password">
<<<<<<< HEAD
                            <input
                                type="password"
                                name="password"
                                id="password"
                                class="form-control<?php echo isset($errors['password']) ? ' input-invalid' : '';?>"
                                placeholder="Password"
                                autocomplete="current-password"
                                required
                            />
                            <small class="field-error" data-error-for="password">
                                <?php echo isset($errors['password']) ? htmlspecialchars($errors['password']) : ''; ?>
                            </small>
                        </div>
                    </div>

=======
                        <input type="password" name="password" id="password" class="form-control" placeholder="Password" required />
                        
                    </div>
                    
                    
>>>>>>> ed92e142f73848822dd9709bbfaa561680b1af8c
                    <div class="signin_button">
                        <button name="signin" class="btn-primary" type="submit">Sign In</button>
                    </div>

                    <div class="create" style="margin-top:12px">
                        <p class="mb-0 me-2">Don't have an account? <a href="/signup">Sign Up</a></p>
                    </div>
                </form>
            </div>
        </div>
<<<<<<< HEAD

        <div id="toast" class="toast" role="alert" aria-live="polite"></div>
        <script src="/js/home/auth/response-handle.js"></script>
=======
>>>>>>> ed92e142f73848822dd9709bbfaa561680b1af8c
    </body>
</html>



