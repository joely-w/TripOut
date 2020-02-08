<?php
/**Process registration REST API**/
include_once('../database/config.php'); #Include script, unless there is parent script that has already included
$database = new Register(); #Instantiate CRUD object with Validation options
if ($database->NotEmpty($_POST, ["name", "email", "username", "password"])) { #Check if all wanted fields exist in posted data and are not empty
    #Grab data from POST and escape strings ready for query:
    $username = $database->Escape($_POST['username']);
    $name = $database->Escape($_POST['name']);
    $email = $database->Escape($_POST['email']);
    #Check if email is in correct format and check if email already exists
    if ($database->Email($email)) {
        if (!($database->UserExists($username, "Username"))) {
            $salt = $database->GenerateSalt();
            $password = md5($_POST['password'] . $salt);
            $query = "INSERT INTO Users (Username, Password,Email,Fullname,Reputation, Salt) VALUES ('$username','$password','$email','$name', 0, '$salt')";
            $result = $database->Execute($query);#Register user in database
        } else {
            $database->errors[] = "User already exists!";
        }
    } else {
        $database->errors[] = "Email already exists";
    }
}

if ($result == true) { #Report back if the registration has succeeded
    mkdir("/var/www/html/events/images/$username"); #If user inserted into database create folder
    echo json_encode(array('success' => 1));
} else {
    echo json_encode(array('success' => 0, 'errors' => $database->errors));
}