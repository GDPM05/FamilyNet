<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class Family extends MY_Controller {

        function __construct(){
            parent::__construct();
            $this->load->model('Family_model');
            $this->load->model('FamilyUser_model');
            $this->load->model('Friends_model');
            $this->loggedIn();
        }

        public function index() {
            $data = array(
                'title' => TITLE.' | Families',
                'user' => $this->session->userdata['user']
            );

            /**
             * Quem pode criar família?
             * Adultos
             * Quem não tiver família
             * 
             */

            $user_id = $this->session->userdata['user']['id'];

            //print_r($this->session->userdata['user']);
              
            $family_id = $this->FamilyUser_model->fetch(['id_family' => $user_id]);

            print_r($family_id);

            $family = ($family_id) ? $this->Family_model->fetch(['id' => $family_id]) : false;

            if(!$family){
                $friends = $this->Friends_model->fetch_friends($user_id);
                foreach($friends as $friend){
                    // Verifica qual ID é o do amigo
                    $friend_id = $friend['id_user1'] == $user_id ? $friend['id_user2'] : $friend['id_user1'];
                    $user = $this->User_model->fetch(['id' => $friend_id]);
                    $data['friends'][] = $user;
                }
                //print_r($data);
            }            

            $this->load->view('common/header', $data);
            $this->load->view('common/menu', $this->data);
            $this->load->view(($family) ? 'family_menu' : 'new_family', $data);
            $this->load->view('common/footer');
        }

        public function new_family(){
            $data = $this->input->post();

            $family_members = [];
            foreach($data as $key => $value)
                if($value == "on") $family_members[] = $key;

            $insert_data = [
                'family_name' => $data['family_name'],
                'n_members' => count($family_members),
                'n_points'=> 0,
                'id_creator'=> $this->user_id
            ];

            $family_id = $this->Family_model->insert($insert_data);

            if($this->Family_model->error){
                $data['error'] = 'Houve um erro durante a criação da família. Tente novamente mais tarde.';
            }else{
                foreach($family_members as $key => $value){
                    $user_id = $this->User_model->fetch(['user' => $value])['id'];
    
                    $insert_data = [
                        'id_family' => $family_id,
                        'id_user' => $user_id
                    ];
    
                    $this->FamilyUser_model->insert($insert_data);
                }    

                if($this->FamilyUser_model->error)
                    $data['error'] = 'Houve um erro durante a criação da família. Tente novamente mais tarde.';      
                else
                    redirect(base_url('family_menu'));
            }

            $this->load->view('common/header', $data);
            $this->load->view('common/menu', $this->data);
            $this->load->view(($family) ? 'family_menu' : 'new_family', $data);
            $this->load->view('common/footer');
        }


    }
?>