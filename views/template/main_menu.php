<a tabindex="10" class="fg-button fg-button-icon-right dropdown-toggle" id="btn-main-menu">Menu <i class="fa fa-caret-down"></i></a>
<!-- <div id="news-items-2" class="hidden"> -->
<div class="dropdown-menu dropdown-menu-lg pull-right" id="content-main-menu" >
  <div class="listview" >

    <div class="lv-body">
      <ul id="drilldown">
        <?php
        $this->benchmark->mark('code_start');
        echo build_menu('ROOT','');
        $this->benchmark->mark('code_end');
        echo '<li><a href="#">'.$this->benchmark->elapsed_time('code_start', 'code_end').'</a></li>';
        ?>
      </ul>
    </div>
    <!-- <div class="lv-body" id="main-menu"> -->
      <!-- <ol class="breadcrumb p-10" >
          <li><a href="#">Home</a></li>
          <li><a href="#">Library</a></li>
          <li><a href="#">Home</a></li>
          <li><a href="#">Library</a></li>
          <li><a href="#">Home</a></li>
          <li><a href="#">Library</a></li>
          <li class="active">Data</li>
      </ol>
      <div class="drilldown">
        <ul class="drilldown-menu p-l-15 p-r-15">
          <li style="height:38px"><a href="">Test</a></li>
          <li style="height:38px"><a href="">Test</a></li>
          <li style="height:38px"><a href="">Test</a></li>
          <li style="height:38px"><a href="">Test</a></li>
          <li style="height:38px"><a href="">Test</a></li>
        </ul>

      </div> -->
    <!-- </div> -->
  </div>
</div>
