$(document).ready(function () {
    $('#').submit(function (e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: '/account/manage_process.php',
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
});