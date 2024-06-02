<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Search extends MY_Controller {

    function __construct(){
        parent::__construct();
        $this->load->library('pagination');
        $this->load->model('User_model');
        $this->load->model('ParentalRole_model');
        if(!$this->LoggedIn()){
            redirect(base_url('logout'));
            exit;
        }
    }

    public function index() {
        $data = array(
            'title' => TITLE.' | Pesquisa',
        );

        $this->load->view('common/header', $data);
        $this->load->view('common/menu', $this->data);
        $this->load->view('search', $data);
        $this->load->view('common/footer');
    }

    public function fetch(){
        $page = $this->input->post('page', TRUE) ?: 1;
        $query = $this->input->post('query', TRUE) ?: '';
        $this->session->set_userdata('last_search', $query);

        if($page == 0){
            $this->session->unset_userdata('last_search');
        }

        $per_page = 7;
        $offset = ($page - 1) * $per_page;
        $this->session->set_userdata('data_offset', $offset);

        $users = $this->User_model->fetch_all_like(["username", $query], $per_page, $offset, null, "user, username, pfp, id, p_role");
        $users = $this->filterCurrentUser($users);

        foreach($users as $key => $a){
            $users[$key]['p_role'] = $this->ParentalRole_model->fetch(['id' => $users[$key]['p_role']]);
        }

        $data['query'] = $users;

        $config = $this->getPaginationConfig($query, $per_page, $page);
        $this->pagination->initialize($config);
        
        $data['links'] = $this->pagination->create_links();
        $data['page'] = $this->pagination;

        $this->output->set_content_type('application/json')->set_output(json_encode($data));
    }

    private function filterCurrentUser($users){
        foreach($users as $key => $user){
            if($user['id'] == $this->session->userdata('user')['id']){
                unset($users[$key]);
            } else {
                $users[$key]['pfp'] = $this->get_profile_pic($user['id']);
            }
        }
        return array_values($users); // Reindex the array
    }

    private function getPaginationConfig($query, $per_page, $page){
        return array(
            'base_url' => base_url("search/"),
            'total_rows' => $this->User_model->get_count(array('user' => $query)),
            'per_page' => $per_page,
            'use_page_numbers' => TRUE,
            'cur_page' => $page
        );
    }
}
?>
