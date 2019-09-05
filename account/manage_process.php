<?php
include_once('/var/www/html/database/config.php'); #Include script, unless there is parent script that has already included
$database = new Login();
if ($database->UpdateField($_POST['field']) == true) {
    $success = 1;
} else {
    $success = 0;
}
echo json_encode(array('success' => 1)); #Return success

