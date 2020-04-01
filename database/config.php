<?php
/**
 * @todo Create Exhaustive Delete Method
 * @body Create Delete Method in Login Object that cascades through database as well as image folder
 * @todo Make file upload in object into single transaction
 * @body Make file upload into single transaction, if database or file upload fails, both fail. Could try executing simultaneously or simply reversion of state
 */

/*Should only be used for API*/

#Creates, Reads, Updates and Destroys as well as Escaping inputs
class CRUD
{
    protected $connection;

    #Declare error array to feedback any errors
    public $errors;

    public function __construct()
    {
        #Get database information set in environment variables
        $username = getenv("DatabaseUsername");
        $password = getenv("DatabasePassword");
        $host = getenv("DatabaseHost");
        $database = getenv("Database");

        #Connect to database when object is instantiated
        if (!isset($this->connection)) {
            #Create MySQLi connection
            $this->connection = new mysqli($host, $username, $password, $database);

            #If connection fails
            if (!$this->connection) {
                #Alerts and aborts
                echo 'Cannot connect to database server';
                exit;
            }
        }

        #Return MySQLi connection
        return $this->connection;
    }

    public function getData($query)
    {
        #Send queries which return data as an array
        $result = $this->connection->query($query);

        #Declare an array to store results as rows in
        $arr_rows = [];
        #Loop through results
        while ($res = $result->fetch_assoc()) {
            #Store each row as a an array inside arr_rows
            $arr_rows[] = $res;
        }

        #Return result
        return $arr_rows;
    }


    public function Execute($query)
    {
        #Executes the passed query
        $result = $this->connection->query($query);
        #If execution succeeds
        if ($result) {
            #Return that execution has succeeded
            return true;
        } #If execution fails
        else {
            #Report error
            $this->errors[] = $this->connection->error;
            return false;
        }
    }

    public function Escape($value)
    {
        #Return escaped version of passed value
        return $this->connection->real_escape_string($value);
    }


    protected function getImage($img_id, $event_id)
    {
        #Get the event creators username
        $username = $this->getData("SELECT User FROM UserEventsLinked WHERE EventID='" . $event_id . "'")[0]['User'];
        #Get image filename and extension from database
        $image_result = $this->getData("SELECT Filename, Filetype FROM Images WHERE User='$username' AND FileID='$img_id'")[0];
        #Compile result into image filepath
        return "/events/images/$username/{$image_result['Filename']}.{$image_result['Filetype']}";
    }

}

class Users extends CRUD
{
    public function __construct()
    {
        #Run parent constructor, since it is not implicitly done in PHP
        parent::__construct();
    }

    protected function PasswordValidate($password)
    {

        if (strlen($password) >= 8 #Password length is >=8
            and preg_match_all("/[0-9]/", $password) >= 1 #Password contains one or more numbers
            and preg_match_all("/[A-Z]/", $password) >= 1 #Password contains one or more uppercase characters
            and preg_match_all("/[a-z]/", $password) >= 1)#Password contains one or more lowercase character
        {
            #If password strong enough escape and return
            return $password;
        }

        #If password is not strong enough, fail
        $this->errors = ["Password is not strong enough!"];
        return false;
    }

    protected function notEmpty($data, $desired)
    {
        #Loop through each element in "desired" array
        foreach ($desired as $item) {

            #If an element in desired is not in the data
            if (!(isset($data[$item]) && $data[$item] != "")) {
                {
                    #Fail validation
                    $this->errors[] = ucfirst($item) . " is empty!";
                    return false;
                }
            }
        }

        #If all desired fields are in data, return true
        return true;
    }

}

#Class for creation and validation of user accounts
class Register extends Users
{
    #Array for validation method to add data to that registration method can then access
    private $validated_data;

    public function __construct()
    {
        #Run parent constructor, since it is not implicitly done in PHP
        parent::__construct();
    }

    public function registerUser($registration_data)
    {
        #If not all data is present
        if (!$this->notEmpty($_POST, ["name", "email", "username", "password"])) {
            #Report error
            $this->errors[] = "Not all data is present!";
            #Fail registration
            return false;
        }

        #If validation is passed, $validation_data will contain valid, escaped fields
        if ($this->Validate($registration_data)) {
            #Insert user into database
            $insertion = $this->Execute("INSERT INTO Users(Username, Password, Email, Fullname, Salt) VALUES('{$this->validated_data['username']}', '{$this->validated_data['password']}', '{$this->validated_data['email']}', '{$this->validated_data['name']}', '{$this->validated_data['salt']}');");

            #Create user an image folder
            #Generate path for image folder
            $folder_source = "/var/www/tripout.tk/public_html/events/images/" . $this->validated_data['username'];
            $this->errors[] = $folder_source;
            #Create directory with 777 permissions
            $folder = mkdir($folder_source, 777);

            #If insertion into database and folder creation succeed, report
            if ($folder and $insertion) {
                return true;
            } else {
                #If insertion into database or folder creation report
                $this->errors[] = "User failed to be created!";
                return false;
            }
        } else {
            #If validation has failed, error array will contain error
            return false;
        }
    }

