<?php
/**
 * @todo Create Delete Method
 * @body Create Delete Method in Login Object after database structure has been fully created,so as to ensure cascading is set up properly.
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
        }

    }

    private function UpdateField($field)
    { #Push field changes to database, $_SESSION key's should be identical to field names
        if ($field == "Password") {
            $_SESSION['Password'] = md5($_SESSION['Password']);
        }
        $query = $this->getData("SELECT Editable FROM LinkedEditable WHERE UserField='$field';");
        if ($query[0] == true) {
            return ($this->Execute("UPDATE Users SET $field='$_SESSION[$field]';")); #Insert $_SESSION value into database, return boolean regarding success
        } else {
            $this->errors[] = "Field cannot be updated!";
            return false;
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
    }
    public function DisplayImages(){
        $this->getData("SELECT Filename, Filetype FROM Images WHERE User='".$_SESSION['Username']."';");
    }
}