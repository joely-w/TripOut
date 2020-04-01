<?
/**Entirety of single_event_process.php**/
include_once('../database/config.php'); #Include classes file
#Instantiate showEvent class and set event ID
$current_event = new showEvent($_POST['event_id']);
if ($_POST['post_status'] == "event") { #If user wants to view event
    #Get event data
    $event_data = $current_event->displayEvent();
    #Print event data in JSON
    echo json_encode(["event_data" => $event_data, "errors" => $current_event->errors]);
} else if ($_POST['post_status'] == "popularity") { #If user wants to like or dislike an event
    #Call like method
    $like_status = $current_event->likeEvent($_POST['like_type']);
    echo json_encode(["status" => $like_status, "errors" => $current_event->errors]);
}

