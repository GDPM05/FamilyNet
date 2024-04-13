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
                'title' => TITLE.' | Direct Messages',
                'user' => $this->session->userdata('user')
            );

            $user_id = $this->session->userdata['user']['id'];

            $conversations_ids = $this->UserConversation_model->fetch_all(false, null, null, null, ['id_user' => $user_id]);
    
            $conversations = [];
            if(!empty($conversations_ids) && is_array($conversations_ids[count($conversations_ids)-1])){
                foreach($conversations_ids as $conv){
                    $conversations[] = $this->get_conversation_details($conv['id_conv'], $user_id);
                }
            }else if(!empty($conversations_ids) && !is_array($conversations_ids[count($conversations_ids)-1])){
                $conversations[] = $this->get_conversation_details($conversations_ids['id_conv'], $user_id);
            }

            $data['conversations'] = (!empty($conversations)) ? $conversations : null;
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

            $data = $this->Friend_Model->fetch_friends($user_id);

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
            $config['allowed_types'] = 'gif|jpg|png';
            $config['max_size'] = 100;

            $this->load->library('upload', $config);

            if(!$this->upload->do_upload('gpic')){
                print_r($this->upload->display_errors());
                return false;
            }

            $group_pic_id = $this->Media_model->insert([
                'path' => base_url().'/media/group_pictures',
                'alt' => $group_info['gname']
            ]);

            if($this->Media_model->error)
                redirect(base_url('direct_msg'));
            

            $group_id = $this->Groups_model->insert([
                'name' => $group_info['gname'],
                'n_members' => count($group_info['friend_list']) + 1, # +1 corresponde ao utilizador que criou o grupo
                'picture' => $group_pic_id,
                'description' => $group_info['gdesc']
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

            redirect(base_url('direct_msg'));
        }

        public function send_message_private($id_friend = null){
            print_r($this->UserConversation_model->check_if_exists(['id_user' => $this->session->userdata('user')['id']]));
            //return;
            if(empty($this->UserConversation_model->check_if_exists(['id_user' => $this->session->userdata('user')['id']])) && empty($this->UserConversation_model->check_if_exists(['id_user' => $id_friend]))){
                $conv_id = $this->Conversation_model->insert([]);
                $this->UserConversation_model->insert([
                    'id_conv' => $convd_id,
                    'id_user' => $this->session->userdata('user')['id']
                ]);
                $this->UserConversation_model->insert([
                    'id_conv' => $convd_id,
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
            $user = $this->User_model->fetch(['id' => $conv], ['id', 'pfp', 'username', 'user']);
            $user_pfp = $this->Media_model->fetch(['id' => $user['pfp']]);
            $conversation['user'] = $user;
            $conversation['pfp'] = $user_pfp;
            return $conversation;
        }

    }
?>