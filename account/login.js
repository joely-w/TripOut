function loginFailed() {
    //Get currently used URL
    const url_string = window.location.href;
    //Break into parameters
    const url = new URL(url_string);
    //Return whether failed parameter exists in URL
    return url['search'] === "?failed";
}

function reportError(error_text) {
    $("#errorfield").html(`<div class="alert alert-danger" role="alert"> ${error_text} </div>`); //Fill error field with reported error
}

function countNumbers(string) {
    //Declare variable to store how many numbers are in string
    let count = 0;

    //Loop through string characters
    for (let i = 0; i < string.length; i++) {

        //If character is a number
        if (/\d/.test(string[i])) {
            //Increment number counter by 1
            count += 1;
        }
    }

    //Return how many numbers are in string
    return count;
}

function PasswordStrong(password) {
    //Return if all the password tests are passed or not
    return password.length >= 8
        && countNumbers(password) >= 1
        && password.search(/[A-Z]/) !== -1 && password.search(/[a-z]/) !== -1;
}

/**
 * @return {boolean}
 */
function Login(username, password) {
    //Declare variable to store regex expression for the email
    let email_expression = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    //If username is not in email format and is longer than allowed username length or password fails strength test
    if ((!(email_expression.test(username)) && username.length > 20) && !PasswordStrong(password)) {
        //Report that login has failed
        $("#errorfield").html(" &#10006; Username/Password is incorrect").addClass("wrong");
        return false;
    }

    //If data in correct form, send data to server
    $.ajax({
        type: "POST",
        url: '/account/login_process.php',
        data: {id: username, password: password},
        dataType: "json",
        success: function (response) {
            //If login is successful
            if (response['success'] === 1) {
                //Reload page
                location.reload();
            }
            //If login fails
            else {
                //Go to login page and pass login failure parameter
                window.location.href = "/account/login.php?failed";
            }
        }
    });
    return true;
}

function isLoggedIn() {
    //Make JSON object to append AJAX response to
    let logged_in = {Status: false, Username: null};
    $.ajax({
        type: "GET",
        url: '/account/isLoggedIn.php',
        dataType: "json",
        //Wait for ajax call to finish before continuing
        async: false,
        success: function (response) {
            //Append result to logged_in JSON array
            logged_in["Status"] = response["loggedIn"];
            logged_in["Username"] = response["Username"];
        }
    });
    //Return response
    return logged_in;
}

//When DOM is ready call anonymous function
$(document).ready(function () {

    //If failed parameter passed
    if (loginFailed()) {
        //Report that login has failed
        reportError("Username or password incorrect!");
    }

    //When the navigation bar login form is submitted
    $('#navbar_loginform').submit(function (e) {
        //Stop the form from submitting
        e.preventDefault();

        //Call login function with login details
        Login($("#navbar_username").val(), $("#navbar_password").val());
    });

    //When full page login form is submitted
    $('#full_login_form').submit(function (e) {
        //Stop the form from submitting
        e.preventDefault();

        //Call login function with login details
        Login($("#full_username").val(), $("#full_password").val());
    });

    //Declare variable to store the login status result in
    let login_status = isLoggedIn();

    //If user is logged in
    if (login_status['Status'] === true) {
        //Show all containers with logged_in class
        $(".logged_in").css("visibility", "visible");

        //Add a welcome text to all elements with class display_username
        $(".display_username").text(`Welcome ${login_status['Username']}!`);
    }
    //If user is not logged in
    else {
        //Show all containers with class not_logged_in
        $(".not_logged_in").css("visibility", "visible");
    }
});
