if (isLoggedIn()['Status'] === true) { //If logged in, show event creation form
    $("#event_form").css("visibility", "visible");
} else { //Else display not logged in notification
    $("#not_logged_in").css("visibility", "visible");
}

class formHandler {
    constructor() {
        this.current_post = ""; //Variable to store the current postcode
        this.content_fields = []; //Array to contain JSON objects containing form data
        this.current_recurrence = ""; //Variable to store the creators latest occurrence choice
        this.number_of_fields = 0; //Variable to keep track of how many sections creator adds
        this.zoom = $("#zoom"); //Elements selected to remove duplicate selectors
        this.line1 = $("#line1");
        this.line2 = $("#line2");
    }

    ImageAppender(array_index, data_value) {
        let image_array = this.content_fields[array_index]['dataSrc']; //Select which object to modify in contentFields
        const index = image_array.indexOf(data_value); //Find if image already exists in object
        if (index > -1) { //If it does then remove it from array
            this.content_fields[array_index].dataSrc.splice(index, 1);
        } else { //If it does not, add it to the array
            this.content_fields[array_index].dataSrc.push(data_value);
        }
    }

    AddImage() {
        let container = document.createElement("div"); //Create container for the image checkboxes
        container.className = "content-el image-upload select-image row"; //Add classes needed for styling
        container.id = (this.number_of_fields).toString(); //Assign ID to container, that will be used to identify place in contentFields, as well as order in content
        let object = this; //Used to avoid confusion with calling "this" as multiple functions
        $.ajax({ //Make an AJAX call to the server to obtain all images that creator has uploaded to their account
            url: "/images/myImages.php",
            type: 'GET',
            dataType: "json",
            success: function (result) {
                let index;
                //Makes sure that if the user adds the image content first that the index used to add image to correct place in contentFields is still correct
                if (object.number_of_fields > 0) {
                    index = object.number_of_fields;
                } else {
                    index = 1;
                }
                //Loop through images that server has sent back
                for (let i = 0; i < result.length; i++) {
                    let img_container = document.createElement("div"); //Create container for image
                    img_container.className = "col-md-2 img-thumb"; //Add necessary classes to image to give checkbox effect and to put into column
                    img_container.innerHTML = `<input type="checkbox" class="image_thumbnail" value="${result[i][1]}" id="${index}-${i}"> 
                                               <label for="${index}-${i}">
                                                <img src="${result[i][0]}" />
                                               </label>`; //Add checkbox to image container, where the value is the image id and the id is the position in content_fields
                    container.appendChild(img_container); //Append to image container and go to next image
                }
            }
        });

        this.content_fields.push({dataType: 'image', dataSrc: []}); //Add object to contentFields with blank array that image keys can be inserted into by the ImageAppender
        $("#usercontent").append(($(container))); //Now that the container is fully populated with images, append to DOM
        this.number_of_fields++; //Increment number of fields in DOM
    }

    AddText() {
        let container = document.createElement("textarea"); //Create textarea
        container.id = 'Text' + this.number_of_fields; //Set textarea's ID
        $("#usercontent").append(container); //Add textarea to DOM
        this.content_fields.push({ //Add data to contentFields
            dataType: 'text',
            dataSrc: new SimpleMDE({element: document.getElementById("Text" + this.number_of_fields)}) //Instantiate SimpleMDE object, which will also turn textarea into full editor
        });
        this.number_of_fields++; //Increment number of fields in DOM
    }

    postCodeLookup(postcode) {
        let error = false;
        if (postcode.length >= 6 && this.current_post !== postcode) { //If postcode right length and has changed from previous lookup
            $.ajax({
                url: "https://api.postcodes.io/postcodes/" + postcode, //Make AJAX call to Postcodes.io
                type: 'GET',
                dataType: 'json',
                async: false,
                statusCode: {
                    404: function () {
                        $("#map").html(`<h3>Postcode not found!</h3>`); //If postcode does not exist, report error
                        error = true;
                    }
                },

            });
        }
        if (!error) {
            this.current_post = postcode;
            this.Location(10, 'addr'); //Display map showing area of postcode
        } else {
            return false;
        }
    }