    public function Validate($registration_data)
    {
        #Escape all elements in registration data
        foreach ($registration_data as $key => $element) {
            $registration_data[$key] = $this->Escape($element);
        }

        #Call email validation method
        $email = $this->emailValidate($registration_data['email']);

        #Validate full name
        $full_name = $registration_data['name'];
        #If full name is over 255 characters, fail validation
        if (strlen($full_name) > 255) {
            $this->errors = ["Full name is too long!"]; #Report error
            return false;
        }
        #If full name contains numbers, fail validation
        if (preg_match('~[0-9]~', $full_name)) {
            $this->errors = ["Full name contains numbers!"]; #Report error
            return false;
        }

        #Call user validation method
        $username = $this->userValidate($registration_data['username']);

        #Validate password
        $password_valid = $this->PasswordValidate($registration_data['password']);
        if ($password_valid) {#If password is valid
            #Generate password data that can be put in database
            $password_details = $this->generateHash($registration_data['password']);
        }

        #If one of validation tests have failed, fail completely
        if (!$email or !$username or !$password_valid) {
            return false;
        }

        #Add data to array in object that can be used by another object,
        #avoids having to return a non-boolean
        $this->validated_data = ["name" => $full_name, "email" => $registration_data['email'],
            "username" => $registration_data['username'], "salt" => $password_details['salt'],
            "password" => $password_details['hash'],
        ];
        #Return that validation has been passed
        return true;
    }

    public function userValidate($username)
    {
        #If username is too long, fail validation
        if (strlen($username) > 20) {
            $this->errors = ["Username is too long!"];
            return false;
        }

        #If user table already contains username, fail validation
        if ($this->getData("SELECT Username from Users WHERE Username='$username'") != []) {
            #If there is a user in table with username, report error and fail validation
            $this->errors = ["Username already exists!"];
            return false;
        }

        #If validation is passed, return true
        return true;
    }

    public function emailValidate($string)
    {
        #If email matches regular expression for email
        if (preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^", $string)) {
            #If email already exists in database
            if ($this->getData("SELECT Email from Users WHERE Email='" . $string . "'") != []) {
                #Email already exists, report error and fail validation
                $this->errors[] = "Email already exists!";
                return false;
            }
        } else {
            #Email not in correct format, report error and fail
            $this->errors[] = "Email not valid";
            return false;
        }
        #If validation is passed, return true
        return true;
    }

    public function generateHash($password)
    {
        #Call salt generation method
        $salt = $this->generateSalt();
        #Return salted password hash along with salt
        return ["hash" => md5($password . $salt), "salt" => $salt];
    }

    private function generateSalt()
    {
        #String contains  all characters that can be included in salt
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcedfghijklmnopqrstuvwxyz0123456789!"£$%^&*()[];:@#~<>,.?/`¬¦|';
        #Declare salt string
        $salt = "";

        #Loop 6 times
        for ($index = 0; $index < 6; $index++) {
            #Add a random character from given characters to salt string
            $salt .= $characters[mt_rand(0, strlen($characters) - 1)];
        }

        #Return randomly generated salt
        return $salt;
    }
}

#Class for logging user into account and managing account details
class Login extends Users
{
    public function __construct()
    {
        parent::__construct();
        if (session_status() == PHP_SESSION_NONE) { #If session has not already been started, start it
            session_start();
        }
    }

    public function authenticateUser($username, $password)
    {
        #If data that is needed does not exist
        if (!$this->notEmpty($_POST, ["id", "password"])) {
            #Fail login
            return false;
        }

        #Regex expression for email format
        $email_format = "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^";

        #Escape username and password
        $username = $this->Escape($username);
        $password = $this->Escape($password);

        #Generate query for login

        #If username is in email format, treat as email field
        if (preg_match($email_format, $username)) {
            #If email exists in database
            if ($this->getData("SELECT Username from Users WHERE Email='$username'") != []) {
                #Get users salt
                $salt = $this->getData("SELECT Salt FROM Users WHERE Email='$username';")[0]['Salt'];
                #Generate login query
                $query = "SELECT * FROM Users WHERE Email='$username' AND Password='" . md5($password . $salt) . "';";
            }
        } #If username not in email format, treat as username field
        else {
            #If username exists in database
            if ($this->getData("SELECT Username from Users WHERE Username='$username'") != []) {
                #Get users salt
                $salt = $this->getData("SELECT Salt FROM Users WHERE Username='" . $username . "';")[0]['Salt'];
                #Generate login query
                $query = "SELECT * FROM Users WHERE Username='" . $username . "' AND Password='" . md5($password . $salt) . "';";
            } else {
                #If username does not exist in database
                $this->errors[] = "Username does not exist!";
                return false;
            }
        }
        #Execute login query
        $result = $this->getData($query);

        #Check if user exists in the database

        #If query has returned a result
        if (isset($result[0])) {
            #Return result (user data)
            return $result[0];
        } else {
            #No result, report that login has failed
            $this->errors[] = "Username/Password incorrect!";
            return false;
        }
    }

