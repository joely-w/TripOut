<?
include_once('../database/config.php'); #Include script, unless there is parent script that has already included
$Image = new Images();
$images_arr = $Image->DisplayImages($_SESSION['Username']);
$full_arr = [];
foreach ($images_arr as $image) {#Structure: Filename, Filetype
    $path = "/events/images/" . $_SESSION['Username'] . "/" . $image['Filename'] . "." . $image['Filetype'];
    $full_arr[] = [$path, $image['FileID']];
}
echo json_encode($full_arr);
