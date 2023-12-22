<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class Directmsg extends MY_Controller {

        function __construct(){
            parent::__construct();
            $this->load->library(array('form_validation', 'session'));
            $this->load->model('Friends_model');
            if(!$this->LoggedIn()){
                redirect(base_url('logout'));
                exit;
            }
        }

        public function index() {
            $data = array(
                'title' => TITLE.' | Direct Messages',
                'user' => $this->session->userdata('user')
            );

            $user_id = $this->session->userdata['user']['id'];

            $friends_ids = $this->Friends_model->fetch_friends($user_id);
            //print_r($friends_ids);
            $users = [];
            foreach($friends_ids as $friends){
                //print_r($friends);
                if($friends['status'] == 1){
                    if($friends['id_user1'] != $user_id){
                        $users[$friends['id_user1']] = $this->User_model->fetch(['id' => $friends['id_user1']], 'id, user, username');
                        $users[$friends['id_user1']]['pfp'] = $this->get_profile_pic($friends['id_user1']);
                    }else{
                        $users[$friends['id_user2']] = $this->User_model->fetch(['id' => $friends['id_user2']], 'id, user, username');
                        $users[$friends['id_user2']]['pfp'] = $this->get_profile_pic($friends['id_user2']);
                    }
                }
            }
            //print_r($users);
            $data['friends'] = $users;

            $this->load->view('common/header', $data);
            $this->load->view('common/menu', $this->data);
            $this->load->view('direct_msg', $data);
            $this->load->view('common/footer');
        }


    }
?>