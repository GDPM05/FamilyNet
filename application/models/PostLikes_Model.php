<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PostLikes_Model extends MY_Model {
    private $table = 'post_like';

    function __construct(){
        parent::__construct($this->table);
    }

    public function check_user($id_user = null, $id_post = null){

        $this->db->where([
            'id_user' => $id_user,
            'id_post' => $id_post
        ]);

        $like = $this->db->get($this->table)->result_array();

        return (!empty($like));
    }

}
