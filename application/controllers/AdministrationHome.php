<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class AdministrationHome extends MY_Controller {
        
        function __construct(){
            $this->load->library('session');
            
            if($this->session->userdata('user')['category'] < 7){
                redirect(base_url('logout'));
                exit();
            }
        }
        
        public function index(){
            
        }

    }