    public function Logout()
    {
        #Destroy existing session
        session_destroy();
    }

    public function loginDevice($data)
    {
        #Save user data to $_SESSION, thus 'logging in' device
        foreach ($data as $key => $element) {
            $_SESSION[$key] = $element;
        }
    }

    private function saveSession($field, $value)
    {
        if (isset($_SESSION[$field])) { #If field exists in session
            #Update session field with new field
            $_SESSION[$field] = $value;
        }
    }

    public function updateField($field, $value)
    {
        #Escape passed values
        $field = $this->Escape($field);
        $value = $this->Escape($value);
        #Load username from session so that it can be used as an unique identifier
        $username = $_SESSION['Username'];

        switch ($field) {
            #Update password field
            case "Password":
                if ($this->PasswordValidate($value)) { #If updated password passes strength tests
                    #Get salt from database
                    $salt = $this->getData("SELECT Salt FROM Users WHERE Username='$username'")[0]['Salt'];
                    #Generate new password hash from password and salt
                    $new_password = md5($value . $salt);
                    #Update users password, return whether update succeeds
                    return $this->Execute("UPDATE Users SET Password = '$new_password' WHERE Username = '$username'");
                } else {
                    #Password validation has failed, so fail
                    $this->errors[] = "Password not strong enough!"; #Report error
                    return false;
                }
                break;
            #Update username field
            case "Username":

                #Validate new username
                if ($this->getData("SELECT Username FROM Users WHERE Username='$value';") != []) { #If username already exists in database
                    $this->errors[] = "Username already exists!";
                    return false;
                }
                if (strlen($value) > 20) { #If username is too long
                    $this->errors[] = "Username is too long!"; #Report error
                    return false;
                }

                #Update session data with new field value
                $this->saveSession($field, $value);

                #Rename user directory

                #Current users image folder
                $user_directory = "var/www/tripout.tk/public_html/events/images/" . $username;
                #New users image folder
                $new_directory = "var/www/tripout.tk/public_html/events/images/" . $value;
                rename($user_directory, $new_directory); #Update images folder name to new username
                return $this->Execute("UPDATE Users SET Username='$value' WHERE Username='$username'"); #Update username in database

                break;
            #Update email field
            case "Email":
                $email_expression = "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^"; #Regex expression for email format
                if ($this->getData("SELECT Email FROM Users WHERE Email='$value';") == []) { #If email does not already exist in database
                    if (preg_match($email_expression, $value)) { #If  email is in correct format
                        $this->saveSession($field, $value); #Update session data with new field value
                        return $this->Execute("UPDATE Users SET Email='$value' WHERE Username='$username';"); #Update email in database
                    } else {
                        $this->errors[] = "Email is not in a valid format!"; #Report error
                        return false;
                    }
                } else {
                    $this->errors[] = "Email already registered!"; #Report error
                    return false;
                }
                break;

        }
        $this->errors["Input field not valid!"];
        return false; #If doesn't match a case, return false
    }
}

#Class for uploading images to server as user and showing images that belong to user
class Images extends Users
{
    public function __construct()
    {
        parent::__construct();
        if (session_status() == PHP_SESSION_NONE) { #If session has not already been started, start it
            session_start();
        }
    }

    private function FileExists($filename, $file_extension)
    {
        #If image does not exist in users account, return that it doesn't
        if ($this->getData("SELECT User FROM Images WHERE Filename='" . $filename . "' and User='" . $_SESSION['Username'] . "' and Filetype='$file_extension';") == []) {
            return false;
        } else { #If image does exist in database, return that it does
            $this->errors[] = "File already exists!"; #Report error
            return true;
        }

    }

