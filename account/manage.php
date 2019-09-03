<?php
$title = "Manage My Account";
include('../header.php');
include('../database/config.php');
$account = new Login();
?>
<body>
<?php include('../navigation.php'); ?>

<div class="container">
    <div class="row">
        <?php
        $fields = $account->AccountFields();
        print_r($fields);
        ?>
    </div>
</div>

</body>