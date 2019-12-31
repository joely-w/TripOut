<?php
$scripts = array("https://www.gstatic.com/charts/loader.js", "statistics.js", "https://d3js.org/d3.v4.js", "https://d3js.org/d3-geo-projection.v2.min.js");
$styles = array("//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css");
$title = "Event statistics";
include('../header.php'); ?>
<body>
<?php include('../navigation.php'); ?>
<div class="container events">
    <div id="statistics">
        <h1 id="title">Your events</h1>
        <select class="form-control" onchange="changeSelect(this.value)" id="selectevents">
            <option value="all" selected>All events</option>
        </select>
        <div id="dashboard" class="row dashboard">
            <div class="col-md-4" id="ViewCounter"></div>
        </div>
    </div>
    <div class="center" id="filter">
    </div>
    <div id="charts" class="charts row">
        <!-- Could do more, maybe make range dependant on creation of event !-->
        <div id="charts" class="charts row">
            <h3>Popularity based on where the event is located</h3>
            <!-- Could do more, maybe make range dependant on creation of event !-->
            <div class="col-md-6">
                <svg id="my_dataviz" width="1200" height="1200"></svg>
            </div>
            <div class="col-md-4">
                <ul id="event_titles"></ul>
            </div>
        </div>
        <div class="col-md-6" id="PieChart">
        </div>
        <div class="col-md-6" id="LikesPerDay"></div>
    </div>
    <div class="charts row">
        <div class="col-md-6" id="DislikesPerDay"></div>
        <div class="col-md-6" id="CumulativePerDay"></div>

    </div>
</div>
</div>
<!-- Events will be appended dynamically here !-->
</div>
</div>
<?php
include('../footer.php'); ?>
</body>