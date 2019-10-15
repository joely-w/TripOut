<?
/**Login user REST API**/

include_once('/var/www/html/database/config.php'); #Include script, unless there is parent script that has already included
$database = new UserDB();
if ($database->NotEmpty($_POST, ["id", "password"])) { #Check if all wanted fields exist in posted data and are not empty
    if ($database->VerifyUser($_POST['id'], $_POST['password'])) {
        echo json_encode(array('success' => 1)); #Return success
    } else {
        echo json_encode(array('success' => 0, 'errors' => $database->errors));
    }

}