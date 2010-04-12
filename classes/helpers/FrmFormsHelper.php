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
            <option value=""><?php _e('Create Form from Template', FRM_PLUGIN_NAME) ?>: </option>
            <?php foreach ($templates as $temp){ ?>
                <option value="<?php echo $temp->id ?>"><?php echo $temp->name ?></option>
            <?php }?>
        </select> 
        <script type="text/javascript">
            function createFromFrmTemplate(form){window.location='<?php $_SERVER["REQUEST_URI"] ?>?page=<?php echo FRM_PLUGIN_NAME; ?>&action=duplicate&id='+form}
        </script>
    <?php    
    }
    
    function forms_dropdown( $field_name, $field_value='', $blank=true, $field_id=false, $onchange=false ){
        global $frm_app_controller, $frm_form;
        if (!$field_id)
            $field_id = $field_name;
            
        $forms = $frm_form->getAll("is_template=0 AND (status is NULL OR status = '' OR status = 'published')",' ORDER BY name');
        ?>
        <select name="<?php echo $field_name; ?>" id="<?php echo $field_id ?>" class="frm-dropdown" <?php if ($onchange) echo 'onchange="'.$onchange.'"'; ?>>
            <?php if ($blank){ ?>
            <option value=""></option>
            <?php } ?>
            <?php foreach($forms as $form){ ?>
                <option value="<?php echo $form->id; ?>" <?php selected($field_value, $form->id); ?>><?php echo stripslashes($form->name); ?></option>
            <?php } ?>
        </select>
        <?php
    }
    
    function setup_new_vars(){
        global $frm_app_controller, $frm_form, $frm_settings;
        $values = array();
        foreach (array('name' => __('Untitled Form', FRM_PLUGIN_NAME), 'description' => '') as $var => $default)
            $values[$var] = stripslashes($frm_app_controller->get_param($var, $default));
        
        $values['description'] = wpautop($values['description']);
        
        foreach (array('form_id' => '', 'logged_in' => '', 'editable' => '', 'default_template' => 0, 'is_template' => 0) as $var => $default)
            $values[$var] = stripslashes($frm_app_controller->get_param($var, $default));
            
        $values['form_key'] = ($_POST and isset($_POST['form_key']))?$_POST['form_key']:(FrmAppHelper::get_unique_key('', $frm_form->table_name, 'form_key'));
        $values['email_to'] = ($_POST and isset($_POST['options']['email_to'])) ? $_POST['options']['email_to'] : $frm_settings->email_to;
        $values['custom_style'] = ($_POST and isset($_POST['options']['custom_style'])) ? $_POST['options']['custom_style'] : $frm_settings->custom_style;
        $values['submit_value'] = ($_POST and isset($_POST['options']['submit_value'])) ? $_POST['options']['submit_value'] : $frm_settings->submit_value;
        $values['success_msg'] = ($_POST and isset($_POST['options']['success_msg'])) ? $_POST['options']['success_msg'] : $frm_settings->success_msg;
        $values['show_form'] = ($_POST and isset($_POST['options']['show_form'])) ? 1 : 0;
        $values['akismet'] = ($_POST and isset($_POST['options']['akismet'])) ? 1 : 0;
        $values['before_html'] = FrmFormsHelper::get_default_html('before');
        $values['after_html'] = FrmFormsHelper::get_default_html('after');
        
        return apply_filters('frm_setup_new_form_vars', $values);
    }
    
    function setup_edit_vars($values, $record){
        global $frm_form, $frm_app_controller;

        $values['form_key'] = $frm_app_controller->get_param('form_key', $record->form_key);
        $values['default_template'] = $frm_app_controller->get_param('default_template', $record->default_template);
        $values['is_template'] = $frm_app_controller->get_param('is_template', $record->is_template);

        return apply_filters('frm_setup_edit_form_vars', $values);
    }
    
    function get_default_html($loc){
        if ($loc == 'before'){
            $default_html = <<<BEFORE_HTML
[if form_name]<h3>[form_name]</h3>[/if form_name]
[if form_description]<div class="frm_description">[form_description]</div>[/if form_description]
BEFORE_HTML;
        }else{
            $default_html = '';
        }
        return $default_html;
    }
    
    function replace_shortcodes($html, $form, $title=false, $description=false){
        foreach (array('form_name' => $title,'form_description' => $description) as $code => $show){
            if ($code == 'form_name')
                $replace_with = stripslashes($form->name);
            else if ($code == 'form_description')
                $replace_with = wpautop(stripslashes($form->description));
                
            if (($show == true || $show == 'true') && $replace_with != '' ){
                $html = str_replace('[if '.$code.']','',$html); 
        	    $html = str_replace('[/if '.$code.']','',$html);
            }else{
                $html = preg_replace('/(\[if\s+'.$code.'\])(.*?)(\[\/if\s+'.$code.'\])/mis', '', $html);
            }
            $html = str_replace('['.$code.']', $replace_with, $html);   
        }   
        
        return apply_filters('frm_form_replace_shortcodes', $html, $form);
    }
    
    function get_default_email(){
        $email = <<<DEFAULT_EMAIL
            <table style="border-bottom: 1px solid rgb(238, 238, 238); width: 100%; font-size: 12px; line-height: 135%; font-family: Lucida Grande,Tahoma,Arial,sans-serif;" cellspacing="0">
            			<tbody><tr style="background-color: rgb(245, 245, 245);">
            		<th style="border-top: 1px solid rgb(238, 238, 238); padding: 7px 9px; vertical-align: top; color: rgb(34, 34, 34); text-align: left;">
            			Number 
            					</th>
            		<td style="border-top: 1px solid rgb(238, 238, 238); padding: 7px 9px 7px 0pt; vertical-align: top; color: rgb(51, 51, 51); width: 60%;">
            			<div>89</div>

            		</td>
            	</tr>
            	<tr style="background-color: rgb(255, 255, 255);">
            		<th style="border-top: 1px solid rgb(238, 238, 238); padding: 7px 9px; vertical-align: top; color: rgb(34, 34, 34); text-align: left;">
            			Email 
            					</th>
            		<td style="border-top: 1px solid rgb(238, 238, 238); padding: 7px 9px 7px 0pt; vertical-align: top; color: rgb(51, 51, 51); width: 60%;">
            		<a href="mailto:stephywells@gmail.com" target="_blank">stephywells@gmail.com</a>
            		</td>
            	</tr>
            </tbody></table>
DEFAULT_EMAIL;
        return $email;
    }

}
?>