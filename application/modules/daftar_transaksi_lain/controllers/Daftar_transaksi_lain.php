<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Daftar_transaksi_lain extends CI_Controller {
	
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

		$this->load->model('t_transaksi');
		$this->load->model('t_transaksi_det');
		$this->load->model('m_user');
		$this->load->model('m_global');
	}

	public function cek_kunci_transaksi($date)
	{
		$obj_date = new DateTime();
		$bulan_kunci = (int)$obj_date->createFromFormat('Y-m-d', $date)->format('m');
		$tahun_kunci = (int)$obj_date->createFromFormat('Y-m-d', $date)->format('Y');
		$cek = $this->m_global->single_row('*', ['deleted_at' => null, 'bulan' => $bulan_kunci, 'tahun' => $tahun_kunci], 't_log_kunci');

		if($cek) {
			$sts = true;
		}else{
			$sts = false;
		}

		return [
			'status' => $sts,
			'bulan_kunci' => $bulan_kunci,
			'tahun_kunci' => $tahun_kunci
		];
	}

	public function index()
	{
		$id_user = $this->session->userdata('id_user'); 
		$data_user = $this->m_user->get_detail_user($id_user);
				
		/**
		 * data passing ke halaman view content
		 */
		$data = array(
			'title' => 'Pengelolaaan Daftar Transaksi Lain-Lain',
			'data_user' => $data_user,
			'jenis_trans' => $this->m_global->multi_row('*', ['id <>' => '1', 'deleted_at' => null], 'm_jenis_trans')
		);


		/**
		 * content data untuk template
		 * param (css : link css pada direktori assets/css_module)
		 * param (modal : modal komponen pada modules/nama_modul/views/nama_modal)
		 * param (js : link js pada direktori assets/js_module)
		 */
		$content = [
			'css' 	=> null,
			'modal' => ['modal_daftar_transaksi', 'modal_pembelian','modal_penggajian', 'modal_investasi', 'modal_operasional', 'modal_out_lain', 'modal_in_lain'],
			'js'	=> 'daftar_transaksi_lain.js',
			'view'	=> 'view_daftar_transaksi'
		];

		$this->template_view->load_view($content, $data);
	}

	public function list_transaksi()
	{
		$obj_date = new DateTime();
		$timestamp = $obj_date->format('Y-m-d H:i:s');
		
		$tgl_awal = $obj_date->createFromFormat('d/m/Y', $this->input->post('tglAwal'))->format('Y-m-d').' 00:00:00';
		$tgl_akhir = $obj_date->createFromFormat('d/m/Y', $this->input->post('tglAkhir'))->format('Y-m-d').' 23:59:59';
		$jenis = $this->input->post('jenis');
		
		$list = $this->t_transaksi->get_datatable_penjualan($tgl_awal, $tgl_akhir, $jenis);
		
		$data = array();
		$no =$_POST['start'];
		foreach ($list as $item) {
			// $no++;
			$row = array();
			// $row[] = $no;
			$row[] = $obj_date->createFromFormat('Y-m-d H:i:s', $item->created_at)->format('d-m-Y H:i');
			
			$jenis_trans_txt = '<span style="color:blue;">'.$item->nama_jenis.'</span>';
			$row[] = $jenis_trans_txt;
			
			$row[] = $item->nama;
			$row[] = number_format($item->harga_total, 0 ,',','.');
			$row[] = bulan_indo((int)$item->bulan_trans);
			$row[] = $item->tahun_trans;

			// $status_kuncian = ($item->status_kunci == 'Terkunci') ? '<span style="color:green;">Terkunci</span>' : '<span style="color:red;">Terbuka</span>';
			// $row[] = $status_kuncian;

			$str_aksi = '
				<div class="btn-group">
					<button type="button" class="btn btn-sm btn_1 dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Opsi</button>
					<div class="dropdown-menu">
						<button class="dropdown-item" onclick="detailTransLain(\''.$item->id.'\')">
							<i class="la la-file"></i> Detail Transaksi
						</button>
						<button class="dropdown-item" onclick="editTransLain(\''.$item->id.'\')">
							<i class="la la-pencil"></i> Edit Transaksi
						</button>
						<button class="dropdown-item" onclick="deleteTransLain(\''.$item->id.'\')">
							<i class="la la-trash"></i> Hapus Transaksi
						</button>';

			$str_aksi .= '</div></div>';


			$row[] = $str_aksi;

			$data[] = $row;

		}//end loop

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->t_transaksi->count_all_penjualan($tgl_awal, $tgl_akhir, $jenis),
			"recordsFiltered" => $this->t_transaksi->count_filtered_penjualan($tgl_awal, $tgl_akhir, $jenis),
			"data" => $data
		];
		
		echo json_encode($output);
	}

	public function get_detail_transaksi() {
		$id = $this->input->get('id');
		$data = $this->t_transaksi->get_detail_transaksi($id);
		$is_pembelian = ($data[0]->id_jenis_trans == self::ID_JENIS_PEMBELIAN) ? true : false;
		$html = '';
		$html2 = '';
		$html3 = '';
		
		if($data) {
			$status = true;
            $html .= '
				<div class="kt-section">
					<div class="kt-section__content">
						<table class="table table-bordered table-hover">
						<thead>
							<tr>
								<th>No.</th>
								<th>Nama Item</th>
								<th>Harga</th>
								<th>Qty</th>
								<th>Harga Total</th>
							</tr>
						</thead>
						<tbody>
			';

			$total_harga = 0;
			foreach ($data as $key => $value) {
				$total_harga += $value->harga_satuan * $value->qty;
				$html .= '
                    <tr>
                      <th scope="row">'.($key+1).'</th>
                      <td>'.$value->nama_item.'</td>
					  <td>Rp '.number_format($value->harga_satuan, 0 ,',','.').'</td>
                      <td>'.number_format($value->qty, 0 ,',','.').'</td>
					  <td>'.number_format($total_harga, 0 ,',','.').'</td>
                    </tr>';  
			}

			$html .= '
					<tr>
						<th scope="row" colspan="3">Grand Total</th>
						<td colspan="2" align="center">Rp. '.number_format(($total_harga), 0 ,',','.').'</td>
					</tr>
					';  
			
			$html .= '</tbody></table></div></div>';

			$html2 .= '
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button id="btnCetak" type="button" class="btn btn-primary" onclick="printStruk(\''.$data[0]->id.'\')">Cetak</button>
			';

			if($is_pembelian) {
				$html3 .= '<label class="col-3 col-form-label">Supplier :</label>
                <div class="col-9">
                  <span class="form-control-plaintext kt-font-bolder" id="spn-supplier">'.$data[0]->nama_supplier.'</span>
                </div>';
			}
		}else{
			$status = false;
		}
		
		echo json_encode([
			'status' => $status,
			'data' => $data,
			'html' => $html,
			'html2' => $html2,
			'html3' => $html3
		]);
	}

	public function edit_data()
	{
		$obj_date = new DateTime();
		$timestamp = $obj_date->format('Y-m-d H:i:s');
		$id = $this->input->post('id');
		
		$select = "t_transaksi.*, m_item_trans.nama as nama_item, t_transaksi_det.qty, t_transaksi_det.harga_satuan, t_transaksi_det.id_item_trans, m_supplier.nama_supplier";
		$where = ['t_transaksi.deleted_at' => null, 't_transaksi.id' => $id];
		$table = 't_transaksi';
		$join = [ 
			['table' => 't_transaksi_det', 'on' => 't_transaksi.id = t_transaksi_det.id_transaksi'],
			['table' => 'm_item_trans', 'on' => 't_transaksi_det.id_item_trans = m_item_trans.id'],
			['table' => 'm_supplier', 'on' => 't_transaksi.id_supplier = m_supplier.id']
		];
	
		$datanya = $this->m_global->single_row($select,$where,$table, $join);
		if($datanya) {
			#### cek kuncian laporan
			$cek_kunci = $this->cek_kunci_transaksi($datanya->tgl_trans);
			if($cek_kunci['status'] == true) {
				$bulan_kunci = $cek_kunci['bulan_kunci'];
				$tahun_kunci = $cek_kunci['tahun_kunci'];
				$bln_txt = bulan_indo($bulan_kunci);
				echo json_encode([ 
					'data' => null,
					'jenis_trans'	=> null,
					'status' => false,
					'pesan' => 'Maaf Laporan Bulan '.$bln_txt.' '.$tahun_kunci.' Telah Terkunci'
				]);
				return;
			}

			$status = true;
			$pesan = null;
			$jenis_trans =  $this->cek_jenis_trans($datanya->id_jenis_trans);
		}else{
			$datanya = null;
			$jenis_trans = null;
			$status = false;
			$pesan = 'Maaf Data Tidak Ditemukan';
		}
		
		$data = [ 
			'data' => $datanya,
			'jenis_trans'	=> $jenis_trans,
			'status' => $status,
			'pesan' => $pesan
		];
		
		echo json_encode($data);
	}

	private function cek_jenis_trans($id_jenis_trans)
	{
		switch ((int)$id_jenis_trans) {
			case self::ID_JENIS_PEMBELIAN:
				return 'pembelian';
				break;

			case self::ID_JENIS_PENGGAJIAN:
				return 'penggajian';
				break;

			case self::ID_JENIS_INVESTASI:
				return 'investasi';
				break;

			case self::ID_JENIS_OPERSAIONAL:
				return 'operasional';
				break;

			case self::ID_JENIS_PENGELUARAN_LAIN:
				return 'out_lain';
				break;

			case self::ID_JENIS_PENERIMAAN_LAIN:
				return 'in_lain';
				break;
			
			default:
				return false;
				break;
		}
	}

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

		
		$id_header = $this->input->post('id_trans_beli');
		$id_jenis = $this->input->post('id_jenis_beli');
		$item_beli = $this->input->post('item_beli');
		$sup_beli = $this->input->post('sup_beli');
		$qty_beli = $this->input->post('qty_beli');
		$tanggal = $obj_date->createFromFormat('d/m/Y', $this->input->post('tgl_beli'))->format('Y-m-d');
		$tahun = (int)$obj_date->createFromFormat('d/m/Y', $this->input->post('tgl_beli'))->format('Y');
		$bulan = (int)$obj_date->createFromFormat('d/m/Y', $this->input->post('tgl_beli'))->format('m');
		$harga_beli = $this->input->post('harga_beli_raw');
		$hargatot_beli = $this->input->post('hargatot_beli_raw');

		$oldData = $this->m_global->single_row_array('*', ['id' => $id_header], 't_transaksi');
		$oldDataDet = $this->m_global->single_row_array('*', ['id_transaksi' => $id_header], 't_transaksi_det');

		if($oldData) {
			$this->db->trans_begin();

			###update
			$data = [
				'id_jenis_trans' => $id_jenis,
				'id_supplier' => $sup_beli,
				'bulan_trans' => $bulan,
				'tahun_trans' => $tahun,
				'tgl_trans' => $tanggal,
				'harga_total' => $hargatot_beli,
				'id_user' => $this->session->userdata('id_user'),
				'updated_at' => $timestamp
			];

			$update = $this->m_global->update('t_transaksi', $data, ['id' => $id_header]);
			$data_log_arr_old[] = $oldData;
			$data_log_arr_new[] = $data;

			if($update){
				$data_det = [
					'id_item_trans' => $item_beli,
					'harga_satuan' => $harga_beli,
					'qty' => $qty_beli,
					'updated_at' => $timestamp
				];
							
				$update_det = $this->m_global->update('t_transaksi_det', $data_det, ['id_transaksi' => $id_header]);
				$data_log_arr_old[] = $oldDataDet;
				$data_log_arr_new[] = $data_det;
			}

			$data_log_old = json_encode($data_log_arr_old);
			$data_log_new = json_encode($data_log_arr_new);
			$this->lib_fungsi->catat_log_aktifitas('UPDATE', $data_log_old, $data_log_new);

			if ($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$retval['status'] = false;
				$retval['pesan'] = 'Gagal Update Data';
			}else{
				$this->db->trans_commit();
				$retval['status'] = true;
				$retval['pesan'] = 'Sukses Update Data';
			}
	
		}else{
			$retval['status'] = false;
			$retval['pesan'] = 'Gagal Update Data';
		}
		
		echo json_encode($retval);
	}

	#####################################################

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

		
		$id_header = $this->input->post('id_trans_gaji');
		$id_jenis = $this->input->post('id_jenis_gaji');
		$item_gaji = $this->input->post('item_gaji');
		$tahun = $this->input->post('tahun_gaji');
		$bulan = $this->input->post('bulan_gaji');
		$total_gaji = $this->input->post('harga_gaji_raw');
	
		$oldData = $this->m_global->single_row_array('*', ['id' => $id_header], 't_transaksi');
		$oldDataDet = $this->m_global->single_row_array('*', ['id_transaksi' => $id_header], 't_transaksi_det');

		if($oldData) {
			$this->db->trans_begin();

			###update
			$data = [
				'id_jenis_trans' => $id_jenis,
				'bulan_trans' => $bulan,
				'tahun_trans' => $tahun,
				'tgl_trans' => $datenow,
				'harga_total' => $total_gaji,
				'id_user' => $this->session->userdata('id_user'),
				'updated_at' => $timestamp
			];

			$update = $this->m_global->update('t_transaksi', $data, ['id' => $id_header]);
			$data_log_arr_old[] = $oldData;
			$data_log_arr_new[] = $data;

			if($update){
				$data_det = [
					'id_item_trans' => $item_gaji,
					'harga_satuan' => $total_gaji,
					'qty' => 1,
					'updated_at' => $timestamp
				];
							
				$update_det = $this->m_global->update('t_transaksi_det', $data_det, ['id_transaksi' => $id_header]);
				$data_log_arr_old[] = $oldDataDet;
				$data_log_arr_new[] = $data_det;
			}

			$data_log_old = json_encode($data_log_arr_old);
			$data_log_new = json_encode($data_log_arr_new);
			$this->lib_fungsi->catat_log_aktifitas('UPDATE', $data_log_old, $data_log_new);

			if ($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$retval['status'] = false;
				$retval['pesan'] = 'Gagal Update Data';
			}else{
				$this->db->trans_commit();
				$retval['status'] = true;
				$retval['pesan'] = 'Sukses Update Data';
			}
	
		}else{
			$retval['status'] = false;
			$retval['pesan'] = 'Gagal Update Data';
		}
		
		echo json_encode($retval);
	}

	#####################################################

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

		
		$id_header = $this->input->post('id_trans_inves');
		$id_jenis = $this->input->post('id_jenis_inves');
		$item_inves = $this->input->post('item_inves');
		$tanggal = $obj_date->createFromFormat('d/m/Y', $this->input->post('tgl_inves'))->format('Y-m-d');
		$tahun = (int)$obj_date->createFromFormat('d/m/Y', $this->input->post('tgl_inves'))->format('Y');
		$bulan = (int)$obj_date->createFromFormat('d/m/Y', $this->input->post('tgl_inves'))->format('m');

		$total_inves = $this->input->post('harga_inves_raw');

		$oldData = $this->m_global->single_row_array('*', ['id' => $id_header], 't_transaksi');
		$oldDataDet = $this->m_global->single_row_array('*', ['id_transaksi' => $id_header], 't_transaksi_det');

		if($oldData) {
			$this->db->trans_begin();

			###update
			$data = [
				'id_jenis_trans' => $id_jenis,
				'bulan_trans' => $bulan,
				'tahun_trans' => $tahun,
				'tgl_trans' => $tanggal,
				'harga_total' => $total_inves,
				'id_user' => $this->session->userdata('id_user'),
				'updated_at' => $timestamp
			];

			$update = $this->m_global->update('t_transaksi', $data, ['id' => $id_header]);
			$data_log_arr_old[] = $oldData;
			$data_log_arr_new[] = $data;

			if($update){
				$data_det = [
					'id_item_trans' => $item_inves,
					'harga_satuan' => $total_inves,
					'qty' => 1,
					'updated_at' => $timestamp
				];
							
				$update_det = $this->m_global->update('t_transaksi_det', $data_det, ['id_transaksi' => $id_header]);
				$data_log_arr_old[] = $oldDataDet;
				$data_log_arr_new[] = $data_det;
			}

			$data_log_old = json_encode($data_log_arr_old);
			$data_log_new = json_encode($data_log_arr_new);
			$this->lib_fungsi->catat_log_aktifitas('UPDATE', $data_log_old, $data_log_new);

			if ($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$retval['status'] = false;
				$retval['pesan'] = 'Gagal Update Data';
			}else{
				$this->db->trans_commit();
				$retval['status'] = true;
				$retval['pesan'] = 'Sukses Update Data';
			}
	
		}else{
			$retval['status'] = false;
			$retval['pesan'] = 'Gagal Update Data';
		}
		
		echo json_encode($retval);
	}

	#####################################################

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

		
		$id_header = $this->input->post('id_trans_op');
		$id_jenis = $this->input->post('id_jenis_op');
		$item_inves = $this->input->post('item_inves');
		$tanggal = $obj_date->createFromFormat('d/m/Y', $this->input->post('tgl_op'))->format('Y-m-d');
		$tahun = (int)$obj_date->createFromFormat('d/m/Y', $this->input->post('tgl_op'))->format('Y');
		$bulan = (int)$obj_date->createFromFormat('d/m/Y', $this->input->post('tgl_op'))->format('m');

		$total_op = $this->input->post('harga_op_raw');

		$oldData = $this->m_global->single_row_array('*', ['id' => $id_header], 't_transaksi');
		$oldDataDet = $this->m_global->single_row_array('*', ['id_transaksi' => $id_header], 't_transaksi_det');

		if($oldData) {
			$this->db->trans_begin();

			###update
			$data = [
				'id_jenis_trans' => $id_jenis,
				'bulan_trans' => $bulan,
				'tahun_trans' => $tahun,
				'tgl_trans' => $tanggal,
				'harga_total' => $total_op,
				'id_user' => $this->session->userdata('id_user'),
				'updated_at' => $timestamp
			];

			$update = $this->m_global->update('t_transaksi', $data, ['id' => $id_header]);
			$data_log_arr_old[] = $oldData;
			$data_log_arr_new[] = $data;

			if($update){
				$data_det = [
					'id_item_trans' => $item_inves,
					'harga_satuan' => $total_op,
					'qty' => 1,
					'updated_at' => $timestamp
				];
							
				$update_det = $this->m_global->update('t_transaksi_det', $data_det, ['id_transaksi' => $id_header]);
				$data_log_arr_old[] = $oldDataDet;
				$data_log_arr_new[] = $data_det;
			}

			$data_log_old = json_encode($data_log_arr_old);
			$data_log_new = json_encode($data_log_arr_new);
			$this->lib_fungsi->catat_log_aktifitas('UPDATE', $data_log_old, $data_log_new);

			if ($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$retval['status'] = false;
				$retval['pesan'] = 'Gagal Update Data';
			}else{
				$this->db->trans_commit();
				$retval['status'] = true;
				$retval['pesan'] = 'Sukses Update Data';
			}
	
		}else{
			$retval['status'] = false;
			$retval['pesan'] = 'Gagal Update Data';
		}
		
		echo json_encode($retval);
	}

	#####################################################

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

		$id_header = $this->input->post('id_trans_out');
		$id_jenis = $this->input->post('id_jenis_out');
		$item_out = $this->input->post('item_out');
		$tanggal = $obj_date->createFromFormat('d/m/Y', $this->input->post('tgl_out'))->format('Y-m-d');
		$tahun = (int)$obj_date->createFromFormat('d/m/Y', $this->input->post('tgl_out'))->format('Y');
		$bulan = (int)$obj_date->createFromFormat('d/m/Y', $this->input->post('tgl_out'))->format('m');

		$qty = $this->input->post('qty_out');
		$harga_out = $this->input->post('harga_out_raw');
		$total_out = $this->input->post('hargatot_out_raw');

		$oldData = $this->m_global->single_row_array('*', ['id' => $id_header], 't_transaksi');
		$oldDataDet = $this->m_global->single_row_array('*', ['id_transaksi' => $id_header], 't_transaksi_det');

		if($oldData) {
			$this->db->trans_begin();

			###update
			$data = [
				'id_jenis_trans' => $id_jenis,
				'bulan_trans' => $bulan,
				'tahun_trans' => $tahun,
				'tgl_trans' => $tanggal,
				'harga_total' => $total_out,
				'id_user' => $this->session->userdata('id_user'),
				'updated_at' => $timestamp
			];

			$update = $this->m_global->update('t_transaksi', $data, ['id' => $id_header]);
			$data_log_arr_old[] = $oldData;
			$data_log_arr_new[] = $data;

			if($update){
				$data_det = [
					'id_item_trans' => $item_out,
					'harga_satuan' => $harga_out,
					'qty' => $qty,
					'updated_at' => $timestamp
				];
							
				$update_det = $this->m_global->update('t_transaksi_det', $data_det, ['id_transaksi' => $id_header]);
				$data_log_arr_old[] = $oldDataDet;
				$data_log_arr_new[] = $data_det;
			}

			$data_log_old = json_encode($data_log_arr_old);
			$data_log_new = json_encode($data_log_arr_new);
			$this->lib_fungsi->catat_log_aktifitas('UPDATE', $data_log_old, $data_log_new);

			if ($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$retval['status'] = false;
				$retval['pesan'] = 'Gagal Update Data';
			}else{
				$this->db->trans_commit();
				$retval['status'] = true;
				$retval['pesan'] = 'Sukses Update Data';
			}
	
		}else{
			$retval['status'] = false;
			$retval['pesan'] = 'Gagal Update Data';
		}
		
		echo json_encode($retval);
	}

	#####################################################

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

		$id_header = $this->input->post('id_trans_in');
		$id_jenis = $this->input->post('id_jenis_in');
		$item_in = $this->input->post('item_in');
		$tanggal = $obj_date->createFromFormat('d/m/Y', $this->input->post('tgl_in'))->format('Y-m-d');
		$tahun = (int)$obj_date->createFromFormat('d/m/Y', $this->input->post('tgl_in'))->format('Y');
		$bulan = (int)$obj_date->createFromFormat('d/m/Y', $this->input->post('tgl_in'))->format('m');

		$qty = $this->input->post('qty_in');
		$harga_in = $this->input->post('harga_in_raw');
		$total_in = $this->input->post('hargatot_in_raw');

		$oldData = $this->m_global->single_row_array('*', ['id' => $id_header], 't_transaksi');
		$oldDataDet = $this->m_global->single_row_array('*', ['id_transaksi' => $id_header], 't_transaksi_det');

		if($oldData) {
			$this->db->trans_begin();

			###update
			$data = [
				'id_jenis_trans' => $id_jenis,
				'bulan_trans' => $bulan,
				'tahun_trans' => $tahun,
				'tgl_trans' => $tanggal,
				'harga_total' => $total_in,
				'id_user' => $this->session->userdata('id_user'),
				'updated_at' => $timestamp
			];

			$update = $this->m_global->update('t_transaksi', $data, ['id' => $id_header]);
			$data_log_arr_old[] = $oldData;
			$data_log_arr_new[] = $data;

			if($update){
				$data_det = [
					'id_item_trans' => $item_in,
					'harga_satuan' => $harga_in,
					'qty' => $qty,
					'updated_at' => $timestamp
				];
							
				$update_det = $this->m_global->update('t_transaksi_det', $data_det, ['id_transaksi' => $id_header]);
				$data_log_arr_old[] = $oldDataDet;
				$data_log_arr_new[] = $data_det;
			}

			$data_log_old = json_encode($data_log_arr_old);
			$data_log_new = json_encode($data_log_arr_new);
			$this->lib_fungsi->catat_log_aktifitas('UPDATE', $data_log_old, $data_log_new);

			if ($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				$retval['status'] = false;
				$retval['pesan'] = 'Gagal Update Data';
			}else{
				$this->db->trans_commit();
				$retval['status'] = true;
				$retval['pesan'] = 'Sukses Update Data';
			}
	
		}else{
			$retval['status'] = false;
			$retval['pesan'] = 'Gagal Update Data';
		}
		
		echo json_encode($retval);
	}

	######################################################

	/**
	 * Hanya melakukan softdelete saja
	 * isi kolom updated_at dengan datetime now()
	 */

	public function delete_item_trans_lain()
	{
		$id = $this->input->post('id');
		$this->db->trans_begin();
		$old_data_header = $this->t_transaksi->get_by_condition(['id' => $id, 'deleted_at' => null]);
		$old_data = $this->t_transaksi_det->get_by_condition(['id_transaksi' => $id, 'deleted_at' => null]);
		
		#### cek kuncian laporan
		$cek_kunci = $this->cek_kunci_transaksi($old_data_header[0]->tgl_trans);
		if($cek_kunci['status'] == true) {
			$bulan_kunci = $cek_kunci['bulan_kunci'];
			$tahun_kunci = $cek_kunci['tahun_kunci'];
			$bln_txt = bulan_indo($bulan_kunci);
			echo json_encode([ 
				'data' => null,
				'jenis_trans'	=> null,
				'status' => false,
				'pesan' => 'Maaf Laporan Bulan '.$bln_txt.' '.$tahun_kunci.' Telah Terkunci'
			]);
			return;
		}

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
		
	
	


	public function import_excel()
	{
		$select = "m_pegawai.*, m_jabatan.nama as nama_jabatan";
		$where = ['m_pegawai.deleted_at' => null];
		$table = 'm_pegawai';
		$join = [ 
			[
				'table' => 'm_jabatan',
				'on'	=> 'm_pegawai.id_jabatan = m_jabatan.id'
			]
		];

		$data = $this->m_global->multi_row($select, $where, $table, $join, 'm_pegawai.kode');
		
		$spreadsheet = $this->excel->spreadsheet_obj();
		$writer = $this->excel->xlsx_obj($spreadsheet);
		$number_format_obj = $this->excel->number_format_obj();
		
		$spreadsheet
			->getActiveSheet()
			->getStyle('E2:E1000')
			->getNumberFormat()
			->setFormatCode($number_format_obj::FORMAT_NUMBER);

		$spreadsheet
			->getActiveSheet()
			->getStyle('F2:F1000')
			->getNumberFormat()
			->setFormatCode($number_format_obj::FORMAT_NUMBER);	
		
		$sheet = $spreadsheet->getActiveSheet();

		$sheet
			->setCellValue('A1', 'Kode')
			->setCellValue('B1', 'Nama')
			->setCellValue('C1', 'Alamat')
			->setCellValue('D1', 'Jabatan')
			->setCellValue('E1', 'Telp. 1')
			->setCellValue('F1', 'Telp. 2')
			->setCellValue('G1', 'Status Aktif');
		
		$startRow = 2;
		$row = $startRow;
		if($data){
			foreach ($data as $key => $val) {
				$sts = ($val->is_aktif = '1') ? 'Aktif' : 'Non Aktif';
				
				$sheet
					->setCellValue("A{$row}", $val->kode)
					->setCellValue("B{$row}", $val->nama)
					->setCellValue("C{$row}", $val->alamat)
					->setCellValue("D{$row}", $val->nama_jabatan)
					->setCellValue("E{$row}", $val->telp_1)
					->setCellValue("F{$row}", $val->telp_2)
					->setCellValue("G{$row}", $sts);
				$row++;
			}
			$endRow = $row - 1;
		}
		
		
		$filename = 'master-pegawai-'.time();
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
		
	}

	public function template_excel()
	{
		$file_url = base_url().'files/template_dokumen/template_master_pegawai.xlsx';
		header('Content-Type: application/octet-stream');
		header("Content-Transfer-Encoding: Binary"); 
		header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\""); 
		readfile($file_url); 
	}

	public function export_data_master()
	{
		$obj_date = new DateTime();
		$timestamp = $obj_date->format('Y-m-d H:i:s');

		$file_mimes = ['text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
		$retval = [];
		if(isset($_FILES['file_excel']['name']) && in_array($_FILES['file_excel']['type'], $file_mimes)) {
			$arr_file = explode('.', $_FILES['file_excel']['name']);
			$extension = end($arr_file);
			if('csv' == $extension){
				$reader = $this->excel->csv_reader_obj();
			} else {
				$reader = $this->excel->reader_obj();
			}

			$spreadsheet = $reader->load($_FILES['file_excel']['tmp_name']);
			$sheetData = $spreadsheet->getActiveSheet()->toArray();
			
			for ($i=0; $i <count($sheetData); $i++) { 
				
				if ($sheetData[$i][0] == '' || $sheetData[$i][1] == '' || $sheetData[$i][2] == '' || $sheetData[$i][3] == '') {
					
					if($i == 0) {
						$flag_kosongan = true;
						$status_ekspor = false;
						$pesan = "Data Kosong...";
					}else{
						$flag_kosongan = false;
						$status_ekspor = true;
						$pesan = "Data Sukses Di Ekspor";
					}

					break;
				}

				$data['kode'] = strtoupper(strtolower(trim($sheetData[$i][0])));
				$data['nama'] = strtoupper(strtolower(trim($sheetData[$i][1])));
				$data['alamat'] = strtoupper(strtolower(trim($sheetData[$i][2])));
				
				#jabatan
				$id_jabatan = $this->m_pegawai->get_id_jabatan_by_name(strtolower(trim($sheetData[$i][3])));
				
				if($id_jabatan){
					$data['id_jabatan'] = $id_jabatan->id;
				}else{
					if($i == 0) {
						continue;
					}else{
						$flag_kosongan = false;
						$status_ekspor = false;
						$pesan = "Terjadi Kesalahan Dalam Penulisan Nama Jabatan, Mohon Cek Kembali";
						break;
					}
				}
				#end jabatan

				if($sheetData[$i][4] != ''){
					$data['telp_1'] = trim($sheetData[$i][4]);
				}

				if($sheetData[$i][5] != ''){
					$data['telp_2'] = trim($sheetData[$i][5]);
				}

				$data['created_at'] = $timestamp;
				$data['is_aktif'] = 1;

				$retval[] = $data;
			}

			if($status_ekspor) {
				// var_dump(count($retval));exit;
				## jika array maks cuma 1, maka batalkan (soalnya hanya header saja disana) ##
				if(count($retval) <= 1) {
					echo json_encode([
						'status' => false,
						'pesan'	=> 'Ekspor dibatalkan, Data Kosong...'
					]);

					return;
				}
				
				$this->db->trans_begin();
				
				#### truncate loh !!!!!!
				$this->m_pegawai->trun_master_pegawai();
				
				foreach ($retval as $keys => $vals) {
					#### simpan
					$vals['id'] = $this->m_pegawai->get_max_id_pegawai();
					$simpan = $this->m_pegawai->save($vals);
				}

				if ($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					$status = false;
					$pesan = 'Gagal melakukan ekspor, cek ulang dalam melakukan pengisian data excel';
				}else{
					$this->db->trans_commit();
					$status = true;
					$pesan = 'Sukses ekspor data pegawai';
				}

				echo json_encode([
					'status' => $status,
					'pesan'	=> $pesan
				]);
				
			}else{
				echo json_encode([
					'status' => false,
					'pesan'	=> $pesan
				]);
			}

		}else{
			echo json_encode([
				'status' => false,
				'pesan'	=> 'Terjadi Kesalahan dalam upload file. pastikan file adalah file excel .xlsx/.xls'
			]);
		}
	}

	public function cetak_data()
	{
		$select = "m_tindakan.*";
		$where = ["m_tindakan.deleted_at is null"];
		$orderby = "m_tindakan.kode_tindakan asc";
		$data = $this->m_global->multi_row($select, $where, 'm_tindakan', null, $orderby);
		$data_klinik = $this->m_global->single_row('*', 'deleted_at is null', 'm_klinik');

		$retval = [
			'data' => $data,
			'data_klinik' => $data_klinik,
			'title' => 'Master Data Tindakan'
		];
		
		// $this->load->view('pdf', $retval);
		$html = $this->load->view('pdf', $retval, true);
	    $filename = 'master_data_tindakan_'.time();
	    $this->lib_dompdf->generate($html, $filename, true, 'A4', 'potrait');
	}

	public function get_select_tindakan()
	{
		$term = $this->input->get('term');
		$data_tindakan = $this->m_global->multi_row('*', ['deleted_at' => null, 'nama_tindakan like' => '%'.$term.'%'], 'm_tindakan', null, 'nama_tindakan');
		if($data_tindakan) {
			foreach ($data_tindakan as $key => $value) {
				$row['id'] = $value->id_tindakan;
				$row['text'] = $value->kode_tindakan.' - '.$value->nama_tindakan;
				$row['kode'] = $value->kode_tindakan;
				$row['nama'] = $value->nama_tindakan;
				$row['harga'] = number_format($value->harga,0,',','.');
				$row['harga_raw'] = $value->harga;

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