    private function ValidateUpload($file)
    {
        $path_parts = pathinfo($file['name']); //Break filename into parts
        $max_file_size = 20000000; #Currently at 20MB, if changed, must change manage.js file limit as well
        $extensions_allowed = ["jpeg", "jpg", "png"]; #Define file types that will pass the validation
        $temp = explode(".", $file['name']); #Get file extension by splitting filename into sections, separated by "." and selecting last section
        $file_extension = end($temp); #Get file extension
        if ($file['type'] == "image/png" || $file['type'] == "image/jpg" || $file['type'] == "image/jpeg") { #If image is a correct file type
            if (($file["size"] < $max_file_size) && in_array($file_extension, $extensions_allowed)) { #If file size and extension is correct
                if (!$this->FileExists($path_parts['filename'], $path_parts['extension'])) { #If file does not already exist
                    return ['filename' => $path_parts['filename'], 'extension' => $path_parts['extension']];#Return filename and file extension
                } else {
                    $this->errors[] = "File already exists!"; #Report error
                    return false;
                }
            } else {
                $this->errors[] = "File size or extension or wrong!"; #Report error
                return false;
            }
        } else {
            #Report error, stating how many megabytes uploaded file exceeds the limit by
            $this->errors[] = "Image size is " . ($max_file_size - $file['size']) / 1000000 . " megabytes too big!";
            return false;
        }
    }

    private function ImageEntry($file_name, $extension)
    {
        $file_id = md5($_SESSION['Username'] . $file_name . $extension); #Generate image ID that will be unique
        $query = "INSERT INTO Images(User, Filename, Filetype, FileID) VALUES('" . $_SESSION['Username'] . "', '$file_name','$extension', '$file_id');"; #Compile insertion query
        if ($this->Execute($query)) { #Execute insertion query, if succeeds
            return true;
        } else { #If insertion fails, report error
            $this->errors[] = "Failed to insert into database";
            return false;
        }
    }

    public function UploadImage($file_arr)
    {
        $file_validation = $this->ValidateUpload($file_arr); #Validate file, returns image data if validation is passed
        if ($file_validation != false) { #If file passes validation
            $upload_directory = "../events/images/" . $_SESSION['Username'] . "/"; #Relative file path to users image folder
            $upload_path = realpath(getcwd() . "/" . $upload_directory) . "/" . basename($file_arr['name']); #Absolute file path to image file destination
            move_uploaded_file($file_arr['tmp_name'], $upload_path); #Move the uploaded file to destination
            $this->ImageEntry($file_validation['filename'], $file_validation['extension']); #Add image to database
            return ["success" => true, "filepath" => $upload_path]; #Return success
        } else {
            return false; #Return fail, error will have been placed in errors array
        }
    }

    public function DisplayImages($username)
    {
        return $this->getData("SELECT Filename, Filetype, FileID FROM Images WHERE User='" . $username . "';"); #Return array of all images belonging to user
    }
}

#Class for validating and processing all event content and adding event to database
class addEvent extends CRUD
{
    #Declare event id variable here so that all methods can access it easily
    private $new_event_id;
    #Declare variables to store data in once validation has been passed, so that it can be accessed by creation methods
    private $address_details;
    #As table will vary on occurrence, create a query that can be changed by validation method depending on occurrence
    private $occurrence_details;
    #Declare array to append validated content sections to
    private $event_content;

    public function __construct()
    {
        parent::__construct();
    }

    #The following methods can be private as they will only be called by
    # the eventCreate method
    private function validateLocation($address_data)
    {
        #Remove whitespace and convert all characters in postcode to uppercase
        $postcode = strtoupper(str_replace(' ', '', $address_data['postcode']));
        #Check if postcode matches one of the accepted expressions
        if (!preg_match("/^[A-Z]{1,2}[0-9]{2,3}[A-Z]{2}$/", $postcode) &&
            !preg_match("/^[A-Z]{1,2}[0-9][A-Z][0-9][A-Z]{2}$/", $postcode) &&
            !preg_match("/^GIR0[A-Z]{2}$/", $postcode)) {
            #If post code is not in correct format, report error and fail
            $this->errors[] = "Postcode is not valid!";
            return false;
        }
        #Check that address will fit in database and is of right datatype
        if (strlen($address_data['line1']) > 128
            or strlen($address_data['line2']) > 128
            or strlen($address_data['line2']) > 128
            or strlen($address_data['town']) > 128
            or !is_numeric($address_data['zoom'])
            or !is_numeric($address_data['lat'])
            or !is_numeric($address_data['lng'])) {
            $this->errors[] = "Address is not valid! {$address_data['line1']}";
            return false;
        }
        #Escape all location data
        foreach ($address_data as $key => $value) {
            $address_data[$key] = $this->Escape($value);
        }
        #Add location data to array that will be used to insert data into database
        $this->address_details = $address_data;
        return true;
    }

