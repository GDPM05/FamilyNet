<?php defined('BASEPATH') OR exit('No direct script access allowed');

require 'vendor/autoload.php';

class Auth extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('url');
    }

    public function google_auth(){
        $client = new Google_Client();
        $client->setClientId('908931447639-ocnea685o5j4rj3711vjlhepmeh4quf6.apps.googleusercontent.com');
        $client->setClientSecret('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');
        $client->setRedirectUri(base_url('callback'));
        $client->addScope(Google_Service_Oauth2::USERINFO_EMAIL);

        $authUrl = $client->createAuthUrl();
        redirect($authUrl);
    }

    public function callback()
    {
        $code = $this->input->get('code');

        $client = new Google_Client();
        $client->setClientId('908931447639-ocnea685o5j4rj3711vjlhepmeh4quf6.apps.googleusercontent.com');
        $client->setClientSecret('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');
        $client->setRedirectUri(base_url('callback'));
        $client->addScope(Google_Service_Oauth2::USERINFO_EMAIL);

        if (!$code) {
            echo 'Código de autorização ausente.';
            return;
        }

        try {
            $client->authenticate($code);
            $service = new Google_Service_Oauth2($client);
            $userDetails = $service->userinfo->get();

            // Manipule as informações do usuário conforme necessário
            $this->handleAuthenticatedUser($userDetails);


        } catch (\Exception $e) {
            echo 'Erro ao obter o token de acesso: ' . $e->getMessage();
            return;
        }
    }
    
    private function handleAuthenticatedUser($userDetails) {
        // Manipule as informações do usuário e armazene na sessão conforme necessário
        print_r($userDetails);
        if($user = $this->User_model->fetch(['email' => $userDetails['email']])){
            session_regenerate_id();
            $this->createSession($user, session_id());
            redirect(base_url('main'));
        }else{
            $pfp = $this->Media_model->insert(['alt' => md5(time()), 'path' => $userDetails['picture']]);

            $insert_data = [
                'email' => $userDetails['email'],
                'pfp' => $pfp
            ];

            $this->User_model->insert($insert_data);
            $this->session->set_userdata(md5('current_signup_data'), ['email' => $userDetails['email']]);
            redirect(base_url('complete_signup'));
        }
        $this->session->set_userdata('user_details', $userDetails); 
    }

    protected function createSession($userdata, $token = null){
        $this->session->set_userdata(array(
            'logged_in' => TRUE,
            'user' => $userdata,
            'access_token' => $token
        ));

        if(!empty($token)){
            $insert_token = array('access_token' => $token);
            $this->User_model->update($insert_token, array('id' => $userdata['id']));
        }
        
    }

    public function check_session(){
        $token = $this->session->userdata('access_token');

        $query = $this->User_model->fetch(array('access_token' => $token));
        if(!$query && $query->token != $token){
            return false;
        }
        return true;
    }

}
