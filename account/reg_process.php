<?php
/**Entirety of reg_process.php**/
include_once('../database/config.php'); #Include classes module
$database = new Register(); #Instantiate Register class

#Call registration method to validate and register user
if ($database->registerUser($_POST)) {#If registration succeeds, report success
    echo json_encode(["success" => 1, "errors" => $database->errors]);
} else {#If validation or registration fail, report report error
    echo json_encode(["success" => 0, "errors" => $database->errors]);
}