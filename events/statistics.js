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

function filterData(type, value) {
    switch (type) {
        case "range":
            const ID = event_data[document.getElementById("selectevents").value]['ID'];
            getChartData(ID, value);
    }
}


function changeSelect(index) {
    if (index === "all") {
        displayForAll();
    } else {
        getChartData(event_data[index]['ID'], 14)
    }
}

function displayForAll() {
    $.ajax({
        type: "POST",
        data: {
            request: ["allevents"]
        },
        url: 'statistics_process.php',
        success: function (response) {
            let postcodes = [];
            let data = JSON.parse(response);
            for (let event = 0; event < data["allevents"].length; event++) {
                postcodes[event] = data["allevents"][event]['Location']['PostCode'];
            }
            $.ajax({
                type: "POST",
                async: false,
                data: {
                    "postcodes": postcodes
                },
                url: 'https://api.postcodes.io/postcodes',
                success: function (response) {
                    for (let event = 0; event < data["allevents"].length; event++) {
                        data["allevents"][event]['Location']['long'] = response["result"][event]["result"]["longitude"];
                        data["allevents"][event]['Location']['lat'] = response["result"][event]["result"]["latitude"];
                    }
                }
            });
            displayMap(data["allevents"]);
        }
    });
}

function displayMap(location_data) {
    let svg = d3.select("svg"),
        width = +svg.attr("width"),
        height = +svg.attr("height");

    let projection = d3.geoMercator()
        .center([3.4360, 52.3555])
        .scale(3000)
        .translate([width / 2, height / 2]);

    let markers = [];
    for (let i = 0; i < location_data.length; i++) {
        markers[i] = {
            long: location_data[i]['Location']['long'],
            lat: location_data[i]['Location']['lat']
        }
    }
    d3.json("https://raw.githubusercontent.com/holtzy/D3-graph-gallery/master/DATA/world.geojson", function (data) {

        data.features = data.features.filter(function (d) {
            return d.properties.name === "England";
        });

        svg.append("g")
            .selectAll("path")
            .data(data.features)
            .enter()
            .append("path")
            .attr("fill", "#b8b8b8")
            .attr("d", d3.geoPath()
                .projection(projection)
            )
            .style("stroke", "black")
            .style("opacity", .3);

        for (let i = 0; i < markers.length; i++) {
            let colour = ColourGenerator();
            $("#event_titles").append(`<li><span class='blockcol' style='background-color:#${colour}'></span><span class="text">` + location_data[i]['Title'] + `</span></li>`);
            svg
                .selectAll("myCircles")
                .data([markers[i]])
                .enter()
                .append("circle")
                .attr("cx", function (d) {
                    return projection([d.long, d.lat])[0]
                })
                .attr("cy", function (d) {
                    return projection([d.long, d.lat])[1]
                })
                .attr("r", Math.ceil(location_data[i]['Views']) / 10)
                .style("fill", colour)
                .attr("stroke", colour)
                .attr("stroke-width", 3)
                .attr("fill-opacity", .7)
        }
    })
}

function getChartData(id, range) {
    $.ajax({
        type: "POST",
        data: {
            request: ["likes", "views", "liketrend"], //Tell API what data is being requested
            id: id, //EventID
            range: range //Number of days to pull likes for like trends
        },
        url: 'statistics_process.php',
        success: function (response) {
            const jsonData = JSON.parse(response);
            generate("LikeTrend", jsonData["liketrend"]);
            generate("PieChart", jsonData["likes"]);
            generate("ViewCounter", jsonData["views"]);
            loadContent(jsonData["views"]);
            $("#filter").html(`<input class="form-control" type="range" min="7" max="48" value="${range}" onclick="filterData('range', this.value)"/>`);
        }
    });
}

function count(status, data, date) { //Count number of status on a certain date (status can either be like:1 or dislike:0)
    let status_counter = 0;
    for (let i = 0; i < data.length; i++) {
        if (parseInt(data[i]["LikeBoolean"]) === status && data[i]['Date'] === date) {
            status_counter += 1;
        }
    }
    return status_counter;
}

function twoDigits(date) {
    if (0 <= date && date < 10) {
        return "0" + date.toString();
    } else if (-10 < date && date < 0) {
        return "-0" + (-1 * date).toString();
    }

    return date.toString();
}

function formatDate(date) {
    return date.getUTCFullYear() + "-" + twoDigits(1 + date.getUTCMonth()) + "-" + twoDigits(date.getUTCDate())
}

/**
 * @return {string}
 */
