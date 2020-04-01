class displayEvent {
    constructor() {
        //Set current vote to null
        this.current_vote = null;

        //Declare lookup table to convert integers to week day names
        this.week_lookup = {
            0: "Sunday",
            1: "Monday",
            2: "Tuesday",
            3: "Wednesday",
            4: "Thursday",
            5: "Friday",
            6: "Saturday"
        };

        //Get URL parameters
        const url = new URL(window.location.href);

        //Find ID parameter in URL and set it as event ID
        this.id = url.searchParams.get("id");
    }

    splitWords(word) {
        //Split word by capital letters, used to convert keys into correctly spelt text (occurrence)
        return word.replace(/([a-z])([A-Z])/g, '$1 $2');
    }

    fillOccurrence(occurrence_data) {
        //Declare variable to store current occurrence field in loop
        let occurrence_field;

        //Create blank string to append occurrence HTML content to
        let occurrence_content = "";

        //Loop through occurrence information
        for (let key in occurrence_data) {

            //If key has a property (needed to eliminate prototype array)
            if (occurrence_data.hasOwnProperty(key)) {

                //Handle occurrence cases
                switch (key) {
                    case "DayOfWeek":
                        //Convert day of the week from integer to actual day
                        occurrence_field = this.week_lookup[occurrence_data[key]];
                        break;
                    case "OccurrenceType":
                        //Convert occurrence type to uppercase
                        occurrence_field = occurrence_data[key].replace(/^\w/, c => c.toUpperCase());
                        break;
                    case "StartTime":
                    case "EndTime":
                        //Get rid of seconds part of time object
                        occurrence_field = occurrence_data[key].slice(0, -3);
                        break;
                    default:
                        occurrence_field = occurrence_data[key];
                }

                //Add generated information to occurrence content within a HTML structure
                occurrence_content += `<div class="occurrence-section"><span>${this.splitWords(key)}: </span> ${occurrence_field}</div>`;
            }
        }

        //Write the final compiled HTML to the occurrence container
        $("#occurrence").html(occurrence_content);
    }

    fillLikes(like_data) {
        console.log(like_data);
        //Declare variables to store like and dislike icons CSS classes in
        let like_class;
        let dislike_class;

        //Update classes vote to the users current vote
        this.current_vote = parseInt(like_data['User']);

        switch (like_data['User']) {

            case -1: //User has not liked or disliked event
            case false: //User is not logged in
                //Set both icons to non-filled
                dislike_class = "far";
                like_class = "far";
                break;

            case 1:
                //Set like icon to solid filled
                dislike_class = "far";
                like_class = "fas";
                break;

            case 0:
                //Set dislike icon to solid filled
                dislike_class = "fas";
                like_class = "far";
        }

        //Generate like/dislike icons content and write it to icons container
        $("#icons").html(`<a id="like">
                            <i class="${like_class} fa-3x fa-thumbs-up"></i>
                          </a>
                          <span id="like-count">
                            ${like_data['Likes']}
                          </span>
                          <a id="dislike">
                            <i class="${dislike_class} fa-3x fa-thumbs-down"></i>
                          </a>
                          <span id="dislike-count">
                            ${like_data['Dislikes']}
                          </span>`);
    }

    fillMap(map_data) {
        //Generate address
        let address = map_data["Line1"] + "," + map_data["Line2"] + "," + map_data["Town"] + "," + map_data["PostCode"];

        //Generate settings for marker on map
        let marker = encodeURI("size:mid|color:0xFFFF00|label:Venue|" + address);

        //Generate image source
        let image_source = `https://maps.googleapis.com/maps/api/staticmap?center=${encodeURI(address)}&size=800x400&key=AIzaSyAqt8ejRfMThaP6C3Kfxcd8fN7OpI5RXUc&zoom=${map_data['Zoom']}&markers=${marker}`;

        //Add map image to map container
        $("#map").html(`<img src='${image_source}' alt="Map of Event" />`);
        //Add event address to address container
        $("#address").html(`<span class="first">${map_data["Line1"]}</span>
                            <span>${map_data["Line2"]}</span>
                            <span>${map_data['Town']}</span>
                            <span>${map_data['PostCode']}</span>
                            <a class="btn btn-primary" target="_blank" href="https://www.google.com/maps/dir/?api=1&destination=${encodeURI(address)}">Get directions</a>`);
    }

    fillContent(content_data) {
        //Declare variable to store the compiled HTML content of the various event content sections
        let compiled_content = "";

        //Loop through event content to compile each section
        for (let i = 0; i < content_data.length; i++) {

            //Handle images
            if (content_data[i]['Datatype'] === "Image") {

                //If image is first in row, add a row container
                if (content_data[i - 1]['Datatype'] !== "Image") {
                    //Add row container to content
                    compiled_content += `<div class="image-row">`;
                }

                //Add image to content
                compiled_content += `<div class="event-image">
                                        <a onclick="Modal('${content_data[i]['Source']}')">
                                            <img alt="" src="${content_data[i]['Source']}" />
                                        </a>
                                      </div>`;

                //If not at end of data and next content is not an image, close image row
                if (i < content_data.length - 1 && content_data[i + 1]['Datatype'] !== "Image") {
                    compiled_content += `</div>`;
                }
            }

            //Handle text
            else if (content_data[i]['Datatype'] === "Text") {

                //Instantiate markdown parse
                let converter = new showdown.Converter(),
                    text = content_data[i]['Source'],
                    //Parse markdown
                    html = converter.makeHtml(text);

                //Add parsed markdown to content
                compiled_content += html;
            }
        }
        //Write compiled content HTML to event content container
        $("#event-content").html(compiled_content);
    }

    getData() {
        //Declare class in variable so as to avoid scope issues with "this" in AJAX call
        let current_class = this;

        //Get event data from server
        $.ajax({
            type: "POST",
            url: 'single_event_process.php',
            data: {
                post_status: "event",
                event_id: this.id
            },
            dataType: "json",

            //If the call to the server is successful, process data and display
            success: function (response) {

                //Declare event data from response
                let event_data = response["event_data"];

                //Update page title
                document.title = "TripOut - " + event_data['Title'];
                $("#title").text(event_data['Title']);

                //Write how many views the event has to the views container
                $("#views").html(event_data['Views']['Views'] + " views");

                //Call method to handle occurrence information creation
                current_class.fillOccurrence(event_data['Occurrence']);

                //Call method to likes/dislikes information
                current_class.fillLikes(event_data['Popularity']);

                //Call method to insert map and handle address information
                current_class.fillMap(event_data['Location']);

                //Call method to handle custom event content
                current_class.fillContent(event_data['Content']);
            }
        });
    }

    submitVote(status) {

        //Declare class in variable so as to avoid scope issues with "this" in AJAX call
        let current_class = this;
        //Send like/dislike to server to process
        $.ajax({
            type: "POST",
            url: 'single_event_process.php',
            data: {
                post_status: "popularity",
                like_type: status,
                event_id: this.id
            },
            dataType: "json",
            //On success of call, process data
            success: function (response) {

                //Declare variable to store whether the vote was successful
                let server_status = response['status'];

                //Declare event selectors to avoid duplicate selectors
                let thumb_up = $(".fa-thumbs-up");
                let thumb_down = $(".fa-thumbs-down");
                let like_count = $("#like-count");
                let dislike_count = $("#dislike-count");

                //Handle the server response
                switch (server_status) {

                    //If the user liked the event
                    case "like":

                        //If the users current vote is not a like
                        if (current_class.current_vote !== 1) {
                            //Increment like counter by one on page
                            like_count.text(parseInt(like_count.text()) + 1);

                            //Make like icon solid fill
                            thumb_up.removeClass("far").addClass("fas");
                        }
                        //If users current vote is dislike (could not exist at all)
                        if (current_class.current_vote === 0) {
                            //Remove solid fill from dislike button
                            thumb_down.removeClass("fas").addClass("far");

                            //Subtract one from dislike counter
                            dislike_count.text(parseInt(dislike_count.text()) - 1);
                        }

                        //Update users current vote to like
                        current_class.current_vote = 1;
                        break;

                    //If the user disliked the event
                    case "dislike":
                        //If the users current vote is not a dislike
                        if (current_class.current_vote !== 0) {
                            //Increment dislike counter by one on page
                            dislike_count.text(parseInt(dislike_count.text()) + 1);

                            //Make dislike icon solid fill
                            thumb_down.removeClass("far").addClass("fas");
                        }

                        //If users current vote is like (could not exist at all)
                        if (current_class.current_vote === 1) {
                            //Remove solid fill from like button
                            thumb_up.removeClass("fas").addClass("far");

                            //Subtract one from like counter
                            like_count.text(parseInt(like_count.text()) - 1);
                        }
                        //Update users current vote to dislike
                        current_class.current_vote = 0;
                        break;
                }
            }
        });
    }
}

//When the document is ready, execute anonymous function
$(document).ready(function () {
    //Instantiate displayEvent class
    let event = new displayEvent();

    //Call method to load event data to page
    event.getData();
    //Declare icons selectors
    let icons = $("#icons");

    //Declare event listener on the like icon to call the submitVote method should it be clicked
    icons.on('click', '#like', function () {
        event.submitVote("like");
    });

    //Declare event listener on the dislike icon to call the submitVote method should it be clicked
    icons.on('click', '#dislike', function () {
        event.submitVote("dislike");
    });
});