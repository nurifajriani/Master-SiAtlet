<?php 
class RatingKecocokan_model extends CI_Model {

	public function truncate()
	{
		$this->db->truncate('rating_kecocokan');
	}
	public function insert($data)
	{
		$this->db->insert('rating_kecocokan', $data);
	}
	public function minNilaiKriteria($id_kriteria)
	{
		$this->db->select('MIN(nilai_rating_kecocokan) as min');
		$this->db->from('rating_kecocokan');
		$this->db->where('id_kriteria', $id_kriteria);
		return $this->db->get()->row();
	}
	public function maxNilaiKriteria($id_kriteria)
	{
		$this->db->select('MAX(nilai_rating_kecocokan) as max');
		$this->db->from('rating_kecocokan');
		$this->db->where('id_kriteria', $id_kriteria);
		return $this->db->get()->row();
	}
	public function getNilaiRatingKecocokan($id_kriteria, $id_peserta)
	{
		$this->db->select('nilai_rating_kecocokan, id_rating_kecocokan');
		$this->db->from('rating_kecocokan');
		$this->db->where('id_kriteria', $id_kriteria);
		$this->db->where('id_peserta', $id_peserta);
		return $this->db->get()->row();
	}
	public function update($id_rating_kecocokan, $data)
	{
		$this->db->where('id_rating_kecocokan', $id_rating_kecocokan);
		$this->db->update('rating_kecocokan', $data);
	}
}
?>