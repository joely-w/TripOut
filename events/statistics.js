//Script relies on the structure of events array not changing after AJAX call
let event_data;
$.ajax({
    type: "POST",
    data: {
        request: ["events"]
    },
    url: 'statistics_process.php',
    success: function (response) {
        const jsonData = JSON.parse(response);
        let events = jsonData['events'];
        event_data = events;
        for (let event_index = 0; event_index < events.length; event_index++) {
            $('#selectevents').append($('<option>', {
                value: event_index,
                text: events[event_index]["Title"]
            }));
        }
    }
});

function changeSelect(index) {
    getChartData(event_data[index]['ID'])
}

function getChartData(id) {
    $.ajax({
        type: "POST",
        data: {
            request: ["likes", "views", "liketrend"], //Tell API what data is being requested
            id: id, //EventID
            range: 7 //Number of days to pull likes for like trends
        },
        url: 'statistics_process.php',
        success: function (response) {
            const jsonData = JSON.parse(response);
            generate("LikeTrend", jsonData["liketrend"]);
            generate("PieChart", jsonData["likes"]);
            generate("ViewCounter", jsonData["views"]);
        }
    });
}

function count(status, data, date) { //Count number of status on a certain date (status can either be like or dislike)
    let status_counter = 0;
    for (let i = 0; i < data.length; i++) {
        if (parseInt(data[i]["LikeBoolean"]) === status && data[i]['Date'] === date) {
            status_counter += 1;
        }
    }
    return status_counter;
}

/**
 * You first need to create a formatting function to pad numbers to two digits…
 **/
function twoDigits(d) {
    if (0 <= d && d < 10) return "0" + d.toString();
    if (-10 < d && d < 0) return "-0" + (-1 * d).toString();
    return d.toString();
}

/**
 * …and then create the method to output the date string as desired.
 * Some people hate using prototypes this way, but if you are going
 * to apply this to more than one Date object, having it as a prototype
 * makes sense.
 **/
Date.prototype.toMysqlFormat = function () {
    return this.getUTCFullYear() + "-" + twoDigits(1 + this.getUTCMonth()) + "-" + twoDigits(this.getUTCDate());
};

function compileDates(data, range) {
    let compiled_dates = [];
    let options = {};
    for (let offset = 0; offset < range; offset++) {
        let current_date = new Date();
        current_date.setDate(current_date.getDate() - offset);
        let final_date = current_date.toMysqlFormat();
        compiled_dates[offset] = [new Date(final_date), count(1, data, final_date)];
    }
    return compiled_dates;
}

function generate(type, passed_data) {
    google.charts.load('current', {'packages': ['corechart']});
    switch (type) {
        case "LikeTrend":

        function drawCharta() {
            var data = new google.visualization.DataTable();
            data.addColumn('date', 'Date');
            data.addColumn('number', 'Number of likes');
            data.addRows(compileDates(passed_data["statuses"], passed_data["range"]));
            var options = {
                title: 'Likes per day',
                width: 900,
                height: 500,
                hAxis: {
                    format: 'yy-M-d',
                    gridlines: {count: 15}
                },
                vAxis: {
                    gridlines: {color: 'none'},
                    minValue: 0
                }
            };

            var chart = new google.visualization.LineChart(document.getElementById('LikesPerDay'));

            chart.draw(data, options);
        }

            google.charts.setOnLoadCallback(drawCharta);
            break;

        case "PieChart": //Data should be in form {likes, dislikes}

        function drawChart() {
            // Define the chart to be drawn.
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Vote type');
            data.addColumn('number', 'Number of votes');
            data.addRows([
                ['Likes', parseInt(passed_data['likes'])],
                ['Dislikes', parseInt(passed_data['dislikes'])]
            ]);

            // Set chart options
            var options = {
                'title': 'Likes and Dislikes',
                'width': 800,
                'height': 500,
                'colors': ["#0CCE6B", "#E71D36"]
            };

            // Instantiate and draw the chart.
            var chart = new google.visualization.PieChart(document.getElementById('PieChart'));
            chart.draw(data, options);
        }

            google.charts.setOnLoadCallback(drawChart);
            break;
        case "ViewCounter":
            $("#ViewCounter").html(`<p>Total views: ${passed_data}</p>`);
            break;
    }

}