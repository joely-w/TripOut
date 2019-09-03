$(document).ready(function () {
    $('#loginform').submit(function (e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: '/account/login_process.php',
            data: $(this).serialize(),
            success: function (response) {
                var jsonData = JSON.parse(response);

                if (jsonData.success === 1) {
                    location.reload(); /*Reload page to load user info*/
                } else {
                    document.getElementById("errorfield").innerHTML = "<span class='error'>" + jsonData.errors[0] + "</span>"
                }
            }
        });
    });
    $('#regform').submit(function (e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: '/account/reg_process.php',
            data: $(this).serialize(),
            success: function (response) {
                var jsonData = JSON.parse(response);

                if (jsonData.success === 1) {
                    document.getElementById("login").innerHTML = "<h1 class='header'>Account successfully created!</h1>";
                } else {
                    document.getElementById("errorfield").innerHTML = "<span class='error'>" + jsonData.errors[0] + "</span>"
                }
            }
        });
    });
});