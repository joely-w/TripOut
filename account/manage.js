class Manage {
    updateValue(field, value) {
        //Send field and value to backend page to process
        $.ajax({
            type: "POST",
            url: '/account/manage_process.php',
            data: {
                Command: "update",
                Field: field,
                Value: value
            },
            dataType: "json",
            success: function (response) {
                //If updating field has succeeded
                if (response['success'] === 1) {
                    //Report success to user
                    $("#report").html(`<div class='alert alert-success' role='alert'> ${field} has been updated!</div>`);

                    //Remove success report after three seconds
                    setTimeout(function () {
                        location.reload()
                    }, 3000);
                }
                //If updating has failed
                else {
                    //Show first error provided by backend
                    $(`#report`).html(`<div class="alert alert-danger" role="alert"> Field not updated! ${response['errors'][0]} </div>`);
                }
            }
        });
    }

    logOut() {
        $.ajax({
            type: "POST",
            url: '/account/manage_process.php',
            data: { //Logout user
                Command: "logout"
            },
            dataType: "json",
            success: function (response) {
                if (response["logout"] === 1) { //If login succeeds reload page
                    location.reload()
                }
            }
        });
    }

    myImages() {
        //Get images from backend
        $.ajax({
            type: "GET",
            url: '/images/myImages.php',
            dataType: "json",
            success: function (response) {
                //Loop through images array to append images to container
                for (let i = 0; i < response.length; i++) {
                    //First element in each sub array is the image path
                    let image_source = response[i][0];

                    //Add image to images container, along with link to open up modal when image is clicked
                    $("#images").append(`<div class="col-md-2 img-thumb">
                                        <a onclick="Modal('${image_source}')">
                                            <img src="${image_source}" alt=""/>
                                        </a>
                                      </div>`);

                }
            }
        });
    }

    uploadImage() {
        let current_class = this;
        //Make progress bar visible
        $(".progress").css("display", "block");
        //Send data to server
        $.ajax({
            type: 'POST',
            url: '/images/upload_image_process.php',
            //The form with the file inputs
            data: new FormData($('#image_upload')[0]),
            //jQuery shouldn't process the data as FormData is being used
            processData: false,
            contentType: false,
            dataType: "json",
            //While the AJAX call is being made
            xhr: function () {
                const xhr = new window.XMLHttpRequest();
                //Try and get upload progress
                xhr.upload.addEventListener("progress", function (evt) {
                    //If there is an upload going on
                    if (evt.lengthComputable) {
                        //Pass upload progress (in percent) to function that updates progress bar
                        current_class.progressBar(Math.round(evt.loaded / evt.total * 100));
                    }
                }, false);
                return xhr;
            },
            success: function (response) {
                //If image has been uploaded
                if (response['success'] === 1) {
                    //Report success
                    $("#upload-notifications").html(`<div class="alert alert-success" role="alert"> Image has been uploaded! </div>`);
                    //Clear images pane
                    $("#images").html(null);
                    //Refresh images in images tab with new uploaded images now included
                    current_class.myImages();
                }
                //If image has not been uploaded
                else {
                    //Report first error
                    $("#upload-notifications").html(`<div class="alert alert-danger" role="alert">Image upload failed! ${response['errors'][0]}</div>`);
                }
            }
        });
    }

    getAccountDetails() { //Fill inputs with users account information
        $.ajax({
            type: "POST",
            url: '/account/manage_process.php',
            data: {//Get account information from API
                Command: "account_fields"
            },
            dataType: "json",
            success: function (response) {
                //Update inputs with session values obtained from API
                $("#passwd").val("PasswordString"); //Password should not be viewable so a template string is used instead
                $("#username").val(response['Username']);
                $("#email").val(response['Email']);
            }
        });
    }

    progressBar(percent) {
        //Select progress bar in DOM
        let progress = $("#progress");

        //Set width to the percent complete so user can visually see how much is done
        progress.width(percent + "%");

        //Set progress text to percent complete so user can see what percent is complete
        progress.text(percent + "%");
    }

    validateImage() {
        //Select input that contains image
        let image = $("#file_upload");
        //Get filename of selected file
        let filename = image.val().split('\\').pop();
        //Get extension of file
        let extension = filename.replace(/^.*\./, '');
        //Declare allowed file extensions
        let allowed_extensions = ["jpg", "jpeg", "png"];
        //If extension is not allowed, converts to lowercase so that capital extensions are allowed (Eg. JPG as well as jpg)
        if (allowed_extensions.indexOf(extension.toLowerCase()) < 0) {
            //Report failure and display error
            $(`#report`).html(`<div class="alert alert-danger" role="alert">File format not supported. Needs to be jpg, jpeg or png!</div>`);
            return false;
        }
        //If image size is greater than 20MB
        if (image[0].files[0].size > 20000000) {
            //Report failure and display error
            $(`#report`).html(`<div class="alert alert-danger" role="alert">File is too big, must be 20MB or less!</div>`);
            return false;
        }
        //If validation has been passed, show filename so that user knows it has been selected and return true
        $("#files").text(filename);
        return true;
    }

    eventHandlers() {
        let current_class = this;

        //If username,password or email updated, call update function
        $("#username").change(function () {
            current_class.updateValue("Username", this.value);
        });
        $("#email").change(function () {
            current_class.updateValue("Email", this.value);
        });
        $("#passwd").change(function () {
            current_class.updateValue("Password", this.value);
        });

        //When form is submitted
        $("#image_upload").submit(function (e) {
            e.preventDefault(); //Stop form submitting traditionally
            current_class.uploadImage(); //Call image upload function
        });
        $("#file_upload").on("change", function () { //When user selects file
            if (!current_class.validateImage()) {//If file validation fails
                $("#file_upload").val(null); //Reset input field
            }
        });
        $("#logout").click(function () {
            current_class.logOut();
        });

        //Call image filling function and account details filling function
        current_class.myImages();
        current_class.getAccountDetails();
    }
}


$(document).ready(function () {
    let form = new Manage();
    //Run method that will listen for event actions
    form.eventHandlers();
});