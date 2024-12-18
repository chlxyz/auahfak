<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class Formstatus_model extends CI_Model {


    public function get_data_by_session($session_id) {
        // Query database berdasarkan session_id
        $db = $this->load->database('default', TRUE);
        $sql="select * from ms_niktelp where NIK=?";
        $query = $db->query($sql,array($session_id)); // Ganti 'users' dengan nama tabel yang sesuai

        // Mengembalikan hasil query sebagai array atau null jika tidak ditemukan
        return $query->row_array();
    }
 
    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('date');
    }
    

    // public function get_history() {
    //     $this->db->select('ht.*, mpa.NAME1');
    //     $this->db->from('Horizon_Transaction ht');
    //     $this->db->join('ms_PersArea mpa', 'ht.PersArea = mpa.PERSA', 'left');

    //     $query = $this->db->get();
    //     return $query->result(); // Return the result as an array of objects
    // }

    public function get_history($letter_type = null) {
        $this->db->select('ht.NIK, ht.*, mpa.NAME1, hmp1.nama as approval1_name, hmp2.nama as approval2_name');
    
        if ($letter_type) {
            $this->db->where('jenis_surat', $letter_type);
        }
        $this->db->from('Horizon_Transaction ht');
        $this->db->join('ms_PersArea mpa', 'ht.PersArea = mpa.PERSA', 'left');
        $this->db->join('Horizon_Master_PenandaTangan hmp1', 'ht.nik_1st_approval = hmp1.NIK', 'left');
        $this->db->join('Horizon_Master_PenandaTangan hmp2', 'ht.nik_2nd_approval = hmp2.NIK', 'left');
    
        $query = $this->db->get();
    
        return $query->result(); // Mengembalikan hasil sebagai array objek
    }
    
    public function get_viewer_access() {
        $this->db->select('*');
        $this->db->from('Horizon_Master_PT');
        $query = $this->db->get();
        return $query->result();
    }

 
}
?>