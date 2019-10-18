<?
include_once('/var/www/html/database/config.php'); #Include script, unless there is parent script that has already included
$Events = new Featured();
$allSnippets = array();
foreach ($Events->allEvents() as $row) {
    $allSnippets[] = ($Events->eventSnippet($row['ID']));
}
echo json_encode($allSnippets);

