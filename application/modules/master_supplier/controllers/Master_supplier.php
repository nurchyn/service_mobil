<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Master_supplier extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		if($this->session->userdata('logged_in') === false) {
			return redirect('login');
		}

		$this->load->model('m_supplier');
		$this->load->model('m_user');
		$this->load->model('m_global');
		$this->load->model('set_role/m_set_role', 'm_role');
	}

	public function get_select_supplier()
	{
		$term = $this->input->get('term');
		$data = $this->m_global->multi_row('*', ['deleted_at' => null, 'nama_supplier like' => '%'.$term.'%'], 'm_supplier', null, 'nama_supplier');
		if($data) {
			foreach ($data as $key => $value) {
				$row['id'] = $value->id;
				$row['text'] = $value->nama_supplier;
				$retval[] = $row;
			}
		}else{
			$retval = false;
		}
		echo json_encode($retval);
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
			'title' => 'Pengelolaan Data Supplier',
			'data_user' => $data_user,
			'data_role'	=> $data_role
		);

		/**
		 * content data untuk template
		 * param (css : link css pada direktori assets/css_module)
		 * param (modal : modal komponen pada modules/nama_modul/views/nama_modal)
		 * param (js : link js pada direktori assets/js_module)
		 */
		$content = [
			'css' 	=> null,
			'modal' => 'modal_master_supplier',
			'js'	=> 'master_supplier.js',
			'view'	=> 'view_master_supplier'
		];

		$this->template_view->load_view($content, $data);
	}

	public function list_supplier()
	{
		$list = $this->m_supplier->get_datatable_user();
		$data = array();
		$no =$_POST['start'];
		foreach ($list as $supplier) {
			$no++;
			$row = array();
			//loop value tabel db
			$row[] = $no;
			$row[] = $supplier->nama_supplier;
			$row[] = $supplier->alamat_supplier;
			$row[] = $supplier->telp_supplier;
			
			$str_aksi = '
				<div class="btn-group">
					<button type="button" class="btn btn-sm btn_1 dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Opsi</button>
					<div class="dropdown-menu">
						<button class="dropdown-item" onclick="edit_supplier(\''.$supplier->id.'\')">
							<i class="la la-pencil"></i> Edit Supplier
						</button>
						<button class="dropdown-item" onclick="delete_supplier(\''.$supplier->id.'\')">
							<i class="la la-trash"></i> Hapus
						</button>
			';


			$str_aksi .= '</div></div>';
			$row[] = $str_aksi;

			$data[] = $row;
		}//end loop

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->m_supplier->count_all(),
			"recordsFiltered" => $this->m_supplier->count_filtered(),
			"data" => $data
		];
		
		echo json_encode($output);
	}

	public function add_data_supplier()
	{
		$this->load->library('Enkripsi');
		$obj_date = new DateTime();
		$timestamp = $obj_date->format('Y-m-d H:i:s');
		$arr_valid = $this->rule_validasi();
	
		$nama = $this->input->post('nama');
		$alamat = $this->input->post('alamat');
		$telp = $this->input->post('telp');
		
		if ($arr_valid['status'] == FALSE) {
			echo json_encode($arr_valid);
			return;
		}


		$this->db->trans_begin();
		
		$data_supplier = [
			'nama_supplier' => $nama,
			'alamat_supplier' => $alamat,
			'telp_supplier' => $telp,
			'created_at' => $timestamp
		];
		
		$insert = $this->m_supplier->save($data_supplier);
		
		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$retval['status'] = false;
			$retval['pesan'] = 'Gagal menambahkan Supplier';
		}else{
			$this->db->trans_commit();
			$retval['status'] = true;
			$retval['pesan'] = 'Sukses menambahkan Supplier';
		}

		echo json_encode($retval);
	}

	public function edit_supplier()
	{
		$this->load->library('Enkripsi');
		$id_user = $this->session->userdata('id_user');
		$data_user = $this->m_user->get_by_id($id_user);
	
		$id = $this->input->post('id');
		//$oldData = $this->m_user->get_by_id($id);

		$where = ['m_supplier.id' => $id];

		$oldData = $this->m_global->getSelectedData('m_supplier', $where)->row();
		if(!$oldData){
			return redirect($this->uri->segment(1));
		}
		
		$data = array(
			'data_user' => $data_user,
			'old_data'	=> $oldData,
	
		);
		
		echo json_encode($data);
	}

	public function update_data_supplier()
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
		$alamat = $this->input->post('alamat');
		$telp = $this->input->post('telp');
		

		
		$this->db->trans_begin();

		$data_supplier = [
			'nama_supplier' => $nama,
			'alamat_supplier' => $alamat,
			'telp_supplier' => $telp,
			'updated_at' => $timestamp
		];

		$where = ['id' => $id];
		$update = $this->m_supplier->update($where, $data_supplier);

		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$data['status'] = false;
			$data['pesan'] = 'Gagal update Master Supplier';
		}else{
			$this->db->trans_commit();
			$data['status'] = true;
			$data['pesan'] = 'Sukses update Master Supplier';
		}
		
		echo json_encode($data);
	}

	
	/**
	 * Hanya melakukan softdelete saja
	 * isi kolom updated_at dengan datetime now()
	 */
	public function delete_supplier()
	{
		$id = $this->input->post('id');
		$del = $this->m_supplier->softdelete_by_id($id);
		if($del) {
			$retval['status'] = TRUE;
			$retval['pesan'] = 'Data Master Supplier dihapus';
		}else{
			$retval['status'] = FALSE;
			$retval['pesan'] = 'Data Master Supplier dihapus';
		}

		echo json_encode($retval);
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

		if ($this->input->post('alamat') == '') {
			$data['inputerror'][] = 'alamat';
			$data['error_string'][] = 'Wajib mengisi Alamat';
			$data['status'] = FALSE;
		}
	

        return $data;
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
}
