<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Post_Model extends MY_Model {
    private $table = 'post';

    function __construct(){
        parent::__construct($this->table);
    }

    public function fetch_by_family($user_id, $id_friends = []) {
        if(empty($id_friends))
            return [];

        $this->db->where('privacy_level', 3);
        $this->db->group_start();
        foreach ($id_friends as $friend_id) {
            $this->db->or_where('id_sender', $friend_id['id_user']);
        }
        $this->db->group_end();

        return $this->db->get($this->table)->result_array();
    }
}
