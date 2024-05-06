<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class Profile extends MY_Controller {
        public $data = array();
        function __construct(){
            parent::__construct();
            $this->load->model('Friends_model');
            $this->load->model('Notification_model');
            //$this->load->library('Mailer');
            if(!$this->LoggedIn()){
                redirect(base_url('logout'));
                exit;
            }
        }

        public function index() {
            $data = array(
                'title' => TITLE.' | Profile',
                'user' => $this->session->userdata['user']
            );

            $this->load->view('common/header', $data);
            $this->load->view('common/menu', $this->data);
            $this->load->view('profile', $data);
            $this->load->view('common/footer');
        }   

        public function load_profile(){
            $info = $this->uri->segment(2);
            $user = (is_numeric($info)) ?  $this->User_model->fetch(array('id' => $info)) :  $this->User_model->fetch(array('user' => $info));
            $pfp = $this->Media_model->fetch(array('id' => $user['pfp']));
            $this->data['title'] = TITLE.' | '.$user['username'];
            $this->data['user_pfp']['path'] = $pfp['path'];
            $this->data['user_pfp']['alt'] = $pfp['alt'];
            $this->data['user'] = $user;
            $this->data['already_friends'] = $this->Friends_model->check_friends($this->session->userdata('user')['id'], $user['id']);
            
            $this->load->view('common/header', $this->data);
            $this->load->view('common/menu', $this->data);
            $this->load->view('show_profile', $this->data);
            $this->load->view('common/footer');
        }

        public function send_friend_invitation(){
            $error = false;
            $message = '';

            /* STATUS
                1 -> Aceite
                2 -> Recusado/Eliminado
                3 -> Pendente
            */

            $user = $this->User_model->fetch(['id' => $this->uri->segment(2)]);

            $check_friends = $this->Friends_model->check_friends($user['id'], $this->session->userdata('user')['id']);

            if($check_friends !== TRUE){
                switch($check_friends->status){
                    case 2:
                        $this->update_invite($user['id'], 3);
                        return;
                        break;
                    case (1 || 3):
                        redirect(base_url('main'));
                        return;
                        break;
                }
            }

            $this->Friends_model->insert([
                'id_user1' => $user['id'],
                'id_user2' => $this->session->userdata('user')['id'],
                'status' => 3
            ]);

            $this->Notification_model->insert([
                'type_id' => 1,
                'sent_date' => date('Y-m-d H:i:s'),
                'receiver_id' => $user['id'],
                'sender_id' => $this->session->userdata('user')['id'],
                'message_text' => $this->session->userdata('user')['username'].' enviou um pedido de amizade'
            ]);

            if($this->Friends_model->error){
                $error = $this->Friends_model->error;
                $message = $this->Friends_model->error_message;
            }

            if($this->Notification_model->error){
                $error = $this->Notification_model->error;
                $message = $this->Notification_model->error_message;
            }

            header('Content-Type: application/json');
            echo json_encode(['erro' => $error, 'mensagem' => $message]);
        }

        public function update_invite($user_id = null, $status = null){
            $message = '';
            $error = false;
        
            $user = $this->User_model->fetch(['id' => $user_id]);
            $check_friends = $this->Friends_model->check_friends($user['id'], $this->session->userdata('user')['id']);
        
            if($check_friends === TRUE)
                return;
        
            switch($status){
                case 1:
                    $type_id = 2;
                    $status_message = $this->session->userdata('user')['username'].' aceitou o seu pedido de amizade!';
                    break;
                case 2:
                    $type_id = 2; 
                    $status_message = ($check_friends->status == 3) ? $this->session->userdata('user')['username'].' recusou o seu pedido de amizade!' : $user['username'].' já não é seu amigo.';
                    break;
                case 3: 
                    $type_id = 1;
                    $status_message = $this->session->userdata('user')['username'].' enviou um pedido de amizade!';
                    break;
            }

            $this->Friends_model->update_invite($user['id'], $this->session->userdata('user')['id'], $status);
            
            $this->Notification_model->delete(['id' => $this->input->post()['notification_id']]);

            $notification_id = $this->Notification_model->insert([
                'type_id' => 2,
                'sent_date' => date('Y-m-d H:i:s'),
                'receiver_id' => $user['id'],
                'sender_id' =>  $this->session->userdata('user')['id'],
                'message_text' => $status_message
            ]);

            if($this->Friends_model->error){
                $error = $this->Friends_model->error;
                $message = $this->Friends_model->error_message;
            }

            if($this->Notification_model->error){
                $error = $this->Notification_model->error;
                $message = $this->Notification_model->error_message;
            }
        
            $data['mensagem'] = $status_message;
            $data['error'] = $error;
            $data['error_message'] = $message;

            header('Content-Type: application/json');
            
            echo json_encode($data);
        }

        public function get_friends(){

            $friends = $this->Friends_model->fetch_friends($this->session->userdata('user')['id']);

           // print_r($friends);

            $friend_user = [];
            foreach($friends as $friend){
                $user_id = ($friend['id_user1'] == $this->session->userdata('user')['id']) ? $friend['id_user2'] : $friend['id_user1'];
                $user = $this->User_model->fetch(['id' => $user_id], 'id, user, username, pfp');
                $user['pfp'] = $this->Media_model->fetch(['id' => $user['pfp']]);
                $friend_user[] = $user;
            }

            //print_r($friend_user);

            header('Content-Type: application/json');
            echo json_encode($friend_user);
        }

        public function update_info(){

            $info = $this->input->post();   

            if(empty($info)){
                return;
            }

            $update_data = [
                'email'   => $info['email'],
                'username'   => $info['username'],
                'phone'   => $info['phone']
            ];

            $this->User_model->update($update_data, ['id' => $this->session->userdata('user')['id']]);

            if($this->User_model->error){
                return false;
            }

            $new_userdata = $this->User_model->fetch(['id' => $this->session->userdata('user')['id'], 'id, user, username, email, phone, pfp, birthday, gender, p_role']);

            $this->session->set_userdata('user', $new_userdata);

            redirect(base_url('profile'));

        }


    }


?>
