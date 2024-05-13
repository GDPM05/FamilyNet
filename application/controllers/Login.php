<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class Login extends MY_Controller {
        public $data;
        protected $phpass;

        function __construct(){
            parent::__construct();

            $this->load->library(array('form_validation', 'PasswordHash'));
            $this->passwordhash->init(8, false);
            $this->load->model('User_model');
            //$this->login->init($this->passwordhash);
        }

        public function login(){
            $data['title'] = TITLE.' | Login';

            //$this->login->($this->session->userdata('user')['id']);
            print_r($this->session->userdata('access_token'));

            if($this->LoggedIn()){
                if(!$this->check_session($this->session->userdata('access_token')))
                    $this->logout();
                else
                    redirect(base_url('main'));
            }

            $this->form_validation->set_rules('email', 'Email', 'required|trim');
            $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');

            if($this->form_validation->run()){
                $email = $this->input->post('email');
                $password = $this->input->post('password');

                if($user = $this->User_model->fetch(array('email' => $email))){
                    //print_r($user);
                    if($this->checkPassword($password, $user['password'])){
                        session_regenerate_id();
                        $this->createSession($user, session_id(), $this->input->post('keep_login'));
                        redirect(base_url('main'));
                    }else
                        $this->data['login_error'] = 'Username or password incorrect.';
                }else
                    $this->data['login_error'] = 'Username or password incorrect.';
            }

            $this->load->view('common/header', $data);
            $this->load->view('home', $data);
            $this->load->view('common/footer');
        }

        protected function createSession($userdata, $token = null, $keep_login = "off"){
            $this->session->set_userdata(array(
                'logged_in' => TRUE,
                'user' => $userdata,
                'access_token' => $token
            ));
        

            if($keep_login == "on"){
                $userdata['logged_in'] = TRUE;
                $userdata['access_token'] = $token;
                setcookie('user_login', serialize($userdata), (time() + (86400 * 3)), '/');
            }

            if(!empty($token)){
                $insert_token = array('access_token' => $token);
                $this->User_model->update($insert_token, array('id' => $userdata['id']));
            }
            
        }

        public function check_session(){
            $token = ($this->session->userdata('access_token') !== null) ? $this->session->userdata('access_token') : unsrialize($_COOKIE['user_login'])['access_token'];

            $query = $this->User_model->fetch(array('access_token' => $token));
            if(!$query && $query->token != $token){
                $this->logout();
            }
            return true;
        }

        // Método responsável por verificar se a password submetida coincide com a na base de dados
        protected function checkPassword($password, $hash){
            return $this->passwordhash->CheckPassword($password, $hash);
        }

        public function logout(){
            session_destroy();
            setcookie('user_login', '', time() - 3600, '/');
            $this->data['login_success'] = 'Logout efetuado com sucesso';
            redirect(base_url());     
        }
    }
?>