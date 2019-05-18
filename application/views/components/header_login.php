<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <title><?php echo $header_pageTitle; ?></title>
    <link rel="shortcut icon" href="favicon.ico" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <script src="<?php echo base_url('assets/js/jquery-1.11.1.min.js') ?>"></script>
    <script src="<?php echo base_url('assets/js/bootstrap.min.js') ?>"></script>
    <script src="<?php echo base_url('assets/js/main.js') ?>"></script>
    <link href="<?php echo base_url('assets/css/bootstrap/bootstrap.min.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.1/css/all.css">
    <!-- Libraries CSS Files -->
    <link href="<?php echo base_url('assets/css/styles.css') ?>" rel="stylesheet">
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/hideshowpassword/2.0.8/hideShowPassword.min.js"></script>
</head>
<body>
	<div class="site-header-main">
	<div class="container">
	<div class="row">
		<?php echo $siteInformation; ?>
		<?php echo $mainNavigation; ?>
	</div>
	</div>
	</div>
