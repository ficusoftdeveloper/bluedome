<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
	<title><?php echo $header_pageTitle; ?></title>
	<link rel="shortcut icon" href="favicon.ico" />
	<meta content="width=device-width, initial-scale=1.0" name="viewport">
	<script src="<?php echo base_url() ?>/assets/js/jquery-1.11.1.min.js"></script>
	<script src="<?php echo base_url() ?>assets/js/bootstrap.min.js"></script>
	<script src="<?php echo base_url() ?>assets/js/main.js"></script>
	<link href="<?php echo base_url() ?>assets/css/bootstrap/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.1/css/all.css">
	<!-- Libraries CSS Files -->
	<link href="<?php echo base_url() ?>assets/css/styles.css" rel="stylesheet">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
	<style>
	/* The Modal (background) */
.modal {
  display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 1; /* Sit on top */
  padding-top: 100px; /* Location of the box */
  left: 0;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: rgb(0,0,0); /* Fallback color */
  background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

/* Modal Content */
.modal-content {
  background-color: #fefefe;
  margin: auto;
  padding: 10px;
  border: 1px solid #888;
  width: 80%;
	height: 85%;
}

/* The Close Button */
.close {
  color: #aaaaaa;
  float: right;
  font-size: 28px;
  font-weight: bold;
}

.close:hover,
.close:focus {
  color: #000;
  text-decoration: none;
  cursor: pointer;
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
