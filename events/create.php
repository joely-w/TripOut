<?php
$title = "Create Event";
$styles = array("//stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css");
$scripts = array("create.js");
include('../header.php'); ?>
<body>
<?php include('../navigation.php'); ?>
<div class="create">
    <h1>Create an event</h1>
    <!--<div class="toolbox">
        <span>What content do
            you want to add?</span>
        <button type="button" onclick="Add('text')" class="btn btn-primary">Add text</button>
        <button type="button" onclick="Add('image')" class="btn btn-primary">Add image</button>
        <button type="button" onclick="Add('review')" class="btn btn-primary">Add review</button>

    </div>!-->
    <div id="content" class="content"> <!-- All content will be appended to this div-->
        <form>
            <input class="form-control" type="text" name="title" placeholder="Event Title"/>
            <div class="toolbar">
                <button id="underline" class="btn btn-primary fa fa-underline"
                        onclick="document.execCommand('underline', false, '');"></button>
                <button id="italic" class="btn btn-primary fa fa-italic"
                        onclick="document.execCommand('italic', false, '');"></button>
                <button id="bold" class="btn btn-primary fa fa-bold"
                        onclick="document.execCommand('bold', false, '');"></button>
                <button id="cut" class="btn btn-primary fa fa-scissors"
                        onclick="document.execCommand('cut',false,'')"></button>
                <button id="undo" class="btn btn-primary fa fa-undo"
                        onclick="document.execCommand('undo',false,'')"></button>
                <button id="redo" class="btn btn-primary fa fa-repeat"
                        onclick="document.execCommand('redo',false,'')"></button>
                <select id=fontSize" onchange="document.execCommand('fontSize',false,this.value)">
                    <option value="1">8pt</option>
                    <option value="2">10pt</option>
                    <option value="3">12pt</option>
                    <option value="4">14pt</option>
                    <option value="5">18pt</option>
                    <option value="6">24pt</option>
                    <option value="7">36pt</option>
                </select>
                <button id="strikeThrough" class="btn btn-primary fa fa-strikethrough"
                        onclick="document.execCommand('strikeThrough',false,'')"></button>
                <button id="justifyCenter" class="btn btn-primary fa fa-align-center"
                        onclick="document.execCommand('justifyCenter',false,'')"></button>
                <button id="justifyLeft" class="btn btn-primary fa fa-align-left"
                        onclick="document.execCommand('justifyLeft',false,'')"></button>
                <button id="justifyRight" class="btn btn-primary fa fa-align-right"
                        onclick="document.execCommand('justifyRight',false,'')"></button>
            </div>
            <div class="center">
                <div class="editor" contenteditable>
                    <h1>Sample header</h1>
                    <p>Sample text</p>
                </div>
            </div>
            <div class="image-upload">
                <span>Uploaded Images</span>
            </div>
        </form>
    </div>
</div>
</body>