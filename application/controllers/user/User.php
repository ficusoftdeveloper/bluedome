<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * User controller class.
 * Manage user related operations.
 */
class User extends CI_Controller {

	/**
	 * construct an object of user controller class.
	 */
	function __construct() {
		parent::__construct();
		$this->load->helper('url');
		$this->load->library('session');
		$this->load->model('user/usermodel');
		$this->load->helper('form');
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->load->helper('welcome');
	}

	/**
	 * User registration action callback.
	 */
	function register() {
		if ($this->input->post('postSubmit')) {
			$user = [
				'user_name' => $this->input->post('user_name'),
				'user_email' => $this->input->post('user_email'),
				'user_password' => $this->input->post('user_password'),
				'user_address' => $this->input->post('user_address'),
			];
			// Check if email is valid of not.
			$email_check = $this->usermodel->email_check($user['user_email']);
			if ($email_check) {
				$this->usermodel->register_user($user);
				$this->session->set_flashdata('success_msg', 'Registered successfully, Now login to your account');
				redirect('user/login');
			} else {
				$this->session->set_flashdata('error_msg', 'Error occured, Try again.');
				redirect('user/register');
			}
		}

		// Manage view components.
		$this->load->view('components/header_login', header_component());
		$this->load->view('user/register');
		$this->load->view('components/footer_login');
	}


	/**
	 * User login action callback.
	 */
	function login() {
		if ($this->input->post('postSubmit')) {
			$user_login = [
				'user_email' => $this->input->post('user_email'),
				'user_password' => $this->input->post('user_password')
			];
			// Validate user credentials.
			$data = $this->usermodel->login_user($user_login['user_email'], $user_login['user_password']);
			if ($data) {
				// Set user specific session variables.
				$this->session->set_userdata('user_id', $data['user_id']);
				$this->session->set_userdata('user_email', $data['user_email']);
				$this->session->set_userdata('user_name', $data['user_name']);
				$this->session->set_userdata('user_address', $data['user_address']);
				$this->session->set_userdata('user_mobile', $data['user_mobile']);

				$this->session->set_flashdata('success_msg', 'You have successfully logged in!');
				redirect('inspection');
			} else {
				$this->session->set_flashdata('error_msg', 'Please enter correct user credentials');
				redirect('user/login');
			}
		}

		// Manage views compoments.
		$this->load->view('components/header_login', header_component());
		$this->load->view('user/login');
		$this->load->view('components/footer_login');
	}

	/**
	 * Forgot password action callback.
	 */
	function forgot_password() {
		$this->load->view('components/header_login', header_component());
		$this->load->view('user/forgot_password');
		$this->load->view('components/footer_login');
	}

	/**
	 * User logout action callback.
	 */
	public function logout() {
		$this->session->sess_destroy();
		redirect('user/login', 'refresh');
	}
}
