<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
require APPPATH . '/libraries/JWT.php';
require APPPATH . '/libraries/ExpiredException.php';
require APPPATH . '/libraries/BeforeValidException.php';
require APPPATH . '/libraries/SignatureInvalidException.php';
require APPPATH . '/libraries/JWK.php';

use Restserver\Libraries\REST_Controller;
use \Firebase\JWT\JWT;
use \Firebase\JWT\ExpiredException;

class Hasil extends REST_Controller
{

	var $key 	= '';

	public function __construct()
	{
		parent::__construct();
		date_default_timezone_set('Asia/Jakarta');

		$this->key 		= 'API_SPK';

		$this->load->model('User_model');
		$this->load->model('Kriteria_model');
		$this->load->model('BobotKriteria_model');
		$this->load->model('NilaiKriteria_model');
		$this->load->model('NilaiPeserta_model');
		$this->load->model('Peserta_model');
		$this->load->model('RatingKecocokan_model');
		$this->load->model('RatingKinerja_model');
		$this->load->model('Hasil_model');
	}

	public function ranking_post()
	{
		if (count($this->post()) == 3) {
			if ($this->post('token') && $this->post('id_lomba') && $this->post('jenis_kelamin')) {

				$token				= $this->post('token');
				$id_lomba			= $this->post('id_lomba');
				$jenis_kelamin		= $this->post('jenis_kelamin');

				if (count(explode('.', $token)) == 3) {

					$decode 				= json_decode(base64_decode(str_replace('_', '/', str_replace('-', '+', explode('.', $token)[1]))));

					$decode_key_secret 		= $decode->key_secret;
					$decode_id_user			= $decode->id_user;

					if ($decode_key_secret == $this->key) {

						$peserta_lomba 	= $this->Peserta_model->get_peserta_lomba($id_lomba, $jenis_kelamin);
						$kriteria 		= $this->Kriteria_model->kriteria_lomba($id_lomba);

						foreach ($peserta_lomba as $key1 => $value1) {
							$hasil 	= 0;
							foreach ($kriteria as $key2 => $value2) {
								$min 	= $this->min_kriteria($value2->id_kriteria, $jenis_kelamin);
								$max 	= $this->max_kriteria($value2->id_kriteria, $jenis_kelamin);
								$nilai_kriteria_peserta = $this->NilaiPeserta_model->nilaiKriteriaPeserta($value1->id_peserta, $value2->id_kriteria);

								if (!empty($nilai_kriteria_peserta)) {
									$nilai_kriteria_peserta = $this->NilaiPeserta_model->nilaiKriteriaPeserta($value1->id_peserta, $value2->id_kriteria)->nilai;

									// $sum_bobot 		= $this->BobotKriteria_model->sumBobot()->jml;
									$nilai_bobot	= $this->BobotKriteria_model->get_by_kriteria($value2->id_kriteria)->bobot;

									//normalisasi matriks
									if ($value2->sifat == 'Benefit') {
										if ($min > 0 || $max > 0) {
											$rij 	= sprintf("%.3f", $nilai_kriteria_peserta / $max);
										}
									}
									if ($value2->sifat == 'Cost') {
										if ($min > 0 || $max > 0) {
											$rij 	= sprintf("%.3f", $min / $nilai_kriteria_peserta);
										}
									}

									//pembobotan normalisasi
									// $count = $rij * ($nilai_bobot / $sum_bobot);
									$count = $rij * ($nilai_bobot);
									$hasil = $hasil + $count;
								}
							}

							$cek_nilai_peserta = $this->Hasil_model->cek_nilai($value1->id_peserta, $value1->id_lomba);
							if (empty($cek_nilai_peserta)) {
								$data 	= array(
									'hasil' 		=> round($hasil, 3),
									'id_lomba' 		=> $value1->id_lomba,
									'id_peserta' 	=> $value1->id_peserta,
									'created_at' 	=> date('Y-m-d H-i-s')
								);
								$this->Hasil_model->insert($data);
							}
							if (!empty($cek_nilai_peserta)) {
								$data 	= array(
									'hasil' 		=> round($hasil, 3),
									'updated_at' 	=> date('Y-m-d H-i-s')
								);
								$this->Hasil_model->update($cek_nilai_peserta->id_hasil, $data);
							}
						}

						$ranking 	= $this->Hasil_model->ranking($id_lomba, $jenis_kelamin);

						$response['meta']['message'] 	= 'Data ranking ditemukan';
						$response['meta']['code'] 		= '200';
						$response['meta']['status'] 	= 'success';

						$no 	= 1;
						foreach ($ranking as $key => $value) {
							$response['data'][$key]['ranking'] 			= $no++;
							$response['data'][$key]['id_peserta'] 		= $value->id_peserta;
							$response['data'][$key]['nama_peserta'] 	= $value->nama_peserta;
							$response['data'][$key]['hasil'] 			= $value->hasil;
						}
					} else {
						$response['meta']['message'] 	= 'Akun tidak ditemukan';
						$response['meta']['code'] 		= '404';
						$response['meta']['status'] 	= 'not found';
					}
				} else {
					$response['meta']['message'] 	= 'Token tidak valid';
					$response['meta']['code'] 		= '03';
					$response['meta']['status'] 	= 'error';
				}
			} else {
				$response['meta']['message'] 	= 'Nama parameter tidak valid';
				$response['meta']['code'] 		= '02';
				$response['meta']['status'] 	= 'error';
			}
		} else {
			$response['meta']['message'] 	= 'Jumlah parameter tidak valid';
			$response['meta']['code'] 		= '01';
			$response['meta']['status'] 	= 'error';
		}
		$this->response($response, REST_Controller::HTTP_OK);
	}
	public function min_kriteria($id_kriteria, $jenis_kelamin)
	{
		return $this->NilaiPeserta_model->min_nilai_kriteria($id_kriteria, $jenis_kelamin)->min;
	}
	public function max_kriteria($id_kriteria, $jenis_kelamin)
	{
		return $this->NilaiPeserta_model->max_nilai_kriteria($id_kriteria, $jenis_kelamin)->max;
	}
}
