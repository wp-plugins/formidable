<?php
class FrmSettings{
    // Page Setup Variables
    var $preview_page_id;
    var $preview_page_id_str;
    var $theme_css;
    var $theme_name;

    function FrmSettings(){
        $this->set_default_options();
    }

    function set_default_options(){
        if(!isset($this->preview_page_id))
          $this->preview_page_id = 0;
          
        $this->preview_page_id_str = 'frm-preview-page-id';
        
        if(!isset($this->theme_css)){
            $this->theme_css = FRM_URL.'/css/ui-lightness/jquery-ui-1.7.2.custom.css';
            $this->theme_name = 'UI lightness';
        }
    }

    function validate($params,$errors){   
        if($params[ $this->preview_page_id_str ] == 0)
          $errors[] = "The Preview Page Must Not Be Blank.";

        return $errors;
    }

    function update($params){
        $this->preview_page_id = (int)$params[ $this->preview_page_id_str ];
        if (isset($params[ 'frm_themepicker_css' ]))
            $this->theme_css = $params[ 'frm_themepicker_css' ];
        
        if (isset($params[ 'frm_themepicker_name' ])) 
            $this->theme_name = $params[ 'frm_themepicker_name' ];
    }

    function store(){
        // Save the posted value in the database
        update_option( 'frm_options', $this);
    }
  
}
?>
