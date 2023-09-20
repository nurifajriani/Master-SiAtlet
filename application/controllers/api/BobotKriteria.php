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

class BobotKriteria extends REST_Controller {

	var $key 	= '';

	public function __construct()
	{
		parent::__construct();
		date_default_timezone_set('Asia/Jakarta');

		$this->key 		= 'API_SPK';

		$this->load->model('BobotKriteria_model');
		$this->load->model('User_model');
	}	
	public function add_post()
	{ // *
		if (count($this->post()) == 4) { 

			if ( $this->post('token') && $this->post('bobot') && $this->post('id_lomba') && $this->post('id_kriteria') )
			{ // nama parameter sesuai
				$bobot				= $this->post('bobot');
				$id_lomba			= $this->post('id_lomba');
				$id_kriteria 		= $this->post('id_kriteria');
				$token				= $this->post('token');

				if (count(explode('.', $token)) == 3) {

					$decode 				= json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1])))); 

					$decode_key_secret 		= $decode->key_secret;
					$decode_id_user			= $decode->id_user;

					if ( $decode_key_secret == $this->key ) {

						$check_kriteria 	= $this->BobotKriteria_model->cek_bobot_kriteria($id_kriteria, $bobot);

						if (!empty($check_kriteria)) {
							$response['meta']['message'] 	= 'Bobot kriteria sudah diinput sebelumnya';
							$response['meta']['code'] 		= '04';
							$response['meta']['status'] 	= 'error';
						}
						else {
							$data 	= array(
								'bobot'				=> $bobot,
								'id_lomba'			=> $id_lomba,
								'id_kriteria'		=> $id_kriteria,
								'created_at'		=> date('Y-m-d H-i-s')
							);
							$this->BobotKriteria_model->insert($data); 

							$response['meta']['message'] 	= 'Data bobot kriteria berhasil ditambahkan';
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
		if (count($this->post()) == 5) { 

			if ( $this->post('token') && $this->post('id_bobot') && $this->post('bobot') && $this->post('id_lomba') && $this->post('id_kriteria') )
			{ // nama parameter sesuai
				$id_bobot 			= $this->post('id_bobot');
				$bobot				= $this->post('bobot');
				$id_lomba			= $this->post('id_lomba');
				$id_kriteria 		= $this->post('id_kriteria');
				$token				= $this->post('token');

				if (count(explode('.', $token)) == 3) {

					$decode 				= json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1])))); 

					$decode_key_secret 		= $decode->key_secret;
					$decode_id_user			= $decode->id_user;

					if ( $decode_key_secret == $this->key ) {

						$cek_data 			= $this->BobotKriteria_model->get_by_id($id_bobot);

						if (!empty($cek_data)) {
							
							$data 	= array(
								'bobot'				=> $bobot,
								'id_lomba'			=> $id_lomba,
								'id_kriteria'		=> $id_kriteria,
								'updated_at'		=> date('Y-m-d H-i-s')
							);
							$this->BobotKriteria_model->update($id_bobot, $data); // insert ke database

							$response['meta']['message'] 	= 'Data bobot kriteria berhasil diubah';
							$response['meta']['code'] 		= '200';
							$response['meta']['status'] 	= 'success';
						}
						else{
							$response['meta']['message'] 	= 'Data bobot kriteria tidak ditemukan';
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

			if ( $this->post('token') && $this->post('id_bobot') )
			{ // nama parameter sesuai
				$id_bobot 			= $this->post('id_bobot');
				$token				= $this->post('token');

				if (count(explode('.', $token)) == 3) {

					$decode 				= json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1])))); 

					$decode_key_secret 		= $decode->key_secret;
					$decode_id_user			= $decode->id_user;

					if ( $decode_key_secret == $this->key ) {

						$cek_data 			= $this->BobotKriteria_model->get_by_id($id_bobot);

						if (!empty($cek_data)) {

							$this->BobotKriteria_model->delete($id_bobot);

							$response['meta']['message']	= 'Data bobot kriteria berhasil dihapus';
							$response['meta']['code'] 		= '200';
							$response['meta']['status'] 	= 'success';
						}
						else{
							$response['meta']['message'] 	= 'Data bobot kriteria tidak ditemukan';
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

			if ( $this->post('token') && $this->post('id_bobot') )
			{ // nama parameter sesuai
				$id_bobot 			= $this->post('id_bobot');
				$token				= $this->post('token');

				if (count(explode('.', $token)) == 3) {

					$decode 				= json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1])))); 

					$decode_key_secret 		= $decode->key_secret;
					$decode_id_user			= $decode->id_user;

					if ( $decode_key_secret == $this->key ) {

						$cek_data 			= $this->BobotKriteria_model->get_by_id($id_bobot);

						if (!empty($cek_data)) {

							$response['meta']['message']	= 'Data ditemukan';
							$response['meta']['code'] 		= '200';
							$response['meta']['status'] 	= 'success';
							$response['data']['id_bobot']	= $cek_data->id_bobot;
							$response['data']['bobot']		= $cek_data->bobot;
							$response['data']['id_lomba']	= $cek_data->id_lomba;
							$response['data']['nama_lomba']	= $cek_data->nama_lomba;
							$response['data']['id_kriteria']	= $cek_data->id_kriteria;
							$response['data']['nama_kriteria']	= $cek_data->nama_kriteria;
						}
						else{
							$response['meta']['message'] 	= 'Data bobot kriteria tidak ditemukan';
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
				$id_bobot 			= $this->post('id_bobot');
				$token				= $this->post('token');

				if (count(explode('.', $token)) == 3) {

					$decode 				= json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1])))); 

					$decode_key_secret 		= $decode->key_secret;
					$decode_id_user			= $decode->id_user;
					$decode_username 		= $decode->username;
					$decode_password 		= $decode->password;

					$cek_user 		= $this->User_model->cek_token($decode_id_user, $decode_username, md5($decode_password));

					if ( ($decode_key_secret == $this->key) && !empty($cek_user) ) {

						$cek_data 			= $this->BobotKriteria_model->get_by_user($decode_id_user);

						if (!empty($cek_data)) {

							$response['meta']['message']	= 'Data ditemukan';
							$response['meta']['code'] 		= '200';
							$response['meta']['status'] 	= 'success';
							$response['meta']['message'] 	= $cek_data;
						}
						else{
							$response['meta']['message'] 	= 'Data bobot kriteria tidak ditemukan';
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

						$cek_data 			= $this->BobotKriteria_model->get_all();

						if (!empty($cek_data)) {

							$response['meta']['message']	= 'Data ditemukan';
							$response['meta']['code'] 		= '200';
							$response['meta']['status'] 	= 'success';
							foreach ($cek_data as $key => $value) {
								$response['data'][$key]['id_bobot']		= $value->id_bobot;
								$response['data'][$key]['bobot']		= $value->bobot;
								$response['data'][$key]['id_lomba']		= $value->id_lomba;
								$response['data'][$key]['nama_lomba']	= $value->nama_lomba;
								$response['data'][$key]['id_kriteria']	= $value->id_kriteria;
								$response['data'][$key]['nama_kriteria']		= $value->nama_kriteria;
							}
						}
						else{
							$response['meta']['message'] 	= 'Data bobot kriteria tidak ditemukan';
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
	public function by_lomba_post() 
	{ // *
		if (count($this->post()) == 2) { // jumlah parameter sesuai

			if ( $this->post('token') && $this->post('id_lomba') )
			{ // nama parameter sesuai
				$id_lomba 			= $this->post('id_lomba');
				$token				= $this->post('token');

				if (count(explode('.', $token)) == 3) {

					$decode 				= json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1])))); 

					$decode_key_secret 		= $decode->key_secret;
					$decode_id_user			= $decode->id_user;

					if ( $decode_key_secret == $this->key ) {

						$cek_data 			= $this->BobotKriteria_model->get_by_lomba($id_lomba);

						if (!empty($cek_data)) {

							$response['meta']['message']	= 'Data ditemukan';
							$response['meta']['code'] 		= '200';
							$response['meta']['status'] 	= 'success';
							foreach ($cek_data as $key => $value) {
								$response['data'][$key]['id_bobot']		= $value->id_bobot;
								$response['data'][$key]['bobot']		= $value->bobot;
								$response['data'][$key]['id_lomba']		= $value->id_lomba;
								$response['data'][$key]['nama_lomba']	= $value->nama_lomba;
								$response['data'][$key]['id_kriteria']	= $value->id_kriteria;
								$response['data'][$key]['nama_kriteria']		= $value->nama_kriteria;
								$response['data'][$key]['sifat']		= $value->sifat;
							}
						}
						else{
							$response['meta']['message'] 	= 'Data bobot kriteria tidak ditemukan';
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