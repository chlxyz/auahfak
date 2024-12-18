<div class="container hidden-xs ">
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
<div class="container hidden-xs ">
  <div class="card p-10 m-b-0">
    <nav class="navbar navbar-custom ">
      <div class=" navbar-collapse row" id="bar-top">
        <div class="col-xs-9 col-sm-6 col-md-6 col-lg-4 p-l-20">
          <ul class="nav navbar-nav ">

            <li class=""><?php echo anchor('home','Home <i class="fa fa-home fa-lg"></i>'); ?></li>

            <!-- <li class="dropdown" id="main-menu"> -->
            <li class="dropdown " >
              <?php  $this->load->view('template/main_menu'); ?>
            </li>

            <li class="hidden-xs hidden-sm "><a href="#">User Guide <i class="fa fa-download fa-lg"></i></a></li>
          </ul>
        </div>

        <div class="col-xs-3 col-sm-6 col-md-6 col-lg-8 ">
          <ul class="nav navbar-nav navbar-right ">
            <li class="hidden-xs hidden-sm"><a href="#">Term of Use <i class="fa fa-file-text fa-lg"></i></a></li>
            <li class="hidden-xs hidden-sm"><a href="#">Help <i class="fa fa-question fa-lg"></i></a></li>
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
          </div>
        </div><!-- /.navbar-collapse -->
      </nav>

  </div>
</div><!-- /.container-fluid -->
