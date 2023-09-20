<?php 

class BobotKriteria_model extends CI_Model {

	public function insert($data)
	{ // *
		$this->db->insert('bobot_kriteria', $data);
	}
	public function get_by_id($id_bobot)
	{ // *
		$this->db->from('bobot_kriteria');
		$this->db->join('lomba', 'bobot_kriteria.id_lomba=lomba.id_lomba');
		$this->db->join('kriteria', 'bobot_kriteria.id_kriteria=kriteria.id_kriteria');
		$this->db->where('bobot_kriteria.id_bobot', $id_bobot);
		return $this->db->get()->row();
	}
	public function update($id_bobot, $data)
	{ // *
		$this->db->where('id_bobot', $id_bobot);
		$this->db->update('bobot_kriteria', $data);
	}
	public function get_all()
	{ // *
		$this->db->from('bobot_kriteria');
		$this->db->join('lomba', 'bobot_kriteria.id_lomba=lomba.id_lomba');
		$this->db->join('kriteria', 'bobot_kriteria.id_kriteria=kriteria.id_kriteria');
		return $this->db->get()->result();
	}
	public function get_by_lomba($id_lomba)
	{ // *
		$this->db->from('bobot_kriteria');
		$this->db->join('lomba', 'bobot_kriteria.id_lomba=lomba.id_lomba');
		$this->db->join('kriteria', 'bobot_kriteria.id_kriteria=kriteria.id_kriteria');
		$this->db->where('bobot_kriteria.id_lomba', $id_lomba);
		return $this->db->get()->result();
	}
	public function get_by_user($id_user)
	{
		$this->db->from('bobot_kriteria a');
		$this->db->join('lomba b', 'a.id_lomba=b.id_lomba');
		$this->db->where('b.id_user', $id_user);
		return $this->db->get()->result();
	}
	public function delete($id_bobot)
	{ // *
		$this->db->where('id_bobot', $id_bobot);
		$this->db->delete('bobot_kriteria');
	}
	public function get_by_lomba_kriteria($id_lomba, $id_kriteria)
	{
		$this->db->from('bobot_kriteria');
		$this->db->where('id_lomba', $id_lomba);
		$this->db->where('id_kriteria', $id_kriteria);
		return $this->db->get()->row();
	}
	public function sumBobot()
	{ // *
		$this->db->select('sum(bobot) as jml');
		$this->db->from('bobot_kriteria');
		return $this->db->get()->row();
	}
	public function get_by_kriteria($id_kriteria)
	{ // *
		$this->db->from('bobot_kriteria');
		$this->db->where('id_kriteria', $id_kriteria);
		return $this->db->get()->row();
	}
	public function cek_bobot_kriteria($id_kriteria, $bobot)
	{ // *
		$this->db->from('bobot_kriteria');
		$this->db->where('id_kriteria', $id_kriteria);
		$this->db->where('bobot', $bobot);
		return $this->db->get()->row();
	}
}
?>