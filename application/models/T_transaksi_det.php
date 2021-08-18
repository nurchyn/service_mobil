<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class T_transaksi_det extends CI_Model
{
	var $table = 't_transaksi_det';
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

	public function softdelete_by_trans($id_trans)
	{
		$obj_date = new DateTime();
		$timestamp = $obj_date->format('Y-m-d H:i:s');
		$where = ['id_transaksi' => $id_trans];
		$data = ['deleted_at' => $timestamp];
		return $this->db->update($this->table, $data, $where);
	}

	// public function get_detail_user($id_user)
	// {
	// 	$this->db->select('*');
	// 	$this->db->from('m_user');
	// 	$this->db->where('id', $id_user);

    //     $query = $this->db->get();

    //     if ($query->num_rows() > 0) {
    //         return $query->result();
    //     }
	// }
}