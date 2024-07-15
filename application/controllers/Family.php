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
            $this->load->model('ActivityMedia_model');
            $this->load->model('ActivityLikes_Model');
            $this->load->model('ActivityParticipant_Model');
            $this->loggedIn();
        }

        public function index() {
            $this->data['title'] = TITLE.' | Família';
            $this->data['user'] = $this->session->userdata['user'];
            $this->data['genders'] = $this->Gender_model->fetch_all();
            $this->data['admin'] = $this->FamilyUser_model->fetch(['id_user' => $this->session->userdata('user')['id']])['admin'];
            
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
                    $user = $this->User_model->fetch(['id' => $id['id_user']], 'id, user, username, pfp, gender, p_role');
                    $user['admin'] = $id['admin'];
                    $family_members[] = $user;
                }

                $friends = $this->Friends_model->fetch_friends($user_id);

                $friends_array = [];
                foreach($friends as $friend){
                    $id_friend = ($friend['id_user1'] == $user_id) ? $friend['id_user2'] : $friend['id_user1'];

                    $friend_data = $this->User_model->fetch(['id' => $id_friend], 'id, username');

                    $friends_array[] = $friend_data;
                }

                $this->data['friends'] = $friends_array;
                $this->data['family_creator'] = $family['id_creator'];
                $this->data['family_name'] = $family['family_name'];
                $this->data['family_members'] = $family_members;
            }

            $this->load->view('common/header', $this->data);
            $this->load->view('common/menu', $this->data);
            $this->load->view((!empty($family)) ? 'family_menu' : 'new_family', $this->data);
            $this->load->view('common/footer');
        }

        public function get_family_members(){
            $family_id = $this->FamilyUser_model->fetch(['id_user' => $this->session->userdata('user')['id']]);

            $family = ($family_id) ? $this->Family_model->fetch(['id' => $family_id['id_family']]) : false;

            $family_members_ids = $this->FamilyUser_model->fetch_all(null, null, null, null, ['id_family' => $family_id['id_family']]);
            $family_members = [];
            foreach($family_members_ids as $id){
                //print_r($id);
                $family_members[] = $this->User_model->fetch(['id' => $id['id_user']], 'id, user, username, pfp, gender, p_role');
            }

            return $family_members;
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

        public function create_child_account() {
            // Fetch genders
            $return_data['genders'] = $this->Gender_model->fetch_all();
            $return_data['user'] = $this->session->userdata('user');
            $family_id = $this->FamilyUser_model->fetch(['id_user' => $this->session->userdata('user')['id']]);
            $family = ($family_id) ? $this->Family_model->fetch(['id' => $family_id['id_family']]) : false;
            $return_data['family_creator'] = $family['id_creator'];
            $return_data['family_name'] = $family['family_name'];
            $return_data['family_members'] = $this->get_family_members();
            
            // Get input data
            $data = $this->input->post();
        
            // Validate input
            if (empty($data['name']) || empty($data['birthday']) || empty($data['email']) || empty($data['gender'])) {
                $return_data['error'] = true;
                $return_data['error_msg'] = 'Todos os campos devem ser preenchidos.';
                $this->load_views($return_data);
                return;
            }
        
            // Validate date of birth
            $data_nascimento = new DateTime($data['birthday']);
            $data_atual = new DateTime(date('Y'));
        
            if ($data_nascimento->format('Y') > $data_atual->format('Y') || $data_nascimento->format('Y') < ($data_atual->format('Y') - 100)) {
                $return_data['error'] = true;
                $return_data['error_msg'] = 'Insira uma data válida.';
                $this->load_views($return_data);
                return;
            }
            
            if($this->User_model->check_if_exists(['email' => $data['email']])){
                $return_data['error'] = true;
                $return_data['error_msg'] = 'Este email já está em uso.';
                $this->load_views($return_data);
                return;
            }

            // Insert user data
            $media_id = $this->Media_model->insert([
                'path' => base_url('/media/profile_pictures/default/child.png'),
                'alt' => 'child'
            ]);
        
            $insert_data = [
                'username' => $data['name'],
                'user' => strtolower(str_replace(' ', '', $data['name'])),
                'birthday' => $data['birthday'],
                'email' => $data['email'],
                'gender' => $data['gender'],
                'pfp' => $media_id
            ];
            $user = $this->User_model->insert($insert_data);
            print_r($insert_data);
        
            if (empty($user)) {
                $return_data['error'] = true;
                $return_data['error_msg'] = 'Houve um erro na criação da conta. Tente novamente mais tarde.';
                $this->load_views($return_data);
                return;
            }
        
            // Insert child account
            $child = $this->ChildAccount_model->insert([
                'id_user' => $user,
                'access' => 'none',
                'access_age' => 0,
                'parent' => $this->session->userdata('user')['id']
            ]);
            var_dump($user);
        
            $check_child = $this->ChildAccount_model->fetch(['id_user' => $user]);
            print_r($check_child);
        
            if (empty($check_child)) {
                $return_data['error'] = true;
                $return_data['error_msg'] = 'Houve um erro na criação da conta. Tente novamente mais tarde.';
                $this->load_views($return_data);
                return;
            }
        
            $family = $this->FamilyUser_model->fetch(['id_user' => $this->session->userdata('user')['id']]);
            var_dump($family);
        
            if (empty($family)) {
                $return_data['error'] = true;
                $return_data['error_msg'] = 'Houve um erro na criação da conta. Tente novamente mais tarde.';
                $this->load_views($return_data);
                return;
            }
        
            $this->FamilyUser_model->insert([
                'id_family' => $family['id_family'],
                'id_user' => $user
            ]);
        
            $check_family_member = $this->FamilyUser_model->fetch(['id_user' => $user]);
        
            if (empty($check_family_member)) {
                $return_data['error'] = true;
                $return_data['error_msg'] = 'Houve um erro na criação da conta. Tente novamente mais tarde.';
                $this->load_views($return_data);
                return;
            }
        
            // Redirect on success
            redirect(base_url('family_menu'));
        }
        
        private function load_views($data) {
            $this->load->view('common/header', $this->data);
            $this->load->view('common/menu', $this->data);
            $this->load->view('family_menu', $data);
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
                $user['admin'] = $id['admin'];
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

            foreach($activities as $act => $key){
                $id_media = $this->ActivityMedia_model->fetch_all(false, null, null, null, ['id_activity' => $activities[$act]['id']]);

                foreach($id_media as $id){
                    $media = $this->Media_model->fetch(['id' => $id['id_media']]);
                    $activities[$act]['images'][] = $media;
                }

                $id_activity = $activities[$act]['id'];
                $like = $this->ActivityLikes_Model->check_if_exists(['id_activity' => $id_activity, 'id_user' => $this->session->userdata('user')['id']]);

                $activities[$act]['liked'] = (!$like) ? false : true;

                $participant = $this->ActivityParticipant_Model->check_if_exists(['id_activity' => $id_activity, 'id_user' => $this->session->userdata('user')['id']]);

                $activities[$act]['participant'] = (!$participant) ? false : true; 
            }
            
            $return_data['success'] = true;
            $return_data['message'] = '';
            $return_data['data'] = $activities;
            echo json_encode($return_data);
            return true;
        }

        public function participate($id_activity = null){
            header('Content-Type: application/json');

            $return_data = [
                'success' => false,
                'message' => '',
                'data' => []
            ];

            if(!$id_activity){
                $return_data['message'] = 'Ocorreu um erro. Tente novamente mais tarde.';
                echo json_encode($return_data);
                return false;
            }

            $activity = $this->Activity_model->check_if_exists(['id' => $id_activity]);

            if(!$activity){
                $return_data['message'] = 'Ocorreu um erro. Tente novamente mais tarde.';
                echo json_encode($return_data);
                return false;
            }

            $participant = $this->ActivityParticipant_Model->check_if_exists(['id_activity' => $id_activity, 'id_user' => $this->session->userdata('user')['id']]);

            if($participant){
                $return_data['message'] = 'Já participou desta atividade!';
                echo json_encode($return_data);
                return false;
            }

            $this->Activity_model->update(['n_participants' => $activity['n_participants']+1], ['id' => $activity['id']]);

            $this->ActivityParticipant_Model->insert([
                'id_activity' => $id_activity,
                'id_user' => $this->session->userdata('user')['id']
            ]);

            if($this->ActivityParticipant_Model->error){
                $return_data['message'] = 'Ocorreu um erro. Tente novamente mais tarde.';
                echo json_encode($return_data);
                return false;
            }

            $return_data['success'] = true;
            echo json_encode($return_data);
            return true;
        }

        public function like($activity_id = null){
            header('Content-Type: application/json');
            $return_data = [
                'success' => false,
                'message' => '',
                'data' => []
            ];

            if(empty($activity_id)){
                $return_data['message'] = 'Ocorreu um erro. Tente novamente mais tarde.';
                echo json_encode($return_data);
                return false;
            }

            $activity = $this->Activity_model->check_if_exists(['id' => $activity_id]);

            if(!$activity){
                $return_data['message'] = 'Ocorreu um erro. Tente novamente mais tarde.';
                echo json_encode($return_data);
                return false;
            }

            $like = $this->ActivityLikes_Model->check_if_exists(['id_activity' => $activity_id, 'id_user' => $this->session->userdata('user')['id']]);

            if($like){
                $return_data['message'] = 'Já deu gosto nesta atividade!';
                echo json_encode($return_data);
                return false;
            }

            $this->Activity_model->update(['n_likes' => $activity['n_likes']+1], ['id' => $activity['id']]);

            $this->ActivityLikes_Model->insert(['id_activity' => $activity_id, 'id_user' => $this->session->userdata('user')['id']]);

            if($this->ActivityLikes_Model->error){
                $return_data['message'] = 'Ocorreu um erro. Tente novamente mais tarde.';
                echo json_encode($return_data);
                return false;
            }

            $return_data['success'] = true;
            echo json_encode($return_data);
            return true;
        }

        public function add_member(){
            header('Content-Type: application/json');
            $return_data = [
                'success' => false,
                'message' => '',
                'data' => []
            ];
            $data = $this->input->post();

            $user = $this->session->userdata('user');

            $family_id = $this->FamilyUser_model->fetch(['id_user' => $user['id']]);

            $family = $this->Family_model->fetch(['id' => $family_id['id_family']]);

            $members = $this->FamilyUser_model->fetch_all(false, null, null, null, ['id_family' => $family_id['id_family']]);

            $already_member = false;
            foreach ($members as $member) {
                if($member['id_user'] == $data['member'])
                    $already_member = true;
            }

            if($already_member){
                $return_data['error'] = true;
                $return_data['message'] = 'Este utilizador já é membro da sua família!';
                echo json_encode($return_data);
                return false;                
            }

            $this->FamilyUser_model->insert(['id_user' => $data['member'], 'id_family' => $family_id['id_family']]);

            if($this->FamilyUser_model->error){
                $return_data['error'] = true;
                $return_data['message'] = 'Ocorreu um erro. Tente novamente mais tarde.';
                echo json_encode($return_data);
                return false;
            }   

            $this->Family_model->update(['n_members' => ($family['n_members'] + 1)], ['id' => $family['id']]);

            if($this->Family_model->error){
                $return_data['error'] = true;
                $return_data['message'] = 'Ocorreu um erro. Tente novamente mais tarde.';
                echo json_encode($return_data);
                return false;
            }  

            $return_data['success'] = true;
            echo json_encode($return_data);
            return true;
        }

        public function remove_member($id_member = null){
            if($id_member == null){
                redirect(base_url(''));
                return false;
            }

            $user = $this->session->userdata('user');

            $family_id = $this->FamilyUser_model->fetch(['id_user' => $user['id']]);

            $family = $this->Family_model->fetch(['id' => $family_id['id_family']]);

            $members = $this->FamilyUser_model->fetch_all(false, null, null, null, ['id_family' => $family_id['id_family']]);

            $already_member = false;
            foreach ($members as $member) {
                if($member['id_user'] == $id_member)
                    $already_member = true;
            }

            if(!$already_member){
                redirect(base_url('family_menu'));           
                return false;
            }

            $this->FamilyUser_model->delete(['id_user' => $id_member]);

            if($this->FamilyUser_model->error){
                redirect(base_url('family_menu'));           
                return false;
            }  

            $this->Family_model->update(['n_members' => ($family['n_members'] - 1)], ['id' => $family['id']]);

            if($this->Family_model->error){
                $redirect(base_url('family_menu'));           
                return false;
            }  

            redirect(base_url('family_menu'));           
            return true;
        }

        public function promote_member($id_member = null) {
            if($id_member == null){
                redirect(base_url('family_menu'));
                return false;
            }

            $user = $this->session->userdata('user');

            $family_id = $this->FamilyUser_model->fetch(['id_user' => $user['id']]);

            $family = $this->Family_model->fetch(['id' => $family_id['id_family']]);

            $members = $this->FamilyUser_model->fetch_all(false, null, null, null, ['id_family' => $family_id['id_family']]);

            $already_member = false;
            foreach ($members as $member) {
                if($member['id_user'] == $id_member)
                    $already_member = true;
            }

            if(!$already_member){
                redirect(base_url('family_menu'));           
                return false;
            }

            $this->FamilyUser_model->update(['admin' => 1], ['id_user' => $id_member]);

            if($this->FamilyUser_model->error){
                redirect(base_url('family_menu'));           
                return false;
            }  

            redirect(base_url('family_menu'));
            return true;
        }

        public function update_info(){
            
            $user = $this->session->userdata('user');

            $data = $this->input->post();

            if(empty($data)){
                redirect(base_url('family_menu'));
                return false;
            }

            $family_id = $this->FamilyUser_model->fetch(['id_user' => $user['id']]);
            
            $this->Family_model->update(['family_name' => $data['familyName']], ['id' => $family_id['id_family']]);

            if($this->Family_model->error){
                reidrect(base_url('family_menu'));
                return false;
            }

            redirect(base_url('family_menu'));
            return true;
        }

    }   
?>

