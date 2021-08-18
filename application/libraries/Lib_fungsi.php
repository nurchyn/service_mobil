<?php
class Lib_fungsi extends CI_Controller {
    protected $_ci;
    
    function __construct(){
        $this->_ci = &get_instance();
    }
    
	/**
	 * $aksi : AKSI LOG (STRING)
	 * $old_data : DATA SEBELUMNYA (JSON)
	 * $new_data : DATA BARU (JSON)
	 */
	function catat_log_aktifitas($aksi=null, $old_data = null, $new_data=null) {
		$obj_date = new DateTime();
		$timestamp = $obj_date->format('Y-m-d H:i:s');
		$id_user = $this->_ci->session->userdata('id_user');
		$menu = $this->_ci->uri->segment(1);
		$q_menu = $this->_ci->db->query("select * from m_menu where link = '$menu'")->row();
		
		if($q_menu) {
			$id_menu = $q_menu->id;
		}else{
			$id_menu = null;
		}

		$data = [
			'id' => gen_uuid(),
			'id_m_menu' => $id_menu,
			'id_m_user' => $id_user,
			'aksi' => $aksi,
			'old_data' => $old_data,
			'new_data' => $new_data,
			'created_at' => $timestamp
		];
		$this->_ci->db->insert('t_log_aktivitas', $data);
	}


	###################
	public function insert_counter($id_member, $counter_mobil, $counter_motor, $is_add_cnt_mobil=false, $is_add_cnt_motor=false)
	{
		$retval = [];
		$obj_date = new DateTime();
		$timestamp = $obj_date->format('Y-m-d H:i:s');

		if ($is_add_cnt_mobil) {
			if ($counter_mobil == 9) {
				## set deleted_at pada id_member	
				$update = $this->_ci->db->update('t_log_counter_member', ['deleted_at' => $timestamp], ['id_member' => $id_member, 'id_jenis_counter' => '1']);
				if ($update) {
					$retval[] = TRUE;
				} else {
					$retval[] = FALSE;
				}
			}else{
				$data = [
					'id_member' => $id_member,
					'id_jenis_counter' => 1,
					'created_at' => $timestamp,
				];

				$ins = $this->_ci->db->insert('t_log_counter_member', $data);
				if ($ins) {
					$retval[] = TRUE;
				} else {
					$retval[] = FALSE;
				}
			}
		}

		if ($is_add_cnt_motor) {
			if ($counter_motor == 9) {
				## set deleted_at pada id_member
				$update2 = $this->_ci->db->update('t_log_counter_member', ['deleted_at' => $timestamp], ['id_member' => $id_member, 'id_jenis_counter' => '2']);
				if ($update2) {
					$retval[] = TRUE;
				} else {
					$retval[] = FALSE;
				}
			}else{
				$data = [
					'id_member' => $id_member,
					'id_jenis_counter' => 2,
					'created_at' => $timestamp,
				];

				$ins = $this->_ci->db->insert('t_log_counter_member', $data);
				if ($ins) {
					$retval[] = TRUE;
				} else {
					$retval[] = FALSE;
				}
			}
		}

		if (in_array(false, $retval)) {
			return false;
		}else{
			return true;
		}
		
	}

	function cek_counter($id_member) {
		$query = $this->_ci->db->query("SELECT count(*) AS total_count, t_log_counter_member.id_jenis_counter, m_jenis_counter.jenis_counter FROM t_log_counter_member left join m_jenis_counter on t_log_counter_member.id_jenis_counter = m_jenis_counter.id WHERE t_log_counter_member.id_member = '$id_member' AND t_log_counter_member.deleted_at IS NULL GROUP BY t_log_counter_member.id_jenis_counter")->result();
		if($query) {
			$counter = $query;
		}else{
			$counter = null;
		}

		return $counter;		
	}
}