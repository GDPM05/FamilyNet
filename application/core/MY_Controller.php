<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class MY_Controller extends CI_Controller{
        public $data = array();
        public $user_id;
        function __construct(){
            parent::__construct();
            $this->load->model('Media_model');
            $this->load->model('User_model');
            
            $this->user_id = (isset($this->session->userdata('user')['id'])) ? $this->session->userdata('user')['id'] : null;

            if($this->LoggedIn()){
                $this->data['path'] = $this->get_profile_pic($this->user_id);
            }
        }

        public function get_profile_pic($user_id){
            $user = $this->User_model->fetch(['id' => $user_id]);
            $media = $this->Media_model->fetch(["id" => $user['pfp']]);

            return $media['path'];
        }

        public function LoggedIn(){

            $user_login = (isset($_COOKIE['user_login'])) ? unserialize($_COOKIE['user_login']) : null;            //print_r($user_login);

            if($user_login !== null && $user_login['logged_in']){
                $logged_in = true;
                $this->session->set_userdata($user_login);
            }else if($this->session->userdata('logged_in'))
                $logged_in = (($this->session->userdata('logged_in') !== null && $this->session->userdata('logged_in')) || (isset($_COOKIE['user_login']) && $_COOKIE['user_login']['logged_in']));
            else
                $logged_in = false;        
            
            return $logged_in;
        }
    }

?>