<?php $display = apply_filters('frm_display_field_options', array('type' => $field['type'], 'field_data' => $field, 'required' => true, 'description' => true, 'options' => true, 'label_position' => true, 'invalid' => false, 'size' => false, 'clear_on_focus' => false, 'default_blank' => true)); ?>

<li id="frm_field_id_<?php echo $field['id']; ?>" class="edit_form_item frm_field_box ui-state-default frm_hide_options<?php echo $display['options'] ?>">
    <span class="ui-icon ui-icon-arrowthick-2-n-s alignright"></span>
    <a href="javascript:void(0);" class="ui-icon ui-icon-trash alignright" id="frm_delete_field<?php echo $field['id']; ?>" title="Delete Field"></a>
    <?php if ($display['required']){ ?>
    <span id="require_field_<?php echo $field['id']; ?>">
        <a href="javascript:frm_mark_required( <?php echo $field['id']; ?>,  <?php echo $field_required = ($field['required'] == '0')?('0'):('1'); ?>)" class="ui-icon ui-icon-star alignleft frm_required<?php echo $field_required ?>" id="req_field_<?php echo $field['id']; ?>" title="Mark as <?php echo ($field['required'] == '0')?'':'not '; ?>Required"></a>
    </span>
    <?php } ?>
    <div class="frm_ipe_field_label frm_pos_<?php echo $field['label']; ?>" id="field_<?php echo $field['id']; ?>"><?php echo $field['name'] ?></div>
    
<?php if ($display['type'] == 'text'){ ?>
    <input type="text" name="<?php echo $field_name ?>" value="<?php echo $field['default_value']; ?>" size="<?php echo $field['size']; ?>"/> 
<?php }else if ($field['type'] == 'textarea'){ ?>
    <textarea name="<?php echo $field_name ?>" cols="<?php echo $field['size']; ?>" rows="<?php echo $field['max']; ?>"><?php echo $field['default_value']; ?></textarea> 
    
<?php }else if ($field['type'] == 'radio' || $field['type'] == 'checkbox'){
        $field['value'] = maybe_unserialize($field['default_value']);
        require(FRM_VIEWS_PATH.'/frm-fields/radio.php');   ?>
        <div id="frm_add_field_<?php echo $field['id']; ?>">
            <a href="javascipt:void(0)" class="frm_add_field_option" id="field_<?php echo $field['id']; ?>"><span class="ui-icon ui-icon-plusthick alignleft"></span> Add an Option</a>
        </div>

<?php }else if ($field['type'] == 'select'){ ?>
    <select name='<?php echo $field_name ?>' id='<?php echo $field_name ?>'>
        <?php foreach ($field['options'] as $opt){ 
            $selected = ($field['default_value'] == $opt)?(' selected="selected"'):(''); ?>
            <option value="<?php echo $opt ?>"<?php echo $selected ?>><?php echo $opt ?></option>
        <?php } ?>
    </select>
    <?php if ($display['default_blank']) FrmFieldsHelper::show_default_blank_js($field['id'], $field['default_blank']); ?>
    <br/>
    <?php foreach ($field['options'] as $opt_key => $opt)
            require(FRM_VIEWS_PATH.'/frm-fields/single-option.php');
 ?>
    <div id="frm_add_field_<?php echo $field['id']; ?>">
        <a href="javascipt:void(0)" class="frm_add_field_option" id="field_<?php echo $field['id']; ?>"><span class="ui-icon ui-icon-plusthick alignleft"></span> Add an Option</a>
        <?php do_action('frm_add_multiple_opts', $field); ?>
    </div>

<?php }else if ($field['type'] == 'captcha'){
        if ($frm_recaptcha_enabled){
            global $recaptcha_opt, $frm_siteurl; ?>
            <img src="<?php echo FRM_URL ?>/images/<?php echo $recaptcha_opt['re_theme'];?>-captcha.png">
            <span class="howto">Hint: Change colors in the "Registration Options" <a href="<?php echo $frm_siteurl ?>/wp-admin/options-general.php?page=wp-recaptcha/wp-recaptcha.php">reCAPTCHA settings</a></span>
            <input type="hidden" name="<?php echo $field_name ?>" value="1"/>
<?php   }else
            echo 'Please download and activate the WP reCAPTCHA plugin to enable this feature.';
      
    }else
        do_action('frm_display_added_fields',$field);

