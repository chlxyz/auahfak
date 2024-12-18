<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Geoloc_model extends CI_Model {

  public function get_countries()
  {
    $this->db->from('core_m_country');
    return $this->db->get()->result();
  }

  public function get_country($country_code='ID')
  {
    $this->db->from('core_m_country');
    $this->db->where('country_code', strtoupper($country_code));
    return $this->db->get()->row();
  }

}

/* End of file Geoloc_model.php */
/* Location: ./application/models/Geoloc_model.php */