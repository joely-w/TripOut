function getID() { //Because getting GET parameters from URL isn't nice in JavaScript
    const url_string = window.location.href;
    const url = new URL(url_string);
    return url.searchParams.get("id");
}

/**
 * @return {string}
 */
function SplitWords(word) {
    return word.replace(/([A-Z])/g, ' $1').trim()

}

$.ajax({
    type: "POST",
    url: './single_event_process.php',
    data: {eventID: getID()},
    success: function (response) {
        const jsonData = JSON.parse(response);
        document.title = "TripOut - " + jsonData[0]["Source"];
        let content = "";
        $("#title").html(jsonData[0]["Source"]); //First element is always title
        document.title = "TripOut - " + jsonData[0]["Source"];
        const event = $("#events");
        let occurrence_content = "";
        let occurrence_array = jsonData[1]["Source"];
        for (const key in occurrence_array) {
            if (occurrence_array[key] !== null) {
                occurrence_content += `<span class='descriptor'>${SplitWords(key)}</span>${occurrence_array[key]}`;
            }
        }
        $("#occurrence").html(occurrence_content);
        let location = jsonData[2]["Source"]["Line1"] + "," + jsonData[2]["Source"]["Line2"] + "," + jsonData[2]["Source"]["Town"] + "," + jsonData[2]["Source"]["PostCode"];
        let marker = encodeURI("size:mid|color:0xFFFF00|label:Venue|" + location);
        let map_resource = `https://maps.googleapis.com/maps/api/staticmap?center=${encodeURI(location)}&size=800x300&key=AIzaSyAqt8ejRfMThaP6C3Kfxcd8fN7OpI5RXUc&zoom=${jsonData[2]['Source']['Zoom']}&markers=${marker}`;
        $("#map").html(`<div class="col-md-6"><img src='${map_resource}' /></div><div class="address col-md-6"><span class='bold'>Address:</span>${location.replace(/,/g, '<br />')} <br /><a class='btn btn-primary' target='_blank' href='https://www.google.com/maps/dir/?api=1&destination=${encodeURI(location)}'>Get directions</a></div>`);
        for (let i = 3; i < jsonData.length; i++) { //First element can be skipped as it is the title
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
        event.append(content);
    }
});