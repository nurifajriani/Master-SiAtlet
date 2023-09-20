<?php 

class Kriteria_model extends CI_Model {

	public function insert($data)
	{ // *
		$this->db->insert('kriteria', $data);
	}
	public function get_by_id($id_kriteria)
	{ // *
		$this->db->from('kriteria');
		$this->db->join('lomba', 'kriteria.id_lomba=lomba.id_lomba');
		$this->db->where('id_kriteria', $id_kriteria);
		return $this->db->get()->row();
	}
	public function update($id_kriteria, $data)
	{ // *
		$this->db->where('id_kriteria', $id_kriteria);
		$this->db->update('kriteria', $data);
	}
	public function get_all_by_user($id_user)
	{
		$this->db->from('kriteria');
		$this->db->join('lomba', 'kriteria.id_lomba=lomba.id_lomba');
		$this->db->where('lomba.id_user', $id_user);
		return $this->db->get()->result();
	}
	public function get_all()
	{ // *
		$this->db->from('kriteria');
		$this->db->join('lomba', 'kriteria.id_lomba=lomba.id_lomba');
		return $this->db->get()->result();
	}
	public function delete($id_kriteria)
	{ // *
		$this->db->where('id_kriteria', $id_kriteria);
		$this->db->delete('kriteria');
	}
	public function get_by_id_lomba($id_kriteria, $id_lomba)
	{ 
		$this->db->from('kriteria');
		$this->db->join('lomba', 'kriteria.id_lomba=lomba.id_lomba');
		$this->db->where('kriteria.id_kriteria', $id_kriteria);
		$this->db->where('kriteria.id_lomba', $id_lomba);
		return $this->db->get()->row();
	}
	public function kriteria_by_id_lomba($id_lomba)
	{ 
		$this->db->from('kriteria');
		$this->db->join('lomba', 'kriteria.id_lomba=lomba.id_lomba');
		$this->db->where('kriteria.id_lomba', $id_lomba);
		return $this->db->get()->result();
	}
	public function kriteria_lomba($id_lomba)
	{ // *
		$this->db->from('kriteria');
		$this->db->join('lomba', 'kriteria.id_lomba=lomba.id_lomba');
		$this->db->where('kriteria.id_lomba', $id_lomba);
		return $this->db->get()->result();
	}

}
?>