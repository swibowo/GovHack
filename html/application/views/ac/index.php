<?php

// 2012-06-02
// enrico.simonetti@gmail.com & seandy.wibowo@gmail.com

$this->load->view('head');

?>

<div id="container">
	<div id="header">
		<span class="heading"><?php echo !empty($title) ? $title : ''; ?></span>
	</div>
	<hr/>
	<div id="chronicle">
		<div id="chronicle-container">
		</div>
	</div>
	<div id="maps">
	</div>
</div>

<script>
  $(function(){
    
    //$('#chronicle').masonry({itemSelector: '.box'});
    
  });
</script>

<?php
$this->load->view('foot');
