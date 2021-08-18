<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Master_barang extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		if($this->session->userdata('logged_in') === false) {
			return redirect('login');
		}

		$this->load->model('m_barang');
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
			'title' => 'Pengelolaan Data Barang',
			'data_user' => $data_user,
			'data_role'	=> $data_role,
			'kategori'  => $this->m_global->getSelectedData('m_kategori_barang', ['is_aktif'=> '1'])->result()
		);

		/**
		 * content data untuk template
		 * param (css : link css pada direktori assets/css_module)
		 * param (modal : modal komponen pada modules/nama_modul/views/nama_modal)
		 * param (js : link js pada direktori assets/js_module)
		 */
		$content = [
			'css' 	=> null,
			'modal' => 'modal_master_barang',
			'js'	=> 'master_barang.js',
			'view'	=> 'view_master_barang'
		];

		$this->template_view->load_view($content, $data);
	}

	public function list_barang()
	{
		$list = $this->m_barang->get_datatable_user();
		$data = array();
		$no =$_POST['start'];
		foreach ($list as $barang) {
			$no++;
			$row = array();
			//loop value tabel db
			$row[] = $no;
			$row[] = $barang->nama_barang;
			$row[] = $barang->harga_awal;
			$row[] = $barang->harga_jual;
			$row[] = $barang->stok;
			$row[] = $barang->nama_kategori;
			// $row[] = '<img src="'.base_url().'/files/img/barcode/'.$member->kode_member.'.jpg"  width="200">';

			$str_aksi = '
				<div class="btn-group">
					<button type="button" class="btn btn-sm btn_1 dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Opsi</button>
					<div class="dropdown-menu">
						<button class="dropdown-item" onclick="edit_barang(\''.$barang->id.'\')">
							<i class="la la-pencil"></i> Edit Data Barang
						</button>
						<button class="dropdown-item" onclick="delete_barang(\''.$barang->id.'\')">
							<i class="la la-trash"></i> Hapus
						</button>
			';


			$str_aksi .= '</div></div>';
			$row[] = $str_aksi;

			$data[] = $row;
		}//end loop

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->m_barang->count_all(),
			"recordsFiltered" => $this->m_barang->count_filtered(),
			"data" => $data
		];
		
		echo json_encode($output);
	}

	public function add_data_barang()
	{
		$this->load->library('Enkripsi');
		$obj_date = new DateTime();
		$timestamp = $obj_date->format('Y-m-d H:i:s');
		$arr_valid = $this->rule_validasi();
	
		$nama_barang = $this->input->post('nama');
		$harga_awal = $this->input->post('harga_awal');
		$harga_jual = $this->input->post('harga_jual');
		$stok = $this->input->post('stok');
		$id_kategori = $this->input->post('id_kategori');
		
		if ($arr_valid['status'] == FALSE) {
			echo json_encode($arr_valid);
			return;
		}


		$this->db->trans_begin();
		
		$data_barang = [
			'nama_barang' => $nama_barang,
			'harga_jual' => $harga_jual,
			'harga_awal' => $harga_awal,
			'stok' => $stok,
			'id_kategori_barang' => $id_kategori,
			'created_at' => $timestamp
		];
		
		$insert = $this->m_barang->save($data_barang);
		
		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$retval['status'] = false;
			$retval['pesan'] = 'Gagal menambahkan Data Barang';
		}else{
			$this->db->trans_commit();
			$retval['status'] = true;
			$retval['pesan'] = 'Sukses menambahkan Data Barang';
		}

		echo json_encode($retval);
	}

	public function edit_barang()
	{
		$this->load->library('Enkripsi');
		$id_user = $this->session->userdata('id_user');
		$data_user = $this->m_user->get_by_id($id_user);
	
		$id = $this->input->post('id');
		//$oldData = $this->m_user->get_by_id($id);

		$where = ['m_barang.id' => $id];

		$oldData = $this->m_global->getSelectedData('m_barang', $where)->row();
		if(!$oldData){
			return redirect($this->uri->segment(1));
		}
		
		$data = array(
			'data_user' => $data_user,
			'old_data'	=> $oldData,
	
		);
		
		echo json_encode($data);
	}

	public function update_data_barang()
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
		$nama_barang = $this->input->post('nama');
		$harga_awal = $this->input->post('harga_awal');
		$harga_jual = $this->input->post('harga_jual');
		$stok = $this->input->post('stok');
		$id_kategori = $this->input->post('id_kategori');
		

		
		$this->db->trans_begin();

		$data_barang = [
			'nama_barang' => $nama_barang,
			'harga_awal' => $harga_awal,
			'harga_jual' => $harga_jual,
			'stok'		 => $stok,
			'id_kategori_barang' => $id_kategori,
			'updated_at' => $timestamp
		];

		$where = ['id' => $id];
		$update = $this->m_barang->update($where, $data_barang);

		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$data['status'] = false;
			$data['pesan'] = 'Gagal update Data Barang';
		}else{
			$this->db->trans_commit();
			$data['status'] = true;
			$data['pesan'] = 'Sukses update Data Barang';
		}
		
		echo json_encode($data);
	}

	/**
	 * Hanya melakukan softdelete saja
	 * isi kolom updated_at dengan datetime now()
	 */
	public function delete_barang()
	{
		$id = $this->input->post('id');
		$del = $this->m_barang->softdelete_by_id($id);
		if($del) {
			$retval['status'] = TRUE;
			$retval['pesan'] = 'Data Master Barang dihapus';
		}else{
			$retval['status'] = FALSE;
			$retval['pesan'] = 'Data Master Barang dihapus';
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
			$data['error_string'][] = 'Wajib mengisi Nama';
			$data['status'] = FALSE;
		}

		if ($this->input->post('harga_awal') == '') {
			$data['inputerror'][] = 'harga_awal';
			$data['error_string'][] = 'Wajib mengisi Jenis Kelamin';
			$data['status'] = FALSE;
		}

		if ($this->input->post('harga_jual') == '') {
			$data['inputerror'][] = 'harga_jual';
			$data['error_string'][] = 'Wajib mengisi Jenis Kelamin';
			$data['status'] = FALSE;
		}
	
		if ($this->input->post('id_kategori') == '') {
			$data['inputerror'][] = 'id_kategori';
			$data['error_string'][] = 'Wajib mengisi Kategori Barang';
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