    private function validateOccurrence($occurrence_data)
    {
        $start_time = date("G:i:00", strtotime($occurrence_data['start_time']));
        $end_time = date("G:i:00", strtotime($occurrence_data['end_time']));

        #If start time or end time could not be converted to a date object, fail
        if ($start_time == false or $end_time == false) {
            $this->errors[] = "Start or end time not in correct format!"; #Report error
            return false;
        }

        #Validate that start time and end time are possible
        if (date("Hi", $start_time) > date("Hi", $end_time)) { #If start time is after end time
            $this->errors[] = "Start and end time is not possible!"; #Report error
            return false;
        }

        #Escape all occurrence data
        foreach ($occurrence_data as $key => $value) {
            $occurence_data[$key] = $this->Escape($value);
        }

        #Add time and type to occurrence array
        $this->occurrence_details['start_time'] = $start_time;
        $this->occurrence_details['end_time'] = $end_time;
        $this->occurrence_details['type'] = $occurrence_data['type'];

        #Validate data based on occurrence type
        switch ($occurrence_data['type']) {
            case "once":
                #Create date objects for start and end date
                $start_date = date_format(date_create_from_format("d/m/Y", $occurrence_data['start_date']), "Y-m-d");
                $end_date = date_format(date_create_from_format("d/m/Y", $occurrence_data['end_date']), "Y-m-d");

                #Check that start date is before end date
                if ($start_date > $end_date) {
                    $this->errors[] = "Event starts before it finishes!";
                    return false;
                }
                $this->occurrence_details['query'] = "INSERT INTO SingleEvent(EventID, StartDate, EndDate) VALUES ('{$this->new_event_id}', '{$start_date}', '{$end_date}')";
                return true;
                break;
            case "daily":
                #No query needed for daily as start and finish time already created
                $this->occurrence_details['query'] = "once";
                return true;
                break;
            case "weekly":
                #Check that weekday exists in week
                if ($occurrence_data['day'] > 6) {
                    $this->errors[] = "Weekday is not in week!";
                    return false;
                }
                $this->occurrence_details['query'] = "INSERT INTO WeeklyEvent(EventID, DayOfWeek) VALUES('{$this->new_event_id}', {$occurrence_data['day']});";
                return true;
                break;

            case "monthly":
                #Check that day and week are in valid range
                if ($occurrence_data['week'] > 5 or $occurrence_data['day'] > 6) {
                    $this->errors[] = "Week or day not in valid range!";
                }
                #Check that event does sometimes occur in year
                if ($occurrence_data['week'] == 5 and $occurrence_data['day'] > 2) {
                    $this->errors[] = "Day does not exist in month!";
                }
                $this->occurrence_details['query'] = "INSERT INTO MonthlyEvent(EventID, DayOfWeek, WeekOfMonth) VALUES('{$this->new_event_id}', {$occurrence_data['day']}, {$occurrence_data['week']}); ";
                return true;
                break;

            case "yearly":
                #Check if month and day are in valid range
                if ($occurrence_data['month'] > 12 or $occurrence_data['day'] > 31) {
                    $this->errors[] = "Month or day not in valid range!"; #Report error
                    return false;
                }
                #Compile a data that can be inserted into database, year does not matter as annual
                $date = '2020' . $occurrence_data['month'] . $occurrence_data['day'];
                $date = date("Y-m-d", strtotime($date));
                $this->occurrence_details['query'] = "INSERT INTO AnnualEvent(EventID, Date) VALUES('$this->new_event_id', $date))";
                return true;
                break;

        }
        #If no case is met, report error
        $this->errors[] = "Occurrence type does not exist!";
        return false;
    }

    private function validateContent($content)
    {
        $element_index = 0;
        foreach ($content as $section) {
            if ($section['data_type'] == "text") { #If content is text

                #Escape markdown text
                $data_src = $this->Escape($section['data_src']);

                #Add to content array
                $this->event_content[$element_index]["data_src"] = $data_src;
                $this->event_content[$element_index]["datatype"] = "text";
            } else if ($section['data_type'] == "image") { #If content is image

                #Escape all image IDs
                $image_position = 0;
                $image_array = [];
                foreach ($section['data_src'] as $image) {
                    $image_array[$image_position] = $this->Escape($image);
                    $image_position++;
                }

                #Generate MySQL array of all images
                $images = "('" . join("','", $section['data_src']) . "')";

                #Get number of elements in array
                $number_of_elements = sizeOf($section['data_src']);

                #Generate query
                $query = "SELECT COUNT(distinct fileID) = {$number_of_elements} as Owner FROM Images WHERE FileID IN $images AND User = '{$_SESSION['Username']}'";
                #If all data exists in table with user
                if ($this->getData($query)[0]['Owner'] != 1) {
                    #If some images don't belong to user, report error and fail
                    $this->errors[] = "Some images don't belong to user!";
                    return false;
                }

                #Add to content
                $this->event_content[$element_index]["data_src"] = $section['data_src'];
                $this->event_content[$element_index]["datatype"] = "image";
            } else {
                $this->errors[] = "Unsupported datatype!";
                return false;
            }
            $element_index += 1;
        }
        return true;
    }

