<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	
	public function __construct()
	{
		parent::__construct();
		//Do your magic here
	}
	

	public function index()
	{	
		if ($this->session->userdata('logged_in')) {
			redirect('home');
		}

		$this->load->view('view_login2');
	}

	public function proses()
	{

		$this->load->model('m_user');
		$this->load->library('Enkripsi');
		
		$pass_string = $this->input->post('password');
		$hasil_password = $this->enkripsi->enc_dec('encrypt', $pass_string);
		// $this->register_user($this->input->post('username'), $hasil_password);
		// exit;
		$data_input = array(
			'data_user'=>$this->input->post('username'),
			'data_password'=>$hasil_password,
		);
		
		$result = $this->m_user->login($data_input);

		if ($result) {
			$this->m_user->set_lastlogin($result->id);
			
			$arr_userdata = [
				'username' => $result->username,
				'id_user' => $result->id,
				'last_login' => $result->last_login,
				'id_role' => $result->id_role,
				'logged_in' => true,
			];

			$this->session->set_userdata($arr_userdata);

			$data_log = json_encode($arr_userdata);
			$this->lib_fungsi->catat_log_aktifitas('LOGIN', null, $data_log);

			echo json_encode([
				'status' => true
			]);
		}else{
			echo json_encode([
				'status' => false
			]);
			// $this->session->set_flashdata('message', 'Kombinasi Username & Password Salah, Mohon di cek ulang');
			// redirect('login');
		}
	}

	public function logout_proc()
	{
		if ($this->session->userdata('logged_in')) 
		{
			$arr_userdata = [
				'username' => $this->session->userdata('username'),
				'id_user' => $this->session->userdata('id_user'),
				'last_login' => $this->session->userdata('last_login'),
				'id_role' => $this->session->userdata('id_role'),
				'logged_in' => false,
			];

			$data_log = json_encode($arr_userdata);
			$this->lib_fungsi->catat_log_aktifitas('LOGOUT', null, $data_log);

			//$this->session->sess_destroy();
			$this->session->unset_userdata('username');
			$this->session->unset_userdata('id_user');
			$this->session->unset_userdata('last_login');
			$this->session->unset_userdata('id_role');
			$this->session->set_userdata(array('logged_in' => false));
		}
		
		return redirect('home');
	}

	public function lihat_pass($username)
	{
		$this->load->library('Enkripsi');
		$data = $this->db->query("select password from tbl_user where username = '$username'")->row();
		$str_dec = $this->enkripsi->decrypt($data->password);
		echo $str_dec;
	}

	public function register_user($username, $pass)
	{
		$data = [
			'id' => 1,
			'id_role' => 1,
			'kode_user' => 'USR-00001',
			'username' => trim($username),
			'password' => $pass,
			'created_at' => date('Y-m-d H:i:s') 
		];
		$this->db->insert('m_user', $data);
		
	}
}
