<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
require APPPATH . '/libraries/JWT.php';
require APPPATH . '/libraries/ExpiredException.php';
require APPPATH . '/libraries/BeforeValidException.php';
require APPPATH . '/libraries/SignatureInvalidException.php';
require APPPATH . '/libraries/JWK.php';

use Restserver\Libraries\REST_Controller;
use \Firebase\JWT\JWT;
use \Firebase\JWT\ExpiredException;

class NilaiKriteria extends REST_Controller {

	var $key 	= '';

	public function __construct()
	{
		parent::__construct();
		date_default_timezone_set('Asia/Jakarta');

		$this->key 		= 'API_SPK';

		$this->load->model('Kriteria_model');
		$this->load->model('NilaiKriteria_model');
		$this->load->model('Lomba_model');
		$this->load->model('User_model');
	}
	public function add_post() 
	{ // *
		if (count($this->post()) == 5) { 

			if ( $this->post('token') && $this->post('nilai') && $this->post('keterangan') && $this->post('id_kriteria') && $this->post('jenis_kelamin') )
			{ // nama parameter sesuai
				$nilai 				= $this->post('nilai');
				$keterangan			= $this->post('keterangan');
				$id_kriteria 		= $this->post('id_kriteria');
				$jenis_kelamin 		= $this->post('jenis_kelamin');
				$token				= $this->post('token');

				if (count(explode('.', $token)) == 3) {

					$decode 				= json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1])))); 

					$decode_key_secret 		= $decode->key_secret;
					$decode_id_user			= $decode->id_user;

					if ( $decode_key_secret == $this->key ) {

						$check_kriteria 		= $this->NilaiKriteria_model->cek_nilai_kriteria($id_kriteria, $nilai, $jenis_kelamin);

						if (!empty($check_kriteria)) {
							$response['meta']['message'] 	= 'Nilai kriteria sudah diinput sebelumnya';
							$response['meta']['code'] 		= '04';
							$response['meta']['status'] 	= 'error';
						}
						else {
							$data 	= array(
								'nilai' 			=> $nilai,
								'keterangan'		=> $keterangan,
								'id_kriteria' 		=> $id_kriteria,
								'jenis_kelamin' 	=> $jenis_kelamin,
								'created_at'		=> date('Y-m-d H-i-s')
							);
							$this->NilaiKriteria_model->insert($data);

							$response['meta']['message'] 	= 'Data nilai kriteria berhasil ditambahkan'; 
							$response['meta']['code'] 		= '200';
							$response['meta']['status'] 	= 'success';
						}
					}
					else{
						$response['meta']['message'] 	= 'Akun tidak ditemukan';
						$response['meta']['code'] 		= '404';
						$response['meta']['status'] 	= 'not found';
					}
				}
				else{
					$response['meta']['message'] 	= 'Token tidak valid';
					$response['meta']['code'] 		= '03';
					$response['meta']['status'] 	= 'error';
				}
			}
			else{ // nama parameter ada yang tidak sesuai
				$response['meta']['message'] 	= 'Nama parameter tidak valid';
				$response['meta']['code'] 		= '02';
				$response['meta']['status'] 	= 'error';
			}
		}
		else{ // jumlah parameter tidak sesuai
			$response['meta']['message'] 	= 'Jumlah parameter tidak valid';
			$response['meta']['code'] 		= '01';
			$response['meta']['status'] 	= 'error';
		}

		$this->response($response, REST_Controller::HTTP_OK);
	}
	public function update_post() 
	{ // *
		if (count($this->post()) == 6) { // jumlah parameter sesuai

			if ( $this->post('token') && $this->post('id_nilai_kriteria') && $this->post('nilai') && $this->post('keterangan') && $this->post('id_kriteria') && $this->post('jenis_kelamin') )
			{ // nama parameter sesuai
				$id_nilai_kriteria 	= $this->post('id_nilai_kriteria');
				$nilai 				= $this->post('nilai');
				$keterangan			= $this->post('keterangan');
				$id_kriteria 		= $this->post('id_kriteria');
				$jenis_kelamin 		= $this->post('jenis_kelamin');
				$token				= $this->post('token');

				if (count(explode('.', $token)) == 3) {

					$decode 				= json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1])))); 

					$decode_key_secret 		= $decode->key_secret;
					$decode_id_user			= $decode->id_user;

					if ( $decode_key_secret == $this->key ) {

						$data_nilai_kriteria 	= $this->NilaiKriteria_model->get_by_id($id_nilai_kriteria);

						if (!empty($data_nilai_kriteria)) {
							$data 	= array(
								'nilai' 			=> $nilai,
								'keterangan'		=> $keterangan,
								'id_kriteria' 		=> $id_kriteria,
								'jenis_kelamin' 	=> $jenis_kelamin,
								'updated_at'		=> date('Y-m-d H-i-s')
							);
							$this->NilaiKriteria_model->update($id_nilai_kriteria, $data);

							$response['meta']['message'] 	= 'Data nilai kriteria berhasil diubah'; 
							$response['meta']['code'] 		= '200';
							$response['meta']['status'] 	= 'success';
						}
						else{
							$response['meta']['message'] 	= 'Data nilai kriteria tidak ditemukan';
							$response['meta']['code'] 		= '404';
							$response['meta']['status'] 	= 'not found';
						}
					}
					else{
						$response['meta']['message'] 	= 'Akun tidak ditemukan';
						$response['meta']['code'] 		= '404';
						$response['meta']['status'] 	= 'not found';
					}
				}
				else{
					$response['meta']['message'] 	= 'Token tidak valid';
					$response['meta']['code'] 		= '03';
					$response['meta']['status'] 	= 'error';
				}
			}
			else{ // nama parameter ada yang tidak sesuai
				$response['meta']['message'] 	= 'Nama parameter tidak valid';
				$response['meta']['code'] 		= '02';
				$response['meta']['status'] 	= 'error';
			}
		}
		else{ // jumlah parameter tidak sesuai
			$response['meta']['message'] 	= 'Jumlah parameter tidak valid';
			$response['meta']['code'] 		= '01';
			$response['meta']['status'] 	= 'error';
		}

		$this->response($response, REST_Controller::HTTP_OK);
	}
	public function get_by_id_post() 
	{ // *
		if (count($this->post()) == 2) {

			if ( $this->post('token') && $this->post('id_nilai_kriteria') )
			{ // nama parameter sesuai
				$id_nilai_kriteria 	= $this->post('id_nilai_kriteria');
				$token				= $this->post('token');

				if (count(explode('.', $token)) == 3) {

					$decode 				= json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1])))); 

					$decode_key_secret 		= $decode->key_secret;
					$decode_id_user			= $decode->id_user;

					if ( $decode_key_secret == $this->key ) {

						$data_nilai_kriteria 	= $this->NilaiKriteria_model->get_by_id($id_nilai_kriteria);

						if (!empty($data_nilai_kriteria)) {

							$response['meta']['message']	= 'Data ditemukan';
							$response['meta']['code'] 		= '200';
							$response['meta']['status'] 	= 'success';
							$response['data']['id_nilai_kriteria'] 	= $data_nilai_kriteria->id_nilai_kriteria;
							$response['data']['nilai'] 				= $data_nilai_kriteria->nilai;
							$response['data']['keterangan'] 		= $data_nilai_kriteria->keterangan;
							$response['data']['id_kriteria'] 		= $data_nilai_kriteria->id_kriteria;
							$response['data']['nama_kriteria'] 		= $data_nilai_kriteria->nama_kriteria;
							$response['data']['jenis_kelamin'] 		= $data_nilai_kriteria->jenis_kelamin;  
						}
						else{
							$response['meta']['message'] 	= 'Data nilai kriteria tidak ditemukan';
							$response['meta']['code'] 		= '404';
							$response['meta']['status'] 	= 'not found';
						}
					}
					else{
						$response['meta']['message'] 	= 'Akun tidak ditemukan';
						$response['meta']['code'] 		= '404';
						$response['meta']['status'] 	= 'not found';
					}
				}
				else{
					$response['meta']['message'] 	= 'Token tidak valid';
					$response['meta']['code'] 		= '03';
					$response['meta']['status'] 	= 'error';
				}
			}
			else{ // nama parameter ada yang tidak sesuai
				$response['meta']['message'] 	= 'Nama parameter tidak valid';
				$response['meta']['code'] 		= '02';
				$response['meta']['status'] 	= 'error';
			}
		}
		else{ // jumlah parameter tidak sesuai
			$response['meta']['message'] 	= 'Jumlah parameter tidak valid';
			$response['meta']['code'] 		= '01';
			$response['meta']['status'] 	= 'error';
		}

		$this->response($response, REST_Controller::HTTP_OK);
	}
	public function get_by_id_kriteria_post() 
	{ // *
		if (count($this->post()) == 2) {

			if ( $this->post('token') && $this->post('id_kriteria') )
			{ // nama parameter sesuai
				$id_kriteria 		= $this->post('id_kriteria');
				$token				= $this->post('token');

				if (count(explode('.', $token)) == 3) {

					$decode 				= json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1])))); 

					$decode_key_secret 		= $decode->key_secret;
					$decode_id_user			= $decode->id_user;

					if ( $decode_key_secret == $this->key ) {

						$data_nilai_kriteria 	= $this->NilaiKriteria_model->get_by_id_kriteria($id_kriteria);

						if (!empty($data_nilai_kriteria)) {

							$response['meta']['message']	= 'Data ditemukan';
							$response['meta']['code'] 		= '200';
							$response['meta']['status'] 	= 'success';
							foreach ($data_nilai_kriteria as $key => $value) {
								$response['data'][$key]['id_nilai_kriteria']= $value->id_nilai_kriteria;
								$response['data'][$key]['nilai'] 			= $value->nilai;
								$response['data'][$key]['keterangan'] 		= $value->keterangan;
								$response['data'][$key]['id_kriteria'] 		= $value->id_kriteria;
								$response['data'][$key]['nama_kriteria'] 	= $value->nama_kriteria;
								$response['data'][$key]['nama_lomba'] 		= $value->nama_lomba;
								$response['data'][$key]['jenis_kelamin'] 	= $value->jenis_kelamin;
							}  
						}
						else{
							$response['meta']['message'] 	= 'Data nilai kriteria tidak ditemukan';
							$response['meta']['code'] 		= '404';
							$response['meta']['status'] 	= 'not found';
						}
					}
					else{
						$response['meta']['message'] 	= 'Akun tidak ditemukan';
						$response['meta']['code'] 		= '404';
						$response['meta']['status'] 	= 'not found';
					}
				}
				else{
					$response['meta']['message'] 	= 'Token tidak valid';
					$response['meta']['code'] 		= '03';
					$response['meta']['status'] 	= 'error';
				}
			}
			else{ // nama parameter ada yang tidak sesuai
				$response['meta']['message'] 	= 'Nama parameter tidak valid';
				$response['meta']['code'] 		= '02';
				$response['meta']['status'] 	= 'error';
			}
		}
		else{ // jumlah parameter tidak sesuai
			$response['meta']['message'] 	= 'Jumlah parameter tidak valid';
			$response['meta']['code'] 		= '01';
			$response['meta']['status'] 	= 'error';
		}

		$this->response($response, REST_Controller::HTTP_OK);
	}
	public function get_by_user_post() 
	{
		if (count($this->post()) == 1) { // jumlah parameter sesuai

			if ( $this->post('token') )
			{ // nama parameter sesuai
				$token				= $this->post('token');

				if (count(explode('.', $token)) == 3) {

					$decode 				= json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1])))); 

					$decode_key_secret 		= $decode->key_secret;
					$decode_id_user			= $decode->id_user;
					$decode_username 		= $decode->username;
					$decode_password 		= $decode->password;

					$cek_user 		= $this->User_model->cek_token($decode_id_user, $decode_username, md5($decode_password));

					if ( ($decode_key_secret == $this->key) && !empty($cek_user) ) {

						$data_nilai_kriteria 	= $this->NilaiKriteria_model->get_by_user($decode_id_user);

						if (!empty($data_nilai_kriteria)) {

							$response['meta']['message'] 	= 'Data ditemukan';
							$response['meta']['code'] 		= '200';
							$response['meta']['status'] 	= 'success';
							$response['data'] 				= $data_nilai_kriteria; 
						}
						else{
							$response['meta']['message'] 	= 'Data nilai kriteria tidak ditemukan';
							$response['meta']['code'] 		= '404';
							$response['meta']['status'] 	= 'not found';
						}
					}
					else{
						$response['meta']['message'] 	= 'Akun tidak ditemukan';
						$response['meta']['code'] 		= '404';
						$response['meta']['status'] 	= 'not found';
					}
				}
				else{
					$response['meta']['message'] 	= 'Token tidak valid';
					$response['meta']['code'] 		= '03';
					$response['meta']['status'] 	= 'error';
				}
			}
			else{ // nama parameter ada yang tidak sesuai
				$response['meta']['message'] 	= 'Nama parameter tidak valid';
				$response['meta']['code'] 		= '02';
				$response['meta']['status'] 	= 'error';
			}
		}
		else{ // jumlah parameter tidak sesuai
			$response['meta']['message'] 	= 'Jumlah parameter tidak valid';
			$response['meta']['code'] 		= '01';
			$response['meta']['status'] 	= 'error';
		}

		$this->response($response, REST_Controller::HTTP_OK);
	}
	public function get_all_post() 
	{ // *
		if (count($this->post()) == 1) { // jumlah parameter sesuai

			if ( $this->post('token') )
			{ // nama parameter sesuai
				$token				= $this->post('token');

				if (count(explode('.', $token)) == 3) {

					$decode 				= json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1])))); 

					$decode_key_secret 		= $decode->key_secret;
					$decode_id_user			= $decode->id_user;

					if ( $decode_key_secret == $this->key ) {

						$data_nilai_kriteria 	= $this->NilaiKriteria_model->get_all();

						if (!empty($data_nilai_kriteria)) {

							$response['meta']['message'] 	= 'Data ditemukan';
							$response['meta']['code'] 		= '200';
							$response['meta']['status'] 	= 'success';
							foreach ($data_nilai_kriteria as $key => $value) {
								$response['data'][$key]['id_nilai_kriteria']= $value->id_nilai_kriteria;
								$response['data'][$key]['nilai'] 			= $value->nilai;
								$response['data'][$key]['keterangan'] 		= $value->keterangan;
								$response['data'][$key]['id_kriteria'] 		= $value->id_kriteria;
								$response['data'][$key]['nama_kriteria'] 	= $value->nama_kriteria;
								$response['data'][$key]['jenis_kelamin'] 	= $value->jenis_kelamin;
							} 
						}
						else{
							$response['meta']['message'] 	= 'Data nilai kriteria tidak ditemukan';
							$response['meta']['code'] 		= '404';
							$response['meta']['status'] 	= 'not found';
						}
					}
					else{
						$response['meta']['message'] 	= 'Akun tidak ditemukan';
						$response['meta']['code'] 		= '404';
						$response['meta']['status'] 	= 'not found';
					}
				}
				else{
					$response['meta']['message'] 	= 'Token tidak valid';
					$response['meta']['code'] 		= '03';
					$response['meta']['status'] 	= 'error';
				}
			}
			else{ // nama parameter ada yang tidak sesuai
				$response['meta']['message'] 	= 'Nama parameter tidak valid';
				$response['meta']['code'] 		= '02';
				$response['meta']['status'] 	= 'error';
			}
		}
		else{ // jumlah parameter tidak sesuai
			$response['meta']['message'] 	= 'Jumlah parameter tidak valid';
			$response['meta']['code'] 		= '01';
			$response['meta']['status'] 	= 'error';
		}

		$this->response($response, REST_Controller::HTTP_OK);
	}
	public function delete_post() 
	{ // *
		if (count($this->post()) == 2) { 

			if ( $this->post('token') && $this->post('id_nilai_kriteria') )
			{ // nama parameter sesuai
				$id_nilai_kriteria 	= $this->post('id_nilai_kriteria');
				$token				= $this->post('token');

				if (count(explode('.', $token)) == 3) {

					$decode 				= json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1])))); 

					$decode_key_secret 		= $decode->key_secret;
					$decode_id_user			= $decode->id_user;

					if ( $decode_key_secret == $this->key ) {

						$data_nilai_kriteria 	= $this->NilaiKriteria_model->get_by_id($id_nilai_kriteria);

						if (!empty($data_nilai_kriteria)) {

							$this->NilaiKriteria_model->delete($id_nilai_kriteria);

							$response['meta']['message'] 	= 'Data nilai kriteria berhasil dihapus'; 
							$response['meta']['code'] 		= '200';
							$response['meta']['status'] 	= 'success';
						}
						else{
							$response['meta']['message'] 	= 'Data nilai kriteria tidak ditemukan';
							$response['meta']['code'] 		= '404';
							$response['meta']['status'] 	= 'not found';
						}
					}
					else{
						$response['meta']['message'] 	= 'Akun tidak ditemukan';
						$response['meta']['code'] 		= '404';
						$response['meta']['status'] 	= 'not found';
					}
				}
				else{
					$response['meta']['message'] 	= 'Token tidak valid';
					$response['meta']['code'] 		= '03';
					$response['meta']['status'] 	= 'error';
				}
			}
			else{ // nama parameter ada yang tidak sesuai
				$response['meta']['message'] 	= 'Nama parameter tidak valid';
				$response['meta']['code'] 		= '02';
				$response['meta']['status'] 	= 'error';
			}
		}
		else{ // jumlah parameter tidak sesuai
			$response['meta']['message'] 	= 'Jumlah parameter tidak valid';
			$response['meta']['code'] 		= '01';
			$response['meta']['status'] 	= 'error';
		}

		$this->response($response, REST_Controller::HTTP_OK);
	}
	public function nilai_kriteria_putra_post() 
	{
		if (count($this->post()) == 3) { // jumlah parameter sesuai

			if ( $this->post('token') && $this->post('id_kriteria') && $this->post('id_lomba') )
			{ // nama parameter sesuai
				$id_kriteria 		= $this->post('id_kriteria');
				$id_lomba 	 		= $this->post('id_lomba');
				$token				= $this->post('token');

				if (count(explode('.', $token)) == 3) {

					$decode 				= json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1])))); 

					$decode_key_secret 		= $decode->key_secret;
					$decode_id_user			= $decode->id_user;

					if ( $decode_key_secret == $this->key ) {

						$data_nilai_kriteria 	= $this->NilaiKriteria_model->cekNilaiPutra($id_kriteria, $id_lomba);

						if (!empty($data_nilai_kriteria)) {

							$response['meta']['message']	= 'Data ditemukan';
							$response['meta']['code'] 		= '200';
							$response['meta']['status'] 	= 'success';
							$response['data'] 				= $data_nilai_kriteria; 
						}
						else{
							$response['meta']['message'] 	= 'Data nilai kriteria tidak ditemukan';
							$response['meta']['code'] 		= '404';
							$response['meta']['status'] 	= 'not found';
						}
					}
					else{
						$response['meta']['message'] 	= 'Akun tidak ditemukan';
						$response['meta']['code'] 		= '404';
						$response['meta']['status'] 	= 'not found';
					}
				}
				else{
					$response['meta']['message'] 	= 'Token tidak valid';
					$response['meta']['code'] 		= '03';
					$response['meta']['status'] 	= 'error';
				}
			}
			else{ // nama parameter ada yang tidak sesuai
				$response['meta']['message'] 	= 'Nama parameter tidak valid';
				$response['meta']['code'] 		= '02';
				$response['meta']['status'] 	= 'error';
			}
		}
		else{ // jumlah parameter tidak sesuai
			$response['meta']['message'] 	= 'Jumlah parameter tidak valid';
			$response['meta']['code'] 		= '01';
			$response['meta']['status'] 	= 'error';
		}

		$this->response($response, REST_Controller::HTTP_OK);
	}
	public function nilai_kriteria_putri_post() 
	{
		if (count($this->post()) == 3) { // jumlah parameter sesuai

			if ( $this->post('token') && $this->post('id_kriteria') && $this->post('id_lomba') )
			{ // nama parameter sesuai
				$id_kriteria 		= $this->post('id_kriteria');
				$id_lomba 	 		= $this->post('id_lomba');
				$token				= $this->post('token');

				if (count(explode('.', $token)) == 3) {

					$decode 				= json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1])))); 

					$decode_key_secret 		= $decode->key_secret;
					$decode_id_user			= $decode->id_user;

					if ( $decode_key_secret == $this->key ) {

						$data_nilai_kriteria 	= $this->NilaiKriteria_model->cekNilaiPutri($id_kriteria, $id_lomba);

						if (!empty($data_nilai_kriteria)) {

							$response['meta']['message']	= 'Data ditemukan';
							$response['meta']['code'] 		= '200';
							$response['meta']['status'] 	= 'success';
							$response['data'] 				= $data_nilai_kriteria; 
						}
						else{
							$response['meta']['message'] 	= 'Data nilai kriteria tidak ditemukan';
							$response['meta']['code'] 		= '404';
							$response['meta']['status'] 	= 'not found';
						}
					}
					else{
						$response['meta']['message'] 	= 'Akun tidak ditemukan';
						$response['meta']['code'] 		= '404';
						$response['meta']['status'] 	= 'not found';
					}
				}
				else{
					$response['meta']['message'] 	= 'Token tidak valid';
					$response['meta']['code'] 		= '03';
					$response['meta']['status'] 	= 'error';
				}
			}
			else{ // nama parameter ada yang tidak sesuai
				$response['meta']['message'] 	= 'Nama parameter tidak valid';
				$response['meta']['code'] 		= '02';
				$response['meta']['status'] 	= 'error';
			}
		}
		else{ // jumlah parameter tidak sesuai
			$response['meta']['message'] 	= 'Jumlah parameter tidak valid';
			$response['meta']['code'] 		= '01';
			$response['meta']['status'] 	= 'error';
		}

		$this->response($response, REST_Controller::HTTP_OK);
	}
}
?>