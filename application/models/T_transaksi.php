<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class T_transaksi extends CI_Model
{
	var $table = 't_transaksi';
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function get_by_id($id)
	{
		$this->db->from($this->table);
		$this->db->where('id',$id);
		$query = $this->db->get();

		return $query->row();
	}

	public function get_by_condition($where, $is_single = false)
	{
		$this->db->from($this->table);
		$this->db->where($where);
		$query = $this->db->get();
		if($is_single) {
			return $query->row();
		}else{
			return $query->result();
		}
	}

	public function save($data)
	{
		return $this->db->insert($this->table, $data);	
	}

	public function update($where, $data)
	{
		return $this->db->update($this->table, $data, $where);
	}

	public function softdelete_by_id($id)
	{
		$obj_date = new DateTime();
		$timestamp = $obj_date->format('Y-m-d H:i:s');
		$where = ['id' => $id];
		$data = ['deleted_at' => $timestamp];
		return $this->db->update($this->table, $data, $where);
	}

	public function get_invoice(){
		$obj_date = new DateTime();
		$tahun = $obj_date->format('y');
		$bulan = $obj_date->format('m');
		$hari = $obj_date->format('d');

		$q = $this->db->query("select MAX(RIGHT(kode,5)) as kode_max from $this->table");
		$kd = "";
		if($q->num_rows()>0){
			foreach($q->result() as $k){
				$tmp = ((int)$k->kode_max)+1;
				$kd = sprintf("%05s", $tmp);
			}
		}else{
			$kd = "00001";
		}
		return "INV-".$tahun.$bulan.$hari.$kd;
	}
	
	###################################### datatable penjualan
	protected $column_search_p = ['t_transaksi.kode','t_transaksi.created_at','jenis_member','m_user.nama','t_transaksi.harga_total', 't_transaksi.harga_bayar', 't_transaksi.harga_kembalian','status_kunci'];

	protected $column_search_tl = ['t_transaksi.created_at','m_jenis_trans.nama_jenis','m_user.nama','t_transaksi.harga_total', 't_transaksi.bulan_trans', 't_transaksi.tahun_trans'];

	protected $column_order_p = ['t_transaksi.kode','t_transaksi.created_at','jenis_member','m_user.nama','t_transaksi.harga_total', 't_transaksi.harga_bayar', 't_transaksi.harga_kembalian', 'status_kunci',null];

	protected $column_order_tl = ['t_transaksi.created_at','m_jenis_trans.nama_jenis','m_user.nama','t_transaksi.harga_total', 't_transaksi.bulan_trans', 't_transaksi.tahun_trans',null];

	protected $order_p = ['t_transaksi.kode' => 'desc']; 

	protected $order_t1 = ['t_transaksi.created_at' => 'desc']; 

	function get_datatable_penjualan($tgl_awal, $tgl_akhir, $jenis = '1')
	{
		$term = $_REQUEST['search']['value'];
		$this->_get_datatable_penjualan_query($tgl_awal, $tgl_akhir, $jenis, $term);
		if($_REQUEST['length'] != -1)
		$this->db->limit($_REQUEST['length'], $_REQUEST['start']);

		$query = $this->db->get();
		return $query->result();
	}

	private function _get_datatable_penjualan_query($tgl_awal, $tgl_akhir, $jenis, $term='')
	{
		$this->db->select('
			t_transaksi.*,
			m_jenis_trans.nama_jenis,
			m_member.kode_member,
			CASE WHEN t_transaksi.id_member is null THEN \'Reguler\' ELSE \'Member\' END as jenis_member,
			CASE WHEN t_transaksi.is_kunci = 1 THEN \'Terkunci\' ELSE \'Terbuka\' END as status_kunci,
			m_user.nama
		');
		
		$this->db->from('t_transaksi');
		$this->db->join('m_jenis_trans', 't_transaksi.id_jenis_trans=m_jenis_trans.id', 'left');
        $this->db->join('m_member', 't_transaksi.id_member=m_member.id', 'left');
		$this->db->join('m_user', 't_transaksi.id_user=m_user.id', 'left');
		$this->db->where('t_transaksi.created_at >=' ,$tgl_awal);
		$this->db->where('t_transaksi.created_at <=' ,$tgl_akhir);
		$this->db->where('t_transaksi.id_jenis_trans' ,$jenis);
		$this->db->where('t_transaksi.deleted_at is null');
		
		$i = 0;

		if($jenis == '1'){
			$arr_search = $this->column_search_p;
			$arr_column_order = $this->column_order_p;
			$arr_order = $this->order_p;
		}else{
			$arr_search = $this->column_search_tl;
			$arr_column_order = $this->column_order_tl;
			$arr_order = $this->order_t1;
		}
		
		// loop column 
		foreach ($arr_search as $item) 
		{
			// if datatable send POST for search
			if($_POST['search']['value']) 
			{
				// first loop
				if($i===0) 
				{
					// open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
					$this->db->group_start();
					$this->db->like($item, $_POST['search']['value']);
				}
				else
				{
					if($item == 'jenis_member') {
						/**
						 * param both untuk wildcard pada awal dan akhir kata
						 * param false untuk disable escaping (karena pake subquery)
						 */
						$this->db->or_like('(CASE WHEN t_transaksi.id_member is null THEN \'Reguler\' ELSE \'Member\' END)', $_POST['search']['value'],'both',false);
					}
					elseif($item == 'status_kunci') {
						/**
						 * param both untuk wildcard pada awal dan akhir kata
						 * param false untuk disable escaping (karena pake subquery)
						 */
						$this->db->or_like('(CASE WHEN t_transaksi.is_kunci = 1 THEN \'Terkunci\' ELSE \'Terbuka\' END)', $_POST['search']['value'],'both',false);
					}
					elseif($item == 't_transaksi.bulan_trans') {
						/**
						 * param both untuk wildcard pada awal dan akhir kata
						 * param false untuk disable escaping (karena pake subquery)
						 */
						$this->db->or_like('(
							CASE 
								WHEN t_transaksi.bulan_trans = 1 THEN \'Januari\'
								WHEN t_transaksi.bulan_trans = 2 THEN \'Februari\'
								WHEN t_transaksi.bulan_trans = 3 THEN \'Maret\'
								WHEN t_transaksi.bulan_trans = 4 THEN \'April\'
								WHEN t_transaksi.bulan_trans = 5 THEN \'Mei\'
								WHEN t_transaksi.bulan_trans = 6 THEN \'Juni\'
								WHEN t_transaksi.bulan_trans = 7 THEN \'Juli\'
								WHEN t_transaksi.bulan_trans = 8 THEN \'Agustus\'
								WHEN t_transaksi.bulan_trans = 9 THEN \'September\'
								WHEN t_transaksi.bulan_trans = 10 THEN \'Oktober\'
								WHEN t_transaksi.bulan_trans = 11 THEN \'November\'
								WHEN t_transaksi.bulan_trans = 12 THEN \'Desember\'
							END
						)', $_POST['search']['value'],'both',false);
					}
					else{
						$this->db->or_like($item, $_POST['search']['value']);
					}
				}
				//last loop
				if(count($arr_search) - 1 == $i) 
					$this->db->group_end(); //close bracket
			}
			$i++;
		}

		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($arr_column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($arr_order))
		{
			$order = $arr_order;
            $this->db->order_by(key($order), $order[key($order)]);
		}
	}

	function count_filtered_penjualan($tgl_awal, $tgl_akhir, $jenis='1')
	{
		$this->_get_datatable_penjualan_query($tgl_awal, $tgl_akhir, $jenis);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all_penjualan($tgl_awal, $tgl_akhir,  $jenis='1')
	{
		$this->db->from($this->table);
		$this->db->where($this->table.'.created_at >=' ,$tgl_awal);
		$this->db->where($this->table.'.created_at <=' ,$tgl_akhir);
		$this->db->where($this->table.'.id_jenis_trans' ,$jenis);
		$this->db->where($this->table.'.deleted_at is null');
		return $this->db->count_all_results();
	}
	###################################### end datatable penjualan
	

	public function get_detail_penjualan($id)
	{
		$this->db->select('
			t_transaksi.*,
			t_transaksi_det.id_item_trans,
			t_transaksi_det.harga_satuan,
			t_transaksi_det.is_disc_jual,
			t_transaksi_det.ket_disc_jual,
			m_item_trans.nama as nama_item,
            m_member.kode_member,
			m_member.nama as nama_member,
			CASE WHEN t_transaksi.id_member is null THEN \'Reguler\' ELSE \'Member\' END as jenis_member,
            m_user.nama
		');

		$this->db->from('t_transaksi');
		$this->db->join('t_transaksi_det', 't_transaksi.id = t_transaksi_det.id_transaksi', 'left');
		$this->db->join('m_item_trans', 't_transaksi_det.id_item_trans = m_item_trans.id', 'left');
        $this->db->join('m_member', 't_transaksi.id_member = m_member.id', 'left');
		$this->db->join('m_user', 't_transaksi.id_user = m_user.id', 'left');
		$this->db->where('t_transaksi.id', $id);
		$query = $this->db->get();

		if($query) {
			return $query->result();
		}else{
			return false;
		}
	}

	public function get_detail_transaksi($id)
	{
		$this->db->select('
			t_transaksi.*,
			t_transaksi_det.id_item_trans,
			t_transaksi_det.harga_satuan,
			t_transaksi_det.qty,
			m_item_trans.nama as nama_item,
            m_supplier.nama_supplier,
			m_user.nama as nama_user
		');

		$this->db->from('t_transaksi');
		$this->db->join('t_transaksi_det', 't_transaksi.id = t_transaksi_det.id_transaksi', 'left');
		$this->db->join('m_item_trans', 't_transaksi_det.id_item_trans = m_item_trans.id', 'left');
        $this->db->join('m_supplier', 't_transaksi.id_supplier = m_supplier.id', 'left');
		$this->db->join('m_user', 't_transaksi.id_user = m_user.id', 'left');
		$this->db->where('t_transaksi.id', $id);
		$query = $this->db->get();

		if($query) {
			return $query->result();
		}else{
			return false;
		}
	}

	public function get_laporan_transaksi($id_jenis, $date_awal, $date_akhir)
	{
		$this->db->select('
			t_transaksi.*,
			t_transaksi_det.harga_satuan,
			t_transaksi_det.qty,
			m_item_trans.nama,
			m_jenis_trans.nama_jenis,
			m_jenis_trans.kode_jenis,
			m_member.kode_member,
			m_member.nama as nama_member,
			m_supplier.nama_supplier'
		);

		$this->db->from('t_transaksi');
		$this->db->join('t_transaksi_det', 't_transaksi.id = t_transaksi_det.id_transaksi', 'left');
		$this->db->join('m_member', 't_transaksi.id_member = m_member.id', 'left');
		$this->db->join('m_item_trans', 't_transaksi_det.id_item_trans = m_item_trans.id', 'left');
		$this->db->join('m_jenis_trans', 't_transaksi.id_jenis_trans = m_jenis_trans.id', 'left');
		$this->db->join('m_supplier', 't_transaksi.id_supplier = m_supplier.id', 'left');
		$this->db->where('t_transaksi.deleted_at', null);
		$this->db->where('t_transaksi.id_jenis_trans', $id_jenis);
		$this->db->where('t_transaksi.tgl_trans >=', $date_awal);
		$this->db->where('t_transaksi.tgl_trans <=', $date_akhir);
		$this->db->order_by('t_transaksi.tgl_trans', 'asc');
		
		$query = $this->db->get();
		if($query) {
			return $query->result();
		}else{
			return false;
		}
	}

	public function get_laporan_keuangan($bulan, $tahun)
	{
		$this->db->select('
			t_transaksi.tgl_trans,
			t_transaksi.id_jenis_trans,
			sum(t_transaksi.harga_total) as total_harga,
			t_transaksi.id_jenis_trans,
			m_jenis_trans.nama_jenis,
			m_jenis_trans.kode_jenis,
			m_jenis_trans.cashflow,
		');

		$this->db->from('t_transaksi');
		$this->db->join('m_jenis_trans', 't_transaksi.id_jenis_trans = m_jenis_trans.id', 'left');
		$this->db->where('t_transaksi.deleted_at', null);
		$this->db->where('t_transaksi.bulan_trans', $bulan);
		$this->db->where('t_transaksi.tahun_trans', $tahun);
		$this->db->group_by('t_transaksi.tgl_trans');
		$this->db->group_by('t_transaksi.id_jenis_trans');
		$this->db->order_by('t_transaksi.tgl_trans', 'asc');
		$this->db->order_by('m_jenis_trans.cashflow', 'asc');
		
		$query = $this->db->get();
		if($query) {
			return $query->result();
		}else{
			return false;
		}
	}

	public function cari_saldo_by_hitung($bulan, $tahun)
	{
		$objDate = new DateTime($tahun.'-'.$bulan.'-01');
		$tgl =  $objDate->format('Y-m-d');

		$this->db->select("
			SUM(CASE WHEN b.cashflow = 'in' THEN a.harga_total END ) as penerimaan,
			SUM(CASE WHEN b.cashflow = 'out' THEN a.harga_total END ) as pengeluaran,
			(SUM(CASE WHEN b.cashflow = 'in' THEN a.harga_total END ) - SUM(CASE WHEN b.cashflow = 'out' THEN a.harga_total END )) as saldo
		");

		$this->db->from('t_transaksi a');
		$this->db->join('m_jenis_trans b', 'a.id_jenis_trans = b.id', 'left');
		$this->db->where('a.deleted_at', null);
		$this->db->where('a.tgl_trans <', $tgl);

		$query = $this->db->get();
		if($query) {
			return $query->row();
		}else{
			return false;
		}
	}
}