<header id="header" class="visible-xs" >
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
        'style' => 'height:60px;margin-top:-15px;margin-left:-15px;');
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
<div class=" visible-xs " style="height:60px">

</div>

<aside id="sidebar" class="visible-xs">
  <div class="sidebar-inner">
    <div class="si-inner">
      <div class="profile-menu">
        <a href="">

          <div class="profile-pic">
            <?php
              echo profile_pic();

            ?>
          </div>

          <div class="profile-info">
            <?php
            if ($this->session->userdata('fullname')) {
              echo $this->session->userdata('fullname');
            }
            ?>
            <i class="md md-arrow-drop-down"></i>
          </div>
        </a>

        <ul class="main-menu">

          <li>
            <?php echo anchor('account/back_portal', '<i class="md md-replay"></i> '. lang('menu_back_portal'), ''); ?>
            <?php echo anchor('account/logout', '<i class="md md-exit-to-app"></i> '. lang('menu_logout'), ''); ?>

          </li>
        </ul>
      </div>
      <div class="row">
        <div class="col-xs-12">
          <ul id="drilldown">
            <!-- <li>
              <a href="">Menu</a>
              <ul> -->
                <?php


                // TODO Membuat Managerial self services
                echo build_menu('ROOT','');
                // echo build_menu('ROOT2','');
                ?>

              <!-- </ul>
            </li> -->
          </ul>

        </div>
      </div>
    </div>
  </div>
</aside>
