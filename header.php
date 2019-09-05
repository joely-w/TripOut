<?php
session_start(); #Start session on all pages before headers are sent
?>
<head>
    <? if (isset($scripts)) { #If any unique scripts are required, set in pages $script array.
        foreach ($scripts as $path) {
            ?>
            <script src="<?php echo $path ?>"></script>
            <?
        }
    } ?>
    <title>Trip Out - <?php echo $title ?></title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="/stylesheets/style.css">
    <script src="/account/login.js"></script>
</head>