<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class NotificationHandler{

    public $ci;

    public const FRIEND_INVITE = 1;
    public const INFO = 2;

    public function __construct(){
        $this->ci =& get_instance();
        $this->ci->load->model('Notification_model');
    }

    public function notify($type_id = null, $id_receiver = null, $id_sender = null, $message = ''){
        $insert_data = [
            'type_id' => $type_id,
            'receiver_id' => $id_receiver,
            'sender_id' => $id_sender,
            'message_text' => $message,
            'sent_date' => date('Y-m-d H:i:s')
        ];

        $id = $this->ci->Notification_model->insert($insert_data);

        if($this->ci->Notification_model->error){
            // Tratar o erro
            //print_r($this->ci->Notification_model->error_msg);
            return false;
        }

        return $id;
    }

    public function denotify($notification_id = null){
        if(empty($notification_id)){
            // tratar o erro
            return false;
        }

        $this->ci->Notification_model->delete(['id' => $notification_id]);

        if($this->ci->Notification_model->error){
            // tratar o erro
            return false;
        }

        return true;
    }

}