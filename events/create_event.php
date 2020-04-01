<?php
/**
 * @todo Make image append correct style (as modal is not available)
 * @body Make the appending of image to My Images directly after upload work with create page styles, as create page will not have the modal, and needs checkbox styles.
 */
$title = "Create Event";
$styles = array("//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css",
    "//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css", "//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-glyphicons.css", "//cdn.jsdelivr.net/simplemde/latest/simplemde.min.css");
$scripts = array("//momentjs.com/downloads/moment.js",
    "//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js", "//cdn.jsdelivr.net/simplemde/latest/simplemde.min.js");
include('../header.php');
?>
<body>

<?php
include('../navigation.php'); ?>
<h1 class="center">Create your event</h1>
<div class="errorfield"></div>
<form method="post" id="event_form" class="center">
    <a class="btn btn-primary" href="#event_title" onclick="this.remove()">Start creating event</a>

    <div id="event_title" class="form-element form-group">
        <input type="text" id="title" class="form-control" required>
        <label class="form-control-placeholder" for="title">Event name</label>
        <a class="btn btn-primary" href="#time">Next</a>
    </div>

    <div id="time" class="form-element">
        <div class="col-md-6"><p>Start time</p>
            <div id="datetimepicker"></div>
        </div>
        <div class="col-md-6 .offset-md-3"><p>Finish time</p>
            <div id="datetimepicker1"></div>
        </div>
        <label for="recurrence">How often does your event happen?</label>
        <select class="form-control" id="recurrence" name="recurrence">
            <option selected disabled>Select</option>
            <option value="once">Once</option>
            <option value="daily">Daily</option>
            <option value="weekly">Weekly</option>
            <option value="monthly">Monthly</option>
            <option value="yearly">Yearly</option>
        </select>
        <div id="date">
        </div>
        <a class="btn btn-primary" href="#location">Next</a>
    </div>

    <div id="location" class="form-element">
        <p>Where does your event happen?</p>
        <div class="row">
            <div class="col-md-6">
                <label>Postcode:<input type="text" class="form-control" id="postcode"
                                       placeholder="AB1 2CD"/></label>
                <label>Address Line 1<input type="text" class="form-control" id="line1"
                                            placeholder="1 Example Road"/></label>
                <label>Address Line 2<input type="text" class="form-control" id="line2"
                                            placeholder="Example District"/></label>
                <label>County<input type="text" id="county" class="form-control"
                                    placeholder="Example County"/></label><br/>
                <label> Zoom map:<input type="range" class="form-control" min="1" max="20" value="14" id="zoom"></label>
            </div>
            <div class="col-md-6" id="map"></div>
        </div>
        <a class="btn btn-primary" href="#content">Next</a>

    </div>
    <div id="content" class="form-element">
        <div id="usercontent">
            <div class="toolbox">
                <p>What content do you want to add?</p>
                <button class="btn btn-primary" type="button" id="addtext">Add text</button>
                <button class="btn btn-primary" type="button" id="addimage">Add image</button>
            </div>
            <!-- All content will be appended to this div-->
        </div>
        <button>Create event</button>
    </div>

</form>

<div class="row not_logged_in">
    <div class="col-md-6 col-md-offset-3">
        <div class="manage alert alert-danger" role="alert">
            You're not logged in!
            <a href="/account/login.php">Login now</a>
        </div>
    </div>
</div>
<?
$scripts_footer = array("create.js");
include('../footer.php'); ?>
</body>
