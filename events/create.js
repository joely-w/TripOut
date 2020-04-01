if (isLoggedIn()['Status'] === true) { //If logged in, show event creation form
    $("#event_form").css("visibility", "visible");
} else { //Else display not logged in notification
    $("#not_logged_in").css("visibility", "visible");
}
class Create {
    constructor() {
        //Declare variable to store the current postcode
        this.current_post = "";

        //Declare array to contain JSON objects containing form data
        this.content_fields = [];

        //Declare variable to store the creators latest occurrence choice
        this.current_recurrence = "";

        //Declare variable to keep track of how many sections creator adds
        this.number_of_fields = 0;

        //Declare selectors to remove duplicate selectors
        this.zoom = $("#zoom");
        this.line1 = $("#line1");
        this.line2 = $("#line2");

        //Declare time selectors as attributes to remove duplicate selectors
        this.starttime = $("#datetimepicker");
        this.endtime = $("#datetimepicker1");
    }

    imageAppender(array_index, data_value) {
        //Select which object to modify in content_fields
        let image_array = this.content_fields[array_index].data_src;

        //Find if image already exists in object
        const index = image_array.indexOf(data_value);

        //If image does exist in array
        if (index > -1) {
            //Remove image from array
            this.content_fields[array_index].data_src.splice(index, 1);
        }
        //If image does not exist in array
        else {
            //Add image to array
            this.content_fields[array_index].data_src.push(data_value);
        }
    }

    addImage() {
        //Create a container to store dialogue in
        let container = document.createElement("div");

        //Add classes needed for styling
        container.className = "content-el image-upload select-image row";

        //Assign ID to container, that will be used to identify place in content_fields, as well as order in content
        container.id = (this.number_of_fields).toString();

        //Declare variable for class to avoid issues with scope of "this"
        let object = this;

        //Make an AJAX call to the server to obtain all images that creator has uploaded to their account
        $.ajax({
            url: "/images/myImages.php",
            type: 'GET',
            dataType: "json",
            success: function (result) {
                //Declare index variable
                let index;

                //Ensure that index in content_fields is always correct
                if (object.number_of_fields > 0) {
                    index = object.number_of_fields;
                } else {
                    index = 1;
                }

                //Loop through images that server has sent back
                for (let i = 0; i < result.length; i++) {
                    //Create container for image
                    let img_container = document.createElement("div");

                    //Add necessary classes to image to give checkbox effect and to put into column
                    img_container.className = "col-md-2 img-thumb";

                    //Add checkbox to image container, where the value is the image id and the id is the position in content_fields
                    img_container.innerHTML = `<input type="checkbox" class="image_thumbnail" value="${result[i][1]}" id="${index}-${i}"> 
                                               <label for="${index}-${i}">
                                                <img src="${result[i][0]}"  alt=""/>
                                               </label>`;

                    //Add to image container and go to next image
                    container.appendChild(img_container);
                }
            }
        });

        //Add JSON object to content_fields with blank array that image keys can be inserted into by the ImageAppender
        this.content_fields.push({data_type: 'image', data_src: []});

        //Now that the container is fully populated with images, append to DOM
        $("#usercontent").append(($(container)));

        //Increment number of fields in DOM
        this.number_of_fields++;
    }

    addText() {
        //Create textarea
        let container = document.createElement("textarea");

        //Set textarea's ID
        container.id = 'Text' + this.number_of_fields;

        //Add textarea to DOM
        $("#usercontent").append(container);

        //Add data to contentFields
        this.content_fields.push({
            data_type: 'text',
            //Instantiate SimpleMDE object, which will also turn textarea into full editor
            data_src: new SimpleMDE({element: document.getElementById("Text" + this.number_of_fields)})
        });

        //Increment number of fields in DOM
        this.number_of_fields++;
    }

    postCodeLookup(postcode) {
        //Declare validity of data as true initially
        let error = false;

        //If postcode right length and has changed from previous lookup
        if (postcode.length >= 6 && this.current_post !== postcode) {
            //Make AJAX call to Postcodes.io
            $.ajax({
                url: "https://api.postcodes.io/postcodes/" + postcode,
                type: 'GET',
                dataType: 'json',
                async: false,
                statusCode: {
                    //If postcode does not exist
                    404: function () {
                        //Report that postcode does not exist
                        $("#map").html(`<h3>Postcode not found!</h3>`);
                        //Set data validity to false
                        error = true;
                    }
                },
            });
        }
        //If data is valid
        if (!error) {
            //Update current_postcode
            this.current_post = postcode;

            //Display map showing area of postcode
            this.Location(10, 'addr');
        }
        //If data is not valid
        else {
            //Fail function
            return false;
        }
    }

