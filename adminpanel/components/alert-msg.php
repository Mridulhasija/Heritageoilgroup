<script>
    //     $(document).ready(function() {
    //         $("#page-msg").fadeOut(2000);
    //     });
</script>

<?php
if (isset($_SESSION["success-msg"]) && $_SESSION["success-msg"] != "") :
?>
    <div class="form-group has-success" id="page-msg">
        <label class="control-label" for="inputSuccess">
            <i class="fa fa-check"></i><?= $_SESSION["success-msg"]; ?>
        </label>
    </div>
<?php
    unset($_SESSION["success-msg"]);
endif;

if (isset($_SESSION["error-msg"]) && $_SESSION["error-msg"] != "") :
?>
    <div class="form-group has-error" id="page-msg">
        <label class="control-label" for="inputError">
            <i class="fa fa-times-circle-o"></i> <?= $_SESSION["error-msg"]; ?>
        </label>
    </div>
<?php
    unset($_SESSION["error-msg"]);
endif;
?>