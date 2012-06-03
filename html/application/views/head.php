<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="utf-8"
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"/>
	<title><?php echo !empty($title) ? $title : ''; ?></title>

	<script type="text/javascript">
	var base_url = '<?php echo base_url();?>';
	var selected_year = '<?php echo !empty($selected_year) ? "/".$selected_year : "/0"; ?>';
	var selected_category = '<?php echo !empty($selected_category) ? "/".$selected_category : "";  ?>';
	</script>

	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript" >
	</script>
	<script src="<?php echo site_url('assets/js/ac.js'); ?>" type="text/javascript" ></script>
	<script src="<?php echo site_url('assets/js/jquery.masonry.js'); ?>" type="text/javascript"></script>
	<link href="<?php echo site_url('assets/css/main.css'); ?>?ts=<? echo strtotime(date("Y-m-d H:i:s")); ?>" rel="stylesheet" type="text/css">
</head>
<body id="ac_body">