    Location(chosen_zoom, mode) {
        //Declare address and marker variables
        let address;
        let marker;

        //Declare the resolution for the map image
        let resolution = "500x300";

        //Declare variable to store map image source
        let map_resource;

        //If mode is full address
        if (mode === "addr") {
            //If postcode is correct length
            if (this.current_post.length >= 6) {
                //Compile all inputs into one address and encode it to be passed properly in URL
                address = encodeURI(this.line1.val() + this.line2.val() + "," + $("#county").val() + "," + this.current_post);

                //Compile information for where the map marker will be location and ensure it is format to be passed in URL
                marker = encodeURI("size:mid|color:0xFFFF00|label:Venue|") + address;

                //Compile together into image source
                map_resource = `https://maps.googleapis.com/maps/api/staticmap?center=${address}&size=${resolution}&key=AIzaSyAqt8ejRfMThaP6C3Kfxcd8fN7OpI5RXUc&zoom=${chosen_zoom}&markers=${marker}`;
            }
        }
        //If mode address
        else {
            //Encode postcode to be put in URL
            address = encodeURI(this.current_post);

            //Encode marker properly to be put in URL
            marker = encodeURI("size:mid|color:0xFFFF00|label:Venue|") + address;

            //Compile together into image source
            map_resource = `https://maps.googleapis.com/maps/api/staticmap?center=${address}&size=${resolution}&key=AIzaSyAqt8ejRfMThaP6C3Kfxcd8fN7OpI5RXUc&zoom=${chosen_zoom}&markers=${marker}`;
        }
        //Write image to map section
        $("#map").html(`<img src="${map_resource}" alt="Map for event"/>`);

        //Update zoom slider so that zoom is consistent with map
        this.zoom.val(chosen_zoom);
    }

