<?php
/**Process registration REST API**/
include_once('/var/www/html/database/config.php'); #Include script, unless there is parent script that has already included
$database = new UserDB(); #Instantiate CRUD object with Validation options
if ($database->NotEmpty($_POST, ["name", "email", "username", "password"])) { #Check if all wanted fields exist in posted data and are not empty
    #Grab data from POST and escape strings ready for query:
    $username = $database->Escape($_POST['username']);
    $name = $database->Escape($_POST['name']);
    $email = $database->Escape($_POST['email']);
    $password = md5($_POST['password']);
    #Check if email is in correct format and check if email already exists
    if ($database->Email($email) and !($database->UserExists($username))) {
        $query = "INSERT INTO Users (Username, Password,Email,Fullname,Reputation) VALUES ('$username','$password','$email','$name', 0)";
        $result = $database->Execute($query);#Register user in database
    }
}

if ($result == true) { #Report back if the registration has succeeded
    echo json_encode(array('success' => 1));
} else {
    echo json_encode(array('success' => 0, 'errors' => $database->errors));
}