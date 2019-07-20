<?php
include_once('/var/www/html/database/config.php'); #Include script, unless there is parent script that has already included
$username = $_POST['username'];
$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];
$database = new CRUD();
$databas
