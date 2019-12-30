<?
include_once('/var/www/html/database/config.php'); #Include script, unless there is parent script that has already included
$Stats = new Statistics();
$CallbackData = [];
$eventID = $_POST['id'];
foreach ($_POST['request'] as $item) { #need to convert to switch
    switch ($item) {
        case "events":
            $CallbackData["events"] = $Stats->getEvents();
            break;
        case "likes":
            $CallbackData["likes"] = $Stats->eventLikes($eventID);
            break;
        case "views":
            $CallbackData["views"] = $Stats->Views($eventID);
            break;
        case "liketrend":
            $CallbackData["liketrend"] = $Stats->getLikeTrends($eventID, $_POST['range']);
            break;
        case "allevents":
            $CallbackData['allevents'] = $Stats->eventPopularity();
    }

} #Returns all data asked for, so data only needs to be requested once
echo json_encode($CallbackData);