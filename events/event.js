function getID() { //Because getting GET parameters from URL isn't nice in JavaScript
    const url_string = window.location.href;
    const url = new URL(url_string);
    const c = url.searchParams.get("id");
    return c;
}

$(document).ready(function () {
    $.ajax({
        type: "POST",
        url: './single_event_process.php',
        data: {eventID: getID()},
        success: function (response) {
            let content = "";
            const jsonData = JSON.parse(response);
            $("#title").html(jsonData[0]["Source"]); //First element is always title
            const event = $("#events");
            for (let i = 1; i < jsonData.length; i++) { //First element can be skipped as it is the title
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

});