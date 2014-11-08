<?php

// 1.07.05
if ( class_exists('FrmProEntry') ) {
    global $frmpro_entry;
    global $frmpro_entry_meta;
    global $frmpro_field;
    global $frmpro_form;

    $frmpro_entry       = new FrmProEntry();
    $frmpro_entry_meta  = new FrmProEntryMeta();
    $frmpro_field       = new FrmProField();
    $frmpro_form        = new FrmProForm();

    new FrmProDb();
}

// 2.0
if ( ! isset($frm_vars['pro_is_installed']) ) {
    $frm_vars['pro_is_installed'] = false;
}
