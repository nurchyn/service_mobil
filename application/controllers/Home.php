<?php
//defined('BASEPATH ') OR exit('No direct script access allowed');

class Home extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('m_user');
		$this->load->model('m_global');

		if($this->session->userdata('logged_in') === false) {
			return redirect('login');
		}
	}

	public function index()
	{	
		$tahun = date('Y');
		$bulan = date('m');
		$hari = date('d');
		$id_user = $this->session->userdata('id_user');
		$data_user = $this->m_user->get_detail_user($id_user);

		
		$data_dashboard = [];
		
		/**
		 * data passing ke halaman view content
		 */
		// $data_where = array('id_jenis_trans'=>1, 'deleted_at' => NULL);
		$data = array(
			'title' => 'Dashboard Aplikasi',
			'data_user' => $data_user,
			// 'penjualan' => $this->m_global->getSelectedData('m_item_trans', $data_where)->result(),
		);

		/**
		 * content data untuk template
		 * param (css : link css pada direktori assets/css_module)
		 * param (modal : modal komponen pada modules/nama_modul/views/nama_modal)
		 * param (js : link js pada direktori assets/js_module)
		 */
		$content = [
			'css' 	=> null,
			'modal' => null,
			'js'	=> 'dashboard.js',
			'view'	=> 'dashboard/view_dashboard'
		];

		$this->template_view->load_view($content, $data);
	}


	public function oops()
	{	
		$this->load->view('login/view_404');
	}

	public function bulan_indo($bulan)
	{
		$arr_bulan =  [
			1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
		];

		return $arr_bulan[(int) $bulan];
	}

	public function monitoring()
	{
		$tahun = $this->input->post('tahun');
		$id_item = $this->input->post('jenis_penjualan');
		$data_where = array('id'=>$id_item);
		$jenis_penjualan = $this->m_global->getSelectedData('m_item_trans', $data_where)->row();
		$result         = $this->m_global->monitoring_penjualan($id_item, $tahun)->result_array();
		// var_dump($result); die();
		$data_mentah    = array();
		foreach ($result as $key) {
            $data_mentah[$key['bulan']] = $key['jumlah'];
        }

		$user   = array($id_item);
		$data['judul'] = "Grafik Penjualan per Tahun ".$tahun;
		$data['label'] = ["Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember"];
		// $result        = $this->model_app->eksekusi(jumlah_disposisi_user::sql_tahun($usercomma, $tahun2))->result_array();
		$data_mentah   = array();
		foreach ($result as $key) {
		  $data_mentah[$key['id_item_trans']][$key['bulan']] = $key['jumlah'];
		}
		$label          = $data['label'];
		$data['data']   = array();
		$data_grafik    = array();

		for ($i=0; $i < count($label); $i++) {
		  if($i == 0) $no = '1';
		  elseif($i == 1) $no = '2';
		  elseif($i == 2) $no = '3';
		  elseif($i == 3) $no = '4';
		  elseif($i == 4) $no = '5';
		  elseif($i == 5) $no = '6';
		  elseif($i == 6) $no = '7';
		  elseif($i == 7) $no = '8';
		  elseif($i == 8) $no = '9';
		  elseif($i == 9) $no = '10';
		  elseif($i == 10) $no = '11';
		  elseif($i == 11) $no = '12';
			for ($j=0;$j<count($user);$j++) {
			  if($i==0) {
				$data_grafik[$j] = array();
				$data_grafik[$j]['data'] = array();
				// $aktif = "(Aktif)";
				// if($data_user[$user[$j]]['status'] <> 1) {
				//   $aktif = "(Tidak Aktif)";
				// }
				$data_grafik[$j]['label']       = $jenis_penjualan->nama;
				$data_grafik[$j]['backgroundColor'] = "#".$this->random_color();
				$data_grafik[$j]['fill']        = true;
				
			  }
			
			if(isset($data_mentah[$user[$j]][$no])) {
				$data_grafik[$j]['data'][] = $data_mentah[$user[$j]][$no];
			  }
			  else {
				$data_grafik[$j]['data'][] = 0;
			  }
			}
		}
		$data['datasets'] = $data_grafik;
		$data['status'] = true;
		echo json_encode($data);
	}

	function random_color(){
		mt_srand((double)microtime()*1000000);
		$c = '';
		while(strlen($c)<6){
		  $c .= sprintf("%02X", mt_rand(0, 255));
		}
		return $c;
	}
  

}
