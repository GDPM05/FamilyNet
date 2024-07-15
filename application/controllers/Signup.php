<?php
    defined('BASEPATH') OR exit('No direct script access allowed');


    /**
     * 
     * Para fazer o sign up, existem algumas etapas importantes:
     *  - Receber tratar e enviar os dados do utilizador para a base de dados
     *  - Receber e redimensionar a foto de perfil (caso submetida)
     *  - Gerar e enviar um código de verificação por email para o utilizador 
     */

    class Signup extends MY_Controller {
        
        private $general_data = array();

        function __construct(){
            parent::__construct();

            $this->config->load('email');
            $this->load->library(array('form_validation', 'upload', 'image_lib', 'session', 'email'));
            $this->load->library('Captcha');
            $this->load->library('PasswordHash');
            $this->passwordhash->init(8, false);
            $this->load->model('User_model');
            $this->load->model('Media_model');
            $this->load->model('Usercat_model');
            $this->load->model('Gender_model');
            $this->load->model('ParentalRole_model');
            //$this->load->model('signup_model', 'signup');
        }

        public function index() {
            $this->general_data['pfp_path'] = base_url('/media/profile_pictures/default/default.jpg');

            $data = array(
                'title' => TITLE.' | Sign Up',
                'genders' => $this->Gender_model->fetch_all(),
                'p_roles' => $this->ParentalRole_model->fetch_all()
            );

            // Regras que os valores do formulário tem de seguir para ficar tudo padronizado
            $this->form_validation->set_rules('name_in', 'Name', 'required|min_length[5]');
            $this->form_validation->set_rules('username_in', 'Username', 'required|trim');
            $this->form_validation->set_rules('phone_in', 'Phone Number', 'numeric|max_length[15]');
            $this->form_validation->set_rules('birthday_in', 'Birthday', 'required');
            $this->form_validation->set_rules('gender_in', 'Gender', 'required');
            $this->form_validation->set_rules('p_role', 'Parental Role', 'required');
            $this->form_validation->set_rules('email_in', 'Email', 'required|min_length['.EMAIL_LENGTH.']|trim');
            $this->form_validation->set_rules('password_in', 'Password', 'required|min_length['.PW_LENGTH.']|callback_password_check|trim');
            $this->form_validation->set_rules('password_repeat', 'Repeat Password', 'required|min_length['.PW_LENGTH.']|callback_password_check|trim|matches[password_in]');    

            if($this->form_validation->run() == FALSE)
			    $data['formErrors'] = validation_errors(); 
		    else{
                $form_data = $this->input->post();

                // Verificação do CAPTCHA
                if(!$this->captcha->verify($this->input->post('g-recaptcha-response'))['success'])
                    $data['formErrors'] = 'Uncessfull CAPTCHA.';


                $config['upload_path'] = './media/profile_pictures';
                $config['allowed_types'] = 'jpg|png|jpeg';
                $config['max_size'] = '51000';
                $config['encrypt_name'] = TRUE;

                $this->upload->initialize($config);
                if(!$this->upload->do_upload('pfp_in')){
                    $this->general_data['pfp_path'] = base_url('/media/').'profile_pictures/default/default.jpg';
                }else{
                    $data['data_upload'] = $this->upload->data();

                    $resize = $this->resize($data);
                    if(!$resize){
                        $data['formErrors'] .= "<br/> Não foi possível redimensionar a imagem. <br/>";
					    $data['formErrors'] .= $resize['message'];
                    }else
                        $this->general_data['pfp_path'] = base_url('/media/')."profile_pictures/".$data['data_upload']['raw_name'].$data['data_upload']['file_ext'];
                    
                }

                /* Lógica do SignUp */
                /* Verifica se já existe um utilizador com o mesmo email, número de telefone ou username */
                if(!$this->User_model->check_if_exists(['email' => $form_data['email_in']]) || !$this->User_model->check_if_exists(['phone' => $form_data['phone_in']]) || !$this->User_model->check_if_exists(['user' => $form_data['username_in']])){
                    /* Vai buscar o id corresponde à categoria User */
                    $user_cat = $this->Usercat_model->fetch(['permissions' => 'user']);
                    $form_data['user_cat'] = $user_cat;

                    /* Coloca a foto de perfil submetida na tabela media */
                    $insert_media = $this->Media_model->insert(['path' => $this->general_data['pfp_path'], 'alt' => $form_data['username_in']]);
                    if($this->Media_model->error)
                        $data['formErrors'] = $this->Media_model->form_msg;
                    else{
                        $form_data['media_id'] = $insert_media;
                        /* Insere os dados na base de dados para criar um novo user guardando também o id deste novo utilizador */
                        //print_r($form_data);
                        $new_user = $this->User_model->insert([
                            'username' => $form_data['name_in'],
                            'user' => $form_data['username_in'],
                            'birthday' => $form_data['birthday_in'],
                            'category' => $form_data['user_cat']['id_cat'],
                            'email' => $form_data['email_in'],
                            'phone' => $form_data['phone_in'],
                            'password' => $this->passwordhash->HashPassword($form_data['password_in']),
                            'access_token' => md5(time()),
                            'acc_creation' => date("Y-m-d H:i:s"),
                            'pfp' => $form_data['media_id'],
                            'gender' => $form_data['gender_in'],
                            'p_role' => $form_data['p_role'],
                            'active' => 0
                        ]);

                        if($this->User_model->error)
                            $data['formErrors'] = $this->User_model->form_msg;

                        $this->general_data['new_user'] = $new_user;
                    }
                }else{
                    $data['formErrors'] = "Já existe um utilizador com essas informações!";
                }                


                if(empty($data['formErrors'])){
                    $code = $this->generate_code();
                    // Rotina que envia o código para uma variavel de sessão encriptada e para o email do utilizador
                    $this->session->set_userdata(md5('c0d4'), md5(sha1($code)));
                    $this->session->set_userdata(md5('user_id'), $new_user);
                    setcookie(md5('expire'), 1, time() + (60 * 5), '/');

                    if(!$this->send_code($form_data['email_in'], 'Aqui está o código para prosseguir com o login. Tem 5 minutos para introduzir o código. '.$code)){
                        $data['formErrors'] = 'Não foi possível enviar o código, tente outra vez mais tarde.';
                        $this->signup_fail();
                    }
                    header('Location: '.base_url('signup/verify'));
                }
                
		    }

            $this->load->view('common/header', $data);
            $this->load->view('signup', $data);
            $this->load->view('common/footer');   
        }

        public function verify(){
            $data = array(
                'title' => TITLE.' | Signup'
            );

            $code = "";

            //print_r($this->session->userdata(md5('n_tries')));

            if(($this->session->userdata(md5('n_tries')) >= 3) || (!isset($_COOKIE[md5('expire')]))){
                $this->signup_fail();
                return;
            }

            $this->form_validation->set_rules('input1', 'Code', 'required|min_length[1]|max_length[1]');
            $this->form_validation->set_rules('input2', 'Code', 'required|min_length[1]|max_length[1]');
            $this->form_validation->set_rules('input3', 'Code', 'required|min_length[1]|max_length[1]');
            $this->form_validation->set_rules('input4', 'Code', 'required|min_length[1]|max_length[1]');
            $this->form_validation->set_rules('input5', 'Code', 'required|min_length[1]|max_length[1]');
            $this->form_validation->set_rules('input6', 'Code', 'required|min_length[1]|max_length[1]');

            //rint_r()

            if($this->form_validation->run() == FALSE)
                $data['formErrors'] = validation_errors(); 
            else{
                foreach($this->input->post() as $key => $value){
                    $code .= ($value != "Submit") ? $value : null;

                    if(empty($value))
                        $data['formErrors'] = "Internal error.";
                }                

                print_r(md5(sha1($code)));
                print_r($this->session->userdata(md5('c0d4')));

                if(md5(sha1($code)) == $_COOKIE[md5('c0d4')]){
                    $data['success'] = TRUE;
                }else{
                    $this->session->set_userdata(md5('n_tries'), ($this->session->userdata(md5('n_tries'))+1));
                }
            }

            // print_r($_COOKIE[md5('id')]);

            if(isset($data['success']) && $data['success']){
                $this->session->unset_userdata(array(md5('n_tries'), md5('c0d4')));
                $this->User_model->update(['active' => 1], ['id' => $_COOKIE[md5('id')]]);

                $user = $this->User_model->fetch(['id' => $_COOKIE[md5('id')]]);
                print_r($user);
                if(empty($user['password'])){
                    header('location: '.base_url('reset_password/'));
                }
                return;
                unset($_COOKIE[md5('expire')]);
                unset($_COOKIE[md5('c0d4')]);
                unset($_COOKIE[md5('id')]);
                header('location: '.base_url());
            }

            $this->load->view('common/header', $data);
            $this->load->view('verify_code', $data);
            $this->load->view('common/footer');   
        }

        public function signup_fail(){
            session_destroy(); 

            if(isset($this->general_data['pfp_path']))
                $this->delete_pfp($this->general_data['pfp_path']);

            if(isset($this->general_data['new_user']))
                $this->User_model->delete(['id' => $this->general_data['new_user']['id']]);

                redirect(base_url());
        }

        private function generate_code(){
            $code = "";
            /**
             * Tem de gerar um código completamente aleatório e envia-lo por email, em também de o encriptar e guardar numa variável de sessão e depois basta apenas
             * comparar ambos os códigos para perceber se 
             */

            $char_arr = array("@$#&%", "1234567890", "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ");

            // Aleatoriza um valor boolean para "escolher" a forma de encriptar
            if(rand(0, 1)){
                while(mb_strlen($code) < 6){
                    $index_arr = rand(0, count($char_arr)-1);
                    $index_str = rand(0, mb_strlen($char_arr[$index_arr])-1);
                    $char = $char_arr[$index_arr][$index_str];
                    if(!strpos($code, $char))
                        $code .= $char;
                }
            }else{
                // Gera um código com base no método uniqid, cujo gera um identificador único baseado no tempo atual em microssegundos 
                $code_start = rand(1,5);
                $uniqid = uniqid();

                $code = substr($uniqid, $code_start, 6);
            }

            return $code;
        }


        public function password_check($password){
            // Regex responsável por verificar se a palavra passe contém os caracteres necessários para uma palavra passe segura
            $regex = '/^(?=.*\d)(?=.*[A-Z])(?=.*[a-z])(?=.*[^\w\d\s:])([^\s]){8,16}$/';
            if(preg_match($regex, $password))
                return true;
            else{
                $this->form_validation->set_message('password_check', 'The password must include atleast one upper case letter, one number and one special character [&%$#]');
                return false;
            }
                
        }   
    
        private function resize($data){

            $config['source_image'] = $data['data_upload']['full_path'];
            $config['width'] = 100;
            $config['height'] = 100;
            $config['image_library'] = 'gd2';
            $config['create_thumb'] = FALSE;
            $config['maintain_ratio'] = FALSE;

            $this->image_lib->initialize($config);

            if(!$this->image_lib->resize()){
                $data['status'] = false;
                $data['message'] = $this->image_lib->display_errors();
            }else{
                $data['status'] = true;
                $data['message'] = null;
            }

            $this->image_lib->clear();
            return $data;
	    }

        private function delete_pfp($path){
            if(file_exists($path))
                unlink($path);
        }

        private function send_code($email, $message){
            try{
                $this->email->from($this->config->item('smtp_user'), 'FamilyNet');
                $this->email->to($email);
                $this->email->subject('Código de Verificação');
                $this->email->message($message);
                $this->email->send();
            }catch(Exception $e){
                return $e;
            }

            return true;
        }

        public function complete(){
            $data = array(
                'title' => TITLE.' | Sign Up',
                'genders' => $this->Gender_model->fetch_all(),
                'p_roles' => $this->ParentalRole_model->fetch_all(),
                'email' => $this->session->userdata(md5('current_signup_data'))
            );

            //print_r($data);

            $this->load->view('common/header', $data);
            $this->load->view('complete_signup', $data);
            $this->load->view('common/footer');  
        }

        public function complete_validation(){
            $email = $this->input->post('email');
            $data = [];
            // Validation rules for the "complete_signup" page
            $this->form_validation->set_rules('name_in', 'Name', 'required|min_length[5]');
            $this->form_validation->set_rules('username_in', 'Username', 'required|trim');
            $this->form_validation->set_rules('phone_in', 'Phone Number', 'numeric|max_length[15]');
            $this->form_validation->set_rules('birthday_in', 'Birthday', 'required');
            $this->form_validation->set_rules('gender_in', 'Gender', 'required');
            $this->form_validation->set_rules('p_role', 'Parental Role', 'required');

            if ($this->form_validation->run() == FALSE) {
                $data['formErrors'] = validation_errors();
            } else {
                $form_data = $this->input->post();

                // Update the existing user based on the email
                $existing_user = $this->User_model->fetch(['email' => $form_data['email']]);
                if ($existing_user) {
                    // Update user data
                    $update_data = [
                        'username' => $form_data['name_in'],
                        'user' => $form_data['username_in'],
                        'birthday' => $form_data['birthday_in'],
                        'phone' => $form_data['phone_in'],
                        'gender' => $form_data['gender_in'],
                        'p_role' => $form_data['p_role'],
                    ];

                    $this->User_model->update($update_data, ['id' => $existing_user['id']]);
                    $user = $this->User_model->fetch(['id' => $existing_user['id']], ['id', 'email', 'username', 'user', 'phone', 'gender']);
                    if ($this->User_model->error) {
                        $data['formErrors'] = $this->User_model->form_msg;
                    } else {
                        $userdata = ['id' => $user['id'],'email' => $user['email'], 'username' => $user['username'], 'user' => $user['user'], 'phone' => $user['phone'], 'gender' => $user['gender']];
                        $token = session_id();
                        $this->session->set_userdata(array(
                            'logged_in' => TRUE,
                            'user' => $userdata,
                            'access_token' => $token
                        ));
                        redirect(base_url('main'));
                    }
                } else {
                    $data['formErrors'] = 'User not found in the database.';
                }
            }

            // Load views
            $this->load->view('common/header', $data);
            $this->load->view('complete_signup', $data);  // Change the view file name if needed
            $this->load->view('common/footer');

        }

        public function activate_account_external(){
            $user_id = $_COOKIE[md5('id')];
            // $this->session->userdata(md5('user_id'));
            // print_r($this->session->userdata);
            // var_dump($user_id);
            $user = $this->User_model->fetch(['id' => $user_id], 'id, username, email');

            $code = $this->generate_code();

            setcookie(md5('c0d4'), md5(sha1($code)), time() + (60 * 5), '/');
            $this->session->set_userdata(md5('user_id'), $user_id);
            setcookie(md5('expire'), 1, time() + (60 * 5), '/');

            if(!$this->send_code($user['email'], 'Olá, '.$user['username'].', aqui está código para ativares a tua conta. '.$code)){
                $data['formErrors'] = 'Não foi possível enviar o código, tente outra vez mais tarde.';
                redirect(base_url());
            }

            redirect(base_url('signup/verify'));
        }
    }
?>