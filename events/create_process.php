<?php
/**Entirety of create_process.php**/
include_once('../database/config.php'); #Include classes file
session_start(); #Start session so that user can be identified

#Instantiate add event class
$new_event = new addEvent();

#Submit event and report status
if ($new_event->eventCreate($_POST)) {
    #If event is created successfully
    echo json_encode(["success" => 1]);
} else {
    #If event creation fails, report error
    echo json_encode(["success" => 0, "errors" => $new_event->errors]);
}