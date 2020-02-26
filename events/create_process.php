<?php
include_once('../database/config.php'); #Include script, unless there is parent script that has already included
session_start();

$Event = new addEvent();
$generatedEvent = $Event->eventCreate($_POST['eventTitle'], $_SESSION['Username']);
$element_index = 0;
print_r($_POST['eventLocation']);
$Event->addOccurence($generatedEvent, $_POST['eventOccurence']);
$Event->addLocation($generatedEvent, $_POST['eventLocation']);
if ($generatedEvent != false) {
    foreach ($_POST['content'] as $section) {
        if ($section['dataType'] == "text") { #If content piece is text, and not malicious
            $Event->addContent($generatedEvent, "text", $section['dataSrc'], $element_index); #Add text to database
        } elseif ($section['dataType'] == "image") {
            foreach ($section['dataSrc'] as $image_key) {
                $Event->addContent($generatedEvent, "image", $image_key, $element_index); #Add image to database
            }
        }
        $element_index++;
    }
    echo json_encode(["success" => 1]);
} else {
    echo json_encode(['success' => 0, "errors" => $Event->errors]);
    #Failure message
}


