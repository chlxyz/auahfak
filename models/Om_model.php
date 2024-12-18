<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Om_model extends CI_Model
{

  /**
   * [get_persadmins description]
   * @return [type] [description]
   */
  public function get_persadmins()
  {
    $this->db->from('HRSS_PersAdmin');
    $this->db->order_by('PersAdmin_Text');
    return $this->db->get()->result();
  }

  /**
   * [get_orgs_persadmin description]
   * @param  string $pers_admin [description]
   * @return [type]             [description]
   */
  public function get_orgs_persadmin($pers_admin = '')
  {
    $this->db->where('PersAdmin_Id', $pers_admin);
    // return $this->db->get('HRSS_PersAdminOrganization')->result();
    return $this->db->get('ms_PersAdminOrganization')->result();
  }

  public function get_persadmin_parent($persadmin = '')
  {
    $this->db->from("HRSS_PersAdmin");
    $this->db->where("PersAdmin_Id", $persadmin);
    $this->db->order_by("PersAdmin_parent", "DESC");
    return $this->db->get()->row();
  }

  /**
   * [get_orgs_root description]
   * @param  string $pers_admin [description]
   * @param  string $begin      [description]
   * @param  string $end        [description]
   * @return [type]             [description]
   */
  public function get_orgs_root($pers_admin = '', $begin = '', $end = '')
  {
    if ($begin == '') {
      $begin = date('Y-m-d');
    }

    if ($end == '') {
      $end = $begin;
    }
    $this->db->select('o.OrganizationID AS org_id');
    $this->db->select('o.OrganizationName AS org_name');
    $this->db->select('o.OrganizationParent AS org_parent');
    $this->db->select('o.BeginDate AS [begin]');
    $this->db->select('o.EndDate AS [end]');
    $this->db->from('core_m_org o');

    if ($pers_admin != '') {
      $this->db->where("o.OrganizationID IN (SELECT Organization_Id FROM ms_PersAdminOrganization WHERE PersAdmin_Id = '$pers_admin')");
    }

    $this->db->where("((o.BeginDate >= '$begin' AND o.EndDate <='$end') OR
                (o.EndDate >= '$begin' AND o.EndDate <= '$end') OR
                (o.BeginDate >= '$begin' AND o.BeginDate <='$end' ) OR
                (o.BeginDate <= '$begin' AND o.EndDate >= '$end'))");
    $this->db->order_by('o.OrganizationName');

    return $this->db->get()->result();

  }

  /**
   * [get_orgs_child description]
   * @param  integer $org_parent [description]
   * @param  string  $begin      [description]
   * @param  string  $end        [description]
   * @return [type]              [description]
   */
  public function get_orgs_child($org_parent = 0, $date = '')
  {
    $this->load->helper('date');
    $this->load->library('saprfc');
    if ($date == '') {
      $date = date('Ymd');
    }

    $this->saprfc->connect();
    $this->saprfc->functionDiscover('ZHRFM_LISTORGANISASI');
    $importParamName = array(
      'DEPTH',
      'KEYDATE',
      'OBJID'
    );
    $importParamValue = array(
      2,
      $date,
      $org_parent
    );
    $this->saprfc->importParameter($importParamName, $importParamValue);
    $this->saprfc->setInitTable('T_OBJECTSDATA');
    $this->saprfc->executeSAP();
    $obj = $this->saprfc->fetch_rows('T_OBJECTSDATA', 'object', 0, 1);
    $this->saprfc->free();
    $this->saprfc->close();
    if (is_null($obj) == FALSE) {
      foreach ($obj as $row) {
        $temp['org_id'] = $row->OBJECT_ID;
        $temp['org_name'] = $row->LONG_TEXT;
        $temp['org_code'] = $row->SHORT_TEXT;
        $temp['begin'] = date_from_sap($row->START_DATE);
        $temp['end'] = date_from_sap($row->END_DATE);
        $result[] = (object) $temp;
      }
      return $result;
    } else {
      return array();
    }
  }

  public function get_org($org_id = 0, $date = '')
  {

    $this->load->helper('date');
    $this->load->library('saprfc');
    if ($date == '') {
      $date = date('Ymd');
    }

    $this->saprfc->connect();
    $this->saprfc->functionDiscover('ZHRFM_LISTORGANISASI');
    $importParamName = array(
      'DEPTH',
      'KEYDATE',
      'OBJID'
    );
    $importParamValue = array(
      1,
      $date,
      $org_id
    );
    $this->saprfc->importParameter($importParamName, $importParamValue);
    $this->saprfc->setInitTable('T_OBJECTSDATA');
    $this->saprfc->executeSAP();
    $obj = $this->saprfc->fetch_rows('T_OBJECTSDATA', 'object');
    $this->saprfc->free();
    $this->saprfc->close();
    if (is_null($obj) == FALSE) {
      foreach ($obj as $row) {
        $temp['org_id'] = $row->OBJECT_ID;
        $temp['org_name'] = $row->LONG_TEXT;
        $temp['org_parent'] = $row->EXT_OBJ_ID;
        $temp['org_code'] = $row->SHORT_TEXT;
        $temp['begin'] = date_from_sap($row->START_DATE);
        $temp['end'] = date_from_sap($row->END_DATE);
        $result = (object) $temp;
      }
      return $result;
    } else {
      return array();
    }
  }

  public function get_posts_vacant($org_id = 0, $old_post = 0, $date = '')
  {
    $this->load->library('saprfc');

    if ($date == '') {
      $date = date('Ymd');
    }

    $this->db->select('position_id');
    $this->db->from('hcm_pa_taction ');
    $this->db->where('sap_flag', 0);
    if ($old_post > 0) {
      $this->db->where('position_id <>', $old_post);
    }
    $temp = $this->db->get()->result();

    $fill_post = array();
    foreach ($temp as $row) {
      $fill_post[] = $row->position_id;
    }

    $this->saprfc->connect();
    $this->saprfc->functionDiscover('ZHRFM_GETPOSITIONVACANT');
    $importParamName = array(
      'FI_ORG',
      'FI_TANGGAL'
    );
    $importParamValue = array(
      $org_id,
      $date
    );

    $this->saprfc->importParameter($importParamName, $importParamValue);
    $this->saprfc->setInitTable('FO_DATA');
    $this->saprfc->executeSAP();
    $obj = $this->saprfc->fetch_rows('FO_DATA', 'object');

    $this->saprfc->free();
    $this->saprfc->close();

    $result = array();
    $final = array();
    $i = 0;
    if (count($fill_post) > 0) {
      if (count($obj)) {
        foreach ($obj as $row) {
          if (in_array($row->PLANS, $fill_post) == FALSE) {

            $result['post_id'] = $row->PLANS;
            $result['post_name'] = $row->ENAME;
            $final[$i] = (object) $result;
            $i++;
          }
        }
      }

    } else {
      foreach ($obj as $row) {

        $result['post_id'] = $row->PLANS;
        $result['post_name'] = $row->ENAME;
        $final[$i] = (object) $result;
        $i++;

      }
    }
    return $final;
  }


  public function get_post($post_id = 0, $begin = '', $end = '')
  {
    // TODO PERBAAIKI bagian IntlRuleBasedBreakIterator
    if ($begin == '') {
      $begin = date('Y-m-d');
    }

    if ($end == '') {
      $end = $begin;
    }

    $this->db->select('p.OrganizationID AS org_id');
    $this->db->select('p.PositionID AS post_id');
    $this->db->select('p.PositionName AS post_name');
    $this->db->select('p.Chief AS chief_status');
    $this->db->select('p.PositionGroup AS esg');

    $this->db->select('BeginDate AS [begin]');
    $this->db->select('EndDate AS [end]');
    $this->db->from('Core_M_Position_SAP p');
    $this->db->where('p.PositionID', $post_id);
    $this->db->where("((p.BeginDate >= '$begin' AND p.EndDate <='$end') OR
                (p.EndDate >= '$begin' AND p.EndDate <= '$end') OR
                (p.BeginDate >= '$begin' AND p.BeginDate <='$end' ) OR
                (p.BeginDate <= '$begin' AND p.EndDate >= '$end'))");
    $this->db->order_by('p.EndDate', 'desc');
    return $this->db->get()->row();

  }

  public function get_post_sap_row($post_id = 0, $keydate = '', $depth = 0)
  {
    # ZHRFM_GETORGPOSDETAIL
    if ($keydate == '') {
      $keydate = date('Ymd');
    } else {
      $keydate = date('Ymd', strtotime($keydate));
    }
    $this->load->library('saprfc');
    $this->saprfc->connect();
    $this->saprfc->functionDiscover('ZHRFM_GETORGPOSDETAIL');
    $importParamName = array(
      'DEPTH',
      'KEYDATE',
      'OBJID'
    );
    $importParamValue = array(
      $depth,
      $keydate,
      $post_id
    );


    $this->saprfc->importParameter($importParamName, $importParamValue);

    $this->saprfc->setInitTable('T_OBJECTSDATA');
    $this->saprfc->executeSAP();
    $org_ls = $this->saprfc->fetch_rows('T_OBJECTSDATA', 'object');
    $post_name = $this->saprfc->getParameter('T_POSITIONNAME');
    $this->saprfc->free();
    $this->saprfc->close();

    $org_temp = array();
    $count = count($org_ls);

    for ($i = 1; $i < $count; $i++) {
      $x = $i - 1;
      $org_temp[$x] = $org_ls[$i]->LONG_TEXT;
    }

    $org_id = $org_ls[1]->OBJECT_ID;
    $org_name = array_reverse($org_temp);

    $result = array(
      'post_name' => $post_name,
      'org_name' => implode(' - ', $org_name),
      'org_id' => $org_id,
    );

    return (object) $result;
  }

  public function get_post_pers_area($post_id = '', $keydate = '')
  {
    if ($keydate == '') {
      $keydate = date('Ymd');
    }
    $this->load->library('saprfc');
    $this->saprfc->connect();
    $this->saprfc->functionDiscover('ZHRFM_GETPTFROMPOSITION');
    $importParamName = array(
      'FI_POS',
      'FI_TANGGAL'
    );
    $importParamValue = array(
      $post_id,
      $keydate
    );
    $this->saprfc->importParameter($importParamName, $importParamValue);

    $this->saprfc->executeSAP();
    $result = $this->saprfc->getParameter('FI_PT');
    $this->saprfc->free();
    $this->saprfc->close();

    return $result;
  }

  public function get_holder_byOrg($org_id = 0)
  {
    $i = 9;
    do {
      $i--;
      $this->db->select('count(nt.NIK) as val');
      $this->db->from('ms_EmpOrg eo');
      $this->db->join('ms_NikTelp nt', 'SUBSTRING(eo.NIK,3,6) = nt.NIK');
      $this->db->where('Unit' . $i, $org_id);
      $count = $this->db->get()->row()->val;
    } while ($count == 0);

    $this->db->select('nt.NIK as nik');
    $this->db->select('nt.Nama as name');
    $this->db->select('eo.Posisi as post_id');
    $this->db->select('eo.PosisiTxt as post_name');
    $this->db->select('eo.isChief as is_chief');
    $this->db->select('eo.isMain as is_main');
    $this->db->from('ms_EmpOrg eo');
    $this->db->join('ms_NikTelp nt', 'SUBSTRING(eo.NIK,3,6) = nt.NIK');
    $this->db->where('Unit' . $i, $org_id);

    return $this->db->get()->result();

  }

  public function get_emp_unitAbbrv($NIK = '')
  {
    $this->db->select('UnitAbbrv');
    $this->db->from('ms_EmpOrg');
    $this->db->where('NIK', '00' . $NIK);

    return $this->db->get()->row()->UnitAbbrv;
  }

  public function get_emp_position($NIK = '', $orgid = '')
  {
    $abbrv = substr($this->get_emp_unitAbbrv($NIK), 0, 1);
    // var_dump($this->get_emp_unitAbbrv($NIK));

    $this->db->select('Posisi, PosisiTxt');
    $this->db->from('ms_EmpOrg');
    $this->db->where('NIK', '00' . $NIK);

    if (isset($orgid)) {
      if (($abbrv != '' && $abbrv != null) && $abbrv < 9) {
        $this->db->where('Unit' . $abbrv, $orgid);
      }
    }
    $query = $this->db->get();

    // var_dump($query->num_rows());
    if ($query->num_rows() > 0) {
      $posisi->post_name = $query->row()->PosisiTxt;
      $posisi->post_id = $query->row()->Posisi;
      return $posisi;
    } else {
      $posisi->post_name = NULL;
      $posisi->post_id = NULL;
      return $posisi;
    }
  }

}

/* End of file om_model.php */
/* Location: ./application/models/om_model.php */
