<?php
$title = "Register";
$scripts = array("register.js");
include('../header.php'); ?>
<body>
<? include('../navigation.php'); ?>
<div class="container">
    <form id="registration_form" class="not_logged_in registration">
        <!--Will only display form if user not logged in-->
        <h1>Register</h1>
        <div class="row">
            <div class="col-md-6 col-md-offset-3" id="errorfield">
                <!--Append any errors to this div-->
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 col-md-offset-3">
                <label>Full name
                    <input type="text" id="name" placeholder="John Doe"/>
                </label>
            </div>
            <div class="col-md-3"><label>Email
                    <input type="text" id="email" placeholder="john.doe@example.com"/>
                </label>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <label class="username">Username
                    <input type="text" id="username" placeholder="JohnDoe20"/>
                </label>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 col-md-offset-3">
                <label>Password
                    <input type="password" id="pwd" placeholder="Password example"/>
                </label>
            </div>
            <div class="col-md-5 passwordstrength" id="passwordstrength">
                <span class="wrong" id="eight">
                    &#10006; Password length is less than 8 characters
                </span>
                <span class="wrong" id="numbers">
                    &#10006; Password does not contain any numbers
                </span>
                <span class="wrong" id="complexity">
                    &#10006; Password does not contain one uppercase and one lowercase character
                </span>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2 col-md-offset-5">
                <button class="btn btn-primary">Register</button>
            </div>
        </div>
    </form>
    <div class="row logged_in">
        <h1 class="display_username"><!--Append welcome message to user--></h1>
    </div>
</div>
<? include('../footer.php'); ?>
</body>
