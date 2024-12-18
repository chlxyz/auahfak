<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MasterBundlerModel extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('date');
    }

    // function untuk check apakah data sudah ada berdasarkan persarea dan id surat
    public function check_data_exists($persarea, $id_surat) {
        $this->db->select('PersArea_Text');
        $this->db->from('Horizon_Master_PT');
        $this->db->where('PersArea', $persarea);
        $this->db->where('id_surat', $id_surat);
        
        $query = $this->db->get();
    
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    // function untuk get data area
    public function get_data_area() {
        $this->db->select('*');
        $this->db->from('ms_PersArea');
        $query = $this->db->get();
        return $query->result();
    }

    // function untuk get data template
    public function get_data_template() {
        $this->db->select('*');
        $this->db->from('Horizon_Master_Template');
        $query = $this->db->get();
        return $query->result();
    }

    // function untuk get data isi
    public function get_data_isi() {
        $this->db->select('*');
        $this->db->from('Horizon_Master_Isi');
        $query = $this->db->get();
        return $query->result();
    }

    // function untuk get data surat
    public function get_data_surat() {
        $this->db->select('*');
        $this->db->from('Horizon_Master_Surat');
        $query = $this->db->get();
        return $query->result();
    }

    // function untuk get data penandatangan
    public function get_data_penandatangan() {
        $this->db->select('*');
        $this->db->from('Horizon_Master_PenandaTangan');
        $query = $this->db->get();
        return $query->result();
    }

    // function untuk get data viewer
    public function get_data_viewer($nik) {
        $this->db->select('Nama');
        $this->db->from('ms_niktelp');
        $this->db->where('NIK', $nik);
        $query = $this->db->get();
        return $query->row();
    }

    // function untuk get data cap
    public function get_data_cap() {
        $this->db->select('*');
        $this->db->from('Horizon_Master_Cap');
        $query = $this->db->get();
        return $query->result();
    }

    // get data untuk auto populate pada move details atau view
    public function get_details($persArea, $idSurat) {
        $this->db->select('
            Horizon_Master_PT.PersArea,
            Horizon_Master_PT.PersArea_Text,
            Horizon_Master_PT.PersArea_inisial,
            Horizon_Master_PT.PersArea_alamat,
            Horizon_Master_PT.id_1st_tandatangan,
            Horizon_Master_PT.id_2nd_tandatangan,
            Horizon_Master_PT.id_viewer,
            Horizon_Master_PT.id_template,
            Horizon_Master_PT.id_surat,
            Horizon_Master_PT.id_isi,
            Horizon_Master_PT.id_cap,
            Horizon_Master_Template.template_name,
            Horizon_Master_Template.header AS template_header,
            Horizon_Master_Template.footer AS template_footer,
            penandatangan_1.NIK AS approver1_nik,
            penandatangan_1.nama AS approver1_name,
            penandatangan_1.gambar_ttd AS approver1_ttd,
            penandatangan_1.gambar_paraf AS approver1_paraf,
            penandatangan_2.NIK AS approver2_nik,
            penandatangan_2.nama AS approver2_name,
            penandatangan_2.gambar_ttd AS approver2_ttd,
            penandatangan_2.gambar_paraf AS approver2_paraf,
            viewer.Nama AS viewer_name
        ');
        $this->db->from('Horizon_Master_PT');
        
        // Join with Horizon_Master_Template to fetch header and footer
        $this->db->join('Horizon_Master_Template', 'Horizon_Master_PT.id_template = Horizon_Master_Template.id_template', 'left');
        
        // Join with Horizon_Master_PenandaTangan for the first approver's details
        $this->db->join('Horizon_Master_PenandaTangan AS penandatangan_1', 'Horizon_Master_PT.id_1st_tandatangan = penandatangan_1.NIK', 'left');
        
        // Join with Horizon_Master_PenandaTangan for the second approver's details
        $this->db->join('Horizon_Master_PenandaTangan AS penandatangan_2', 'Horizon_Master_PT.id_2nd_tandatangan = penandatangan_2.NIK', 'left');
        
        // Join with table_msniktelp to fetch viewer's name
        $this->db->join('ms_niktelp AS viewer', 'Horizon_Master_PT.id_viewer = viewer.NIK', 'left');
        
        // Filter by persArea and idSurat
        $this->db->where('Horizon_Master_PT.PersArea', $persArea);
        $this->db->where('Horizon_Master_PT.id_surat', $idSurat);
    
        $query = $this->db->get();
    
        // Return the result as an object or null if not found
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return null;
        }
    }

    // function untuk check surat type
    public function check_surat_type($persarea, $id_surat) {
        $this->db->from('Horizon_Master_PT');
        $this->db->where('id_surat', $id_surat);
        $this->db->where('PersArea', $persarea);
        $count = $this->db->count_all_results();  // Count the rows matching the criteria
        return $count;   // Return the count of rows
    }
    
    // function untuk form submission (mode add)
    public function insert_data(
        $persarea,
        $persarea_text,
        $nomor,
        $persarea_inisial,
        $persarea_alamat,
        $id_1st_tandatangan,
        $id_2nd_tandatangan,
        $id_viewer,
        $id_template,
        $id_surat,
        $id_isi,
        $id_cap
    ) {
        $data = [
            'PersArea' => $persarea,
            'PersArea_Text' => $persarea_text,
            'nomor' => isset($nomor) ? $nomor : null,
            'PersArea_inisial' => $persarea_inisial,
            'PersArea_alamat' => $persarea_alamat,
            'id_1st_tandatangan' => $id_1st_tandatangan,
            'id_2nd_tandatangan' => $id_2nd_tandatangan,
            'id_viewer' => $id_viewer,
            'id_template' => $id_template,
            'id_surat' => $id_surat,
            'id_isi' => $id_isi,
            'id_cap' => $id_cap
        ];
    
        if ($this->db->insert('Horizon_Master_PT', $data)) {
            return true;
        } else {
            return false;
        }
    }
    
    // function untuk form submission (mode edit)
    public function update_data(
        $persarea,
        $persarea_text,
        $nomor,
        $persarea_inisial,
        $persarea_alamat,
        $id_1st_tandatangan,
        $id_2nd_tandatangan,
        $id_viewer,
        $id_template,
        $id_surat,
        $id_isi,
        $id_cap
    ) {
        $data = [
            'PersArea' => $persarea,
            'PersArea_Text' => $persarea_text,
            'nomor' => isset($nomor) ? $nomor : null,
            'PersArea_inisial' => $persarea_inisial,
            'PersArea_alamat' => $persarea_alamat,
            'id_1st_tandatangan' => $id_1st_tandatangan,
            'id_2nd_tandatangan' => $id_2nd_tandatangan,
            'id_viewer' => $id_viewer,
            'id_template' => $id_template,
            'id_isi' => $id_isi,
            'id_cap' => $id_cap
        ];
    
        $this->db->where('PersArea', $persarea);
        $this->db->where('id_surat', $id_surat);
    
        if ($this->db->update('Horizon_Master_PT', $data)) {
            return true;
        } else {
            return false;
        }
    }
    
    // function untuk update status
    public function set_inactive_active($id_surat, $is_active) {
        $data = array(
            'is_active' => $is_active
        );
        $this->db->where('id_surat', $id_surat);
        $this->db->update('Horizon_Master_Surat', $data);
    }
}
?>
