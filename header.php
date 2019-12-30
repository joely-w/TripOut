<?php
session_start(); #Start session on all pages before headers are sent
?>
<head>
    <title>Trip Out - <?php echo $title ?></title>
    <link rel="stylesheet" href="/stylesheets/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="/stylesheets/style.css">
    <script src="/account/login.js"></script>
    <? if (isset($scripts)) { #If any unique scripts are required, set in pages $script array.
        foreach ($scripts as $path) {
            ?>
            <script src="<?php echo $path ?>"></script>
            <?
        }
    }
    if (isset($styles)) { #If any unique scripts are required, set in pages $script array.
        foreach ($styles as $path) {
            ?>
            <link rel="stylesheet" href="<?php echo $path ?>"/>
            <?
        }
    }
    ?>
</head>