<?php
include_once('/var/www/html/database/config.php'); #Include script, unless there is parent script that has already included
session_start();
$Event = new Events();
$generatedEvent = $Event->eventCreate($_POST['eventTitle'], $_SESSION['Username']);
$element_index = 0;
if ($generatedEvent != false) {
    foreach ($_POST['content'] as $section) {
        if ($section['dataType'] == "text" and $Event->checkTags($section['dataSrc'])) { #If content piece is text, and not malicious, add to database
            $Event->addContent($generatedEvent, "text", $section['dataSrc'], $element_index);
        } elseif ($section['dataType'] == "image") {
            foreach ($section['dataSrc'] as $image_key) {
                echo $image_key;
                $Event->addContent($generatedEvent, "image", $image_key, $element_index);
            }
        }
        $element_index++;

    }
} else {
    #Failure message
}