<?php
$title = "Browse Events";
$scripts = array("browse.js");
include('../header.php'); ?>
<body>
<?php include('../navigation.php'); ?>
<div class="container events">
    <h1>Browse events</h1>

    <div id="events" class="row">
        <!-- Events will be appended dynamically here!-->
    </div>

</div>
<?php
include('../footer.php'); ?>
</body>