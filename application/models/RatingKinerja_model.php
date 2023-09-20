<?php 
class RatingKinerja_model extends CI_Model {

	public function truncate()
	{
		$this->db->truncate('rating_kinerja');
	}
	public function insert($data)
	{
		$this->db->insert('rating_kinerja', $data);
	}
	public function getNilaiRatngKinerja($id_kriteria, $id_peserta)
	{
		$this->db->from('rating_kinerja');
		$this->db->where('id_kriteria', $id_kriteria);
		$this->db->where('id_peserta', $id_peserta);
		return $this->db->get()->row();
	}
	public function truncateHasil()
	{
		$this->db->truncate('hasil');
	}
	public function createHasil($data)
	{
		$this->db->insert('hasil', $data);
	}
	public function hasilPerhitungan($jenis_kelamin)
	{
		$this->db->from('hasil a');
		$this->db->join('peserta b', 'a.id_peserta=b.id_peserta');
		$this->db->where('b.jenis_kelamin', $jenis_kelamin);
		$this->db->order_by('a.hasil', 'DESC');
		return $this->db->get()->result();
	}
	public function hasilPerhitunganPeserta($id_peserta)
	{
		$this->db->from('hasil a');
		$this->db->join('peserta b', 'a.id_peserta=b.id_peserta');
		$this->db->where('a.id_peserta', $id_peserta);
		$this->db->order_by('a.hasil', 'DESC');
		return $this->db->get()->row();
	}
	public function insertNormalisasi($data)
	{
		$this->db->insert('normalisasi_bobot', $data);
	}
	public function cek_normalisasi($id_kriteria)
	{
		$this->db->from('normalisasi_bobot');
		$this->db->where('id_kriteria', $id_kriteria);
		return $this->db->get()->row();
	}
	public function updateNormalisasi($id_normalisasi_bobot, $data)
	{
		$this->db->where('id_normalisasi_bobot', $id_normalisasi_bobot);
	 	$this->db->update('normalisasi_bobot', $data);
	}
	public function truncateNormalisasi()
	{
		$this->db->truncate('normalisasi_bobot');
	}
	public function getNormalisasi($id_kriteria)
	{
		$this->db->from('normalisasi_bobot');
		$this->db->where('id_kriteria', $id_kriteria);
		return $this->db->get()->row();
	}
	public function by_kriteria_peserta($id_peserta, $id_kriteria)
	{
		$this->db->from('rating_kinerja');
		$this->db->where('id_peserta', $id_peserta);
		$this->db->where('id_kriteria', $id_kriteria);
		return $this->db->get()->row();
	}
	public function update($id_rating_kinerja, $data)
	{
	 	$this->db->where('id_rating_kinerja', $id_rating_kinerja);
	 	$this->db->update('rating_kinerja', $data);
	}
	public function hasil_lomba_peserta($id_lomba, $id_peserta)
	{
		$this->db->from('hasil');
		$this->db->where('id_peserta', $id_peserta);
		$this->db->where('id_lomba', $id_lomba);
		return $this->db->get()->row();
	}
	public function updateHasil($id_hasil, $data)
	{
		$this->db->where('id_hasil', $id_hasil);
	 	$this->db->update('hasil', $data);
	}
}
?>