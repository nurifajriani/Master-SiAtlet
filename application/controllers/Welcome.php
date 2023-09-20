<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */

	public function __construct()
	{
		parent::__construct();
		date_default_timezone_set('Asia/Jakarta');

		$this->key 		= 'API_SPK';

		$this->load->model('Kriteria_model');
		$this->load->model('BobotKriteria_model');
		$this->load->model('NilaiKriteria_model');
		$this->load->model('NilaiPeserta_model');
		$this->load->model('Peserta_model');
		$this->load->model('RatingKecocokan_model');
		$this->load->model('RatingKinerja_model');
	}

	public function index()
	{
		$this->load->view('welcome_message');
	}
	public function cek()
	{
		$id_kriteria 	= '1';
		$id_lomba 		= '1';

		$hasil 	= $this->NilaiKriteria_model->cekNilai($id_kriteria, $id_lomba);
		foreach ($hasil as $key => $value) {
			echo $value->nilai_min." - ".$value->nilai_max."<br>";
		}
	}
	public function Hasil()
	{
		$kriteria 	= $this->Kriteria_model->get_all();
		$peserta 	= $this->Peserta_model->get_all();

		$this->RatingKecocokan_model->truncate();
		$this->RatingKinerja_model->truncateNormalisasi();

		// $normalisasi_bobot 	= [];
		foreach ($kriteria as $key => $value) {
			$bobot 				= $this->BobotKriteria_model->get_by_lomba_kriteria($value->id_lomba, $value->id_kriteria)->bobot;
			$sum_bobot 			= $this->BobotKriteria_model->sumBobot()->jml;
			$normalisasi_bobot 	= $bobot/$sum_bobot;

			$data_normalisasi 	= array(
				'id_kriteria' 		=> $value->id_kriteria,
				'nilai_normalisasi_bobot' 	=> $normalisasi_bobot,
			);
			$this->RatingKinerja_model->insertNormalisasi($data_normalisasi);
		}

		foreach ($peserta as $key => $value) {
			// echo $value->no_reg."<br>";

			$id_peserta 	= $value->id_peserta;

			// echo "<br><br>----- AWAL -----<br><br>";
			// echo $value->no_reg." = <br>";

			// $nilai_prefensi = 0;
			foreach ($kriteria as $key2 => $value2) {
				$id_kriteria 	= $value2->id_kriteria;
				$id_lomba 		= $value2->id_lomba;
				$sifat 			= $value2->sifat;
				$bobot 			= $this->BobotKriteria_model->get_by_lomba_kriteria($id_lomba, $id_kriteria)->bobot;
				// $normalisasi_bobot 	= $this->NormalisasiBobot($bobot);
				$rating_kecocokan 	= $this->NilaiPeserta_model->getNilaiPeserta($id_peserta, $id_lomba, $id_kriteria)->nilai_bobot;
				// $min 			= $this->NilaiKriteria_model->minNilai($id_peserta, $id_kriteria, $id_lomba)->min;
				// $max 			= $this->NilaiKriteria_model->maxNilai($id_peserta, $id_kriteria, $id_lomba)->max;

				$data_rating 	= array(
					'id_peserta' 		=> $id_peserta,
					'id_kriteria' 		=> $id_kriteria,
					'nilai_rating_kecocokan' 	=> $rating_kecocokan
				);
				$this->RatingKecocokan_model->insert($data_rating);

				// if ($sifat == 'Benefit') {
				// 	$rating_kinerja 	= $rating_kecocokan / $max;
				// }
				// if ($sifat == 'Cost') {
				// 	$rating_kinerja 	= $min / $rating_kecocokan;
				// }
				// echo $value2->nama_kriteria." : ".$rating_kecocokan." || ";
				// echo $value2->nama_kriteria." : ".$rating_kinerja." || ";
				// echo " rating : ".$nilai_bobot.", ";
				// echo " normalisasi_bobot : ".$normalisasi_bobot.", ";
				
				// echo " || MIN = ".$min." || MAX = ".$max." || ";
				// $nilai_prefensi += ($normalisasi_bobot * $rating_kinerja);
			}
			// echo $nilai_prefensi."<br>";
			// echo "<br><br>";
		}

		$this->RatingKinerja();
	}
	public function NormalisasiBobot($bobot)
	{
		$sum_bobot 		= $this->BobotKriteria_model->sumBobot()->jml;
		return $bobot/$sum_bobot;
	}
	public function RatingKinerja()
	{
		$kriteria 	= $this->Kriteria_model->get_all();
		$peserta 	= $this->Peserta_model->get_all();

		$this->RatingKinerja_model->truncate();

		foreach ($peserta as $key => $value) {

			$id_peserta 	= $value->id_peserta;

			foreach ($kriteria as $key2 => $value2) {
				$id_kriteria 	= $value2->id_kriteria;
				$id_lomba 		= $value2->id_lomba;
				$sifat 			= $value2->sifat;
				$min 			= $this->RatingKecocokan_model->minNilaiKriteria($id_kriteria)->min;
				$max 			= $this->RatingKecocokan_model->maxNilaiKriteria($id_kriteria)->max;
				$nilai_rating_kecocokan		= $this->RatingKecocokan_model->getNilaiRatingKecocokan($id_kriteria, $id_peserta)->nilai_rating_kecocokan;

				if ($sifat == 'Benefit') {
					$rating_kinerja 	= $nilai_rating_kecocokan / $max;
				}
				if ($sifat == 'Cost') {
					$rating_kinerja 	= $min / $nilai_rating_kecocokan;
				}

				$data_rating 	= array(
					'id_peserta' 		=> $id_peserta,
					'id_kriteria' 		=> $id_kriteria,
					'nilai_rating_kinerja' 	=> $rating_kinerja
				);
				$this->RatingKinerja_model->insert($data_rating);
			}
		}
		
		$this->HitungRating();
	}

	public function HitungRating()
	{
		$kriteria 	= $this->Kriteria_model->get_all();
		$peserta 	= $this->Peserta_model->get_all();

		$this->RatingKinerja_model->truncateHasil();

		foreach ($peserta as $key => $value) {

			$id_peserta 	= $value->id_peserta;
			$hasil 			= 0;

			foreach ($kriteria as $key2 => $value2) {
				$id_kriteria 			= $value2->id_kriteria;
				$normalisasi_bobot		= $this->RatingKinerja_model->getNormalisasi($id_kriteria)->nilai_normalisasi_bobot;
				$nilai_rating_kinerja 	= $this->RatingKinerja_model->getNilaiRatngKinerja($id_kriteria, $id_peserta)->nilai_rating_kinerja;
				$tambah_hasil 			= $normalisasi_bobot * $nilai_rating_kinerja;

				$hasil 					+= $tambah_hasil; 
			}

			$data_hasil 	= array(
				'id_peserta'		=> $id_peserta,
				'id_lomba' 			=> $value->id_lomba,
				'hasil' 			=> $hasil,
				'created_at' 		=> date('Y-m-d H-i-s'),				
			);
			$this->RatingKinerja_model->createHasil($data_hasil);
		}

		$hasil_perhitungan 	= $this->RatingKinerja_model->hasilPerhitungan();

		// foreach ($hasil_perhitungan as $key => $value) {
		// 	echo $key."<br>";
		// }
		
		foreach ($hasil_perhitungan as $key => $value) {
			echo $value->nama_peserta." = ".$value->hasil."<br>";
		}
	}
}
