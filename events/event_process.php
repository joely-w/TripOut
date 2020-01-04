<?
include_once('/var/www/html/database/config.php'); #Include script, unless there is parent script that has already included
$Events = new Featured();
$allSnippets = array();
if (!isset($_POST['filter'])) {
    foreach ($Events->filterEvent("all") as $row) {
        $allSnippets[] = ($Events->eventSnippet($row['ID']));
    }
} else {
    foreach ($Events->filterEvent($_POST['conditions']) as $row) {
        $allSnippets[] = ($Events->eventSnippet($row['ID']));
    }
}
echo json_encode($allSnippets);

