<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Model_message extends CI_Model {

    var $TABLE_MESSAGE = "message";
    var $COLUMN_ID = "id";
    var $COLUMN_TIME = "time";
    var $COLUMN_TEXT = "text";

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();

        //load database 
        $this->load->database();
    }

    function getAllMovements()
    {
        $this->db->select($this->COLUMN_ID . " , " . $this->COLUMN_TIME . " , " . $this->COLUMN_TEXT);
        $this->db->from($this->TABLE_MESSAGE);
        $this->db->order_by($this->COLUMN_TIME, "desc");

        $query = $this->db->get();

        return $query->result();
    }

    function addMovement($message)
    {
        $data = array($this->COLUMN_TIME => date('Y-m-d H:i:s'), $this->COLUMN_TEXT => $message);
        $this->db->insert($this->TABLE_MESSAGE, $data);

        if ($this->db->affected_rows() > 0) {

            // Code here after successful insert
            $insert_id = $this->db->insert_id();

            $this->db->where($this->COLUMN_ID, $insert_id)
                    ->select($this->COLUMN_ID . " , " . $this->COLUMN_TIME. " , " . $this->COLUMN_TEXT)
                    ->from($this->TABLE_MESSAGE);

            $last_insert_query = $this->db->get();
            return $last_insert_query->result();
        } else {
            return null;
        }
    }

}
