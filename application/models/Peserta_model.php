<?php 
class Peserta_model extends CI_Model {

	public function cek_no_reg($jenis_kelamin)
	{
		$this->db->from('pesertaa');
		$this->db->where('jenis_kelamin', $jenis_kelamin);
		$this->db->order_by('id_peserta', 'DESC');
		$this->db->limit(1);

		return $this->db->get()->row();
	}
	public function insert($data)
	{ // *
		$this->db->insert('pesertaa', $data);
	}
	public function update($id_peserta, $data)
	{ // *
		$this->db->where('id_peserta', $id_peserta);
		$this->db->update('pesertaa', $data);
	}
	public function get_by_id($id_peserta)
	{ // *
		$this->db->from('pesertaa');
		$this->db->join('lomba', 'pesertaa.id_lomba=pesertaa.id_lomba');
		$this->db->where('pesertaa.id_peserta', $id_peserta);
		return $this->db->get()->row();
	}
	public function get_all()
	{ // *
		$this->db->from('pesertaa');
		$this->db->join('lomba', 'pesertaa.id_lomba=pesertaa.id_lomba');
		return $this->db->get()->result();
	}
	public function get_by_lomba($id_lomba)
	{ // *
		$this->db->from('pesertaa');
		$this->db->join('lomba', 'pesertaa.id_lomba=lomba.id_lomba');
		$this->db->where('pesertaa.id_lomba', $id_lomba);
		return $this->db->get()->result();
	}
	public function delete($id_peserta)
	{ // *
		$this->db->where('id_peserta', $id_peserta);
		$this->db->delete('pesertaa');
	}
	public function get_peserta_lomba($id_lomba, $jenis_kelamin)
	{ // *
		$this->db->from('pesertaa');
		$this->db->where('jenis_kelamin', $jenis_kelamin);
		$this->db->where('id_lomba', $id_lomba);
		return $this->db->get()->result();
	}
	public function get_peserta_putra($id_lomba)
	{ // *
		$this->db->from('pesertaa');
		$this->db->join('lomba', 'pesertaa.id_lomba=pesertaa.id_lomba');
		$this->db->where('pesertaa.jenis_kelamin', 'laki-laki');
		$this->db->where('pesertaa.id_lomba', $id_lomba);
		return $this->db->get()->result();
	}
	public function get_peserta_putri($id_lomba)
	{ // *
		$this->db->from('pesertaa');
		$this->db->join('lomba', 'pesertaa.id_lomba=pesertaa.id_lomba');
		$this->db->where('pesertaa.jenis_kelamin', 'perempuan');
		$this->db->where('pesertaa.id_lomba', $id_lomba);
		return $this->db->get()->result();
	}
}
?>