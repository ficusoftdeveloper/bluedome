<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
	<title><?php echo $header_pageTitle; ?></title>
	<link rel="shortcut icon" href="favicon.ico" />
	<meta content="width=device-width, initial-scale=1.0" name="viewport">
	<link href="<?php echo base_url() ?>assets/css/bootstrap/bootstrap.min.css" rel="stylesheet">
	<script src="<?php echo base_url() ?>/assets/js/jquery-1.11.1.min.js"></script>
	<script src="<?php echo base_url() ?>assets/js/bootstrap.min.js"></script>
	<script src="<?php echo base_url() ?>assets/js/main.js"></script>
	<script src="<?php echo base_url() ?>assets/js/draw.js"></script>

	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.1/css/all.css">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
	<!-- Libraries CSS Files -->
	<link href="<?php echo base_url() ?>assets/css/styles.css" rel="stylesheet">
	<style>
       /* Set the size of the div element that contains the map */
      #map {
        height: 650px;  /* The height is 400 pixels */
        width: 100%;  /* The width is the width of the web page */
       }
  </style>
</head>
<body>
	<header id="header" class="my-to-header">
	<div class="site-header-main">
	<div class="container">
	<div class="row">
		<?php echo $siteInformation; ?>
		<?php echo $mainNavigation; ?>
	</div>
	</div>
	</div>
	</header>
