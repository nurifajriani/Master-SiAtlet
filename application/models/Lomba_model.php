<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Lomba_model extends CI_Model {

	public function insert($data) // insert data lomba
	{ // *
		$this->db->insert('lomba', $data);
	}	
	public function update($id_lomba, $data)
	{ // *
		$this->db->where('id_lomba', $id_lomba);
		$this->db->update('lomba', $data);
	}
	public function delete($id_lomba)
	{
		$this->db->where('id_lomba', $id_lomba);
		$this->db->delete('lomba');
	}
	public function get_by_id($id_lomba)
	{ // *
		$this->db->from('lomba');
		$this->db->join('user', 'lomba.id_pelatih=user.id_user');
		$this->db->where('id_lomba', $id_lomba);
		return $this->db->get()->row();
	}
	public function get_by_user($id_lomba, $id_user)
	{
		$this->db->from('lomba');
		$this->db->where('id_lomba', $id_lomba);
		$this->db->where('id_user', $id_user);
		return $this->db->get()->result();
	}
	public function get_all_by_pelatih($id_pelatih)
	{ // *
		$this->db->from('lomba');
		$this->db->join('user', 'lomba.id_pelatih=user.id_user');
		$this->db->where('lomba.id_pelatih', $id_pelatih);
		return $this->db->get()->result();
	}
	public function get_all()
	{ // *
		$this->db->from('lomba');
		$this->db->join('user', 'lomba.id_pelatih=user.id_user');
		return $this->db->get()->result();
	}
}
?>