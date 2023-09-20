<?php 
class Hasil_model extends CI_Model {
	public function cek_nilai($id_peserta, $id_lomba)
	{
		$this->db->from('hasil');
		$this->db->where('id_peserta', $id_peserta);
		$this->db->where('id_lomba', $id_lomba);
		return $this->db->get()->row();
	}
	public function insert($data)
	{
		$this->db->insert('hasil', $data);
	}
	public function update($id_hasil, $data)
	{
		$this->db->where('id_hasil', $id_hasil);
		$this->db->update('hasil', $data);
	}
	public function ranking($id_lomba, $jenis_kelamin)
	{
		$this->db->from('hasil');
		$this->db->join('pesertaa', 'hasil.id_peserta=pesertaa.id_peserta');
		$this->db->where('hasil.id_lomba', $id_lomba);
		$this->db->where('pesertaa.jenis_kelamin', $jenis_kelamin);
		$this->db->order_by('hasil', 'DESC');
		return $this->db->get()->result();
	}
}
?>