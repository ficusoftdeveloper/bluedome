<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Inspection controller class.
 */
class Inspection extends CI_Controller {

	/**
	 * Construct an object of inspection controller.
	 */
	function __construct() {
		parent::__construct();
		$this->load->helper('form');
		$this->load->library('upload');
		$this->load->library('session');
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->load->model('media/filemanaged');
		$this->load->helper('welcome');
		$this->load->helper('inspection');
	}

	/**
	 * Index page of inspection controller.
	 * @return [type] [description]
	 */
	public function index() {
		$files = $this->filemanaged->getRows();
		$processedFiles = $this->filemanaged->getProcessedRows();
		$reportFiles = $this->filemanaged->getEnabledRows();

		// Manage views components.
		$this->load->view('components/inspection_header', header_component());
		$this->load->view('inspection/index', inspection_component($this->filemanaged, $this->session));
		$this->load->view('components/inspection_footer');
	}
}
