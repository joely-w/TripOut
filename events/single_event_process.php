<?
include_once('/var/www/html/database/config.php'); #Include script, unless there is parent script that has already included
$EventObject = new Event();
$Event = $EventObject->displayEvent($_POST['eventID']); #Gets assoc array with structure: [Datatype, Source]. First element is always title.
echo json_encode($Event); #Return information in parsable JSON