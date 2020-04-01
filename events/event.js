class fillEvent {
    constructor() {
        this.occurrence_content = {};
        this.map_content = {};
        this.event_content = {};
    }

    getID() {
        //Method used to get "ID" parameter from URL
        const url_string = window.location.href;
        const url = new URL(url_string);
        return url.searchParams.get("id");
    }

    Popularity(likes, dislikes, status) {
        $('#likes').append(likes);
        $("#dislikes").append(dislikes);
        if (status === "like") {
            $("#likes").classList = "fas fa-3x fa-thumbs-up";
        } else if (status === "dislike") {
            $("#dislikes").classList = "fas fa-3x fa-thumbs-down";
        }
    }

    submitLike(status) {
        //Method to submit either a like or dislike from the user to the server

        //If user is logged in, submit like/dislike to server
        if (isLoggedIn()['Status'] === true) {
            $.ajax({
                type: "POST",
                url: 'single_event_process.php',
                data: {
                    post_status: "popularity",
                    like_type: status,
                    event_id: this.getID()
                },
                dataType: "json",
                success: function (response) {
                    if (response['status'] === "error") {
                        console.log("Error");
                    } else {
                        $("#thumb_down").toggleClass("far fas");
                        $("#thumb_up").toggleClass("far fas");
                    }
                }
            });
        } else {
            //If not logged in, report error
            reportError("You must be logged in to like or dislike an event!")
        }
    }

    generateOccurrence(occurrence) {
        let occurrence_content;

        // Declare variable to store occurrence HTML content
        let occurrence_array = occurrence["Source"];

        //Loop through occurrence information
        for (const occurrence_key in occurrence_array) {
            //Check key exists in array (to avoid unintended iteration over array)
            if (occurrence_array.hasOwnProperty(occurrence_key)) {
                //Split each key into readable word so that it can be used as a label
                let readable_key = SplitWords(occurrence_key);
                //Generate HTML content and add to occurrence_content
                occurrence_content += `<span class='descriptor'>
                                                ${SplitWords(readable_key)}
                                            </span>
                                                ${occurrence_array[occurrence_key]}`;
            }
        }
        return occurrence_content;
    }

    generatePanel(popularity, location, views) {
        let location_data = location["Line1"] + "," + location["Line2"] + "," + location["Town"] + "," + location["PostCode"];
        let marker = encodeURI("size:mid|color:0xFFFF00|label:Venue|" + location_data);
        let map_resource = `https://maps.googleapis.com/maps/api/staticmap?center=${encodeURI(location)}&size=800x300&key=AIzaSyAqt8ejRfMThaP6C3Kfxcd8fN7OpI5RXUc&zoom=${event_data["Location"]['Zoom']}&markers=${marker}`;
        return `<div class="col-md-6"><img src='${map_resource}' /></div><div id="address" class="address col-md-6"><span class='bold'>Address:</span>${location_data.replace(/,/g, '<br />')} <br /><a class='btn btn-primary' target='_blank' href='https://www.google.com/maps/dir/?api=1&destination=${encodeURI(location_data)}'>Get directions</a><br /><p class="views">${views} views </p>${like_icons}</div>`;
    }

    getEvent() {
        //Assign class a new variable as scope of "this" will be different in ajax call
        let event_form = this;

        $.ajax({
            type: "POST",
            url: 'single_event_process.php',
            data: {
                post_status: "event",
                event_id: this.getID()
            },
            async: false, //Make call asynchronous so that code will not continue until call has been complete
            dataType: "json",
            success: function (response) {
                //Put event_data into easier to read name
                let event_data = response["event_data"];
                let map_resource;
                let like_icons;

                //Update page title
                document.title = "TripOut - " + event_data['Title'];
                $("#title").html(event_data['Title']);


                //Handle location options

                event_form.occurrence_content = event_form.generateOccurrence(event_data['Occurrence']);
                event_form.event_content = event_form.generateContent(event_data['Content']);
                event_form.map_content = event_form.generatePanel(event_data['Popularity'], event_data['Location'], event_data['Views']['Views']);
            }
        });
    }

    fillContent(occurrence, map, content) {
        $("#occurrence").html(occurrence);
        $("#map").html(map);
        $("#content").html(content);
    }
}

$(document).ready(function () {
    let fill_event = new fillEvent();
    fill_event.getEvent();
    fill_event.fillContent(fill_event.occurrence_content, fill_event.map_content, fill_event.event_content)
});
