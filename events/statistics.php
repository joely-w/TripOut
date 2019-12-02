<?php
$scripts = array("https://www.gstatic.com/charts/loader.js", "statistics.js");
$styles = array("//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css");
$title = "Event statistics";
include('../header.php'); ?>
<script src="https://www.gstatic.com/charts/loader.js"></script>

<body>
<?php include('../navigation.php'); ?>
<div class="container events">
    <div id="statistics">
        <h1 id="title">Your events</h1>
        <select class="form-control" onchange="changeSelect(this.value)" id="selectevents">
            <option selected disabled>Select event</option>
        </select>
        <div class="charts row">
            <div class="col-md-6" id="PieChart"></div>
            <div class="col-md-6" id="LikesPerDay"></div>
        </div>
        <div class="row">
            <div class="col-md-6" id="ViewCounter"></div>
        </div>
    </div>
    <!-- Events will be appended dynamically here !-->
</div>
</div>
<?php
include('../footer.php'); ?>
</body>