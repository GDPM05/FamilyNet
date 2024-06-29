<?php
    defined('BASEPATH') OR exit('No direct script access allowed');

    class Login extends MY_Controller {
        public $data;
        protected $phpass;

        function __construct(){
            parent::__construct();

            $this->load->library(array('form_validation', 'PasswordHash'));
            $this->config->load('email');
            $this->passwordhash->init(8, false);
            $this->load->model('User_model');
            $this->load->model('Media_model');
            $this->load->library('Mailer');
            $this->load->model('ResetPassword_Model');
            $this->data['title'] = TITLE;
            //$this->login->init($this->passwordhash);
        }

        public function login(){
            $this->data['title'] = TITLE.' | Login';

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
                $user = $this->User_model->fetch(array('email' => $email), 'user, id, category,username, email, pfp, p_role, gender, phone, birthday, access_token, password, active');
                if($user){
                    //print_r($user);
                    if($user['active'] == 1){
                        if($this->checkPassword($password, $user['password'])){
                            session_regenerate_id();
                            unset($user['password']);
                            $this->createSession($user, session_id(), $this->input->post('keep_login'));
                            redirect(base_url('main'));
                        }else
                            $this->data['login_error'] = 'Utilizador ou palavra passe incorretos.';
                    }else{
                        $this->session->set_userdata('user_id', $user['id']);
                        setcookie(md5('id'), $user['id'], time() + (30 * 60), '/');
                        $this->data['login_error'] = 'A sua conta ainda não está ativa. Para ativar, aceda a <a href="'.base_url('/activate_account').'">Ativar conta</a>.';
                    }
                }else
                    $this->data['login_error'] = 'Utilizador ou palavra passe incorretos.';
            }

            $this->load->view('common/header', $this->data);
            $this->load->view('home', $this->data);
            $this->load->view('common/footer');
        }

        protected function createSession($userdata, $token = null, $keep_login = "off"){
            
            $userdata['pfp'] = $this->Media_model->fetch(['id' => $userdata['pfp']]);

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

        public function reset_password(){
            $char_list = 'abcderfghijklmnopqrstuvwxyz1234567890';
            $url_code = '';
            for($i = 0; $i < 20; $i++){
                $url_code .= $char_list[random_int(0, strlen($char_list)-1)];
            }

            $url = base_url('reset_password/').$url_code;
            
            redirect($url);
        }

        private function send_email($email, $subject, $message){
            try{
                $this->email->from($this->config->item('smtp_user'), 'FamilyNet');
                $this->email->to($email);
                $this->email->subject($subject);
                $this->email->message($message);
                $this->email->send();
            }catch(Exception $e){
                return $e;
            }

            return true;
        }

        public function verify_reset_password(){
            $url_code = $this->uri->segment(2);

            $email = $this->input->post();

            $email_query = (isset($email['email'])) ? $this->User_model->check_if_exists(['email' => $email['email']]) : true;

            if(!$email_query){
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Insira um email válido!']);
                return false;
            }

            $char_list = 'abcderfghijklmnopqrstuvwxyz1234567890';
            $url_code = '';
            $code = '';
            for($i = 0; $i < 20; $i++){
                $url_code .= $char_list[random_int(0, strlen($char_list)-1)];
            }

            $url = base_url('password_reset/').$url_code;

            if(!empty($email)){
                $subject = 'Reposição de Password';
                $message = 'Aceda ao link a baixo para repor a sua palavra passe. <a href="'.$url.'">Repor Password</a>';
                $this->send_email($email, $subject, $message);

                $this->ResetPassword_Model->insert([
                    'email' => $email['email'],
                    'url_code' => $url_code
                ]);

                setcookie(md5('email'), md5($email['email']), time() + (60 * 10), '/');

                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
                return true;
            }

            $this->load->view('common/header', $this->data);
            $this->load->view('verify_reset_pass', $this->data);
            $this->load->view('common/footer');
        }

        public function password_reset(){

            $code = $this->uri->segment(2);
            $email = $_COOKIE[md5('email')];

            $request = $this->ResetPassword_Model->fetch(['url_code' => $code]);

            if(md5($request['email']) !== $email){
                redirect(base_url('error/password_reset'));
                return false;
            }

            $data = $this->input->post();
            
            if(!empty($data)){
                $this->User_model->update(
                    ['password' => $this->passwordhash->HashPassword($data['password'])],
                    ['email' => $request['email']]
                );

                $this->send_email($request['email'], 'A sua palavra passe foi alterada.', 'A sua palavra passe foi alterada com sucesso!');

                $this->ResetPassword_Model->delete(['email' => $email]);

                header('Content-Type: application/json');
                echo json_encode(['success' => true]);
                return true;
            }

            $this->load->view('common/header', $this->data);
            $this->load->view('password_redefinition', $this->data);
            $this->load->view('common/footer');     
        }
    }
?>