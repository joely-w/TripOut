//Some glitches left for testing in writeup

class Browse {
    constructor() {
        this.filter_data = {};
        this.current_events = [];
    }

    browseAll() {
        let current_class = this;
        $.ajax({
            url: "browse_process.php",
            type: 'GET',
            dataType: 'json',
            success: function (result) {
                for (let i = 0; i < result.length; i++) {
                    current_class.appendEvent(result[i]);
                }
            }
        });
    }

    appendEvent(event_details) {
        let event = `<div id="${event_details['id']}" class="col-md-4 event-thumb"><a href="./event.php?id=${event_details['id']}"><img src="${event_details['thumbnail']}" alt="image"/><h3>${event_details['title']}</h3><span>${this.convertMkString(event_details['description']).substr(0, 220) + '...'}</span></a></div>`;
        $("#events").append(event);
        (this.current_events).push(event_details['id']); //Add event to array of events in DOM
    }

    removeThumbnail(event_id) {
        //Animate event removal
        $("#" + event_id).addClass('animate');
        $('.event-thumb').on('transitionend', function (e) {
            $(e.target).remove()
        });
        return true;
    }

    Filter() {
        let current_class = this;
        //Grab events which satisfy conditions in filter_data and send to DOM
        $.ajax({
            url: "browse_process.php",
            type: 'POST',
            dataType: "json",
            data: {"filter": true, "conditions": this.filter_data},
            success: function (result) {

                let result_ids = []; //List of events in desired DOM
                for (let i = 0; i < result.length; i++) { //Add all ID's to desired DOM
                    result_ids[i] = result[i]['id'];
                }
                //If event is in DOM but shouldn't be delete it
                for (let j = 0; j < current_class.current_events.length; j++) {
                    if (current_class.linearSearch(result_ids, current_class.current_events[j]) === false) { //If event in DOM shouldn't be in DOM
                        current_class.removeThumbnail(current_class.current_events[j]);
                    }
                }
                let temp_current = current_class.current_events;
                for (let i = 0; i < result_ids.length; i++) {
                    //As current_events will increase each time event is added to DOM, stop searching from scaling with appends
                    if (current_class.linearSearch(temp_current, result_ids[i]) === false) {
                        current_class.appendEvent(result[i])
                    }
                }
                current_class.current_events = result_ids;
            }
        });
    }

    convertMkHTML(string) {
        const converter = new showdown.Converter();
        return converter.makeHtml(string);
    }

    convertMkString(string) {
        const Html = $(this.convertMkHTML(string));
        return $("#Invisible").html(Html).text();
    }

    eventHandlers() {
        this.browseAll();
        let current_class = this;
        $(':checkbox').change(function () {
            if ($(this).is(":checked")) {
                //If filter type array does not exist in filter_data create it
                current_class.filter_data[this.name] = (typeof current_class.filter_data[this.name] != 'undefined' && current_class.filter_data[this.name] instanceof Array) ? current_class.filter_data[this.name] : [];
                //Add filter to filter_data inside filter types array (made from name)
                current_class.filter_data[this.name][current_class.filter_data[this.name].length] = this.value;
            } else {
                //If unchecked, remove from filter_data array
                current_class.filter_data[this.name].splice(current_class.linearSearch(current_class.filter_data[this.name], this.value), 1);
                current_class.Filter();

            }
            current_class.Filter();
        });

        $("#clear").click(function () {
            $("input:checkbox").prop('checked', false);
            //Need to loop through current events and remove and then Browse();
            $("#events").empty();
            current_class.current_events = [];
            current_class.browseAll();
        });

        $("#search").keyup(function () {
            current_class.filter_data["Name"] = [$("#search").val()];
            current_class.Filter();
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
                        current_class.filter_data["PostCode"] = [lng, lat, $("#range").val()];
                        current_class.Filter();
                    }
                });
            }
        })
    }

    linearSearch(array, target) {
        for (let i = 0; i < array.length; i++) {
            if (array[i] === target) { //No type coercion allowed
                return i
            }
        }
        return false; //Don't change return, several functions relying on false being here
    }

}

$(document).ready(function () {
    let form = new Browse();
    form.eventHandlers();
});


