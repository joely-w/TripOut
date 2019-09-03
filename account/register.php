<?php
$title = "Register";
include('../header.php'); ?>
<body class="fullscreenpage">
<?php include('../navigation.php'); ?>
<div class="hero">
    <ul class="slider">
        <li id="slider" class="visible"> <!-- Static visible slide -->
            <img alt="Register for TripOut" src="/images/register.jpg">
            <div class='reg' id="login">
                <h1 id="register">Register</h1>
                <div id="errorfield"></div> <!--Content updated if error occurs!-->
                <form id="regform">
                    <input name='username' placeholder='Username' type='text'/>

                    <input name='name' placeholder='Full Name' type='text'/>
                    <input id='pw' name='password' placeholder='Password' type='password'/>
                    <input name='email' placeholder='E-Mail Address' type='text'/>
                    <div class='agree'>
                        <input id='agree' name='agree' type='checkbox'/>
                        <label for='agree'></label>Accept rules and conditions
                    </div>
                    <input class='animated' type='submit' value='Register'/>
                    <a class='forgot' href='#'>Already have an account?</a>
                </form>
            </div>
        </li>
    </ul>

</div>
</body>