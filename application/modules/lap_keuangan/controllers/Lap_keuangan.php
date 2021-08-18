<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lap_keuangan extends CI_Controller {
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
		$profil = $this->m_global->single_row('*', ['deleted_at' => null], 'm_profil');

		if($this->input->get('bulan') != null){
			$arr_valid[] = true;
		}else{
			$arr_valid[] = false;
		}

		if($this->input->get('tahun') != null){
			$arr_valid[] = true;
		}else{
			$arr_valid[] = false;
		}

		if (!in_array(false, $arr_valid)){
			$periode = bulan_indo($this->input->get('bulan')).' '.$this->input->get('tahun');
			$html .= $this->load_tabel_laporan($this->input->get('bulan'), $this->input->get('tahun'));
		}

		

		/**
		 * data passing ke halaman view content
		 */
		$data = array(
			'title' => 'Silahkan Pilih Periode Laporan',
			'data_user' => $data_user,
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
			'js'	=> 'lap_keuangan.js',
			'view'	=> 'view_lap_keuangan'
		];

		$this->template_view->load_view($content, $data);
	}

	private function load_tabel_laporan($bulan, $tahun, $is_data_only = false)
	{
		$cek = $this->m_global->single_row('*', ['bulan' => $bulan, 'tahun' => $tahun, 'deleted_at' => null], 't_log_laporan');
		if($cek) {
			$data_laporan = $this->t_transaksi->get_log_laporan_keuangan($bulan, $tahun);
			$html = $this->generate_html_laporan($data_laporan, $bulan, $tahun);
		}else{
			// cari bulan dan tahun periode sebelumnya
			$arr_periode = $this->ambil_periode_sebelum($bulan, $tahun);
			// cek saldo bulan sebelumnya untuk di olah 
			$cek_saldo = $this->m_global->single_row('*', ['bulan' => $arr_periode['bulan'], 'tahun' => $arr_periode['tahun'], 'deleted_at' => null], 't_log_laporan');
			if($cek_saldo) {
				// set saldo awal dari saldo akhir periode sebelumnya
				$saldo_fix = $cek_saldo->saldo_akhir;
			}else{
				//jika tidak ada terpaksa dihitung semua transaksi hingga sebelum bulan laporan terpilih
				$q_saldo = $this->t_transaksi->cari_saldo_by_hitung($bulan, $tahun);				
				
				if($q_saldo->saldo && (float)$q_saldo->saldo > 0) {
					$saldo_fix = (float)$q_saldo->saldo;
				}else{
					$saldo_fix = 'kosong';
				}

			}

			$data_laporan = $this->t_transaksi->get_laporan_keuangan($bulan, $tahun);
			
			if($is_data_only) {
				return [
					'data' => $data_laporan, 
					'bulan' => $bulan, 
					'tahun' => $tahun, 
					'saldo' => $saldo_fix
				];
			}

			$html = $this->generate_html_laporan($data_laporan, $bulan, $tahun, $saldo_fix);
		}
		
		return $html;
	}

	private function generate_html_laporan($data_laporan, $bulan, $tahun, $saldo_awal = null)
	{
		$obj_date = new DateTime();
		$obj_date2 = new DateTime($tahun.'-'.$bulan.'-01');
		$html = '
			<table class="table table-bordered">
				<thead>
					<tr>
						<th style="text-align:center">No.</th>
						<th style="text-align:center">Tanggal</th>
						<th style="text-align:center">Kode</th>
						<th style="text-align:center">Jenis</th>
						<th style="text-align:center">Penerimaan</th>
						<th style="text-align:center">Pengeluaran</th>
						<th style="text-align:center">Saldo Akhir</th>
					</tr>
				</thead>
				<tbody>
		';

		if($saldo_awal != null) {
			if($saldo_awal == 'kosong') {
				// hasil hitungan
				$saldonya = 0;
			}else{
				// hasil hitungan
				$saldonya = (float)$saldo_awal;
			}
			
			// tambah satu baris awal untuk menampilkan saldo awal
			$html .= '
				<tr>
					<td>1</td>
					<td>'.$obj_date2->format('d/m/Y').'</td>
					<td>-</td>
					<td>Saldo Awal</td>
					<td align="right">-</td>
					<td align="right">-</td>
					<td align="right">'.number_format($saldonya, 0 ,',','.').'</td>
				</tr>
			';

			$tot_penerimaan = 0;
        	$tot_pengeluaran = 0;
			foreach ($data_laporan as $key => $value) {
				$penerimaan = 0;
				$pengeluaran = 0;
				if($value->cashflow == 'in') {
					$saldonya += $value->total_harga;
					$penerimaan = $value->total_harga;
					$tot_penerimaan += $value->total_harga;
				}else{
					$saldonya -= $value->total_harga;
					$pengeluaran = $value->total_harga;
					$tot_pengeluaran += $value->total_harga;
				}

				$html .= '
					<tr>
						<td>'.($key+1).'</td>
						<td>'.$obj_date->createFromFormat('Y-m-d', $value->tgl_trans)->format('d/m/Y').'</td>
						<td>'.$value->kode_jenis.'</td>
						<td>'.$value->nama_jenis.'</td>
						<td align="right">'.number_format($penerimaan, 0 ,',','.').'</td>
						<td align="right">'.number_format($pengeluaran, 0 ,',','.').'</td>
						<td align="right">'.number_format($saldonya, 0 ,',','.').'</td>
					</tr>
				';
			}

			$html .= '
					<tr>
						<td colspan="4" align="center"><strong>Grand Total</strong></td>
						<td align="right"><strong>'.number_format($tot_penerimaan, 0 ,',','.').'</strong></td>
						<td align="right"><strong>'.number_format($tot_pengeluaran, 0 ,',','.').'</strong></td>
						<td align="right"><strong>'.number_format($saldonya, 0 ,',','.').'</strong></td>
					<tr>
				</tbody>
			</table>';
		}else{
			$html .= '</tbody></table>';
		}

		return $html;
	}
		
	private function ambil_periode_sebelum($bulan, $tahun)
	{
		$objDate = new DateTime($tahun.'-'.$bulan.'-01');
		$tgl_fix =  $objDate->modify('-1 month')->format('Y-m-d');
		$bulan = $objDate->createFromFormat('Y-m-d', $tgl_fix)->format('m');
		$tahun = $objDate->createFromFormat('Y-m-d', $tgl_fix)->format('Y');
		return [
			'bulan' => (int)$bulan,
			'tahun' => (int)$tahun
		];
	}

	// public function cetak_laporan($bulan = 4, $tahun = 2021)
	// {
	// 	// $bulan = $this->input->post('bulan');
	// 	// $tahun = $this->input->post('tahun');
		
	// 	#### nanti dilakukan pengecekan disini
	// 	$data = $this->load_tabel_laporan($bulan, $tahun, true);
	// 	$periode = bulan_indo($bulan).' '.$tahun;
	// 	$profil = $this->m_global->single_row('*', ['deleted_at' => null], 'm_profil');

	// 	// echo "<pre>";
	// 	// print_r ($data);
	// 	// echo "</pre>";
	// 	// exit;

	// 	$retval = [
	// 		'data' => $data,
	// 		'title' => 'Laporan Keuangan',
	// 		'periode' => 'Periode '.$periode,
	// 		'profil' => $profil,
	// 		'tahun' => $tahun,
	// 		'bulan' => $bulan
	// 	];

	// 	$this->load->view('view_lap_keuangan_pdf', $retval);
	// 	// $html = $this->load->view('view_lap_keuangan_pdf', $retval, true);
	//     // $filename = 'laporan_keuangan_'.$bulan.'_'.$tahun.'_'.time();
	//     // $this->lib_dompdf->generate($html, $filename, true, 'legal', 'potrait');
	// }

	public function cetak_laporan()
	{
		$bulan = $this->input->post('bulan');
		$tahun = $this->input->post('tahun');
		
		#### nanti dilakukan pengecekan disini
		$data = $this->load_tabel_laporan($bulan, $tahun, true);
		$periode = bulan_indo($bulan).' '.$tahun;
		$profil = $this->m_global->single_row('*', ['deleted_at' => null], 'm_profil');

		// echo "<pre>";
		// print_r ($data);
		// echo "</pre>";
		// exit;

		$retval = [
			'data' => $data,
			'title' => 'Laporan Keuangan',
			'periode' => 'Periode '.$periode,
			'profil' => $profil,
			'tahun' => $tahun,
			'bulan' => $bulan
		];

		// $this->load->view('view_lap_keuangan_pdf', $retval);
		$html = $this->load->view('view_lap_keuangan_pdf', $retval, true);
	    $filename = 'laporan_keuangan_'.$bulan.'_'.$tahun.'_'.time();
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
	///////////////////////////////////////////////////////////////////////

	public function list_penjualan($data)
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
						<th style="text-align:center">Kode</th>
						<th style="text-align:center">Jenis member</th>
						<th style="text-align:center">Member</th>
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
							<td>'.$value->nama_member.'</td>
							<td align="right">'.number_format($value->harga_satuan, 0 ,',','.').'</td>
						</tr>
					';
				}		
		$html .= '
				<tr>
					<td colspan="5" align="center"><strong>Jumlah Total</strong></td>
					<td align="right"><strong>'.number_format($total_harga, 0 ,',','.').'</strong></td>
				<tr>
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
						<th style="text-align:center">No.</th>
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
							<td>'.($key+1).'</td>
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
				<tr>
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
				<tr>
			</tbody>
		</table>';

		return $html;
	}
}
