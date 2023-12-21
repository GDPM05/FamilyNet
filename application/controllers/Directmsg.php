<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class Directmsg extends MY_Controller {

        function __construct(){
            parent::__construct();

            $this->load->library(array('form_validation', 'session'));
            if(!$this->LoggedIn()){
                redirect(base_url('logout'));
                exit;
            }
        }

        public function index() {
            $data = array(
                'title' => TITLE.' | Direct Messages',
            );

            $data['friends'] = $this->main->fetch_friends($this->session->userdata['user']['id']);

            $this->load->view('common/header', $data);
            $this->load->view('common/menu', $this->data);
            $this->load->view('direct_msg', $data);
            $this->load->view('common/footer');
        }


    }
?>