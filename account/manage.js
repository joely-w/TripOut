function updateValue(field, value) {
    console.log(field);
    data = ["update", field, value];
    $.ajax({
        type: "POST",
        url: '/account/manage_process.php',
        data: {data: data},
        success: function (response) {
            var jsonData = JSON.parse(response);
            if (jsonData.success === 1) {
                $("#report").html("<div class='alert alert-success' role='alert'> " + field + " has been updated!</div>");
            } else {
                document.getElementById("report").innerHTML = "<span class='error'>" + jsonData.error + "</span>"
            }
        }
    });
}


function LogOut() {
    data = ["logout"];
    $.ajax({
        type: "POST",
        url: '/account/manage_process.php',
        data: {data: data},
        success: function (response) {
            var jsonData = JSON.parse(response);
            if (jsonData.logout === 1) {
                location.reload()
            }
        }
    });
}