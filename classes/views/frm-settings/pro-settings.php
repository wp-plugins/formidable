<div class="wrap">
<div id="icon-options-general" class="icon32"><br /></div>
<h2><?php echo FRM_PLUGIN_TITLE ?>: Pro Account Information</h2>
<?php $this_uri = preg_replace('#&.*?$#', '', str_replace( '%7E', '~', $_SERVER['REQUEST_URI'])); ?>
<form name="proaccount_form" method="post" action="<?php echo $this_uri; ?>">
<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">
<input type="hidden" name="action" value="pro-settings">
<?php wp_nonce_field('update-options'); ?>

<h3><?php echo FRM_PLUGIN_TITLE ?> Pro Account Information</h3>

<table class="form-table">
  <tr class="form-field">
    <td valign="top" width="15%"><?php _e(FRM_PLUGIN_TITLE." Pro Username*:", $frmpro_username ); ?> </td>
    <td width="85%">
      <input type="text" name="<?php echo $frmpro_username; ?>" value="<?php echo $frmpro_username_val; ?>"/>
      <br/><span class="description">Your <?php echo FRM_PLUGIN_TITLE ?> Pro Username.</span>
    </td>
  </tr>
  <tr class="form-field">
    <td valign="top" width="15%"><?php _e(FRM_PLUGIN_TITLE." Pro Password:", $frmpro_password ); ?> </td>
    <td width="85%">
      <input type="password" name="<?php echo $frmpro_password; ?>" value="<?php echo $frmpro_password_val; ?>"/>
      <br/><span class="description">Your <?php echo FRM_PLUGIN_TITLE ?> Pro Password.</span>
    </td>
  </tr>
</table>

<p class="submit">
<input type="submit" name="Submit" value="<?php _e('Save', FRM_PLUGIN_NAME ) ?>" />
</p>

<?php if($frmpro_is_installed) { ?>
    <div>
        <p><strong>The <?php echo $frm_utils->get_pro_version(); ?> Version of <?php echo FRM_PLUGIN_TITLE ?> Pro is Installed</strong></p>
        <p>
            <a href="<?php echo $this_uri; ?>&action=force-pro-reinstall" title="Re-Install">Re-Install</a> |
            <a href="<?php echo $this_uri; ?>&action=pro-uninstall" onclick="return confirm('Are you sure you want to Un-Install {FRM_PLUGIN_TITLE} Pro? This will delete your pro username & password from your local database, remove all the pro software but will leave all your data intact in case you want to reinstall sometime :) ...');" title="Un-Install" >Un-Install</a>
        </p><br/>
    <!--
        <p><strong>Edit/Update Your Profile:</strong><br/><span class="description">Use your account username and password to log in to your Account and Affiliate Control Panel</span></p>
        <p><a href="http://prettylinkpro.com/amember/member.php">Account</a> | <a href="http://prettylinkpro.com/amember/aff_member.php">Affiliate Control Panel</a></p> -->
    </div>
  
<?php //} else { ?>
  <!--<p><strong>Ready to take your marketing efforts to the next level?</strong><br/>
  <a href="http://prettylinkpro.com">Pretty Link Pro</a> will help you automate, share, test and get more clicks &amp; conversions from your Pretty Links!<br/><br/><a href="http://prettylinkpro.com">Learn More &raquo;</a></p>-->
<?php } ?>

</form>
</div>
