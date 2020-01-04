let filter_data = {};
let current_events = []; //ID's of events currently in DOM
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

function removeThumbnail(eventID) { //Animate event removal
    $("#" + eventID).addClass('animate');
    $('.event-thumb').on('transitionend', function (e) {
        $(e.target).remove()
    });
    return true;
}

function filter() { //Grab events which satisfy conditions in filter_data and send to DOM
    $.ajax({
        url: "event_process.php",
        type: 'POST',
        dataType: "json",
        data: {"filter": true, "conditions": filter_data},
        success: function (result) {
            let result_ids = []; //List of events in desired DOM
            for (let i = 0; i < result.length; i++) { //Add all ID's to desired DOM
                result_ids[i] = result[i]['id'];
            }
            //If event is in DOM but shouldn't be delete it
            for (let j = 0; j < current_events.length; j++) {
                if (linearSearch(result_ids, current_events[j]) === false) { //If event in DOM shouldn't be in DOM
                    removeThumbnail(current_events[j]);
                }
            }
            let temp_current = current_events;
            for (let i = 0; i < result_ids.length; i++) {
                //As current_events will increase each time event is added to DOM, stop searching from scaling with appends
                if (linearSearch(temp_current, result_ids[i]) === false) {
                    appendEvent(result[i])
                }
            }
            current_events = result_ids;
        }
    });
}

function appendEvent(event_details) {
    let event = `<div id="${event_details['id']}" class="col-md-4 event-thumb"><a href="./event.php?id=${event_details['id']}"><img src="${event_details['thumbnail']}" alt="image"/><h3>${event_details['title']}</h3><span>${event_details['description']}</span></a></div>`;
    $("#events").append(event);
    current_events.push(event_details['id']); //Add event to array of events in DOM
}

function linearSearch(array, target) { //Do linear search on array, return first found element
    for (let i = 0; i < array.length; i++) {
        if (array[i] === target) { //No type coercion allowed
            return i
        }
    }
    return false; //Don't change return, several functions relying on false being here
}

$(document).ready(function () {
    Browse();
    //Hooks for filter form:

    $(':checkbox').change(function () {
        if ($(this).is(":checked")) {
            //If filter type array does not exist in filter_data create it
            filter_data[this.name] = (typeof filter_data[this.name] != 'undefined' && filter_data[this.name] instanceof Array) ? filter_data[this.name] : [];
            //Add filter to filter_data inside filter types array (made from name)
            filter_data[this.name][filter_data[this.name].length] = this.value;
        } else {
            //If unchecked, remove from filter_data array
            filter_data[this.name].splice(linearSearch(filter_data[this.name], this.value), 1);
            filter();

        }
        filter();
    });

    $("#clear").click(function () {
        $("input:checkbox").prop('checked', false);
        //Need to loop through current events and remove and then Browse();
        $("#events").empty();
        current_events = [];
        Browse();
    });

    $("#search").keyup(function () {
        filter_data["Name"] = [$("#search").val()];
        filter();
    });

    $("#postcode, #range").on('input', function () {
        let postcode = $("#postcode").val();
        if (postcode.replace(/\s/g, '').length > 5) {

            $.ajax({
                type: "GET",
                url: 'https://api.postcodes.io/postcodes/' + postcode,
                dataType: "json",
                success: function (response) {
                    let lng = response['result']['longitude'];
                    let lat = response['result']['latitude'];
                    filter_data["PostCode"] = [lng, lat, $("#range").val()];
                    filter();
                }
            });
        }
    })
});
