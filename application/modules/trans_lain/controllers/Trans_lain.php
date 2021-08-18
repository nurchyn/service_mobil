<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Trans_lain extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		if($this->session->userdata('logged_in') === false) {
			return redirect('login');
		}

		$this->load->model('m_user');
		$this->load->model('m_global');
		$this->load->model('t_transaksi');
		$this->load->model('t_transaksi_det');
	}

	######################################### BASE FUNCTION ############################################
	public function index()
	{
		$id_user = $this->session->userdata('id_user'); 
		$data_user = $this->m_user->get_detail_user($id_user);
		$list_transaksi = $this->m_global->multi_row('*', ['kode_jenis <>' => 'A-01', 'deleted_at' => null], 'm_jenis_trans', NULL, 'kode_jenis');
		
		/**
		 * data passing ke halaman view content
		 */
		$data = array(
			'title' => 'Transaksi Lain-Lain',
			'data_user' => $data_user,
			'list_transaksi' => $list_transaksi,
		);

		/**
		 * content data untuk template
		 * param (css : link css pada direktori assets/css_module)
		 * param (modal : modal komponen pada modules/nama_modul/views/nama_modal)
		 * param (js : link js pada direktori assets/js_module)
		 */
		$content = [
			'css' 	=> null,
			'modal' => ['modal_pembelian','modal_penggajian', 'modal_investasi', 'modal_operasional', 'modal_out_lain', 'modal_in_lain'],
			'js'	=> ['trans_lain.js', 'pembelian.js', 'penggajian.js', 'investasi.js', 'operasional.js', 'out_lain.js', 'in_lain.js'],
			'view'	=> 'view_trans_lain'
		];

		$this->template_view->load_view($content, $data);
	}

	private function clean_txt_div($text)
	{
		$slug = $text;
		$slug = str_ireplace('div-', "", $slug);
		$slug = str_ireplace('-modal', "", $slug);

		return $slug;
	}

	public function get_old_data()
	{
		$txt_div_modal = $this->input->post('menu');
		$slug = $this->clean_txt_div($this->input->post('menu'));
		$q_jenis = $this->m_global->single_row('*', ['slug' => $slug], 'm_jenis_trans');

		$select = "t_transaksi.*, t_transaksi_det.harga_satuan, m_item_trans.nama as nama_item, t_transaksi_det.qty";
		$where = ['t_transaksi.deleted_at' => null, 't_transaksi.id_jenis_trans' => $q_jenis->id];
		$table = 't_transaksi';
		$join = [ 
			['table' => 't_transaksi_det', 'on' => 't_transaksi.id = t_transaksi_det.id_transaksi'],
			['table' => 'm_item_trans', 'on' => 't_transaksi_det.id_item_trans = m_item_trans.id']
		];
		$order_by = "t_transaksi.tgl_trans desc";
	
		$datanya = $this->m_global->multi_row($select,$where,$table, $join, $order_by);
		
		switch ($slug) {
			case 'pembelian':	
				echo json_encode(['data' => $datanya, 'status' => true, 'menu' => $slug]);
				break;

			case 'penggajian':			
				echo json_encode(['data'=>$datanya, 'status' => true, 'menu' => $slug]);
				break;
			
			case 'investasi':
				echo json_encode(['data'=>$datanya, 'status' => true, 'menu' => $slug]);
				break;

			case 'operasional':
				echo json_encode(['data'=>$datanya, 'status' => true, 'menu' => $slug]);
				break;

			case 'pengeluaran-lain-lain':
				echo json_encode(['data'=>$datanya, 'status' => true, 'menu' => $slug]);
				break;

			case 'penerimaan-lain-lain':
				echo json_encode(['data'=>$datanya, 'status' => true, 'menu' => $slug]);
				break;
						
			default:
				$datanya = null;
				echo json_encode(['data'=> null, 'status' => false, 'menu' => false]);
				break;
		}
	}
	######################################### END BASE FUNCTION ############################################

	################################ PEMBELIAN AREA #############################################
	private function rule_validasi_pembelian()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		if ($this->input->post('item_beli') == '') {
			$data['inputerror'][] = 'item_beli';
			$data['error_string'][] = 'Wajib Mengisi Pembelian';
			$data['status'] = FALSE;
			$data['is_select2'][] = TRUE;
		}

		if ($this->input->post('sup_beli') == '') {
			$data['inputerror'][] = 'sup_beli';
			$data['error_string'][] = 'Wajib Mengisi Supplier';
			$data['status'] = FALSE;
			$data['is_select2'][] = TRUE;
		}

		if ($this->input->post('qty_beli') == '') {
			$data['inputerror'][] = 'qty_beli';
			$data['error_string'][] = 'Wajib Mengisi qty';
			$data['status'] = FALSE;
			$data['is_select2'][] = FALSE;
		}

		if ($this->input->post('harga_beli') == '') {
			$data['inputerror'][] = 'harga_beli';
			$data['error_string'][] = 'Wajib Mengisi harga';
			$data['status'] = FALSE;
			$data['is_select2'][] = FALSE;
		}
			
        return $data;
	}
	
	public function simpan_form_pembelian()
	{
		$data_log_arr = [];
		$obj_date = new DateTime();
		$timestamp = $obj_date->format('Y-m-d H:i:s');
		$datenow = $obj_date->format('Y-m-d');
		$arr_valid = $this->rule_validasi_pembelian();
		
		if ($arr_valid['status'] == FALSE) {
			echo json_encode($arr_valid);
			return;
		}

		$this->db->trans_begin();
		$id_header = gen_uuid();
		$slug_trans = $this->input->post('slug_trans');
		$item_beli = $this->input->post('item_beli');
		$sup_beli = $this->input->post('sup_beli');
		$qty_beli = $this->input->post('qty_beli');
		$tanggal = $obj_date->createFromFormat('d/m/Y', $this->input->post('tgl_beli'))->format('Y-m-d');
		$tahun = (int)$obj_date->createFromFormat('d/m/Y', $this->input->post('tgl_beli'))->format('Y');
		$bulan = (int)$obj_date->createFromFormat('d/m/Y', $this->input->post('tgl_beli'))->format('m');
		$harga_beli = $this->input->post('harga_beli_raw');
		$hargatot_beli = $this->input->post('hargatot_beli_raw');
		$cek_jenis = $this->m_global->single_row('id', ['slug' => $slug_trans], 'm_jenis_trans');
		###insert
		$data = [
			'id' => $id_header,
			'id_jenis_trans' => $cek_jenis->id,
			'id_supplier' => $sup_beli,
			'bulan_trans' => $bulan,
			'tahun_trans' => $tahun,
			'tgl_trans' => $tanggal,
			'harga_total' => $hargatot_beli,
			'id_user' => $this->session->userdata('id_user'),
			'created_at' => $timestamp
		];
					
		$insert = $this->m_global->store($data, 't_transaksi');

		if($insert){
			$data_det = [
				'id' => gen_uuid(),
				'id_transaksi' => $id_header,
				'id_item_trans' => $item_beli,
				'harga_satuan' => $harga_beli,
				'qty' => $qty_beli,
				'created_at' => $timestamp
			];
						
			$insert_det = $this->m_global->store($data_det, 't_transaksi_det');
			$data_log_arr[] = $data_det;
		}

		$data_log = json_encode($data_log_arr);
		$this->lib_fungsi->catat_log_aktifitas('CREATE', null, $data_log);
		
		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$retval['status'] = false;
			$retval['pesan'] = 'Gagal Menambah Data';
		}else{
			$this->db->trans_commit();
			$retval['status'] = true;
			$retval['pesan'] = 'Sukses Menambah Data';
		}

		echo json_encode($retval);
	}

	public function load_form_tabel_pembelian()
	{
		$obj_date = new DateTime();
		$timestamp = $obj_date->format('Y-m-d H:i:s');
		$data = $this->input->post('data');
		
		$html = '';
		
		if($data){
			foreach ($data as $key => $value) {
				$html .= '<tr><td>'.($key+1).'</td><td>'.$obj_date->createFromFormat('Y-m-d', $value['tgl_trans'])->format('d-m-Y').'</td><td>'.$value['nama_item'].'</td><td align="right">'.bulan_indo($value['bulan_trans']).'</td><td>'.$value['tahun_trans'].'</td><td align="right">'.number_format($value['harga_satuan'],0,',','.').'</td><td>'.number_format($value['qty'],0,',','.').'</td><td align="right">'.number_format($value['harga_total'],0,',','.').'</td><td><button type="button" class="btn btn-sm btn-danger" onclick="hapus_pembelian(\''.$value['id'].'\')"><i class="la la-trash"></i></button></td></tr>';
			}
		}else{
			$slug = $this->clean_txt_div($this->input->post('activeModal'));
			$q_jenis = $this->m_global->single_row('*', ['slug' => $slug], 'm_jenis_trans');

			$select = "t_transaksi.*, m_item_trans.nama as nama_item, t_transaksi_det.qty, t_transaksi_det.harga_satuan";
			$where = ['t_transaksi.deleted_at' => null, 't_transaksi.id_jenis_trans' => $q_jenis->id];
			$table = 't_transaksi';
			$join = [ 
				['table' => 't_transaksi_det', 'on' => 't_transaksi.id = t_transaksi_det.id_transaksi'],
				['table' => 'm_item_trans', 'on' => 't_transaksi_det.id_item_trans = m_item_trans.id']
			];
			$order_by = "t_transaksi.tgl_trans desc";
		
			$datanya = $this->m_global->multi_row($select,$where,$table, $join, $order_by);
						
			if($datanya){
				foreach ($datanya as $key => $value) {
					$html .= '<tr><td>'.($key+1).'</td><td>'.$obj_date->createFromFormat('Y-m-d', $value->tgl_trans)->format('d-m-Y').'</td><td>'.$value->nama_item.'</td><td align="right">'.bulan_indo($value->bulan_trans).'</td><td>'.$value->tahun_trans.'</td><td align="right">'.number_format($value->harga_satuan,0,',','.').'</td><td>'.number_format($value->qty,0,',','.').'</td><td align="right">'.number_format($value->harga_total,0,',','.').'</td><td><button type="button" class="btn btn-sm btn-danger" onclick="hapus_pembelian(\''.$value->id.'\')"><i class="la la-trash"></i></button></td></tr>';
				}
			}
		}

		echo json_encode([
			'html' => $html
		]);
	}

	public function delete_data_pembelian()
	{
		$data_log_arr = [];
		$id = $this->input->post('id');
		$this->db->trans_begin();
		$old_data = $this->t_transaksi_det->get_by_condition(['id_transaksi' => $id, 'deleted_at' => null]);

		$del_1 = $this->t_transaksi_det->softdelete_by_trans($id);
		$del_2 = $this->t_transaksi->softdelete_by_id($id);

		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$retval['status'] = false;
			$retval['pesan'] = 'Gagal Menghapus Data';
		}else{
			$this->db->trans_commit();
			$retval['status'] = true;
			$retval['pesan'] = 'Sukses Menghapus Data';
		}

		$data_log = json_encode($old_data);
		$this->lib_fungsi->catat_log_aktifitas('DELETE', $data_log, null);

		echo json_encode($retval);
	}
	################################ END PEMBELIAN AREA #############################################

	################################ PENGGAJIAN AREA #############################################
	private function rule_validasi_penggajian()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		if ($this->input->post('item_gaji') == '') {
			$data['inputerror'][] = 'item_gaji';
			$data['error_string'][] = 'Wajib Mengisi Gaji';
			$data['status'] = FALSE;
			$data['is_select2'][] = TRUE;
		}

		if ($this->input->post('tahun_gaji') == '') {
			$data['inputerror'][] = 'tahun_gaji';
			$data['error_string'][] = 'Wajib Mengisi Tahun Gaji';
			$data['status'] = FALSE;
			$data['is_select2'][] = TRUE;
		}

		if ($this->input->post('bulan_gaji') == '') {
			$data['inputerror'][] = 'bulan_gaji';
			$data['error_string'][] = 'Wajib Mengisi Bulan Gaji';
			$data['status'] = FALSE;
			$data['is_select2'][] = FALSE;
		}

		if ($this->input->post('harga_gaji') == '') {
			$data['inputerror'][] = 'harga_gaji';
			$data['error_string'][] = 'Wajib Mengisi Nilai Gaji Total';
			$data['status'] = FALSE;
			$data['is_select2'][] = FALSE;
		}
			
        return $data;
	}
	
	public function simpan_form_penggajian()
	{
		$data_log_arr = [];
		$obj_date = new DateTime();
		$timestamp = $obj_date->format('Y-m-d H:i:s');
		$datenow = $obj_date->format('Y-m-d');
		$arr_valid = $this->rule_validasi_penggajian();
		
		if ($arr_valid['status'] == FALSE) {
			echo json_encode($arr_valid);
			return;
		}

		$this->db->trans_begin();
		$id_header = gen_uuid();
		$slug_trans = $this->input->post('slug_trans');
		$item_gaji = $this->input->post('item_gaji');
		$tahun = $this->input->post('tahun_gaji');
		$bulan = $this->input->post('bulan_gaji');
		$total_gaji = $this->input->post('harga_gaji_raw');
		
		$cek_jenis = $this->m_global->single_row('id', ['slug' => $slug_trans], 'm_jenis_trans');
		###insert
		$data = [
			'id' => $id_header,
			'id_jenis_trans' => $cek_jenis->id,
			'bulan_trans' => $bulan,
			'tahun_trans' => $tahun,
			'tgl_trans' => $datenow,
			'harga_total' => $total_gaji,
			'id_user' => $this->session->userdata('id_user'),
			'created_at' => $timestamp
		];
					
		$insert = $this->m_global->store($data, 't_transaksi');

		if($insert){
			$data_det = [
				'id' => gen_uuid(),
				'id_transaksi' => $id_header,
				'id_item_trans' => $item_gaji,
				'harga_satuan' => $total_gaji,
				'qty' => 1,
				'created_at' => $timestamp
			];
						
			$insert_det = $this->m_global->store($data_det, 't_transaksi_det');
			$data_log_arr[] = $data_det;
		}

		$data_log = json_encode($data_log_arr);
		$this->lib_fungsi->catat_log_aktifitas('CREATE', null, $data_log);
		
		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$retval['status'] = false;
			$retval['pesan'] = 'Gagal Menambah Data';
		}else{
			$this->db->trans_commit();
			$retval['status'] = true;
			$retval['pesan'] = 'Sukses Menambah Data';
		}

		echo json_encode($retval);
	}

	public function load_form_tabel_penggajian()
	{
		$obj_date = new DateTime();
		$timestamp = $obj_date->format('Y-m-d H:i:s');
		$data = $this->input->post('data');
		
		$html = '';
		
		if($data){
			foreach ($data as $key => $value) {
				$html .= '<tr><td>'.($key+1).'</td><td>'.$obj_date->createFromFormat('Y-m-d H:i:s', $value['created_at'])->format('d-m-Y').'</td><td>'.$value['nama_item'].'</td><td align="right">'.bulan_indo($value['bulan_trans']).'</td><td>'.$value['tahun_trans'].'</td><td align="right">'.number_format($value['harga_total'],0,',','.').'</td><td><button type="button" class="btn btn-sm btn-danger" onclick="hapus_penggajian(\''.$value['id'].'\')"><i class="la la-trash"></i></button></td></tr>';
			}
		}else{
			$slug = $this->clean_txt_div($this->input->post('activeModal'));
			$q_jenis = $this->m_global->single_row('*', ['slug' => $slug], 'm_jenis_trans');

			$select = "t_transaksi.*, m_item_trans.nama as nama_item, t_transaksi_det.qty, t_transaksi_det.harga_satuan";
			$where = ['t_transaksi.deleted_at' => null, 't_transaksi.id_jenis_trans' => $q_jenis->id];
			$table = 't_transaksi';
			$join = [ 
				['table' => 't_transaksi_det', 'on' => 't_transaksi.id = t_transaksi_det.id_transaksi'],
				['table' => 'm_item_trans', 'on' => 't_transaksi_det.id_item_trans = m_item_trans.id']
			];
			$order_by = "t_transaksi.tgl_trans desc";
		
			$datanya = $this->m_global->multi_row($select,$where,$table, $join, $order_by);

			if($datanya){
				foreach ($datanya as $key => $value) {
					$html .= '<tr><td>'.($key+1).'</td><td>'.$obj_date->createFromFormat('Y-m-d H:i:s', $value->created_at)->format('d-m-Y').'</td><td>'.$value->nama_item.'</td><td align="right">'.bulan_indo($value->bulan_trans).'</td><td>'.$value->tahun_trans.'</td><td align="right">'.number_format($value->harga_total,0,',','.').'</td><td><button type="button" class="btn btn-sm btn-danger" onclick="hapus_penggajian(\''.$value->id.'\')"><i class="la la-trash"></i></button></td></tr>';
				}
			}
		}

		echo json_encode([
			'html' => $html
		]);
	}

	public function delete_data_penggajian()
	{
		$data_log_arr = [];
		$id = $this->input->post('id');
		$this->db->trans_begin();
		
		$old_data = $this->t_transaksi_det->get_by_condition(['id_transaksi' => $id, 'deleted_at' => null]);
		$del_1 = $this->t_transaksi_det->softdelete_by_trans($id);
		$del_2 = $this->t_transaksi->softdelete_by_id($id);

		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$retval['status'] = false;
			$retval['pesan'] = 'Gagal Menghapus Data';
		}else{
			$this->db->trans_commit();
			$retval['status'] = true;
			$retval['pesan'] = 'Sukses Menghapus Data';
		}

		$data_log = json_encode($old_data);
		$this->lib_fungsi->catat_log_aktifitas('DELETE', $data_log, null);

		echo json_encode($retval);
	}
	################################ END PENGGAJIAN AREA #############################################

	################################ INVESTASI AREA #############################################
	private function rule_validasi_investasi()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		if ($this->input->post('item_inves') == '') {
			$data['inputerror'][] = 'item_inves';
			$data['error_string'][] = 'Wajib Mengisi Investasi';
			$data['status'] = FALSE;
			$data['is_select2'][] = TRUE;
		}

		if ($this->input->post('tgl_inves') == '') {
			$data['inputerror'][] = 'tgl_inves';
			$data['error_string'][] = 'Wajib Mengisi Tanggal';
			$data['status'] = FALSE;
			$data['is_select2'][] = FALSE;
		}

		if ($this->input->post('harga_inves') == '') {
			$data['inputerror'][] = 'harga_inves';
			$data['error_string'][] = 'Wajib Mengisi Nilai Investasi';
			$data['status'] = FALSE;
			$data['is_select2'][] = FALSE;
		}
			
        return $data;
	}
	
	public function simpan_form_investasi()
	{
		$data_log_arr = [];
		$obj_date = new DateTime();
		$timestamp = $obj_date->format('Y-m-d H:i:s');
		$datenow = $obj_date->format('Y-m-d');
		$arr_valid = $this->rule_validasi_investasi();
		
		if ($arr_valid['status'] == FALSE) {
			echo json_encode($arr_valid);
			return;
		}

		$this->db->trans_begin();
		$id_header = gen_uuid();
		$slug_trans = $this->input->post('slug_trans');
		$item_inves = $this->input->post('item_inves');
		$tanggal = $obj_date->createFromFormat('d/m/Y', $this->input->post('tgl_inves'))->format('Y-m-d');
		$tahun = (int)$obj_date->createFromFormat('d/m/Y', $this->input->post('tgl_inves'))->format('Y');
		$bulan = (int)$obj_date->createFromFormat('d/m/Y', $this->input->post('tgl_inves'))->format('m');
		$total_inves = $this->input->post('harga_inves_raw');
		
		$cek_jenis = $this->m_global->single_row('id', ['slug' => $slug_trans], 'm_jenis_trans');
		###insert
		$data = [
			'id' => $id_header,
			'id_jenis_trans' => $cek_jenis->id,
			'bulan_trans' => $bulan,
			'tahun_trans' => $tahun,
			'tgl_trans' => $tanggal,
			'harga_total' => $total_inves,
			'id_user' => $this->session->userdata('id_user'),
			'created_at' => $timestamp
		];
					
		$insert = $this->m_global->store($data, 't_transaksi');

		if($insert){
			$data_det = [
				'id' => gen_uuid(),
				'id_transaksi' => $id_header,
				'id_item_trans' => $item_inves,
				'harga_satuan' => $total_inves,
				'qty' => 1,
				'created_at' => $timestamp
			];
						
			$insert_det = $this->m_global->store($data_det, 't_transaksi_det');
			$data_log_arr[] = $data_det;
		}

		$data_log = json_encode($data_log_arr);
		$this->lib_fungsi->catat_log_aktifitas('CREATE', null, $data_log);
		
		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$retval['status'] = false;
			$retval['pesan'] = 'Gagal Menambah Data';
		}else{
			$this->db->trans_commit();
			$retval['status'] = true;
			$retval['pesan'] = 'Sukses Menambah Data';
		}

		echo json_encode($retval);
	}

	public function load_form_tabel_investasi()
	{
		$obj_date = new DateTime();
		$timestamp = $obj_date->format('Y-m-d H:i:s');
		$data = $this->input->post('data');
		
		$html = '';
		
		if($data){
			foreach ($data as $key => $value) {
				$html .= '<tr><td>'.($key+1).'</td><td>'.$obj_date->createFromFormat('Y-m-d', $value['tgl_trans'])->format('d-m-Y').'</td><td>'.$value['nama_item'].'</td><td align="right">'.bulan_indo($value['bulan_trans']).'</td><td>'.$value['tahun_trans'].'</td><td align="right">'.number_format($value['harga_total'],0,',','.').'</td><td><button type="button" class="btn btn-sm btn-danger" onclick="hapus_investasi(\''.$value['id'].'\')"><i class="la la-trash"></i></button></td></tr>';
			}
		}else{
			$slug = $this->clean_txt_div($this->input->post('activeModal'));
			$q_jenis = $this->m_global->single_row('*', ['slug' => $slug], 'm_jenis_trans');

			$select = "t_transaksi.*, m_item_trans.nama as nama_item, t_transaksi_det.qty, t_transaksi_det.harga_satuan";
			$where = ['t_transaksi.deleted_at' => null, 't_transaksi.id_jenis_trans' => $q_jenis->id];
			$table = 't_transaksi';
			$join = [ 
				['table' => 't_transaksi_det', 'on' => 't_transaksi.id = t_transaksi_det.id_transaksi'],
				['table' => 'm_item_trans', 'on' => 't_transaksi_det.id_item_trans = m_item_trans.id']
			];

			$order_by = "t_transaksi.tgl_trans desc";
		
			$datanya = $this->m_global->multi_row($select,$where,$table, $join, $order_by);

			if($datanya){
				foreach ($datanya as $key => $value) {
					$html .= '<tr><td>'.($key+1).'</td><td>'.$obj_date->createFromFormat('Y-m-d', $value->tgl_trans)->format('d-m-Y').'</td><td>'.$value->nama_item.'</td><td align="right">'.bulan_indo($value->bulan_trans).'</td><td>'.$value->tahun_trans.'</td><td align="right">'.number_format($value->harga_total,0,',','.').'</td><td><button type="button" class="btn btn-sm btn-danger" onclick="hapus_investasi(\''.$value->id.'\')"><i class="la la-trash"></i></button></td></tr>';
				}
			}
		}

		echo json_encode([
			'html' => $html
		]);
	}

	public function delete_data_investasi()
	{
		$data_log_arr = [];
		$id = $this->input->post('id');
		$this->db->trans_begin();
		
		$old_data = $this->t_transaksi_det->get_by_condition(['id_transaksi' => $id, 'deleted_at' => null]);
		$del_1 = $this->t_transaksi_det->softdelete_by_trans($id);
		$del_2 = $this->t_transaksi->softdelete_by_id($id);

		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$retval['status'] = false;
			$retval['pesan'] = 'Gagal Menghapus Data';
		}else{
			$this->db->trans_commit();
			$retval['status'] = true;
			$retval['pesan'] = 'Sukses Menghapus Data';
		}

		$data_log = json_encode($old_data);
		$this->lib_fungsi->catat_log_aktifitas('DELETE', $data_log, null);

		echo json_encode($retval);
	}
	################################ END INVESTASI AREA #############################################

	################################ OPERASIONAL AREA #############################################
	private function rule_validasi_operasional()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		if ($this->input->post('item_op') == '') {
			$data['inputerror'][] = 'item_op';
			$data['error_string'][] = 'Wajib Memilih Operasional';
			$data['status'] = FALSE;
			$data['is_select2'][] = TRUE;
		}

		if ($this->input->post('tgl_op') == '') {
			$data['inputerror'][] = 'tgl_op';
			$data['error_string'][] = 'Wajib Mengisi Tanggal';
			$data['status'] = FALSE;
			$data['is_select2'][] = TRUE;
		}

		if ($this->input->post('harga_op') == '') {
			$data['inputerror'][] = 'harga_op';
			$data['error_string'][] = 'Wajib Mengisi Nilai';
			$data['status'] = FALSE;
			$data['is_select2'][] = FALSE;
		}
			
        return $data;
	}
	
	public function simpan_form_operasional()
	{
		$data_log_arr = [];
		$obj_date = new DateTime();
		$timestamp = $obj_date->format('Y-m-d H:i:s');
		$datenow = $obj_date->format('Y-m-d');
		$arr_valid = $this->rule_validasi_operasional();
		
		if ($arr_valid['status'] == FALSE) {
			echo json_encode($arr_valid);
			return;
		}

		$this->db->trans_begin();
		$id_header = gen_uuid();
		$slug_trans = $this->input->post('slug_trans');
		$item_op = $this->input->post('item_op');
		$tanggal = $obj_date->createFromFormat('d/m/Y', $this->input->post('tgl_op'))->format('Y-m-d');
		$tahun = (int)$obj_date->createFromFormat('d/m/Y', $this->input->post('tgl_op'))->format('Y');
		$bulan = (int)$obj_date->createFromFormat('d/m/Y', $this->input->post('tgl_op'))->format('m');
		$total_op = $this->input->post('harga_op_raw');
		
		$cek_jenis = $this->m_global->single_row('id', ['slug' => $slug_trans], 'm_jenis_trans');
		###insert
		$data = [
			'id' => $id_header,
			'id_jenis_trans' => $cek_jenis->id,
			'bulan_trans' => $bulan,
			'tahun_trans' => $tahun,
			'tgl_trans' => $tanggal,
			'harga_total' => $total_op,
			'id_user' => $this->session->userdata('id_user'),
			'created_at' => $timestamp
		];
					
		$insert = $this->m_global->store($data, 't_transaksi');

		if($insert){
			$data_det = [
				'id' => gen_uuid(),
				'id_transaksi' => $id_header,
				'id_item_trans' => $item_op,
				'harga_satuan' => $total_op,
				'qty' => 1,
				'created_at' => $timestamp
			];
						
			$insert_det = $this->m_global->store($data_det, 't_transaksi_det');
			$data_log_arr[] = $data_det;
		}

		$data_log = json_encode($data_log_arr);
		$this->lib_fungsi->catat_log_aktifitas('CREATE', null, $data_log);
		
		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$retval['status'] = false;
			$retval['pesan'] = 'Gagal Menambah Data';
		}else{
			$this->db->trans_commit();
			$retval['status'] = true;
			$retval['pesan'] = 'Sukses Menambah Data';
		}

		echo json_encode($retval);
	}

	public function load_form_tabel_operasional()
	{
		$obj_date = new DateTime();
		$timestamp = $obj_date->format('Y-m-d H:i:s');
		$data = $this->input->post('data');
		
		$html = '';
		
		if($data){
			foreach ($data as $key => $value) {
				$html .= '<tr><td>'.($key+1).'</td><td>'.$obj_date->createFromFormat('Y-m-d', $value['tgl_trans'])->format('d-m-Y').'</td><td>'.$value['nama_item'].'</td><td align="right">'.bulan_indo($value['bulan_trans']).'</td><td>'.$value['tahun_trans'].'</td><td align="right">'.number_format($value['harga_total'],0,',','.').'</td><td><button type="button" class="btn btn-sm btn-danger" onclick="hapus_operasional(\''.$value['id'].'\')"><i class="la la-trash"></i></button></td></tr>';
			}
		}else{
			$slug = $this->clean_txt_div($this->input->post('activeModal'));
			$q_jenis = $this->m_global->single_row('*', ['slug' => $slug], 'm_jenis_trans');

			$select = "t_transaksi.*, m_item_trans.nama as nama_item, t_transaksi_det.qty, t_transaksi_det.harga_satuan";
			$where = ['t_transaksi.deleted_at' => null, 't_transaksi.id_jenis_trans' => $q_jenis->id];
			$table = 't_transaksi';
			$join = [ 
				['table' => 't_transaksi_det', 'on' => 't_transaksi.id = t_transaksi_det.id_transaksi'],
				['table' => 'm_item_trans', 'on' => 't_transaksi_det.id_item_trans = m_item_trans.id']
			];
		
			$datanya = $this->m_global->multi_row($select,$where,$table, $join);

			if($datanya){
				foreach ($datanya as $key => $value) {
					$html .= '<tr><td>'.($key+1).'</td><td>'.$obj_date->createFromFormat('Y-m-d', $value->tgl_trans)->format('d-m-Y').'</td><td>'.$value->nama_item.'</td><td align="right">'.bulan_indo($value->bulan_trans).'</td><td>'.$value->tahun_trans.'</td><td align="right">'.number_format($value->harga_total,0,',','.').'</td><td><button type="button" class="btn btn-sm btn-danger" onclick="hapus_operasional(\''.$value->id.'\')"><i class="la la-trash"></i></button></td></tr>';
				}
			}
		}

		echo json_encode([
			'html' => $html
		]);
	}

	public function delete_data_operasional()
	{
		$data_log_arr = [];
		$id = $this->input->post('id');
		$this->db->trans_begin();
		
		$old_data = $this->t_transaksi_det->get_by_condition(['id_transaksi' => $id, 'deleted_at' => null]);
		$del_1 = $this->t_transaksi_det->softdelete_by_trans($id);
		$del_2 = $this->t_transaksi->softdelete_by_id($id);

		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$retval['status'] = false;
			$retval['pesan'] = 'Gagal Menghapus Data';
		}else{
			$this->db->trans_commit();
			$retval['status'] = true;
			$retval['pesan'] = 'Sukses Menghapus Data';
		}

		$data_log = json_encode($old_data);
		$this->lib_fungsi->catat_log_aktifitas('DELETE', $data_log, null);

		echo json_encode($retval);
	}
	################################ END OPERASIONAL AREA #############################################

	################################ PENGELUARAN LAIN AREA #############################################
	private function rule_validasi_pengeluaran_lain()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		if ($this->input->post('item_out') == '') {
			$data['inputerror'][] = 'item_out';
			$data['error_string'][] = 'Wajib Memilih Pengeluaran';
			$data['status'] = FALSE;
			$data['is_select2'][] = TRUE;
		}

		if ($this->input->post('tgl_out') == '') {
			$data['inputerror'][] = 'tgl_out';
			$data['error_string'][] = 'Wajib Mengisi Tanggal';
			$data['status'] = FALSE;
			$data['is_select2'][] = TRUE;
		}

		if ($this->input->post('qty_out') == '') {
			$data['inputerror'][] = 'qty_out';
			$data['error_string'][] = 'Wajib Mengisi Qty';
			$data['status'] = FALSE;
			$data['is_select2'][] = TRUE;
		}

		if ($this->input->post('harga_out') == '') {
			$data['inputerror'][] = 'harga_out';
			$data['error_string'][] = 'Wajib Mengisi Nilai';
			$data['status'] = FALSE;
			$data['is_select2'][] = FALSE;
		}
			
        return $data;
	}
	
	public function simpan_form_out_lain()
	{
		$data_log_arr = [];
		$obj_date = new DateTime();
		$timestamp = $obj_date->format('Y-m-d H:i:s');
		$datenow = $obj_date->format('Y-m-d');
		$arr_valid = $this->rule_validasi_pengeluaran_lain();
		
		if ($arr_valid['status'] == FALSE) {
			echo json_encode($arr_valid);
			return;
		}

		$this->db->trans_begin();
		$id_header = gen_uuid();
		$slug_trans = $this->input->post('slug_trans');
		$item_out = $this->input->post('item_out');
		$tanggal = $obj_date->createFromFormat('d/m/Y', $this->input->post('tgl_out'))->format('Y-m-d');
		$tahun = (int)$obj_date->createFromFormat('d/m/Y', $this->input->post('tgl_out'))->format('Y');
		$bulan = (int)$obj_date->createFromFormat('d/m/Y', $this->input->post('tgl_out'))->format('m');
		$qty = $this->input->post('qty_out');
		$harga_out = $this->input->post('harga_out_raw');
		$total_out = $this->input->post('hargatot_out_raw');
		
		$cek_jenis = $this->m_global->single_row('id', ['slug' => $slug_trans], 'm_jenis_trans');
		###insert
		$data = [
			'id' => $id_header,
			'id_jenis_trans' => $cek_jenis->id,
			'bulan_trans' => $bulan,
			'tahun_trans' => $tahun,
			'tgl_trans' => $tanggal,
			'harga_total' => $total_out,
			'id_user' => $this->session->userdata('id_user'),
			'created_at' => $timestamp
		];
					
		$insert = $this->m_global->store($data, 't_transaksi');

		if($insert){
			$data_det = [
				'id' => gen_uuid(),
				'id_transaksi' => $id_header,
				'id_item_trans' => $item_out,
				'harga_satuan' => $harga_out,
				'qty' => $qty,
				'created_at' => $timestamp
			];
						
			$insert_det = $this->m_global->store($data_det, 't_transaksi_det');
			$data_log_arr[] = $data_det;
		}

		$data_log = json_encode($data_log_arr);
		$this->lib_fungsi->catat_log_aktifitas('CREATE', null, $data_log);
		
		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$retval['status'] = false;
			$retval['pesan'] = 'Gagal Menambah Data';
		}else{
			$this->db->trans_commit();
			$retval['status'] = true;
			$retval['pesan'] = 'Sukses Menambah Data';
		}

		echo json_encode($retval);
	}

	public function load_form_tabel_pengeluaran_lain()
	{
		$obj_date = new DateTime();
		$timestamp = $obj_date->format('Y-m-d H:i:s');
		$data = $this->input->post('data');
		
		$html = '';
		
		if($data){
			foreach ($data as $key => $value) {
				$html .= '<tr><td>'.($key+1).'</td><td>'.$obj_date->createFromFormat('Y-m-d', $value['tgl_trans'])->format('d-m-Y').'</td><td>'.$value['nama_item'].'</td><td align="right">'.bulan_indo($value['bulan_trans']).'</td><td>'.$value['tahun_trans'].'</td><td align="right">'.number_format($value['harga_satuan'],0,',','.').'</td><td align="right">'.number_format($value['qty'],0,',','.').'</td><td align="right">'.number_format($value['harga_total'],0,',','.').'</td><td><button type="button" class="btn btn-sm btn-danger" onclick="hapus_pengeluaran_lain(\''.$value['id'].'\')"><i class="la la-trash"></i></button></td></tr>';
			}
		}else{
			$slug = $this->clean_txt_div($this->input->post('activeModal'));
			$q_jenis = $this->m_global->single_row('*', ['slug' => $slug], 'm_jenis_trans');

			$select = "t_transaksi.*, m_item_trans.nama as nama_item, t_transaksi_det.qty, t_transaksi_det.harga_satuan";
			$where = ['t_transaksi.deleted_at' => null, 't_transaksi.id_jenis_trans' => $q_jenis->id];
			$table = 't_transaksi';
			$join = [ 
				['table' => 't_transaksi_det', 'on' => 't_transaksi.id = t_transaksi_det.id_transaksi'],
				['table' => 'm_item_trans', 'on' => 't_transaksi_det.id_item_trans = m_item_trans.id']
			];

			$order_by = "t_transaksi.tgl_trans desc";
		
			$datanya = $this->m_global->multi_row($select,$where,$table, $join, $order_by);

			if($datanya){
				foreach ($datanya as $key => $value) {
					$html .= '<tr><td>'.($key+1).'</td><td>'.$obj_date->createFromFormat('Y-m-d', $value->tgl_trans)->format('d-m-Y').'</td><td>'.$value->nama_item.'</td><td align="right">'.bulan_indo($value->bulan_trans).'</td><td>'.$value->tahun_trans.'</td><td align="right">'.number_format($value->harga_satuan,0,',','.').'</td><td align="right">'.number_format($value->qty,0,',','.').'</td><td align="right">'.number_format($value->harga_total,0,',','.').'</td><td><button type="button" class="btn btn-sm btn-danger" onclick="hapus_pengeluaran_lain(\''.$value->id.'\')"><i class="la la-trash"></i></button></td></tr>';
				}
			}
		}

		echo json_encode([
			'html' => $html
		]);
	}

	public function delete_data_pengeluaran_lain()
	{
		$id = $this->input->post('id');
		$this->db->trans_begin();
		$old_data = $this->t_transaksi_det->get_by_condition(['id_transaksi' => $id, 'deleted_at' => null]);

		$del_1 = $this->t_transaksi_det->softdelete_by_trans($id);
		$del_2 = $this->t_transaksi->softdelete_by_id($id);

		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$retval['status'] = false;
			$retval['pesan'] = 'Gagal Menghapus Data';
		}else{
			$this->db->trans_commit();
			$retval['status'] = true;
			$retval['pesan'] = 'Sukses Menghapus Data';
		}

		$data_log = json_encode($old_data);
		$this->lib_fungsi->catat_log_aktifitas('DELETE', $data_log, null);

		echo json_encode($retval);
	}
	################################ END PENGELUARAN LAIN AREA #############################################

	################################ PENERIMAAN LAIN AREA #############################################
	private function rule_validasi_penerimaan_lain()
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		if ($this->input->post('item_in') == '') {
			$data['inputerror'][] = 'item_in';
			$data['error_string'][] = 'Wajib Memilih Penerimaan';
			$data['status'] = FALSE;
			$data['is_select2'][] = TRUE;
		}

		if ($this->input->post('tgl_in') == '') {
			$data['inputerror'][] = 'tgl_in';
			$data['error_string'][] = 'Wajib Mengisi Tanggal';
			$data['status'] = FALSE;
			$data['is_select2'][] = TRUE;
		}

		if ($this->input->post('qty_in') == '') {
			$data['inputerror'][] = 'qty_in';
			$data['error_string'][] = 'Wajib Mengisi Qty';
			$data['status'] = FALSE;
			$data['is_select2'][] = TRUE;
		}

		if ($this->input->post('harga_in') == '') {
			$data['inputerror'][] = 'harga_in';
			$data['error_string'][] = 'Wajib Mengisi Nilai';
			$data['status'] = FALSE;
			$data['is_select2'][] = FALSE;
		}
			
        return $data;
	}
	
	public function simpan_form_in_lain()
	{
		$data_log_arr = [];
		$obj_date = new DateTime();
		$timestamp = $obj_date->format('Y-m-d H:i:s');
		$datenow = $obj_date->format('Y-m-d');
		$arr_valid = $this->rule_validasi_penerimaan_lain();
		
		if ($arr_valid['status'] == FALSE) {
			echo json_encode($arr_valid);
			return;
		}

		$this->db->trans_begin();
		$id_header = gen_uuid();
		$slug_trans = $this->input->post('slug_trans');
		$item_in = $this->input->post('item_in');
		$tanggal = $obj_date->createFromFormat('d/m/Y', $this->input->post('tgl_in'))->format('Y-m-d');
		$tahun = (int)$obj_date->createFromFormat('d/m/Y', $this->input->post('tgl_in'))->format('Y');
		$bulan = (int)$obj_date->createFromFormat('d/m/Y', $this->input->post('tgl_in'))->format('m');
		$qty = $this->input->post('qty_in');
		$harga_in = $this->input->post('harga_in_raw');
		$total_in = $this->input->post('hargatot_in_raw');
		
		$cek_jenis = $this->m_global->single_row('id', ['slug' => $slug_trans], 'm_jenis_trans');
		###insert
		$data = [
			'id' => $id_header,
			'id_jenis_trans' => $cek_jenis->id,
			'bulan_trans' => $bulan,
			'tahun_trans' => $tahun,
			'tgl_trans' => $tanggal,
			'harga_total' => $total_in,
			'id_user' => $this->session->userdata('id_user'),
			'created_at' => $timestamp
		];
					
		$insert = $this->m_global->store($data, 't_transaksi');

		if($insert){
			$data_det = [
				'id' => gen_uuid(),
				'id_transaksi' => $id_header,
				'id_item_trans' => $item_in,
				'harga_satuan' => $harga_in,
				'qty' => $qty,
				'created_at' => $timestamp
			];
						
			$insert_det = $this->m_global->store($data_det, 't_transaksi_det');
			$data_log_arr[] = $data_det;
		}

		$data_log = json_encode($data_log_arr);
		$this->lib_fungsi->catat_log_aktifitas('CREATE', null, $data_log);
		
		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$retval['status'] = false;
			$retval['pesan'] = 'Gagal Menambah Data';
		}else{
			$this->db->trans_commit();
			$retval['status'] = true;
			$retval['pesan'] = 'Sukses Menambah Data';
		}

		echo json_encode($retval);
	}

	public function load_form_tabel_penerimaan_lain()
	{
		$obj_date = new DateTime();
		$timestamp = $obj_date->format('Y-m-d H:i:s');
		$data = $this->input->post('data');
		
		$html = '';
		
		if($data){
			foreach ($data as $key => $value) {
				$html .= '<tr><td>'.($key+1).'</td><td>'.$obj_date->createFromFormat('Y-m-d', $value['tgl_trans'])->format('d-m-Y').'</td><td>'.$value['nama_item'].'</td><td align="right">'.bulan_indo($value['bulan_trans']).'</td><td>'.$value['tahun_trans'].'</td><td align="right">'.number_format($value['harga_satuan'],0,',','.').'</td><td align="right">'.number_format($value['qty'],0,',','.').'</td><td align="right">'.number_format($value['harga_total'],0,',','.').'</td><td><button type="button" class="btn btn-sm btn-danger" onclick="hapus_penerimaan_lain(\''.$value['id'].'\')"><i class="la la-trash"></i></button></td></tr>';
			}
		}else{
			$slug = $this->clean_txt_div($this->input->post('activeModal'));
			$q_jenis = $this->m_global->single_row('*', ['slug' => $slug], 'm_jenis_trans');

			$select = "t_transaksi.*, m_item_trans.nama as nama_item, t_transaksi_det.qty, t_transaksi_det.harga_satuan";
			$where = ['t_transaksi.deleted_at' => null, 't_transaksi.id_jenis_trans' => $q_jenis->id];
			$table = 't_transaksi';
			$join = [ 
				['table' => 't_transaksi_det', 'on' => 't_transaksi.id = t_transaksi_det.id_transaksi'],
				['table' => 'm_item_trans', 'on' => 't_transaksi_det.id_item_trans = m_item_trans.id']
			];
			$order_by = "t_transaksi.tgl_trans desc";
		
			$datanya = $this->m_global->multi_row($select,$where,$table, $join, $order_by);

			if($datanya){
				foreach ($datanya as $key => $value) {
					$html .= '<tr><td>'.($key+1).'</td><td>'.$obj_date->createFromFormat('Y-m-d', $value->tgl_trans)->format('d-m-Y').'</td><td>'.$value->nama_item.'</td><td align="right">'.bulan_indo($value->bulan_trans).'</td><td>'.$value->tahun_trans.'</td><td align="right">'.number_format($value->harga_satuan,0,',','.').'</td><td align="right">'.number_format($value->qty,0,',','.').'</td><td align="right">'.number_format($value->harga_total,0,',','.').'</td><td><button type="button" class="btn btn-sm btn-danger" onclick="hapus_penerimaan_lain(\''.$value->id.'\')"><i class="la la-trash"></i></button></td></tr>';
				}
			}
		}

		echo json_encode([
			'html' => $html
		]);
	}

	public function delete_data_penerimaan_lain()
	{
		$id = $this->input->post('id');
		$this->db->trans_begin();
		$old_data = $this->t_transaksi_det->get_by_condition(['id_transaksi' => $id, 'deleted_at' => null]);
		$del_1 = $this->t_transaksi_det->softdelete_by_trans($id);
		$del_2 = $this->t_transaksi->softdelete_by_id($id);

		$data_log = json_encode($old_data);
		$this->lib_fungsi->catat_log_aktifitas('DELETE', $data_log, null);

		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$retval['status'] = false;
			$retval['pesan'] = 'Gagal Menghapus Data';
		}else{
			$this->db->trans_commit();
			$retval['status'] = true;
			$retval['pesan'] = 'Sukses Menghapus Data';
		}

		echo json_encode($retval);
	}
	################################ END PENERIMAAN LAIN AREA #############################################

	public function get_detail_member()
	{
		$counter_mobil = 0;
		$counter_motor = 0;

		$kode_member = trim($this->input->get('kode_member'));
		$data = $this->m_global->single_row('*', ['deleted_at' => null, 'kode_member' => $kode_member], 'm_member');
		if($data) {
			$counter = $this->lib_fungsi->cek_counter($data->id);
			if($counter != null) {
				foreach ($counter as $key => $value) {
					if($value->id_jenis_counter == '1') {
						$counter_mobil = $value->total_count;
					}else if($value->id_jenis_counter == '2') {
						$counter_motor = $value->total_count;
					}
				}
			}
		}
		
		if($data) {
			$retval = [
				'data' => $data,
				'status' => true,
				'counter_mobil' => $counter_mobil,
				'counter_motor' => $counter_motor,
			];
		}else{
			$retval = [
				'data' => null,
				'status' => false,
				'counter_mobil' => $counter_mobil,
				'counter_motor' => $counter_motor,
			];
		}

		echo json_encode($retval);
	}

	public function tampil_barcode()
	{
		//var_dump($this->barcode_lib->generate());exit;
		echo $this->barcode_lib->generate_html('M210328001');
		echo '<br>';
		echo $this->barcode_lib->generate_html('M210328002');
		echo '<br>';
	}

	public function simpan_barcode($value = '123456')
	{
		$this->barcode_lib->save_jpg($value);
	}
}
