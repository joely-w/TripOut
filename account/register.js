$(document).ready(function () {
    $('#regform').submit(function (e) {
        e.preventDefault();
        let Validation = Validate();
        if (Validation !== false) {
            $.ajax({
                type: "POST",
                url: '/account/reg_process.php',
                data: Validation,
                success: function (response) {
                    const jsonData = JSON.parse(response);

                    if (jsonData.success === 1) {
                        document.getElementById("login").innerHTML = "<h1 class='header'>Account successfully created!</h1>";
                    } else {
                        document.getElementById("errorfield").innerHTML = "<span class='error'>" + jsonData.errors[0] + "</span>"
                    }
                }
            });
        }
    });
});

/**
 * @return {boolean}
 */
function Validate() {
    let formData = {};
    let username = $("#username").val();
    let fullname = $("#name").val();
    let email = $("#email").val();
    if (username.length <= 20) {
        formData["username"] = username;
    } else {
        reportError("Username must be 20 characters or less!");
        return false;
    }
    if (fullname.length <= 255 && !(/\d/.test(fullname))) { //Test if name has any numbers in it
        formData["name"] = fullname;
    } else {
        reportError("Name must be 255 characters or less and contain no numbers!");
        return false;
    }
    if (/\\S+@\\S+/.test(email)) { //Test if email is in correct form
        formData["email"] = email;
    } else {
        reportError("Email not in valid format!");
        return false;
    }
    return formData;
}

function reportError(error_text) {
    document.getElementById("errorfield").innerHTML = "<span class='error'>" + error_text + "</span>"
}

function passwordChecker(password) { //Checks password is of correct standard
    let valid = 0;
    if (password.length >= 8) {
        valid += 1;
        $("#eight").html(`&#10004; Password length is ${password.length} characters`).removeClass("wrong").addClass("correct");
    }
    let passwordCount = countNumbers(password);
    if (passwordCount >= 1) {
        valid += 1;
        $("#numbers").html(`&#10004; Password has ${passwordCount} numbers in it`).removeClass("wrong").addClass("correct");
    }
    if (password.search(/[A-Z]/) !== -1 && password.search(/[a-z]/) !== -1) {
        valid += 1;
        $("#complexity").html(`&#10004; Password has lowercase and uppercase characters in it`).removeClass("wrong").addClass("correct");
    }
    if (valid !== 3) {
        return false;
    }
}

function countNumbers(string) {
    let count = 0;
    for (let i = 0; i < string.length; i++) {
        if (/\d/.test(string[i])) {
            count += 1;
        }
    }
    return count;
}

$(document).ready(function () {
    $("#pw").keyup(function () {
        console.log("cheese");
        passwordChecker(this.value);
    });

});