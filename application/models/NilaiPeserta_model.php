<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class NilaiPeserta_model extends CI_Model {

	public function insert($data)
	{
		$this->db->insert('nilai_peserta', $data);
	}
	public function get_by_id($id_nilai_peserta)
	{
		$this->db->from('nilai_peserta');
		$this->db->join('kriteria', 'nilai_peserta.id_kriteria=kriteria.id_kriteria');
		$this->db->join('pesertaa', 'nilai_peserta.id_peserta=pesertaa.id_peserta');
		$this->db->join('lomba', 'nilai_peserta.id_lomba=lomba.id_lomba');
		$this->db->join('nilai_kriteria', 'nilai_peserta.id_nilai_kriteria=nilai_kriteria.id_nilai_kriteria');
		$this->db->where('nilai_peserta.id_nilai_peserta', $id_nilai_peserta);
		return $this->db->get()->row();
	}
	public function get_by_id_lomba($id_lomba)
	{
		$this->db->from('nilai_peserta');
		$this->db->join('kriteria', 'nilai_peserta.id_kriteria=kriteria.id_kriteria');
		$this->db->join('pesertaa', 'nilai_peserta.id_peserta=pesertaa.id_peserta');
		$this->db->join('lomba', 'nilai_peserta.id_lomba=lomba.id_lomba');
		$this->db->join('nilai_kriteria', 'nilai_peserta.id_nilai_kriteria=nilai_kriteria.id_nilai_kriteria');
		$this->db->where('nilai_peserta.id_lomba', $id_lomba);
		return $this->db->get()->result();
	}
	public function update($id_nilai_peserta ,$data)
	{
		$this->db->where('id_nilai_peserta', $id_nilai_peserta);
		$this->db->update('nilai_peserta', $data);
	}
	public function get_by_user($id_user)
	{
		$this->db->from('nilai_peserta a');
		$this->db->join('peserta b', 'a.id_peserta=b.id_peserta');
		$this->db->join('lomba c', 'b.id_lomba=c.id_lomba');
		$this->db->where('c.id_user', $id_user);
		return $this->db->get()->result();
	}
	public function get_all()
	{
		$this->db->select('*');
		$this->db->from('nilai_peserta');
		$this->db->join('kriteria', 'nilai_peserta.id_kriteria=kriteria.id_kriteria');
		$this->db->join('pesertaa', 'nilai_peserta.id_peserta=pesertaa.id_peserta');
		$this->db->join('lomba', 'nilai_peserta.id_lomba=lomba.id_lomba');
		$this->db->join('nilai_kriteria', 'nilai_peserta.id_nilai_kriteria=nilai_kriteria.id_nilai_kriteria');
		return $this->db->get()->result();
	}
	public function by_id_peserta($id_peserta)
	{
		$this->db->select('*');
		$this->db->from('nilai_peserta');
		$this->db->join('kriteria', 'nilai_peserta.id_kriteria=kriteria.id_kriteria');
		$this->db->join('pesertaa', 'nilai_peserta.id_peserta=pesertaa.id_peserta');
		$this->db->join('lomba', 'nilai_peserta.id_lomba=lomba.id_lomba');
		$this->db->join('nilai_kriteria', 'nilai_peserta.id_nilai_kriteria=nilai_kriteria.id_nilai_kriteria');
		$this->db->where('nilai_peserta.id_peserta', $id_peserta);
		return $this->db->get()->result();
	}
	public function delete($id_nilai_peserta)
	{
		$this->db->where('id_nilai_peserta', $id_nilai_peserta);
		$this->db->delete('nilai_peserta');
	}
	public function getNilaiPeserta($id_peserta, $id_lomba, $id_kriteria)
	{
		$this->db->from('nilai_peserta a');
		$this->db->join('nilai_kriteria b', 'a.id_nilai_kriteria=b.id_nilai_kriteria');
		$this->db->where('a.id_peserta', $id_peserta);
		$this->db->where('a.id_lomba', $id_lomba);
		$this->db->where('a.id_kriteria', $id_kriteria);
		return $this->db->get()->row();
	}
	public function nilaiKriteriaPeserta($id_peserta, $id_kriteria)
	{
		$this->db->select('nilai_kriteria.nilai');
		$this->db->from('nilai_peserta');
		$this->db->join('nilai_kriteria', 'nilai_peserta.id_nilai_kriteria=nilai_kriteria.id_nilai_kriteria');
		$this->db->where('nilai_peserta.id_peserta', $id_peserta);
		$this->db->where('nilai_peserta.id_kriteria', $id_kriteria);
		return $this->db->get()->row();
	}
	public function min_nilai_kriteria($id_kriteria, $jenis_kelamin)
	{
		$this->db->select('min(nilai_kriteria.nilai) as min');
		$this->db->from('nilai_peserta');
		$this->db->join('nilai_kriteria', 'nilai_peserta.id_nilai_kriteria=nilai_kriteria.id_nilai_kriteria');
		$this->db->where('nilai_peserta.id_kriteria', $id_kriteria);
		$this->db->where('nilai_kriteria.jenis_kelamin', $jenis_kelamin);
		return $this->db->get()->row();
	}
	public function max_nilai_kriteria($id_kriteria, $jenis_kelamin)
	{
		$this->db->select('max(nilai_kriteria.nilai) as max');
		$this->db->from('nilai_peserta');
		$this->db->join('nilai_kriteria', 'nilai_peserta.id_nilai_kriteria=nilai_kriteria.id_nilai_kriteria');
		$this->db->where('nilai_peserta.id_kriteria', $id_kriteria);
		$this->db->where('nilai_kriteria.jenis_kelamin', $jenis_kelamin);
		return $this->db->get()->row();
	}
}
?>