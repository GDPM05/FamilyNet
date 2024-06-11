<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class Main extends MY_Controller {

        function __construct(){
            parent::__construct();
            if(!$this->LoggedIn()){
                redirect(base_url('logout'));
                exit;
            }
            $this->load->model('Friends_Model');
            $this->load->model('Media_Model');
            $this->load->model('PrivacyLevel_Model');
            $this->load->model('Post_Model');
            $this->load->model('PostLikes_Model');
            $this->load->model('Notification_Model');
            $this->load->model('PostMedia_Model');
            $this->load->model('FamilyUser_Model');
            $this->load->model('Friends_Model');
            $this->load->model('Comments_Model');
            $this->load->model('User_Model');
        }

        public function index() {
            $data = array(
                'title' => TITLE.'',
                'user' => $this->session->userdata('user'),
                'privacy' => $this->PrivacyLevel_Model->fetch_all(),
            );

            if(!$this->session->userdata('logged_in'))
                redirect(base_url('logout'));

            $friends_ids = $this->Friends_Model->fetch_friends($data['user']['id']);

            //print_r($friends_ids);

            $friends = [];
            foreach($friends_ids as $friend){
                $id = ($friend['id_user1'] == $data['user']['id']) ? $friend['id_user2'] : $friend['id_user1'];

                $user = $this->User_model->fetch(['id' => $id], 'id, user, username, email, birthday, pfp');
                $user['pfp'] = $this->Media_model->fetch(['id' => $user['pfp']]);
                $friends[] = $user;
            }

            $data['friends'] = $friends;
            //print_r($data['friends']);
            $this->load->view('common/header', $data);
            $this->load->view('common/menu', $this->data);
            $this->load->view('main', $data);
            $this->load->view('common/footer');
        }

        public function new_post(){
            $data = $this->input->post();
            print_r($data);
            print_r($_FILES);
        
            $post_info = [
                'text' => $data['post-text'],
                'id_sender' => $this->session->userdata('user')['id'],
                'sent_date' => date('Y-m-d H:i:s'),
                'privacy_level' => $data['privacy']
            ];
        
            $post_id = $this->Post_Model->insert($post_info);
        
            // Diretório onde os arquivos serão salvos
            $upload_dir = './media/post-media/';
        
            // Verificar se o diretório existe, caso contrário, criar
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
        
            // Iterar sobre os arquivos enviados
            foreach ($_FILES['file-upload']['tmp_name'] as $key => $tmp_name) {
                // Verificar se o arquivo foi enviado sem erros
                if ($_FILES['file-upload']['error'][$key] === UPLOAD_ERR_OK) {
                    $file_name = basename($_FILES['file-upload']['name'][$key]);
                    $target_file = $upload_dir . $file_name;
            
                    // Mover o arquivo para o diretório de destino
                    if (move_uploaded_file($tmp_name, $target_file)) {
                        // Redimensionar a imagem para 400x400
                        $resize_result = $this->resize_image($target_file, 200, 200);
                        if (!$resize_result['status']) {
                            echo "Erro ao redimensionar a imagem $file_name: " . $resize_result['message'];
                        } else {
                            // Inserir dados da mídia no modelo Media_Model
                            $media_data = [
                                'path' => $resize_result['file_path'],
                                'alt' => md5($resize_result['file_path'])  // Valor associado à imagem, você pode ajustar conforme necessário
                            ];
                            $media_id = $this->Media_Model->insert($media_data);
            
                            // Inserir ID da mídia e ID do post no modelo Post_Media
                            $post_media_data = [
                                'id_post' => $post_id,
                                'id_media' => $media_id
                            ];
                            $this->PostMedia_Model->insert($post_media_data);
                        }
                    } 
                }
            }
            redirect(base_url(''));
        }
        
        private function resize_image($file_path, $width, $height){
            $config['image_library'] = 'gd2';
            $config['source_image'] = $file_path;
            $config['maintain_ratio'] = TRUE;
            $config['width'] = $width;
            $config['height'] = $height;
        
            $this->load->library('image_lib', $config);
        
            if (!$this->image_lib->resize()) {
                return [
                    'status' => false,
                    'message' => $this->image_lib->display_errors(),
                    'file_path' => $file_path
                ];
            }
        
            $this->image_lib->clear();
            return [
                'status' => true,
                'message' => null,
                'file_path' => $file_path
            ];
        }

        public function get_posts() {
            $user_id = $this->session->userdata('user')['id'];
    
            $limit = 10;
            $page = $this->uri->segment(2);
            $offset = ($page - 1) * $limit;
    
            // Fetch the latest 10 posts
            $posts = $this->Post_Model->fetch_all(true, $limit, $offset, null, null);
    
            $result = [];
            
            foreach ($posts as $post) {
                $user = $this->User_model->fetch(['id' => $post['id_sender']], 'id, username, user, pfp');
                $user['pfp'] = $this->Media_Model->fetch(['id' => $user['pfp']]);
                if ($post['privacy_level'] == 1) {
                    // Public post
                    $user['post'] = $post;
                } elseif ($post['privacy_level'] == 2) {
                    // Friends-only post
                    if ($this->Friends_Model->check_friends($user_id, $post['id_sender'])) {
                        $user['post'] = $post;
                    }
                } elseif ($post['privacy_level'] == 3) {
                    // Family-only post
                    $family_id = $this->FamilyUser_Model->fetch(['id_user' => $user_id]);
                    //var_dump($family_id);
                    if ($family_id && $this->FamilyUser_Model->check_if_exists(['id_family' => $family_id['id_family'], 'id_user' => $post['id_sender']])) {
                        $user['post'] = $post;
                    }
                } elseif ($post['privacy_level'] == 4) {
                    // Private post (only the user can see)
                    if ($post['id_sender'] == $user_id) {
                        $user['post'] = $post;
                    }
                }

                if(!isset($user['post']))
                    continue;
                //print_r($user);
                $media = $this->PostMedia_Model->fetch_all(null, null, null, null, ['id_post' => $user['post']['id']]);
                if(!empty($media))
                    foreach($media as $md){
                        // print_r($md);
                        $user['post']['media'][] = $this->Media_model->fetch(['id' => $md['id_media']]);
                    }
                
                $user['already_like'] = $this->PostLikes_Model->check_user($this->session->userdata('user')['id'], $user['post']['id']);
                $result[] = $user;
            }
    
            // Return the filtered posts
            header('Content-Type: application/json');
            echo json_encode($result);
        }

        public function add_like($id_post = null, $id_user){
            header('Content-Type: application/json');

            if($id_post == null || $id_user == null){
                $data['success'] = false;
                $data['message'] = 'Ocorreu um erro. Tente outra vez mais tarde.';
                echo json_encode($data);
                return false;
            }

            $post_likes = (int) $this->Post_Model->get_likes($id_post);

            // print_r($post_likes);

            $this->Post_Model->add_like($id_post);
            $this->PostLikes_Model->insert([
                'id_post' => $id_post,
                'id_user' => $id_user
            ]);

            if((isset($this->Post_Model->error) && $this->Post_Model->error) || (isset($this->PostLike_Model->error) && $this->PostLike_Model->error)){
                $data['success'] = false;
                $data['message'] = 'Ocorreu um erro. Tente outra vez mais tarde.';
                echo json_encode($data);
                return false;
            }

            $data['success'] = true;
            $data['current_likes'] = $this->Post_Model->get_likes($id_post);
            echo json_encode($data);
            return false;
        }

        public function remove_like($id_post = null, $id_user = null){
            header('Content-Type: application/json');

            if($id_post == null || $id_user == null){
                $data['success'] = false;
                $data['message'] = 'Ocorreu um erro. Tente outra vez mais tarde.';
                echo json_encode($data);
                return false;
            }
            
            $post_likes = (int) $this->Post_Model->get_likes($id_post);

            // print_r($post_likes);

            $this->Post_Model->remove_like($id_post);
            $this->PostLikes_Model->delete([
                'id_post' => $id_post,
                'id_user' => $id_user
            ]);


            if((isset($this->Post_Model->error) && $this->Post_Model->error) || (isset($this->PostLikes_Model->error) && $this->PostLikes_Model->error)){
                $data['success'] = false;
                $data['message'] = 'Ocorreu um erro. Tente outra vez mais tarde.';
                echo json_encode($data);
                return false;
            }

            $data['success'] = true;
            $data['current_likes'] = $this->Post_Model->get_likes($id_post);
            echo json_encode($data);
            return false;
        }

        public function add_comment(){
            header('Content-Type: application/json');
            $data = $this->input->post();
            // print_r($data);

            $insert_data = [
                'text' => $data['comment'],
                'id_post' => $data['post_id'],
                'id_user' => $this->session->userdata('user')['id'],
                'sent_date' => date('Y-m-d H:i:s')
            ];

            $this->Comments_Model->insert($insert_data);

            if($this->Comments_Model->error){
                $return_data['success'] = false;
                echo json_encode($return_data);
                return;
            }

            $return_data['success'] = true;
            echo json_encode($return_data);
            return true;
        }

        public function get_comments($page = 1, $id_post = null){
            header('Content-Type: application/json');
            $limit = 10;
            $offset = ($page - 1) * $limit;

            $return_data = [];

            $comments = [];
            
            $comments = $this->Comments_Model->fetch_all(true, $limit, $offset, null, ['id_post' => $id_post]);
            // echo $this->Comments_Model->db->last_query();
            foreach($comments as $key => $comment){
                $comments[$key]['username'] = $this->User_Model->fetch(['id' => $comments[$key]['id_user']], 'username, pfp');
                $comments[$key]['pfp'] = $this->Media_Model->fetch(['id' => $comments[$key]['username']['pfp']], 'path');
                unset($comments[$key]['username']['pfp']);
                // print_r($comment);
            }
            $return_data['comments'] = $comments;
            $return_data['n_comments'] = $this->Comments_Model->get_count(['id_post' => $id_post]);
            echo json_encode($return_data);
            return true;
        }

        public function delete_comment(){
            header('Content-Type: application/json');
            $data = $this->input->post();
            $return_data = ['success' => true];
            $this->Comments_Model->delete(['id' => $data['comment_id']]);

            if($this->Comments_Model->error){
                $return_data['success'] = false;
                echo json_encode($return_data);
                return false;
            }

            $insert_data = [
                'type_id' => 2,
                'sent_date' => date('Y-m-d H:i:s'),
                'receiver_id' => $data['userId'],
                'sender_id' => $this->session->userdata('user')['id'],
                'message_text' => $this->session->userdata('user')['username'].' Apagou um comentário seu na sua publicação. <br/>"'.$data['comment'].'"'
            ];

            $this->Notification_Model->insert($insert_data);

            if($this->Notification_Model->error){
                $return_data['success'] = false;
                echo json_encode($insert_data);
                return false;
            }

            echo json_encode($return_data);
            return true;
        }

        public function delete_post($post_id = null){
            header('Content-Type: application/json');
            if(empty($post_id)){
                $return_data['success'] = false;
                echo json_encode($return_data);
                return false;
            }

            $post = $this->Post_Model->fetch(['id' => $post_id]);

            if(empty($post)){
                $return_data['success'] = false;
                echo json_encode($return_data);
                return false;
            }

            if($post['id_sender'] != $this->session->userdata('user')['id']){
                $return_data['success'] = false;
                echo json_encode($return_data);
                return false;
            }

            $this->Post_Model->delete(['id' => $post_id]);

            if($this->Post_Model->error){
                $return_data['success'] = false;
                echo json_encode($return_data);
                return false;
            }

            $this->PostLikes_Model->delete(['id_post' => $post_id]);

            if($this->PostLikes_Model->error){
                $return_data['success'] = false;
                echo json_encode($return_data);
                return false;
            }

            $this->PostMedia_Model->delete(['id_post' => $post_id]);

            if($this->PostMedia_Model->error){
                $return_data['success'] = false;
                echo json_encode($return_data);
                return false;
            }

            $this->Comments_Model->delete(['id_post' => $post_id]);

            if($this->Comments_Model->error){
                $return_data['success'] = false;
                echo json_encode($return_data);
                return false;
            }

            $return_data['success'] = false;
            echo json_encode($return_data);
            return false;
        }
    }
?>