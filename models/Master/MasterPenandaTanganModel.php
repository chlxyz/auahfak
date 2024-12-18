<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MasterPenandaTanganModel extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('date');
    }

    public function get_data($limit, $offset, $search = '') {
        $sql = "
            SELECT * 
            FROM Horizon_Master_PenandaTangan
        ";
    
        // Add search condition if the search parameter is provided
        if (!empty($search)) {
            $sql .= "
                WHERE nama LIKE ?
            ";
        }
    
        // Add ordering and pagination logic
        $sql .= "
            ORDER BY nama
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
        return $this->db->from('Horizon_Master_PenandaTangan')->count_all_results();
    }

    public function get_by_nik($NIK) {
        return $this->db->get_where('Horizon_Master_PenandaTangan', ['NIK' => $NIK])->row_array();
    }
    

    public function get_data_approver($nik) {
        $this->db->select('Nama');
        $this->db->from('ms_niktelp');
        $this->db->where('NIK', $nik);
        $query = $this->db->get();
        return $query->row();
    }
    
    public function insert_data(
        $NIK,
        $nama,
        $gambar_ttd,
        $gambar_paraf
    ) {
        $data = [
            'NIK' => $NIK,
            'nama' => $nama,
            'gambar_ttd' => $gambar_ttd,
            'gambar_paraf' => $gambar_paraf
        ];
        $this->db->insert('Horizon_Master_PenandaTangan', $data);
    }

    public function update_status($id, $status) {
        $this->db->set('is_active', $status);
        $this->db->where('NIK', $id);
        $this->db->update('Horizon_Master_PenandaTangan');
    }
    
    
    public function update_data(
        $NIK,
        $nama,
        $gambar_ttd,
        $gambar_paraf
    ) {
        $data = array(
            'NIK' => $NIK,
            'nama' => $nama,
            'gambar_ttd' => $gambar_ttd,
            'gambar_paraf' => $gambar_paraf
        );
        $this->db->where('NIK', $NIK);
        $this->db->update('Horizon_Master_PenandaTangan', $data);
    }

    public function set_inactive_active($NIK, $is_active) {
        $data = array(
            'is_active' => $is_active
        );
        $this->db->where('NIK', $NIK);
        $this->db->update('Horizon_Master_PenandaTangan', $data);
    }
}
?>
