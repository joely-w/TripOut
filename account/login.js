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
                        window.location.href = "/account/login.php?failed";
                }
            }
        });
    });
    $('#otherLogin').submit(function (e) { /*For actual login form page*/
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
                    $("#errorfield").html("<div class='alert alert-danger' role='alert'>"+jsonData.errors+"</div>");
                }
            }
        });
    });
});