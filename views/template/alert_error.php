<div class="alert alert-danger alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  <strong>Error!</strong> <?php
    if (isset($msg)) {
      echo $msg;
    }
  ?>
</div>
