class Registration {
    constructor() {
        //Declare a JSON object that can have the form data added to it during validation
        this.form_data = {};
    }

    processForm() {
        //Validate the form
        let validation = this.Validate();

        //Declare class here so as to avoid scope errors with "this"
        let serverData = this.form_data;

        //If validation passes
        if (validation === true) {
            //Submit data to server
            $.ajax({
                type: "POST",
                //Backend file for processing registration
                url: '/account/reg_process.php',
                data: serverData,
                dataType: "json",
                success: function (response) {
                    //If registration succeeds
                    if (response['success'] === 1) {
                        //Clear form and report success
                        $("#registration_form").html("<h1 class='header'>Account successfully created!</h1>");
                    }//If registration fails
                    else {
                        //Report the first error in error field
                        reportError(response['errors'][0])
                    }
                }
            });
        }
    }

    passwordStrength(password) {
        let length;
        let numbers;
        let characters;

        //If password is of appropriate length
        if (password.length >= 8) {
            //Pass length test
            length = 1;
            //Report that password is of an appropriate length
            $("#eight").html(`&#10004; Password length is ${password.length} characters`).removeClass("wrong").addClass("correct");
        }

        //If password is not long enough
        else {
            //Set length test to fail
            length = 0;
            //Report that password is not long enough
            $("#eight").html(`&#10006; Password length is less than 8 characters`).removeClass("correct").addClass("wrong");
        }

        //Count the how many numbers are in the password
        let passwordCount = countNumbers(password);

        //If password contains one or more numbers
        if (passwordCount >= 1) {
            //Pass numbers test
            numbers = 1;
            //Report that there are enough numbers in password
            $("#numbers").html(`&#10004; Password has ${passwordCount} numbers in it`).removeClass("wrong").addClass("correct"); //Report pass
        }

        //If password does not have any numbers in it
        else {
            //Fail numbers test
            numbers = 0;
            //Report that password does not have any numbers in it
            $("#numbers").html(`&#10006; Password does not contain any numbers`).removeClass("correct").addClass("wrong");
        }

        //If password contains a uppercase and lowercase character
        if (password.search(/[A-Z]/) !== -1 && password.search(/[a-z]/) !== -1) {
            //Pass characters test
            characters = 1;
            //Report that password has enough uppercase and lowercase characters
            $("#complexity").html(`&#10004; Password has lowercase and uppercase characters in it`).removeClass("wrong").addClass("correct"); //Report pass
        }

        //If password does not contain more than one uppercase and lowercase character
        else {
            //Fail characters test
            characters = 0;
            //Report that password does not contain an uppercase and lowercase character
            $("#complexity").html(`&#10006; Password does not contain one uppercase and one lowercase character`).removeClass("correct").addClass("wrong"); //Report fail
        }

        //Return whether all tests have been passed or not
        return characters + length + numbers === 3;
    }

    Validate() {

        //Get all the values in the form

        let username = $("#username").val();
        let fullname = $("#name").val();
        let email = $("#email").val();
        let password = $("#pwd").val();

        //Username validation

        //If username is correct length
        if (username.length <= 20) {
            //Add the data to the form data
            this.form_data["username"] = username;
        }
        //If username is too long
        else {
            //Report error
            reportError("Username must be 20 characters or less!");
            //Fail validation
            return false;
        }

        //Full name validation

        //If name has no numbers in it and is within size limit
        if (fullname.length <= 255 && !(/\d/.test(fullname))) {
            //Add fullname to form data
            this.form_data["name"] = fullname;
        }
        //If name has any numbers in it or or it is too big
        else {
            //Report error
            reportError("Name must be 255 characters or less and contain no numbers!");
            //Fail validation
            return false;
        }

        //Email validation

        //If email matches regex expression
        if (/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(email)) {
            //Add email to form data
            this.form_data["email"] = email;
        }
        //If email is not in correct format
        else {
            //Report error
            reportError("Email not in valid format!");
            //Fail validation
            return false;
        }

        //Password validation

        //If password passes strength test
        if (this.passwordStrength(password)) {
            //Add to form data
            this.form_data["password"] = password;
        }
        //If password does not pass strength test
        else {
            //Report error
            reportError("Password is not strong enough!");
            //Fail validation
            return false;
        }

        //Will only get to this point if all validation tests have passed, so return true
        return true;

    }
}


//When the DOM has loaded, execute an anonymous function
$(document).ready(function () {
    //Instantiate Registration class
    let form = new Registration();

    //When the form is submitted
    $('#registration_form').submit(function (e) {
        //Stop form from being submitted (IE posting data to target normally)
        e.preventDefault();

        //Run method to process form
        form.processForm();
    });
    //Whenever a key has been pressed down in password field
    $("#pwd").keyup(function () {
        //Validate password with value currently in password field
        form.passwordStrength(this.value);
    });
});