<?php if (isset($message) && $message != ''){?><div id="message" class="updated fade" style="padding:5px;"><?php echo $message; ?></div><?php } ?>

<?php if( isset($errors) && $errors && count($errors) > 0 ){ ?>
        <div class="error">
            <ul id="frm_errors">
                <?php foreach( $errors as $error )
                    echo '<li>' . $error . '</li>';
                ?>
            </ul>
        </div>
<?php } ?>