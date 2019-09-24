<?php
include_once('/var/www/html/database/config.php'); #Include script, unless there is parent script that has already included
$database = new Login();
$values = $_POST['data']; #Structure should be: 0: Command (update, destroy or logout), 1: Field name, 2: Field value
if ($values[0] == "update") { #If values are to be updated execute UpdateField
    if ($values[1] == "Username") {
        $_SESSION['OldUsername'] = $_SESSION['Username']; #Backup username in case update fails, in which case revert
    }
    if ($database->SaveSession($values[1], $values[2]) == true) {
        echo json_encode(array('success' => 1)); #Return success
    } else {
        echo json_encode(array('success' => 0, 'errors' => $database->errors[0])); #Return failure and give error to display on form
    }
} elseif ($_POST['delete'] == true) {
    #Make a method in Login for deletion, inherits from CRUD so should be straightforward

} elseif ($values[0] == "logout") {
    $database->Logout();
    echo json_encode(array('logout' => 1,)); #Return logged out

}

