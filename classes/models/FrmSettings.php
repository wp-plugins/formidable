<?php
class FrmSettings{
    // Page Setup Variables
    var $preview_page_id;
    var $preview_page_id_str;

    // Is the setup sufficiently completed?
    var $setup_complete;

    function FrmSettings(){
        $this->set_default_options();
    }

    function set_default_options(){
        if(!isset($this->preview_page_id))
          $this->preview_page_id = 0;
          
        $this->preview_page_id_str = 'frm-preview-page-id';

        if( $this->preview_page_id == 0 )
          $this->setup_complete = 0;
        else
          $this->setup_complete = 1;
    }

    function validate($params,$errors){   
        if($params[ $this->preview_page_id_str ] == 0)
          $errors[] = "The Preview Page Must Not Be Blank.";

        return $errors;
    }

    function update($params){
        $this->preview_page_id = (int)$params[ $this->preview_page_id_str ];
    }

    function store(){
        // Save the posted value in the database
        update_option( 'frm_options', $this);
    }
  
}
?>
