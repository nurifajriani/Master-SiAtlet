<?php 
class NilaiKriteria_model extends CI_Model {

	public function insert($data)
	{ // *
		$this->db->insert('nilai_kriteria', $data);
	}
	public function update($id_nilai_kriteria, $data)
	{
		$this->db->where('id_nilai_kriteria', $id_nilai_kriteria);
		$this->db->update('nilai_kriteria', $data);
	}
	public function get_by_id($id_nilai_kriteria)
	{
		$this->db->from('nilai_kriteria');
		$this->db->join('kriteria', 'nilai_kriteria.id_kriteria=kriteria.id_kriteria');
		$this->db->where('nilai_kriteria.id_nilai_kriteria', $id_nilai_kriteria);
		return $this->db->get()->row();
	}
	public function get_by_id_kriteria($id_kriteria)
	{
		$this->db->from('nilai_kriteria');
		$this->db->join('kriteria', 'nilai_kriteria.id_kriteria=kriteria.id_kriteria');
		$this->db->join('lomba', 'kriteria.id_lomba=lomba.id_lomba');
		$this->db->where('nilai_kriteria.id_kriteria', $id_kriteria);
		return $this->db->get()->result();
	}
	public function cek_nilai_kriteria($id_kriteria, $nilai, $jenis_kelamin)
	{
		$this->db->from('nilai_kriteria');
		$this->db->join('kriteria', 'nilai_kriteria.id_kriteria=kriteria.id_kriteria');
		$this->db->join('lomba', 'kriteria.id_lomba=lomba.id_lomba');
		$this->db->where('nilai_kriteria.id_kriteria', $id_kriteria);
		$this->db->where('nilai_kriteria.nilai', $nilai);
		$this->db->where('nilai_kriteria.jenis_kelamin', $jenis_kelamin);
		return $this->db->get()->row();
	}
	public function get_by_user($id_user)
	{
		$this->db->from('nilai_kriteria');
		$this->db->join('lomba', 'nilai_kriteria.id_lomba=lomba.id_lomba');
		$this->db->where('lomba.id_user', $id_user);
		return $this->db->get()->result();
	}
	public function get_all()
	{
		$this->db->from('nilai_kriteria');
		$this->db->join('kriteria', 'nilai_kriteria.id_kriteria=kriteria.id_kriteria');
		return $this->db->get()->result();
	}
	public function delete($id_nilai_kriteria)
	{
		$this->db->where('id_nilai_kriteria', $id_nilai_kriteria);
		$this->db->delete('nilai_kriteria');
	}
	public function cekNilaiPutra($id_kriteria, $id_lomba)
	{
		$this->db->from('nilai_kriteria');
		$this->db->join('kriteria', 'nilai_kriteria.id_kriteria=kriteria.id_kriteria');
		$this->db->where('nilai_kriteria.id_kriteria', $id_kriteria);
		$this->db->where('kriteria.id_lomba', $id_lomba);
		$this->db->where('nilai_kriteria.jenis_kelamin', 'laki-laki');
		return $this->db->get()->result();
	}
	public function cekNilaiPutri($id_kriteria, $id_lomba)
	{
		$this->db->from('nilai_kriteria');
		$this->db->join('kriteria', 'nilai_kriteria.id_kriteria=kriteria.id_kriteria');
		$this->db->where('nilai_kriteria.id_kriteria', $id_kriteria);
		$this->db->where('kriteria.id_lomba', $id_lomba);
		$this->db->where('nilai_kriteria.jenis_kelamin', 'perempuan');
		return $this->db->get()->result();
	}
}
?>