<?php
$title = "Manage My Account";
$scripts = array("manage.js", "../images/upload_handler.js");
include('../header.php');
include('../database/config.php');
$account = new Login();
$Image = new myImages();
#If a user field does not appear on this page, it probably isn't in the LinkedEditable table, add it!
?>

<body>
<?php include('../navigation.php'); ?>
<div class="container">
    <div class="row manage">
        <div id="report">
        </div>
        <!--Content updated if error occurs!-->
        <?php
        if (isset($_SESSION['Username'])) {
        ?>
        <h2>My account
        </h2>
        <form>
            <input type="text" style="display:none">
            <input type="password" style="display:none">
            <?
            $fields = $account->AccountFields();
            foreach ($fields

            as $field) {
            if ($field["Viewable"] == true) {#If actual value of field should not be view by user, hide it (for instance password)
                $valueAttribute = $_SESSION[$field['UserField']];
            } else {
                $valueAttribute = "Hidden value";
            }
            ?>
            <div class="col-md-4">
                <label>
                    <?php echo $field['UserField'] ?>:
                    <br>
                    <input type="<?php echo $field['Datatype'] ?>" name="<?php echo $field['UserField'] ?>"
                           value="<?php echo $valueAttribute ?>"
                           onchange="updateValue('<?php echo $field['UserField'] ?>',this.value)"
                           class="form-control"/>
                </label>
            </div>
        </form>
    <?
    } ?>
        <button type="button" onclick="LogOut()" class="btn btn-primary">Logout
        </button>
        <h2>My images
        </h2>
        <div id="images" class="image-upload row">
            <?php
            $images_arr = $Image->DisplayImages($_SESSION['Username']);
            foreach ($images_arr as $image) {#Structure: Filename, Filetype
                $path = "/events/images/" . $_SESSION['Username'] . "/" . $image['Filename'] . "." . $image['Filetype'];
                ?>
                <div class="col-md-2 img-thumb">
                    <a onclick="Modal('<?php echo $path ?>')"><img src="<?php echo $path; ?>"/></a>
                </div>
                <?
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
        <div class="modal" id="modal">
            <a onclick="closeModal()" class="close">&#10006;</a>
            <img src="" id="modalImage"/>
        </div>

    </div>

    <?
    } else {
        ?>
        <div class="alert alert-danger" role="alert">
            You're not logged in!
            <a href="login.php">Login now
            </a>
        </div>
        <?
    }
    ?>
</div>
</div>
</body>