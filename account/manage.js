function updateValue(field, value) { /*Call the update method in the API*/
    console.log(field, value);
    data = ["update", field, value];
    $.ajax({
        type: "POST",
        url: '/account/manage_process.php',
        data: {
            data: data
        },
        success: function (response) {
            console.log(response);
            var jsonData = JSON.parse(response);
            if (jsonData.success === 1) {
                $("#report").html("<div class='alert alert-success' role='alert'> " + field + " has been updated!</div>");
                setTimeout(function () {
                    document.getElementById("report").innerHTML = "";
                }, 3000);
            } else {
                document.getElementById("report").innerHTML = "<span class='error'>" + jsonData.error + "</span>";
            }
        }
    });
}

function LogOut() { /*Call the logout method in the API*/
    data = ["logout"];
    $.ajax({
        type: "POST",
        url: '/account/manage_process.php',
        data: {
            data: data
        },
        success: function (response) {
            var jsonData = JSON.parse(response);
            if (jsonData.logout === 1) {
                location.reload()
            }
        }
    });
}