    private function addLocation()
    {
        $address = $this->address_details;
        #Insert location data into location table
        $this->Execute("INSERT INTO Location(EventID, PostCode, Line1, Line2, Town, Zoom, lat, lng) 
                               VALUES('{$this->new_event_id}', '{$address['postcode']}','{$address['line1']}', '{$address['line2']}', '{$address['town']}', {$address['zoom']}, {$address['lat']}, {$address['lng']});");
    }

    private function addOccurrence()
    {
        $occurrence = $this->occurrence_details;

        #Insert into occurrence linked table
        $linked = $this->Execute("INSERT INTO OccurrenceEventsLinked(EventID, StartTime, EndTime, OccurrenceType) 
                                        VALUES ('{$this->new_event_id}', '{$occurrence['start_time']}', '{$occurrence['end_time']}', '{$occurrence['type']}');");
        if ($occurrence['query'] == "once") {
            #If occurrence type is once, no specific table, so don't execute a second SQL query
            $specific_table = true;
        } else {
            #Insert into specific table for event
            $specific_table = $this->Execute($occurrence['query']);
        }
        #Return if both insertions succeeded
        return $linked and $specific_table;
    }

    private function addContent()
    {
        #Set initial position in content
        $element_index = 0;
        foreach ($this->event_content as $section) { #Loop through content
            if ($section['datatype'] == "text") { #If content is text
                #Insert data as text
                $this->Execute("INSERT INTO EventContent(EventID, ContentOrder, Datatype, Content) VALUES('{$this->new_event_id}', $element_index, 'text', '{$section['data_src']}')");
            } else if ($section['datatype'] == "image") { #If content is an image
                foreach ($section['data_src'] as $image_key) { #Loop through array of images
                    #Insert each image into database
                    $this->Execute("INSERT INTO EventContent(EventID, ContentOrder, Datatype, Content) VALUES('{$this->new_event_id}', $element_index, 'image', '{$image_key}')"); #Insert each one into the database
                }
            }
            #Increment position of content variable for next section
            $element_index++;
        }
    }

    public function eventCreate($event_data)
    {
        #Escape event title
        $title = $this->Escape($event_data['event_title']);

        #Validate title
        if (strlen($title) >= 128) {
            $this->errors[] = "Title is too long!";
            return false;
        }

        #Generate unique ID for event
        $this->new_event_id = uniqid();

        #Call validation methods
        if (!$this->validateLocation($event_data['event_location'])
            or !$this->validateOccurrence($event_data['event_occurrence'])
            or !$this->validateContent($event_data['content'])) {
            return false;
        }


        #Insert event into linked event users table
        $linked_insertion = $this->Execute("INSERT INTO UserEventsLinked(User, EventID) VALUES('" . $_SESSION['Username'] . "', '$this->new_event_id')");

        #Insert event into events table
        $event_insertion = $this->Execute("INSERT INTO Events(Title,ID) VALUES('$title','$this->new_event_id'); ");

        #Call occurrence method to insert occurrence information to database
        $occurrence = $this->addOccurrence();

        #Call location method to insert location information to database
        $location = $this->addLocation();

        #Call content method to insert all content (images and text) into database
        $content = $this->addContent();

        #Return if all whether all methods were successful
        return $occurrence and $location and $content and $event_insertion and $linked_insertion;
    }
}

#Class for grabbing details about event so the event can be viewed
class showEvent extends CRUD
{
    #Initially set event validity to true
    private $event_valid = true;
    #Declared here so that all methods can use the same event ID
    private $event_id;

    public function __construct($event_id)
    {
        parent::__construct();

        #If session has not already been started, start it
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        #Escape passed event id so that all methods can use it safely
        if ($event_id != null) {
            $this->event_id = $this->Escape($event_id);
        }
        #Check that event exists in database
        $event = $this->getData("SELECT ID FROM Events WHERE ID = '{$this->event_id}';");
        if ($event == []) { #If event does not exist in the database
            #Set event fetching to fail and report error
            $this->errors[] = "Event does not exist!";
            $this->event_valid = false;
        } else { #If event exists in the database
            #Increment view counter by one
            $this->Execute("UPDATE Events SET Views = Views+1 WHERE ID='{$this->event_id}';");
        }

    }

    #Protected so that scope is this class and all child classes
    protected function getLocation()
    {
        #Get location data from location data, return first entry since each event only has one entry
        return $this->getData("SELECT PostCode, Line1, Line2, Town, Zoom FROM Location WHERE EventID='$this->event_id'")[0];

    }

    private function getOccurrence()
    {
        #Create array to map occurrence types to tables in database
        $occurrence_table_mappings = ["once" => "SingleEvent", "weekly" => "WeeklyEvent", "monthly" => "MonthlyEvent", "yearly" => "AnnualEvent"];

        #Generate query to select linked tables occurrence data
        $linked_query = "SELECT StartTime, EndTime, OccurrenceType FROM OccurrenceEventsLinked WHERE EventID = '{$this->event_id}'";
        #Get start and end time for linked table, as well as occurrence type
        $occurrence_information = $this->getData($linked_query)[0];

        #Get data from the relevant occurence table
        if ($occurrence_information['occurrence_type'] != "daily") {
            #Get desired table by looking up in table mapping
            $desired_table = $occurrence_table_mappings[$occurrence_information['OccurrenceType']];
            #Generate query using desired table lookup
            /** @noinspection SqlResolve */
            $query = "SELECT * FROM {$desired_table} WHERE EventID = '$this->event_id'";
            #Get first (and only) record resulting from query
            $new_info = $this->getData($query)[0];
            #Loop through result of record and add data to occurrence_information
            foreach ($new_info as $key => $value) {
                if ($key != "EventID") { #Append occurrence fields that aren't the EventID
                    $occurrence_information[$key] = $value;
                }
            }
        }
        #Return the occurrence information that has been fetched
        return $occurrence_information;
    }

    private function isLiked()
    {
        #Find if user has liked or disliked the event already
        $current_status = $this->getData("SELECT LikeBoolean FROM Popularity WHERE User='{$_SESSION['Username']}' AND EventID='{$this->event_id}';");

        if ($current_status == []) {
            #Return that the user has not like or disliked the event
            return -1;
        }

        #If there is a like record, return it (true = like, false = dislike)
        return intval($current_status[0]['LikeBoolean']);
    }

    private function getPopularity()
    {
        #Get how many likes the event has
        $likes = $this->getData("SELECT COUNT(LikeBoolean) as Likes FROM Popularity WHERE LikeBoolean=true and EventID='$this->event_id'");

        #Get how many dislikes the event has
        $dislikes = $this->getData("SELECT COUNT(LikeBoolean) as Dislikes FROM Popularity WHERE LikeBoolean=false and EventID='$this->event_id'");

        #Add like and dislike data about event to popularity array
        $popularity_data = [];
        $popularity_data["Likes"] = intval($likes[0]['Likes']);
        $popularity_data["Dislikes"] = intval($dislikes[0]['Dislikes']);

        #Add user like status (whether the user has liked/disliked the event or not)
        if (isset($_SESSION['Username'])) { #If user is logged in
            #Add if the user has liked/disliked the event or not
            $popularity_data['User'] = $this->isLiked();
        } else {
            $popularity_data['User'] = false;
        }

        return $popularity_data;
    }

    public function likeEvent($status)
    {
        #If user is not logged in, terminate liking process

        if (!isset($_SESSION['Username'])) {
            $this->errors[] = "You're not logged in!";
            return false;
        }
        #Find if user has already voted on the event
        $like_status = $this->isLiked();

        #Handle likes and dislike cases
        switch ($status) {
            case "like":
                #If user has not already voted on event
                if ($like_status == -1) {
                    #Create like record in database
                    $query = "INSERT INTO Popularity(EventID, User, LikeBoolean, Date) VALUES('{$this->event_id}', '{$_SESSION['Username']}', 1 , CURDATE())";
                } #If the user has already voted on the event
                else {
                    #Update the like record
                    $query = "UPDATE Popularity SET LikeBoolean = 1, Date = CURDATE() WHERE EventID = '{$this->event_id}' AND User='{$_SESSION['Username']}'";
                }
                break;
            case "dislike":
                if ($like_status == -1) {
                    #Create dislike record in database
                    $query = "INSERT INTO Popularity(EventID, User, LikeBoolean, Date) VALUES('{$this->event_id}', '{$_SESSION['Username']}', 0 , CURDATE())";
                } #If the user has already voted on the event
                else {
                    #Update the like record
                    $query = "UPDATE Popularity SET LikeBoolean = 0, Date = CURDATE() WHERE EventID = '{$this->event_id}' AND User='{$_SESSION['Username']}'";
                }
                break;
            default:
                $this->errors[] = "Vote isn't a like or dislike!";
        }
        #If query has been created
        if (isset($query)) {
            #Execute vote query
            $this->Execute($query);
            return $status;
        } else {
            #Else fail
            return false;
        }
    }

    private function viewCounter()
    {
        #Return how many views an event has
        return $this->getData("SELECT Views FROM Events WHERE ID='{$this->event_id}';")[0];
    }

    public function displayEvent()
    {
        #If event does not exist, fail
        if (!$this->event_valid) {
            return false;
        }

        #Declare array that will store content in a structure that can parsed easily.
        $event_data = [];

        #Add title to event_data
        $title = $this->getData("SELECT Title FROM Events WHERE ID='$this->event_id'")[0]['Title'];
        $event_data["Title"] = $title;

        #Add occurrence data to event_data
        $event_data["Occurrence"] = $this->getOccurrence();

        #Add location data to event_data
        $event_data["Location"] = $this->getLocation();

        #Add view counter to event_data
        $event_data["Views"] = $this->viewCounter();

        #Add popularity (likes/dislikes) data to event_data
        $event_data["Popularity"] = $this->getPopularity();

        #Get all content, in the order that is should be displayed
        $content = $this->getData("SELECT Content, Datatype FROM EventContent WHERE EventID='{$this->event_id}' ORDER BY ContentOrder");

        #Loop through content and add to event_data in correct form
        foreach ($content as $item) {
            if ($item['Datatype'] == "image") { #If content section is an image
                #Get image path
                $img_src = $this->getImage($item['Content'], $this->event_id);
                #Add image to event_data
                $event_data["Content"][] = ["Datatype" => "Image", "Source" => $img_src];
            } elseif ($item['Datatype'] == "text") { #If content section is text
                #Add text to event_data
                $event_data["Content"][] = ["Datatype" => "Text", "Source" => $item['Content']];
            }
        }
        #Return event data with all data compiled in it
        return $event_data;
    }
}

#Class to display all events in thumbnail formats
class Browse extends CRUD
{
    private $filter_fields = ["OccurrenceEventsLinked" => "OccurrenceType"]; #Structure [Table=>Field to filter by]

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
        return ["id" => $eventID, "description" => $description . "...", "thumbnail" => $thumbnail_src, "title" => $title];
    }

    public function allEvents()
    {
        return $this->getData("SELECT ID FROM Events");
    }

    #Function to compile all conditions and return event ID's that satisfy conditions
    public function filterEvent($conditions) #OPEN TO INJECTION AS EACH QUERY HAS NOT BEEN SANITIZED IN ARRAY
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
                    $query .= "IN(SELECT EventID FROM Location WHERE ( 3959 * acos( cos( radians(" . $lat . ") ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(" . $long . ") ) + sin( radians(" . $lat . ") ) * sin( radians( lat ) ) ) ) < $range)";

                } else {
                    $list_of_conditions = "'" . implode("','", $array) . "'";
                    $query .= "IN(SELECT EventID FROM $field WHERE " . $this->filter_fields[$field] . " IN($list_of_conditions)) ";
                }
                $array_position += 1;
            }
        }
        return $this->getData($query);
    }


}

