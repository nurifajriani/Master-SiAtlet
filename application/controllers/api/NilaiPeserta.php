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

class NilaiPeserta extends REST_Controller {

	var $key 	= '';

	public function __construct()
	{
		parent::__construct();
		date_default_timezone_set('Asia/Jakarta');

		$this->key 		= 'API_SPK';

		$this->load->model('NilaiPeserta_model');
		$this->load->model('User_model');
	}
	public function add_post() 
	{ // *
		if (count($this->post()) == 5) { 

			if ( $this->post('token') && $this->post('id_peserta') && $this->post('id_lomba') && $this->post('id_kriteria') && $this->post('id_nilai_kriteria') )
			{ // nama parameter sesuai
				$id_peserta			= $this->post('id_peserta');
				$id_lomba			= $this->post('id_lomba');
				$id_kriteria		= $this->post('id_kriteria');
				$id_nilai_kriteria 	= $this->post('id_nilai_kriteria');
				$token				= $this->post('token');

				if (count(explode('.', $token)) == 3) {

					$decode 				= json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1])))); 

					$decode_key_secret 		= $decode->key_secret;
					$decode_id_user			= $decode->id_user;

					if ( $decode_key_secret == $this->key ) {

						$check_kriteria 	= $this->NilaiPeserta_model->nilaiKriteriaPeserta($id_peserta, $id_kriteria);

						if (!empty($check_kriteria)) {
							$response['meta']['message'] 	= 'Nilai peserta sudah ditambahkan sebelumnya';
							$response['meta']['code'] 		= '04';
							$response['meta']['status'] 	= 'error';
						}
						else {
							$data 	= array(
								'id_peserta'		=> $id_peserta,
								'id_lomba'			=> $id_lomba,
								'id_kriteria'		=> $id_kriteria,
								'id_nilai_kriteria'	=> $id_nilai_kriteria,
								'created_at'		=> date('Y-m-d H-i-s')
							);
							$this->NilaiPeserta_model->insert($data);

							$response['meta']['message'] 	= 'Data nilai peserta berhasil ditambahkan';
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

			if ( $this->post('token') && $this->post('id_nilai_peserta') && $this->post('id_peserta') && $this->post('id_lomba') && $this->post('id_kriteria') && $this->post('id_nilai_kriteria') )
			{ // nama parameter sesuai
				$id_nilai_peserta 	= $this->post('id_nilai_peserta');
				$id_peserta			= $this->post('id_peserta');
				$id_lomba			= $this->post('id_lomba');
				$id_kriteria		= $this->post('id_kriteria');
				$id_nilai_kriteria 	= $this->post('id_nilai_kriteria');
				$token				= $this->post('token');

				if (count(explode('.', $token)) == 3) {

					$decode 				= json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1])))); 

					$decode_key_secret 		= $decode->key_secret;
					$decode_id_user			= $decode->id_user;

					if ( $decode_key_secret == $this->key ) {

						$cek_data 			= $this->NilaiPeserta_model->get_by_id($id_nilai_peserta);

						if (!empty($cek_data)) {
							$data 	= array(
								'id_peserta'		=> $id_peserta,
								'id_lomba'			=> $id_lomba,
								'id_kriteria'		=> $id_kriteria,
								'id_nilai_kriteria'	=> $id_nilai_kriteria,
								'updated_at'		=> date('Y-m-d H-i-s')
							);
							$this->NilaiPeserta_model->update($id_nilai_peserta ,$data);

							$response['meta']['message'] 	= 'Data nilai peserta berhasil diubah';
							$response['meta']['code'] 		= '200';
							$response['meta']['status'] 	= 'success';
						}
						else{
							$response['meta']['message'] 	= 'Data nilai peserta tidak ditemukan';
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
		if (count($this->post()) == 2) { // jumlah parameter sesuai

			if ( $this->post('token') && $this->post('id_nilai_peserta') )
			{ // nama parameter sesuai
				$id_nilai_peserta 	= $this->post('id_nilai_peserta');
				$token				= $this->post('token');

				if (count(explode('.', $token)) == 3) {

					$decode 				= json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1])))); 

					$decode_key_secret 		= $decode->key_secret;
					$decode_id_user			= $decode->id_user;

					if ( $decode_key_secret == $this->key ) {

						$cek_data 			= $this->NilaiPeserta_model->get_by_id($id_nilai_peserta);

						if (!empty($cek_data)) {

							$this->NilaiPeserta_model->delete($id_nilai_peserta);

							$response['meta']['message'] 	= 'Data nilai peserta berhasil dihapus';
							$response['meta']['code'] 		= '200';
							$response['meta']['status'] 	= 'success';
						}
						else{
							$response['meta']['message'] 	= 'Data nilai peserta tidak ditemukan';
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
		if (count($this->post()) == 2) { // jumlah parameter sesuai

			if ( $this->post('token') && $this->post('id_nilai_peserta') )
			{ // nama parameter sesuai
				$id_nilai_peserta 	= $this->post('id_nilai_peserta');
				$token				= $this->post('token');

				if (count(explode('.', $token)) == 3) {

					$decode 				= json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1])))); 

					$decode_key_secret 		= $decode->key_secret;
					$decode_id_user			= $decode->id_user;

					if ( $decode_key_secret == $this->key) {

						$cek_data 			= $this->NilaiPeserta_model->get_by_id($id_nilai_peserta);

						if (!empty($cek_data)) {

							$response['meta']['message'] 	= 'Data ditemukan';
							$response['meta']['code'] 		= '200';
							$response['meta']['status'] 	= 'success';
							$response['data']['id_nilai_peserta'] 	= $cek_data->id_nilai_peserta;
							$response['data']['id_peserta'] 		= $cek_data->id_peserta;
							$response['data']['nama_peserta'] 		= $cek_data->nama_peserta;
							$response['data']['id_lomba'] 			= $cek_data->id_lomba;
							$response['data']['nama_lomba'] 		= $cek_data->nama_lomba;
							$response['data']['id_kriteria'] 		= $cek_data->id_kriteria;
							$response['data']['nama_kriteria'] 		= $cek_data->nama_kriteria;
							$response['data']['id_nilai_peserta'] 	= $cek_data->id_nilai_peserta;
							$response['data']['nilai_kriteria_peserta']	= $cek_data->nilai;
							$response['data']['ket_nilai_kriteria']	= $cek_data->keterangan;
						}
						else{
							$response['meta']['message'] 	= 'Data nilai peserta tidak ditemukan';
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

						$cek_data 			= $this->NilaiPeserta_model->get_by_user($decode_id_user);

						if (!empty($cek_data)) {

							$response['meta']['message'] 	= 'Data ditemukan';
							$response['meta']['code'] 		= '200';
							$response['meta']['status'] 	= 'success';
							$response['data'] 				= $cek_data;
						}
						else{
							$response['meta']['message'] 	= 'Data nilai peserta tidak ditemukan';
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

						$cek_data 			= $this->NilaiPeserta_model->get_all();

						if (!empty($cek_data)) {

							$response['meta']['message'] 	= 'Data ditemukan';
							$response['meta']['code'] 		= '200';
							$response['meta']['status'] 	= 'success';
							foreach ($cek_data as $key => $value) {
								$response['data'][$key]['id_nilai_peserta'] 	= $value->id_nilai_peserta;
								$response['data'][$key]['id_peserta'] 			= $value->id_peserta;
								$response['data'][$key]['nama_peserta'] 		= $value->nama_peserta;
								$response['data'][$key]['id_lomba'] 			= $value->id_lomba;
								$response['data'][$key]['nama_lomba'] 			= $value->nama_lomba;
								$response['data'][$key]['id_kriteria'] 			= $value->id_kriteria;
								$response['data'][$key]['nama_kriteria'] 		= $value->nama_kriteria;
								$response['data'][$key]['id_nilai_peserta'] 	= $value->id_nilai_peserta;
								$response['data'][$key]['nilai_kriteria_peserta']	= $value->nilai;
								$response['data'][$key]['ket_nilai_kriteria']	= $value->keterangan;
							}
						}
						else{
							$response['meta']['message'] 	= 'Data nilai peserta tidak ditemukan';
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
	public function by_peserta_post() 
	{ // *
		if (count($this->post()) == 2) { // jumlah parameter sesuai

			if ( $this->post('token') && $this->post('id_peserta') )
			{ // nama parameter sesuai
				$token				= $this->post('token');
				$id_peserta			= $this->post('id_peserta');

				if (count(explode('.', $token)) == 3) {

					$decode 				= json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1])))); 

					$decode_key_secret 		= $decode->key_secret;
					$decode_id_user			= $decode->id_user;

					if ( $decode_key_secret == $this->key ) {

						$cek_data 			= $this->NilaiPeserta_model->by_id_peserta($id_peserta);

						if (!empty($cek_data)) {

							$response['meta']['message'] 	= 'Data ditemukan';
							$response['meta']['code'] 		= '200';
							$response['meta']['status'] 	= 'success';
							foreach ($cek_data as $key => $value) {
								$response['data'][$key]['id_nilai_peserta'] 	= $value->id_nilai_peserta;
								$response['data'][$key]['id_peserta'] 			= $value->id_peserta;
								$response['data'][$key]['nama_peserta'] 		= $value->nama_peserta;
								$response['data'][$key]['id_lomba'] 			= $value->id_lomba;
								$response['data'][$key]['nama_lomba'] 			= $value->nama_lomba;
								$response['data'][$key]['id_kriteria'] 			= $value->id_kriteria;
								$response['data'][$key]['nama_kriteria'] 		= $value->nama_kriteria;
								$response['data'][$key]['id_nilai_peserta'] 	= $value->id_nilai_peserta;
								$response['data'][$key]['nilai_kriteria_peserta']	= $value->nilai;
								$response['data'][$key]['ket_nilai_kriteria']	= $value->keterangan;
							}
						}
						else{
							$response['meta']['message'] 	= 'Data nilai peserta tidak ditemukan';
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
	public function get_by_id_lomba_post() 
	{ // *
		if (count($this->post()) == 2) { // jumlah parameter sesuai

			if ( $this->post('token') && $this->post('id_lomba') )
			{ // nama parameter sesuai
				$id_lomba 	= $this->post('id_lomba');
				$token				= $this->post('token');

				if (count(explode('.', $token)) == 3) {

					$decode 				= json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1])))); 

					$decode_key_secret 		= $decode->key_secret;
					$decode_id_user			= $decode->id_user;

					if ( $decode_key_secret == $this->key) {

						$cek_data 			= $this->NilaiPeserta_model->get_by_id_lomba($id_lomba);

						if (!empty($cek_data)) {

							$response['meta']['message'] 	= 'Data ditemukan';
							$response['meta']['code'] 		= '200';
							$response['meta']['status'] 	= 'success';
							foreach ($cek_data as $key => $value) {
								$response['data'][$key]['id_nilai_peserta'] 	= $value->id_nilai_peserta;
								$response['data'][$key]['id_peserta'] 			= $value->id_peserta;
								$response['data'][$key]['nama_peserta'] 		= $value->nama_peserta;
								$response['data'][$key]['id_lomba'] 			= $value->id_lomba;
								$response['data'][$key]['nama_lomba'] 			= $value->nama_lomba;
								$response['data'][$key]['id_kriteria'] 			= $value->id_kriteria;
								$response['data'][$key]['nama_kriteria'] 		= $value->nama_kriteria;
								$response['data'][$key]['id_nilai_peserta'] 	= $value->id_nilai_peserta;
								$response['data'][$key]['nilai_kriteria_peserta']	= $value->nilai;
								$response['data'][$key]['ket_nilai_kriteria']	= $value->keterangan;
							}
						}
						else{
							$response['meta']['message'] 	= 'Data nilai peserta tidak ditemukan';
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
