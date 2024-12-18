<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ppo_admin extends CI_Controller {

  public function __construct()
  {
    parent::__construct();
    $langArr = array('act','menu','basic','time','confirm','notif');
    foreach ($langArr as $key => $value) {
      $this->lang->load($value,$this->session->userdata('site_lang'));
    }
    $this->load->model('legal/pp_model');
    $this->load->model('account_model');
  }

  public function index()
  {
    $data['ls'] = $this->pp_model->get_all_list();
    $this->load->view('legal/ppo/header_list', $data, FALSE);
  }

  public function add_header()
  {
    $data['modal_title'] = 'Add PP';
    $data['process'] = 'legal/ppo/add_header_process';
    $data['hidden']  = array();
    $opt = $this->pp_model->get_area();
    foreach ($opt as $row) {
      $area_opt[$row->pers_area] = $row->area_name;
    }
    $data['area_opt']  = $area_opt;
    $data['year']      = date('Y');
    $data['pers_area'] = '';
    $data['title']     = '';
    $data['start']     = sql_to_datepicker(date('Y-m-d'));
    $data['end']       = sql_to_datepicker(date('Y-m-d'));
    $data['pre_title'] = '';
    $data['pre_text']  = '';
    $data['is_active'] = FALSE;
    echo $this->load->view('legal/ppo/header_form', $data, TRUE);

  }

  public function add_header_process()
  {
    $year      = $this->input->post('txt_year');
    $area      = $this->input->post('slc_area');
    $title     = $this->input->post('txt_title');
    $start     = $this->input->post('dt_start');
    $end       = $this->input->post('dt_end');
    $pre_title = $this->input->post('txt_pretitle');
    $pre_text  = $this->input->post('txt_pretext');
    $is_active = $this->input->post('chk_status');

    $this->pp_model->add_header($year,$area,$title,$start,$end,$per_title,$pre_text,$is_active);
    redirect('legal/ppo');

  }

  public function edit_header($year='',$pers_area='')
  {
    $opt = $this->pp_model->get_area();
    foreach ($opt as $row) {
      $area_opt[$row->pers_area] = $row->area_name;
    }
    $data['area_opt']  = $area_opt;
    $data['hidden']    = array('year' => $year, 'area' => $pers_area);
    $old = $this->pp_model->get_header_row($year,$pers_area);
    $data['title']     = $old->pp_title;
    $data['start']     = $old->start_date;
    $data['end']       = $old->end_date;
    $data['pre_title'] = $old->pre_title;
    $data['pre_text']  = $old->pre_text;
    $data['is_active'] = $old->is_active;

    $data['modal_title'] = 'Edit PP';
    $data['process']     = 'legal/ppo/edit_header_process';

    $data['year']      = $old->pp_year;
    $data['pers_area'] = $old->pers_area;
    // $this->load->view('legal/ppo/header_form', $data, true);
    echo $this->load->view('legal/ppo/header_form', $data, TRUE);


  }

  public function edit_header_process()
  {
    $year      = $this->input->post('year');
    $area      = $this->input->post('area');
    $title     = $this->input->post('txt_title');
    $start     = $this->input->post('dt_start');
    $end       = $this->input->post('dt_end');
    $pre_title = $this->input->post('txt_pretitle');
    $pre_text  = $this->input->post('txt_pretext');
    $is_active = $this->input->post('chk_status');
    $this->pp_model->edit_header($year,$area,$title,$start,$end,$per_title,$pre_text,$is_active);
    redirect('legal/ppo');

  }

}

/* End of file Ppo.php */
/* Location: ./application/controllers/legal/Ppo.php */
