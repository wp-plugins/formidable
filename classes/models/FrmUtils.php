<?php

class FrmUtils{

    function prepend_and_or_where( $starts_with = ' WHERE', $where = '' ){
      return (( $where == '' )?'':$starts_with . $where);
    }
    
    // For Pagination
    function getLastRecordNum($r_count,$current_p,$p_size){
      return (($r_count < ($current_p * $p_size))?$r_count:($current_p * $p_size));
    }

    // For Pagination
    function getFirstRecordNum($r_count,$current_p,$p_size){
      if($current_p == 1)
        return 1;
      else
        return ($this->getLastRecordNum($r_count,($current_p - 1),$p_size) + 1);
    }
    
    
    // Determines whether or not Formidable Pro is installed and activated
    function pro_is_installed(){
      $activated = get_option('frmpro_activated');

      if(!$activated){
        $username = get_option( 'frmpro_username' );
        $password = get_option( 'frmpro_password' );

        if($username and $password){
          $user_type = $this->get_pro_user_type($username, $password);

          if(!empty($user_type)){
            // Tells us that Pro has been activated
            update_option('frmpro_activated', 1);

            $activated = true;
          }
        }
      }

      return ( $activated and $this->pro_files_installed() );
    }

    function pro_is_available(){return true;}
    
    function pro_files_installed(){
        return file_exists(FRM_PATH . "/pro/formidable-pro.php");
    }

    function get_pro_version(){
        global $frmpro_is_installed;
        if($frmpro_is_installed){
            require_once(FRM_PATH . "/pro/frmpro-config.php");
            global $frmpro_version;

            return $frmpro_version;
        }else
            return 0;
    }

}
?>