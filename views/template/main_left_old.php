<aside id="sidebar">
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
              # code...
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

      <ul class="main-menu">


        <?php
          if ($this->session->userdata('login_nik')) {
            echo '<li>'.anchor('home', '<i class="md md-home"></i> '. lang('menu_home')).'</li>';
              # code...
          }

          if ($this->account_model->count_module_viewer($this->session->userdata('login_nik'),'')) {
            echo '<li>'.anchor('agenda', '<i class="fa fa-calendar"></i> Agenda CHR', '').'</li>';

          }

          echo build_menu('ROOT','');
        ?>
        <li><?php echo anchor('emp_search', '<i class="fa fa-search"></i> Employee Search', ''); ?></li>;
      </ul>
    </div>
  </div>
</aside>
