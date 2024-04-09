<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class MY_Controller extends CI_Controller{
        public $data = array();

        function __construct(){
            parent::__construct();
            $this->load->model('Media_model');
            $this->load->model('User_model');
            
            if($this->LoggedIn()){
                $this->data['path'] = $this->get_profile_pic($this->session->userdata('user')['id'])['path'];
            }
        }

        public function get_profile_pic($user_id){
            $user = $this->User_model->fetch(['id' => $user_id]);
            $media = $this->Media_model->fetch(["id" => $user['pfp']]);

            return $media;
        }

        public function LoggedIn(){
            
            if(isset($_COOKIE['user_login']) && $_COOKIE['user_login']['logged_in']){
                $logged_in = true;
                $this->session->set_userdata($_COOKIE['user_login']);
            }else if($this->session->userdata('logged_in'))
                $logged_in = (($this->session->userdata('logged_in') !== null && $this->session->userdata('logged_in')) || (isset($_COOKIE['user_login']) && $_COOKIE['user_login']['logged_in']));
            else
                $logged_in = false;        
            
            return $logged_in;
        }
    }

?>