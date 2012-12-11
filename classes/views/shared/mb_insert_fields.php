<ul class="subsubsub" style="float:right;margin:0;">
    <li><a class="current frmids" onclick="frmToggleKeyID('frmids');"><?php _e('IDs', 'formidable') ?></a> |</li>
    <li><a class="frmkeys" onclick="frmToggleKeyID('frmkeys');"><?php _e('Keys', 'formidable') ?></a></li>
</ul>
<ul class="alignleft" style="margin:5px 0 0;"><li><?php _e('Fields from your form', 'formidable') ?>:</li></ul>
<ul class="frm_code_list frm_full_width" style="clear:both;max-height:150px;overflow:auto;">
    <?php if(!empty($fields)){
        global $frmdb;
        $linked_forms[] = array();
        
        foreach($fields as $f){ 
            $f->field_options = maybe_unserialize($f->field_options);
            if($f->type == 'data' and (!isset($f->field_options['data_type']) or $f->field_options['data_type'] == 'data' or $f->field_options['data_type'] == ''))
                continue;
        
        FrmAppHelper::insert_opt_html(array('id' => $f->id, 'key' => $f->field_key, 'name' => $f->name, 'type' => $f->type));

	    if($f->type == 'data'){ //get all fields from linked form
            if (isset($f->field_options['form_select']) && is_numeric($f->field_options['form_select'])){
                $linked_form = $frmdb->get_var($frmdb->fields, array('id' => $f->field_options['form_select']), 'form_id');
                if(!in_array($linked_form, $linked_forms)){
                    $linked_forms[] = $linked_form;
                    $linked_fields = $frm_field->getAll("fi.type not in ('divider','captcha','break','html') and fi.form_id =". (int)$linked_form);
                    if($linked_fields){ 
                        foreach ($linked_fields as $linked_field){ 
                            FrmAppHelper::insert_opt_html(array('id' => $f->id ." show=". $linked_field->id, 'key' => $f->field_key ." show=". $linked_field->field_key, 'name' => $linked_field->name, 'type' => $linked_field->type));

                            $ldfe = $linked_field->id;
                            unset($linked_field);
                        } 
                    }
                } 
            }
            $dfe = $f->id;
	    }       
        unset($f);
        }
    } ?>
</ul>

<?php _e('Helpers', 'formidable') ?>:
<ul class="frm_code_list">
<?php
$col = 'one';
$entry_shortcodes = array('id' => __('Entry ID', 'formidable'), 
    'key' => __('Entry Key', 'formidable'),
    'post_id' => __('Post ID', 'formidable'),
    'ip' => __('User IP', 'formidable'),
    'created-at' => __('Entry created at', 'formidable'),
    'updated-at' => __('Entry updated at', 'formidable'),
    '' => '',
    'siteurl' => __('Site URL', 'formidable'),
    'sitename' => __('Site Name', 'formidable'),
);

if(isset($settings_tab) and $settings_tab){
    $entry_shortcodes['125 show=&#34;field_label&#34;'] = __('Field Label', 'formidable');
}else{
    $entry_shortcodes['detaillink'] = __('Single Entry Link', 'formidable');
    $entry_shortcodes['editlink location=&#34;front&#34; label=&#34;Edit&#34; page_id=4'] = __('Edit Entry Link', 'formidable');
}
$entry_shortcodes['evenodd'] = __('Rotate even/odd', 'formidable');

foreach($entry_shortcodes as $skey => $sname){
     if(empty($skey)){
         $col = 'one';
         echo '<li class="clear" style="display:block;height:10px;"></li>';
         continue;
    }
?>
<li class="frm_col_<?php echo $col ?>">
    <a class="frmbutton button <?php echo ($skey == 'siteurl' or $skey == 'sitename') ? 'show_before_content show_after_content' : ''; ?>" onclick="frmInsertFieldCode(jQuery(this),'<?php echo $skey ?>');return false;" href="#"><?php echo $sname ?></a>
</li>
<?php
    $col = ($col == 'one') ? 'two' : 'one';
    unset($skey);
    unset($sname);
}
?>
</ul>

<script type="text/javascript">jQuery(document).ready(function($){ $('.frm_code_list .frmkeys').hide(); });</script>