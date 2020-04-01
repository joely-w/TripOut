<?
include_once('../database/config.php'); #Include script, unless there is parent script that has already included
$Events = new Browse();
$allSnippets = array();
if (!isset($_POST['filter'])) {
    foreach ($Events->allEvents() as $row) {
        $allSnippets[] = ($Events->eventSnippet($row['ID']));
    }
} else {
    foreach ($Events->filterEvent($_POST['conditions']) as $row) {
        $allSnippets[] = ($Events->eventSnippet($row['ID']));
    }
}
echo json_encode($allSnippets);

