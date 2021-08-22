<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Master_kendaraan extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		if($this->session->userdata('logged_in') === false) {
			return redirect('login');
		}

		$this->load->model('m_master_kendaraan');
		$this->load->model('m_user');
		$this->load->model('m_global');
		$this->load->model('set_role/m_set_role', 'm_role');
		$this->load->library('barcode_lib');
	}

	public function index()
	{
		$id_user = $this->session->userdata('id_user'); 
		$data_user = $this->m_user->get_detail_user($id_user);
		$data_role = $this->m_role->get_data_all(['aktif' => '1'], 'm_role');
			
		/**
		 * data passing ke halaman view content
		 */
		$data = array(
			'title' => 'Pengelolaan Data Kendaraan',
			'data_user' => $data_user,
			'data_role'	=> $data_role,
			'merek' => $this->m_global->getSelectedData('m_merek', ['deleted_at' => NULL])->result()
		);

		/**
		 * content data untuk template
		 * param (css : link css pada direktori assets/css_module)
		 * param (modal : modal komponen pada modules/nama_modul/views/nama_modal)
		 * param (js : link js pada direktori assets/js_module)
		 */
		$content = [
			'css' 	=> null,
			'modal' => 'modal_master_kendaraan',
			'js'	=> 'master_kendaraan.js',
			'view'	=> 'view_master_kendaraan'
		];

		$this->template_view->load_view($content, $data);
	}

	public function list_kendaraan()
	{
		$list = $this->m_master_kendaraan->get_datatable_user();
		$data = array();
		$no =$_POST['start'];
		foreach ($list as $kendaraan) {
			$no++;
			$row = array();
			//loop value tabel db
			$row[] = $no;
			$row[] = $kendaraan->nama_kendaraan;
			$row[] = $kendaraan->warna;
			$row[] = $kendaraan->nama_merek;
			$row[] = $kendaraan->nopol;
			// $row[] = '<img src="'.base_url().'/files/img/barcode/'.$member->kode_member.'.jpg"  width="200">';

			$str_aksi = '
				<div class="btn-group">
					<button type="button" class="btn btn-sm btn_1 dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Opsi</button>
					<div class="dropdown-menu">
						<button class="dropdown-item" onclick="edit_kendaraan(\''.$kendaraan->id.'\')">
							<i class="la la-pencil"></i> Edit Data Kendaraan
						</button>
						<button class="dropdown-item" onclick="delete_kendaraan(\''.$kendaraan->id.'\')">
							<i class="la la-trash"></i> Hapus
						</button>
			';


			$str_aksi .= '</div></div>';
			$row[] = $str_aksi;

			$data[] = $row;
		}//end loop

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->m_master_kendaraan->count_all(),
			"recordsFiltered" => $this->m_master_kendaraan->count_filtered(),
			"data" => $data
		];
		
		echo json_encode($output);
	}

	public function edit_kendaraan()
	{
		$this->load->library('Enkripsi');
		$id_user = $this->session->userdata('id_user');
		$data_user = $this->m_user->get_by_id($id_user);
	
		$id = $this->input->post('id');
		//$oldData = $this->m_user->get_by_id($id);

		$where = ['m_kendaraan.id' => $id];

		$oldData = $this->m_global->getSelectedData('m_kendaraan', $where)->row();
		if(!$oldData){
			return redirect($this->uri->segment(1));
		}
		// var_dump($oldData);exit;
		
		$data = array(
			'data_user' => $data_user,
			'old_data'	=> $oldData,
		);
		
		echo json_encode($data);
	}

	public function add_data_kendaraan()
	{
		$this->load->library('Enkripsi');
		$obj_date = new DateTime();
		$timestamp = $obj_date->format('Y-m-d H:i:s');
		$arr_valid = $this->rule_validasi();
	
		$nopol = $this->input->post('nopol');
		$warna = $this->input->post('warna');
		$merek = $this->input->post('merek');
		$nama = $this->input->post('nama');

		if ($arr_valid['status'] == FALSE) {
			echo json_encode($arr_valid);
			return;
		}


		$this->db->trans_begin();

		// $this->simpan_barcode($kode_member);

		$data_user = [
			'nopol' => $nopol,
			'nama_kendaraan' => $nama,
			'warna' => $warna,
			'merek' => $merek,
			'created_at' => $timestamp,
			// 'img_foto'	=> $namafileseo
		];
		
		$insert = $this->m_master_kendaraan->save($data_user);
		
		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$retval['status'] = false;
			$retval['pesan'] = 'Gagal menambahkan Data Kendaraan';
		}else{
			$this->db->trans_commit();
			$retval['status'] = true;
			$retval['pesan'] = 'Sukses menambahkan Data Kendaraan';
		}

		echo json_encode($retval);
	}

	public function update_data_kendaraan()
	{
		$sesi_id_user = $this->session->userdata('id_user'); 
		$id_user = $this->input->post('id_user');
		$this->load->library('Enkripsi');
		$obj_date = new DateTime();
		$timestamp = $obj_date->format('Y-m-d H:i:s');
		
	
		$arr_valid = $this->rule_validasi(true);

		if ($arr_valid['status'] == FALSE) {
			echo json_encode($arr_valid);
			return;
		}

		$id = $this->input->post('id');
		$nama = $this->input->post('nama');
		$nopol = $this->input->post('nopol');
		$warna = $this->input->post('warna');
		$merek = $this->input->post('merek');
		
		$this->db->trans_begin();

	
		//SIMPAN BARCODE
		// $data_where = array('kode_member' => $kode_member);
		// $member     = $this->m_global->getSelectedData('m_member', $data_where)->row();
		// if (empty($member)) {
		// 	$this->simpan_barcode($kode_member);
		// }
		//SIMPAN BARCODE

		$data_user = [
			'nama_kendaraan' => $nama,
			'warna' => $warna,
			'merek' => $merek,
			'nopol' => $nopol,
			'updated_at' => $timestamp
		];


		$where = ['id' => $id];
		$update = $this->m_master_kendaraan->update($where, $data_user);

		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$data['status'] = false;
			$data['pesan'] = 'Gagal update Master Kendaraan';
		}else{
			$this->db->trans_commit();
			$data['status'] = true;
			$data['pesan'] = 'Sukses update Master Kendaraan';
		}
		
		echo json_encode($data);
	}

	/**
	 * Hanya melakukan softdelete saja
	 * isi kolom updated_at dengan datetime now()
	 */
	public function delete_kendaraan()
	{
		$id = $this->input->post('id');
		$del = $this->m_master_kendaraan->softdelete_by_id($id);
		if($del) {
			$retval['status'] = TRUE;
			$retval['pesan'] = 'Data Master Kendaraan dihapus';
		}else{
			$retval['status'] = FALSE;
			$retval['pesan'] = 'Data Master Kendaraan dihapus';
		}

		echo json_encode($retval);
	}

	public function edit_status_user($id)
	{
		$input_status = $this->input->post('status');
		// jika aktif maka di set ke nonaktif / "0"
		$status = ($input_status == "aktif") ? $status = 0 : $status = 1;
			
		$input = array('status' => $status);

		$where = ['id' => $id];

		$this->m_user->update($where, $input);

		if ($this->db->affected_rows() == '1') {
			$data = array(
				'status' => TRUE,
				'pesan' => "Status User berhasil di ubah.",
			);
		}else{
			$data = array(
				'status' => FALSE
			);
		}

		echo json_encode($data);
	}

	// ===============================================
	private function rule_validasi($is_update=false, $skip_pass=false)
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		if ($this->input->post('nama') == '') {
			$data['inputerror'][] = 'nama';
			$data['error_string'][] = 'Wajib mengisi Nama Kendaraan';
			$data['status'] = FALSE;
		}

		if ($this->input->post('warna') == '') {
			$data['inputerror'][] = 'warna';
			$data['error_string'][] = 'Wajib mengisi Warna';
			$data['status'] = FALSE;
		}

		if ($this->input->post('merek') == '') {
			$data['inputerror'][] = 'merek';
			$data['error_string'][] = 'Wajib mengisi Merek Kendaraan';
			$data['status'] = FALSE;
		}

		if ($this->input->post('nopol') == '') {
			$data['inputerror'][] = 'nopol';
			$data['error_string'][] = 'Wajib mengisi Nopol Kendaraan';
			$data['status'] = FALSE;
		}
	
	

        return $data;
	}

	private function konfigurasi_upload_img($nmfile)
	{ 
		//konfigurasi upload img display
		$config['upload_path'] = './upload/member/';
		$config['allowed_types'] = 'gif|jpg|png|jpeg|bmp';
		$config['overwrite'] = TRUE;
		$config['max_size'] = '4000';//in KB (4MB)
		$config['max_width']  = '0';//zero for no limit 
		$config['max_height']  = '0';//zero for no limit
		$config['file_name'] = $nmfile;
		//load library with custom object name alias
		$this->load->library('upload', $config, 'file_obj');
		$this->file_obj->initialize($config);
	}

	private function konfigurasi_image_resize($filename)
	{
		//konfigurasi image lib
	    $config['image_library'] = 'gd2';
	    $config['source_image'] = './upload/member/'.$filename;
	    $config['create_thumb'] = FALSE;
	    $config['maintain_ratio'] = FALSE;
	    $config['new_image'] = './upload/member/'.$filename;
	    $config['overwrite'] = TRUE;
	    $config['width'] = 450; //resize
	    $config['height'] = 500; //resize
	    $this->load->library('image_lib',$config); //load image library
	    $this->image_lib->initialize($config);
	    $this->image_lib->resize();
	}

	private function konfigurasi_image_thumb($filename, $gbr)
	{
		//konfigurasi image lib
	    $config2['image_library'] = 'gd2';
	    $config2['source_image'] = './upload/member/'.$filename;
	    $config2['create_thumb'] = TRUE;
	 	$config2['thumb_marker'] = '_thumb';
	    $config2['maintain_ratio'] = FALSE;
	    $config2['new_image'] = './upload/member/thumbs/'.$filename;
	    $config2['overwrite'] = TRUE;
	    $config2['quality'] = '60%';
	 	$config2['width'] = 45;
	 	$config2['height'] = 45;
	    $this->load->library('image_lib',$config2); //load image library
	    $this->image_lib->initialize($config2);
	    $this->image_lib->resize();
	    return $output_thumb = $gbr['raw_name'].'_thumb'.$gbr['file_ext'];	
	}

	private function seoUrl($string) {
	    //Lower case everything
	    $string = strtolower($string);
	    //Make alphanumeric (removes all other characters)
	    $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
	    //Clean up multiple dashes or whitespaces
	    $string = preg_replace("/[\s-]+/", " ", $string);
	    //Convert whitespaces and underscore to dash
	    $string = preg_replace("/[\s_]/", "-", $string);
	    return $string;
	}

	public function simpan_barcode($value)
	{
		$this->barcode_lib->save_jpg($value);
	}
}
