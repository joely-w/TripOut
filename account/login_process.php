<?php
/**Login user REST API**/
/**Entirety of login_process.php**/
include_once('../database/config.php'); #Include classes file
$database = new Login(); #Instantiate login class


#Call user authentication method
$login_data = $database->authenticateUser($_POST['id'], $_POST['password']);

#If user successfully authenticated
if ($login_data != false) {
    #Initiate device session with user data
    $database->loginDevice($login_data);
    #Report success
    echo json_encode(['success' => 1]);
} #If user not successfully authenticated
else {
    #Report error
    echo json_encode(['success' => 0, 'errors' => $database->errors]);
}

