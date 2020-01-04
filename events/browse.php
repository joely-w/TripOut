<?php
$title = "Browse Events";
$scripts = array("browse.js");
include('../header.php'); ?>
<body>
<?php include('../navigation.php'); ?>
<div class="container events">
    <h1>Browse events</h1>
    <div class="panel">
        <a data-toggle="collapse" href="#filter" role="button"
           aria-expanded="false">Filters</a></div>
    <div class="collapse filter" id="filter">
        <div class="tools row">
            <div class="col-md-3">
                Occurence:
                <form id="filter">
                    <ul class="ks-cboxtags">
                        <li><input type="checkbox" id="once" class="form-check-input" name="Occurrence"
                                   value="once"/>
                            <label for="once">Once</label></li>
                        <li><input type="checkbox" id="daily" class="form-check-input" name="Occurrence"
                                   value="daily"/>
                            <label for="daily">Daily</label></li>
                        <li><input type="checkbox" id="weekly" class="form-check-input" name="Occurrence"
                                   value="weekly"/>
                            <label for="weekly">Weekly</label></li>
                        <li><input type="checkbox" id="monthly" class="form-check-input" name="Occurrence"
                                   value="monthly"/>
                            <label for="monthly">Monthly</label></li>
                        <li><input type="checkbox" id="yearly" class="form-check-input" name="Occurrence"
                                   value="yearly"/>
                            <label for="yearly">Yearly</label></li>
                    </ul>
                </form>
            </div>
            <div class="search col-md-3">
                Name:
                <input type="text" class="form-control" id="search" placeholder="Search title"
                       aria-label="Search title"/>
            </div>
            <div class="postcode col-md-3">
                Location:
                <form>
                    <input type="text" class="form-control" id="postcode" placeholder="Post Code"
                           aria-label="Post Code"/>
                    <div class="input-group">
                        <input type="number" class="form-control" id="range" placeholder="Range (miles)"
                               aria-label="Range in miles" min=0 value="5"/>
                        <span class="input-group-addon">miles</span>
                    </div>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 col-md-offset-4 center">
                <button class="btn btn-primary" id="clear">Clear filters</button>
            </div>
        </div>
    </div>
    <div id="events" class="row flex-row">

        <!-- Events will be appended dynamically here!-->
    </div>

</div>
<?php
include('../footer.php'); ?>
</body>