<?php

class FrmFormsHelper{
    function get_direct_link($key, $prli_link_id=false){
        global $frm_siteurl;
        $target_url = $frm_siteurl . '/index.php?plugin='. FRM_PLUGIN_NAME. '&controller=forms&action=preview&form='.$key;
        if ($prli_link_id && class_exists('PrliLink')){
            $prli = prli_get_pretty_link_url($prli_link_id);
            if ($prli) $target_url = $prli;
        }
        return $target_url;
    }
    
    function get_template_dropdown($templates){ ?>
        <select id="select_form" name="select_form" onChange='createFromFrmTemplate(this.value)'>
            <option value="">Create Form from Template: </option>
            <?php foreach ($templates as $temp){ ?>
                <option value="<?php echo $temp->id ?>"><?php echo $temp->name ?></option>
            <?php }?>
        </select> 
        <script type="text/javascript">
            function createFromFrmTemplate(form){window.location='<?php $_SERVER["REQUEST_URI"] ?>?page=<?php echo FRM_PLUGIN_NAME; ?>&action=duplicate&id='+form}
        </script>
    <?php    
    }
    
    function forms_dropdown( $field_name, $field_value, $blank=true ){
        global $frm_app_controller, $frm_form;

        $forms = $frm_form->getAll("is_template=0 AND (status is NULL OR status = '' OR status = 'published')",' ORDER BY name');
        ?>
        <select name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" class="frm-dropdown">
            <?php if ($blank){ ?>
            <option value=""></option>
            <?php } ?>
            <?php foreach($forms as $form){ ?>
                <option value="<?php echo $form->id; ?>" <?php selected($field_value, $form->id); ?>><?php echo $form->name; ?></option>
            <?php } ?>
        </select>
        <?php
    }
    
    function setup_new_vars(){
        global $frm_app_controller, $frm_form;
        $values = array();
        foreach (array('name' => 'Untitled Form', 'description' => '') as $var => $default)
            $values[$var] = stripslashes($frm_app_controller->get_param($var, $default));
        
        foreach (array('form_id' => '', 'logged_in' => '', 'editable' => '', 'default_template' => 0, 'is_template' => 0) as $var => $default)
            $values[$var] = stripslashes($frm_app_controller->get_param($var, $default));
            
        $values['form_key'] = ($_POST and isset($_POST['form_key']))?$_POST['form_key']:(FrmAppHelper::get_unique_key('', $frm_form->table_name, 'form_key'));
        $values['email_to'] = ($_POST and isset($_POST['options']['email_to'])) ? $_POST['options']['email_to'] : '';
        $values['submit_value'] = ($_POST and isset($_POST['options']['submit_value'])) ? $_POST['options']['submit_value'] : 'Submit';
        $values['success_msg'] = ($_POST and isset($_POST['options']['success_msg'])) ? $_POST['options']['success_msg'] : 'Your responses were successfully submitted. Thank you!';
        $values['akismet'] = ($_POST and isset($_POST['options']['akismet'])) ? 1 : 0;
        
        return apply_filters('frm_setup_new_form_vars', $values);
    }
    
    function setup_edit_vars($values, $record){
        global $frm_form, $frm_app_controller;

        $values['form_key'] = $frm_app_controller->get_param('form_key', $record->form_key);
        $values['default_template'] = $frm_app_controller->get_param('default_template', $record->default_template);
        $values['is_template'] = $frm_app_controller->get_param('is_template', $record->is_template);
        $options = stripslashes_deep(unserialize($record->options));

        if (is_array($options)){
            foreach ($options as $opt => $value)
                $values[$opt] = $frm_app_controller->get_param($opt, $value);
        }

        return apply_filters('frm_setup_edit_form_vars', $values);
    }

}
?>