<?php

class FrmStatisticsController{
    function FrmStatisticsController(){
        add_action('admin_menu', array( $this, 'menu' ), 30);
    }
    
    function menu(){
        global $frmpro_is_installed;
        if(!$frmpro_is_installed)
            add_submenu_page(FRM_PLUGIN_TITLE, FRM_PLUGIN_TITLE .' | Statistics', 'Statistics', 8, FRM_PLUGIN_TITLE.'-statistics',array($this,''));
    }
    
    function list_entries(){
        require_once(FRM_VIEWS_PATH . '/frm-statistics/list.php');
    }

}

?>