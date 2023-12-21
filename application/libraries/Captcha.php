<?php
    defined('BASEPATH') OR exit('No direct script access allowed');


    /**
     * Esta classe será responsável pelo controlo de CAPTCHAS no site todo.
     * 
     * No construtor, recebe 1 parametro, se vai ser passado ou não uma configuração padrão.
     *  
     * Caso algo seja passado no default_config, que no caso tem de ser um array de configurações, essas passam a ser as configuração usadas no CURL, caso nada seja passado
     * serão usadas as configurações padrão.
     * 
     */

    class Captcha{

        private $default_config;
        private $config;
        private $url = 'https://www.google.com/recaptcha/api/siteverify';
        private $key = '6LegHdEoAAAAALp3IgWN5bYWXw5TOxMB0OF-PrZf';
        private $curl;

        function __construct($default_config = array()){
            $this->default_config = $default_config;
        }

        private function curl_setup(){
            $this->curl = curl_init(); 
            if(empty($this->default_config)){
                curl_setopt($this->curl, CURLOPT_URL, $this->url);
                curl_setopt($this->curl, CURLOPT_POST, true);
                curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, false);
            }else{
                foreach($this->default_config as $config => $value){
                    curl_setopt($this->curl, $config, $value);
                }
            }
        }        

        public function verify($input){
            $data = array(
                'secret' => $this->key,
                'response' => $input
            );

            $this->curl_setup();

            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $data);

            $response = curl_exec($this->curl);

            $response_status = json_decode($response, true);

            print_r($response_status);

            $this->end_curl($this->curl);

            return $response_status;
        }

        private function end_curl(){
            curl_close($this->curl);
        }

    }

?>