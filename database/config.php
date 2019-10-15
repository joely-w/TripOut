<?php
/**
 * @todo Create Exhaustive Delete Method
 * @body Create Delete Method in Login Object that cascades through database as well as image folder
 * @todo Make file upload in object into single transaction
 * @body Make file upload into single transaction, if database or file upload fails, both fail. Could try executing simultaneously or simply reversion of state
 */

/*Should only be used for API*/

#Parent class for connection to database
class Database
{
    protected $connection;
    private $_username = "developer";
    private $_password = "3comicN*!b.";
    private $_host = "localhost";
    private $_database = "TripOut";

    #Database Constructor

    public function __construct()
    {
        #Connect to database when object is instantiated
        if (!isset($this->connection)) {

            $this->connection = new mysqli($this->_host, $this->_username, $this->_password, $this->_database);
            #Creates connection to database using MySQLi
            if (!$this->connection) {
                #Alerts and aborts if connection to database was unsuccessful
                echo 'Cannot connect to database server';
                exit;
            }
        }

        return $this->connection;
    }
}

class CRUD extends Database
{
    #The purpose of methods will be described alongside the function declarations


    public function __construct()
    {
        parent::__construct();
        #Run parent constructor, since it is not implicitly done in PHP
    }

    public function getData($query)
    {
        #Send Queries which return data as an array
        $result = $this->connection->query($query);
        if ($result == false) {
            #If query yields no result, don't return an array
            return false;
        }
        $arr_rows = array(); #Create an array to store results in, each row is a new result
        while ($res = $result->fetch_assoc()) {
            #Loop through results and store each new row in arr_rows array
            $arr_rows[] = $res;
        }
        return $arr_rows;
    }

    public function Execute($query)
    {
        #Executes query (returns boolean)
        $result = $this->connection->query($query);

        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    public function Escape($value)
    {
        return $this->connection->real_escape_string($value);
    }
}

class UserDB extends CRUD #Class for creation and validation of user accounts
{
    public $errors = [];

    public function __construct()
    {
        parent::__construct();
        #Run parent constructor, since it is not implicitly done in PHP
    }

    public function VerifyUser($username, $password)
    {
        $username = $this->Escape($username);
        $password = $this->Escape($password);
        if ($this->EmailFormat($username)) {
            if ($this->UserExists($username, "Email")) {
                $salt = $this->getData("SELECT Salt FROM Users WHERE Email='$username';");
                $query = "SELECT * FROM Users WHERE Email='$username' AND Password='" . md5($password . $salt) . "';";
                $result = $this->getData($query)[0]; #Since only one user will exist with that email (see validation in 'reg_process.php'), get first result in array
            } else {
                $this->errors[] = "Username/Password incorrect!";
                return False;
            }
        } else {
            if ($this->UserExists($username, "Username")) {
                $salt = $this->getData("SELECT Salt FROM Users WHERE Username='$username';")[0]['Salt'];
                $query = "SELECT * FROM Users WHERE Username='$username' AND Password='" . md5($password . $salt) . "';";#Using Email or Username not redundant since salt is random and so could be identical
                $result = $this->getData($query)[0];#Since only one user will exist with that email or username (see validation in 'reg_process.php'), get first result in array
            } else {
                $this->errors[] = "Username/Password incorrect! " . $username;
                return False;
            }
        }
        if ($result != []) { #If there is a result, log in user and report success
            $login = new Login();
            $login->LoginDevice($result); #Login device using details given by array
            return True;
        }
        $this->errors[] = "Username/Password incorrect!";
        return False; #No condition needed as implicit
    }

    public function UserExists($value, $identifier)
    { #Check if username exists in users
        #Parameter should be escaped before being passed
        if ($this->getData("SELECT Username from Users WHERE $identifier='$value'") == []) {
            return false; #Username does not already exist
        } else {
            return true; #Username already exists
        }
    }

    public function GenerateSalt()
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcedfghijklmnopqrstuvwxyz0123456789!"£$%^&*()[];:@#~<>,.?/`¬¦|'; #String with all characters that could be included in salt
        $salt = "";
        for ($index = 0; $index < 6; $index++) { #Generate random string from characters given, of length 6
            $salt .= $characters[mt_rand(0, strlen($characters) - 1)];
        }
        return $salt;
    }

    public function Email($string)
    {
        if ($this->EmailFormat($string)) { #Check if string follows correct email pattern
            if ($this->getData("SELECT Email from Users WHERE Email='" . $string . "'") == []) { #String should be passed as an escaped string, check if email already exists in database
                return true; #Email is in correct format and does not already exist in database
            } else {
                $this->errors[] = "Email already exists!";
                return false;

            }
        } else {
            $this->errors[] = "Email not valid";
            return false;
        }
    }

    public function EmailFormat($string)
    {
        if (preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^", $string)) {
            return true;
        } else {
            return false;
        }
    }

    public function NotEmpty($data, $desired)
    { #Check if each element in desired is in data and is not empty, since no empty elements should be in desired, emptiness checks are implicit
        foreach ($desired as $item) {
            if (!(isset($data[$item]) && $data[$item] != "")) {
                {#If an element in desired is not in the data, fail validation
                    $this->errors[] = ucfirst($item) . " is empty!";
                    return false;
                }
            }
        }
        return true;
    }
}

class Login extends CRUD
{
    public function __construct()
    {
        parent::__construct();
        if (session_status() == PHP_SESSION_NONE) { #If session has not already been started, start it!
            session_start();
        }
    }