    Location(chosen_zoom, mode) {
        let address; //Declare address variable
        let marker; //Declare marker location variable
        let resolution = "500x300"; //Resolution for map image
        let map_resource; //Declare variable to store map image source
        if (mode === "addr") { //If data being sent is full address, send google maps full address
            if (this.current_post.length >= 6) { //If postcode is correct length
                address = encodeURI(this.line1.val() + this.line2.val() + "," + $("#county").val() + "," + this.current_post); //Compile all inputs into one address and encode it to be passed properly in URL
                marker = encodeURI("size:mid|color:0xFFFF00|label:Venue|") + address; //Compile information for where the map marker will be location and ensure it is format to be passed in URL
                map_resource = `https://maps.googleapis.com/maps/api/staticmap?center=${address}&size=${resolution}&key=AIzaSyAqt8ejRfMThaP6C3Kfxcd8fN7OpI5RXUc&zoom=${chosen_zoom}&markers=${marker}`; //Compile together into image source
            }
        } else { //If mode is not address, must only be displaying postcode
            address = encodeURI(this.current_post); //Encode postcode to be put in URL
            marker = encodeURI("size:mid|color:0xFFFF00|label:Venue|") + address; //Encode marker properly to be put in URL
            map_resource = `https://maps.googleapis.com/maps/api/staticmap?center=${address}&size=${resolution}&key=AIzaSyAqt8ejRfMThaP6C3Kfxcd8fN7OpI5RXUc&zoom=${chosen_zoom}&markers=${marker}`;//Compile together into image source
        }
        $("#map").html(`<img src="${map_resource}" alt="Map for event"/>`); //Insert image into map container
        this.zoom.val(chosen_zoom); //Update zoom slider so that zoom is consistent with map
    }

    changeOccurrence(occurrence) {
        if (occurrence === this.current_reccurence) {//If new selection is same as old selection, terminate
            return false;
        }
        let container = $("#date");
        this.current_reccurence = occurrence;
        switch (occurrence) { //Handle occurrence cases
            case "once":
                container.html(`<div class="col-md-6"><p>Start date</p><div id="startdate"></div></div><div class="col-md-6 .offset-md-3"><p>End date</p><div id="enddate"></div></div>`);
                $('#startdate').datetimepicker({
                    format: 'DD/MM/YYYY',
                    inline: true,
                    sideBySide: true
                });
                $('#enddate').datetimepicker({
                    format: 'DD/MM/YYYY',
                    inline: true,
                    sideBySide: true,
                    useCurrent: false
                });
                $("#startdate").on("dp.change", function (e) { //When start date is changed, update end dates minimum date
                    $('#enddate').data("DateTimePicker").minDate(e.date);
                });
                $("#enddate").on("dp.change", function (e) { //When end date is changed, update start dates maximum date
                    $('#startdate').data("DateTimePicker").maxDate(e.date);
                });
                break;
            case "weekly":
                container.html(`<label for="recurrence">What day does your event happen on?<select class="select-css" id="day" name="day">
                                        <option selected disabled>Select</option>
                                        <option value="0">Sunday</option>
                                        <option value="1">Monday</option>
                                        <option value="2">Tuesday</option>
                                        <option value="3">Wednesday</option>
                                        <option value="4">Thursday</option>
                                        <option value="5">Friday</option>
                                        <option value="6">Saturday</option>
                                </select></label>`);
                break;
            case "monthly":
                container.html(`<label>Which week in the month the event on?
                                    <select id="week" class="select-css" name="weeknumber">
                                        <option selected disabled>Select</option>
                                        <option value="1">Week 1</option>
                                        <option value="2">Week 2</option>
                                        <option value="3">Week 3</option>
                                        <option value="4">Week 4</option>
                                        <option value="5">Week 5</option>
                                    </select>
                                </label>
                                <label>What day in the week is the event?
                                    <select class="select-css" id="day" name="day">
                                        <option selected disabled>Select</option>
                                        <option value="0">Sunday</option>
                                        <option value="1">Monday</option>
                                        <option value="2">Tuesday</option>
                                        <option value="3">Wednesday</option>
                                        <option value="4">Thursday</option>
                                        <option value="5">Friday</option>
                                        <option value="6">Saturday</option>
                                    </select>
                                </label>`);
                break;
            case "yearly":
                container.html(`<label>What month does your event happen in?
                                    <select class="form-control" id="month">
                                        <option value="1">January</option>
                                        <option value="2">February</option>
                                        <option value="3">March</option>
                                        <option value="4">April</option>
                                        <option value="5">May</option>
                                        <option value="6">June</option>
                                        <option value="7">July</option>
                                        <option value="8">August</option>
                                        <option value="9">September</option>
                                        <option value="10">October</option>
                                        <option value="11">November</option>
                                        <option value="12">December</option>
                                    </select>
                                </label>
                                <label>What day of the month does your event happen on?
                                    <input class="form-control" type="number" min="1" max="31" name="day" id="day"/>
                                </label>`);
                break;
        }

    }
}

