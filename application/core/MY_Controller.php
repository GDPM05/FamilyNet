<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class MY_Controller extends CI_Controller{
        public $data = array();
        public $user_id;
        function __construct(){
            parent::__construct();
            $this->load->model('Media_model');
            $this->load->model('User_model');
            
            $this->user_id = $this->session->userdata('user')['id'];

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
            $logged_in = $this->session->userdata('logged_in');
    
            return $logged_in;
        }
    }

?>