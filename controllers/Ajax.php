<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends CI_Controller{

  public function __construct()
  {
    parent::__construct();
    $this->load->library('session');
    $this->load->helper('url');
    $this->load->helper('language');

    //Codeigniter : Write Less Do More
    $langArr = array('act','menu','basic','time','confirm','notif');
    foreach ($langArr as $key => $value) {
      $this->lang->load($value,$this->session->userdata('site_lang'));
    }
  }

  function index()
  {

  }

  public function org_breadcrumb()
  {
    $this->load->model('om_model');

    $org_id  = $this->input->post('org_id');
    $temp    = array();
    $rest    = array();
    $i       = 0;
    if ($org_id > 0) {
      do {
        $org = $this->om_model->get_org($org_id)->org_parent;
        $temp[$i] = array('id' => $org->org_id, 'label' => $org->org_name);
        $i++;
        $org_id = $org->org_parent;
      } while ($org->org_parent > 50002147);
    }
    $temp[$i] = array('id' => 0, 'label' => 'ROOT');
    for ($x=$i; $x >= 0 ; $x--) {
      $rest[$i-$x] = $temp[$x];
    }
    echo json_encode($rest);

  }

  public function org_branch()
  {
    $this->load->model('om_model');
    $temp   = array();
    $org_id = $this->input->post('org_id');
    if ($org_id == 0 OR $org_id == 50002147) {
      $pers_admin = $this->session->userdata('pers_admin');
      // $pers_admin =
      $org_ls = $this->om_model->get_orgs_root($pers_admin);

    } else {
      $org_ls = $this->om_model->get_orgs_child($org_id);
    }

    if (count($org_ls)) {
      $i = 0;
      foreach ($org_ls as $org) {
        $temp[$i] = array(
          'id'    => $org->org_id,
          'label' => $org->org_name
        );
        $i++;
      }

    }
    echo json_encode($temp);

  }

  public function org_holder()
  {

  }

  public function org_unhold()
  {
    $org_id = $this->input->post('org_id');
    $ls = $this->om_model->get_posts_vacant($org_id,0,$start_date);
    $i = 0;
    foreach ($list as $row) {
      $temp[$i] = array(
        'id'    => $row->post_id,
        'label' => $row->post_name
      );
      $i++;
    }
  }

  // public function build_menu($parent = 'ROOT',$result='')
  // {
  //   $this->load->model('account_model');
  //   $result = '';
  //   $temp = $this->account_model->get_menu_ls('',$parent);
  //
  //   foreach ($temp as $row) {
  //     $count = $this->account_model->count_menu('',$row->menu_code);
  //     if ($count > 0 ) {
  //       $result .= '<li class="sub-menu">';
  //       $result .= '<a href="">';
  //
  //       if ($row->menu_icon != '') {
  //         $result .= '<i class="'.$row->menu_icon.' fa-lg"></i> ';
  //       }
  //
  //       switch ($row->is_lang) {
  //         case TRUE:
  //           $result .= lang($row->menu_lang);
  //           break;
  //
  //         case FALSE:
  //           $result .= $row->menu_name;
  //           break;
  //       }
  //
  //       $result .= '</a>';
  //       $result .= '<ul>';
  //       $result .= build_menu($row->menu_code,$result);
  //       $result .= '</ul>';
  //       $result .= '</li>';
  //     } else {
  //       $result .= '<li>';
  //
  //       if ($row->menu_icon != '') {
  //         $icon = '<i class="'.$row->menu_icon.' fa-lg"></i> ';
  //       } else {
  //         $icon = '';
  //       }
  //
  //       if ($row->is_lang) {
  //         $menu_text = lang($row->menu_lang);
  //       } else {
  //         $menu_text = $row->menu_name;
  //
  //       }
  //
  //       if ($row->file_loc == 'CI') {
  //         $result .= anchor($row->menu_url, $icon. $menu_text);
  //
  //       } elseif ($row->file_loc == 'PHP') {
  //         $result .= anchor('account/back_portal/'.$row->menu_url, $icon. $menu_text);
  //
  //       } else {
  //         $result .= '<a href="">'.$icon. $menu_text.'</a>';
  //       }
  //
  //       $result .= '</li>';
  //     }
  //   }
  //   echo $result;
  // }

  public function menu_breadcrumb()
  {
    $this->load->model('menu_model');
    $menu_code = $this->input->post('menu_code');
    $temp = array();
    $rest = array();
    $i      = 0;
    if ($menu_code != 'ROOT') {
      do {
        $menu = $this->menu_model->get_menu_row($menu_code);
        if ($menu->is_lang == TRUE) {
          $temp[$i] = array('code' => $menu->menu_code, 'label' => lang($menu->menu_lang));
        } else {
          $temp[$i] = array('code' => $menu->menu_code, 'label' => $menu->menu_name);
        }
        $i++;
        $menu_code = $menu->parent_code;
      } while ($menu->parent_code != 'ROOT');
    }
    $temp[$i] = array('code' => 'ROOT', 'label' =>'All');
    for ($x=$i; $x >= 0 ; $x--) {
      $rest[$i-$x] = $temp[$x];
    }
    
    echo json_encode($rest);

  }

  public function menu_branch()
  {
    $this->load->model('menu_model');
    $parent_code = $this->input->post('menu_code');
    // $parent_code = 'ROOT';
    // $cache = $this->cache->memcached->get('menu');
    // if ($cache) {
    //   $temp = $cache;
    // } else {
      $temp = $this->menu_model->get_menu_ls('',$parent_code);
    //   $this->cache->memcached->save('menu', $temp, 300);
    //
    // }
    $result = array();
    $i = 0;
    foreach ($temp as $row) {
      $rec = array();
      $rec['code'] = $row->menu_code;
      $rec['sub'] = $row->count_sub;

      if ($row->count_sub == 0) {
        if ($row->file_loc == 'CI') {
          $rec['url'] = base_url().'index.php/'.$row->menu_url;
        } elseif ($row->file_loc == 'PHP') {
          $rec['url'] = base_url().'index.php/account/back_portal/'.$row->menu_url;

        }
      }

      if ($row->menu_icon != '' or ! is_null($row->menu_icon)) {
        $rec['icon'] = $row->menu_icon;


      } else {
        $rec['icon'] = '';

      }

      if ($row->is_lang == FALSE) {
        $rec['label'] = $row->menu_name;

      } else {
        $rec['label'] = lang($row->menu_lang);

      }
      $result[$i] = $rec;
      $i++;
    }

    echo json_encode($result);

  }

  public function notif_json()
  {
    $this->load->model('menu_model');
    $result = array();
    // Leave Request
    // $count = $this->menu_model->check_query('count_leavreq');
    $count = 0;
    if ($count > 0) {
      $result[] = array(
        'title'   => 'Leave Request',
        'count'   => $count,
        'message' => 'Need approval '.$count .' request(s)',
        'url'     => base_url().'index.php/account/back_portal/'
      );
    }

    // Attendance Request
    // $count = $this->menu_model->check_query('count_attreq');
    $count = 0;
    if ($count > 0) {
      $result[] = array(
        'title'   => 'Attandance Request',
        'count'   => $count,
        'message' => 'Need approval '.$count .' request(s)',
        'url'     => base_url().'index.php/account/back_portal/reqAttendanceApproval.php'
      );
    }




    echo json_encode($result);

  }

  public function switch_lang()
	{
    $lang = $this->input->post('lang');
    $this->session->set_userdata('site_lang',$lang);
	}

}
