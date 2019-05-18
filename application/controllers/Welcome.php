<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Default controller of Blue Dome application.
 */
class Welcome extends CI_Controller {

	/**
	 * Construct an object of default controller.
	 */
	function __construct() {
		parent::__construct();
		$this->load->helper('welcome');
	}

	/**
	 * Index for default controller.
	 */
	public function index() {
		$data = [];

		//get messages from the session
		if($this->session->userdata('success_msg')){
			$data['success_msg'] = $this->session->userdata('success_msg');
			$this->session->unset_userdata('success_msg');
		}

		if($this->session->userdata('error_msg')){
			$data['error_msg'] = $this->session->userdata('error_msg');
			$this->session->unset_userdata('error_msg');
		}

		// set page title.
		$data['pageTitle'] = "WELCOME DASHBOARD";

		// Load view components for this controller.
		$this->load->view('components/header', header_component());
		$this->load->view('welcome_dashboard', $data);
		$this->load->view('components/footer');
	}
}
