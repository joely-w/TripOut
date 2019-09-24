<?
/**Upload image REST API**/
include_once('/var/www/html/database/config.php'); #Include script, unless there is parent script that has already included
$database = new myImages();
$upload = $database->UploadImage($_FILES["file"], $_SESSION['Username']);
if ($upload[0]==true) { #First array item will be success boolean, second will be file path
    echo json_encode(array('success' => 1, 'Username' => $_SESSION['Username'], 'Filepath' => $upload[1], 'errors' => $database->errors));

}
else{
    echo json_encode(array('success' => 0, 'errors' => $database->errors));

};
