<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kendaraan extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		if($this->session->userdata('logged_in') === false) {
			return redirect('login');
		}

		$this->load->model('m_kendaraan');
		$this->load->model('m_user');
		$this->load->model('m_global');
		$this->load->model('set_role/m_set_role', 'm_role');
		$this->load->library('barcode_lib');
		$this->load->library('perhitungan_lib');
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
			'title' => 'add Data Kendaraan',
			'data_user' => $data_user,
			'data_role'	=> $data_role,
			'merek' => $this->m_global->getSelectedData('m_merek', array('deleted_at' => NULL))->result_array(),
			'customer' => $this->m_global->getSelectedData('m_customer', array('deleted_at' => NULL))->result_array(),
			'kendaraan' => $this->m_global->getSelectedData('m_kendaraan', array('deleted_at' => NULL))->result_array(),
		);

		/**
		 * content data untuk template
		 * param (css : link css pada direktori assets/css_module)
		 * param (modal : modal komponen pada modules/nama_modul/views/nama_modal)
		 * param (js : link js pada direktori assets/js_module)
		 */
		$content = [
			'css' 	=> null,
			'modal' => 'modal_master_member',
			'js'	=> 'kendaraan.js',
			// 'view'	=> 'view_master_member'
			'view'	=> 'view_add_kendaraan'
		];

		$this->template_view->load_view($content, $data);
	}

	public function view_kendaraan()
	{
		$id_user = $this->session->userdata('id_user'); 
		$data_user = $this->m_user->get_detail_user($id_user);
		$data_role = $this->m_role->get_data_all(['aktif' => '1'], 'm_role');
			
		/**
		 * data passing ke halaman view content
		 */
		$data = array(
			'title' => 'List Data Kendaraan Masuk',
			'data_user' => $data_user,
			'data_role'	=> $data_role,
			'mekanik'   => $this->m_global->getSelectedData('m_mekanik', ['deleted_at' => NULL])->result(),
			'pekerjaan'  => $this->m_global->getSelectedData('m_pekerjaan', ['deleted_at' => NULL])->result(),
			'merek' => $this->m_global->getSelectedData('m_merek', array('deleted_at' => NULL))->result_array()
		);

		/**
		 * content data untuk template
		 * param (css : link css pada direktori assets/css_module)
		 * param (modal : modal komponen pada modules/nama_modul/views/nama_modal)
		 * param (js : link js pada direktori assets/js_module)
		 */
		$content = [
			'css' 	=> null,
			'modal' => ['modal_master_member', 'modal_tambah_mekanik', 'modal_list_pekerjaan'],
			'js'	=> 'kendaraan.js',
			// 'view'	=> 'view_master_member'
			'view'	=> 'view_kendaraan_masuk'
		];

		$this->template_view->load_view($content, $data);
	}

	public function view_kendaraan_selesai()
	{
		$id_user = $this->session->userdata('id_user'); 
		$data_user = $this->m_user->get_detail_user($id_user);
		$data_role = $this->m_role->get_data_all(['aktif' => '1'], 'm_role');
			
		/**
		 * data passing ke halaman view content
		 */
		$data = array(
			'title' => 'List Data Kendaraan Selesai',
			'data_user' => $data_user,
			'data_role'	=> $data_role,
			// 'mekanik'   => $this->m_global->getSelectedData('m_mekanik', ['deleted_at' => NULL])->result()
		);

		/**
		 * content data untuk template
		 * param (css : link css pada direktori assets/css_module)
		 * param (modal : modal komponen pada modules/nama_modul/views/nama_modal)
		 * param (js : link js pada direktori assets/js_module)
		 */
		$content = [
			'css' 	=> null,
			'modal' => ['modal_master_member', 'modal_tambah_mekanik'],
			'js'	=> 'kendaraan_selesai.js',
			// 'view'	=> 'view_master_member'
			'view'	=> 'view_kendaraan_selesai'
		];

		$this->template_view->load_view($content, $data);
	}

	public function list_kendaraan_masuk()
	{

		$id_user = $this->session->userdata('id_user'); 
		$data_user = $this->m_user->get_detail_user($id_user);
		// var_dump($data_user); die();
		$data_role = $this->m_role->get_data_all(['aktif' => '1'], 'm_role');
		$status   = $this->input->post('status');
		$list = $this->m_kendaraan->get_datatable_user($status);
		// echo $this->db->last_query(); die();
		$data = array();
		$no =$_POST['start'];
		foreach ($list as $kendaraan) {
			$no++;
			$row = array();
			//loop value tabel db
			$row[] = $no;
			// $row[] = $kendaraan->invoice;
			// $row[] = $kendaraan->nama_customer;
			// $row[] = $kendaraan->nama_kendaraan;
			// $row[] = $kendaraan->nopol;
			// $row[] = $kendaraan->nama_merek;
			// $row[] = $kendaraan->warna;
			$identitas = '
				Invoice : <strong>'.$kendaraan->invoice.'</strong><br>
				Nama Customer : <strong>'.$kendaraan->nama_customer.'</strong><br>
				Nama Kendaraan : <strong>'.$kendaraan->nama_kendaraan.'</strong><br>
				No Polisi : <strong>'.$kendaraan->nopol.'</strong><br>
				Merek : <strong>'.$kendaraan->nama_merek.'</strong><br>
				Warna : <strong>'.$kendaraan->warna.'</strong><br>
				Telp : <strong>'.$kendaraan->telp.'</strong><br>
			';

			if ($kendaraan->tgl_selesai) {
				$tgl_selesai = date('d-m-Y', strtotime($kendaraan->tgl_selesai));
			} else {
				$tgl_selesai = null;
			}
			
			$row[] = $identitas;
			$row[] = $kendaraan->keluhan;
			$row[] = $kendaraan->nama_mekanik;
			// $row[] = $kendaraan->telp;
			$row[] = date('d-m-Y H:i:s', strtotime($kendaraan->created_at));
			$row[] = $tgl_selesai;
			// $row[] = '<img src="'.base_url().'/files/img/barcode/'.$member->kode_member.'.jpg"  width="200">';
			if ($kendaraan->status == NULL) {
				$stat = '<span class="badge badge-danger">Baru</span>';
			}else if ($kendaraan->status == 1) {
				$stat = '<span class="badge badge-primary">Dalam Pengerjaan</span>';
			} else {
				$stat = '<span class="badge badge-success">Selesai</span>';
			}
			
			$btn_selesai = '<button class="dropdown-item" onclick="konfirmasi_selesai(\''.$kendaraan->id.'\')">
								<i class="la la-check"></i> Konfirmasi Selesai
							</button>';
			$row[] = $stat;
			$str_aksi = '';
			if ($kendaraan->status != 2) {
				if ($data_user[0]->id_role == 1) { //admin
					$str_aksi = '
					<div class="btn-group">
						<button type="button" class="btn btn-sm btn_1 dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Opsi</button>
						<div class="dropdown-menu">
								<button class="dropdown-item" onclick="edit_kendaraan(\''.$kendaraan->id.'\')">
								<i class="la la-pencil"></i> Edit Data kendaraan
								</button>
								<button class="dropdown-item" onclick="tambah_mekanik(\''.$kendaraan->id.'\')">
									<i class="la la-pencil"></i> Tambahkan Mekanik
								</button>
								<button class="dropdown-item" onclick="tambah_onderdil(\''.$kendaraan->id.'\')">
									<i class="la la-car"></i> Input Onderdil
								</button>
								<button class="dropdown-item" onclick="list_pekerjaan(\''.$kendaraan->id.'\')">
									<i class="la la-list"></i> List Pekerjaan
								</button>
							
								<button class="dropdown-item" onclick="delete_kendaraan(\''.$kendaraan->id.'\')">
									<i class="la la-trash"></i> Hapus
								</button>			
						
					';
					if ($kendaraan->status == 1) {
						$str_aksi .= $btn_selesai;
					}
					
				}elseif ($data_user[0]->id_role == 4) { //advisor
				
			
					$str_aksi = '
					<div class="btn-group">
						<button type="button" class="btn btn-sm btn_1 dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Opsi</button>
						<div class="dropdown-menu">
								<button class="dropdown-item" onclick="edit_kendaraan(\''.$kendaraan->id.'\')">
								<i class="la la-pencil"></i> Edit Data kendaraan
								</button>
								
								<button class="dropdown-item" onclick="delete_kendaraan(\''.$kendaraan->id.'\')">
									<i class="la la-trash"></i> Hapus
								</button>
											
						
					';
					
				} else if ($data_user[0]->id_role == 5) { //workshop
					if ($kendaraan->status == 1) {
						$str_aksi = '
						<div class="btn-group">
							<button type="button" class="btn btn-sm btn_1 dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Opsi</button>
							<div class="dropdown-menu">
									
									<button class="dropdown-item" onclick="tambah_mekanik(\''.$kendaraan->id.'\')">
										<i class="la la-pencil"></i> Tambahkan Mekanik
									</button>
									<button class="dropdown-item" onclick="tambah_onderdil(\''.$kendaraan->id.'\')">
										<i class="la la-car"></i> Input Onderdil
									</button>
									<button class="dropdown-item" onclick="list_pekerjaan(\''.$kendaraan->id.'\')">
										<i class="la la-list"></i> List Pekerjaan
									</button>
									
						';

						if ($kendaraan->status == 1) {
							$str_aksi .= $btn_selesai;
						}
					} else {
						$str_aksi = '
						<div class="btn-group">
							<button type="button" class="btn btn-sm btn_1 dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Opsi</button>
							<div class="dropdown-menu">
									
									<button class="dropdown-item" onclick="tambah_mekanik(\''.$kendaraan->id.'\')">
										<i class="la la-pencil"></i> Tambahkan Mekanik
									</button>
									<button class="dropdown-item" onclick="tambah_onderdil(\''.$kendaraan->id.'\')">
										<i class="la la-car"></i> Input Onderdil
									</button>
									
							
						';
					}
					
				}
			}


			$str_aksi .= '</div></div>';
			$row[] = $str_aksi;

			$data[] = $row;
		}//end loop

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->m_kendaraan->count_all(),
			"recordsFiltered" => $this->m_kendaraan->count_filtered(),
			"data" => $data
		];
		
		echo json_encode($output);
	}

	public function add_kendaraan(){
		// var_dump('tes tes');
		$id_user = $this->session->userdata('id_user'); 
		$data_user = $this->m_user->get_detail_user($id_user);
		$data_role = $this->m_role->get_data_all(['aktif' => '1'], 'm_role');
			
		/**
		 * data passing ke halaman view content
		 */
		$data = array(
			'title' => 'Pengelolaan Data Member',
			'data_user' => $data_user,
			'data_role'	=> $data_role,
		);

		/**
		 * content data untuk template
		 * param (css : link css pada direktori assets/css_module)
		 * param (modal : modal komponen pada modules/nama_modul/views/nama_modal)
		 * param (js : link js pada direktori assets/js_module)
		 */
		$content = [
			'css' 	=> null,
			'modal' => 'modal_master_member',
			'js'	=> 'kendaraan.js',
			'view'	=> 'view_add_kendaraan'
		];

		$this->template_view->load_view($content, $data);
	}


	public function add_data_kendaraan(){
		$this->load->library('Enkripsi');
		$obj_date = new DateTime();
		$timestamp = $obj_date->format('Y-m-d H:i:s');
		$arr_valid = $this->rule_validasi();
	
		$customer = $this->input->post('customer');
		$kendaraan = $this->input->post('kendaraan');
		$keluhan = $this->input->post('keluhan');
		
		if ($arr_valid['status'] == FALSE) {
			echo json_encode($arr_valid);
			return;
		}


		$this->db->trans_begin();
		
		$data_kendaraan = [
			'id_customer' => $customer,
			'id_kendaraan' => $kendaraan,
			'keluhan' => $keluhan,
			'invoice' => $this->m_global->get_invoice(),
			'created_at' => $timestamp
		];
		
		$insert = $this->m_kendaraan->save($data_kendaraan);
		
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
	/**
	 * Hanya melakukan softdelete saja
	 * isi kolom updated_at dengan datetime now()
	 */
	public function delete_member()
	{
		$id = $this->input->post('id');
		$del = $this->m_member->softdelete_by_id($id);
		if($del) {
			$retval['status'] = TRUE;
			$retval['pesan'] = 'Data Master Member dihapus';
		}else{
			$retval['status'] = FALSE;
			$retval['pesan'] = 'Data Master Member dihapus';
		}

		echo json_encode($retval);
	}

	public function konfirmasi_selesai()
	{
		$id = $this->input->post('id');
		$del = $this->m_kendaraan->konfirmasi_selesai($id);
		if($del) {
			$retval['status'] = TRUE;
			$retval['pesan'] = 'Data Kendaraan telah diselesaikan';
		}else{
			$retval['status'] = FALSE;
			$retval['pesan'] = 'Data Kendaraan telah diselesaikans';
		}

		echo json_encode($retval);
	}

	public function edit_mekanik()
	{
		$this->load->library('Enkripsi');
		$id_user = $this->session->userdata('id_user');
		$data_user = $this->m_user->get_by_id($id_user);
	
		$id = $this->input->post('id');
		//$oldData = $this->m_user->get_by_id($id);

		$where = ['t_kendaraan_masuk.id' => $id];

		$oldData = $this->m_global->getSelectedData('t_kendaraan_masuk', $where)->row();
		if(!$oldData){
			return redirect($this->uri->segment(1));
		}

		if ($oldData->tgl_selesai != NULL) {
			$tgl_selesai = date('d/m/Y', strtotime($oldData->tgl_selesai));
		}else{
			$tgl_selesai = NULL;
		}
		
		$data = array(
			'data_user' => $data_user,
			'old_data'	=> $oldData,
			'tgl_selesai' => $tgl_selesai,
	
		);
		
		echo json_encode($data);
	}

	public function edit_kendaraan()
	{
		$this->load->library('Enkripsi');
		$id_user = $this->session->userdata('id_user');
		$data_user = $this->m_user->get_by_id($id_user);
	
		$id = $this->input->post('id');
		//$oldData = $this->m_user->get_by_id($id);

		$where = ['t_kendaraan_masuk.id' => $id];

		$oldData = $this->m_global->getSelectedData('t_kendaraan_masuk', $where)->row();
		if(!$oldData){
			return redirect($this->uri->segment(1));
		}

		if ($oldData->tgl_selesai != NULL) {
			$tgl_selesai = date('d/m/Y', strtotime($oldData->tgl_selesai));
		}else{
			$tgl_selesai = NULL;
		}
		
		$data = array(
			'data_user' => $data_user,
			'old_data'	=> $oldData,
	
		);
		
		echo json_encode($data);
	}

	public function update_mekanik()
	{
		$sesi_id_user = $this->session->userdata('id_user'); 
		$id_user = $this->input->post('id_user');
		$this->load->library('Enkripsi');
		$obj_date = new DateTime();
		$timestamp = $obj_date->format('Y-m-d H:i:s');
	

		$id = $this->input->post('id');
		$id_mekanik = $this->input->post('id_mekanik');

		$tgl_selesai = $obj_date->createFromFormat('d/m/Y', $this->input->post('tgl_selesai'))->format('Y-m-d').' 00:00:00';
		// $date->getTimestamp()
		
		$this->db->trans_begin();

		$data = [
			'tgl_selesai' => $tgl_selesai,
			'status' => 1,
			'id_mekanik' => $id_mekanik,
		];

		$where = ['id' => $id];
		$update = $this->m_kendaraan->update($where, $data);

		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$data['status'] = false;
			$data['pesan'] = 'Gagal update mekanik';
		}else{
			$this->db->trans_commit();
			$data['status'] = true;
			$data['pesan'] = 'Sukses update mekanik';
		}
		
		echo json_encode($data);
	}

	public function update_data_kendaraan()
	{
		$sesi_id_user = $this->session->userdata('id_user'); 
		$id_user = $this->input->post('id_user');
		$this->load->library('Enkripsi');
		$obj_date = new DateTime();
		$timestamp = $obj_date->format('Y-m-d H:i:s');
	

		$id = $this->input->post('id');
		$nama_customer = $this->input->post('nama_customer');
		$nama_kendaraan = $this->input->post('nama_kendaraan');
		$nopol = $this->input->post('nopol');
		$merek = $this->input->post('merek');
		$hp = $this->input->post('hp');
		$warna = $this->input->post('warna');
		$keluhan = $this->input->post('keluhan');

		// $tgl_selesai = $obj_date->createFromFormat('d/m/Y', $this->input->post('tgl_selesai'))->format('Y-m-d').' 00:00:00';
		// $date->getTimestamp()
		
		$this->db->trans_begin();

		$data = [
			'nama_customer' => $nama_customer,
			'nama_kendaraan' => $nama_kendaraan,
			'nopol' => $nopol,
			'merk' => $merek,
			'telp' => $hp,
			'warna' => $warna,
			'keluhan' => $keluhan,
			// 'invoice' => $this->m_global->get_invoice(),
			'updated_at' => $timestamp
		];

		$where = ['id' => $id];
		$update = $this->m_kendaraan->update($where, $data);

		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$data['status'] = false;
			$data['pesan'] = 'Gagal update data kendaraan';
		}else{
			$this->db->trans_commit();
			$data['status'] = true;
			$data['pesan'] = 'Sukses update data kendaraan';
		}
		
		echo json_encode($data);
	}

	public function insert_pekerjaan()
	{
		$sesi_id_user = $this->session->userdata('id_user'); 
		$id_user = $this->input->post('id_user');
		$this->load->library('Enkripsi');
		$obj_date = new DateTime();
		$timestamp = $obj_date->format('Y-m-d H:i:s');
	

		$id = $this->input->post('id');
		$id_pekerjaan = $this->input->post('pekerjaan');

		// $date->getTimestamp()
		
		$this->db->trans_begin();

		$data = [
			'id_t_kendaraan_masuk' => $id,
			'id_pekerjaan' => $id_pekerjaan,
			'created_at' => $timestamp,
		];

		$insert = $this->m_kendaraan->savePekerjaan($data);

		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$data['status'] = false;
			$data['pesan'] = 'Gagal menambah list pekerjaan';
			$data['id']    = $id;
		}else{
			$this->db->trans_commit();
			$data['status'] = true;
			$data['pesan'] = 'Sukses menambah list pekerjaan';
			$data['id']    = $id;
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

		if ($this->input->post('customer') == '') {
			$data['inputerror'][] = 'customer';
			$data['error_string'][] = 'Wajib memilih Customer';
			$data['status'] = FALSE;
		}

		if ($this->input->post('kendaraan') == '') {
			$data['inputerror'][] = 'kendaraan';
			$data['error_string'][] = 'Wajib memilih Kendaraan';
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

	public function onderdil($id){
		$id_user = $this->session->userdata('id_user'); 
		$data_user = $this->m_user->get_detail_user($id_user);
		$data_role = $this->m_role->get_data_all(['aktif' => '1'], 'm_role');
			
		/**
		 * data passing ke halaman view content
		 */
		$data = array(
			'title' => 'add Data Onderdil',
			'data_user' => $data_user,
			'data_role'	=> $data_role,
			'onderdil' => $this->m_global->getSelectedData('m_barang', array('deleted_at' => NULL))->result(),
			'id'	=> $id
		);

		/**
		 * content data untuk template
		 * param (css : link css pada direktori assets/css_module)
		 * param (modal : modal komponen pada modules/nama_modul/views/nama_modal)
		 * param (js : link js pada direktori assets/js_module)
		 */
		$content = [
			'css' 	=> null,
			'modal' => 'modal_master_member',
			'js'	=> 'onderdil.js',
			// 'view'	=> 'view_master_member'
			'view'	=> 'view_onderdil'
		];

		$this->template_view->load_view($content, $data);
	}

	public function list_onderdil(){
		$id = $this->input->post('id');
		$total = 0;
        $data = $this->m_kendaraan->get_onderdil($id)->result();
        foreach($data as $row){
			$subtotal = $row->harga_jual * $row->qty;
			$total += $subtotal;
            ?>
            <tr>
                
                <td style="vertical-align: middle;"><?php echo $row->nama_barang; ?></td>
				<td width="10%"><input type="number" class="form-control" width="5" id="qty_order_<?php echo $row->id;?>" value="<?php echo $row->qty; ?>" onchange="tes(<?php echo $row->id ?>)"></td>
                <td style="vertical-align: middle;"><?php echo 'Rp '.number_format($row->harga_jual); ?></td>
                <td style="vertical-align: middle;"><?php echo 'Rp '.number_format($subtotal); ?></td>
				<td style="vertical-align: middle;"><button class="btn-danger" alt="batalkan" onclick="hapus_onderdil(<?php echo $row->id; ?>)"><i class="fa fa-times"></i></button></td>
            </tr>
			
            <?php
        }
		echo '<tr>
			<td colspan="3" style="text-align:center;"><strong>Total</strong></td>
			<td colspan="2"><strong>Rp '.number_format($total).'</strong></td>
		</tr>';
	}

	public function list_pekerjaan(){
		$id = $this->input->post('id');
		$total = 0;
        $data = $this->m_kendaraan->get_pekerjaan($id)->result();
		$no = 0;
        foreach($data as $row){
			$no ++;
            ?>
            <tr>
				<td ><?php echo $no; ?></td>
                <td style="vertical-align: middle;"><?php echo $row->type_wo; ?></td>
				<td style="vertical-align: middle;"><?php echo $row->nama_pekerjaan; ?></td>
				<td style="vertical-align: middle;"><button class="btn-danger" alt="batalkan" onclick="hapus_pekerjaan(<?php echo $row->id; ?>)"><i class="fa fa-times"></i></button></td>
            </tr>
			
            <?php
        }
		
	}

	public function add_onderdil()
	{
		$this->load->library('Enkripsi');
		$obj_date = new DateTime();
		$timestamp = $obj_date->format('Y-m-d H:i:s');
		$arr_valid = $this->rule_validasi();
	
		$id_kendaraan = $this->input->post('id_kendaraan');
		$id_barang    = $this->input->post('id_barang');
		$qty = $this->input->post('qty');

		$barang = $this->m_global->getSelectedData('m_barang', array('id' => $id_barang))->row();

		$cek_barang = $this->m_global->getSelectedData('t_kendaraan_masuk_detail', array('id_onderdil' => $id_barang, 'id_t_kendaraan_masuk' => $id_kendaraan))->row();
		if ($cek_barang) {
			$retval['status'] = false;
			$retval['pesan'] = 'Barang yang anda masukkan sudah terinput';
			echo json_encode($retval);
			exit;
		}

		$this->db->trans_begin();
		
		$data = [
			'id_t_kendaraan_masuk' => $id_kendaraan,
			'qty' => $qty,
			'id_onderdil' => $id_barang,
			'harga' => $barang->harga_jual,
			'created_at' => $timestamp
		];
		
		$insert = $this->m_kendaraan->saveOnderdil($data);
		
		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$retval['status'] = false;
			$retval['pesan'] = 'Gagal menambahkan Onderdil';
		}else{
			$this->db->trans_commit();
			$retval['status'] = true;
			$retval['pesan'] = 'Sukses menambahkan Onderdil';
		}

		echo json_encode($retval);
	}

	public function hapus_onderdil()
	{
		$id = $this->input->post('id');
		$data_where = array('id' => $id);
		$del = $this->m_global->force_delete($data_where, 't_kendaraan_masuk_detail');
		if($del) {
			$retval['status'] = TRUE;
			$retval['pesan'] = 'Data Barang berhasil dihapus';
		}else{
			$retval['status'] = FALSE;
			$retval['pesan'] = 'Data Barang berhasil dihapus';
		}

		echo json_encode($retval);

	}

	public function hapus_pekerjaan()
	{
		$id = $this->input->post('id');
		$data_where = array('id' => $id);
		$del = $this->m_global->force_delete($data_where, 't_kendaraan_pekerjaan_detail');
		if($del) {
			$retval['status'] = TRUE;
			$retval['pesan'] = 'List Pekerjaan berhasil dihapus';
			$retval['id'] = $id;
		}else{
			$retval['status'] = FALSE;
			$retval['pesan'] = 'List Pekerjaan berhasil dihapus';
			$retval['id'] = $id;
		}

		echo json_encode($retval);

	}

	public function change_qty()
	{
		$this->db->trans_begin();
		$id = $this->input->post('id');
		$qty  = $this->input->post('qty');

		
		$data = array(
				'qty' => $qty,
				);
				
		$data_where = array('id' => $id);
		$update = $this->m_kendaraan->updateOnderdil($data_where, $data);
		
		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$retval['status'] = false;
			$retval['pesan'] = 'Gagal Mengubah Data';
		}else{
			$this->db->trans_commit();
			$retval['status'] = true;
			$retval['pesan'] = 'Sukses Mengubah Data ';
			// $retval['order_id'] = $order_id;
		}

		echo json_encode($retval);
	}

	public function getCustomer()
	{
		$this->load->library('Enkripsi');
		$id_user = $this->session->userdata('id_user');
		$data_user = $this->m_user->get_by_id($id_user);
	
		$id = $this->input->post('id');
		//$oldData = $this->m_user->get_by_id($id);

		$where = ['id' => $id];

		$oldData = $this->m_global->getSelectedData('m_customer', $where)->row();
		if(!$oldData){
			return redirect($this->uri->segment(1));
		}

	
		$data = array(
			'data_user' => $data_user,
			'old_data'	=> $oldData,
	
		);
		
		echo json_encode($data);
	}

	public function getKendaraan()
	{
		$this->load->library('Enkripsi');
		$id_user = $this->session->userdata('id_user');
		$data_user = $this->m_user->get_by_id($id_user);
	
		$id = $this->input->post('id');
		//$oldData = $this->m_user->get_by_id($id);

		$where = ['id' => $id];

		$oldData = $this->m_global->getSelectedData('m_kendaraan', $where)->row();
		if(!$oldData){
			return redirect($this->uri->segment(1));
		}

	
		$data = array(
			'data_user' => $data_user,
			'old_data'	=> $oldData,
	
		);
		
		echo json_encode($data);
	}

	public function get_perhitungan()
	{
		### nyeluk library yo mas 
		### ben ga rame controller e
		$data = $this->perhitungan_lib->main();
		
		echo "<pre>";
		print_r ($data);
		echo "</pre>";
		
	}
}
