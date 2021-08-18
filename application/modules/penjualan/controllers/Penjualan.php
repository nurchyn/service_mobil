<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Penjualan extends CI_Controller {
	const ID_JENIS_TRANS = 1;

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
		$this->load->library('barcode_lib');
		// $this->load->library('thermalprint_lib');
	}

	################### cetak struk serverside by lib escpos
	// public function cetak_struk($id_trans)
	// {
	// 	$id_user = $this->session->userdata('id_user'); 
	// 	$data_profile = $this->m_global->single_row("*",NULL,'m_profil');
	// 	$select = 'trans.*, trans_det.id as id_det, trans_det.id_item_trans, trans_det.harga_satuan, m_item_trans.nama';
	// 	$join = [ 
	// 		['table' => 't_transaksi_det as trans_det', 'on' => 'trans.id = trans_det.id_transaksi'],
	// 		['table' => 'm_item_trans', 'on' => 'trans_det.id_item_trans = m_item_trans.id and m_item_trans.id_jenis_trans = 1']
	// 	];
	// 	$data_penjualan = $this->m_global->multi_row($select, ['trans.id' =>  $id_trans,'trans.deleted_at' => null], 't_transaksi as trans', $join);
	// 	// echo $this->db->last_query();exit;
		
	// 	$data_user = $this->m_user->get_detail_user($id_user);
		
	// 	$this->thermalprint_lib->cek_cetak($data_user, $data_profile, $data_penjualan);
	// }

	##################### cetak struk client side 
	public function cetak_struk($id_trans)
	{
		$id_user = $this->session->userdata('id_user'); 
		$data_profile = $this->m_global->single_row("*",NULL,'m_profil');
		$select = 'trans.*, trans_det.id as id_det, trans_det.id_item_trans, trans_det.harga_satuan, m_item_trans.nama';
		$join = [ 
			['table' => 't_transaksi_det as trans_det', 'on' => 'trans.id = trans_det.id_transaksi'],
			['table' => 'm_item_trans', 'on' => 'trans_det.id_item_trans = m_item_trans.id and m_item_trans.id_jenis_trans = 1']
		];
		$data_penjualan = $this->m_global->multi_row($select, ['trans.id' =>  $id_trans,'trans.deleted_at' => null], 't_transaksi as trans', $join);		
		$data_user = $this->m_user->get_detail_user($id_user);		

		// echo "<pre>";
		// print_r ($data_user);
		// echo "</pre>";

		// echo "<pre>";
		// print_r ($data_profile);
		// echo "</pre>";

		// echo "<pre>";
		// print_r ($data_penjualan);
		// echo "</pre>";
		// exit;

		$html = $this->get_template_cetak($data_user, $data_profile, $data_penjualan);
		echo json_encode([
			'status' => true,
			'html' => $html
		]);
	}

	public function get_template_cetak_header()
	{
		return "
			<html lang='en'>
				<head>
					<meta charset='UTF-8'>
					<meta name='viewport' content='width=device-width, initial-scale=1.0'>
					<meta http-equiv='X-UA-Compatible' content='ie=edge'>
					<link rel='stylesheet' href='style.css'>
					<title>Receipt example</title>
					<style>

						* {
							font-size: 12px;
							font-family: 'Arial';
						}
						
						table {
							margin-left:7px;
						}
						
						td,
						th,
						tr,
						table.tabel-penjualan {
							border-top: 1px dashed black;
							border-collapse: collapse;
						}

						table.tabel-petugas 
						td,
						th,
						tr {
							border-collapse: collapse;
							font-size: 9px;
							border: none;
						}
						
						td.description,
						th.description {
							width: 50%;
							max-width: 50%;
							font-size:10px;
							text-align:left;
						}
						
						td.quantity,
						th.quantity {
							width: 40px;
							max-width: 40px;
							word-break: break-all;
						}

						td.quality,
						th.quality {
							width: 10%;
							max-width: 10%;
							word-break: break-all;
							text-align: left!important;
							font-size:10px;
						}
						
						td.price,
						th.price {
							width: 40%;
							max-width: 40%;
							word-break: break-all;
							font-size:10px;
						}
						
						.centered {
							text-align: center;
							align-content: center;
						}

						.centered2 {
							text-align: center;
							align-content: center;
							margin-left:6px!important;
						}
						
						.ticket {
							margin-left:5px;
							width: 167px;
							max-width: 167px;
						}
						
						img {
							max-width: inherit;
							width: inherit;
						}
						
						@media print {
							.hidden-print,
							.hidden-print * {
								display: none !important;
							}
						}
					</style>
				</head>
		";
	}

	public function get_template_cetak($data_user, $data_profile, $data_penjualan)
	{
		$obj_date = new DateTime();
		$timestamp = $obj_date->format('Y-m-d H:i:s');
		$jam_trans = $obj_date->createFromFormat('Y-m-d H:i:s', $data_penjualan[0]->created_at)->format('d-m-Y H:i');
		$retval = $this->get_template_cetak_header();
		$retval .= "
				<body>
					<div class='ticket'>
						<p class='centered2'><span style='font-size: 12px;'>$data_profile->nama</span>
							<br><span style='font-size: 9px;'>$data_profile->alamat</span>
							<br><span style='font-size: 9px;'>$data_profile->kota</span></p>
						<hr class='centered2'>
						<table class='tabel-petugas'>
							<tbody>
								<tr>
									<td>Invoice</td>
									<td>:</td>
									<td>".$data_penjualan[0]->kode."</td>
								</tr>
								<tr>
									<td>Kasir</td>
									<td>:</td>
									<td>".$data_user[0]->nama."</td>
								</tr>
								<tr>
									<td>Waktu</td>
									<td>:</td>
									<td>".$jam_trans."</td>
								</tr>
							</tbody>
						</table>
						<table class='tabel-penjualan' width='100%'>
							<thead>
								<tr>
									<th class='quality'>No</th>
									<th class='description'>Deskripsi</th>
									<th class='price'>Harga</th>
								</tr>
							</thead>
							<tbody>";
								foreach ($data_penjualan as $key => $value) {
									$retval .= "<tr>
										<td class='quality'>".($key+1)."</td>
										<td class='description'>$value->nama</td>
										<td class='price' style='text-align:right;'>".number_format($value->harga_satuan,0,',','.')."</td>
									</tr>";
								}

								$retval .= "<tr>
										<td class='description' colspan='2'><strong>Total</strong></td>
										<td class='price' style='text-align:right;font-weight:bold;'>".number_format($data_penjualan[0]->harga_total,0,',','.')."</td>
									</tr>
									<tr>
										<td class='description' colspan='2' style='border-top: 0px;'>Pembayaran</td>
										<td class='price' style='text-align:right;border-top: 0px;'>".number_format($data_penjualan[0]->harga_bayar,0,',','.')."</td>
									</tr>
									<tr>
										<td class='description' colspan='2' style='border-top: 0px;'>Kembalian</td>
										<td class='price' style='text-align:right;border-top: 0px;'>".number_format($data_penjualan[0]->harga_kembalian,0,',','.')."</td>
									</tr>	
								";

							$retval .= "</tbody>
						</table>
						<p class='centered2' style='font-size: 9px;'>Terima Kasih
							<br>Atas Kepercayaan Anda</p>
					</div>
				</body>
			</html>
		";

		return $retval;
	}

	public function index()
	{
		$id_user = $this->session->userdata('id_user'); 
		$data_user = $this->m_user->get_detail_user($id_user);
		$list_item = $this->m_global->multi_row('*', ['id_jenis_trans' => 1, 'deleted_at' => null], 'm_item_trans', NULL, 'nama');
		
		/**
		 * data passing ke halaman view content
		 */
		$data = array(
			'title' => 'Formulir Penjualan',
			'data_user' => $data_user,
			'list_item' => $list_item,
		);

		// echo "<pre>";
		// print_r ($data);
		// echo "</pre>";
		// exit;

		/**
		 * content data untuk template
		 * param (css : link css pada direktori assets/css_module)
		 * param (modal : modal komponen pada modules/nama_modul/views/nama_modal)
		 * param (js : link js pada direktori assets/js_module)
		 */
		$content = [
			'css' 	=> null,
			'modal' => null,
			'js'	=> 'penjualan.js',
			'view'	=> 'form_data_penjualan'
		];

		$this->template_view->load_view($content, $data);
	}
	
	public function get_detail_item($kode_member = null)
	{
		$html = '';
		$total = 0;
		$counter_mobil = 0;
		$counter_motor = 0;

		if($kode_member != null) {
			$data_member = $this->m_global->single_row('*', ['kode_member' => $kode_member, 'deleted_at' => null], 'm_member');
			$counter = $this->lib_fungsi->cek_counter($data_member->id);
			//var_dump($counter);exit;
			if ($counter != null) {
				foreach ($counter as $key => $value) {
					if ($value->id_jenis_counter == '1') {
						$counter_mobil = $value->total_count;
					} else if ($value->id_jenis_counter == '2') {
						$counter_motor = $value->total_count;
					}
				}
			}
		}

		// var_dump((int)$counter_mobil, (int)$counter_motor);exit;
		
		if($this->input->get('arrItem') != '') {
			for ($i=0; $i < count($this->input->get('arrItem')); $i++) { 
				$id_item = $this->input->get('arrItem')[$i];
				$det_item = $this->m_global->single_row('*', ['id_jenis_trans' => 1, 'deleted_at' => null, 'id' => $id_item], 'm_item_trans');
				if($det_item) {
					if((int)$counter_mobil >= 9) {
						
						if($det_item->id_jenis_counter == '1') {
							$total += 0;
							$harga_fix = 0;
						}else{
							$total += (float)$det_item->harga;
							$harga_fix = $det_item->harga;
						}

					}else{
						if ((int)$counter_motor >= 9) {
							if ($det_item->id_jenis_counter == '2') {
								$total += 0;
								$harga_fix = 0;
							} else {
								$total += (float)$det_item->harga;
								$harga_fix = $det_item->harga;
							}
						}else{
							$total += (float)$det_item->harga;
							$harga_fix = $det_item->harga;
						}
					}
					
					$html .= '<tr>
								<td>'.$det_item->nama.'</td>
								<td>
									<input type="hidden" class="form-control" value='.$det_item->id.' name="id_item[]">
									<input type="hidden" class="form-control" value='.$harga_fix.' name="harga[]">
								</td>
								<td class="kt-font-danger kt-font-lg" style="text-align: right;"><div><span class="pull-left">Rp. </span><span class="pull-right">'.number_format($harga_fix,2,',','.').'</span></td>
							</tr>';
				}
			}
		}
		

		if($html != '') {
			$html .= '<tr>
						<th><span style="font-size:16px;font-weight:bold;">Grand Total</span></th>
						<th><input type="hidden" id="total_harga_global" class="form-control" value="'.$total.'" name="total"></th>
						<th class="kt-font-danger kt-font-lg" style="text-align: right;"><div><span class="pull-left">Rp. </span><span class="pull-right">'.number_format($total,2,',','.').'</span></th>
					</tr>';

			$html .= '<tr>
						<th><span style="font-size:16px;font-weight:bold;">Pembayaran</span></th>
						<th><input type="hidden" id="pembayaran_harga_global" class="form-control" name="pembayaran"></th>
						<th class="kt-font-success kt-font-lg" style="text-align: right;"><div><span class="pull-left">Rp. </span><span class="pull-right" id="span_pembayaran_harga_global">'.number_format(0,2,',','.').'</span></th>
					</tr>';

			$html .= '<tr>
						<th><span style="font-size:16px;font-weight:bold;">Kembalian</span></th>
						<th><input type="hidden" id="kembalian_harga_global" class="form-control" name="kembalian"></th>
						<th class="kt-font-primary kt-font-lg" style="text-align: right;"><div><span class="pull-left">Rp. </span><span class="pull-right" id="span_kembalian_harga_global">'.number_format(0,2,',','.').'</span></th>
					</tr>';
		}
		
		
		$retval = [
			'html' => $html,
			// 'data' => $det_item,
		];

		echo json_encode($retval);
	}

	private function get_div_button($id_header)
	{
		return '
			<button type="button" class="btn btn-secondary" onclick="location.reload()">Transaksi Selanjutnya</button>
			<button type="button" class="btn btn-brand" onclick="printStruk(\''.$id_header.'\')">Print</button>
		';
	}

	public function get_no_invoice()
	{
		$nomor = $this->t_transaksi->get_invoice();
		echo json_encode($nomor);
	}

	public function get_data_penjualan_edit()
	{
		$id = $this->input->post('id');
		$join = [ 
			['table' => 'm_member', 'on' => 't_transaksi.id_member = m_member.id']
		];

		$data_trans = $this->m_global->single_row('t_transaksi.*, m_member.kode_member', ['t_transaksi.id' => $id, 't_transaksi.is_kunci' => '0'], 't_transaksi', $join);
		
		if($data_trans) {
			$data_det = $this->m_global->multi_row('*', ['id_transaksi' => $data_trans->id], 't_transaksi_det');
			$retval = [
				'data' => $data_trans,
				'data_det' => $data_det,
				'status' => true,
			];
		}else{
			$retval = [
				'data' => null,
				'data_det' => null,
				'status' => false,
			];
		}
		echo json_encode($retval);
	}

	public function simpan_trans_reg()
	{
		$data_log_arr = [];
		$obj_date = new DateTime();
		$timestamp = $obj_date->format('Y-m-d H:i:s');
		$list_item = $this->input->post('list_item_reg'); 
		
		if($list_item == null) {
			$data['inputerror'][] = 'list_item_reg';
            $data['error_string'][] = 'Wajib mengisi Item Transaksi';
			$data['status'] = FALSE;
			$data['is_select2'][] = TRUE;
			echo json_encode($data);
			return;
		}

		$arr_valid = $this->rule_validasi('reguler');
		
		if ($arr_valid['status'] == FALSE) {
			echo json_encode($arr_valid);
			return;
		}

		$this->db->trans_begin();

		## insert header
		$id_header = gen_uuid();
		$data_ins = [
			'id' => $id_header,
			'kode' => $this->t_transaksi->get_invoice(),
			'id_jenis_trans' => self::ID_JENIS_TRANS,
			'id_user' => $this->session->userdata('id_user'),
			'created_at' => $timestamp,
			'bulan_trans' => (int)$obj_date->format('m'),
			'tahun_trans' => (int)$obj_date->format('Y'),
			'tgl_trans' => $obj_date->format('Y-m-d')
		];

		$insert = $this->t_transaksi->save($data_ins);
		if($insert) {
			$total = 0;
			for ($i=0; $i < count($list_item); $i++) {
				$total += $this->input->post('harga')[$i];
				
				$data_ins_det = [
					'id' => gen_uuid(),
					'id_transaksi' => $id_header,
					'id_item_trans' => $this->input->post('id_item')[$i],
					'harga_satuan' => $this->input->post('harga')[$i],
					'qty' => 1,
					'created_at' => $timestamp
				];

				$insert_det = $this->t_transaksi_det->save($data_ins_det);
				$data_log_arr[] = $data_ins_det;
			}

			$data_upd_header = [
				'harga_total' => $total,
				'harga_bayar' => $this->input->post('pembayaran_reg_raw'),
				'harga_kembalian' => $this->input->post('kembalian_reg_raw'),
			];

			$update = $this->t_transaksi->update(['id'=> $id_header], $data_upd_header);

			$data_log = json_encode($data_log_arr);
			$this->lib_fungsi->catat_log_aktifitas('CREATE', null, $data_log);
		}
				
		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$retval['id_trans'] = $id_header;
			$retval['status'] = false;
			$retval['button'] = false;
			$retval['pesan'] = 'Gagal memproses Data Transaksi';
		}else{
			$this->db->trans_commit();
			$retval['id_trans'] = $id_header;
			$retval['status'] = true;
			$retval['button'] = $this->get_div_button($id_header);
			$retval['pesan'] = 'Sukses memproses Data Transaksi';
		}

		echo json_encode($retval);
	}

	public function simpan_trans_mem()
	{
		$data_log_arr = [];
		$counter_mobil = 0;
		$counter_motor = 0;
		$is_add_cnt_mobil = false;
		$is_add_cnt_motor = false;

		$obj_date = new DateTime();
		$timestamp = $obj_date->format('Y-m-d H:i:s');
		$list_item = $this->input->post('list_item_mem'); 
		$kode_member = $this->input->post('member_id');
		$data_member = $this->m_global->single_row('*', ['deleted_at' => null, 'kode_member' => $kode_member], 'm_member');

		if(!$data_member) {
			$data['inputerror'][] = 'member_id';
            $data['error_string'][] = 'Kode Member Tidak Ditemukan';
			$data['status'] = FALSE;
			$data['is_select2'][] = FALSE;
			echo json_encode($data);
			return;
		}
		
		if($list_item == null) {
			$data['inputerror'][] = 'list_item_mem';
            $data['error_string'][] = 'Wajib mengisi Item Transaksi';
			$data['status'] = FALSE;
			$data['is_select2'][] = TRUE;
			echo json_encode($data);
			return;
		}

		$arr_valid = $this->rule_validasi('member');
		
		if ($arr_valid['status'] == FALSE) {
			echo json_encode($arr_valid);
			return;
		}

		$this->db->trans_begin();

		## insert header
		$id_header = gen_uuid();
		$data_ins = [
			'id' => $id_header,
			'kode' => $this->t_transaksi->get_invoice(),
			'id_jenis_trans' => self::ID_JENIS_TRANS,
			'id_member' => $data_member->id,
			'id_user' => $this->session->userdata('id_user'),
			'created_at' => $timestamp,
			'bulan_trans' => (int)$obj_date->format('m'),
			'tahun_trans' => (int)$obj_date->format('Y'),
			'tgl_trans' => $obj_date->format('Y-m-d')
		];

		$insert = $this->t_transaksi->save($data_ins);
		if($insert) {
			$total = 0;
			for ($i=0; $i < count($list_item); $i++) {
				$total += $this->input->post('harga')[$i];
				
				$data_ins_det = [
					'id' => gen_uuid(),
					'id_transaksi' => $id_header,
					'qty' => 1,
					'id_item_trans' => $this->input->post('id_item')[$i],
					'harga_satuan' => $this->input->post('harga')[$i],
					'created_at' => $timestamp,
				];

				$insert_det = $this->t_transaksi_det->save($data_ins_det);
				$data_log_arr[] = $data_ins_det;
			}

			$data_upd_header = [
				'harga_total' => $total,
				'harga_bayar' => $this->input->post('pembayaran_mem_raw'),
				'harga_kembalian' => $this->input->post('kembalian_mem_raw')
			];

			$update = $this->t_transaksi->update(['id'=> $id_header], $data_upd_header);

			### cek tipe item
			for ($z = 0; $z < count($list_item); $z++) {
				$id_item = $this->input->post('id_item')[$z];
				$cek = $this->m_global->single_row('*', ['id' => $id_item], 'm_item_trans');
				if($cek->id_jenis_counter && $cek->id_jenis_counter == '1') {
					$is_add_cnt_mobil = true;
				}

				if ($cek->id_jenis_counter && $cek->id_jenis_counter == '2') {
					$is_add_cnt_motor = true;
				}
			}

			if($update) {
				$counter = $this->lib_fungsi->cek_counter($data_member->id);
				if ($counter != null) {
					foreach ($counter as $key => $value) {
						if ($value->id_jenis_counter == '1') {
							$counter_mobil = $value->total_count;
						} else if ($value->id_jenis_counter == '2') {
							$counter_motor = $value->total_count;
						}
					}
				}

				$ins_count = $this->lib_fungsi->insert_counter($data_member->id, $counter_mobil, $counter_motor, $is_add_cnt_mobil, $is_add_cnt_motor);

				if($ins_count === FALSE) {
					$this->db->trans_rollback();
					$retval['status'] = false;
					$retval['button'] = false;
					$retval['pesan'] = 'Gagal memproses Data Transaksi';
				}
			}

			$data_log = json_encode($data_log_arr);
			$this->lib_fungsi->catat_log_aktifitas('CREATE', null, $data_log);
		}
				
		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$retval['status'] = false;
			$retval['button'] = false;
			$retval['pesan'] = 'Gagal memproses Data Transaksi';
			$retval['id_trans'] = $id_header;
		}else{
			$this->db->trans_commit();
			$retval['status'] = true;
			$retval['button'] = $this->get_div_button($id_header);
			$retval['pesan'] = 'Sukses memproses Data Transaksi';
			$retval['id_trans'] = $id_header;
		}

		echo json_encode($retval);
	}

	private function rule_validasi($flag)
	{
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		if($flag == 'reguler') {
			if ($this->input->post('pembayaran_reg') == '') {
				$data['inputerror'][] = 'pembayaran_reg';
				$data['error_string'][] = 'Wajib Mengisi Pembayaran';
				$data['status'] = FALSE;
				$data['is_select2'][] = FALSE;
			}
			
		}else{
			if ($this->input->post('pembayaran_mem') == '') {
				$data['inputerror'][] = 'pembayaran_mem';
				$data['error_string'][] = 'Wajib Mengisi Pembayaran';
				$data['status'] = FALSE;
				$data['is_select2'][] = FALSE;
			}
			
		}

        return $data;
	}

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
		echo '<table border="1" width="100%">';
		for ($i=1; $i <= 30; $i++) { 
			
			$rundom = 'M'.rand();
			echo '<tr height="100px">';
			echo '<td width="5%">'.$i.'</td>';
			echo  '<td align="center">'.$this->barcode_lib->generate_html($rundom).'</td>';
			echo '<td>'.$rundom.'</td>';
			echo '</tr>';

		}
		echo '</table>';
		

	}

	public function simpan_barcode($value = '123456')
	{
		$this->barcode_lib->save_jpg($value);
	}


	////////////////////////////////////////////////////////////////////////////////

	public function print_html()
	{
		$this->load->view('print_temp');
		
	}
}
