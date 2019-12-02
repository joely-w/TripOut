<?
include_once('/var/www/html/database/config.php'); #Include script, unless there is parent script that has already included
$Stats = new Statistics();
$CallbackData = [];
$eventID = $_POST['id'];
foreach ($_POST['request'] as $item) {
    if ($item == "events") {
        $CallbackData["events"] = $Stats->getEvents();
    } else if ($item == "likes") {
        $CallbackData["likes"] = $Stats->eventLikes($eventID);
    } else if ($item == "views") {
        $CallbackData["views"] = $Stats->Views($eventID);
    } else if ($item == "liketrend") {
        $CallbackData["liketrend"] = $Stats->getLikeTrends($eventID, $_POST['range']);
    }
}
echo json_encode($CallbackData);