<div class="wrap">
    <div class="frmicon icon32"><br/></div>
    <h2><?php _e('Import/Export', 'formidable'); ?></h2>

    <?php include(FrmAppHelper::plugin_path() .'/classes/views/shared/errors.php'); ?>
    <div id="poststuff" class="metabox-holder">
    <div id="post-body">
    <div id="post-body-content">

    <div class="postbox ">
    <h3 class="hndle"><span><?php _e('Import', 'formidable') ?></span></h3>
    <div class="inside">
        <p><?php echo apply_filters('frm_upload_instructions1', __('Upload your Formidable XML file to import the forms into this site.', 'formidable')) ?></p>
        <p><?php echo apply_filters('frm_upload_instructions2', __('Choose a Formidable XML file to upload, then click "Upload file and import."', 'formidable')) ?></p>
        <br/>
        <form enctype="multipart/form-data" method="post">
            <input type="hidden" name="frm_action" value="import_xml" />
            <p><label><?php _e('Choose a file from your computer', 'formidable') ?></label> (<?php printf(__('Maximum size: %s', 'formidable'), ini_get('upload_max_filesize')) ?>)
            <input type="file" name="frm_import_file" size="25" />
            </p>
            
            <?php do_action('frm_csv_opts', $forms) ?>

            <p class="submit">
                <input type="submit" value="<?php _e('Upload file and import', 'formidable') ?>" class="button-primary" />
            </p>
        </form>
    </div>
    </div>
    
    
    <div class="postbox">
    <h3 class="hndle"><span><?php _e('Export', 'formidable') ?></span></h3>
    <div class="inside">
        <form method="post" action="<?php echo admin_url('admin-ajax.php'); ?>" id="frm_export_xml">
            <input type="hidden" name="action" value="frm_export_xml" />
            <?php //wp_nonce_field('export-xml'); ?>
            <table class="form-table">
                <?php if (count($export_format) == 1) { 
                    reset($export_format); ?>
                <tr><td colspan="2"><input type="hidden" name="format" value="<?php echo key($export_format) ?>" /></td></tr>
                <?php } else { ?>
                <tr class="form-field">
                    <th scope="row"><?php _e('Export Format', 'formidable'); ?>:</th>
                    <td>
                        <select name="format">
                        <?php foreach ( $export_format as $t => $type ){ ?>
                            <option value="<?php echo $t ?>" data-support="<?php echo esc_attr($type['support']) ?>" <?php echo isset($type['count']) ? 'data-count="'. esc_attr($type['count']) .'"' : ''; ?>><?php echo isset($type['name']) ? $type['name'] : $t ?></option>
                        <?php } ?>
                        </select>
                    </td>
                </tr>
                <?php } ?>
                
                <?php if (count($export_types) == 1) { 
                    reset($export_types); ?>
                <tr><td colspan="2"><input type="hidden" name="type[]" value="<?php echo key($export_types) ?>" /></td></tr>
                <?php } else { ?>
                <tr class="form-field">
                    <th scope="row"><?php _e('Data Types to Export', 'formidable'); ?>:</th>
                    <td>
                        <?php _e('Include the following data types in your export file', 'formidable'); ?><br/>
                        <?php foreach ( $export_types as $t => $type ){ ?>
                        <label><input type="checkbox" name="type[]" value="<?php echo $t ?>"/> <?php echo $type ?></label> &nbsp;
                        <?php } ?>
                    </td>
                </tr>
                <?php } ?>

                <tr class="form-field">
                    <th scope="row"><?php _e('Select forms (optional)', 'formidable'); ?>:</th>
                    <td>
                        <?php _e('If you would like to include ONLY specific forms and the entries and views related to those forms, select those forms here', 'formidable'); ?>:<br/>
                        <!-- <div class="postbox" style="padding:0 10px;max-height:300px;overflow:auto;"> -->
                        <select name="frm_export_forms[]" multiple="multiple">
                        <?php foreach($forms as $form){ ?>
                            <option value="<?php echo $form->id ?>"><?php 
                        echo ($form->name == '') ? '(no title)' : $form->name;
                        echo ' &mdash; '. $form->form_key;
                        if ( $form->is_template && $form->default_template ) {
                            echo ' '. __('(default template)', 'formidable');
                        } else if ( $form->is_template ) { 
                            echo ' '. __('(template)', 'formidable');
                        }
                        ?></option>
                        <?php } ?>
                        </select>
                        <!-- </div> -->
                    </td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" value="<?php _e('Export Selection', 'formidable') ?>" class="button-primary" />
            </p>
        </form>

    </div>
    </div>


    </div>
    </div>
    </div>
</div>