#Class to give all appropriate statistics to be processed at frontend
class Statistics extends showEvent
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
        $sql = "SELECT LikeBoolean, Date FROM Popularity WHERE Date BETWEEN $current AND '$today' AND EventID = '$eventID'";
        return ["range" => $range, "statuses" => $this->getData($sql)];
    }

    public function getEvents()
    { #Return all users events
        $user = $this->Escape($_SESSION['Username']);
        return $this->getData("SELECT Events.* FROM Events, UserEventsLinked WHERE UserEventsLinked.User='$user' AND UserEventsLinked.EventID = Events.ID;");
    }

    public function eventLikes($eventID)
    { #Retun how many likes and dislikes an event has
        $eventID = $this->Escape($eventID);
        #Get how many likes the event has
        $likes = $this->getData("SELECT COUNT(LikeBoolean) FROM Popularity WHERE LikeBoolean=true and EventID='$eventID'")[0]['COUNT(LikeBoolean)'];
        #Get how many dislikes the event has
        $dislikes = $this->getData("SELECT COUNT(LikeBoolean) FROM Popularity WHERE LikeBoolean=false and EventID='$eventID'")[0]['COUNT(LikeBoolean)'];
        return ["likes" => $likes, "dislikes" => $dislikes];
    }

    public function eventPopularity()
    {
        $eventData = [];
        $user = $this->Escape($_SESSION['Username']);
        $events = $this->getData("SELECT Events.Views, Events.Title, Events.ID FROM Events, UserEventsLinked WHERE UserEventsLinked.User='$user' and UserEventsLinked.EventID = Events.ID");
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
