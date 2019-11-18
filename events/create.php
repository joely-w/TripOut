<?php
/**
 * @todo Make image append correct style (as modal is not available)
 * @body Make the appending of image to My Images directly after upload work with create page styles, as create page will not have the modal, and needs checkbox styles.
 */
$title = "Create Event";
$styles = array("//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css",
    "//stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css");
$scripts = array("//momentjs.com/downloads/moment.js",
    "//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js", "/images/upload_handler.js");
include('../database/config.php');
include('../header.php');
?>
<body>

<?php
include('../navigation.php');
if (isset($_SESSION['Username'])) {

    $Image = new myImages();
    ?>
    <form method="post" id="event_form" action="create_process.php">

        <div id="alert_box" class="center create-title">
        </div>
        <div class="toolbox"><span>What content do you want to add?</span>
            <button class="btn btn-primary" type="button" onclick="Add('text')">Add text</button>
            <button class="btn btn-primary" type="button" onclick="Add('image')">Add image</button>
            <button class="center btn btn-primary">
                Submit
            </button>
        </div>
        <div id="create" class="create">
            <div id="content" class="content">
                <h1>Create an event</h1>
                <input class="form-control" id="title" type="text" name="title" placeholder="Event Title"/>
                <a data-toggle="collapse" href="#timelocation" role="button"
                   aria-expanded="false" aria-controls="collapseExample">
                    <h2>Time and Location</h2></a>
                <div class="collapse in" id="timelocation">
                    <div id="time">
                        <div class="col-md-6"><p>Start time</p>
                            <div id="datetimepicker"></div>
                        </div>
                        <div class="col-md-6 .offset-md-3"><p>Finish time</p>
                            <div id="datetimepicker1"></div>
                        </div>
                    </div>
                    <label for="recurrence"><p>How often does your event happen?</p></label><select class="form-control"
                                                                                                    id="recurrence"
                                                                                                    name="recurrence">
                        <option selected disabled>Select</option>
                        <option value="once">Once</option>
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                        <option value="yearly">Yearly</option>
                    </select>
                    <div id="date">
                    </div>
                    <div id="location"><p>Where does your event happen?</p>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="postcode" id="postcode" placeholder="Postcode"
                                   onkeyup="postCodeLookup(this.value)"/>
                            <input type="text" class="form-control" name="line1" onchange="showMap(14, 'addr')"
                                   id="line1"
                                   placeholder="Address Line 1"/>
                            <input type="text" class="form-control" name="line2" id="line2"
                                   placeholder="Address Line 2 "/>
                            <input type="text" id="county" class="form-control" id="county" name="county"
                                   placeholder="town"/>
                            Zoom map:
                            <input type="range" min="1" max="20" value="14" id="zoom" onchange="showMap(this.value)">


                            <button type="button" onclick="showMap(14, 'addr')" class="btn-primary btn">Find venue
                            </button>
                        </div>
                        <div class="col-md-6" id="map"></div>
                    </div>
                </div>
                <div id="usercontent"><!-- All content will be appended to this div-->
                </div>
    </form>
    </div>
    </div>
<? } else {
    ?>
    <div class="manage alert alert-danger" role="alert">
        You're not logged in!
        <a href="/account/login.php">Login now
        </a>
    </div>
    <?php
}
$scripts_footer = array("create.js");
include('/var/www/html/footer.php'); ?>
</body>
