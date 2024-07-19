<?php
//
//  Nagios Core Config Manager
//  Copyright (c) 2010-2019 Nagios Enterprises, LLC
//
//  File: form_footer.php
//  Desc: Creates the bottom of the Form class, which closes anything that was opened.
//
?>

        <div class="bottomButtons">
            <?php if (ccm_has_access_for($this->exactType)) { ?>
            <input class="btn btn-sm btn-primary" name="subForm" type="button" id="subForm1" value="<?php echo _("Save"); ?>">
            <?php } ?>
            <input class="btn btn-sm btn-default" name="subAbort" type="button" id="subAbort1" onclick="abort('<?php echo encode_form_valq($FIELDS['exactType']); ?>','<?php echo encode_form_valq($FIELDS['returnUrl']); ?>')" value="<?php echo _("Cancel"); ?>">

            <input name="cmd" type="hidden" id="cmd" value="submit">
            <input name="mode" type="hidden" id="mode" value="<?php print encode_form_valq($FIELDS['mode']); ?>">
            <input name="hidId" type="hidden" id="hidId" value="<?php print encode_form_valq($FIELDS['hidId']); ?>">
            <input name="hidName" type="hidden" id="hidName" value="<?php print encode_form_valq($FIELDS['hidName']); ?>">
            <input name="hidServiceDescription" type="hidden" id="hidName" value="<?php print encode_form_valq($FIELDS['hidServiceDescription']); ?>">
            <input name="hostAddress" type="hidden" id="hostAddress" value="<?php print encode_form_valq($FIELDS['hostAddress']); ?>">
            <input name="exactType" type="hidden" id="exactType" value="<?php print encode_form_valq($FIELDS['exactType']); ?>">
            <input name="type" type="hidden" id="type" value="<?php print encode_form_valq($FIELDS['exactType']); ?>">
            <input name="genericType" type="hidden" id="genericType" value="<?php print encode_form_valq($FIELDS['genericType']); ?>"> 
            <input name="returnUrl" type="hidden" id="returnUrl" value="<?php print encode_form_valq($FIELDS['returnUrl']); ?>">
            <input name="token" id="token" type="hidden" value="<?php print $_SESSION['token']; ?>">
        </div>
    </form>
    <!-- End form -->

    </div>
    <!-- end formContainer -->

</div>
<!-- End main wrapper -->