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

        public function get_messages(){
            $offset = $this->uri->segment(2);
            $friend_id = $this->uri->segment(3);
        
            $messages = $this->Message_model->fetch_messages(true, 15, $offset, 'send_date DESC', ['id_friend' => $friend_id, 'id_user' => $this->session->userdata('user')['id']]);
        
            $this->Message_model->update_message_state($friend_id, $this->session->userdata('user')['id']);

            header('Content-Type: application/json');
            echo json_encode($messages);
        }
        

        public function fetch_user(){
            $user_id = $this->uri->segment(2);
            $data['user'] = $this->User_model->fetch(['id' => $user_id], 'id, user, username, pfp');
            $data['user']['pfp'] = $this->get_profile_pic($data['user']['id']);
            header('Content-Type: application/json');
            echo json_encode($data['user']);
        }

        public function send_message(){
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

    }
?>