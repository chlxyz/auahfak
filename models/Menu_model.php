<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Menu_model extends CI_Model{

  public function __construct()
  {
    parent::__construct();
    $this->load->database();
    //Codeigniter : Write Less Do More
  }

  public function get_menu_row($menu_code='')
  {
    $this->db->from('sys_menu');
    $this->db->where('menu_code', $menu_code);
    return $this->db->get()->row();
  }

  public function get_menu_query()
  {
    // check base on query function
    $this->db->select('menu_code,query_function');
    $this->db->from ('sys_menu_access a');
    $this->db->where('a.is_active', TRUE);
    $this->db->where('a.query_check', 1);
    $temp = $this->db->get()->result();

    $result = array();
    $i = 0 ;
    foreach ($temp as $row) {
      if($this->check_query( $row->query_function) == TRUE) {
        $result[$i] = $row->menu_code;
        $i++;
      }

    }

    return $result;
  }

  public function count_menu($role='',$parent_code='ROOT')
  {

    $nik = $this->session->userdata('login_nik');

    // check base on query function
    $this->db->select('menu_code,query_function');
    $this->db->from ('sys_menu_access a');
    $this->db->where('a.is_active', TRUE);
    $this->db->where('a.query_check', 1);
    $temp = $this->db->get()->result();

    $add_on = array();
    $i = 0 ;
    foreach ($temp as $row) {
      if($this->check_query( $row->query_function) == TRUE) {
        $add_on[$i] = $row->menu_code;
        $i++;
      }
    }

    // SubQuery of SubQuery;
    $this->db->select('SpecialName');
    $this->db->join('ms_ModuleAdmin a', 'a.SpecialID = r.SpecialID');
    $this->db->from ('ms_ModuleSpecial r');
    $this->db->where('a.isActive', TRUE);
    $this->db->where('a.UserLogin', $nik);
    $sub_2 = $this->db->get_compiled_select();

    // SubQuery
    $this->db->select('menu_code');
    $this->db->from ('sys_menu_access a');
    $this->db->where('a.is_active', TRUE);
    $this->db->group_start();
    $this->db->where('a.role_code IN ( '.$sub_2.')');
    $this->db->or_where('a.role_code', 'USER');
    $this->db->group_end();
    $sub_1 = $this->db->get_compiled_select();

    $this->db->select('COUNT(*) as val');
    $this->db->from('sys_menu m');

    $this->db->where('m.parent_code', $parent_code);
    $this->db->where('m.is_active', TRUE);

    $this->db->group_start();
    $this->db->where('m.menu_code IN ( '.$sub_1.')');
    if (count($add_on)) {
      $this->db->or_where_in('m.menu_code',$add_on);

    }
    $this->db->group_end();

    return $this->db->get()->row()->val;
  }

  public function get_menu_ls($role='',$parent_code='ROOT')
  {
    // $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
    // $this->cache->memcached->is_supported();
    // $cache = $this->cache->memcached->get('menu');
    // if ($cache) {
    //
    //   return $cache;
    // } else {
      $nik = $this->session->userdata('login_nik');
      // check base on query function
      $this->db->select('menu_code,query_function');
      $this->db->from ('sys_menu_access a');
      $this->db->where('a.is_active', TRUE);
      $this->db->where('a.query_check', 1);
      $temp = $this->db->get()->result();

      $add_on = array();
      $i = 0 ;
      foreach ($temp as $row) {
        if($this->check_query( $row->query_function) == TRUE) {
          $add_on[$i] = $row->menu_code;
          $i++;
        }
      }

      // SubQuery of SubQuery;
      $this->db->select('SpecialName');
      $this->db->join('ms_ModuleAdmin a', 'a.SpecialID = r.SpecialID');
      $this->db->from ('ms_ModuleSpecial r');
      $this->db->where('a.isActive', TRUE);
      $this->db->where('a.UserLogin', $nik);
      $sub_2 = $this->db->get_compiled_select();

      // SubQuery
      $this->db->select('menu_code');
      $this->db->from ('sys_menu_access a');
      $this->db->where('a.is_active', TRUE);
      $this->db->group_start();
      $this->db->where('a.role_code IN ( '.$sub_2.')');
      $this->db->or_where('a.role_code', 'USER');

      $this->db->group_end();
      $sub_1 = $this->db->get_compiled_select();

      // Main Query
      $this->db->select('m.*');
      $this->db->select('(SELECT COUNT(*) FROM sys_menu c WHERE c.parent_code = m.menu_code) as count_sub');
      $this->db->from('sys_menu m');

      $this->db->where('m.parent_code', $parent_code);
      $this->db->where('m.is_active', TRUE);

      $this->db->group_start();
      $this->db->where('m.menu_code IN ( '.$sub_1.')');
      if (count($add_on)) {
        $this->db->or_where_in('m.menu_code',$add_on);

      }
      $this->db->group_end();


      $this->db->order_by('m.order_value');
      // echo $this->db->get_compiled_select();
      $data = $this->db->get()->result();
      // $this->cache->memcached->save('menu', $data, 300);

      return $data;

    // }
  }

  public function get_pic_patch($nik='')
  {
    if ($nik) {
      $card = $this->load->database('idcard', TRUE);
      $card->select('old_photo_path');
      $card->from('id_card_online');
      $card->where('nik',$nik);

      $temp = $card->get()->row()->old_photo_path;
      $temp = strtoupper(preg_replace("/\\\\/", "/", $temp));
      $result = str_replace(array("P:/","./ASSETS/"), "https://idcard.kompasgramedia.com/assets/", $temp);
      $headers = get_headers($result);

      if (substr($headers[0], 9, 3) != "200") {
        $result = strtolower($result);
      }
      return $result;

    } else {
      return false;
    }
  }

  public function get_om_row($nik='',$keydate='')
  {
    if ($keydate=='') {
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

    $config['ashost']   = SAP_HOST_PROD;
    $config['sysnr']    = SAP_SYSNR_PROD;
    $config['client']   = SAP_CLIENT_PROD;
    $config['user']     = SAP_USER_PORTAL2;
    $config['passwd']   = SAP_PASSWD_PORTAL2;
    $config['msgsrv']   = '';
    $config['r3name']   = SAP_R3NAME_PROD;
    $config['codepage'] = SAP_CODEPAGE_PROD;

    $this->saprfc->sapAttr($config);
    $this->saprfc->connect();
    $this->saprfc->functionDiscover('ZHRFM_GETPOSORG_OM');
    $importParamName = array(
      'KEYDATE',
      'OBJID');

    $importParamValue = array(
      $keydate,
      $nik);

    $this->saprfc->importParameter($importParamName, $importParamValue);
    $this->saprfc->setInitTable('FI_OUT');
    $this->saprfc->executeSAP();
    $obj = $this->saprfc->fetch_row('FI_OUT','object',1);

    $this->saprfc->free();
    $this->saprfc->close();

    return $obj;
  }

  public function check_query($query_function='')
  {

    $nik = $this->session->userdata('login_nik');
    $pers_admin = $this->session->userdata('pers_admin');
    // return TRUE;
    switch ($query_function) {
      case 'is_chief_or_cochief':
        $this->db->select('COUNT(*) as val');
        $this->db->from('ms_EmpOrg');
        $this->db->where('NIK', '00'.$nik);
        $this->db->where_in('isChief',array(1,2));

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
        $this->db->where_not_in('AbsenceType', array(1001,1002,1003));

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
        $this->db->where_in('AbsenceType', array(1001,1002,1003));

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
        $this->db->where_not_in('isFirstApproved',array('1','2'));
        $this->db->group_end();
        $this->db->or_group_start();
        $this->db->where('SecondApproverNIK', $nik);
        $this->db->where_not_in('isSecondApproved',array('1','2'));
        $this->db->group_end();
        $this->db->or_group_start();
        $this->db->where('ThirdApproverNIK', $nik);
        $this->db->where_not_in('isThirdApproved',array('1','2'));
        $this->db->group_end();
        $this->db->where('IsFriendAccepted', 1);

        return $this->db->get()->row()->val;
        break;
      case 'count_bpjs_edit':
        $this->db->select('COUNT(*) as val');
        $this->db->from('tb_Bpjs');
        $this->db->where_in('Active', array('6','7','8'));
        $this->db->where_in('NIK', array($nik,'021620','006332','007201'));
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
        $this->db->where('isFirstApproved',1);
        $this->db->where('SecondApproverNIK', $nik);
        $this->db->where('isSecondApproved is NULL');
        $this->db->group_end();
        $this->db->or_group_start();
        $this->db->where('isFirstApproved',1);
        $this->db->where('isSecondApproved',1);

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
        if ($nik == '021620' OR $nik == '007201') {
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
          $this->db->where_in('PersArea', array('0023','0060'));
          $this->db->group_end();

          if ($this->db->get()->row()->val == TRUE) {
            return TRUE;
          } else {
            return FALSE;
          }

        }
        break;
      case 'is_agenda':

          $this->db->select('COUNT(*) as val');
          $this->db->from('ms_ModuleView');
          $this->db->where('UserLogin', $nik);
          $this->db->where('isActive', '1');
          $this->db->where('ModuleID', '2');
          
          if ($this->db->get()->row()->val) {
            return TRUE;
          } else {
            return FALSE;
          }
        break;
      default:
        return FALSE;
        break;
    }
  }

}
