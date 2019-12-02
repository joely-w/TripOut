let number_of_fields = 0;
let contentFields = []; /*Contains field keys, structure of each node: [{dataType,dataName}]*/
let toolbar_elements = [ /*Contains all element details for text sidebar, used to iteratively create toolbar instead of storing static html, structure: [FA Icon, Exec Command]*/
    ["underline", "underline"],
    ["italic", "italic"],
    ["bold", "bold"],
    ["scissors", "cut"],
    ["repeat", "redo"],
    ["strikethrough", "strikeThrough"],
    ["align-center", "justifyCenter"],
    ["align-left", "justifyLeft"],
    ["align-right", "justifyRight"]
];

function ImageAppender(array_index, data_value) {
    let image_array = contentFields[array_index].dataSrc;
    /* If image already in upload section, remove. Otherwise add image to sections selected images*/
    const index = image_array.indexOf(data_value);
    if (index > -1) {
        image_array.splice(index, 1);
    } else {
        image_array.push(data_value);
    }
}

function Add(content) {
    let container;
    if (content === "image") {
        container = document.createElement("div");
        container.className = "content-el";
        container.id = (number_of_fields).toString();
        container.className = "image-upload select-image row";
        $.ajax({
            url: "/images/myImages.php",
            type: 'GET',
            success: function (res) {
                const result = JSON.parse(res);
                for (let i = 0; i < result.length; i++) {
                    let imgcont = document.createElement("div");
                    imgcont.className = "cold-md-2 img-thumb";
                    imgcont.innerHTML = `<input type="checkbox" onchange="ImageAppender(number_of_fields-1, this.value)" value="${result[i][1]}" id="cont${number_of_fields}${i}"><label for="cont${number_of_fields}${i}"><img src="${result[i][0]}"></label>`;
                    container.appendChild(imgcont);
                }
            }
        });
        contentFields.push({dataType: 'image', dataSrc: []});
    }
    if (content === "text") {
        /*Create sidebar for editing text*/
        container = document.createElement("div");
        container.className = "content-el";
        // noinspection JSValidateTypes
        container.id = number_of_fields;

        let sidebar = document.createElement("div");
        sidebar.className = "sidebar";
        let toolbar = document.createElement("div");
        toolbar.className = "toolbar";
        for (let i = 0; i < toolbar_elements.length; i++) {
            /*Loop through toolbar elements and create each button in toolbar*/
            const button = document.createElement("button");
            button.className = "btn btn-primary fa fa-" + toolbar_elements[i][0];
            const control = toolbar_elements[i][1];
            button.onclick = function () {
                document.execCommand(control, false, '')
            };
            button.type = "button";
            toolbar.appendChild(button);
        }
        sidebar.appendChild(toolbar);
        /*Create Rich Text Editor*/
        let Editor = document.createElement("div");
        Editor.contentEditable = "true";
        Editor.innerHTML = '<h1>Here\'s some content!</h1><p>Put some words here to talk about your event!</p>';
        Editor.className = "editor";
        Editor.id = `Text` + number_of_fields;
        /*Append Sidebar and Editor to DOM*/
        container.appendChild(Editor);
        container.appendChild(sidebar);
        contentFields.push({dataType: 'text', dataSrc: 'Text' + number_of_fields});
    }
    number_of_fields++;
    $("#usercontent").append(($(container)
            .hide()
            .fadeIn(500)
    ));
}

function parseLocation() {
    return {
        postcode: current_post,
        line1: $("#line1").val(),
        line2: $("#line2").val(),
        county: $("#county").val(),
        zoom: $("#zoom").val()
    };
}

function parseOccurence() {
    let starttime = $("#datetimepicker").data('date');
    let endtime = $("#datetimepicker1").data('date');
    if (current_recurrence === "once") {
        let startdate = $('#startdate').data('date');
        let enddate = $('#enddate').data('date');
        return {
            type: "once",
            startdate: startdate,
            enddate: enddate,
            starttime: starttime,
            endtime: endtime
        }
    } else if (current_recurrence === "daily") {
        return {
            type: "daily",
            starttime: starttime,
            endtime: endtime
        }
    } else if (current_recurrence === "weekly") {
        let day = $('#day').val();
        return {
            type: "weekly",
            starttime: starttime,
            endtime: endtime,
            day: day
        }
    } else if (current_recurrence === "monthly") {
        let week = $('#week').val();
        let day = $('#day').val();
        return {
            type: "monthly",
            starttime: starttime,
            endtime: endtime,
            week: week,
            day: day
        }
    } else if (current_recurrence === "yearly") {
        let day = $('#day').val();
        let month = $('#month').val();
        return {
            type: "yearly",
            starttime: starttime,
            endtime: endtime,
            day: day,
            month: month
        }
    }
}

function processForm() {
    for (let i = 0; i < contentFields.length; i++) {
        if (contentFields[i].dataType === "text") { /*If text then grab text content and replace the id with the content*/
            contentFields[i].dataSrc = document.getElementById(contentFields[i].dataSrc).innerHTML; /*In processForm() as should not be called before user has finished editing and is submitting*/
        }
    }
    console.log({
        eventOccurence: parseOccurence(),
        eventLocation: parseLocation(),
        eventTitle: document.getElementById("title").value,
        content: contentFields
    });
    $.ajax({
        url: "/events/create_process.php",
        type: "POST",
        data: {
            eventOccurence: parseOccurence(),
            eventTitle: document.getElementById("title").value,
            eventLocation: parseLocation(),
            content: contentFields
        },
        success: function (response) {
            console.log(response);
            $("#content").html("<h1>Event has been created!</h1>")
        },

    });
}

