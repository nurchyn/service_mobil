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
								<button class="dropdown-item" onclick="hitung_prediksi(\''.$kendaraan->id.'\')">
									<i class="la la-bar-chart-o"></i> Hitung Prediksi
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

	public function hitung_prediksi()
	{
		#### inputan
		$x1 = [];
		$x2 = [];
		$t = [];
		#### bobot
		$v11 = [];
		$v12 = [];   
		$v21 = [];
		$v22 = [];
		$bias1 = [];
		$bias2 = [];
		$w1 = [];
		$w2 = [];
		$b = [];
		#### post
		$id_kendaraan_masuk = $this->input->post('id');
		#x
		$qty_pekerjaan = $this->m_kendaraan->get_pekerjaan($id_kendaraan_masuk)->num_rows();
		#y
		$qty_onderdil = $this->m_kendaraan->get_onderdil($id_kendaraan_masuk)->num_rows();
		#t
		$tgl_masuk_selesai = $this->m_global->single_row_array('created_at, tgl_selesai', ['deleted_at'=>null, 'id'=>$id_kendaraan_masuk], 't_kendaraan_masuk');
	
		$tgl_masuk = DateTime::createFromFormat('Y-m-d H:i:s', $tgl_masuk_selesai['created_at']);

		if($tgl_masuk_selesai['tgl_selesai'] == null) {
			$tgl_selesai = $tgl_masuk->modify('+2 days');
			$interval = $tgl_masuk->diff($tgl_selesai);
			## update t_kendaraan_masuk
			$this->m_global->update('t_kendaraan_masuk', ['hitung_pekerjaan' => $qty_pekerjaan, 'hitung_onderdil' => $qty_onderdil, 'lama_service' => $interval->d, 'tgl_selesai' => $tgl_selesai], ['id' => $id_kendaraan_masuk]);
		}else{
			$tgl_selesai = DateTime::createFromFormat('Y-m-d H:i:s', $tgl_masuk_selesai['tgl_selesai']);
			$interval = $tgl_masuk->diff($tgl_selesai);
			## update t_kendaraan_masuk
			$this->m_global->update('t_kendaraan_masuk', ['hitung_pekerjaan' => $qty_pekerjaan, 'hitung_onderdil' => $qty_onderdil, 'lama_service' => $interval->d], ['id' => $id_kendaraan_masuk]);
		}
		
		## get data kendaraan masuk
		$data_kendaraan = $this->m_global->multi_row_array('*', ['deleted_at'=>null], 't_kendaraan_masuk', NULL, 'created_at desc', NULL);

		if($data_kendaraan) {
			foreach ($data_kendaraan as $key => $value) {
				array_push($x1, $value['hitung_pekerjaan']);
				array_push($x2, $value['hitung_onderdil']);
				array_push($t, $value['lama_service']);

				if($key == 0) {
					//first loop 
					array_push($v11, number_format((float)$this->random_0_1(), 2, '.', ''));
					array_push($v12, number_format((float)$this->random_0_1(), 2, '.', ''));
					array_push($v21, number_format((float)$this->random_0_1(), 2, '.', ''));
					array_push($v22, number_format((float)$this->random_0_1(), 2, '.', ''));
					array_push($bias1, number_format((float)$this->random_0_1(), 2, '.', ''));
					array_push($bias2, number_format((float)$this->random_0_1(), 2, '.', ''));
					array_push($w1, number_format((float)$this->random_0_1(), 2, '.', ''));
					array_push($w2, number_format((float)$this->random_0_1(), 2, '.', ''));
					array_push($b, number_format((float)$this->random_0_1(), 2, '.', ''));
				}else{
					// duplicate sisanya
					array_push($v11, $v11[0]);
					array_push($v12, $v12[0]);
					array_push($v21, $v21[0]);
					array_push($v22, $v22[0]);
					array_push($bias1, $bias1[0]);
					array_push($bias2, $bias2[0]);
					array_push($w1, $w1[0]);
					array_push($w2, $w2[0]);
					array_push($b, $b[0]);
				}
				
			}
		}

		$arr_input = [
			'x1' => $x1,
            'x2' => $x2,   
            't' => $t,
            'a' => 0.1
		];

		$arr_bobot = [
			'v11' => $v11,
            'v12' => $v12,   
            'v21' => $v21,
            'v22' => $v22,
            'bias1' => $bias1,
            'bias2' => $bias2,
            'w1' => $w1,
            'w2' => $w2,
            'b' => $b,
		];

		$this->proses_perhitungan(10, $arr_input, $arr_bobot);
		// exit;
		// echo "<pre>";
		// print_r ($arr_input);
		// echo "</pre>";

		// echo "<pre>";
		// print_r ($arr_bobot);
		// echo "</pre>";

		exit;

		var_dump($qty_pekerjaan, $qty_onderdil);exit;
	}

	public function proses_perhitungan($epoch = 10, $input = null, $bobot = null)
	{

		### nyeluk library yo mas 
		### ben ga rame controller e
		$result = [];

		$obj_date = new DateTime();
		$timestamp = $obj_date->format('Y-m-d H:i:s');
		if($input == null) {
			######### DUMMY DATA ######
			$input = [
				'x1' => [0.5, 0, 0.75, 0.25, 1],
				'x2' => [0.25, 0, 1, 0.5, 1],   
				't' => [0.0555555555555556, 0, 0.444444444444444, 0.666666666666667, 1],
				'a' => 0.1
			];
		}else{
			// normalisasi
			$input_norm = $this->perhitungan_lib->normalisasi($input);
		}
		
		if($bobot == null) {
			######### DUMMY DATA ######
			$bobot = [
				'v11' => [0.03,0.03,0.03,0.03,0.03],
				'v12' => [0.02,0.02,0.02,0.02,0.02],   
				'v21' => [0.2,0.2,0.2,0.2,0.2,0.2],
				'v22' => [0.3,0.3,0.3,0.3,0.3,0.3],
				'bias1' => [0.7,0.7,0.7,0.7,0.7],
				'bias2' => [0.3,0.3,0.3,0.3,0.3],
				'w1' => [0.5,0.5,0.5,0.5,0.5],
				'w2' => [0.09,0.09,0.09,0.09,0.09],
				'b' => [0.31,0.31,0.31,0.31,0.31],
			];
		}
		

		$this->db->trans_begin();

		$ins_header = [
			'arr_input_x1' => json_encode($input_norm['x1']),
			'arr_input_x2' => json_encode($input_norm['x2']),
			'arr_input_t' => json_encode($input_norm['t']),
			'alpha' => $input['a'],
			'arr_bobot_v11_awal' => json_encode($bobot['v11']),
			'arr_bobot_v12_awal' => json_encode($bobot['v12']),
			'arr_bobot_v21_awal' => json_encode($bobot['v21']),
			'arr_bobot_v22_awal' => json_encode($bobot['v22']),

			'arr_bias_1_awal' => json_encode($bobot['bias1']),
			'arr_bias_2_awal' => json_encode($bobot['bias2']),
			'arr_bobot_w1_awal' => json_encode($bobot['w1']),
			'arr_bobot_w2_awal' => json_encode($bobot['w2']),
			'arr_bobot_b_awal' => json_encode($bobot['b']),
			'epoch' => $epoch,
			'jml_baris_input' => count($input['x1']),
			'created_at' => $timestamp
		];

		$id_header = $this->m_global->store_id($ins_header, 't_perhitungan');

		for ($i=0; $i <$epoch; $i++) { 
			
			if($i == 0) {
				//first loop
				/**
				 * param 1 : inputan statis
				 * param 2 : bobot statis
				 */
				$data = $this->perhitungan_lib->main($input_norm, $bobot);
			}else{
				/**
				 * param 1 : inputan statis
				 * param 2 : bobot statis
				 * param 3 : prev data loop
				 */
				$data = $this->perhitungan_lib->main($input_norm, $bobot, $result[$i-1]);
			}

			$ins_det = [
				'id_perhitungan' => $id_header,
				'epoch_ke' => $i+1,
				'arr_bobot_v11' => json_encode($data['arr_bobot']['v11']),
				'arr_bobot_v12' => json_encode($data['arr_bobot']['v12']),
				'arr_bobot_v21' => json_encode($data['arr_bobot']['v21']),
				'arr_bobot_v22' => json_encode($data['arr_bobot']['v22']),
				'arr_bobot_bias1' => json_encode($data['arr_bobot']['bias1']),
				'arr_bobot_bias2' => json_encode($data['arr_bobot']['bias2']),
				'arr_bobot_w1' => json_encode($data['arr_bobot']['w1']),
				'arr_bobot_w2' => json_encode($data['arr_bobot']['w2']),
				'arr_bobot_b' => json_encode($data['arr_bobot']['b']),
				'arr_aktivasi_z1_raw' => json_encode($data['aktivasi']['z1_raw']),
				'arr_aktivasi_z2_raw' => json_encode($data['aktivasi']['z2_raw']),
				'arr_aktivasi_z1' => json_encode($data['aktivasi']['z1']),
				'arr_aktivasi_z2' => json_encode($data['aktivasi']['z2']),
				'arr_output_y' => json_encode($data['output']['y']),
				'arr_output_aktivasi' => json_encode($data['output']['aktivasi']),
				'arr_output_faktor_error_y' => json_encode($data['output']['faktor_error_y']),
				'arr_output_perubahan_bobot_w1' => json_encode($data['output']['perubahan_bobot_w1']),
				'arr_output_perubahan_bobot_w2' => json_encode($data['output']['perubahan_bobot_w2']),
				'arr_output_perubahan_bobot_w_bias' => json_encode($data['output']['perubahan_bobot_w_bias']),
				'arr_output_faktor_error_z_net1' => json_encode($data['output']['faktor_error_z_net1']),
				'arr_output_faktor_error_z_net2' => json_encode($data['output']['faktor_error_z_net2']),
				'arr_output_faktor_error_z1' => json_encode($data['output']['faktor_error_z1']),
				'arr_output_faktor_error_z2' => json_encode($data['output']['faktor_error_z2']),
				'arr_output_perubahan_bobot_v11' => json_encode($data['output']['perubahan_bobot_v11']),
				'arr_output_perubahan_bobot_v12' => json_encode($data['output']['perubahan_bobot_v12']),
				'arr_output_perubahan_bobot_v21' => json_encode($data['output']['perubahan_bobot_v21']),
				'arr_output_perubahan_bobot_v22' => json_encode($data['output']['perubahan_bobot_v22']),
				'arr_output_perubahan_bobot_vb1' => json_encode($data['output']['perubahan_bobot_vb1']),
				'arr_output_perubahan_bobot_vb2' => json_encode($data['output']['perubahan_bobot_vb2']),
				'arr_output_error' => json_encode($data['output']['error']),
				'arr_output_error2' => json_encode($data['output']['error2']),
				'mse' => $data['mse'],
			];

			$this->m_global->store($ins_det, 't_perhitungan_det');
			$arr_mse[] = $data['mse'];

			if ($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				echo 'gagal';
				return;
			}else{
				$this->db->trans_commit();
			}

			array_push($result, $data);
		}
		
		$mse_min = min($arr_mse);
		// update_t_perhitungan
		$this->m_global->update('t_perhitungan', ['mse_terkecil' => $mse_min], ['id' => $id_header]);

		echo 'sukses';

		// exit;
		
	}

	function random_0_1() 
	{
		return (float)rand() / (float)getrandmax();
	}

}
