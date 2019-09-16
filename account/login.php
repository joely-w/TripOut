<?php
$title = "Login";
#No login script in script array as already included in navigation bar
include('../header.php'); ?>
<body>
<?php include('../navigation.php'); ?>
<div class="hero">
    <ul class="slider">
        <li id="slider" class="visible"> <!-- Static visible slide -->
            <img alt="Login to TripOut" src="/images/login.jpg">
            <div class='login' id="login">
                <?php if (isset($_GET['failed'])){
                echo"<div class='alert alert-danger' role='alert'>Login failed! Please try again</div>";

                }
                if (isset($_SESSION['Username'])) {#If user is logged in then greet?>
                    <h1 id="register">Hi there <?php echo $_SESSION['Fullname']; ?>!</h1>

                <? } else { #If not logged in, show login form
                    ?>
                    <h1 id="register">Login</h1>
                    <div id="errorfield"></div> <!--Content updated if error occurs!-->
                    <form id="otherLogin">
                        <input name='id' placeholder='Username' type='text'/>
                        <input id='pw' name='password' placeholder='Password' type='password'/>
                        <input class='animated' type='submit' value='Login'/>
                        <a class='forgot' href='/account/forgot.php'>Forgot your password?</a>
                    </form>
                    <?php
                } ?>
            </div>

        </li>
    </ul>

</div>

</body>