if ($display['clear_on_focus']){
    FrmFieldsHelper::show_onfocus_js($field['id'], $field['clear_on_focus']);

    if ($display['default_blank'])
        FrmFieldsHelper::show_default_blank_js($field['id'], $field['default_blank']);
}

if ($display['description']){ ?> 
    <div class="frm_ipe_field_desc description" id="field_<?php echo $field['id']; ?>"><?php echo $field['description']; ?></div> 
<?php
}

if ($display['options']){ ?>  
    <div class="postbox">
        <h3 class="trigger">Field Options:</h3> 
        <div class="toggle_container inside">
            <?php if ($display['size']){ ?>
            <p><label><?php echo ($field['type'] == 'textarea' || $field['type'] == 'rte')?'Columns':'Field Size' ?></label>
                <input type="text" name="field_options[size_<?php echo $field['id'] ?>]" value="<?php echo $field['size']; ?>" size="5">
            
                <label class="nofloat"><?php echo ($field['type'] == 'textarea' || $field['type'] == 'rte')?'Rows':'Max length of input' ?></label>
                <input type="text" name="field_options[max_<?php echo $field['id'] ?>]" value="<?php echo $field['max']; ?>" size="5">
            </p>
            <?php } ?>
            <?php if ($display['label_position']){ ?>
            <p><label>Label Position:</label>
                <select name="field_options[label_<?php echo $field['id'] ?>]">
                    <option value="top"<?php echo ($field['label'] == 'top')?(' selected="true"'):(''); ?>>Top</option>
                    <option value="left"<?php echo ($field['label'] == 'left')?(' selected="true"'):(''); ?>>Left</option>
                    <option value="none"<?php echo ($field['label'] == 'none')?(' selected="true"'):(''); ?>>Hidden</option>
                </select>    
            </p>
            <?php } ?>
            <?php if ($display['required']){ ?>
            <p><label>Indicate required field with:</label>
                <input type="text" name="field_options[required_indicator_<?php echo $field['id'] ?>]" value="<?php echo $field['required_indicator']; ?>">
            </p>
            <p><label>Error message if required field is left blank:</label>    
            <input type="text" name="field_options[blank_<?php echo $field['id'] ?>]" value="<?php echo $field['blank']; ?>" size="50">
            </p>
            <?php } ?>
            <?php if ($display['invalid']){ ?>
            <p><label>Error message if entry is an invalid format:</label>    
            <input type="text" name="field_options[invalid_<?php echo $field['id'] ?>]" value="<?php echo $field['invalid']; ?>" size="50">
            </p>
            <?php } ?>
            <?php do_action('frm_field_options_form', $field, $display); ?>
        </div>
    </div>   
<?php } ?>         
</li>
 
<script type="text/javascript">
    jQuery(document).ready(function() {
        jQuery("#frm_delete_field<?php echo $field['id']; ?>").click(function(){  
            if(confirm("<?php _e('Are you sure you want to delete this field and all data associated with it?', FRM_PLUGIN_NAME); ?>")){
            jQuery.ajax({
               type:"POST",
               url:"<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php",
               data:"action=frm_delete_field&field_id=<?php echo $field['id']; ?>",
               success:function(msg){
                   jQuery('#new_fields').append(msg);
                   jQuery("#frm_field_id_<?php echo $field['id']; ?>").hide('highlight',{},500,callback);
               }
            });
            return false;
            }
        });
          
        function callback(){setTimeout(function(){jQuery("#frm_delete_field<?php echo $field['id']; ?>:hidden").removeAttr('style').hide().fadeIn();}, 1000);};
    });
</script>
