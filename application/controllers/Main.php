<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class Main extends MY_Controller {

        function __construct(){
            parent::__construct();
            $this->loggedIn();
        }

        public function index() {
            $data = array(
                'title' => TITLE.'',
            );

            if(!$this->session->userdata('logged_in'))
                redirect(base_url('logout'));

            $this->load->view('common/header', $data);
            $this->load->view('common/menu', $this->data);
            $this->load->view('main', $data);
            $this->load->view('common/footer');
        }

    }
?>