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
    <div class="row">
        <?php
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
                           onchange="update(<?php echo $field['UserField'] ?>,this.value)"/>
                </label>
            </div>
            <?
        }
        ?>
    </div>
</div>

</body>