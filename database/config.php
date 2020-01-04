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

#Creates, Reads, Updates and Destroys as well as Escaping inputs
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

#Class for creation and validation of user accounts
class UserDB extends CRUD
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
                $salt = $this->getData("SELECT Salt FROM Users WHERE Username='" . $username . "';")[0]['Salt'];
                $query = "SELECT * FROM Users WHERE Username='" . $username . "' AND Password='" . md5($password . $salt) . "';";#Using Email or Username not redundant since salt is random and so could be identical
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

    public function EmailFormat($string)
    {
        if (preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^", $string)) {
            return true;
        } else {
            return false;
        }
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

#Class for logging user into account and managing account details
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
        $_SESSION["Backup" . $field] = $_SESSION[$field];
        $_SESSION[$field] = $value;
        if ($this->UpdateField($field)) {
            return true;
        } else {
            $_SESSION[$field] = $_SESSION["Backup" . $field];
            unset($_SESSION["Backup" . $field]);
            return false;
        }

    }

    private function UpdateField($field)
    { #Push field changes to database, $_SESSION key's should be identical to field names
        if ($field == "Password") {
            $_SESSION['Password'] = md5($_SESSION['Password'] . $this->getData("SELECT Salt FROM Users WHERE Username = '" . $_SESSION['Username'] . "';")[0]['Salt']);
        }
        if ($field == "Username") {
            if (!$this->UserExists($_SESSION['Username'])) { #If new username doesn't already exist in database, apply username update
                rename("../events/images/" . $_SESSION['BackupUsername'], "../events/images/" . $_SESSION['Username']); #Rename users image directory
                return ($this->Execute("UPDATE Users SET Username='" . $_SESSION[$field] . "' WHERE Username = '" . $_SESSION['BackupUsername'] . "'"));
            } else {
                $this->errors[] = "Username failed to update";
                $_SESSION['Username'] = $_SESSION['OldUsername'];
                return false;
            }

        } else {
            $query = $this->getData("SELECT Editable FROM LinkedEditable WHERE UserField='$field';");
            if ($query[0] == true) {
                return ($this->Execute("UPDATE Users SET $field='$_SESSION[$field]' WHERE Username='" . $this->Escape($_SESSION['Username']) . "';")); #Insert $_SESSION value into database, return boolean regarding success
            } else {
                $this->errors[] = "Field cannot be updated!";
                return false;
            }
        }
    }

    public function UserExists($username)
    { #Check if username exists in users
        #Parameter should be escaped before being passed
        if ($this->getData("SELECT Username from Users WHERE Username='$username'") == []) {
            return false; #Username does not already exist
        } else {
            $this->errors[] = "Username already exists!" . $_SESSION['Username'];

            return true; #Username already exists
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

#Class for uploading images to server as user
class myImages extends CRUD
{#For displaying images
    public function __construct()
    {
        parent::__construct();
        if (session_status() == PHP_SESSION_NONE) { #If session has not already been started, start it!
            session_start();
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

    private function Validate($file, $username)
    {
        $pathparts = pathinfo($file['name']);
        $MaxFileSize = 20000000; #Currently at 20MB, if changed, must change manage.js file limit as well
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

    public function FileExists($filename, $fileextension, $username)
    {
        if ($this->getData("SELECT User FROM Images WHERE Filename='$filename' and User='" . $username . "' and Filetype='$fileextension';") == []) { #If record with image on already exists, return true
            return false;
        } else {
            $this->errors[] = "File already exists!";
            return true;
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

    public function DisplayImages($username)
    {
        return $this->getData("SELECT Filename, Filetype, FileID FROM Images WHERE User='" . $username . "';");
    }
}

#Class for validating and processing all event content and adding event to database
class addEvent extends CRUD
{
    private $supported_tags = ["html", "body", "h1", "h2", "h3", "h4", "h5", "span", "p", "b", "div", "em", "strong", "a", "ul", "li", "ol", "br", "font", "i", "u"];

    public function __construct()
    {
        parent::__construct();
    }

    public function addLocation($eventID, $address)
    {
        foreach ($address as $key => $value) {
            $address[$key] = $this->Escape($value);
        }
        $postcode = $address['postcode'];
        $line1 = $address['line1'];
        $line2 = $address['line2'];
        $town = $address['county'];
        $zoom = $address['zoom'];
        $lat = $address['lat'];
        $lng = $address['lng'];
        $this->Execute("INSERT INTO Location(Event, PostCode, Line1, Line2, Town, Zoom, lat, lng) VALUES('$eventID', '$postcode','$line1', '$line2', '$town', $zoom, $lat, $lng )");
        #Address array filled with all details required for map
    }

    public function addOccurence($eventID, $occurence)
    {
        foreach ($occurence as $key => $value) { #Escape all values in array
            $occurence[$key] = $this->Escape($value);
        }
        $eventID = $this->Escape($eventID);
        $starttime = date("G:i:00", strtotime($occurence['starttime']));
        $endtime = date("G:i:00", strtotime($occurence['endtime']));
        $type = $occurence['type'];
        switch ($type) {
            case "once":
                $startdate = date_format(date_create_from_format("d/m/Y", $occurence['startdate']), "Y-m-d");
                $enddate = date_format(date_create_from_format("d/m/Y", $occurence['enddate']), "Y-m-d");
                return $this->Execute("INSERT INTO Occurrence(Type, Event, StartDate, EndDate, StartTime,EndTime) VALUES('Once', '$eventID', '$startdate','$enddate','$starttime', '$endtime')");
            case "daily":
                return $this->Execute("INSERT INTO Occurrence(Type, Event, StartTime, EndTime) VALUES('Daily', '$eventID','$starttime', '$endtime')");
            case "weekly":
                $day = $occurence['day'];
                return $this->Execute("INSERT INTO Occurrence(Type, Event, StartTime, EndTime, Day) VALUES('Weekly', '$eventID','$starttime', '$endtime', '$day')");
            case "monthly":
                $day = $occurence['day'];
                $week = $occurence['week'];
                return $this->Execute("INSERT INTO Occurrence(Type, Event, StartTime, EndTime, Day, Week) VALUES('Monthly', '$eventID','$starttime', '$endtime', '$day', '$week')");
            case "yearly":
                $day = $occurence['day'];
                $month = $occurence['month'];
                return $this->Execute("INSERT INTO Occurrence(Type, Event, StartTime, EndTime, Day, Month) VALUES('Yearly', '$eventID','$starttime', '$endtime', '$day', '$month')");
        }
    }

    public function addContent($id, $datatype, $content, $position)
    { #Add an event content section
        $content = $this->Escape($content);

        /** @var integer $position */
        return $this->Execute("INSERT INTO EventContent(EventID, ContentOrder, Datatype, Content) VALUES('$id',$position, '$datatype','$content')");
    }

    public function eventCreate($title, $username) #Create event in table, return id on success or false on failure. MUST be executed before any other event operations!
    { #Need to check uniqueness of event yet!
        $id = uniqid();
        $title = $this->Escape($title); #Username loaded from database, already escaped
        if ($this->Execute("INSERT INTO Events(Title,ID,User) VALUES('$title','$id','$username')")) {
            return $id;
        } else {
            return false;
        }

    }

    public function checkTags($string) #Check to see if all tags in content are in the HTML whitelist
    {
        $DOM = new DOMDocument();
        $DOM->loadHTML($string);
        foreach ($DOM->getElementsByTagName('*') as $element) { #Compare each element tag to $supported_tags
            if (!in_array($element->tagName, $this->supported_tags)) {
                echo $element->tagName . "\n";
                return false;
            }
        }
        return true;
    }
}

#Class for grabbing details about event so the event can be viewed
class Event extends CRUD
{
    public function __construct()
    {
        parent::__construct();
        if (session_status() == PHP_SESSION_NONE) { #If session has not already been started, start it!
            session_start();
        }
    }

    public function getLocation($eventID)
    {
        return $this->getData("SELECT PostCode, Line1, Line2, Town, Zoom FROM Location WHERE Event='$eventID'")[0];

    }

    public function getOccurence($eventID)
    {
        #Doesn't need to escape eventID as already escaped in displayEvent()
        return $occurence_information = $this->getData("SELECT Type, StartDate, StartTime, EndTime, EndDate, Day, Week, Month FROM Occurrence WHERE Event='$eventID'")[0];
    }

    public function allEvents() #Returns all event IDs
    {
        return $this->getData("SELECT ID FROM Events;");
    }

    public function displayEvent($eventID) #Displays single event given ID
    { ##SHOULD SPLIT PARSING CONTENT INTO SEPARATE FUNCTION##
        $eventID = $this->Escape($eventID);
        $content = $this->getData("SELECT Content, Datatype FROM EventContent WHERE EventID='" . $eventID . "' ORDER BY ContentOrder"); #Returns all content, in ascending order.
        $presentable_content = []; #Array that will store content in a nice structure that can parsed easily.
        $presentable_content[] = ["Datatype" => "Title", "Source" => $this->getData("SELECT Title FROM Events WHERE ID='$eventID'")[0]['Title']]; #Add title to array
        $presentable_content[] = ["Datatype" => "Occurrence", "Source" => $this->getOccurence($eventID)]; #Add event occurrence to array
        $presentable_content[] = ["Datatype" => "Location", "Source" => $this->getLocation($eventID)]; #Add location to array
        $presentable_content[] = ["Datatype" => "Views", "Source" => $this->viewCounter($eventID)]; #Add view counter to array
        $presentable_content[] = ["Datatype" => "Popularity", "Source" => $this->getPopularity($eventID)]; #Add likes per date
        foreach ($content as $item) { #Add
            if ($item['Datatype'] == "image") {
                $img_src = $this->getImage($item['Content'], $eventID);
                $presentable_content[] = ["Datatype" => "Image", "Source" => $img_src];
            } elseif ($item['Datatype'] == "text") {
                $presentable_content[] = ["Datatype" => "Text", "Source" => $item['Content']];
            }
        }
        $this->Execute("UPDATE Events SET Views = Views+1 WHERE ID='" . $eventID . "';");
        return $presentable_content;
    }

    public function getImage($imgID, $eventID)
    {
        $username = $this->getData("SELECT User FROM Events WHERE ID='" . $eventID . "'")[0]['User'];
        $image_result = $this->getData("SELECT Filename, Filetype FROM Images WHERE User='$username' AND FileID='$imgID'")[0];
        $img_src = "/events/images/$username/" . $image_result['Filename'] . "." . $image_result['Filetype'];
        return $img_src;
    }

    public function getPopularity($eventID)
    {
        #Get how many likes the event has
        $likes = $this->getData("SELECT COUNT(LikeBoolean) FROM Popularity WHERE LikeBoolean=true and Event='$eventID'");
        #Get how many dislikes the event has
        $dislikes = $this->getData("SELECT COUNT(LikeBoolean) FROM Popularity WHERE LikeBoolean=false and Event='$eventID'");
        if (isset($_SESSION['Username'])) {
            return ["user" => $this->isPopular($_SESSION['Username'], $eventID), "likes" => $likes[0]['COUNT(LikeBoolean)'], "dislikes" => $dislikes[0]['COUNT(LikeBoolean)']];
        } else {
            return ["user" => false, "likes" => $likes[0]['COUNT(LikeBoolean)'], "dislikes" => $dislikes[0]['COUNT(LikeBoolean)']];

        }

    }

    public function isPopular($user, $eventID)
    {
        $current_status = $this->getData("SELECT LikeBoolean FROM Popularity WHERE User='$user' AND Event='$eventID';");
        if ($current_status == []) { #If user has not liked or disliked the event return false
            return false;
        } else if ($current_status[0]['LikeBoolean'] == true) {
            return "like";
        } else if ($current_status[0]['LikeBoolean'] == false) {
            return "dislike";
        }
    }

    public function eventPop($eventID, $status)
    { #Add like record to database, including validation
        if (isset($_SESSION['Username'])) {
            $user = $this->Escape($_SESSION['Username']);
            $eventID = $this->Escape($eventID);
            $status = $this->Escape($status); #Status is whether like is true or false
            if ($this->isPopular($user, $eventID) == false) { #If user hasn't already voted on event, add record
                /** @var bool $status */
                $this->Execute("INSERT INTO Popularity(Event, User, LikeBoolean, Date) VALUES('$eventID', '$user', $status, CURDATE())");#Will fail if Event or User don't exist or if Status is not a boolean
            } else { #If user has voted before, change vote
                /** @var bool $status */
                $this->Execute("UPDATE Popularity SET LikeBoolean = $status, Date = CURDATE() WHERE Event = '$eventID' AND User='$user'"); #Update vote of users
            }
            return $status;
        } else {
            return "error";
        }
    }

    public function viewCounter($eventID)
    {
        return $this->getData("SELECT Views FROM Events WHERE ID='$eventID';")[0];
    }

}

#Class to display all events in thumbnail formats
class Featured extends Event
{
    public function __construct()
    {
        parent::__construct();
    }

    public function eventSnippet($eventID)
    {
        #Returns all information needed to display event as snippet
        $eventID = $this->Escape($eventID); #Escape eventID to be safe
        $description = $this->getData("SELECT Content FROM EventContent WHERE EventID='$eventID' AND Datatype='text' ORDER BY ContentOrder ASC LIMIT 1;")[0]['Content']; #Get the first paragraph of the event as the description, will be wrapped later
        $title = $this->getData("SELECT Title FROM Events WHERE ID='$eventID';")[0]['Title']; #Get title of event

        $random_thumbnail_id = $this->getData("SELECT Content FROM EventContent WHERE Datatype='image' AND EventID='$eventID'")[0]['Content']; #Grab a random image from event
        $thumbnail_src = $this->getImage($random_thumbnail_id, $eventID);
        return ["id" => $eventID, "description" => substr(strip_tags($description), 0, 220) . "...", "thumbnail" => $thumbnail_src, "title" => $title];
    }

    private $filter_fields = ["Occurrence" => "Type"]; #Structure [Table=>Field to filter by]

    #Function to compile all conditions and return event ID's that satisfy conditions
    function filterEvent($conditions) #OPEN TO INJECTION AS EACH QUERY HAS NOT BEEN SANITIZED IN ARRAY
    { #Conditions array structure: [Field => Array[Value1, Value2...]]
        $query = "SELECT ID FROM Events ";
        if ($conditions != "all") {
            $array_position = 0;
            foreach ($conditions as $field => $array) {
                $field = $this->Escape($field);
                if ($array_position != 0) {
                    $query .= "AND ID ";
                }
                if ($array_position == 0) {
                    $query .= "WHERE ID ";
                }
                if ($field == "Name") {
                    $title = $this->Escape($array[0]);
                    $query .= "IN(SELECT ID FROM Events WHERE INSTR(Title, '{$title}') > 0)";
                } else if ($field == "PostCode") { #Structure will be [Long, Lat, Range(Miles)]
                    $long = $array[0];
                    $lat = $array[1];
                    $range = $array[2];
                    $query .= "IN(SELECT Event FROM Location WHERE ( 3959 * acos( cos( radians(" . $lat . ") ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(" . $long . ") ) + sin( radians(" . $lat . ") ) * sin( radians( lat ) ) ) ) < $range)";

                } else {
                    $list_of_conditions = "'" . implode("','", $array) . "'";
                    $query .= "IN(SELECT Event FROM $field WHERE " . $this->filter_fields[$field] . " IN($list_of_conditions)) ";
                }
                $array_position += 1;
            }
        }
        return $this->getData($query);
    }


}

#Class to give all appropriate statistics to be processed at frontend
class Statistics extends Event
{ #Object to give statistics about events
    public function __construct()
    {
        parent:: __construct();
        if (session_status() == PHP_SESSION_NONE) { #If session has not already been started, start it!
            session_start();
        }
    }

    public function getLikeTrends($eventID, $range)
    { #Get number of likes each day, within a range->(in days)
        $today = date("Y-m-j");
        $current = date('Y-m-d', (strtotime('-' . $range . ' day', strtotime($today))));
        $eventID = $this->Escape($eventID);
        $sql = "SELECT LikeBoolean, Date FROM Popularity WHERE Date BETWEEN $current AND '$today' AND Event = '$eventID'";
        return ["range" => $range, "statuses" => $this->getData($sql)];
    }

    public function getEvents()
    { #Return all users events
        $user = $this->Escape($_SESSION['Username']);
        return $this->getData("SELECT * FROM Events WHERE User='$user';");
    }

    public function eventLikes($eventID)
    { #Retun how many likes and dislikes an event has
        $eventID = $this->Escape($eventID);
        #Get how many likes the event has
        $likes = $this->getData("SELECT COUNT(LikeBoolean) FROM Popularity WHERE LikeBoolean=true and Event='$eventID'")[0]['COUNT(LikeBoolean)'];
        #Get how many dislikes the event has
        $dislikes = $this->getData("SELECT COUNT(LikeBoolean) FROM Popularity WHERE LikeBoolean=false and Event='$eventID'")[0]['COUNT(LikeBoolean)'];
        return ["likes" => $likes, "dislikes" => $dislikes];
    }

    public function eventPopularity()
    {
        $eventData = [];
        $user = $this->Escape($_SESSION['Username']);
        $events = $this->getData("SELECT Views, Title, ID FROM Events WHERE User='$user'");
        foreach ($events as $event) {
            $eventData[] = ["Views" => $event['Views'], "Title" => $event['Title'], "ID" => $event['ID'], "Location" => $this->getLocation($event['ID'])];
        }
        return $eventData;
    }

    public function Views($eventID)
    { #Return how many views an event has
        $eventID = $this->Escape($eventID);
        return $this->getData("SELECT Views FROM Events WHERE ID='$eventID';")[0]["Views"];

    }

}
