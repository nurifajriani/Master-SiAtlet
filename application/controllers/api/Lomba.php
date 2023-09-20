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

class Lomba extends REST_Controller {

	var $key 	= '';

	public function __construct()
	{
		parent::__construct();
		date_default_timezone_set('Asia/Jakarta');

		$this->key 		= 'API_SPK';

		$this->load->model('Lomba_model');
		$this->load->model('User_model');
	}
	public function add_post() 
	{ // *
		if (count($this->post()) == 4) { 

			if ( $this->post('token') && $this->post('nama_lomba') && $this->post('waktu_lomba') && $this->post('id_pelatih') )
			{ 
				$nama_lomba			= $this->post('nama_lomba');
				$waktu_lomba		= $this->post('waktu_lomba');
				$id_pelatih			= $this->post('id_pelatih');
				$token				= $this->post('token');

				if (count(explode('.', $token)) == 3) {

					$decode 				= json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1])))); 

					$decode_key_secret 		= $decode->key_secret;
					$decode_id_user			= $decode->id_user;

					if ( $decode_key_secret == $this->key  ) {

						$data 	= array(
							'nama_lomba'		=> $nama_lomba,
							'waktu_lomba'		=> $waktu_lomba,
							'id_pelatih' 		=> $id_pelatih,
							'created_at'		=> date('Y-m-d H-i-s')
						);
						$this->Lomba_model->insert($data);

						$response['meta']['message'] 	= 'Data lomba berhasil ditambahkan';
						$response['meta']['code'] 		= '200';
						$response['meta']['status'] 	= 'success';
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
				$response['code'] 		= '02';
				$response['status'] 	= 'error';
				$response['message'] 	= 'Nama parameter tidak valid';
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

			if ( $this->post('token') && $this->post('id_lomba') && $this->post('nama_lomba') && $this->post('waktu_lomba') && $this->post('id_pelatih') )
			{ 
				$id_lomba 			= $this->post('id_lomba');
				$nama_lomba			= $this->post('nama_lomba');
				$waktu_lomba		= $this->post('waktu_lomba');
				$id_pelatih 		= $this->post('id_pelatih');
				$token				= $this->post('token');

				if (count(explode('.', $token)) == 3){

					$decode 				= json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1])))); 

					$decode_key_secret 		= $decode->key_secret;
					$decode_id_user			= $decode->id_user;

					if ( $decode_key_secret == $this->key ) {

						$cek_lomba 		= $this->Lomba_model->get_by_id($id_lomba);

						if (!empty($cek_lomba)) {
							$data 	= array(
								'nama_lomba'		=> $nama_lomba,
								'waktu_lomba'		=> $waktu_lomba,
								'id_pelatih' 		=> $id_pelatih,
								'updated_at'		=> date('Y-m-d H-i-s')
							);
							$this->Lomba_model->update($id_lomba, $data);

							$response['meta']['message'] 	= 'Data lomba berhasil diubah';
							$response['meta']['code'] 		= '200';
							$response['meta']['status'] 	= 'success';
						}
						else{
							$response['meta']['message'] 	= 'Data lomba tidak ditemukan';
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

			if ( $this->post('token') && $this->post('id_lomba') )
			{ // nama parameter sesuai
				$id_lomba 			= $this->post('id_lomba');
				$token				= $this->post('token');

				if (count(explode('.', $token)) == 3) {

					$decode 				= json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1])))); 

					$decode_key_secret 		= $decode->key_secret;
					$decode_id_user			= $decode->id_user;

					if ( $decode_key_secret == $this->key ) {

						$cek_lomba 		= $this->Lomba_model->get_by_id($id_lomba);

						if (!empty($cek_lomba)) {
							$this->Lomba_model->delete($id_lomba);

							$response['meta']['message'] 	= 'Data lomba berhasil dihapus';
							$response['meta']['code'] 		= '200';
							$response['meta']['status'] 	= 'success';
						}
						else{
							$response['meta']['message'] 	= 'Data lomba tidak ditemukan';
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

			if ( $this->post('token') && $this->post('id_lomba') )
			{ // nama parameter sesuai
				$id_lomba 			= $this->post('id_lomba');
				$token				= $this->post('token');

				if (count(explode('.', $token)) == 3) {

					$decode 				= json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1])))); 

					$decode_key_secret 		= $decode->key_secret;
					$decode_id_user			= $decode->id_user;


					if ( $decode_key_secret == $this->key ) {

						$cek_lomba 		= $this->Lomba_model->get_by_id($id_lomba);

						if (!empty($cek_lomba)) {

							$response['meta']['message']	= 'Data ditemukan';
							$response['meta']['code'] 		= '200';
							$response['meta']['status'] 	= 'success';
							$response['data']['id_lomba']	= $cek_lomba->id_lomba;
							$response['data']['nama_lomba']	= $cek_lomba->nama_lomba;
							$response['data']['waktu_lomba']	= $cek_lomba->waktu_lomba;
							$response['data']['id_pelatih']		= $cek_lomba->id_pelatih;
							$response['data']['nama_pelatih']	= $cek_lomba->nama;
						}
						else{
							$response['meta']['message'] 	= 'Data lomba tidak ditemukan';
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
	public function get_by_pelatih_post() 
	{ // *
		if (count($this->post()) == 2) { // jumlah parameter sesuai

			if ( $this->post('token') && $this->post('id_pelatih') )
			{ // nama parameter sesuai
				$id_pelatih			= $this->post('id_pelatih');
				$token				= $this->post('token');

				if (count(explode('.', $token)) == 3){

					$decode 				= json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1])))); 

					$decode_key_secret 		= $decode->key_secret;
					$decode_id_user			= $decode->id_user;

					if ( $decode_key_secret == $this->key ) {

						$cek_lomba 		= $this->Lomba_model->get_all_by_pelatih($id_pelatih);

						if (!empty($cek_lomba)) {

							$response['meta']['message']	= 'Data ditemukan';
							$response['meta']['code'] 		= '200';
							$response['meta']['status'] 	= 'success';

							foreach ($cek_lomba as $key => $value) {
								$response['data'][$key]['id_lomba']		= $value->id_lomba;
								$response['data'][$key]['nama_lomba']	= $value->nama_lomba;
								$response['data'][$key]['waktu_lomba']	= $value->waktu_lomba;
								$response['data'][$key]['id_pelatih']	= $value->id_pelatih;
								$response['data'][$key]['nama_pelatih']	= $value->nama;
							}
						}
						else{
							$response['meta']['message'] 	= 'Data lomba tidak ditemukan';
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
		if (count($this->post()) == 1) { 

			if ( $this->post('token') )
			{ // nama parameter sesuai
				$token				= $this->post('token');

				if (count(explode('.', $token)) == 3){

					$decode 				= json_decode(base64_decode(str_replace('_', '/', str_replace('-','+',explode('.', $token)[1])))); 

					$decode_key_secret 		= $decode->key_secret;
					$decode_id_user			= $decode->id_user;

					if ( $decode_key_secret == $this->key ) {

						$cek_lomba 		= $this->Lomba_model->get_all();

						if (!empty($cek_lomba)) {

							$response['meta']['message'] 	= 'Data ditemukan';
							$response['meta']['code'] 		= '200';
							$response['meta']['status'] 	= 'success';
							// $response['data'] 				= $cek_lomba;
							foreach ($cek_lomba as $key => $value) {
								$response['data'][$key]['id_lomba']		= $value->id_lomba;
								$response['data'][$key]['nama_lomba']		= $value->nama_lomba;
								$response['data'][$key]['waktu_lomba']	= $value->waktu_lomba;
								$response['data'][$key]['id_pelatih']		= $value->id_pelatih;
								$response['data'][$key]['nama_pelatih']	= $value->nama;
							}
						}
						else{
							$response['meta']['message'] 	= 'Data lomba tidak ditemukan';
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