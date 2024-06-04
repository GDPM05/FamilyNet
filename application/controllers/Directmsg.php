<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class Directmsg extends MY_Controller {

        function __construct(){
            parent::__construct();
            $this->load->library(array('form_validation', 'session'));
            $this->load->model('Friends_model');
            $this->load->model('Message_model');
            $this->load->model('Groups_model');
            $this->load->model('GroupUsers_model');
            $this->load->model('Conversation_model');
            $this->load->model('UserConversation_model');
            if(!$this->LoggedIn()){
                redirect(base_url('logout'));
                exit;
            }
        }

        public function index() {
            $data = array(
                'title' => TITLE.' | Mensagens Privadas',
                'user' => $this->session->userdata('user')
            );

            $user_id = $this->session->userdata['user']['id'];

            $conversations_ids = $this->UserConversation_model->fetch_all(false, null, null, null, ['id_user' => $user_id]);

            $friends_ids = $this->Friends_model->fetch_friends($this->session->userdata('user')['id']);
            
            $friends_arr = [];
            foreach($friends_ids as $friends){
                //print_r($friends);
                unset($friends['status']);
                foreach($friends as $id){
                    //print_r($id);
                    if($id != $data['user']['id'])
                        $friends_arr[] = $this->User_model->fetch(['id' => $id]);
                }
            }

            $conversations = [];
            if(!empty($conversations_ids) && is_array($conversations_ids[count($conversations_ids)-1])){
                foreach($conversations_ids as $conv){
                    $conversations[] = $this->get_conversation_details($conv['id_conv'], $user_id);
                }
            }else if(!empty($conversations_ids) && !is_array($conversations_ids[count($conversations_ids)-1])){
                $conversations[] = $this->get_conversation_details($conversations_ids['id_conv'], $user_id);
            }

            $groups_ids = $this->GroupUsers_model->fetch_all(null, null, null, null, ['id_user' => $data['user']['id']]);
            $groups = [];
            foreach($groups_ids as $group){
                //print_r($group);
                $groups[] = $this->Groups_model->fetch(['id' => $group['id_group']]);
            }

            foreach($groups as $group){
                $groups[array_search($group, $groups)]['picture'] = $this->Media_model->fetch(['id' => $groups[array_search($group, $groups)]['picture']]);
            }

            $data['conversations'] = $conversations; //(!empty($conversations)) ? $conversations : null;
            $data['friends'] = $friends_arr;
            $data['groups'] = $groups;
            $this->load->view('common/header', $data);
            $this->load->view('common/menu', $this->data);
            $this->load->view('direct_msg', $data);
            $this->load->view('common/footer');
        }

        public function get_messages(){
            $offset = $this->uri->segment(2);
            $conv_id = $this->uri->segment(3);
        
            $messages = $this->Message_model->fetch_messages(true, 15, $offset, 'send_date DESC', ['id_conv' => $conv_id]);
        
            $this->Message_model->update_message_state($conv_id, $this->session->userdata('user')['id']);

            header('Content-Type: application/json');
            echo json_encode($messages);
        }
        

        public function fetch_user(){
            $conv_id = $this->uri->segment(2);
            $user_id = $this->session->userdata['user']['id'];

            $user_id = $this->UserConversation_model->get_conversation(['id_conv' => $conv_id, 'my_id' => $user_id])['id_user'];

            $data['user'] = $this->User_model->fetch(['id' => $user_id], 'id, user, username, pfp');
            $data['user']['pfp'] = $this->get_profile_pic($data['user']['id']);
            header('Content-Type: application/json');
            echo json_encode($data['user']);
        }

        public function send_message(){
            //print_r($_POST);
            $data = $_POST['message'];
    
            $this->Message_model->insert($data);

            if($this->Message_model->error){
                $data['error'] = $this->Message_model->error;
                $data['error_msg'] = $this->Message_model->error_message;
            }

            header('Content-Type: application/json');
            echo json_encode($data);
        }

        public function get_friends(){
            $user_id = $this->session->userdata['user']['id'];

            $data = $this->Friends_model->fetch_friends($user_id);

            if($this->Friends_model->error){
                $data['error'] = $this->Message_model->error;
                $data['error_msg'] = $this->Message_model->error_message;
            }

            header('Content-Type: application/json');
            echo json_encode($data);
        }

        public function create_group(){
            $group_info = $this->input->post();
            //print_r($user_info);

            foreach($group_info as $info){
                if(empty($info))
                    redirect(base_url('direct_msg'));
            }

            $config['upload_path'] = './media/group_pictures';
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            $config['max_size'] = 100;

            $this->load->library('upload', $config);

            if(!$this->upload->do_upload('gpic')){
                print_r($this->upload->display_errors());
                return false;
            }

            $data = $this->upload->data();

            $group_pic_id = $this->Media_model->insert([
                'path' => base_url('/media/')."group_pictures/".$data['raw_name'].$data['file_ext'],
                'alt' => $group_info['gname']
            ]);

            if($this->Media_model->error)
                redirect(base_url('direct_msg'));
            

            $conv_id = $this->Conversation_model->insert(['title' => md5($group_info['title'])]);

            $group_id = $this->Groups_model->insert([
                'name' => $group_info['gname'],
                'n_members' => count($group_info['friend_list']) + 1, # +1 corresponde ao utilizador que criou o grupo
                'picture' => $group_pic_id,
                'description' => $group_info['gdesc'],
                'id_conversation' => $conv_id,
                'privacy' => ($group_info['gprivacy'] == 'on') ? 1 : 0
            ]);

            if($this->Groups_model->error)
                redirect(base_url('direct_msg'));

            $this->GroupUsers_model->insert([
                'id_group' => $group_id,
                'id_user' => $this->session->userdata['user']['id'],
                'group_admin' => 1
            ]);

            foreach($group_info['friend_list'] as $friend){
                $this->GroupUsers_model->insert([
                    'id_group' => $group_id,
                    'id_user' => $friend,
                    'group_admin' => 0
                ]);
            }

            // return;
            redirect(base_url('direct_msg'));
        }

        public function send_message_private($id_friend = null){
            print_r($this->UserConversation_model->check_if_exists(['id_user' => $this->session->userdata('user')['id']]));
            //return;
            if($this->UserConversation_model->check_if_conv_exists($id_friend, $this->session->userdata('user')['id'])){
                $conv_id = $this->Conversation_model->insert(['title' => md5($id_friend)]);
    
                $this->UserConversation_model->insert([
                    'id_conv' => $conv_id,
                    'id_user' => $this->session->userdata('user')['id']
                ]);
                $this->UserConversation_model->insert([
                    'id_conv' => $conv_id,
                    'id_user' => $id_friend
                ]);
            }

           redirect(base_url('direct_msg')); 
        }

        private function get_conversation_details($id, $user_id){
            if(empty($id) || empty($user_id))
                return;
            $conversation = [];
            $conversation['id'] = $id;
            $conv = $this->UserConversation_model->get_conversation(['my_id' => $user_id, 'id_conv' => $id])['id_user'];
            $user = $this->User_model->fetch(['id' => $conv], 'id, pfp, username, user');
            $user_pfp = $this->Media_model->fetch(['id' => $user['pfp']]);
            $conversation['user'] = $user;
            $conversation['pfp'] = $user_pfp;
            return $conversation;
        }

        public function search_friends($values = ""){
            header('Content-Type: application/json');
            if($values == ""){
                echo json_encode(['error' => true, 'error_msg' => "Invalide values."]);
                return;
            }

            $friends = $this->Friends_model->fetch_friends($this->session->userdata('user')['id']);

            $users = $this->User_model->fetch_all_like(['username', $values], null, null, ['id' => $friends, 'multiple' => true], null);

            foreach($users as $user){
                if($user['id'] == $this->session->userdata('user')['id'])
                    unset($users[array_search($user, $users)]);
                else{
                    $users[array_search($user, $users)]['pfp'] = $this->Media_model->fetch(['id' => $users[array_search($user, $users)]['pfp']]);
                }
            }

            echo json_encode($users);
        }

        public function new_conv($id){
            header('Content-Type: application/json');
            if(empty($id)){
                echo json_encode(['error' => true, 'error_msg' => 'Info missing...']);
                return;
            }

            $user_id = $this->session->userdata('user')['id'];

            if($this->UserConversation_model->check_if_conv_exists($user_id, $id)){
                $conv_id = $this->Conversation_model->insert(['title' => md5("".$user_id."".$id)]);
                $this->UserConversation_model->insert(['id_conv' => $conv_id, 'id_user' => $user_id]);
                $this->UserConversation_model->insert(['id_conv' => $conv_id, 'id_user' => $id]);
            }else{
                echo json_encode(['error' => true, 'error_msg' => 'Users already have a conversation...']);
                return; 
            }

             echo json_encode(['error' => false, 'success_msg' => 'Conversation created successfully']);    
        }   

        public function get_group_members($conv_id = null){
            header('Content-Type: application/json');
            
            if(!$conv_id){
                echo json_encode(['success' => false, 'message' => 'Ocorreu um erro. Tente novamente mais tarde.']);
                return false;
            }

            $group = $this->Groups_model->fetch(['id_conversation' => $conv_id]);

            if(empty($group)){
                echo json_encode(['success' => false, 'message' => 'Ocorreu um erro. Tente novamente mais tarde.']);
                return false;
            }

            $group_members = $this->GroupUsers_model->fetch_all(false, null, null, null, ['id_group' => $group['id']]);

            if(empty($group_members)){
                echo json_encode(['success' => false, 'message' => 'Ocorreu um erro. Tente novamente mais tarde.']);
                return false;
            }

            foreach($group_members as $key => $info){
                if($group_members[$key]['id_user'] == $this->session->userdata('user')['id']){
                    unset($group_members[$key]);
                    continue;
                }

                $group_members[$key] = $this->User_model->fetch(['id' => $group_members[$key]['id_user']], 'id, username, user, pfp');

                if(empty($group_members[$key])){
                    echo json_encode(['success' => false, 'message' => 'Ocorreu um erro. Tente novamente mais tarde.']);
                    return false;
                }

                $group_members[$key]['pfp'] = $this->Media_model->fetch(['id' => $group_members[$key]['pfp']]);
                
                if(empty($group_members[$key]['pfp'])){
                    echo json_encode(['success' => false, 'message' => 'Ocorreu um erro. Tente novamente mais tarde.']);
                    return false;
                }
            }

            echo json_encode(['success' => true, 'message' => '', 'data' => $group_members]);
            return true;
        }

        public function fetch_conversation($id_conv = null){
            header('Content-Type: application/json');

            if($id_conv == null){
                echo json_encode(['success' => false, 'message' => 'Ocorreu um erro. Tente novamente mais tarde.']);
                return false;
            }

            $conversa = $this->Groups_model->fetch(['id_conversation' => $id_conv]);

            if(empty($conversa)){
                echo json_encode(['success' => false, 'message' => 'Ocorreu um erro. Tente novamente mais tarde.']);
                return false;
            }

            $return_data = [
                'success' => true,
                'data' => [
                    'name' => $conversa['name'],
                    'picture' => $this->Media_model->fetch(['id' => $conversa['picture']]),
                    'description' => $conversa['description']
                ]
            ];

            echo json_encode($return_data);
            return true;
        }

    }
?>