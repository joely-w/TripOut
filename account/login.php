<?php
$title = "Login";
#No login script in script array as already included in navigation bar
include('../header.php'); ?>
<body>
<?php include('../navigation.php'); ?>
<div class="container">
    <div class="row logged_in">
        <h1 class="display_username"><!--Append welcome message to user--></h1>
    </div>
    <form class="login not_logged_in" id="full_login_form"><!--Will only display if not logged in -->
        <h1>Login</h1>
        <div class="row">
            <div class="col-md-6 col-md-offset-3" id="errorfield">
                <!--Append any errors to this div-->
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 col-md-offset-3">
                <label>Username or Email
                    <input name='username' type='text' id="full_username" placeholder="Username/Email"/>
                </label>
            </div>
            <div class="col-md-3">
                <label>Password
                    <input name='password' type='password' id="full_password" placeholder="Password"/>
                </label>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <button class="btn btn-primary">
                    Login
                </button>
                <a class='forgot' href='/account/forgot.php'>Forgot your password?</a>
            </div>
        </div>
    </form>
</div>
</body>