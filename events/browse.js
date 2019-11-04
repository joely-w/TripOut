function Browse() { /*Grab all events from API*/
    $.ajax({
        url: "event_process.php",
        type: 'GET',
        dataType: 'json',
        success: function (result) {
            for (let i = 0; i < result.length; i++) {
                appendEvent(result[i]);
            }
        }
    });
}

function appendEvent(event_details) {
    let event = `<div class="col-md-4 event-thumb"><a href="./event.php?id=${event_details['id']}"><img src="${event_details['thumbnail']}" alt="image"/><h3>${event_details['title']}</h3><span>${event_details['description']}</span></a></div>`;
    $("#events").append(event);
}

Browse();