let current_post;

function postCodeLookup(postcode) {
    if (postcode.length >= 6 && current_post !== postcode) { //If postcode right length and has changed from previous lookup
        $.ajax({
            url: "https://api.postcodes.io/postcodes/" + postcode,
            type: 'GET',
            success: function (res) {
                if (res['status'] === 200) {
                    let county = $('#county');
                    county.val(res['result']['parish']);
                    county.prop("disabled", true);
                    current_post = postcode;
                    showMap(10, postcode)
                }
            }
        });
        $("#zoom").val(10);
    }
}

let image_exist = false;

function showMap(zoom, mode) {
    let address;
    let marker;
    let res = "500x300"; //Resolution for map image
    if (mode === "addr") {
        if (current_post.length >= 6 && image_exist === false) {
            address = encodeURI($("#line1").val() + $("#line2").val() + "," + $("#county").val() + "," + current_post);
            marker = encodeURI("size:mid|color:0xFFFF00|label:Venue|") + address;
            let map_resource = `https://maps.googleapis.com/maps/api/staticmap?center=${address}&size=${res}&key=AIzaSyAqt8ejRfMThaP6C3Kfxcd8fN7OpI5RXUc&zoom=${zoom}&markers=${marker}`;
            $("#map").html(`<img src="${map_resource}" />`);
        }
    } else {
        address = encodeURI(current_post);
        marker = encodeURI("size:mid|color:0xFFFF00|label:Venue|") + address;
        let map_resource = `https://maps.googleapis.com/maps/api/staticmap?center=${address}&size=${res}&key=AIzaSyAqt8ejRfMThaP6C3Kfxcd8fN7OpI5RXUc&zoom=${zoom}&markers=${marker}`;
        $("#map").html(`<img src="${map_resource}" />`);
    }
    $("#zoom").val(zoom);
}

let current_recurrence;
$('#recurrence').on('change', function () {
    if (this.value === "once" && current_recurrence !== "once") { //Don't do anything if user just clicks same option again
        current_recurrence = "once";
        $("#date").html(` <div class="col-md-6"><p>Start date</p><div id="startdate"></div></div><div class="col-md-6 .offset-md-3"><p>End date</p><div id="enddate"></div></div>`);
        $('#startdate').datetimepicker({
            format: 'DD/MM/YYYY',
            inline: true,
            sideBySide: true
        });
        $('#enddate').datetimepicker({
            format: 'DD/MM/YYYY',
            inline: true,
            sideBySide: true,
            useCurrent: false //Important! See issue #1075
        });
        $("#startdate").on("dp.change", function (e) {
            $('#enddate').data("DateTimePicker").minDate(e.date);
        });
        $("#enddate").on("dp.change", function (e) {
            $('#startdate').data("DateTimePicker").maxDate(e.date);
        });
    } else if (this.value === "daily" && current_recurrence !== "daily") { //If
        current_recurrence = "daily";
        $("#date").html(null);
    } else if (this.value === "weekly" && current_recurrence !== "weekly") {
        current_recurrence = "weekly";
        $("#date").html(`<label for="recurrence"><p>What day does your event happen on?</p></label><select class="select-css" id="day" name="day"> <option selected disabled>Select</option> <option value="monday">Monday</option> <option value="tuesday">Tuesday</option> <option value="wednesday">Wednesday</option> <option value="thursday">Thursday</option> <option value="friday">Friday</option> <option value="saturday">Saturday</option> <option value="sunday">Sunday</option> </select>`);
    } else if (this.value === "monthly" && current_recurrence !== "monthly") {
        current_recurrence = "monthly";
        $("#date").html(`<label for="week"><p>Which week in the month the event on?</p></label> <select class="select-css" id="week" name="weeknumber"> <option selected disabled>Select</option> <option value="1">Week 1</option> <option value="2">Week 2</option> <option value="3">Week 3</option> <option value="4">Week 4</option> </select><label for="day"><p>What day in the week is the event?</p></label> <select class="select-css" id="day" name="day"> <option selected disabled>Select</option> <option value="Monday">Monday</option> <option value="Tuesday">Tuesday</option> <option value="Wednesday">Wednesday</option> <option value="Thursday">Thursday</option> <option value="Friday">Friday</option> <option value="Saturday">Saturday</option> <option value="Sunday">Sunday</option> </select>`);
    } else if (this.value === "yearly" && current_recurrence !== "yearly") {
        current_recurrence = "yearly";
        $("#date").html(`<label>What month does your event happen on?<select class="form-control" id="month"> <option value="January">January</option> <option value="February">February</option> <option value="March">March</option> <option value="April">April</option> <option value="May">May</option> <option value="June">June</option> <option value="July">July</option> <option value="August">August</option> <option value="September">September</option> <option value="October">October</option> <option value="November">November</option> <option value="December">December</option> </select> </label> <label>What day of the month does your event happen on? <input class="form-control" type="number" min="1" max="31" name="day" id="day"/></label>`);

    }
});

$('#datetimepicker').datetimepicker({
    inline: true,
    sideBySide: true,
    format: 'LT'
});
$('#datetimepicker1').datetimepicker({
    inline: true,
    sideBySide: true,
    format: 'LT'
});
$("#datetimepicker").on("dp.change", function (e) {
    $('#datetimepicker1').data("DateTimePicker").minDate(e.date);
});
$("#datetimepicker1").on("dp.change", function (e) {
    $('#datetimepicker').data("DateTimePicker").maxDate(e.date);
});
$("#event_form").submit(function (e) {
    e.preventDefault();
    processForm();
});