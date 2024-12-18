<div class="container" id="tb1" style="margin-bottom: 20px;"> <!-- In -->				
</div>
<script src="<?php echo base_url()?>assets/material/js/jquery-2.1.1.min.js" type="text/javascript"></script>
<script type="text/javascript">
		var base_url = '<?php echo base_url() ?>' + 'index.php/';
		function loadTb(){
			$('#tb1').load(base_url+'pa/cli/upload',function () {
			    $(this).unwrap();
			});
		}
		setInterval(function(){
		    loadTb(); // this will run after every 5 minutes

		}, 300000);
</script>