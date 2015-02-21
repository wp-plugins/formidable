<div class="nav-menus-php">
<div class="wrap">
    <?php FrmStylesHelper::style_menu(); ?>

	<?php include(FrmAppHelper::plugin_path() .'/classes/views/shared/errors.php'); ?>

	<div class="manage-menus">
 		<span class="add-edit-menu-action">
 		    <?php if ( count($styles) < 2 && !empty($style->ID) ) {
 		        printf(__('Edit your style below, or %1$screate a new style%2$s.', 'formidable'), '<a href="?page=formidable-styles&frm_action=new_style">', '</a>');

 		    } else { ?>
 		        <form method="get">
 		            <input type="hidden" name="page" value="<?php echo $_GET['page'] ?>"/>
 		            <input type="hidden" name="frm_action" value="edit" />
     		        <label class="selected-menu"><?php _e('Select a style to edit:', 'formidable'); ?></label>
     		        <select name="id">
     		            <option value=""><?php _e('&mdash; Select &mdash;') ?></option>
     		            <?php foreach ( $styles as $s ) { ?>
     		            <option value="<?php echo $s->ID ?>" <?php selected($s->ID, $style->ID) ?>><?php echo $s->post_title . (empty($s->menu_order) ? '' : ' ('. __('default', 'formidable') .')') ?></option>
     		            <?php } ?>
     		        </select>
     		        <span class="submit-btn">
     		            <input type="submit" class="button-secondary" value="<?php _e('Select', 'formidable') ?>"/>
     		        </span>
 		        </form>
 		        <span class="add-new-menu-action"><?php printf(__('or %1$screate a new style%2$s.', 'formidable'), '<a href="?page=formidable-styles&frm_action=new_style">', '</a>'); ?></span>
<?php
 		    } ?>

		</span>
    </div><!-- /manage-menus -->

	<form id="frm_styling_form" action="" name="frm_styling_form" method="post">
	    <input type="hidden" name="ID" value="<?php echo $style->ID ?>" />
		<input type="hidden" name="frm_action" value="save" />
        <textarea name="<?php echo $frm_style->get_field_name('custom_css') ?>" class="frm_hidden"><?php echo FrmAppHelper::esc_textarea($style->post_content['custom_css']) ?></textarea>
		<?php wp_nonce_field( 'frm_style_nonce', 'frm_style' ); ?>

	<div id="nav-menus-frame">
	<div id="menu-settings-column" class="metabox-holder">
		<div class="clear"></div>

		<div class="styling_settings">
		    <input type="hidden" name="style_name" value="frm_style_<?php echo $style->post_name ?>" />
			<?php FrmStylesController::do_accordion_sections( FrmStylesController::$screen, 'side', compact('style', 'frm_style') ); ?>
		</div>

	</div><!-- /#menu-settings-column -->


	<div id="menu-management-liquid">
		<div id="menu-management">
				<div class="menu-edit blank-slate">
					<div id="nav-menu-header">
						<div class="major-publishing-actions">
							<label class="menu-name-label howto open-label" for="menu-name">
								<span><?php _e('Style Name', 'formidable') ?></span>
								<input id="menu-name" name="<?php echo $frm_style->get_field_name('post_title', ''); ?>" type="text" class="menu-name regular-text menu-item-textbox" title="<?php esc_attr_e('Enter style name here', 'formidable') ?>" value="<?php echo esc_attr($style->post_title) ?>" />
							</label>

							<input name="prev_menu_order" type="hidden" value="<?php echo esc_attr($style->menu_order) ?>" />
							<label class="menu-name-label howto open-label default-style-box" for="menu_order">
							<span>
							<?php if ( $style->menu_order ) { ?>
							    <input name="<?php echo $frm_style->get_field_name('menu_order', ''); ?>" type="hidden" value="1" />
							    <input id="menu_order" disabled="disabled" type="checkbox" value="1" <?php checked($style->menu_order, 1) ?> />
							<?php } else { ?>
								<input id="menu_order" name="<?php echo $frm_style->get_field_name('menu_order', ''); ?>" type="checkbox" value="1" <?php checked($style->menu_order, 1) ?> />
							<?php } ?>
							    <?php _e('Make default style', 'formidable') ?></span>
							</label>

							<div class="publishing-action">
								<input type="submit" id="save_menu_header" class="button button-primary menu-save" value="<?php esc_attr_e('Save Style', 'formidable'); ?>"  />
							</div><!-- END .publishing-action -->
						</div><!-- END .major-publishing-actions -->
					</div><!-- END .nav-menu-header -->
					<div id="post-body">
						<div id="post-body-content">

							<?php include( dirname(__FILE__) .'/_sample_form.php') ?>

						</div><!-- /#post-body-content -->
					</div><!-- /#post-body -->
					<div id="nav-menu-footer" class="submitbox">
						<div class="major-publishing-actions">
						    <?php if ( !empty($style->ID) && empty($style->menu_order) ) { ?>
						    <a href="<?php echo admin_url('admin.php?page=formidable-styles&frm_action=destroy&id='. $style->ID); ?>" class="submitdelete deletion" onclick="return confirm('<?php _e('Are you sure you want to delete that style?', 'formidable') ?>')" style="padding-right:10px;"><?php _e('Delete Style', 'formidable') ?></a>
						    <?php } ?>
						    <?php
						    if ( $style->ID ) {
							    echo '<span class="howto"><span>.frm_style_'. $style->post_name .'</span></span>';
							} ?>
                            <div class="publishing-action">
                                <input type="button" value="<?php esc_attr_e('Reset to Default', 'formidable') ?>" class="button-secondary frm_reset_style" />
								<input type="submit" id="save_menu_header" class="button button-primary menu-save" value="<?php esc_attr_e('Save Style', 'formidable'); ?>"  />
							</div><!-- END .publishing-action -->
						</div><!-- END .major-publishing-actions -->
					</div><!-- /#nav-menu-footer -->
				</div><!-- /.menu-edit -->
		</div><!-- /#menu-management -->
	</div><!-- /#menu-management-liquid -->
	</div><!-- /#nav-menus-frame -->
	</form>
</div><!-- /.wrap-->
</div><!-- /.nav-menu-php -->

<script type="text/javascript">jQuery('.control-section.accordion-section.open').removeClass('open');</script>
<div id="this_css"></div>