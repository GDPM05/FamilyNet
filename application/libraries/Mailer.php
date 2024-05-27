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
    }

    public function send_email($to = null, $subject = null, $msg = null){
        if(empty($to) || empty($subject) || empty($msg)){
            $this->error = true;
            $this->error_msg = "Informações de email vazias.";
            return false;
        }

        try{
            $this->CI->email->from($this->from);
            $this->CI->email->to($to);
            $this->CI->email->subject($subject);
            $this->CI->email->message($msg);
            
            if (!$this->CI->email->send()) {
                $this->error = true;
                $this->error_msg = $this->CI->email->print_debugger();
                return false;
            }

            return true;
        }catch(Exception $e){
            $this->error = true;
            $this->error_msg = $e->getMessage();
            return false;
        }
    }
}
?>
