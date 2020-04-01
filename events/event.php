<?php
$scripts = array("/images/modal.js", "single_event.js", "//cdnjs.cloudflare.com/ajax/libs/showdown/1.9.1/showdown.min.js");
$styles = array("//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css");
include('../header.php'); ?>
<body>

<?php include('../navigation.php'); ?>
<div class="single-event container">
    <!--Title row-->
    <div class="row">
        <h1 id="title"></h1>
    </div>
    <!--Event information row-->
    <div class="row">
        <!-- Occurrence, views and like/dislike icons column-->
        <div class="col-md-6">
            <div id="occurrence"></div>
            <div class="views">
                <i class="fas fa-4x fa-eye"></i>
                <span id="views"></span>
            </div>
            <div id="icons"></div>
            <div id="address"></div>

        </div>
        <!--Map container column-->
        <div class="col-md-6">
            <div id="map"></div>
        </div>
    </div>
    <div id="event-content" class="row">
        <!--Event content inserted here-->
    </div>
</div>
<?php
include('../footer.php'); ?>
</body>