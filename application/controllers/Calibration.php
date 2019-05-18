<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Calibration controller class.
 */
class Calibration extends CI_Controller {

	/**
	 * Directory path of source raw files.
	 * @var string
	 */
	private $rawSourceUploadPath = "uploads/raw/";

	/**
	 * Construct an object of calibration controller class.
	 */
	function __construct() {
		parent::__construct();
		$this->load->helper('form');
		$this->load->library('upload');
		$this->load->library('session');
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->load->model('media/filemanaged');
		$this->load->helper('media/media');
	}

	/**
	 * Save callback action of calibration.
	 */
	public function save() {
		$fid = $this->input->post('fid');
		$distance = $this->input->post('distance');
		$is_image_dim = ($distance) ? 1 : 0;
		$unit = $this->input->post('unit');
		$file = $this->filemanaged->getRows($fid);
		$imgBase64 = $this->input->post('imgBase64');
		if (!empty($file)) {
			$fileextension = parse_file_type($file['filetype']);
			$img = str_replace('data:image/png;base64,', '', $imgBase64);
			$img = str_replace(' ', '+', $img);
			$data = base64_decode($img);
			$filenamewithoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $file['filename']);
			$file_data = $this->rawSourceUploadPath . $filenamewithoutExt . '_calibrated' . $fileextension;
			$success = file_put_contents($file_data, $data);
			if ($success) {
				// Set file as calibrated in database.
				$update = [
					'calibrated_filename' => $filenamewithoutExt . '_calibrated' . $fileextension,
					'is_calibrated' => 1,
				];

				// Insert file properties.
				$file_props = [
					'distance_poo' => $distance,
					'unit_poo' => $unit,
					'is_image_dim' => $is_image_dim,
					'date_modified' => date('m/d/Y'),
				];

				// Update file properties.
				$insert = $this->filemanaged->updateFileProps($fid, $file_props);

				// Update file managed as calibrated file.
				$update_status = $this->filemanaged->update($update, $fid);
				if ($update_status) {
					echo json_encode($success);
				}
			}
		}
	}

	/**
	 * Reset calibration to its initial state.
	 */
	public function reset() {
		$fid = $this->input->post('fid');
		$file = $this->filemanaged->getRows($fid);
		if (!empty($file)) {
			if ($file['is_calibrated']) {
				$fileextension = parse_file_type($file['filetype']);
				$filenamewithoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $file['filename']);
				$file_data = $this->rawSourceUploadPath . $filenamewithoutExt . '_calibrated' . $fileextension;
				if (file_exists($file_data)) {
					unlink($file_data);
				}

				// Reset calibrated file to its original state.
				$update = [
					'calibrated_filename' => '',
					'is_calibrated' => 0,
				];

				// Update filemanaged data to reset calibration data.
				$update_status = $this->filemanaged->update($update, $fid);
				if ($update_status) {
					echo json_encode($success);
				}
			}
		}
	}

	/**
	 * Ajax call to store pixels for calibration.
	 */
	public function set_pixels() {
		$pixels = $this->input->post('pixels');
		$fid = $this->input->post('fid');
		if (is_numeric($pixels) && $pixels && $fid) {

			// Store pixels to database.
			$file_props = [
				'pixels' => $pixels,
				'date_modified' => date('m/d/Y'),
			];

			// Update file properties.
			$insert = $this->filemanaged->updateFileProps($fid, $file_props);

			echo json_encode(TRUE);
		} else {
			echo json_encode(FALSE);
		}
	}

}
