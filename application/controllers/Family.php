<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class Family extends MY_Controller {

        function __construct(){
            parent::__construct();
            $this->load->model('Family_model');
            $this->load->model('FamilyUser_model');
            $this->load->model('Friends_model');
            $this->load->model('Conversation_model');
            $this->load->model('Gender_model');
            $this->load->model('ChildAccount_model');
            $this->load->model('Activity_model');
            $this->loggedIn();
        }

        public function index() {
            $this->data['title'] = TITLE.' | Família';
            $this->data['user'] = $this->session->userdata['user'];
            $this->data['genders'] = $this->Gender_model->fetch_all();
            
            $user_id = $this->session->userdata['user']['id'];

            //print_r($this->session->userdata['user']);
              
            $family_id = $this->FamilyUser_model->fetch(['id_user' => $user_id]);

            $family = ($family_id) ? $this->Family_model->fetch(['id' => $family_id['id_family']]) : false;

            if(!$family){
                $friends = $this->Friends_model->fetch_friends($user_id);
                foreach($friends as $friend){
                    // Verifica qual ID é o do amigo
                    $friend_id = $friend['id_user1'] == $user_id ? $friend['id_user2'] : $friend['id_user1'];
                    $user = $this->User_model->fetch(['id' => $friend_id]);
                    $this->data['friends'][] = $user;
                }
                //print_r($data);
            }else{
                $family_members_ids = $this->FamilyUser_model->fetch_all(null, null, null, null, ['id_family' => $family_id['id_family']]);
                $family_members = [];
                foreach($family_members_ids as $id){
                    //print_r($id);
                    $family_members[] = $this->User_model->fetch(['id' => $id['id_user']], 'id, user, username, pfp, gender, p_role');
                }
                $this->data['family_creator'] = $family['id_creator'];
                $this->data['family_name'] = $family['family_name'];
                $this->data['family_members'] = $family_members;
            }

            

            $this->load->view('common/header', $this->data);
            $this->load->view('common/menu', $this->data);
            $this->load->view((!empty($family)) ? 'family_menu' : 'new_family', $this->data);
            $this->load->view('common/footer');
        }

        public function new_family(){
            $data = $this->input->post();

            $family_members = [];
            foreach($data as $key => $value)
                if($value == "on") $family_members[] = $key;

            $family_conversation = $this->Conversation_model->insert(['title' => md5($data['family_name'])]);

            $insert_data = [
                'family_name' => $data['family_name'],
                'n_members' => count($family_members),
                'n_points'=> 0,
                'id_creator'=> $this->user_id,
                'id_conversation' => $family_conversation
            ];
            
            $family_id = $this->Family_model->insert($insert_data);

            $this->FamilyUser_model->insert(['id_user' => $this->session->userdata('user')['id'], 'id_family' => $family_id]);

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

        public function create_child_account(){
            // Fetch genders
            $return_data['genders'] = $this->Gender_model->fetch_all();

            // Get input data
            $data = $this->input->post();
            print_r($data);

            // Validate input
            if(empty($data['name']) || empty($data['birthday']) || empty($data['gender'])){
                $return_data['error'] = true;
                $return_data['error_msg'] = 'All fields must be provided.';
            } else {
                // Validate date of birth
                $data_nascimento = new DateTime($data['birthday']);
                $data_atual = new DateTime(date('Y'));

                if($data_nascimento->format('Y') > $data_atual->format('Y') || $data_nascimento->format('Y') < ($data_atual->format('Y') - 100)){
                    $return_data['error'] = true;
                    $return_data['error_msg'] = 'Insert a valid date.';
                } else {
                    // Insert user data

                    $media_id = $this->Media_model->insert([
                        'path' => base_url('/media/profile_pictures/default/child.png'),
                        'alt' => 'child'
                    ]);

                    $insert_data = [
                        'username' => $data['name'],
                        'user' => strtolower(str_replace(' ', '', $data['name'])),
                        'birthday' => $data['birthday'],
                        'gender' => $data['gender'],
                        'pfp' => $media_id
                    ];
                    $user = $this->User_model->insert($insert_data);
                    print_r($insert_data);

                    // Insert child account
                    if(empty($user)){
                        $return_data['error'] = true;
                        $return_data['error_msg'] = 'There was an error creating the user. Please try again later.';
                    } else {
                        $child = $this->ChildAccount_model->insert(['id_user' => $user, 'access' => 'none', 'access_age' => 0, 'parent' => $this->session->userdata('user')['id']]);
                        var_dump($user);

                        $check_child = $this->ChildAccount_model->fetch(['id_user' => $user]);
                        print_r($check_child);
                        if(empty($check_child)){
                            $return_data['error'] = true;
                            $return_data['error_msg'] = 'There was an error creating the child account. Please try again later. ';
                        } else {
                            $family = $this->FamilyUser_model->fetch(['id_user' => $this->session->userdata('user')['id']]);
                            var_dump($family);
                            if(empty($family)){
                                $return_data['error'] = true;
                                $return_data['error_msg'] = 'There was an error creating the child account. Please try again later.';
                            }else{
                                $this->FamilyUser_model->insert(['id_family' => $family['id_family'], 'id_user' => $user]);

                                $check_family_member = $this->FamilyUser_model->fetch(['id_user' => $user]);

                                if(empty($check_family_member)){
                                    $return_data['error'] = true;
                                    $return_data['error_msg'] = 'There was an error creating the child account. Please try again later.';
                                }else{
                                    //Redirect on success
                                    redirect(base_url('family_menu'));
                                    return;
                                }
                            }
                        }
                    }
                }
            }

            // Load views
            $this->load->view('common/header', $this->data);
            $this->load->view('common/menu', $this->data);
            $this->load->view('family_menu', $return_data);
            $this->load->view('common/footer');
        }

        public function get_family(){
            $user_id = $this->session->userdata('user')['id'];

            $family_id = $this->FamilyUser_model->fetch(['id_user' => $user_id]);

            $family_members_ids = $this->FamilyUser_model->fetch_all(null, null, null, null, ['id_family' => $family_id['id_family']]);

            $family_members = [];

            foreach($family_members_ids as $id){
                $user = $this->User_model->fetch(['id' => $id['id_user']], 'id, user, username, pfp');
                $user['pfp'] = $this->Media_model->fetch(['id' => $user['pfp']]);
                $family_members[] = $user; 
            }

            header('Content-Type: application/json');
            
            echo json_encode($family_members);
        }

        public function get_activities($page = 0){
            header('Content-Type: application/json');

            $return_data = [
                'success' => false,
                'message' => ''
            ];

            $perPage = 5;

            $offset = ($page - 1) * $perPage;

            $activities = $this->Activity_model->fetch_all(null, $offset, $page, null, null);

            if(empty($activities)){
                $return_data['message'] = 'No activities found.';
                echo json_encode($return_data);
                return false;
            }

            
            $return_data['success'] = true;
            $return_data['message'] = '';
            $return_data['data'] = $activities;
            echo json_encode($return_data);
            return true;
        }

    }
?>