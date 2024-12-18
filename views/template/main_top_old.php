<?php
  $this->load->view('template/basic_top');
  echo link_tag('assets/css/style_old.css');
  echo link_tag('assets/material/vendors/sweet-alert/sweet-alert.min.css');
  echo link_tag('assets/material/vendors/summernote/summernote.css');
  echo link_tag('assets/dataTables/dataTables.bootstrap.css');

?>
<body class="toggled sw-toggled">

<header id="header" >
  <ul class="header-inner">
    <li id="menu-trigger" data-trigger="#sidebar">
      <div class="line-wrap" title="Menu">
        <div class="line top"></div>
        <div class="line center"></div>
        <div class="line bottom"></div>
      </div>
    </li>
    <li class="logo">

      <?php
      $attr = array(
        'src'   => 'assets/images/logo/logo.png',
        'class' => '',
        'style' => 'height:80px;margin-top:-30px;margin-left:-15px;');
        echo img($attr);
        ?>
      </li>

    <li class="pull-right" style="padding-top:0px">
    <ul class="top-menu">
      <li id="toggle-width">
        <div class="toggle-switch" >
          <input id="tw-switch" type="checkbox" hidden="hidden" checked="checked">
          <label for="tw-switch" class="ts-helper"></label>
        </div>
      </li>

      <li class="dropdown">
        <a data-toggle="dropdown" class="tm-settings" href=""></a>
        <ul class="dropdown-menu dm-icon pull-right">
          <li>
            <a data-action="fullscreen" href=""><i class="md md-fullscreen"></i> Toggle Fullscreen</a>
          </li>
          <li>
            <a data-action="clear-localstorage" href=""><i class="md md-delete"></i> Clear Local Storage</a>
          </li>
          <li>
            <a href=""><i class="md md-person"></i> Privacy Settings</a>
          </li>
          <li>
            <a href=""><i class="md md-settings"></i> Other Settings</a>
          </li>
        </ul>
      </li>
      <li class="hidden-xs" id="chat-trigger" data-trigger="#chat">
        <a class="tm-chat" href=""></a>
      </li>
      </ul>
    </li>
  </ul>

</header>
