<?php
    defined('BASEPATH') OR exit('No direct script access allowed');


    class Mailer {

        private $from;
        private $CI;

        public $error = false;
        public $error_msg = ''; 

        public function __construct($from = null) {
            $this->CI =& get_instance();
            $this->CI->load->config('email');
            $this->CI->load->library('email');
            $this->from = ($from == null) ? $this->CI->config->item('smtp_user') : $from;

            var_dump($this->from);
        }

        
        public function send_email($to = null, $subject = null, $msg = null){

            if(empty($to) || empty($subject) || empty($msg)){
                $this->error = true;
                $this->error_msg = "Informações de email vazias.";
            }

            try{
                $this->CI->email->from($this->from);
                $this->CI->email->to($to);
                $this->CI->email->subject($subject);
                $this->CI->email->message($msg);
                $this->CI->email->send();
            }catch(Exception $e){
                return $e;
            }

        }

    }
    /*
     private function send_code($email, $code){
            try{
                $this->email->from($this->config->item('smtp_user'), 'FamilyNet');
                $this->email->to($email);
                $this->email->subject('Código de Verificação');
                $this->email->message('Aqui está o código para prosseguir com o login. Tem 5 minutos para introduzir o código. '.$code);
                $this->email->send();
            }catch(Exception $e){
                return $e;
            }


            return true;
        }
     */