    changeOccurrence(occurrence) {
        //If new selection is same as old selection
        if (occurrence === this.current_recurrence) {
            //Terminate
            return false;
        }
        //Declare container selector
        let container = $("#date");

        //Set current recurrence to chosen occurrence
        this.current_recurrence = occurrence;

        //Handle occurrence cases
        switch (occurrence) {
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

                //When start date is changed
                $("#startdate").on("dp.change", function (e) {
                    //Update end dates minimum date
                    $('#enddate').data("DateTimePicker").minDate(e.date);
                });

                //When end date is changed
                $("#enddate").on("dp.change", function (e) {
                    //Update start dates maximum date
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

    Listener() {
        //Assign class to variable to stop "this" being used in wrong scope
        let handler = this;

        //Initiate start time clock
        handler.starttime.datetimepicker({
            inline: true,
            sideBySide: true,
            format: 'LT'
        });

        //Initiate end time clock
        handler.endtime.datetimepicker({
            inline: true,
            sideBySide: true,
            format: 'LT'
        });

        //Whenever start time clock changes
        handler.starttime.on(
            "dp.change",
            function (e) {
                //Update end times clock to have a minimum time of the current value of the start time clock
                handler.endtime.data("DateTimePicker").minDate(e.date);
            });

        //Whenever end time clock changes
        handler.endtime
            .on(
                "dp.change",
                function (e) {
                    //Update start times clock to have a maximum time of the current value of the end time clock
                    handler.starttime.data("DateTimePicker").maxDate(e.date);
                });

        //When dropdown changes state
        $('#recurrence').on(
            'change',
            function () {
                //Change occurrence options to match new occurrence type
                handler.changeOccurrence(this.value);
            });

        //When form is submitted
        $("#event_form").submit(
            function (e) {
                //Stop from submitting the traditional way
                e.preventDefault();

                //Call the processForm method
                handler.processForm();
            });

        //When a key is pressed in the postcode field
        $("#postcode").on(
            "keyup",
            function () {
                //If postcode length is greater than 5
                if (this.value.length > 5) {
                    //Call lookup function with value inside postcode
                    handler.postCodeLookup(this.value);
                }
            });

        //When zoom slider is adjusted
        handler.zoom.on(
            'change',
            function () {
                //If postcode is a valid length
                if (handler.current_post.length >= 6) {
                    //If line 1 is not empty
                    if (handler.line1.val() !== null) {
                        //Show map in full address mode with new zoom
                        handler.Location(handler.form.zoom.val(), 'addr')
                    }
                    //If line 1 is empty
                    else {
                        //Show map with just postcode mode and new zoom
                        handler.Location(handler.zoom.val())
                    }
                }
            });

        //When add text button is clicked
        $("#addtext").on(
            'click',
            function () {
                //Call add text method from the formHandler
                handler.addText()
            });

        //When add image button is clicked
        $("#addimage").on(
            'click',
            function () {
                //Call add image method from the formHandler
                handler.addImage()
            });

        //When line 1 of the address is changed
        handler.line1.on('change', function () {
            //If postcode is valid and line 1 is not empty
            if (handler.current_post.length >= 6 && handler.line1.val() !== null) {
                //Show map in full address mode
                handler.Location(14, 'addr');
            }
        });

        //If any image checkbox is selected
        $('body').on(
            'click',
            'input.image_thumbnail',
            function () {
                //Call the ImageAppender with the position in content_fields and the id of the image
                handler.imageAppender(this.id.split("-")[0] - 1, this.value);
            });
    }

    processForm() {
        //Set form validity to true until found otherwise
        let valid = true;

        //Declare variables to store longitude and latitude in
        let longitude;
        let latitude;

        //Make AJAX call to Postcodes.io API
        $.ajax({
            url: "https://api.postcodes.io/postcodes/" + this.current_post,
            type: 'GET',
            dataType: "json",
            //Finish AJAX function before continuing, means that longitude and latitude can obtained
            async: false,
            success: function (res) {
                //Store longitude and latitude in variables to be used in return statement
                longitude = res['result']['longitude'];
                latitude = res['result']['latitude'];
            },
            //If postcode lookup fails
            error: function () {
                //Fail validation
                valid = false;
                //Report error
                reportError("Postcode not valid!");
            }
        });
        //Save data to JSON object for submission
        let location_data = {
            postcode: this.current_post,
            line1: this.line1.val(),
            line2: this.line2.val(),
            town: $("#county").val(),
            zoom: this.zoom.val(),
            lng: longitude,
            lat: latitude
        };

        //Process occurrence

        //Declare blank JSON object to append occurence data to
        let occurrence_data = {};

        //Get the start time and end time from the objects instantiated in formHandler
        let start_time = this.starttime.data('date');
        let end_time = this.endtime.data('date');

        //Handle the form data depending on what type of occurrence was selected when submit button was clicked
        switch (this.current_recurrence) {
            case "once":
                occurrence_data = {
                    type: "once",
                    start_date: $('#startdate').data('date'),
                    end_date: $('#enddate').data('date'),
                    start_time: start_time,
                    end_time: end_time
                };
                break;
            case "daily":
                occurrence_data = {
                    type: "daily",
                    start_time: start_time,
                    end_time: end_time
                };
                break;
            case "weekly":
                occurrence_data = {
                    type: "weekly",
                    start_time: start_time,
                    end_time: end_time,
                    day: parseInt($('#day').val())
                };
                break;
            case "monthly":
                occurrence_data = {
                    type: "monthly",
                    start_time: start_time,
                    end_time: end_time,
                    week: $('#week').val(),
                    day: $('#day').val()
                };
                break;
            case "yearly":
                occurrence_data = {
                    type: "yearly",
                    start_time: start_time,
                    end_time: end_time,
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
        //If title length is greater than 128 characters
        if (title.length > 128) {
            //Fail validation
            valid = false;
            //Report error
            reportError("Title is too long! Must be less than 129 characters")
        }


        //Process text

        //Loop through form content
        for (let i = 0; i < this.content_fields.length; i++) {
            //If the datatype is text
            if (this.content_fields[i].data_type === "text") {
                //Replace SimpleMDE objects with content inside text editors
                this.content_fields[i].data_src = this.content_fields[i].data_src.value();
            }
        }

        //If validation has been passed
        if (valid) {
            $.ajax({
                url: "/events/create_process.php",
                type: "POST",
                //Compile data together
                data: {
                    event_occurrence: occurrence_data,
                    event_title: title,
                    event_location: location_data,
                    content: this.content_fields
                },
                dataType: "json",
                success: function (response) {
                    if (response['success'] === 1) {
                        //Report success
                        $("#event_form").html("<h1>Event successfully created!");
                    } else {
                        //If event was not submitted report error (server side error)
                        reportError(response['errors'][0])
                    }
                },
            });
        }
    }
}


//When the DOM is ready, execute anonymous function
$(document).ready(function () {
    //Instantiate class
    let form = new Create();

    //Start listening for any of the form actions to be triggered
    form.Listener();
});