class eventHandler {
    constructor(form) { //Pass the form object that will handle all the processing
        this.form = form;
        this.starttime = $("#datetimepicker"); //Declared here to remove duplicate selectors
        this.endtime = $("#datetimepicker1");
    }

    Listener() { //Function to create all event listeners
        let Handler = this; //Done to avoid mixing up scopes since each event hook could have a "this"
        Handler.starttime.datetimepicker({ //Initiate start time clock
            inline: true,
            sideBySide: true,
            format: 'LT'
        });

        Handler.endtime.datetimepicker({ //Initiate end time clock
            inline: true,
            sideBySide: true,
            format: 'LT'
        });

        Handler.starttime.on(
            "dp.change",
            function (e) { //Whenever start time clock changes, update end times clock to have a minimum time of the current value of the start time clock
                Handler.endtime.data("DateTimePicker").minDate(e.date);
            });
        Handler.endtime
            .on(//Whenever end time clock changes, update start times clock to have a maximum time of the current value of the end time clock
                "dp.change",
                function (e) {
                    Handler.starttime.data("DateTimePicker").maxDate(e.date);
                });
        $('#recurrence').on(
            'change',
            function () { //When dropdown changes state
                Handler.form.changeOccurrence(this.value); //Change occurrence options to match new occurrence type
            });

        $("#event_form").submit( //When form is submitted
            function (e) {
                e.preventDefault(); //Stop from submitting the traditional way
                Handler.processForm(); //Call the processForm method
            });

        $("#postcode").on(
            "keyup",
            function () {
                if (this.value.length > 5) { //If postcode length > 5
                    Handler.form.postCodeLookup(this.value); //When key is pressed in postcode, call lookup function with value inside postcode
                }
            });
        Handler.form.zoom.on( //When zoom slider is adjusted
            'change',
            function () {
                if (Handler.form.current_post.length >= 6) { //If postcode is a valid length
                    if (Handler.form.line1.val() !== null) { //If line 1 is not empty, show map in full address mode with new zoom
                        Handler.form.Location(Handler.form.zoom.val(), 'addr') //Show map
                    } else { //If line 1 is empty, show map with just postcode mode and new zoom
                        Handler.form.Location(Handler.zoom.val())
                    }
                }
            });

        $("#addtext").on( //When add text button is clicked, call add text method from the formHandler
            'click',
            function () {
                Handler.form.AddText()
            });

        $("#addimage").on(
            'click',
            function () {//When add image button is clicked, call add image method from the formHandler
                Handler.form.AddImage()
            })
        ;
        Handler.form.line1.on('change', function () { //When line 1 of the address is called, update map to show map including line 1 in the address
            if (Handler.form.current_post.length >= 6 && Handler.form.line1.val() !== null) {
                Handler.form.Location(14, 'addr'); //When creator has stopped inputting into input, update map as a full address
            }
        });
        $('body').on(
            'click',
            'input.image_thumbnail', //When a checkbox from any image selection section is selected or deselected
            function () {
                //Call the ImageAppender with the position in content_fields and the id of the image
                Handler.form.ImageAppender(this.id.split("-")[0] - 1, this.value);
            });
    }

