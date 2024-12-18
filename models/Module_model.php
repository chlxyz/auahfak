<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Module_model extends CI_Model {

  public function get_viewer_ls($module_code='',$int_val = '' , $char_val ='')
  {
    $this->db->select('mv.*');
    $this->db->select('nt.Nama as fullname');
    $this->db->from('ms_ModuleView mv');
    $this->db->join('ms_Module m', 'mv.ModuleID = m.Module');
    $this->db->join('ms_niktelp nt', 'mv.UserLogin = nt.NIK','left');
    if (is_int($module_code)) {
      $this->db->where('m.ModuleID', $module_code);

    } else {
      $this->db->where('UPPER(m.ModuleName)', strtoupper($module_code));
      
    }

    if ($int_val != '') {
      $this->db->where('mv.Integer_Miscellaneous', $int_val);
      
    }

    if ($char_val != '') {
      $this->db->where('mv.Varchar_Miscellaneous', $char_val);
    }
    $this->db->where('mv.isActive', 1);

    return $this->db->get()->result();
  }

  public function get_admin_ls($module_code='',$role_code ='')
  {
   
  }

}

/* End of file Module_model.php */
/* Location: ./application/models/Module_model.php */