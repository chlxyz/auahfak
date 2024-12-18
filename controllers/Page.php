<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Page extends CI_Controller {

  public function index()
  {
    $this->load->view('test');
  }

  public function login()
  {
    $this->load->view('page/login_form');
  }

  public function error404()
  {
    $this->load->view('page/404_view');
  }

  public function messages()
  {
    $this->load->view('page/messages_view');
    # code...
  }

  public function calendar()
  {
    # code...
  }

}

/* End of file page.php */
/* Location: ./application/controllers/page.php */
