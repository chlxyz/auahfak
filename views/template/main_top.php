<?php
  $this->load->view('template/basic_top');

  echo link_tag('assets/material/vendors/sweet-alert/sweet-alert.min.css');
  echo link_tag('assets/material/vendors/summernote/summernote.css');
  echo link_tag('assets/dataTables/dataTables.bootstrap.css');

  echo link_tag('assets/Bootstrap-DrillDownMenu/bootstrap.drilldown.css');
  echo link_tag('assets/css/style.css');

  $this->load->view('template/header-animation');
  $this->benchmark->mark('code_start');

?>

<body class="">
<div class="container">
  <div class="card  m-b-0">
    <div class="row">
      <div class="col-lg-6 col-sm-4 col-xs-12" style="z-index:100">
        <?php
        $attr = array(
          'src'   => 'assets/images/logo/logo.png',
          'class' => 'image-responsive',
          'style' => '');
          echo img($attr);

          ?>
      </div>
      <div class="col-lg-6 col-sm-8 pull-right hidden-xs">
        <div id="swiffycontainer"  style="width: 553px; height: 99px;float:right">

        </div>

      </div>

    </div>

  </div>
</div>
<style>

</style>
<div class="container ">
  <div class="card  p-10 m-b-0">
    <nav class="navbar navbar-custom ">
      <div class=" navbar-collapse row" id="bar-top">

          <ul class="nav navbar-nav pull-left m-l-5">
            <li class="pull-left ">
              <?php echo anchor('account/back_portal/home.php', lang('menu_home').' <i class="fa fa-home fa-lg"></i>'); ?>

              <!-- <?php echo anchor('home','Home <i class="fa fa-comment fa-lg"></i>'); ?> -->
            </li>

            <li id="main-menu" class="pull-left">
            </li>

            <li class="hidden-xs hidden-sm "><a href="#">User Guide <i class="fa fa-download fa-lg"></i></a></li>
          </ul>

          <ul class="nav navbar-nav pull-right m-r-5">
            <li class="hidden-xs hidden-sm"><a href="#">Term of Use <i class="fa fa-file-text fa-lg"></i></a></li>
            <li class="hidden-xs hidden-sm"><a href="#">Help <i class="fa fa-question fa-lg"></i> </a></li>
            <li class="dropdown"><a href="#" data-toggle="dropdown">Language <i class="fa fa-comment fa-lg"></i> <span class="caret"></span></a>
              <ul class="dropdown-menu" aria-labelledby="dLabel">
                <li><a href="#" class="btn-lang" data-lang="english">English</a></li>
                <li><a href="#" class="btn-lang" data-lang="indonesia">Indonesia</a></li>
              </ul>
            </li>
            <li><?php echo anchor('account/logout','Logout <i class="fa fa-power-off fa-lg"></i>') ?></li>

            <li class="hidden-xs logo">
              <?php
              $attr = array(
                'src'   => 'assets/images/logo_kg_white_on_trans.png',
                'class' => '',
                'style' => 'height:30px;margin-top:10px');
                echo img($attr);
                ?>
              </li>

            </ul>


        </div><!-- /.navbar-collapse -->
      </nav>

  </div>
</div><!-- /.container-fluid -->
