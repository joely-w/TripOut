<?php
include_once('../database/config.php'); #Include script, unless there is parent script that has already included
$database = new Login();
$Image = new myImages();
switch ($_POST['Command']) {
    case "update":
        if ($database->SaveSession($_POST["Field"], $_POST["Value"]) == true) {
            echo json_encode(['success' => 1]); #Return success
        } else {
            echo json_encode(['success' => 0, 'errors' => $database->errors[0]]); #Return failure and give error to display on form
        }
        break;
    case "logout":
        $database->Logout();
        echo json_encode(['logout' => 1,]); #Return logged out
        break;
    case "images":
        $images_arr = $Image->DisplayImages($_SESSION['Username']);
        $compiled_array = [];
        foreach ($images_arr as $image) {#Structure: Filename, Filetype
            $compiled_array[] = "/events/images/" . $_SESSION['Username'] . "/" . $image['Filename'] . "." . $image['Filetype'];
        }
        echo json_encode(['images' => $compiled_array]);
        break;
    case "account_fields":
        $fields = $database->AccountFields();
        $compiled_fields = [];
        foreach ($fields as $field) {
            $valueAttribute = ($field["Viewable"] ? $_SESSION[$field['UserField']] : "Hidden value"); #If field should not be viewed, don't send actual data for field
            $compiled_fields[] = ["UserField" => $field['UserField'], "Datatype" => $field['Datatype'], "valueAttribute" => $valueAttribute];
        }
        echo json_encode($compiled_fields);
        break;
}

