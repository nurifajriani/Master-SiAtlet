<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

	public function insert($data) // * menambahkan data user baru
	{
		$this->db->insert('user', $data);
	}
	public function update($id_user, $data) // mengupdate data user
	{ // *
		$this->db->where('id_user', $id_user);
		$this->db->update('user', $data);
	}
	public function delete($id_user) // menghapus data user by id_user
	{ // *
		$this->db->where('id_user', $id_user);
		$this->db->delete('user');
	}
	public function get_by_id($id_user) // get data user by id_user
	{ // *
		$this->db->from('user');
		$this->db->where('id_user', $id_user);
		return $this->db->get()->row();
	}
	public function cek_level_username($username, $level)
	{ // *
		$this->db->from('user');
		$this->db->where('username', $username);
		$this->db->where('level', $level);
		return $this->db->get()->row();
	}
	public function cek_level_iduser($id_user, $level)
	{ // *
		$this->db->from('user');
		$this->db->where('id_user', $id_user);
		$this->db->where('level', $level);
		return $this->db->get()->row();
	}
	public function reset_password($data, $username, $level)
	{ // *
		$this->db->where('username', $username);
		$this->db->where('level', $level);
		$this->db->update('user', $data);
	}
	public function get_all()
	{ // *
		$this->db->from('user');
		return $this->db->get()->result();
	}
	public function cek_level($level)
	{ // *
		$this->db->from('user');
		$this->db->where('level', $level);
		return $this->db->get()->result();
	}

	// LOGIN USER
	public function login($username, $password, $level)
	{ // *
		$this->db->from('user');
		$this->db->where('username', $username);
		$this->db->where('password_decrypt', $password);
		$this->db->where('level', $level);
		return $this->db->get()->row();
	}
	public function cek_token($id_user, $username, $password)
	{
		$this->db->from('user');
		$this->db->where('id_user', $id_user);
		$this->db->where('username', $username);
		$this->db->where('password', $password);
		return $this->db->get()->row();
	}
	public function cek_username($username)
	{
		$this->db->from('user');
		$this->db->where('username', $username);
		return $this->db->get()->row();
	}
}
?>