<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {

  public function index()
  {
    $this->load->driver('cache', array('adapter' => 'memcached', 'backup' => 'file'));

    if($this->cache->memcached->is_supported()){
      echo 'supported';

    } else {
      echo 'Not supported';

    }
    if ( ! $foo = $this->cache->memcached->get('foo2'))
    {
      echo 'Saving to the cache!<br />';
      $foo = 'foobarbaz!';

      // Save into the cache for 5 minutes
      $this->cache->memcached->save('foo2', $foo, 300);
      echo $this->cache->get_metadata('foo2');
    } else{
      echo 'Load from the cache!<br />';

    }

      echo $foo;

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

  public function test2()
  {
    $this->load->view('test_1');

  }

  public function test_p()
  {
    $data['a'] = $this->input->post('txt');
    echo json_encode($data);
  }

}

/* End of file page.php */
/* Location: ./application/controllers/page.php */