function ColourGenerator() {
    let strings = '0123456789ABCDEF';
    let initial_string = "";
    for (let index = 0; index <= 5; index++) {
        initial_string += strings[Math.floor(Math.random() * 16)];
    }
    return initial_string;
}

function compileDates(data, range, status_type) {
    let compiled_dates = [];
    switch (status_type) {
        case 2: //Cumulative popularity
            let total = 0;
            for (let offset = range; offset >= 0; offset--) {
                let current_date = new Date();
                current_date.setDate(current_date.getDate() - offset);
                let final_date = formatDate(current_date);
                total += (count(1, data, final_date) - count(0, data, final_date));
                compiled_dates[offset] = [new Date(final_date), total];
            }
            break;
        case 1: //Likes per day
            for (let offset = 0; offset < range; offset++) {
                let current_date = new Date();
                current_date.setDate(current_date.getDate() - offset);
                let final_date = formatDate(current_date);
                compiled_dates[offset] = [new Date(final_date), count(1, data, final_date)];
            }
            break;
        case 0: //Dislikes per day
            for (let offset = 0; offset < range; offset++) {
                let current_date = new Date();
                current_date.setDate(current_date.getDate() - offset);
                let final_date = formatDate(current_date);
                compiled_dates[offset] = [new Date(final_date), count(0, data, final_date)];
            }

            break;
    }
    return compiled_dates;
}

function generate(type, passed_data) {
    google.charts.load('current', {'packages': ['corechart']});
    switch (type) {
        case "LikeTrend":

        function drawTimeline() {
            const cumulative = new google.visualization.DataTable();
            cumulative.addColumn('date', 'Date');
            cumulative.addColumn('number', 'Cumulative popularity');
            cumulative.addRows(compileDates(passed_data["statuses"], passed_data["range"], 2));
            const CumulativeOptions = {
                title: 'Total popularity over time',
                width: 900,
                height: 500,
                hAxis: {
                    format: 'dd/M',
                    gridlines: {count: 15}
                },
                vAxis: {
                    gridlines: {color: 'none'}
                }
            };


            const dislikes = new google.visualization.DataTable();
            dislikes.addColumn('date', 'Date');
            dislikes.addColumn('number', 'Number of dislikes');
            dislikes.addRows(compileDates(passed_data["statuses"], passed_data["range"], 0));
            const DislikeOptions = {
                title: 'Dislikes per day',
                width: 900,
                height: 500,
                hAxis: {
                    format: 'dd/M',
                    gridlines: {count: 15}
                },
                vAxis: {
                    gridlines: {color: 'none'},
                }
            };

            const likes = new google.visualization.DataTable();
            likes.addColumn('date', 'Date');
            likes.addColumn('number', 'Number of likes');
            likes.addRows(compileDates(passed_data["statuses"], passed_data["range"], 1));
            const LikesOptions = {
                title: 'Likes per day',
                width: 900,
                height: 500,
                hAxis: {
                    format: 'dd/M',
                    gridlines: {count: 15}
                },
                vAxis: {
                    gridlines: {color: 'none'},
                    minValue: 0
                }
            };
            const Cumulative = new google.visualization.LineChart(document.getElementById('CumulativePerDay'));
            const LikesPerDay = new google.visualization.LineChart(document.getElementById('LikesPerDay'));
            const DislikesPerDay = new google.visualization.LineChart(document.getElementById('DislikesPerDay'));
            Cumulative.draw(cumulative, CumulativeOptions);
            LikesPerDay.draw(likes, LikesOptions);
            DislikesPerDay.draw(dislikes, DislikeOptions);
        }

            google.charts.setOnLoadCallback(drawTimeline);
            break;

        case "PieChart": //Data should be in form {likes, dislikes}

        function drawPieChart() {
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

            google.charts.setOnLoadCallback(drawPieChart);
            break;
        case "ViewCounter":
            $("#dashboard").html(`<div class="col"><div class="counter"><i class="fas fa-eye fa-2x"></i><h2 class="timer count-title count-number" id="views">0</h2><p class="count-text ">Views</p></div></div>`);
            break;
    }

}

function loadContent(views) { //Animate view counter
    $({countNum: $('#views').text()}).animate({countNum: views}, {
        duration: 1500,
        easing: 'linear',
        step: function () {
            $('#views').text(Math.floor(this.countNum));
        },
        complete: function () {
            $('#views').text(this.countNum);
        }
    });
}

$(document).ready(function () {
    displayForAll();
});
