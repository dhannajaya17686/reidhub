
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ReidHub | Reset Password</title>
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
                gap: 1rem;
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
                justify-content: center;
                align-items: center;
                margin-bottom: 2%;
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
                margin-top: 0.5rem;
            }

            form {
                display: flex;
                flex-direction: column;
                align-items: center;
                width: 100%;
                margin-top: 2vw;
            }

            .pin-container {
                display: flex;
                justify-content: center;
                align-items: center;
                gap: 1rem;
            }

            .pin-box {
                width: clamp(2rem, 4vw, 3rem);
                height: clamp(2rem, 4vw, 3rem);
                text-align: center;
                font-size: clamp(1.5rem, 3vw, 2rem);
                border: 2px solid #0466C8;
                border-radius: 0.5rem;
                margin-bottom: 10%;
            }


            .btn-submit {
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

            .btn-submit:hover {
                background-color: #ffffff;
                color: #0466C8;
            }

            .resend {
                text-align: center;
                color: #000000b6;
                margin: 0;
                font-size: clamp(0.8rem, 1.5vw, 1.2rem);
                margin-top: 2rem;
            }

            .resend a {
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
                    <p>Verify Your Email</p>
                </div>

                <p>We sent a verification code to your email</p>
                <p>Enter 5 digit code sent to you</p>
                <form method="POST" enctype="multipart/form-data" class="form">
                    
                    <div class="pin-container">
                        <input type="text" maxlength="1" class="pin-box" />
                        <input type="text" maxlength="1" class="pin-box" />
                        <input type="text" maxlength="1" class="pin-box" />
                        <input type="text" maxlength="1" class="pin-box" />
                        <input type="text" maxlength="1" class="pin-box" />
                    </div>

                    <div class="submit_button">
                        <button name="submit" class="btn-submit" type="submit">Submit</button>
                    </div>
                    <div class="resend">
                        <p class="mb-0 me-2">Didn't receive the code? <a href="./verify_email.html">Resend</a></p>
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>
