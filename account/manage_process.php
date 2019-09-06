<?php
include_once('/var/www/html/database/config.php'); #Include script, unless there is parent script that has already included
$database = new Login();
if($_POST['update']==true) { #If values are being updated execute UpdateField
    if ($database->UpdateField($_POST['field']) == true) {
        $success = 1;
    } else {
        $success = 0;
    }
    echo json_encode(array('success' => 1)); #Return success
}
elseif($_POST['delete']==true){
    #Make a method in Login for deletion, inherits from CRUD so should be straightforward

}
