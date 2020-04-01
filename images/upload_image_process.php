<?
/**Entirety of upload_image_process.php**/
include_once('../database/config.php'); #Include classes file
$database = new Images();
$upload = $database->UploadImage($_FILES["image"]); //Get upload file
if ($upload['success'] == true) {#If file validates and uploads properly
    echo json_encode(['success' => 1]); #Report success

} else {#If upload fails
    echo json_encode(['success' => 0, 'errors' => $database->errors]);  #Report failure along with errors

}
