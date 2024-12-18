<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Account_model extends CI_Model
{

  public function __construct()
  {
    // Call the CI_Model constructor
    parent::__construct();

    // nambah ini 12/5/2024
    $this->load->database();
    
    $this->SMS = $this->load->database('SMS', TRUE);
  }

  public function proc_login($nik = '', $pass = '')
  {
    // check active
    $is_active = $this->check_active($nik);
    $pass_db = $this->get_pass($nik);

    if ($is_active == TRUE) {
      if ($pass_db == $pass) {
        $user = $this->get_detail($nik);
        switch ($user->lock) {
          case TRUE:
            return 'ERROR_ACCOUNT_LOCK';
            break;

          case FALSE:
            $arr_session = array(
              'log_nik' => $nik,
              'log_name' => ucwords(strtolower($user->name)),
              'log_pers_admin' => $user->pers_admin,
              'log_pers_area' => $user->pers_area,
              'log_sap' => $user->StatusNonSAP,
              'log_sap_role' => $user->ModuleRoleID
            );
            $this->session->set_userdata($arr_session);
            $this->update_session($nik, $this->session->userdata('session_id'));
            return 'OK';
            break;
        }

      } else {
        $re = $this->pw_counter($nik);
        switch ($re) {
          case 'LOCK':
            return 'ERROR_ACCOUNT_LOCK';
            break;

          default:
            return 'ERROR_PASSWORD_' . $re;
            break;
        }

      }
    } else {
      return 'ERROR_EMP_INACTIVE';
    }

  }

  /**
   * [mendapatkan password portal]
   * @param  string $nik
   * @return [string]
   */
  public function get_pass($nik = '')
  {
    $this->db->select('convert(varchar(16), decryptbypassphrase(userLogin, password)) as pass');
    $this->db->where('userLogin', $nik);
    return $this->db->get('tr_login')->row()->pass;
  }

  public function get_persadmin($persadmin = '')
  {
    $this->db->select('*');
    $this->db->from('HRSS_PersAdmin');
    $this->db->where('PersAdmin_Id', $persadmin);
    $this->db->order_by('PersAdmin_parent', 'DESC');
    // var_dump($this->db->get()->row());
    return $this->db->get()->row();
  }

  public function get_niktelp($nik = '')
  {
    $this->db->select('*');
    $this->db->from('ms_niktelp');
    $this->db->where('NIK', $nik);
    return $this->db->get()->row();
  }

  /**
   * [mencek aktif/tidaknya karyawan di SAP via ms_niktelp]
   * @param  string $nik [nomor induk karyawan]
   * @return [bollean]      [description]
   */
  public function check_active($nik = '')
  {
    $this->db->select('COUNT(*) as val');
    $this->db->where('NIK', $nik);
    $result = $this->db->get('ms_niktelp')->row()->val;

    if ($result == 0) {
      return FALSE;
    } else {
      return TRUE;
    }
  }

  /**
   * [memanggil detail akun HR Portal berdasarkan nik]
   * @param  string $nik [nomor induk karyawan]
   * @return [type]      [satu record]
   */
  public function get_detail($nik = '')
  {
    $this->db->select('m.NIK as nik');
    $this->db->select('m.Nama as name');
    $this->db->select('m.Positions as position');
    $this->db->select('m.esg as esg');
    $this->db->select('m.Unit as org');
    $this->db->select('l.phoneNumber as phone');
    $this->db->select('l.email as email');
    $this->db->select('m.PersAdmin as pers_admin');
    $this->db->select('m.PersArea as pers_area');
    $this->db->select('m.SubArea as pers_subarea');
    $this->db->select('l.lastSessionID');
    $this->db->select('l.ModuleRoleID');
    $this->db->select('l.StatusNonSAP');
    $this->db->select('l.counterPassword as pass_count');
    $this->db->select('l.lock');
    $this->db->select('m.TglUpdate as last_update');
    $this->db->from('ms_niktelp m');
    $this->db->join('tr_login l', 'm.NIK = l.userLogin', 'left');
    // $this->db->where('m.NIK', $nik);
    $this->db->like('NIK', $nik);

    return $this->db->get()->row();
  }

  public function tambahwaout($phone, $text)
  {
    $object = array(
      'MessageTo' => $phone,
      'MessageText' => 'HR PORTAL Agenda Bersama - ' . ltrim(str_replace("'", "\"", strip_tags($text))),
      'IsSent' => '0',
    );

    $this->SMS->insert('WAPortalOut', $object);
  }

  public function get_birthdate($nik = '', $keydate = '')
  {
    if ($keydate == '') {
      $keydate = date('Ymd');
    }
    // BAPI Config untuk transaksi Upload Hiring
    $this->load->library('saprfc');
    /*$config['ashost']   = '10.9.12.100';
    $config['sysnr']    = '30';
    $config['client']   = '600';
    $config['user']     = 'HCM-PORTAL-1';
    $config['passwd']   = 'hris2010';
    $config['msgsrv']   = '';
    $config['r3name']   = 'LHR';
    $config['codepage'] = '4110';*/

    $config['ashost'] = SAP_HOST_PROD;
    $config['sysnr'] = SAP_SYSNR_PROD;
    $config['client'] = SAP_CLIENT_PROD;
    $config['user'] = SAP_USER_PORTAL1;
    $config['passwd'] = SAP_PASSWD_PORTAL1;
    $config['msgsrv'] = '';
    $config['r3name'] = SAP_R3NAME_PROD;
    $config['codepage'] = SAP_CODEPAGE_PROD;

    $this->saprfc->sapAttr($config);
    $this->saprfc->connect();
    $this->saprfc->functionDiscover('ZHRFM_CV_MINI');
    $importParamName = array(
      'FI_PERNR',
      'FI_PERNR_DIAKSES',
      'FI_TANGGAL'
    );

    $importParamValue = array(
      $nik,
      $nik,
      $keydate
    );

    $this->saprfc->importParameter($importParamName, $importParamValue);
    $this->saprfc->setInitTable('FI_CV');
    $this->saprfc->executeSAP();
    $obj = $this->saprfc->fetch_row('FI_CV', 'object', 1);

    $this->saprfc->free();
    $this->saprfc->close();

    $dob = substr($obj->TTL, 0, 4) . '-' . substr($obj->TTL, 4, 2) . '-' . substr($obj->TTL, 6, 2);

    return $dob;

  }

  public function get_esg($nik = '')
  {
    $this->db->select('esg');
    $this->db->where('NIK', $nik);
    return $this->db->get('ms_niktelp')->row()->esg;

  }

  public function get_detail_sap($nik, $keydate = '')
  {
    if ($keydate == '') {
      $keydate = date('Ymd');
    }


    $this->load->library('saprfc');
    $this->saprfc->connect();
    $this->saprfc->functionDiscover('ZHRFM_CV_MINI_SISDM2');
    $importParamName = array(
      'FI_PERNR',
      'FI_PERNR_DIAKSES',
      'FI_TANGGAL'
    );
    $importParamValue = array(
      $nik,
      $nik,
      $keydate
    );
    $this->saprfc->importParameter($importParamName, $importParamValue);

    $this->saprfc->setInitTable('FI_CV');
    $this->saprfc->executeSAP();
    $temp = $this->saprfc->fetch_row('FI_CV', 'object');
    $this->saprfc->free();
    $this->saprfc->close();

    if ($temp) {
      $result = array(
        'nik' => $temp->NIK,
        'fullname' => $temp->NAMALENGKAP,
        'birthdate' => date_from_sap($temp->TTL),
        'org_id' => $temp->ORGID,
        'org_name' => $temp->UNIT,
        'post_id' => $temp->POSID,
        'post_name' => $temp->POSITIONS,
        'esg' => $temp->ESG,
        'persadmin' => $temp->PERSADMIN,
        'joindate' => date_from_sap($temp->TGLMASUK),
        'permdate' => $temp->TGLDIANGKAT
      );
      return (object) $result;

    } else {
      return false;
    }

  }

  /**
   * [mendapatkan session ID dan waktu login terakhir terakhir]
   * @param  string $nik [nomor induk karyawan]
   * @return [type]      []
   */
  public function get_last_session($nik = '')
  {
    $this->db->select('lastSessionID');
    $this->db->where('userLogin', $nik);

    return $this->db->get('tr_login')->row()->lastSessionID;
  }

  /**
   * [mengupdate sesi (id dan waktu) login user ]
   * @param  string $nik        [nik]
   * @param  string $session_id [session id]
   */
  public function update_session($nik = '', $session_id = '')
  {
    if ($nik == '') {
      $nik = $this->session->userdata('login_nik');
    }

    if ($session_id == '') {
      $session_id = $this->session->session_id;
    }
    $object = array(
      'lastSessionID' => $session_id,
      'lastLoginTime' => date('Y-m-d H:i:s')
    );
    $this->db->where('userLogin', $nik);
    $this->db->update('tr_login', $object);
  }

  /**
   * [update counter salah password dan lock akun HR Portal]
   * @param  string  $nik     [nomor induk karyawan]
   * @param  integer $counter [jumlah kesalahan]
   * @param  boolean $lock    [TRUE = akun terkunci]
   */
  public function update_lock_counter($nik = '', $counter = 0, $lock = FALSE)
  {
    $object = array(
      'counter' => $counter,
      'lock' => $lock
    );
    $this->db->where('userLogin', $nik);
    $this->db->update('tr_login', $object);
  }

  public function pw_counter($nik = '')
  {
    $this->db->select('counterPassword');
    $this->db->where('userLogin', $nik);
    $counter = $this->db->get('tr_login')->row()->counterPassword;
    $counter += 1;
    if ($counter < 5) {
      $object = array(
        'counter' => $counter
      );
      $this->db->where('userLogin', $nik);
      $this->db->update('tr_login', $object);

      return $counter;
    } else {
      $object = array(
        'counter' => $counter,
        'lock' => TRUE
      );
      $this->db->where('userLogin', $nik);
      $this->db->update('tr_login', $object);

      return 'LOCK';
    }
  }

  public function reset_pw_counter($nik = '')
  {
    $object = array(
      'counter' => 0,
      'lock' => FALSE
    );
    $this->db->where('userLogin', $nik);
    $this->db->update('tr_login', $object);
  }

  /**
   * [mendapatkan username dan password SAP Role]
   * @param  string $role_id [role ID]
   * @return [type]          [satu record]
   */
  public function get_sap_role($role_id = 1)
  {
    $this->db->select('ModuleRoleID, Username, Password');
    $this->db->where('ModuleRoleID', $role_id);
    return $this->db->get('ModuleRoleID')->row();
  }

  public function count_module_viewer($nik = '', $module = '')
  {
    if ($nik) {
      $this->db->select('COUNT (ViewID) as val');
      $this->db->from('ms_ModuleView MV');
      if ($module != '') {
        $this->db->join('ms_Module M', 'MV.ModuleID = M.ModuleID');
        if (is_int($module)) {
          $this->db->where('MV.ModuleID', $module);
        } else {
          $this->db->where('UPPER(M.ModuleName)', strtoupper($module));
        }
      }
      $this->db->where('MV.UserLogin', $nik);
      $this->db->where('MV.isActive', 1);
      return $this->db->get()->row()->val;

    } else {
      return false;
    }

  }

  public function count_module_role($nik = '', $module = '', $role = '')
  {
    $this->db->select('COUNT (AdminID) as val');
    $this->db->from('ms_ModuleAdmin MA');


    if ($module != '') {
      $this->db->join('ms_Module M', 'MA.ModuleID = M.ModuleID');
      if (is_int($module)) {
        $this->db->where('MA.ModuleID', $module);
      } else {
        $this->db->where('UPPER(M.ModuleName)', strtoupper($module));
      }
    }

    if ($role != '') {
      $this->db->join('ms_ModuleSpecial S', 'MA.SpecialID = S.SpecialID');
      if (is_array($role)) {
        $this->db->where_in('S.SpecialName', $role);
      } else {
        if (is_int($role)) {
          $this->db->where('MA.SpecialID', $role);
        } else {
          $this->db->where('UPPER(S.SpecialName)', strtoupper($role));
        }
      }
    }

    $this->db->where('MA.UserLogin', $nik);
    $this->db->where('MA.isActive', 1);

    return $this->db->get()->row()->val;
  }

  public function get_module_role($nik = '', $module = '', $role = '')
  {
    $this->db->select('MA.*');
    $this->db->select('M.ModuleName as module_name');
    $this->db->select('S.SpecialName as role_name');
    $this->db->from('ms_ModuleAdmin MA');
    $this->db->join('ms_Module M', 'MA.ModuleID = M.ModuleID');
    $this->db->join('ms_ModuleSpecial S', 'MA.SpecialID = S.SpecialID');

    if ($module != '') {
      if (is_int($module)) {
        $this->db->where('MA.ModuleID', $module);
      } else {
        $this->db->where('UPPER(M.ModuleName)', strtoupper($module));
      }
    }

    if ($role != '') {
      if (is_array($role)) {
        $this->db->where_in('S.SpecialName', $role);
      } else {
        if (is_int($role)) {
          $this->db->where('MA.SpecialID', $role);
        } else {
          $this->db->where('UPPER(S.SpecialName)', strtoupper($role));
        }
      }
    }

    $this->db->where('MA.UserLogin', $nik);

    return $this->db->get()->result();
  }

  public function get_module_role_row($nik = '', $module = '', $role = '')
  {
    $this->db->select('MA.*');
    $this->db->select('M.ModuleName as module_name');
    $this->db->select('S.SpecialName as role_name');
    $this->db->from('ms_ModuleAdmin MA');
    $this->db->join('ms_Module M', 'MA.ModuleID = M.ModuleID');
    $this->db->join('ms_ModuleSpecial S', 'MA.SpecialID = S.SpecialID');

    if ($module != '') {
      if (is_int($module)) {
        $this->db->where('MA.ModuleID', $module);
      } else {
        $this->db->where('UPPER(M.ModuleName)', trim(strtoupper($module)));
      }
    }

    if ($role != '') {
      if (is_array($role)) {
        $this->db->where_in('S.SpecialName', $role);
      } else {
        if (is_int($role)) {
          $this->db->where('MA.SpecialID', $role);
        } else {
          $this->db->where('UPPER(S.SpecialName)', trim(strtoupper($role)));
        }
      }
    }

    $this->db->where('MA.UserLogin', $nik);
    $this->db->where('MA.isActive', 1);
    $this->db->order_by('MA.AdminID', 'desc');

    return $this->db->get()->row();
  }

  public function get_role_arr($nik = '')
  {
    if ($nik == '') {
      $nik = $this->session->userdata('login_nik');
    }

    $this->db->select('r.SpecialName AS role_code');
    $this->db->from('ms_moduleAdmin a');

    $this->db->join('ms_moduleSpecial r', 'a.SpecialID = r.SpecialID');

    $this->db->where('a.UserLogin', $nik);
    $this->db->where('a.isActive', TRUE);

    $temp = $this->db->get()->result();
    $result = array();
    foreach ($temp as $row) {
      $result[] = $row->role_code;
    }

    return $result;
  }

  public function get_menu_query()
  {
    // check base on query function
    $this->db->select('menu_code,query_function');
    $this->db->from('sys_menu_access a');
    $this->db->where('a.is_active', TRUE);
    $this->db->where('a.query_check', 1);
    $temp = $this->db->get()->result();

    $result = array();
    $i = 0;
    foreach ($temp as $row) {
      if ($this->check_query($row->query_function) == TRUE) {
        $result[$i] = $row->menu_code;
        $i++;
      }

    }

    return $result;
  }

  public function count_menu($role = '', $parent_code = 'ROOT')
  {
    $nik = $this->session->userdata('login_nik');

    // check base on query function
    $this->db->select('menu_code,query_function');
    $this->db->from('sys_menu_access a');
    $this->db->where('a.is_active', TRUE);
    $this->db->where('a.query_check', 1);
    $temp = $this->db->get()->result();

    $add_on = array();
    $i = 0;
    foreach ($temp as $row) {
      if ($this->check_query($row->query_function) == TRUE) {
        $add_on[$i] = $row->menu_code;
        $i++;
      }
    }

    // SubQuery of SubQuery;
    $this->db->select('SpecialName');
    $this->db->join('ms_ModuleAdmin a', 'a.SpecialID = r.SpecialID');
    $this->db->from('ms_ModuleSpecial r');
    $this->db->where('a.isActive', TRUE);
    $this->db->where('a.UserLogin', $nik);
    $sub_2 = $this->db->get_compiled_select();

    // SubQuery
    $this->db->select('menu_code');
    $this->db->from('sys_menu_access a');
    $this->db->where('a.is_active', TRUE);
    $this->db->group_start();
    $this->db->where('a.role_code IN ( ' . $sub_2 . ')');

    $this->db->or_where('a.role_code', 'USER');
    $this->db->group_end();
    $sub_1 = $this->db->get_compiled_select();

    $this->db->select('COUNT(*) as val');
    $this->db->from('sys_menu m');

    $this->db->where('m.parent_code', $parent_code);
    $this->db->where('m.is_active', TRUE);

    $this->db->group_start();
    $this->db->where('m.menu_code IN ( ' . $sub_1 . ')');
    if (count($add_on)) {
      $this->db->or_where_in('m.menu_code', $add_on);

    }
    $this->db->group_end();

    return $this->db->get()->row()->val;
  }

  public function get_menu_ls($role = '', $parent_code = 'ROOT')
  {
    $nik = $this->session->userdata('login_nik');

    // check base on query function
    $this->db->select('menu_code,query_function');
    $this->db->from('sys_menu_access a');
    $this->db->where('a.is_active', TRUE);
    $this->db->where('a.query_check', 1);
    $temp = $this->db->get()->result();

    $add_on = array();
    $i = 0;
    foreach ($temp as $row) {
      if ($this->check_query($row->query_function) == TRUE) {
        $add_on[$i] = $row->menu_code;
        $i++;
      }
    }

    // SubQuery of SubQuery;
    $this->db->select('SpecialName');
    $this->db->join('ms_ModuleAdmin a', 'a.SpecialID = r.SpecialID');
    $this->db->from('ms_ModuleSpecial r');
    $this->db->where('a.isActive', TRUE);
    $this->db->where('a.UserLogin', $nik);
    $sub_2 = $this->db->get_compiled_select();

    // SubQuery
    $this->db->select('menu_code');
    $this->db->from('sys_menu_access a');
    $this->db->where('a.is_active', TRUE);
    $this->db->group_start();
    $this->db->where('a.role_code IN ( ' . $sub_2 . ')');
    $this->db->or_where('a.role_code', 'USER');

    $this->db->group_end();
    $sub_1 = $this->db->get_compiled_select();

    $this->db->select('m.*');
    $this->db->from('sys_menu m');

    $this->db->where('m.parent_code', $parent_code);
    $this->db->where('m.is_active', TRUE);

    $this->db->group_start();
    $this->db->where('m.menu_code IN ( ' . $sub_1 . ')');
    if (count($add_on)) {
      $this->db->or_where_in('m.menu_code', $add_on);

    }
    $this->db->group_end();


    $this->db->order_by('m.order_value');
    // echo $this->db->get_compiled_select();
    return $this->db->get()->result();
  }

  public function get_pic_patch($nik = '')
  {
    if ($nik) {
      $card = $this->load->database('idcard', TRUE);
      $card->select('old_photo_path');
      $card->from('id_card_online');
      $card->where('nik', $nik);

      $temp = $card->get()->row()->old_photo_path;
      $temp = strtoupper(preg_replace("/\\\\/", "/", $temp));
      $result = str_replace(array("P:/", "./ASSETS/"), "https://idcard.kompasgramedia.com/assets/", $temp);
      $headers = get_headers($result);

      if (substr($headers[0], 9, 3) != "200") {
        $result = strtolower($result);
      }
      return $result;

    } else {
      return false;
    }
  }

  public function get_om_row($nik = '', $keydate = '')
  {
    if ($keydate == '') {
      $keydate = date('Ymd');
    }
    // BAPI Config untuk transaksi Upload Hiring
    $this->load->library('saprfc');
    /*$config['ashost']   = '10.9.12.100';
    $config['sysnr']    = '30';
    $config['client']   = '600';
    $config['user']     = 'HCM-PORTAL-2';
    $config['passwd']   = 'hris2011';
    $config['msgsrv']   = '';
    $config['r3name']   = 'LHR';
    $config['codepage'] = '4110';*/

    $config['ashost'] = SAP_HOST_PROD;
    $config['sysnr'] = SAP_SYSNR_PROD;
    $config['client'] = SAP_CLIENT_PROD;
    $config['user'] = SAP_USER_PORTAL2;
    $config['passwd'] = SAP_PASSWD_PORTAL2;
    $config['msgsrv'] = '';
    $config['r3name'] = SAP_R3NAME_PROD;
    $config['codepage'] = SAP_CODEPAGE_PROD;

    $this->saprfc->sapAttr($config);
    $this->saprfc->connect();
    $this->saprfc->functionDiscover('ZHRFM_GETPOSORG_OM');
    $importParamName = array(
      'KEYDATE',
      'OBJID'
    );

    $importParamValue = array(
      $keydate,
      $nik
    );

    $this->saprfc->importParameter($importParamName, $importParamValue);
    $this->saprfc->setInitTable('FI_OUT');
    $this->saprfc->executeSAP();
    $obj = $this->saprfc->fetch_row('FI_OUT', 'object', 1);

    $this->saprfc->free();
    $this->saprfc->close();

    return $obj;
  }

  public function get_pa_infotype0001_last($nik)
  {
    $this->db->select('Posisi as post_id');
    $this->db->from('ms_EmpOrg');
    $this->db->where('NIK', '00' . $nik);
    $this->db->order_by('UpdateTime', 'desc');
  }

  public function check_query($query_function = '')
  {

    $nik = $this->session->userdata('login_nik');
    $pers_admin = $this->session->userdata('pers_admin');
    switch ($query_function) {
      case 'is_chief_or_cochief':
        $this->db->select('COUNT(*) as val');
        $this->db->from('ms_EmpOrg');
        $this->db->where('NIK', '00' . $nik);
        $this->db->where_in('isChief', array(1, 2));

        if ($this->db->get()->row()->val) {
          return TRUE;
        } else {
          return FALSE;
        }
        break;
      case 'count_skkl':
        $this->db->select('COUNT(t.*) as val');
        $this->db->from('tb_SKKLTransaction t');
        $this->db->join('ms_niktelp m', 'm.NIK = t.NIK');

        $this->db->where('PersAdmin', $pers_admin);
        $this->db->where('isApproved', TRUE);
        $this->db->where('HRNIK is NULL');
        $this->db->where('t.Flag is NULL');

        return $this->db->get()->row()->val;
        break;
      case 'count_attreq':
        $this->db->select('COUNT(*) as val');
        $this->db->from('tb_AttendanceTransaction');
        $this->db->where('isApproved is NULL');

        $this->db->group_start();
        $this->db->where('FirstApproverNIK', $nik);
        $this->db->where('isFirstApproved is NULL');
        $this->db->group_end();
        $this->db->or_group_start();
        $this->db->where('SecondApproverNIK', $nik);
        $this->db->where('isSecondApproved is NULL');
        $this->db->group_end();
        $this->db->or_group_start();
        $this->db->where('ThirdApproverNIK', $nik);
        $this->db->where('isThirdApproved is NULL');
        $this->db->group_end();

        return $this->db->get()->row()->val;
        break;
      case 'count_absreq':
        $this->db->select('COUNT(*) as val');
        $this->db->from('tr_Absence');
        $this->db->where_not_in('AbsenceType', array(1001, 1002, 1003));

        $this->db->group_start();
        $this->db->where('FirstApproverNIK', $nik);
        $this->db->where('isFirstApproved is NULL');
        $this->db->group_end();
        $this->db->or_group_start();
        $this->db->where('SecondApproverNIK', $nik);
        $this->db->where('isSecondApproved is NULL');
        $this->db->group_end();
        $this->db->or_group_start();
        $this->db->where('ThirdApproverNIK', $nik);
        $this->db->where('isThirdApproved is NULL');
        $this->db->group_end();

        return $this->db->get()->row()->val;
        break;
      case 'count_leavreq':
        $this->db->select('COUNT(*) as val');
        $this->db->from('tr_Absence');
        $this->db->where_in('AbsenceType', array(1001, 1002, 1003));

        $this->db->group_start();
        $this->db->where('FirstApproverNIK', $nik);
        $this->db->where('isFirstApproved is NULL');
        $this->db->group_end();
        $this->db->or_group_start();
        $this->db->where('SecondApproverNIK', $nik);
        $this->db->where('isSecondApproved is NULL');
        $this->db->group_end();
        $this->db->or_group_start();
        $this->db->where('ThirdApproverNIK', $nik);
        $this->db->where('isThirdApproved is NULL');
        $this->db->group_end();

        return $this->db->get()->row()->val;
        break;
      case 'count_subttt_confirm':
        $this->db->select('COUNT(*) as val');
        $this->db->from('Sub_SubtitutionTransaction');
        $this->db->where('FriendNIK', $nik);
        $this->db->where('IsFriendAccepted', 0);
        return $this->db->get()->row()->val;

        break;
      case 'count_subttt_req':
        $this->db->select('COUNT(*) as val');
        $this->db->from('Sub_SubtitutionTransaction');
        $this->db->group_start();
        $this->db->where('FirstApproverNIK', $nik);
        $this->db->where_not_in('isFirstApproved', array('1', '2'));
        $this->db->group_end();
        $this->db->or_group_start();
        $this->db->where('SecondApproverNIK', $nik);
        $this->db->where_not_in('isSecondApproved', array('1', '2'));
        $this->db->group_end();
        $this->db->or_group_start();
        $this->db->where('ThirdApproverNIK', $nik);
        $this->db->where_not_in('isThirdApproved', array('1', '2'));
        $this->db->group_end();
        $this->db->where('IsFriendAccepted', 1);

        return $this->db->get()->row()->val;
        break;
      case 'count_bpjs_edit':
        $this->db->select('COUNT(*) as val');
        $this->db->from('tb_Bpjs');
        $this->db->where_in('Active', array('6', '7', '8'));
        $this->db->where_in('NIK', array($nik, '021620', '006332', '007201'));
        return $this->db->get()->row()->val;
        break;
      case 'count_motoloan_req':
        $this->db->select('COUNT(*) as val');
        $this->db->from('VW_MotorCycleLoanDetail');
        $this->db->group_start();
        $this->db->where('FirstApproverNIK', $nik);
        $this->db->where('isFirstApproved is NULL');
        $this->db->group_end();
        $this->db->or_group_start();
        $this->db->where('isFirstApproved', 1);
        $this->db->where('SecondApproverNIK', $nik);
        $this->db->where('isSecondApproved is NULL');
        $this->db->group_end();
        $this->db->or_group_start();
        $this->db->where('isFirstApproved', 1);
        $this->db->where('isSecondApproved', 1);

        $this->db->where('ThirdApproverNIK', $nik);
        $this->db->where('isThirdApproved is NULL');
        $this->db->group_end();

        return $this->db->get()->row()->val;
        break;
      case 'count_motoloan_hr':
        $detail = $this->get_detail($nik);

        $this->db->select('COUNT(*) as val');
        $this->db->from('tb_MotorcycleLoanPA');
        $this->db->where('Active', TRUE);
        $this->db->where('PersAdmin', $detail->pers_admin);
        $this->db->where('PersonalSubArea', $detail->pers_subarea);

        return $this->db->get()->row()->val;
        break;
      case 'is_epayroll':
        if ($nik == '021620' or $nik == '007201') {
          return TRUE;
        } else {
          $this->db->select('COUNT(*) as val');
          $this->db->from('ms_niktelp');
          $this->db->where('NIK', $nik);
          $this->db->group_start();

          $this->db->where('PayAdmin', '914');
          $this->db->where('status', 'Contract');
          $this->db->group_end();
          $this->db->or_group_start();
          $this->db->where('PayArea', 'KB');
          $this->db->where_in('PersArea', array('0023', '0060'));
          $this->db->group_end();

          if ($this->db->get()->row()->val == TRUE) {
            return TRUE;
          } else {
            return FALSE;
          }

        }
        break;
    }
  }



}

/* End of file Account_model.php */
/* Location: ./application/models/Account_model.php */