    processForm() {
        let valid = true; //Set form validity to true until found otherwise
        //Process location data and get longitude/latitude of postcode
        let longitude;
        let latitude;
        $.ajax({
            url: "https://api.postcodes.io/postcodes/" + this.form.current_post, //Make AJAX call to API
            type: 'GET',
            dataType: "json",
            async: false, //Finish AJAX function before continuing, means that longitude and latitude can be returned from function
            success: function (res) {
                longitude = res['result']['longitude']; //Store longitude and latitude in variables to be used in return statement
                latitude = res['result']['latitude'];
            },
            error: function () {//If postcode lookup fails, assume it doesn't exist
                valid = false; //Fail validation
                reportError("Postcode not valid!"); //Report error

            }
        });
        let location_data = { //Save data to JSON object for submission
            postcode: this.form.current_post,
            line1: this.form.line1.val(),
            line2: this.form.line2.val(),
            county: $("#county").val(),
            zoom: this.form.zoom.val(),
            lng: longitude,
            lat: latitude
        };

        //Process occurrence
        let occurrence_data = {}; //Declare blank JSON object to append occurence data to
        let starttime = this.starttime.data('date'); //Get the start time and end time from the objects instantiated in formHandler
        let endtime = this.endtime.data('date');
        switch (this.form.current_recurrence) { //Handle the form data depending on what type of occurrence was selected when submit button was clicked
            case "once":
                occurrence_data = {
                    type: "once",
                    startdate: $('#startdate').data('date'),
                    enddate: $('#enddate').data('date'),
                    starttime: starttime,
                    endtime: endtime
                };
                break;
            case "daily":
                occurrence_data = {
                    type: "daily",
                    starttime: starttime,
                    endtime: endtime
                };
                break;
            case "weekly":
                // noinspection JSJQueryEfficiency
                occurrence_data = {
                    type: "weekly",
                    starttime: starttime,
                    endtime: endtime,
                    day: parseInt($('#day').val())
                };
                break;
            case "monthly":
                // noinspection JSJQueryEfficiency
                occurrence_data = {
                    type: "monthly",
                    starttime: starttime,
                    endtime: endtime,
                    week: $('#week').val(),
                    day: $('#day').val()
                };
                break;
            case "yearly":
                // noinspection JSJQueryEfficiency
                occurrence_data = {
                    type: "yearly",
                    starttime: starttime,
                    endtime: endtime,
                    day: $('#day').val(),
                    month: $('#month').val()
                };
                break;
            default:
                reportError("No occurrence option chosen!");
                valid = false;
        }

        //Validate title
        let title = $("#title").val();
        if (title.length > 128) { //Do validation on title
            valid = false;
            reportError("Title is too long! Must be less than 129 characters")
        }

        //Process text
        for (let i = 0; i < this.form.content_fields.length; i++) { //Loop through form content
            if (this.form.content_fields[i].dataType === "text") { //If text then grab text content and replace the id with the content
                this.form.content_fields[i].dataSrc = this.form.content_fields[i].dataSrc.value(); //Replace SimpleMDE objects with content inside text editors
            }
        }
        if (valid) { //If validation has been passed
            $.ajax({
                url: "/events/create_process.php",
                type: "POST",
                data: { //Compile data together
                    eventOccurence: occurrence_data,
                    eventTitle: title,
                    eventLocation: location_data,
                    content: this.form.content_fields
                },
                dataType: "json",
                success: function (response) {
                    if (response['success'] === 1) {
                        $("#event_form").html("<h1>Event successfully created!"); //Report success
                    } else {
                        reportError(response['errors'][0]) //If event was not submitted report error
                    }
                },
            });
        }
    }
}

$(document).ready(function () { //When the DOM is ready, execute anonymous function
    let event_listener = new eventHandler(new formHandler()); //Instantiate class
    event_listener.Listener(); //Start listening for any of the form actions to be triggered
});


