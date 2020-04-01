<?php
/**Entirety of manage_process.php**/
include_once('../database/config.php'); #Include classes file
$database = new Login(); #Instantiate classes that will be used
$Image = new Images();
if (isset($_SESSION['Username'])) { #If user is logged in
    switch ($_POST['Command']) {
        case "update":
            if ($database->updateField($_POST['Field'], $_POST['Value'])) { #Call update method from login object
                echo json_encode(['success' => 1]); #Print update success
            } else {
                echo json_encode(['success' => 0, 'errors' => $database->errors]); #Print update success
            }
            break;
        case "logout":
            $database->Logout(); #Log user out
            echo json_encode(['logout' => 1,]); #Print logged out success
            break;
        case "images":
            $images_arr = $Image->DisplayImages($_SESSION['Username']); #Get all images from database belonging to user
            $compiled_array = []; #Initiate array to hold compiled image paths in
            foreach ($images_arr as $image) {#Loop through each image node
                $compiled_array[] = "/events/images/" . $_SESSION['Username'] . "/" . $image['Filename'] . "." . $image['Filetype']; #Compile image path from node and add to compiled_array
            }
            echo json_encode(['images' => $compiled_array]); #Print array of compiled images
            break;
        case "account_fields":
            $compiled_fields = [ #Return necessary data for the users account information fields
                "Email" => $_SESSION['Email'],
                "Fullname" => $_SESSION['Fullname'],
                "Username" => $_SESSION['Username']
            ];
            echo json_encode($compiled_fields); #Print field data
            break;
        case "upload_image":

    }
} else {#If user is not logged in, report error
    echo json_encode(["success" => 0, "errors" => ["Not logged in!"]]);
}

