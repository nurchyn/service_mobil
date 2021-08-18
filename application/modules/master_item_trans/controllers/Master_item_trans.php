<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Master_item_trans extends CI_Controller {
	
	const ID_JENIS_PEMBELIAN = 2;
	const ID_JENIS_PENGGAJIAN = 3;
	const ID_JENIS_INVESTASI = 4;
	const ID_JENIS_OPERSAIONAL = 5;
	const ID_JENIS_PENGELUARAN_LAIN = 6;
	const ID_JENIS_PENERIMAAN_LAIN = 7;

	public function __construct()
	{
		parent::__construct();
		if($this->session->userdata('logged_in') === false) {
			return redirect('login');
		}

		$this->load->model('m_item_trans');
		$this->load->model('m_user');
		$this->load->model('m_global');
	}

	public function index()
	{
		$id_user = $this->session->userdata('id_user'); 
		$data_user = $this->m_user->get_detail_user($id_user);
		// $data_jabatan = $this->m_global->multi_row('*', 'deleted_at is null', 'm_jabatan', null, 'nama');
				
		/**
		 * data passing ke halaman view content
		 */
		$data = array(
			'title' => 'Pengelolaan Data Master Item Transaksi',
			'data_user' => $data_user,
			'jenis_trans' => $this->m_global->getSelectedData('m_jenis_trans', NULL)->result()
			// 'data_jabatan'	=> $data_jabatan
		);

		/**
		 * content data untuk template
		 * param (css : link css pada direktori assets/css_module)
		 * param (modal : modal komponen pada modules/nama_modul/views/nama_modal)
		 * param (js : link js pada direktori assets/js_module)
		 */
		$content = [
			'css' 	=> null,
			'modal' => 'modal_master_item_trans',
			'js'	=> 'master_item_trans.js',
			'view'	=> 'view_master_item_trans'
		];

		$this->template_view->load_view($content, $data);
	}

	public function list_item_trans()
	{
		$list = $this->m_item_trans->get_datatable();
		
		$data = array();
		$no =$_POST['start'];
		foreach ($list as $jenis) {
			$no++;
			$row = array();
			//loop value tabel db
			$row[] = $no;
			$row[] = $jenis->kode_jenis;
			$row[] = $jenis->nama_jenis;
			$row[] = $jenis->nama;
			$row[] = 'Rp '.number_format($jenis->harga_awal,2);
			$row[] = 'Rp '.number_format($jenis->harga,2);
			// $aktif_txt = ($diag->is_aktif == 1) ? '<span style="color:blue;">Aktif</span>' : '<span style="color:red;">Non Aktif</span>';
			// $row[] = $aktif_txt;			
			
			$str_aksi = '
				<div class="btn-group">
					<button type="button" class="btn btn-sm btn_1 dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Opsi</button>
					<div class="dropdown-menu">
						<button class="dropdown-item" onclick="edit_item_trans(\''.$jenis->id.'\')">
							<i class="la la-pencil"></i> Edit Item Transaksi
						</button>
						<button class="dropdown-item" onclick="delete_item_trans(\''.$jenis->id.'\')">
							<i class="la la-trash"></i> Hapus
						</button>
			';

			$row[] = $str_aksi;

			$data[] = $row;

		}//end loop

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->m_item_trans->count_all(),
			"recordsFiltered" => $this->m_item_trans->count_filtered(),
			"data" => $data
		];
		
		echo json_encode($output);
	}

	public function edit_item_trans()
	{
		$this->load->library('Enkripsi');
		$id_user = $this->session->userdata('id_user');
		$data_user = $this->m_user->get_by_id($id_user);
	
		$id = $this->input->post('id');
		$oldData = $this->m_item_trans->get_by_id($id);
		
		if(!$oldData){
			return redirect($this->uri->segment(1));
		}

		$data = array(
			'data_user' => $data_user,
			'old_data'	=> $oldData
		);
		
		echo json_encode($data);
	}

	public function add_data_item_trans()
	{
	
		$this->load->library('Enkripsi');
		$obj_date = new DateTime();
		$timestamp = $obj_date->format('Y-m-d H:i:s');
		$arr_valid = $this->rule_validasi();
		
		$id_jenis_trans = $this->input->post('id_jenis_trans');
		$nama = $this->input->post('nama');
		$harga_awal = $this->input->post('harga_awal');
		$harga = $this->input->post('harga');
		$keterangan    = $this->input->post('keterangan');

		if ($arr_valid['status'] == FALSE) {
			echo json_encode($arr_valid);
			return;
		}


		$this->db->trans_begin();
		
		$data = [
			'id_jenis_trans' => $id_jenis_trans,
			'nama' => $nama,
			'harga_awal' => $harga_awal,
			'harga' => $harga,
			'keterangan'         => $keterangan,
			'created_at' 	=> $timestamp
		];
		
		$insert = $this->m_item_trans->save($data);

		$data_log = json_encode($data);
		$this->lib_fungsi->catat_log_aktifitas('CREATE', null, $data_log);
		
		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$retval['status'] = false;
			$retval['pesan'] = 'Gagal menambahkan Data Item Transaksi';
		}else{
			$this->db->trans_commit();
			$retval['status'] = true;
			$retval['pesan'] = 'Sukses menambahkan Data Item Transaksi';
		}

		echo json_encode($retval);
	}

	public function update_data_item_trans()
	{
		$id_user = $this->session->userdata('id_user'); 
		$this->load->library('Enkripsi');
		$obj_date = new DateTime();
		$timestamp = $obj_date->format('Y-m-d H:i:s');
		$arr_valid = $this->rule_validasi(true);

		if ($arr_valid['status'] == FALSE) {
			echo json_encode($arr_valid);
			return;
		}

		$id_jenis_trans = $this->input->post('id_jenis_trans');
		$nama = $this->input->post('nama');
		$harga_awal = $this->input->post('harga_awal');
		$harga = $this->input->post('harga');
		$keterangan    = $this->input->post('keterangan');
		$old_data = $this->m_global->single_row_array('*', ['id' => $this->input->post('id')], 'm_item_trans');
		$this->db->trans_begin();
		
		$data = [
			'id_jenis_trans' => $id_jenis_trans,
			'nama' => $nama,
			'harga_awal' => $harga_awal,
			'harga' => $harga,
			'keterangan'         => $keterangan,
			'updated_at' 	=> $timestamp
		];

		$where = ['id' => $this->input->post('id')];
		$update = $this->m_item_trans->update($where, $data);

		$data_log_old = json_encode($old_data);
		$data_log_new = json_encode($data);
		$this->lib_fungsi->catat_log_aktifitas('UPDATE', $data_log_old, $data_log_new);
				
		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$data['status'] = false;
			$data['pesan'] = 'Gagal update Master Item Transaksi';
		}else{
			$this->db->trans_commit();
			$data['status'] = true;
			$data['pesan'] = 'Sukses update Master Item Transaksi';
		}
		
		echo json_encode($data);
	}

	/**
	 * Hanya melakukan softdelete saja
	 * isi kolom updated_at dengan datetime now()
	 */
	public function delete_item_trans()
	{
		$id = $this->input->post('id');
		$old_data = $this->m_global->single_row_array('*', ['id' => $id], 'm_item_trans');
		$del = $this->m_item_trans->softdelete_by_id($id);

		$data_log_old = json_encode($old_data);
		$this->lib_fungsi->catat_log_aktifitas('DELETE', $data_log_old, null);
		if($del) {
			$retval['status'] = TRUE;
			$retval['pesan'] = 'Data Master Item Transaksi Berhasil dihapus';
		}else{
			$retval['status'] = FALSE;
			$retval['pesan'] = 'Data Master Item Transaksi Gagal dihapus';
		}

		echo json_encode($retval);
	}

	public function get_select_pembelian()
	{
		$term = $this->input->get('term');
		$data_pembelian = $this->m_global->multi_row('*', ['deleted_at' => null, 'id_jenis_trans' => self::ID_JENIS_PEMBELIAN, 'nama like' => '%'.$term.'%'], 'm_item_trans', null, 'nama');
		if($data_pembelian) {
			foreach ($data_pembelian as $key => $value) {
				$row['id'] = $value->id;
				$row['text'] = $value->nama;
				$row['harga'] = $value->harga;
				$retval[] = $row;
			}
		}else{
			$retval = false;
		}
		echo json_encode($retval);
	}

	public function get_select_penggajian()
	{
		$term = $this->input->get('term');
		$data_pembelian = $this->m_global->multi_row('*', ['deleted_at' => null, 'id_jenis_trans' => self::ID_JENIS_PENGGAJIAN, 'nama like' => '%'.$term.'%'], 'm_item_trans', null, 'nama');
		if($data_pembelian) {
			foreach ($data_pembelian as $key => $value) {
				$row['id'] = $value->id;
				$row['text'] = $value->nama;
				$row['harga'] = $value->harga;
				$retval[] = $row;
			}
		}else{
			$retval = false;
		}
		echo json_encode($retval);
	}

	public function get_select_investasi()
	{
		$term = $this->input->get('term');
		$data_pembelian = $this->m_global->multi_row('*', ['deleted_at' => null, 'id_jenis_trans' => self::ID_JENIS_INVESTASI, 'nama like' => '%'.$term.'%'], 'm_item_trans', null, 'nama');
		if($data_pembelian) {
			foreach ($data_pembelian as $key => $value) {
				$row['id'] = $value->id;
				$row['text'] = $value->nama;
				$row['harga'] = $value->harga;
				$retval[] = $row;
			}
		}else{
			$retval = false;
		}
		echo json_encode($retval);
	}

	public function get_select_operasional()
	{
		$term = $this->input->get('term');
		$data_pembelian = $this->m_global->multi_row('*', ['deleted_at' => null, 'id_jenis_trans' => self::ID_JENIS_OPERSAIONAL, 'nama like' => '%'.$term.'%'], 'm_item_trans', null, 'nama');
		if($data_pembelian) {
			foreach ($data_pembelian as $key => $value) {
				$row['id'] = $value->id;
				$row['text'] = $value->nama;
				$row['harga'] = $value->harga;
				$retval[] = $row;
			}
		}else{
			$retval = false;
		}
		echo json_encode($retval);
	}

	public function get_select_pengeluaran_lain()
	{
		$term = $this->input->get('term');
		$data_pembelian = $this->m_global->multi_row('*', ['deleted_at' => null, 'id_jenis_trans' => self::ID_JENIS_PENGELUARAN_LAIN, 'nama like' => '%'.$term.'%'], 'm_item_trans', null, 'nama');
		if($data_pembelian) {
			foreach ($data_pembelian as $key => $value) {
				$row['id'] = $value->id;
				$row['text'] = $value->nama;
				$row['harga'] = $value->harga;
				$retval[] = $row;
			}
		}else{
			$retval = false;
		}
		echo json_encode($retval);
	}

	public function get_select_penerimaan_lain()
	{
		$term = $this->input->get('term');
		$data_pembelian = $this->m_global->multi_row('*', ['deleted_at' => null, 'id_jenis_trans' => self::ID_JENIS_PENERIMAAN_LAIN, 'nama like' => '%'.$term.'%'], 'm_item_trans', null, 'nama');
		if($data_pembelian) {
			foreach ($data_pembelian as $key => $value) {
				$row['id'] = $value->id;
				$row['text'] = $value->nama;
				$row['harga'] = $value->harga;
				$retval[] = $row;
			}
		}else{
			$retval = false;
		}
		echo json_encode($retval);
	}
	// ===============================================
	private function rule_validasi()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		if ($this->input->post('id_jenis_trans') == '') {
			$data['inputerror'][] = 'id_jenis_trans';
            $data['error_string'][] = 'Wajib memilih Jenis Transaksi';
            $data['status'] = FALSE;
		}

		if ($this->input->post('nama') == '') {
			$data['inputerror'][] = 'nama';
            $data['error_string'][] = 'Wajib mengisi Nama Item Transaksi';
            $data['status'] = FALSE;
		}

		if ($this->input->post('harga_awal') == '') {
			$data['inputerror'][] = 'harga_awal';
            $data['error_string'][] = 'Wajib mengisi Harga Awal';
            $data['status'] = FALSE;
		}

		if ($this->input->post('harga') == '') {
			$data['inputerror'][] = 'harga';
            $data['error_string'][] = 'Wajib mengisi Harga';
            $data['status'] = FALSE;
		}

		
	
        return $data;
	}
}
