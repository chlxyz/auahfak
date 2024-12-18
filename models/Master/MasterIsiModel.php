<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MasterIsiModel extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('date');
    }

    public function get_data($limit, $offset, $search = '') {
        $sql = "
            SELECT * 
            FROM Horizon_Master_Isi
        ";
    
        // Add search condition if the search parameter is provided
        if (!empty($search)) {
            $sql .= "
                WHERE Judul LIKE ?
            ";
        }
    
        // Add ordering and pagination logic
        $sql .= "
            ORDER BY Judul
            OFFSET ? ROWS FETCH NEXT ? ROWS ONLY
        ";
    
        // Prepare parameters for the query
        $params = [];
        if (!empty($search)) {
            $searchParam = "%$search%";
            $params[] = $searchParam;
        }
        $params = array_merge($params, [$offset, $limit]);
    
        // Execute the query with the prepared parameters
        $query = $this->db->query($sql, $params);
    
        // Return the result as an array of objects
        return $query->result();
    }

        public function count_data() {
        return $this->db->from('Horizon_Master_Isi')->count_all_results();
    }

    public function insert_data($isi, $lampiran1, $lampiran2, $judul) {
        $data = [
            'isi' => $isi,
            'lampiran1' => $lampiran1,
            'lampiran2' => $lampiran2,
            'Judul' => $judul
        ];
        
        if ($this->db->insert('Horizon_Master_Isi', $data)) {
            return true; // Insert successful
        } else {
            return false; // Insert failed
        }
    }    

    public function update_status($id, $status) {
        $this->db->set('is_active', $status);
        $this->db->where('id_isi', $id);
        $this->db->update('Horizon_Master_Isi');
    }
    

    public function update_data($id_isi, $isi, $lampiran1, $lampiran2, $judul) {
        $data = [
            'isi' => $isi,
            'lampiran1' => $lampiran1,
            'lampiran2' => $lampiran2,
            'Judul' => $judul
        ];
    
        $this->db->where('id_isi', $id_isi);
        $this->db->update('Horizon_Master_Isi', $data);
    
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function get_data_by_id($id_isi) {
        $this->db->where('id_isi', $id_isi);
        $query = $this->db->get('Horizon_Master_Isi');
        return $query->row_array(); // Return as an associative array
    }
    
    
    public function set_inactive_active($id_isi, $is_active) {
        $data = array(
            'is_active' => $is_active
        );
        $this->db->where('id_isi', $id_isi);
        $this->db->update('Horizon_Master_isi', $data);
    }

    public function get_details($id_isi) {
        $this->load->database();
    
        $this->SQLSRV = $this->load->database('TESTING', TRUE);

        //     // $this->db->select('id_isi');
        //     $this->db->select('isi');
        //     // $this->db->select('lampiran1');
        //     // $this->db->select('lampiran2');
        //     // $this->db->select('Judul');
        // $this->db->from('Horizon_Master_Isi');
        // $this->db->where('id_isi', $id_isi);

        $query = $this->SQLSRV->query("SELECT isi, lampiran1, lampiran2, Judul FROM Horizon_Master_Isi WHERE id_isi = '$id_isi'");

        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return null;
        }
    }
}
?>
