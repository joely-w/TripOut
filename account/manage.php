<?php
$title = "Manage My Account";
$styles = ["//stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"];
$scripts = ["manage.js", "/images/modal.js"];
include('../header.php');
?>

<body>
<?php include('../navigation.php'); ?>
<div class="container">
    <div class="logged_in"><!--Will only be visible if logged in-->
        <ul class="nav nav-tabs"> <!--List of tab links-->
            <li class="active"><a data-toggle="tab" href="#myaccount">Account Details</a></li>
            <li><a data-toggle="tab" href="#myimages">My Images</a></li>
            <li><a data-toggle="tab" href="#upload">Upload Image</a></li>
            <li><a data-toggle="tab" href="#eventstats">Event Statistics</a></li>
        </ul>
        <div class="tab-content"><!--Stores all tab panes-->

            <div id="myaccount" class="tab-pane fade in active"><!--Tab pane for account-->
                <h2>My account</h2>
                <div id="report"></div>
                <div class="row">
                    <div class="col-md-4">
                        <label>Username<input class="form-control" type="text" id="username"/></label>
                    </div>
                    <div class="col-md-4">
                        <label>Email<input class="form-control" type="text" id="email"/></label>
                    </div>
                    <div class="col-md-4">
                        <label>Password<input class="form-control" type="password" id="passwd"/></label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 col-md-offset-4">
                        <button type="button" id="logout" class="btn btn-primary">Logout</button>
                    </div>
                </div>
            </div>

            <div id="myimages" class="tab-pane fade">
                <!--Tab pane for images-->
                <h2>My images</h2>
                <div id="images" class="row">
                    <!--Images will be appended here-->
                </div>
            </div>

            <div id="upload" class="tab-pane fade">
                <!--Tab pane for image uploads-->
                <h2>Upload Image</h2>
                <div id="upload-notifications">
                    <!--Append any notifications about status of upload to this div-->
                </div>
                <form id="image_upload">
                    <!--File upload form-->
                    <div class="row">
                        <div class="col-md-4 col-md-offset-4 image-upload">
                            <!--Put upload section in column and place in middle of row-->
                            <label class="file-upload"><i class="fa fa-upload fa-6">
                                    <!--Image upload icon-->
                                </i>
                                Choose an image
                                <!--Input placed inside label so that input can be hidden while still being used, which will be used when styling file upload-->
                                <input id="file_upload" type="file" name="image"/>
                            </label>
                        </div>
                        <div class="files row">
                            <div class="col-md-4 col-md-offset-4">
                                <div id="files">
                                    <!--Append file name to here since input is hidden-->
                                </div>
                                <!--File input that accepts image formats jpg, jpeg, png-->
                                <button class="btn btn-primary">Upload image</button>
                            </div>
                        </div>
                    </div>
                    <div class="progress"><!--Upload progress bar, does not display by default-->
                        <div class="progress-bar progress-bar-striped active" id="progress" role="progressbar"
                             style="width:0">
                            0%
                        </div>
                    </div>
                </form>
            </div>


            <div id="eventstats" class="tab-pane fade"> <!--Tab pane for event statistics-->
                <h3>Event Statistics</h3>
                <a href="/events/statistics.php" class="btn btn-primary">My Event Statistics</a>
            </div>
        </div>

    </div>
</div>
<div class="not_logged_in">
    <div class="alert alert-danger" role="alert">
        You're not logged in!
        <a href="login.php">Login now
        </a>
    </div>
</div>
<div class="modal" id="modal">
    <a onclick="closeModal()" class="close">
        <!--Will be used to call function to close modal-->
        &#10006;
    </a>
    <img src="" id="modalImage" alt="Modal Image Template"/>
</div>
</body>