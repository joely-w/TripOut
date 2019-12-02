<?
include_once('/var/www/html/database/config.php'); #Include script, unless there is parent script that has already included
$EventObject = new Event();
if ($_POST['postStatus'] == "event") {
    $Event = $EventObject->displayEvent($_POST['eventID']); #Gets assoc array with structure: [Datatype, Source]. First element is always title.
    echo json_encode($Event); #Return information in parsable JSON
} else if ($_POST['postStatus'] == "popularity") {
    $LikeStatus = $EventObject->eventPop($_POST['eventID'], $_POST['likeType']);
    echo json_encode(array("status" => $LikeStatus));
}

