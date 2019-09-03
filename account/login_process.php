<?
/**Login user REST API**/

include_once('/var/www/html/database/config.php'); #Include script, unless there is parent script that has already included
$database = new UserDB();
if ($database->NotEmpty($_POST, ["id", "password"])) { #Check if all wanted fields exist in posted data and are not empty
    #Grab data from POST and escape strings ready for query:
    $username = $database->Escape($_POST['id']);
    $password = md5($_POST['password']);
    if ($database->EmailFormat($username)) { #Check if ID is an email, if not assume user is logging in with username
        $query = "SELECT * FROM Users WHERE Email='" . $username . "' AND Password='" . $password . "';";
    } else {
        $query = "SELECT * FROM Users WHERE Username='" . $username . "' AND Password='" . $password . "';";
    }
    $result = $database->getData($query)[0]; #Since only one user will exist with that email (see validation in 'reg_process.php'), get first result in array
    if ($result == []) { #If no users are found, login failed
        echo json_encode(array('success' => 0, 'errors' => ['Username or Password incorrect!']));
    } else { #If login successful, save user information to $_SESSION
        $login = new Login();
        $login->LoginDevice($result); #Login device using details given by array
        echo json_encode(array('success' => 1)); #Return success
    }
} else {
    echo json_encode(array('success' => 0, 'errors' => $database->errors));

}
