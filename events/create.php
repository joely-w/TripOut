<?php
$title = "Create Event";
$styles = array("//cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote.css");
$scripts = array("//cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote.js", "create.js");
include('../header.php'); ?>
<body>
<?php include('../navigation.php'); ?>
<div class="create">
    <h1>Create an event</h1>
    <div class="toolbox"> <!-- Toolbox for adding content-->
        <span>What content do
            you want to add?</span>
        <button type="button" onclick="Add('text')" class="btn btn-primary">Add text</button>
        <button type="button" onclick="Add('image')" class="btn btn-primary">Add image</button>
        <button type="button" onclick="Add('review')" class="btn btn-primary">Add review</button>

    </div>
    <div id="content" class="content"> <!-- All content will be appended to this div-->

    </div>
</div>
</body>