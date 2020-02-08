<?php
$title = "Manage My Account";

$scripts = array("manage.js", "../images/upload_handler.js");
include('../header.php');
include('../database/config.php');
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
        <a data-toggle="collapse" href="#account" role="button"
           aria-expanded="false">
            <h2>My account</h2></a>
        <div class="collapse in row" id="account">
            <input type="text" style="display:none">
            <!--Used to distract browser from actual password and username fields so it doesn't autofill!-->
            <input type="password" style="display:none">

            <button type="button" onclick="LogOut()" class="btn btn-primary">Logout
            </button>
        </div>
        <a data-toggle="collapse" href="#images" role="button"
           aria-expanded="false">
            <h2>My images</h2></a>
        <div id="images" class="collapse in image-upload row">
        </div>
        <a data-toggle="collapse" href="#image-upload" role="button"
           aria-expanded="false">
            <h2>Upload image</h2></a>
        <div id="upload-notifications"></div>
        <!--Append any notifications about status of upload to this div-->
        <div id="image-upload" class="collapse in image-upload row">
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

        <a href="/events/statistics.php" class="btn btn-primary">My Event Statistics</a>
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