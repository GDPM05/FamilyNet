<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class Notification extends MY_Controller {

        function __construct(){
            parent::__construct();
            $this->load->library(array('pagination'));
            $this->load->model('Notification_model');
            if(!$this->LoggedIn()){
                redirect(base_url('logout'));
                exit;
            }
        }

        public function index() {
            $data = array(
                'title' => TITLE.' | Notificações',
                'notifications' => array(),
                'user' => $this->session->userdata('user')
            );

            $user = $this->session->userdata('user');

            $page = $this->uri->segment(2) OR 0; // Fazer operador ternerarui
            $per_page = 10;
            
            $config = array();
            $config['base_url'] = base_url("notification/");
            $config['total_rows'] = $this->Notification_model->get_count(array('receiver_id' => $user['id']));
            $config['per_page'] = $per_page;
            $config['use_page_numbers'] = TRUE;
            $config['cur_page'] = $page;
            $this->pagination->initialize($config);
            
            $data['links'] = $this->pagination->create_links();
            $data['page'] = $this->pagination;

            $data['notifications'] = $this->Notification_model->fetch_all(true, $per_page, $page, null, ['receiver_id' => $user['id']]);
            
            foreach($data['notifications'] as $key => $notification){
                $data['notifications'][$key]['sender'] = $this->User_model->fetch(['id' => $notification['sender_id']]);
            }

            $this->load->view('common/header', $data);
            $this->load->view('common/menu', $this->data);
            $this->load->view('notifications', $data);
            $this->load->view('common/footer');
        }

    }
?>