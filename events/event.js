function getID() { //Because getting GET parameters from URL isn't nice in JavaScript
    const url_string = window.location.href;
    const url = new URL(url_string);
    return url.searchParams.get("id");
}

/**
 * @return {string}
 */
function SplitWords(word) { //Split word by capital letters, used to convert keys into correctly spelt text (occurence)
    return word.replace(/([A-Z])/g, '$1').trim()
}

function Popularity(likes, dislikes, status) {
    $('#likes').append(likes);
    $("#dislikes").append(dislikes);
    if (status === "like") {
        $("#likes").classList = "fas fa-3x fa-thumbs-up";
    } else if (status === "dislike") {
        $("#dislikes").classList = "fas fa-3x fa-thumbs-down";
    }
}

function likePop(status) {
    $.ajax({
        type: "POST",
        url: './single_event_process.php',
        data: {
            postStatus: "popularity",
            likeType: status,
            eventID: getID()
        },
        success: function (response) {
            const jsonData = JSON.parse(response);
            if (jsonData['status'] !== "error") {
                $("#thumbdown").toggleClass("far fas");
                $("#thumbup").toggleClass("far fas");
            } else {
                console.log("Error");
            }
        }
    });
}

$.ajax({
    type: "POST",
    url: './single_event_process.php',
    data: {
        postStatus: "event",
        eventID: getID()
    },
    success: function (response) {
        const jsonData = JSON.parse(response);
        let map_resource;
        let like_icons;

        //Update page title

        document.title = "TripOut - " + jsonData[0]["Source"];
        $("#title").html(jsonData[0]["Source"]); //First element is always title

        let content = "";
        const event = $("#events");

        //Handle occurrence

        let occurrence_content = "";
        let occurrence_array = jsonData[1]["Source"];
        for (const key in occurrence_array) {
            if (occurrence_array[key] !== null) {
                occurrence_content += `<span class='descriptor'>${SplitWords(key)}</span>${occurrence_array[key]}`;
            }
        }

        //Handle likes

        if (jsonData[4]['Source']['user'] === false) {
            like_icons = `<span id="likes"><a onclick="likePop(true)"><i id="thumbup" class="far fa-3x fa-thumbs-up"></i></a></span>
                <span id="dislikes"><a onclick="likePop(false)"><i id="thumbdown" class="far fa-3x fa-thumbs-down"></i></a></span>`;
        }
        if (jsonData[4]['Source']['user'] === "like") {
            like_icons = `<span id="likes"><a onclick="likePop(true)"><i id="thumbup" class="fas fa-3x fa-thumbs-up"></i></a></span>
                <span id="dislikes"><a onclick="likePop(false)"><i id="thumbdown" class="far fa-3x fa-thumbs-down"></i></a></span>`
        }
        if (jsonData[4]['Source']['user'] === "dislike") {
            like_icons = `<span id="likes"><a onclick="likePop(true)"><i id="thumbup" class="far fa-3x fa-thumbs-up"></i></a></span>
                <span id="dislikes"><a onclick="likePop(false)"><i id="thumbdown" class="fas fa-3x fa-thumbs-down"></i></a></span>`
        }

        //Handle location options

        let location = jsonData[2]["Source"]["Line1"] + "," + jsonData[2]["Source"]["Line2"] + "," + jsonData[2]["Source"]["Town"] + "," + jsonData[2]["Source"]["PostCode"];
        let marker = encodeURI("size:mid|color:0xFFFF00|label:Venue|" + location);
        map_resource = `https://maps.googleapis.com/maps/api/staticmap?center=${encodeURI(location)}&size=800x300&key=AIzaSyAqt8ejRfMThaP6C3Kfxcd8fN7OpI5RXUc&zoom=${jsonData[2]['Source']['Zoom']}&markers=${marker}`;
        $("#map").html(`<div class="col-md-6"><img src='${map_resource}' /></div><div id="address" class="address col-md-6"><span class='bold'>Address:</span>${location.replace(/,/g, '<br />')} <br /><a class='btn btn-primary' target='_blank' href='https://www.google.com/maps/dir/?api=1&destination=${encodeURI(location)}'>Get directions</a><br /><p class="views">${jsonData[3]['Source']['Views']} views </p>${like_icons}</div>`);

        //Load the custom event content

        for (let i = 5; i < jsonData.length; i++) { //First element can be skipped as it is the title
            if (jsonData[i]['Datatype'] === "Image") {
                if (jsonData[i - 1]['Datatype'] !== "Image") {//If image is first in row, add to a div for justifying
                    content += `<div class="image-row">`;
                }
                content += `<div class="event-image"><a onclick="Modal('${jsonData[i]["Source"]}')"><img src="${jsonData[i]["Source"]}" /></a></div>`;
                if (i < jsonData.length - 1 && jsonData[i + 1]['Datatype'] !== "Image") {
                    content += `</div>`; //If the next piece of content is not an image, close image row
                }
            } else if (jsonData[i]['Datatype'] === "Text") {
                content += jsonData[i]['Source'];
            }
        }

        //Add all data to DOM

        $("#occurrence").html(occurrence_content);
        event.append(content);
        $(document).ready(function () {
            Popularity(jsonData[4]['Source']['likes'].toString(), jsonData[4]['Source']['dislikes'], jsonData[4]['Source']['user']);

        })
    }
});