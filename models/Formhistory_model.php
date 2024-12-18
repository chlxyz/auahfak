<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class Formhistory_model extends CI_Model {


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


    public function get_history($session_id) {
        $this->db->select('ht.NIK, ht.*, mpa.NAME1, hmp1.nama as approval1_name, hmp2.nama as approval2_name');
       
        if ($session_id) {
            $this->db->where('ht.NIK', $session_id);
        }
       
        $this->db->from('Horizon_Transaction ht');
        $this->db->join('ms_PersArea mpa', 'ht.PersArea = mpa.PERSA', 'left');
        $this->db->join('Horizon_Master_PenandaTangan hmp1', 'ht.nik_1st_approval = hmp1.NIK', 'left');
        $this->db->join('Horizon_Master_PenandaTangan hmp2', 'ht.nik_2nd_approval = hmp2.NIK', 'left');
       
        $query = $this->db->get();
       
        return $query->result(); // Return the result as an array of objects
    }
    

    // public function get_history($session_id) {
 
    //     if ($session_id) {
    //         $this->db->where('NIK', $session_id);
    //     }
 
    //     $this->db->select('ht.*, mpa.NAME1');
    //     $this->db->from('Horizon_Transaction ht');
    //     $this->db->join('ms_PersArea mpa', 'ht.PersArea = mpa.PERSA', 'left');
 
    //     $query = $this->db->get();
 
    //     return $query->result(); // Mengembalikan hasil sebagai array objek
 
    // }
 
    public function get_isi($idIsi) {
        $this->load->database();
    
        $this->SQLSRV = $this->load->database('TESTING', TRUE);
    
        $query = $this->SQLSRV->query("SELECT isi FROM Horizon_Master_Isi WHERE id_isi = ?", [$idIsi]);
    
        if ($query->num_rows() > 0) {
            return $query->row()->isi;
        } else {
            return null;
        }
    }

    public function get_viewer_access() {
        $this->db->select('*');
        $this->db->from('Horizon_Master_PT');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_surat_text($jenisSurat) {
        $this->db->select('id_surat');
        $this->db->from('Horizon_Master_Surat');
        $this->db->where('surat_text', $jenisSurat);
        $query = $this->db->get();

        return $query->row()->id_surat; // Return id_surat directly or null if no result
    }
}
?>