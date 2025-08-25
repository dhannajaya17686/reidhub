
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ReidHub | SignUp</title>
        <link rel="stylesheet" href="css/home/globals.css">
        <style>
            body{
                margin: 0;
                padding: 0;
                background-color: #E6E8F9;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                line-height: 0;
            }

            .wrapper {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                align-items: center;
                min-width: 80%;
                height: 90%;
                border-radius: 20px;
                box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
                overflow: hidden;
                background-color: #ffffff;
            }

            .wrapper > * {
                flex: 1 1 50%;
                min-width: 0;
                box-sizing: border-box;
            }

            .container {
                background-color: #ffffff;
                display: flex;
                flex-direction: column;
                align-items: center;
                height:100%;
                justify-content: center;
            }

            .container > * {
                flex: 1 1 auto;
                min-width: 0;
                max-width: 100%;
                flex-shrink: 1;
                box-sizing: border-box;
            }

            .header {
                display: flex;
                flex-direction: column;
                justify-content: flex-end;
                align-items: center;
                
            }

            .header p{
                text-align: center;
                font-size: clamp(1.5rem, 4vw, 3rem);
                font-weight: 400;
                color: #165797;
            }

            p {
                text-align: center;
                color: #000000b6;
                font-size: clamp(0.8rem, 1.5vw, 1.2rem);
                margin-bottom: 5%;
            }


            .header img {
                width: clamp(2rem, 6vw, 6rem);
                margin-bottom: 3rem;
            }

            form {
                display: flex;
                flex-direction: column;
                align-items: center;
                width: 100%;
                justify-content: start;
                margin-top: 2%;
            }

            .form-outline {
                position: relative;
                width: 100%;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
            }

            .form-name{
                display: flex;
                gap: 5vw;
            }

            .form-gender{
                position: relative;
                width: 100%;
                display: flex;
                flex-direction: row;
                align-items: center;
                justify-content: center;
                margin-bottom: 3vw;
                margin-top: 1vw;
                font-size: clamp(1rem, 2vw, 1.2rem);
            }

            .form-gender label{
                margin-left: 2vw;
            }


            .form-password {
                position: relative;
            }

            .form-password input {
                width: 100%;
                width: clamp(2rem, 20vw, 20rem);
                box-sizing: border-box;
            }

            #togglePassword {
                position: absolute;
                right: 1rem; 
                top: 40%;
                transform: translateY(-50%);
                cursor: pointer;
                width: clamp(1rem, 1.5vw, 1.5rem);
                height: auto;
            }


            .form-control::placeholder {
                color: #aaaaaa;
            }

            .form-control:focus::placeholder {
                color: transparent;
            }

            .btn-primary {
                color: #ffffff;
                background-color: #0466C8;
                cursor: pointer;
                flex-shrink: 1;
            }

            .btn-primary:hover {
                background-color: #ffffff;
                color: #0466C8;
            }

            .form-control,
            .btn-primary {
                width: clamp(2rem, 20vw, 20rem);
                padding: clamp(0.5rem, 1vw, 1rem);
                font-size: clamp(0.9rem, 1.2vw, 1.2rem);
                border-radius: 3rem;
                box-sizing: border-box;
                border-color: #0466C8;
                margin-bottom: 2vh;
            }

        </style>
    </head>

    <body>
        <div id="notification" class="notification"></div>
        <div class="wrapper">
            <div class="container">
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
                            <input type="confirm_password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirm Password" required />
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
