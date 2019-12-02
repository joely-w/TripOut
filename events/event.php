<?php
$scripts = array("/images/modal.js", "event.js");
$styles = array("//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css");
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