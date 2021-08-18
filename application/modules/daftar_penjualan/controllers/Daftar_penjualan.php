<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Daftar_penjualan extends CI_Controller {
	
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

	public function index()
	{
		$id_user = $this->session->userdata('id_user'); 
		$data_user = $this->m_user->get_detail_user($id_user);
				
		/**
		 * data passing ke halaman view content
		 */
		$data = array(
			'title' => 'Pengelolaaan Daftar Penjualan',
			'data_user' => $data_user,
			// 'jenis_trans' => $this->m_global->getSelectedData('m_jenis_trans', NULL)->result()
		);

		/**
		 * content data untuk template
		 * param (css : link css pada direktori assets/css_module)
		 * param (modal : modal komponen pada modules/nama_modul/views/nama_modal)
		 * param (js : link js pada direktori assets/js_module)
		 */
		$content = [
			'css' 	=> null,
			'modal' => 'modal_daftar_penjualan',
			'js'	=> 'daftar_penjualan.js',
			'view'	=> 'view_daftar_penjualan'
		];

		$this->template_view->load_view($content, $data);
	}

	public function list_penjualan()
	{
		$obj_date = new DateTime();
		$timestamp = $obj_date->format('Y-m-d H:i:s');
		$tgl_awal = $obj_date->createFromFormat('d/m/Y', $this->input->post('tglAwal'))->format('Y-m-d').' 00:00:00';
		$tgl_akhir = $obj_date->createFromFormat('d/m/Y', $this->input->post('tglAkhir'))->format('Y-m-d').' 23:59:59';
	
		$list = $this->t_transaksi->get_datatable_penjualan($tgl_awal, $tgl_akhir);
		
		$data = array();
		$no =$_POST['start'];
		foreach ($list as $item) {
			// $no++;
			$row = array();
			//loop value tabel db
			// $row[] = $no;
			$row[] = $item->kode;
			$row[] = $obj_date->createFromFormat('Y-m-d H:i:s', $item->created_at)->format('d-m-Y H:i');
			
			$jenis_member_txt = ($item->jenis_member == 'Reguler') ? '<span style="color:blue;">Reguler</span>' : '<span style="color:red;">Member</span>';
			$row[] = $jenis_member_txt;
			
			$row[] = $item->nama;
			$row[] = number_format($item->harga_total, 0 ,',','.');
			$row[] = number_format($item->harga_bayar, 0 ,',','.');
			$row[] = number_format($item->harga_bayar, 0 ,',','.');

			$status_kuncian = ($item->status_kunci == 'Terkunci') ? '<span style="color:green;">Terkunci</span>' : '<span style="color:red;">Terbuka</span>';
			$row[] = $status_kuncian;
			
			// $row[] = $aktif_txt;			
			
			$str_aksi = '
				<div class="btn-group">
					<button type="button" class="btn btn-sm btn_1 dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> Opsi</button>
					<div class="dropdown-menu">
						<button class="dropdown-item" onclick="detailPenjualan(\''.$item->id.'\')">
							<i class="la la-file"></i> Detail Penjualan
						</button>
			';

			if($this->session->userdata('id_role') == '1') {
				$str_aksi .= '
					<button class="dropdown-item" onclick="toggleKunci(\''.$item->id.'\')">
						<i class="la la-lock"></i> Buka/Kunci
					</button>
					<button class="dropdown-item" onclick="deletePenjualan(\''.$item->id.'\')">
						<i class="la la-trash"></i> Hapus Penjualan
					</button>
				';
			}else{
				if($item->status_kunci == 'Terbuka') {
					$str_aksi .= '
						<button class="dropdown-item" onclick="editPenjualan(\''.$item->id.'\')">
							<i class="la la-pencil"></i> Edit Penjualan
						</button>
					';
				}
			}

			$str_aksi .= '</div></div>';


			$row[] = $str_aksi;

			$data[] = $row;

		}//end loop

		$output = [
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->t_transaksi->count_all_penjualan($tgl_awal, $tgl_akhir),
			"recordsFiltered" => $this->t_transaksi->count_filtered_penjualan($tgl_awal, $tgl_akhir),
			"data" => $data
		];
		
		echo json_encode($output);
	}

	function get_detail_penjualan() {
		$id = $this->input->get('id');
		$data = $this->t_transaksi->get_detail_penjualan($id);
		$html = '';
		$html2 = '';
		
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
							<th>Potongan</th>
							<th>Ket Potongan</th>
							</tr>
						</thead>
						<tbody>
			';

			$total_harga = 0;
			$total_pot = 0;
			foreach ($data as $key => $value) {
				$txt_disc_jual = ($value->is_disc_jual) ? number_format($value->harga_satuan, 0 ,',','.') : '0';
				$txt_ket_disc_jual = ($value->ket_disc_jual) ? $value->ket_disc_jual : '-';
				$total_harga += $value->harga_satuan;
				$total_pot += ($value->is_disc_jual) ? $value->harga_satuan : 0;
				$html .= '
                    <tr>
                      <th scope="row">'.($key+1).'</th>
                      <td>'.$value->nama_item.'</td>
					  <td>Rp '.number_format($value->harga_satuan, 0 ,',','.').'</td>
                      <td>'.$txt_disc_jual.'</td>
					  <td>'.$txt_ket_disc_jual.'</td>
                    </tr>';  
			}

			$html .= '
					<tr>
						<th scope="row" colspan="2">Total</th>
						<td>Rp '.number_format($total_harga, 0 ,',','.').'</td>
						<td>Rp '.number_format($total_pot, 0 ,',','.').'</td>
					</tr>
					<tr>
						<th scope="row" colspan="3">Grand Total</th>
						<td colspan="2" align="center">Rp '.number_format(($total_harga-$total_pot), 0 ,',','.').'</td>
					</tr>
					';  
			
			$html .= '</tbody></table></div></div>';

			$html2 .= '
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button id="btnCetak" type="button" class="btn btn-primary" onclick="printStruk(\''.$data[0]->id.'\')">Cetak</button>
			';
		}else{
			$status = false;
		}
		
		echo json_encode([
			'status' => $status,
			'data' => $data,
			'html' => $html,
			'html2' => $html2
		]);
	}

	public function toggle_kunci()
	{
		$id_trans = $this->input->post('id_trans');
		$data_trans = $this->m_global->single_row('*', ['id' => $id_trans], 't_transaksi');

		#### cek kuncian laporan
		if($data_trans) {
			## cek kunci bulanan dulu, sudah dikunci atau belum
			$cek_kunci = $this->cek_kunci_transaksi($data_trans->tgl_trans);
			
			if($cek_kunci['status'] == true) {
				$bulan_kunci = $cek_kunci['bulan_kunci'];
				$tahun_kunci = $cek_kunci['tahun_kunci'];
				$bln_txt = bulan_indo($bulan_kunci);
				echo json_encode([ 
					'status' => false,
					'pesan' => 'Maaf Laporan Bulan '.$bln_txt.' '.$tahun_kunci.' Telah Terkunci'
				]);
				return;
			}

			$status = true;
			if($data_trans->is_kunci == '1') {
				$where = ['is_kunci' => '0'];
				$pesan = 'Transaksi di Buka Kuncinya';
			}else{
				$where = ['is_kunci' => '1'];
				$pesan = 'Transaksi di Kunci';
			}

			$update = $this->t_transaksi->update(['id' => $data_trans->id], $where);
			
			if($update) {
				$data_log_old = json_encode($data_trans);
				
				$data_trans_new = $this->m_global->single_row('*', ['id' => $id_trans], 't_transaksi');
				$data_log_new = json_encode($data_trans_new);
				
				$this->lib_fungsi->catat_log_aktifitas('UPDATE', $data_log_old, $data_log_new);
				echo json_encode(['status' => $status, 'pesan' => $pesan]);
			}
		}else{
			$status = false;
			echo json_encode(['status' => $status, 'pesan' => 'Maaf Data tidak ditemukan, Proses Gagal']);
		}
	}

	/////////////////////////////

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
		$del = $this->m_item_trans->softdelete_by_id($id);
		if($del) {
			$retval['status'] = TRUE;
			$retval['pesan'] = 'Data Master Item Transaksi Berhasil dihapus';
		}else{
			$retval['status'] = FALSE;
			$retval['pesan'] = 'Data Master Item Transaksi Gagal dihapus';
		}

		echo json_encode($retval);
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
