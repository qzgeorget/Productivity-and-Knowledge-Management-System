<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Login Page</title>
    <style>
        body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f0f0f0; 
        }
        button{
            background: transparent;
            color: blue;
            width: 450px;
            height: 75px;
            font-family: sans-serif;
            font-size: 150px;
            border-color: blue;
            font-weight: 600;
            border-radius: 22px;
            letter-spacing: 7px;
            transition: 2s;
            text-align: center;
            align-items: center;
            justify-content: center;
        }

        .container1{
            text-align: center;
            justify-content: center;
            align-items: center;
        }

        .login1 {
            width: 400px;
            max-width: 400px;
            margin: 1rem;
            padding: 2rem;
            box-shadow: 0 0 40px rgba(0, 0, 0, 0.2);
            border-radius: var(--border-radius);
            background: #ffffff;
            text-align: center;
        }

        button:hover{
            cursor: pointer;
            transition: 0.5s;
        }

        .login1{
            text-align: center;
            color:blue;
            font-size: 20px;
        }

        .form__title {
            margin-bottom: 2rem;
            text-align: center;
        }

        .form__message {
            text-align: center;
            margin-bottom: 1rem;
        }

        .form__message--success {
            color: var(--color-success);
        }

        .form__message--error {
            color: var(--color-error);
        }

        .form__input-group {
            margin-bottom: 1rem;
        }

        .form__input {
            display: block;
            width: 100%;
            padding: 0.75rem;
            box-sizing: border-box;
            border-radius: var(--border-radius);
            border: 1px solid #dddddd;
            outline: none;
            background: #eeeeee;
            transition: background 0.2s, border-color 0.2s;
        }

        .form__input:focus {
            border-color: var(--color-primary);
            background:rgb(233, 231, 231);
        }

        .form__button {
            width: 100%;
            padding: 1rem 2rem;
            font-weight: bold;
            font-size: 1.1rem;
            color: blue;
            border: none;
            border-radius: var(--border-radius);
            outline: none;
            cursor: pointer;
            background: #e6c406 ;
        }
        .form__button:hover {
            background: #caac03 ;
        }

        .form__button:active {
            transform: scale(0.98);
        }

        header {
            background: blue;
            position:fixed; 
            top:0px; 
            left:0px;
            width: 100%;
            z-index: 997;
            height: 150px;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        header::after {
            content: '';
            display: table;
            clear: both;
        }

        nav {
            text-align: center;
            display:inline-block;
        }
        .navbar-text {
            color: gold;
            font-size: 35px;
            text-align: center;
        }
    </style>
</head>
<body>
    <header>
        <div class="wrapper">
            <nav>
            <p class="navbar-text">
                <img src="mialogo.png" alt="Logo Description" width="300" height="80">
            </p>
            </nav>
        </div>
    </header>
    <div class="container1">
        <div class="col">
            <div class="login1">
                <h1 class="form__title">LOGIN</h1>
                <div class="form__input-group">
                    <input type="text" id="username2" class="form__input" autofocus placeholder="Username...">
                    <div class="form__input-error-message" id="usernameError"></div>
                </div>
                <div class="form__input-group">
                    <input type="password" id="password2" class="form__input" autofocus placeholder="Password...">
                    <div class="form__input-error-message" id="passwordError"></div>
                </div>
                <button class="form__button" id="loginButton">Continue</button>
                <div id="loginResult" class="form__message"></div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById("loginButton").addEventListener("click", function() {
            var username = document.getElementById("username2").value;
            var password = document.getElementById("password2").value;
            var loginResult = document.getElementById("loginResult");

            if (username === "leader" && password === "123") {
                window.location.href = "teamleader.html";
                loginResult.innerHTML = "";
            } 
            else if (username === "member" && password === "123") {
                window.location.href = "TeamMembersPage_1.0.html";
                loginResult.innerHTML = "";
            } 
            else if (username === "manager" && password === "123") {
                window.location.href = "Manager.html";
                loginResult.innerHTML = "";
            } 
            else if (username === "technical" && password === "123") {
                window.location.href = "mainforum.html";
                loginResult.innerHTML = "";
            } 
            else {
                loginResult.innerHTML = "Invalid username or password. Please try again.";
            }
        });
    </script>
</body>
</html>
