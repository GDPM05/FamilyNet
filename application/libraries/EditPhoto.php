<?php
    defined('BASEPATH') OR exit('No direct script access allowed');


    class EditPhoto {

        private $configs = array();
        private $CI;

        public function __construct() {
            $this->CI =& get_instance();
            $this->CI->load->library('PasswordHash', array(8, false));
        }

        public function prepare_profile_picture($img){
            $this->config['source_image'] = ;
        }
    }
?>