    public function UserExists($username)
    { #Check if username exists in users
        #Parameter should be escaped before being passed
        if ($this->getData("SELECT Username from Users WHERE Username='$username'") == []) {
            return false; #Username does not already exist
        } else {
            $this->errors[] = "Username already exists!";

            return true; #Username already exists
        }
    }

    public function DeleteAccount($username)
    {
        $query = "DELETE FROM Users WHERE Username='$username'";
    }

    public function AccountFields()
    { #Returns array of editable fields
        return $this->getData("SELECT UserField,Datatype,Viewable FROM LinkedEditable WHERE Editable=1"); #Return each field name along with whether they are editable in an array
    }

    public function SaveSession($field, $value)
    {
        $value = $this->Escape($value); #Escape values before saving to session
        $field = $this->Escape($field);
        $_SESSION[$field] = $value;
        if ($this->UpdateField($field)) {
            return true;
        } else {
            return false;
        }

    }

    private function UpdateField($field)
    { #Push field changes to database, $_SESSION key's should be identical to field names
        if ($field == "Password") {
            $_SESSION['Password'] = md5($_SESSION['Password']);
        }
        if ($field == "Username") {
            if (!$this->UserExists($_SESSION['Username'])) { #If new username doesn't already exist in database, apply username update
                rename("../events/images/" . $_SESSION['OldUsername'], "../events/images/" . $_SESSION['Username']); #Rename users image directory
                unset($_SESSION['OldUsername']); #Removes backup, because who needs it at this point
                return ($this->Execute("UPDATE Users SET 'Username'='$_SESSION[$field]';"));
            } else {
                $this->errors[] = "Username failed to update";
                $_SESSION['Username'] = $_SESSION['OldUsername'];
                return false;
            }

        } else {
            $query = $this->getData("SELECT Editable FROM LinkedEditable WHERE UserField='$field';");
            if ($query[0] == true) {
                return ($this->Execute("UPDATE Users SET $field='$_SESSION[$field]';")); #Insert $_SESSION value into database, return boolean regarding success
            } else {
                $this->errors[] = "Field cannot be updated!";
                return false;
            }
        }
    }

    public function LoginDevice($data)
    { #Save login details to $_SESSION, thus 'logging in' device
        foreach ($data as $key => $element) {
            $_SESSION[$key] = $element;
        }
    }

    public function Logout()
    {
        session_destroy();
    }
}

class myImages extends CRUD
{#For displaying images
    public function __construct()
    {
        parent::__construct();
        if (session_status() == PHP_SESSION_NONE) { #If session has not already been started, start it!
            session_start();
        }
    }

    private function ImageEntry($filename, $extension, $username)
    {
        $fileID = md5($username . $filename . $extension);
        $query = "INSERT INTO Images(User,Filename,Filetype, FileID) VALUES('$username', '$filename','$extension', '$fileID');";
        if ($this->Execute($query)) {
            return true;
        } else {
            $this->errors[] = "Failed to insert into database";
        }
    }

    public function FileExists($filename, $fileextension, $username)
    {
        if ($this->getData("SELECT User FROM Images WHERE Filename='$filename' and User='" . $username . "' and Filetype='$fileextension';") == []) { #If record with image on already exists, return true
            return false;
        } else {
            $this->errors[] = "File already exists!";
            return true;
        }

    }

    private function Validate($file, $username)
    {
        $pathparts = pathinfo($file['name']);
        $MaxFileSize = 10000000; #Currently at 10MB, if changed, must change manage.js file limit as well
        $ExtensionsAllowed = array("jpeg", "jpg", "png"); #Define file types that will pass the validation
        $temp = explode(".", $file['name']); #Get file extension by splitting filename into sections, separated by "." and selecting last section
        $FileExtension = end($temp);
        if (($file['type'] == "image/png" || $file['type'] == "image/jpg" || $file['type'] == "image/jpeg")
            && ($file["size"] < $MaxFileSize)
            && in_array($FileExtension, $ExtensionsAllowed)
            && $this->FileExists($pathparts['filename'], $pathparts['extension'], $username) == false) { #If file passes format, size checking and has not already been uploaded by user, add to database and return true
            $this->ImageEntry($pathparts['filename'], $pathparts['extension'], $username);
            return true;
        } else {
            return false;
        }
    }

    public function UploadImage($file_arr, $username)
    {
        if (isset($file_arr["type"]) && $this->Validate($file_arr, $username)) {
            $sourcePath = $file_arr['tmp_name'];
            $targetPath = "../events/images/" . $username . "/" . $file_arr['name']; #Directory already created in registration process
            move_uploaded_file($sourcePath, $targetPath);
            return [true, $targetPath];
        } else {
            return [false, null];
        }
    }

    public function DisplayImages($username)
    {
        return $this->getData("SELECT Filename, Filetype, FileID FROM Images WHERE User='" . $username . "';");
    }
}

class Events extends CRUD
{
    public function __construct()
    {
        parent::__construct();
    }

    private function getTags($string)
    { #Return a list of all the tags in a string
        preg_match_all('~<(.*?)>~', $string, $output);
        return $output[0];
    }

    private $supported_tags = ["h1", "h2", "h3", "h4", "h5", "span", "p", "b"];

    public function checkTags($string)
    {
        $tags = $this->getTags($string);

        foreach ($tags as $tag) {
            $result = preg_replace('/[<>]/s', '', $tag);
            echo $result . "<br>";
            if (in_array($tag, $this->supported_tags)) {
                echo "Stop trying to insert $tag tags you scumbag";
            }

        }
    }

    public function processImages($list)
    {

    }
}