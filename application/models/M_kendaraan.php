<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class M_kendaraan extends CI_Model
{
	var $table = 't_kendaraan_masuk';
	var $column_search = ['t_kendaraan_masuk.invoice','m_customer.nama_customer',];
	
	var $column_order = [
		null, 
		't_kendaraan_masuk.invoice',
		't_kendaraan_masuk.nama_customer',
        't_kendaraan_masuk.nama_kendaraan',
        't_kendaraan_masuk.nopol',
        't_kendaraan_masuk.merk',
        't_kendaraan_masuk.warna',
        't_kendaraan_masuk.keluhan',
		null
	];

	var $order = ['t_kendaraan_masuk.id' => 'desc']; 

	public function __construct()
	{
		parent::__construct();
		//alternative load library from config
		$this->load->database();
	}

	private function _get_datatables_query($term='', $status=null)
	{
		$this->db->select('
			t_kendaraan_masuk.*,
            m_customer.nama_customer,
			m_customer.alamat,
			m_customer.telp,
			m_kendaraan.nama_kendaraan,
			m_kendaraan.nopol,
			m_kendaraan.warna,
			m_merek.nama_merek,
			m_merek.chasis,
			m_mekanik.nama_mekanik
		');

		$this->db->from('t_kendaraan_masuk');	
        $this->db->join('m_customer', 'm_customer.id=t_kendaraan_masuk.id_customer', 'left');
		$this->db->join('m_kendaraan', 'm_kendaraan.id=t_kendaraan_masuk.id_kendaraan', 'left');
		$this->db->join('m_merek', 'm_merek.id=m_kendaraan.merek');
		$this->db->join('m_mekanik', 'm_mekanik.id=t_kendaraan_masuk.id_mekanik', 'left');
		$this->db->where('t_kendaraan_masuk.deleted_at is null');
		if ($status) {
			$this->db->where('t_kendaraan_masuk.status', 2);
		} else {
			$this->db->where('(t_kendaraan_masuk.status !=', 2);
			$this->db->or_where("t_kendaraan_masuk.status IS NULL)", NULL, FALSE);
		}
		
        $this->db->order_by('t_kendaraan_masuk.id', 'desc');
		
		$i = 0;
		// loop column 
		foreach ($this->column_search as $item) 
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
					$this->db->or_like($item, $_POST['search']['value']);
				}
				//last loop
				if(count($this->column_search) - 1 == $i) 
					$this->db->group_end(); //close bracket
			}
			$i++;
		}

		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order))
		{
			$order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatable_user($status=null)
	{
		$term = $_REQUEST['search']['value'];
		$this->_get_datatables_query($term, $status);
		if($_REQUEST['length'] != -1)
		$this->db->limit($_REQUEST['length'], $_REQUEST['start']);

		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered()
	{
		$this->_get_datatables_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all()
	{
		$this->db->from($this->table);
		return $this->db->count_all_results();
	}

	public function get_detail_user($id_user)
	{
		$this->db->select('*');
		$this->db->from('m_user');
		$this->db->where('id', $id_user);

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->result();
        }
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

	public function konfirmasi_selesai($id)
	{
		$obj_date = new DateTime();
		// $timestamp = $obj_date->format('Y-m-d H:i:s');
		$where = ['id' => $id];
		$data = ['status' => 2];
		return $this->db->update($this->table, $data, $where);
	}

	//dibutuhkan di contoller login untuk ambil data user
	function login($data){
		return $this->db->select('*')
			->where('username',$data['data_user'])
			->where('password',$data['data_password'])
			->where('status', 1 )
			->get($this->table)->row();
	}

	//dibutuhkan di contoller login untuk set last login
	function set_lastlogin($id){
		$this->db->where('id',$id);
		$this->db->update(
			$this->table, 
			['last_login'=>date('Y-m-d H:i:s')]
		);			
	}

	function get_kode_user(){
            $q = $this->db->query("select MAX(RIGHT(kode_user,5)) as kode_max from m_user");
            $kd = "";
            if($q->num_rows()>0){
                foreach($q->result() as $k){
                    $tmp = ((int)$k->kode_max)+1;
                    $kd = sprintf("%05s", $tmp);
                }
            }else{
                $kd = "00001";
            }
            return "USR-".$kd;
	}
	
	public function get_max_id_member()
	{
		$q = $this->db->query("SELECT MAX(id) as kode_max from m_member");
		$kd = "";
		if($q->num_rows()>0){
			$kd = $q->row();
			return (int)$kd->kode_max + 1;
		}else{
			return '1';
		} 
	}

	public function get_id_pegawai_by_name($nama)
	{
		$this->db->select('id');
		$this->db->from('m_pegawai');
		$this->db->where('LCASE(nama)', $nama);
		$q = $this->db->get();
		if ($q) {
			return $q->row();
		}else{
			return false;
		}
	}

	public function get_id_role_by_name($nama)
	{
		$this->db->select('id');
		$this->db->from('m_role');
		$this->db->where('LCASE(nama)', $nama);
		$q = $this->db->get();
		if ($q) {
			return $q->row();
		}else{
			return false;
		}
	}

	public function trun_master_user()
	{
		$this->db->query("truncate table m_user");
	}

	function get_onderdil($id)
	{
		$query = "
				SELECT 
					d.*,
					b.nama_barang,
					b.harga_jual
				FROM t_kendaraan_masuk_detail d
				LEFT JOIN t_kendaraan_masuk k ON k.id = d.id_t_kendaraan_masuk
				LEFT JOIN m_barang b ON b.id = d.id_onderdil
				WHERE d.id_t_kendaraan_masuk = $id
				ORDER BY d.id DESC
			";
		return $this->db->query($query);
	}

	function get_pekerjaan($id)
	{
		$query = "
				SELECT 
					d.*,
					b.type_wo,
					b.nama_pekerjaan
				FROM t_kendaraan_pekerjaan_detail d
				LEFT JOIN m_pekerjaan b ON b.id = d.id_pekerjaan
				WHERE d.id_t_kendaraan_masuk = $id
				ORDER BY d.id DESC
			";
		return $this->db->query($query);
	}

	public function saveOnderdil($data)
	{
		return $this->db->insert('t_kendaraan_masuk_detail', $data);	
	}

	public function savePekerjaan($data)
	{
		return $this->db->insert('t_kendaraan_pekerjaan_detail', $data);	
	}

	public function updateOnderdil($where, $data)
	{
		return $this->db->update('t_kendaraan_masuk_detail', $data, $where);
	}
}