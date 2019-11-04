<?php
include("./single_event_process.php");
$title = $Event[0]["Source"];
include('../header.php'); ?>
<body>
<?php include('../navigation.php'); ?>
<div class="container events">
    <div id="events" class="row">
        <h1><?php echo $title ?></h1>
        <?php
        foreach ($Event as $Item) {
            if ($Item['Datatype'] == "Text") {
                echo $Item['Source'];
            } else if ($Item['Datatype'] == "Image") {
                ?>
                <div class="event-image">
                    <ul class="slider">
                        <li class="visible"> <!-- Current visible slide -->
                            <img class='event-image' src='<?php echo $Item['Source'] ?>'/>
                        </li>

                </div>
                <?
            }
        }
        ?>
        <!-- Events will be appended dynamically here !-->
    </div>
</div>
<?php
include('../footer.php'); ?>
</body>