
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ReidHub | Forgot Password</title>
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
            }

            .wrapper {
                display: flex;
                flex-wrap: wrap;
                min-width: 80%;
                height: 90%;
                border-radius: 20px;
                box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
                overflow: hidden;
                background-color: #ffffff;
                align-items: center;
                justify-content: center;
            }

            .wrapper > * {
                flex: 1 1 50%;
                min-width: 0;
                box-sizing: border-box;
                padding: 0;
            }

            .container {
                background-color: #ffffff;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                height: 100%;
            }

            .container > * {
                min-width: 0;
                max-width: 100%;
                flex-shrink: 1;
                box-sizing: border-box;
                max-height: 100%;
            }

            .header {
                display: flex;
                flex-direction: column;
                justify-content: end;
                align-items: center;
            }

            .header p{
                text-align: center;
                font-size: clamp(1.5rem, 4vw, 3rem);
                font-weight: 400;
                color: #165797;
            }

            .header img {
                width: clamp(2rem, 6vw, 6rem);
                margin-bottom: 2rem;
            }

            p {
                text-align: center;
                color: #000000b6;
                margin: 0;
                font-size: clamp(0.8rem, 1.5vw, 1.2rem);
                margin-bottom: 2rem;
            }

            form {
                display: flex;
                flex-direction: column;
                align-items: center;
                width: 100%;
                justify-content: flex-start;
            }

            .form-outline {
                position: relative;
                width: 100%;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
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
                top: 50%;
                transform: translateY(-50%);
                cursor: pointer;
                width: clamp(1rem, 2vw, 1.5rem);
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
                margin-bottom: 2vh;
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


            .btn-primary {
                color: #ffffff;
                background-color: #0466C8;
                border: 1 solid #0466C8;
                cursor: pointer;
                flex-shrink: 1;
                width: clamp(2rem, 20vw, 20rem);
                padding: clamp(0.5rem, 1vw, 1rem);
                font-size: clamp(0.9rem, 1.2vw, 1.2rem);
                border-radius: 3rem;
                box-sizing: border-box;
                border-color: #0466C8;
            }

            .btn-primary:hover {
                background-color: #ffffff;
                color: #0466C8;
            }

            .forgot .create {
                text-align: center;
                color: #000000b6;
                font-size: clamp(0.8rem, 1.5vw, 1.2rem);
                margin-top: 20vw;
            }

            .forgot a .create a {
                text-decoration: none;
                color: #0264ec;
            }

        </style>
    </head>

    <body>
        <div id="notification" class="notification"></div>
        <div class="wrapper">
            <div class="container">
                <div class="header">
                    <img src="assets/images/logo-no-text.png" alt="ReidHub Logo">
                     <p>Forgot Password?</p>
                    
                </div>
                <form method="POST" enctype="multipart/form-data" class="form">
                    <p>Please enter your email to reset the password</p>
                    
                    <div class="form-outline">
                        <input type="email" name="email" id="email" class="form-control" placeholder="Email" autocomplete="off" required />
                    </div>
                    

                    <div class="submit_button">
                        <button name="submit" class="btn-primary" type="submit">Submit</button>
                    </div>
                </form>
            </div>
        </div>

    </body>
</html>


