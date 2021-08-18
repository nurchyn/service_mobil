<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lap_transaksi extends CI_Controller {
	const ID_JENIS_PENJUALAN = 1;
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

	public function index()
	{	
		$obj_date = new DateTime();
		$timestamp = $obj_date->format('Y-m-d H:i:s');
		
		### deklarasi variabel
		$arr_valid = [];
		$data_laporan = null;
		$periode = null;
		$jenis_trans_txt = null;
		$html = '';

		$id_user = $this->session->userdata('id_user'); 
		$data_user = $this->m_user->get_detail_user($id_user);
		$jenis_trans	= $this->m_global->multi_row('*', ['deleted_at' => null], 'm_jenis_trans', NULL, 'kode_jenis asc');
		$profil = $this->m_global->single_row('*', ['deleted_at' => null], 'm_profil');

		if($this->input->get('mulai') != null){
			$tgl_awal = $obj_date->createFromFormat('d/m/Y', $this->input->get('mulai'))->format('Y-m-d');
			$arr_valid[] = true;
		}else{
			$arr_valid[] = false;
		}

		if($this->input->get('akhir') != null){
			$tgl_akhir = $obj_date->createFromFormat('d/m/Y', $this->input->get('akhir'))->format('Y-m-d');
			$arr_valid[] = true;
		}else{
			$arr_valid[] = false;
		}

		if($this->input->get('jenis') != null){
			## cek valid jenis
			$cek = $this->m_global->single_row('*', ['id' => $this->input->get('jenis')], 'm_jenis_trans');
			if($cek) {
				$arr_valid[] = true;
				$jenis_trans_txt = $cek->nama_jenis;
			}else{
				$arr_valid[] = false;
			}
		}else{
			$arr_valid[] = false;
		}
		
		if (!in_array(false, $arr_valid)){
			if(isset($tgl_awal) && isset($tgl_akhir)) {
				$periode = $this->input->get('mulai').' s/d '.$this->input->get('akhir');
				$html .= $this->load_tabel_laporan($this->input->get('jenis'), $tgl_awal, $tgl_akhir);
			}
		}

		

		/**
		 * data passing ke halaman view content
		 */
		$data = array(
			'title' => 'Silahkan Pilih Laporan Transaksi',
			'data_user' => $data_user,
			'jenis_trans' => $jenis_trans,
			'data_laporan' => $data_laporan,
			'periode' => $periode,
			'profil' => $profil,
			'jenis_trans_txt' => $jenis_trans_txt,
			'html' => $html
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
			'js'	=> 'lap_transaksi.js',
			'view'	=> 'view_lap_transaksi'
		];

		$this->template_view->load_view($content, $data);
	}

	private function load_tabel_laporan($id_jenis_trans, $tgl_awal, $tgl_akhir)
	{
		$data_laporan = $this->t_transaksi->get_laporan_transaksi($id_jenis_trans, $tgl_awal, $tgl_akhir);
				
		switch ((int)$id_jenis_trans) {
			case self::ID_JENIS_PENJUALAN:
				return $this->list_penjualan($data_laporan);
				break;

			case self::ID_JENIS_PEMBELIAN:
				return $this->list_data_pembelian($data_laporan);
				break;

			case self::ID_JENIS_PENGGAJIAN:
				return $this->list_data_global($data_laporan);
				break;

			case self::ID_JENIS_INVESTASI:
				return $this->list_data_global($data_laporan);
				break;

			case self::ID_JENIS_OPERSAIONAL:
				return $this->list_data_global($data_laporan);
				break;

			case self::ID_JENIS_PENGELUARAN_LAIN:
				return $this->list_data_global($data_laporan);
				break;

			case self::ID_JENIS_PENERIMAAN_LAIN:
				return $this->list_data_global($data_laporan);
				break;
			
			default:
				return false;
				break;
		}
	}

	public function list_penjualan($data)
	{
		$obj_date = new DateTime();
		$timestamp = $obj_date->format('Y-m-d H:i:s');
		$total_harga = 0;
		$html = '
			<table id="tbl_content" class="table table-bordered table-hover" cellspacing="0" width="100%" border="2">
				<thead>
					<tr>
						<th style="width: 5%;text-align:center">No.</th>
						<th style="width: 10%;text-align:center">Tanggal</th>
						<th style="width: 15%;text-align:center">Kode</th>
						<th style="width: 10%; text-align:center">Jenis member</th>
						<th style="width: 10%; text-align:center">Kode member</th>
						<th style="width: 30%;text-align:center">Member</th>
						<th style="text-align:center">Harga Satuan</th>
					</tr>
				</thead>
				<tbody>';
				foreach ($data as $key => $value) {
					$total_harga += $value->harga_satuan;
					$is_jenis = ($value->id_member == '1') ? 'Member' : 'Reguler';
					$html .= '
						<tr>
							<td>'.($key+1).'</td>
							<td>'.$obj_date->createFromFormat('Y-m-d', $value->tgl_trans)->format('d/m/Y').'</td>
							<td>'.$value->kode.'</td>
							<td>'.$is_jenis.'</td>
							<td>'.$value->kode_member.'</td>
							<td>'.$value->nama_member.'</td>
							<td align="right">'.number_format($value->harga_satuan, 0 ,',','.').'</td>
						</tr>
					';
				}		
		$html .= '
				<tr>
					<td colspan="6" align="center"><strong>Jumlah Total</strong></td>
					<td align="right"><strong>'.number_format($total_harga, 0 ,',','.').'</strong></td>
				</tr>
			</tbody>
		</table>';
		// var_dump($html);exit;
		return $html;
	}

	public function list_data_pembelian($data)
	{
		$obj_date = new DateTime();
		$timestamp = $obj_date->format('Y-m-d H:i:s');
		$total_harga = 0;

		$html = '
			<table class="table table-bordered">
				<thead>
					<tr>
						<th style="width: 5%;text-align:center">No.</th>
						<th style="text-align:center">Tanggal</th>
						<th style="text-align:center">Nama</th>
						<th style="text-align:center">Supplier</th>
						<th style="text-align:center">Qty</th>
						<th style="text-align:center">Harga Satuan</th>
						<th style="text-align:center">Harga Total</th>
					</tr>
				</thead>
				<tbody>';
				foreach ($data as $key => $value) {
					$total_harga += $value->harga_total;
					$html .= '
						<tr>
							<td align="center">'.($key+1).'</td>
							<td>'.$obj_date->createFromFormat('Y-m-d', $value->tgl_trans)->format('d/m/Y').'</td>
							<td>'.$value->nama.'</td>
							<td>'.$value->nama_supplier.'</td>
							<td>'.number_format($value->qty, 0 ,',','.').'</td>
							<td align="right">'.number_format($value->harga_satuan, 0 ,',','.').'</td>
							<td align="right">'.number_format($value->harga_total, 0 ,',','.').'</td>
						</tr>
					';
				}		
		$html .= '
				<tr>
					<td colspan="6" align="center"><strong>Jumlah Total</strong></td>
					<td align="right"><strong>'.number_format($total_harga, 0 ,',','.').'</strong></td>
				</tr>
			</tbody>
		</table>';

		return $html;
	}

	public function list_data_global($data)
	{
		$obj_date = new DateTime();
		$timestamp = $obj_date->format('Y-m-d H:i:s');
		$total_harga = 0;

		$html = '
			<table class="table table-bordered">
				<thead>
					<tr>
						<th style="text-align:center">No.</th>
						<th style="text-align:center">Tanggal</th>
						<th style="text-align:center">Nama</th>
						<th style="text-align:center">Bulan</th>
						<th style="text-align:center">Tahun</th>
						<th style="text-align:center">Harga Total</th>
					</tr>
				</thead>
				<tbody>';
				foreach ($data as $key => $value) {
					$total_harga += $value->harga_total;
					$html .= '
						<tr>
							<td>'.($key+1).'</td>
							<td>'.$obj_date->createFromFormat('Y-m-d', $value->tgl_trans)->format('d/m/Y').'</td>
							<td>'.$value->nama.'</td>
							<td>'.bulan_indo((int)$value->bulan_trans).'</td>
							<td>'.$value->tahun_trans.'</td>
							<td align="right">'.number_format($value->harga_total, 0 ,',','.').'</td>
						</tr>
					';
				}		
		$html .= '
				<tr>
					<td colspan="5" align="center"><strong>Jumlah Total</strong></td>
					<td align="right"><strong>'.number_format($total_harga, 0 ,',','.').'</strong></td>
				</tr>
			</tbody>
		</table>';

		return $html;
	}

	public function cetak_laporan()
	{
		$obj_date = new DateTime();
		$timestamp = $obj_date->format('Y-m-d H:i:s');
		$mulai = $this->input->post('mulai');
		$akhir = $this->input->post('akhir');
		$jenis = $this->input->post('jenis');

		$tgl_awal = $obj_date->createFromFormat('d/m/Y', $mulai)->format('Y-m-d');
		$tgl_akhir = $obj_date->createFromFormat('d/m/Y', $akhir)->format('Y-m-d');
		
		$cek = $this->m_global->single_row('*', ['id' => $jenis], 'm_jenis_trans');
		$jenis_trans_txt = $cek->nama_jenis;
		$periode = $mulai.' s/d '.$akhir;

		#### nanti dilakukan pengecekan disini
		$data = $this->load_tabel_laporan($jenis, $tgl_awal, $tgl_akhir);
		$periode = $tgl_awal.' s/d '.$tgl_akhir;
		$profil = $this->m_global->single_row('*', ['deleted_at' => null], 'm_profil');

		$retval = [
			'data' => $data,
			'title' => 'Laporan Transaksi '.$jenis_trans_txt,
			'periode' => 'Periode '.$periode,
			'profil' => $profil,
		];
		
		// echo "<pre>";
		// print_r ($retval);
		// echo "</pre>";
		// exit;

		// $this->load->view('view_lap_transaksi_pdf', $retval);
		$html = $this->load->view('view_lap_transaksi_pdf', $retval, true);
	    $filename = 'laporan_transaksi_'.$jenis_trans_txt.'_'.time();
	    $this->lib_dompdf->generate($html, $filename, true, 'legal', 'potrait');
	}

	public function import_excel()
	{
		$bulan = $this->input->get('bulan');
		$tahun = $this->input->get('tahun');
		
		$obj_date = new DateTime();
		$obj_date2 = new DateTime($tahun.'-'.$bulan.'-01');
		$timestamp = $obj_date->format('Y-m-d H:i:s');
		#### nanti dilakukan pengecekan disini
		$data = $this->load_tabel_laporan($bulan, $tahun, true);
		$periode = bulan_indo($bulan).' '.$tahun;
		$profil = $this->m_global->single_row('*', ['deleted_at' => null], 'm_profil');
		
		
		// echo "<pre>";
		// print_r ($data);
		// echo "</pre>";
		// exit;

		if($data) {
			$counter = count($data['data'])+1;
		}else{
			$counter = 1;
		}

		$spreadsheet = $this->excel->spreadsheet_obj();
		$writer = $this->excel->xlsx_obj($spreadsheet);
		$number_format_obj = $this->excel->number_format_obj();
		
		$spreadsheet
			->getActiveSheet()
			->getStyle('A1:G'.$counter)
			->getNumberFormat()
			//format text masih ada bug di nip. jadi kacau
			//->setFormatCode($number_format_obj::FORMAT_TEXT);
			// solusi pake format custom
			->setFormatCode('#');
		
		$sheet = $spreadsheet->getActiveSheet();

		$sheet
			->setCellValue('A1', 'No')
			->setCellValue('B1', 'Tanggal')
			->setCellValue('C1', 'Kode')
			->setCellValue('D1', 'Jenis')
			->setCellValue('E1', 'Penerimaan')
			->setCellValue('F1', 'Pengeluaran')
			->setCellValue('G1', 'Saldo Akhir');
		$no = 1;
		$startRow = 2;
		$row = $startRow;
		if($data['data']){
			// row saldo
			if($data['saldo'] != null) {
				if($data['saldo'] == 'kosong') {
				  $saldonya = 0;
				}else{
				  // hasil hitungan
				  $saldonya = (float)$data['saldo'];
				}
	  
				$sheet
					->setCellValue("A{$row}", $no++)
					->setCellValue("B{$row}", $obj_date2->format('d/m/Y'))
					->setCellValue("C{$row}", '-')
					->setCellValue("D{$row}", 'Saldo Awal')
					->setCellValue("E{$row}", '-')
					->setCellValue("F{$row}", '-')
					->setCellValue("G{$row}", number_format($saldonya, 0 ,',','.'));
				
				$row++;
			}

			$tot_penerimaan = 0;
        	$tot_pengeluaran = 0;
			foreach ($data['data'] as $key => $val) {
				$penerimaan = 0;
				$pengeluaran = 0;

				if($val->cashflow == 'in') {
					$saldonya += $val->total_harga;
					$penerimaan = $val->total_harga;
					$tot_penerimaan += $val->total_harga;
				}else{
					$saldonya -= $val->total_harga;
					$pengeluaran = $val->total_harga;
					$tot_pengeluaran += $val->total_harga;
				}

				$sheet
					->setCellValue("A{$row}", $no++)
					->setCellValue("B{$row}", $obj_date->createFromFormat('Y-m-d', $val->tgl_trans)->format('d/m/Y'))
					->setCellValue("C{$row}", $val->kode_jenis)
					->setCellValue("D{$row}", $val->nama_jenis)
					->setCellValue("E{$row}", number_format($penerimaan, 0 ,',','.'))
					->setCellValue("F{$row}", number_format($pengeluaran, 0 ,',','.'))
					->setCellValue("G{$row}", number_format($saldonya, 0 ,',','.'));
				$row++;
			}

			$sheet->mergeCells("A{$row}:D{$row}");
			$sheet
					->setCellValue("A{$row}", 'Grand Total')
					->setCellValue("E{$row}", number_format($tot_penerimaan, 0 ,',','.'))
					->setCellValue("F{$row}", number_format($tot_pengeluaran, 0 ,',','.'))
					->setCellValue("G{$row}", number_format($saldonya, 0 ,',','.'));
				$row++;

			$endRow = $row - 1;
		}
		
		
		$filename = 'laporan_keuangan_'.$bulan.'_'.$tahun.'_'.time();
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
		
	}
}
