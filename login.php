<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>

    <div class="container">
        <!-- LOGIN FORM -->
        <div class="form-box login">
            <form id="loginForm">
                <h1>Login</h1>
                <div class="input-box">
                    <input type="text" id="login_username" placeholder="Username" required>
                    <i class='bx bxs-user'></i>
                </div>
                <div class="input-box">
                    <input type="password" id="login_password" placeholder="Password" required>
                    <i class='bx bxs-lock-alt'></i>
                </div>

                <div class="forgot-link">
                    <a href="#" id="openChangePass">Change Password?</a>
                </div>

                <button type="submit" class="btn">Login</button>
            </form>
        </div>

        <!-- REGISTER FORM -->
        <div class="form-box register">
            <form id="registerForm">
                <h1>Signup</h1>
                <div class="input-box">
                    <input type="text" id="reg_username" placeholder="Username" required>
                    <i class='bx bxs-user'></i>
                </div>
                <div class="input-box">
                    <input type="email" id="reg_email" placeholder="Email" required>
                    <i class='bx bxs-envelope'></i>
                </div>
                <div class="input-box">
                    <input type="password" id="reg_password" placeholder="Password" required>
                    <i class='bx bxs-lock-alt'></i>
                </div>
                <button type="submit" class="btn">Signup</button>
            </form>
        </div>

        <!-- CHANGE PASSWORD FORM -->
        <div class="form-box change-password" style="display:none;">
            <form id="changePassForm">
                <h1>Change Password</h1>

                <div class="input-box">
                    <input type="text" id="cp_username" placeholder="Username" required>
                    <i class="bx bxs-user"></i>
                </div>

                <div class="input-box">
                    <input type="password" id="old_password" placeholder="Old Password" required>
                    <i class="bx bxs-lock-alt"></i>
                </div>

                <div class="input-box">
                    <input type="password" id="new_password" placeholder="New Password" required>
                    <i class="bx bxs-lock-alt"></i>
                </div>

                <button type="submit" class="btn">Update Password</button>

                <br><br>
                <a href="#" id="backToLogin">Back to Login</a>
            </form>
        </div>
        <div class="toggle-box">
            <div class="toggle-panel toggle-left">
                <h1>Hello, Welcome!</h1>
                <p>Don't have an account?</p>
                <button class="btn register-btn">Signup</button>
            </div>

            <div class="toggle-panel toggle-right">
                <h1>Welcome Back!</h1>
                <p>Already have an account?</p>
                <button class="btn login-btn">Login</button>
            </div>
        </div>
    </div>
<script>
$(function() {
    $('.register-btn').click(function() {
        $('.container').addClass('active');
        $(".change-password").hide();
    });
    $('.login-btn').click(function() {
        $('.container').removeClass('active');
        $('.change-password').hide();
        $('.login').show();
    });
    $("#openChangePass").click(function(e){
        e.preventDefault();
        $(".login").hide();
        $(".change-password").show();
    });
    $("#backToLogin").click(function(e){
        e.preventDefault();
        $(".change-password").hide();
        $(".login").show();
    });
    $("#registerForm").submit(function(e){
        e.preventDefault();

        $.post("auth_controller.php", {
            type: "register",
            username: $("#reg_username").val(),
            email: $("#reg_email").val(),
            password: $("#reg_password").val()
        }, function(res){
            alert(res.message);
            if (res.status === "success") {
                $('.container').removeClass('active');
            }
        }, "json");
    });
    $("#loginForm").submit(function(e){
        e.preventDefault();

        $.post("auth_controller.php", {
            type: "login",
            username: $("#login_username").val(),
            password: $("#login_password").val()
        }, function(res){
            alert(res.message);
            if (res.status === "success") {
                window.location = "dashboard.php";
            }
        }, "json");
    });

    $("#changePassForm").submit(function(e){
        e.preventDefault();

        $.post("auth_controller.php", {
            type: "change_password",
            username: $("#cp_username").val(),
            old_password: $("#old_password").val(),
            new_password: $("#new_password").val()
        }, function(res){
            alert(res.message);
             if (res.status === "success") {
                $(".change-password").hide();
                $(".login").show();
            } 
        }, "json");
    });

});
</script>
</body>
</html>
