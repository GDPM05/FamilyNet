<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class Search extends MY_Controller {
        //private $count = 0;
        function __construct(){
            parent::__construct();
            $this->load->library('pagination');
            $this->load->model('User_model');
            if(!$this->LoggedIn()){
                redirect(base_url('logout'));
                exit;
            }
        }

        public function index() {
            $data = array(
                'title' => TITLE.' | Search',
            );

            $this->load->view('common/header', $data);
            $this->load->view('common/menu', $this->data);
            $this->load->view('search', $data);
            $this->load->view('common/footer');
        }

        public function fetch(){
            $data = array();
            
            $page = ($this->input->post('page')) ? $this->input->post('page') : 1;
            $query = ($this->input->post('query')) ? $this->input->post('query') : '';
            $data['page_num'] = $page;

            if($page == 0)
                unset($_SESSION['last_search']);

            $per_page = 7;
            
            $_SESSION['last_search'] = $query;

            $offset = ($page - 1) * $per_page;
            $_SESSION['data']['offset'] = $offset;
            $data['query'] = $this->User_model->fetch_all_like(["user", $query], $per_page, $offset, null);

            foreach($data['query'] as $key => $user){
                if($user['id'] == $this->session->userdata('user')['id']){
                    unset($data['query'][$key]);
                    continue;
                }
                $data['query'][$key]['pfp'] = $this->get_profile_pic($user['id']);
            }

            //print_r($data);
            $config = array();
            $config['base_url'] = base_url("search/");
            $config['total_rows'] = $this->User_model->get_count(array('user' => $query));
            $config['per_page'] = $per_page;
            $config['use_page_numbers'] = TRUE;
            $config['cur_page'] = $page;
            $this->pagination->initialize($config);
            
            $data['links'] = $this->pagination->create_links();
            $data['page'] = $this->pagination;
            

            header('Content-Type: application/json');
            
            echo json_encode($data);
        }

        

        public function getProfilePicture(){
            $userId = $this->input->post('userId'); // Obtém o user.id da solicitação AJAX

            // Use $userId para buscar o URL da imagem com base no ID
            $profilePictureUrl = $this->main->get_profile_pic($userId);

            // Retorne o URL da imagem como uma resposta AJAX
            echo $profilePictureUrl;
        }

        

    }
?>