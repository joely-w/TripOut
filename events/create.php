<?php
/**
 * @todo Make image append correct style (as modal is not available)
 * @body Make the appending of image to My Images directly after upload work with create page styles, as create page will not have the modal, and needs checkbox styles.
 */
$title = "Create Event";
$styles = array("//stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css");
$scripts = array("/images/upload_handler.js");
include('../database/config.php');
include('../header.php');
?>

<body>
<?php
include('../navigation.php');
if (isset($_SESSION['Username'])) {

    $Image = new myImages();
    ?>
    <div id="alert_box" class="center create-title">
    </div>
    <div id="create" class="create">
        <form method="post" id="event_form" action="create_process.php">

            <div class="toolbox"><span>What content do you want to add?</span>
                <button class="btn btn-primary" type="button" onclick="Add('text')">Add text</button>
                <button class="btn btn-primary" type="button" onclick="Add('image')">Add image</button>
            </div>
            <div id="content" class="content">
                <h1>Create an event</h1>
                <input class="form-control" id="title" type="text" name="title" placeholder="Event Title"/>

                <div id="usercontent"><!-- All content will be appended to this div--></div>
                <button class="center btn btn-primary">
                    Submit
                </button>
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
