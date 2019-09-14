<?php
$title = "Manage My Account";
$scripts = array("manage.js");
include('../header.php');
include('../database/config.php');
$account = new Login();
#If a user field does not appear on this page, it probably isn't in the LinkedEditable table, add it!
?>
<body>
<?php include('../navigation.php'); ?>

<div class="container">
    <div class="row manage">
        <div id="report"></div> <!--Content updated if error occurs!-->

        <?php
        if (isset($_SESSION['Username'])) {
            ?>
            <?
            $fields = $account->AccountFields();
            foreach ($fields as $field) {
                if ($field["Viewable"] == true) {#If actual value of field should not be view by user, hide it (for instance password)
                    $valueAttribute = $_SESSION[$field['UserField']];
                } else {
                    $valueAttribute = "Hidden value";
                }
                ?>
                <div class="col-md-4">
                    <label>
                        <?php echo $field['UserField'] ?>:<br>
                        <input type="<?php echo $field['Datatype'] ?>" name="<?php echo $field['UserField'] ?>"
                               value="<?php echo $valueAttribute ?>"
                               onchange="updateValue('<?php echo $field['UserField'] ?>',this.value)"
                               class="form-control"/>
                    </label>
                </div>
                <?
            } ?>
            <button type="button" onclick="LogOut()" class="btn btn-primary">Logout</button>
            <?
        } else {
            ?>
            <div class="alert alert-danger" role="alert">
                You're not logged in! <a href="login.php">Login now</a>
            </div>
            <?
        }
        ?>

    </div>
</div>

</body>