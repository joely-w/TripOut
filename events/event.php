<?php
$title = $Event[0]["Source"];
$scripts = array("/images/modal.js", "event.js");
include('../header.php'); ?>
<body>
<?php include('../navigation.php'); ?>
<div class="container events">
    <div id="events" class="row">
        <h1 id="title"></h1>
        <div class="locator container">
            <div id="occurrence">
            </div>
            <div id="map" class="row"></div>
        </div>
        <div class="modal" id="modal">
            <a onclick="closeModal()" class="close">&#10006;</a>
            <img src="" id="modalImage"/>
        </div>

    </div>
    <!-- Events will be appended dynamically here !-->
</div>
</div>
<?php
include('../footer.php'); ?>
</body>