<script>
    function frm_insert_form(){
        var form_id=jQuery("#frm_add_form_id").val();
        if(form_id==""){alert("<?php _e("Please select a form", "formidable") ?>");return;}
        var title_qs=jQuery("#frm_display_title").is(":checked") ? " title=true" : "";
        var description_qs=jQuery("#frm_display_description").is(":checked") ? " description=true" : "";
        var win = window.dialogArguments || opener || parent || top;
        win.send_to_editor("[formidable id="+form_id+title_qs+description_qs+"]");
    }
    
    function frm_insert_display(){
        var display_id = jQuery("#frm_add_display_id").val();
        if(display_id==""){alert("<?php _e("Please select a custom display", "formidable") ?>");return;}
        var win = window.dialogArguments || opener || parent || top;
        win.send_to_editor("[display-frm-data id="+display_id+"]");
    }
</script>

<div id="frm_insert_form" style="display:none;">
    <div class="wrap">
    <h2><?php _e("Insert A Form", "formidable"); ?></h2>
    <p class="howto"><?php _e("Select a form below to add it to your post or page.", "formidable"); ?></p>
    
    <p><?php FrmFormsHelper::forms_dropdown( 'frm_add_form_id' )?></p>

    <p><input type="checkbox" id="frm_display_title" /> <label for="frm_display_title"><?php _e("Display form title", "formidable"); ?></label> &nbsp; &nbsp;
        <input type="checkbox" id="frm_display_description" /> <label for="frm_display_description"><?php _e("Display form description", "formidable"); ?></label>
    </p>
    
    <p><input type="button" class="button-primary" value="Insert Form" onclick="frm_insert_form();" style="margin-right:15px;" />
    <a class="button" href="#" onclick="tb_remove();return false;"><?php _e("Cancel", "formidable"); ?></a>
    </p>
        
<?php if(isset($displays) and !empty($displays)){ ?>
    <br/><br/>  
    <h2><?php _e("Insert Custom Display", "formidable"); ?></h2>
    <p class="howto"><?php _e("Select a custom display below to add it to your post or page.", "formidable"); ?></p>
    
    <p>
        <select name="frm_add_display_id" id="frm_add_display_id" class="frm-dropdown">
            <option value=""></option>
            <?php foreach ($displays as $display){ ?>
                <option value="<?php echo $display->id ?>"><?php echo $display->name ?></option>
            <?php } ?>
        </select>
    </p>
    
    <p><input type="button" class="button-primary" value="Insert Display" onclick="frm_insert_display();" style="margin-right:15px;" />
    <a class="button" href="#" onclick="tb_remove();return false;"><?php _e("Cancel", "formidable"); ?></a>
    </p>
<?php } ?>
    </div>
</div>