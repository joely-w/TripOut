<?php
/**
 * @todo Make image append correct style (as modal is not available)
 * @body Make the appending of image to My Images directly afer upload work with create page styles, as create page will not have the modal, and needs checkbox styles.
 */
$title = "Create Event";
$styles = array("//stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css");
$scripts = array("create.js", "/images/upload_handler.js");
include('../database/config.php');
include('../header.php'); ?>

<body>
<?php
$Image = new myImages();
include('../navigation.php'); ?>
<div class="create">
    <h1>Create an event
    </h1>
    <!--<div class="toolbox">
<span>What content do
you want to add?</span>
<button type="button" onclick="Add('text')" class="btn btn-primary">Add text</button>
<button type="button" onclick="Add('image')" class="btn btn-primary">Add image</button>
<button type="button" onclick="Add('review')" class="btn btn-primary">Add review</button>
</div>!-->
    <div id="content" class="content">
        <!-- All content will be appended to this div-->
        <input class="form-control" type="text" name="title" placeholder="Event Title"/>
        <div class="toolbar">
            <button type="button" id="underline" class="btn btn-primary fa fa-underline"
                    onclick="document.execCommand('underline', false, '');">
            </button>
            <button type="button" id="italic" class="btn btn-primary fa fa-italic"
                    onclick="document.execCommand('italic', false, '');">
            </button>
            <button type="button" id="bold" class="btn btn-primary fa fa-bold"
                    onclick="document.execCommand('bold', false, '');">
            </button>
            <button type="button" id="cut" class="btn btn-primary fa fa-scissors"
                    onclick="document.execCommand('cut',false,'')">
            </button>
            <button type="button" id="undo" class="btn btn-primary fa fa-undo"
                    onclick="document.execCommand('undo',false,'')">
            </button>
            <button type="button" id="redo" class="btn btn-primary fa fa-repeat"
                    onclick="document.execCommand('redo',false,'')">
            </button>
            <select id="fontSize" onchange="document.execCommand('fontSize',false,this.value)">
                <option value="1">8pt
                </option>
                <option value="2">10pt
                </option>
                <option value="3">12pt
                </option>
                <option value="4">14pt
                </option>
                <option value="5">18pt
                </option>
                <option value="6">24pt
                </option>
                <option value="7">36pt
                </option>
            </select>
            <button type="button" id="strikeThrough" class="btn btn-primary fa fa-strikethrough"
                    onclick="document.execCommand('strikeThrough',false,'')">
            </button>
            <button type="button" id="justifyCenter" class="btn btn-primary fa fa-align-center"
                    onclick="document.execCommand('justifyCenter',false,'')">
            </button>
            <button type="button" id="justifyLeft" class="btn btn-primary fa fa-align-left"
                    onclick="document.execCommand('justifyLeft',false,'')">
            </button>
            <button type="button" id="justifyRight" class="btn btn-primary fa fa-align-right"
                    onclick="document.execCommand('justifyRight',false,'')">
            </button>
        </div>
        <div class="center">
            <div class="editor" contenteditable>
                <h1>Describe your event
                </h1>
                <p>
                    Put some words that will talk about your event. It can be styled if you want <strong>like
                        this</strong>.
                </p>
            </div>
        </div>
        <h2>
            My Images
        </h2>

        <div id="images" class="image-upload select-image row">
            <?php
            $images_arr = $Image->DisplayImages($_SESSION['Username']);
            $file_count = 0;
            foreach ($images_arr as $image) {#Structure: Filename, Filetype
                $path = "/events/images/" . $_SESSION['Username'] . "/" . $image['Filename'] . "." . $image['Filetype'];
                ?>
                <div class="col-md-2 img-thumb">
                    <input type="checkbox" name="<?php echo $file_count ?>" id="<?php echo $file_count ?>">

                    <label for="<?php echo $file_count ?>"><img src="<?php echo $path; ?>"/></label>
                </div>
                <?
                $file_count += 1;
            }
            ?>
        </div>
        <h2>Upload image</h2>
        <div id="upload-notifications"></div>
        <!--Append any notifications about status of upload to this div-->
        <div id="image-upload" class="image-upload row">
            <form id="uploadimage" method="post" enctype="multipart/form-data">
                <div id="selectImage">
                    <label class="uploader btn btn-default">
                        <img id="previewing" height="50"
                             src="https://icon-library.net/images/file-icon-png/file-icon-png-23.jpg"/>
                        <label>Select Your Image</label>
                        <input class="btn" onchange="fileHandler(this)" type="file" name="file" id="file" required/>
                    </label>

                    <input type="submit" value="Upload" class="submit btn btn-primary"/>
                </div>
            </form>
        </div>

    </div>
    </form>
    </div>
</div>
</body>