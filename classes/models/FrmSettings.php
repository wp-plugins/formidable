<?php
class FrmSettings{
    // Page Setup Variables
    var $preview_page_id;
    var $preview_page_id_str;
    var $lock_keys;
    
    var $custom_style;

    function FrmSettings(){
        $this->set_default_options();
    }

    function set_default_options(){
        if(!isset($this->preview_page_id))
          $this->preview_page_id = 0;
          
        $this->preview_page_id_str = 'frm-preview-page-id';
        
        if(!isset($this->lock_keys))
            $this->lock_keys = true;
        
        if(!isset($this->custom_style))
            $this->custom_style = true;
    }

    function validate($params,$errors){   
        //if($params[ $this->preview_page_id_str ] == 0)
        //  $errors[] = "The Preview Page Must Not Be Blank.";
        $errors = apply_filters( 'frm_validate_settings', $errors, $params );
        return $errors;
    }

    function update($params){
        $this->preview_page_id = (int)$params[ $this->preview_page_id_str ];
        $this->lock_keys = isset($params['frm_lock_keys']) ? 1 : 0;
        $this->custom_style = isset($params['frm_custom_style']) ? 1 : 0;
        
        do_action( 'frm_update_settings', $params );
    }

    function store(){
        // Save the posted value in the database
        update_option( 'frm_options', $this);

        do_action( 'frm_store_settings' );
    }
  
}
?>
