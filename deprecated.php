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

    // 2.0
    //global $frmpro_display;
    //$frmpro_display = new FrmProDisplay();
}

// 2.0
if ( ! isset($frm_vars['pro_is_installed']) ) {
    $frm_vars['pro_is_installed'] = false;
}

// Instansiate Models
global $frmdb;
global $frm_field;
global $frm_form;
global $frm_entry;
global $frm_entry_meta;

$frmdb              = new FrmDb();
$frm_field          = new FrmField();
$frm_form           = new FrmForm();
$frm_entry          = new FrmEntry();
$frm_entry_meta     = new FrmEntryMeta();
