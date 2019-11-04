<?
include_once('/var/www/html/database/config.php'); #Include script, unless there is parent script that has already included
$EventObject = new Event();
$Event = $EventObject->displayEvent($_GET['id']); #Gets assoc array with structure: [Datatype, Source]. First element is always title.
