<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Page controller class.
 * Manage pages on site.
 */
class Pages extends CI_Controller {

	/**
	 * construct an object of page controller class.
	 */
	function __construct() {
		parent::__construct();
		$this->load->helper('welcome');
	}

	/**
	 * Contact page.
	 */
	public function contact() {
		$this->load->view('components/header', header_component());
		$this->load->view('pages/contact');
		$this->load->view('components/footer');
	}

	/**
	 * Solution Page.
	 */
	public function solution() {
		$this->load->view('components/header', header_component());
		$this->load->view('pages/solution');
		$this->load->view('components/footer');
	}

	/**
	 * Inspection page.
	 */
	public function inspection() {
		$this->load->view('components/header', header_component());
		$this->load->view('pages/inspection');
		$this->load->view('components/footer');
	}
}
