<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Empinfo_model extends CI_Model {

  public function get_bday_ls($scope='ALL',$time='TODAY')
  {
    // Cari unit ID yang login
    $this->db->select('Unit1 as unit');
    $this->db->from('ms_EmpOrg');
    $this->db->where('NIK','00'.$this->session->userdata('login_nik'));
    $unit = $this->db->get()->row()->unit;

    $this->db->select('m.NIK as nik');
    $this->db->select('m.Nama as name');
    $this->db->select('m.Positions as post_name');
    $this->db->select('m.Unit as org_name');
    $this->db->select('l.birthDate as birthdate');

    switch (strtoupper($scope)) {
      case 'HR':
      case 'PERS_ADMIN':
      case 'PERSADMIN':
        $pers_admin = $this->session->userdata('pers_admin');
        $this->db->where('m.PersAdmin', $pers_admin);
        break;
      case 'PT':
      case 'PERS_AREA':
      case 'PERSAREA':
        $pers_area = $this->session->userdata('pers_area');
        $this->db->where('m.PersArea', $pers_area);
        break;
      case 'BU':
      case 'UNIT':
        $this->db->join('ms_EmpOrg eo', 'SUBSTRING(eo.NIK,3,6) = m.NIK');
        $this->db->where('eo.Unit1', $unit);

        break;
    }

    switch (strtoupper($time)) {
      case 'TODAY':
        $this->db->select('DATEDIFF(YY,l.birthDate,GETDATE()) as age');
        $this->db->where('DATEPART(d, l.birthDate) = DATEPART(d, GETDATE())');
        $this->db->where('DATEPART(m, l.birthDate) = DATEPART(m, GETDATE())');
        break;

      case 'TOMMOROW':
        $this->db->select('DATEDIFF(YY,l.birthDate,DATEADD(day, 1, GETDATE())) as age');
        $this->db->where('DATEPART(d, l.birthDate) = DATEPART(d, DATEADD(day, 1, GETDATE()))');
        $this->db->where('DATEPART(m, l.birthDate) = DATEPART(m, DATEADD(day, 1, GETDATE()))');
        break;
      case 'UPCOMING':
        // Today until end of month
        $this->db->select('DATEDIFF(YY,l.birthDate,DATEADD(day, 30, GETDATE())) as age');

        $this->db->where('NOT DATEPART(d, l.birthDate) <= DATEPART(d, GETDATE())');
        $this->db->where('DATEPART(m, l.birthDate) = DATEPART(m, GETDATE())');
        break;
      case 'NEXT_MONTH':
        // Today until end of month
        $this->db->select('DATEDIFF(YY,l.birthDate,DATEADD(month, 1, GETDATE())) as age');


        $this->db->where('DATEPART(m, l.birthDate) = DATEPART(m, DATEADD(month, 1, GETDATE()))');
        break;
      default:
        $this->db->select("DATEDIFF(YY,l.birthDate,'".$time."') as age");
        $this->db->where("DATEPART(d, l.birthDate) = DATEPART(d, '".$time."')");
        $this->db->where("DATEPART(m, l.birthDate) = DATEPART(m, '".$time."')");
        break;
    }

    $this->db->from('ms_niktelp m');
    $this->db->join('tr_login l', 'm.NIK = l.userLogin');
    $this->db->order_by('DATEPART(m, l.birthDate)');
    $this->db->order_by('DATEPART(d, l.birthDate)');

    $this->db->order_by('age', 'desc');

    return $this->db->get()->result();

  }

}

/* End of file Empinfo_model.php */
/* Location: ./application/models/Empinfo_model.php */
