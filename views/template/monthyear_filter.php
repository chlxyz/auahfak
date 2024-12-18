<!-- <div class="card">
  <div class="card-body card-padding" > -->
    <!-- <div class="row"> -->
      <div class="input-group">
        <span class="input-group-addon"><i class="md md-today"></i></span>

        <div class="col-md-1 col-xs-6">
          <div class="fg-line select">
            <?php
            $month_opt = array();
            for ($i=1; $i <= 12 ; $i++) {
              $month_opt[$i] = lang('time_M_'.$i);
            }

            echo form_dropdown('slc_month', $month_opt, date('m'), 'class="form-control filter"');

            ?>
          </div>
        </div>
        <div class="col-md-2 col-xs-6">
          <div class="fg-line select">
            <?php
            $year_opt = array();
            for ($i=2015; $i <= 2020 ; $i++) {
              $year_opt[$i] = $i;
            }
            echo form_dropdown('slc_year', $year_opt, date('Y'), 'class="form-control filter"');

            ?>
          </div>
        </div>
      </div>
    <!-- </div> -->
  <!-- </div>
</div> -->
